<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Offer;
use App\Models\Client;
use App\Models\Cashier;
use App\Models\Charity;
use App\Models\History;
use App\Models\Notification;
use App\Models\Partner;
use App\Models\Voucher;
use App\Models\User_Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

use App\Traits\NotificationTrait;


class partner_cashier_controller extends Controller
{
    use NotificationTrait;

    public function show_my_offers()
    {
        //ran edit
        $person = User::where("id",Auth::id())->first();

        if ($person->role_id == 3)
        {
            $cashier = Cashier::where('user_id',$person->id)->first();
            $person = User::where('id',$cashier->partner_id)->first();
        }
        $offers =  $person->offers;
        if($offers->isEmpty())
        {
            return response()->json([$offers,'status'=>200 ,'message' => 'Empty'] , status:200);
        }
        foreach($offers as $offer)
        {
            $offer->images;
            if(now() > $offer->end_time)
            {
                $offer->delete();
            }
        }
        return response()->json([$offers,'status'=>200 ,'message' => 'success'] , status:200);
    }

    public function show_my_accept_voucher()
    {
        $person = User::where("id",Auth::id())->first();

        if($person->role_id == 2)
        {
            $partner_user = $person->id;
        }
        else if ($person->role_id == 3)
        {
            $cashier = Cashier::where('user_id',$person->id)->first();
            $pu = User::where('id',$cashier->partner_id)->first();
            $partner_user = $pu->id;
        }

        $voucher = Voucher::where('user_id',$partner_user)->where('accept',1)->get();

        if(is_Null($voucher))
        {
            return response()->json([$voucher,'status'=>200 ,'message' => 'Empty'] , status:200);
        }

        return response()->json([$voucher,'status'=>200 ,'message' => 'success'] , status:200);
    }

    public function information_client_from_otp(Request $request)
    {
        $validator= Validator::make($request->all(),[
            'otp'=>'required|min:4|max:4'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        else{
            $Client = Client::where('OTP',$request->otp )->where('OTP_exp_date' , '>' , now())->first();
            if(is_Null($Client))
            {
                return response()->json([$Client,'status'=>200 ,'message' => 'Empty'] , status:200);
            }
            $Client->User;

            return response()->json([$Client,'status'=>200 ,'message' => 'success'] , status:200);
        }
    }

    public function calculate_points_from_invoice(Request $request)
    {
        $validator= Validator::make($request->all(),[
            'invoice'=>'required|numeric|min:1',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        else{
            $partner_id = Auth::id();
            $partner = Partner::where('user_id',$partner_id)->first();
            $service_before = $partner->service;
            $service_after = $service_before / 100;
            $invoice_after = $request->invoice * $service_after;
            return response()->json([$invoice_after,'status'=>200 ,'message' => 'Success'] , status:200);
        }
    }

    public function add_points_to_client(Request $request)
    {
        $validator= Validator::make($request->all(),[
            'OTP'=>'required|min:4|max:4',
            'invoice'=>'required|numeric|min:1',
            'confirm_invoice'=>'required|numeric|min:1',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        else if($request->confirm_invoice != $request->invoice)
        {
            return response()->json(['status'=>402 ,'message' => 'Wrong invoice'] , status:402);
        }
        else{
            //ran edit
            $person = User::where("id",Auth::id())->first();

            if($person->role_id == 2)
            {
                $partner = Partner::where('user_id',Auth::id())->first();
                $service_before = $partner->service;
                $service_after = $service_before / 100;
                $points = $request->invoice * $service_after;
            }
            else if ($person->role_id == 3)
            {
                $cashier = Cashier::where('user_id',$person->id)->first();
                $partner_user = User::where('id',$cashier->partner_id)->first();
                $partner = Partner::where('user_id',$partner_user->id)->first();
                $service_before = $partner->service;
                $service_after = $service_before / 100;
                $points = $request->invoice * $service_after;
            }

            if($request->role == 4)
            {
                $user = Client::where('OTP',$request->OTP)->first();
            }
            elseif($request->role == 5)
            {
                $user = Charity::where('OTP',$request->OTP)->first();
            }

            if(now() > $user->OTP_exp_date)
            {
                $user->OTP = null;
                $user->save();
                return response()->json(['status'=>200 ,'message' => 'OTP expired'] , status:200);
            }
            if($partner->points > $points)
            {
                $partner->update([
                    'points' => $partner->points - $points
                ]);
                $partner->save();

                if($user->points_exp_date < now()){
                    $user->update([
                        'points' => 0,
                        'points_exp_date' => now()->addMonths(6),
                    ]);
                    $user->save();
                }
                $user->update([
                    'points' => $user->points + $points
                ]);
                $user->save();

                $history = History::query()->create([
                    'operation' => "add",
                    'transfer_points' => $points,
                    'transfer_time' =>now(),
                    'invoice' => $request->invoice,
                    'from_user' => Auth::id(),
                    'to_user' => $user->user_id
                ]);

                //send notification
                $to_user = User::whereNotNull('device_token')->where('id',$user->user_id)->first();
                if(!is_Null($to_user)){
                    $this->send_notify($to_user->device_token,"Loyality System","you recieved a $points points from $person->name");
                    $notify = Notification::query()->create([
                        'title' => "$person->name",
                        'body' => "you recieved a $points points from $person->name",
                        'user_id' => $user->user_id,
                    ]);
                }

                return response()->json(['user points'=>$user->points,'status'=>200 ,'message' => 'Success'] , status:200);
            }

            return response()->json(['status'=>400 ,'message' => 'Not enough partner points'] , status:400);
        }
    }

    public function get_voucher_info_from_otp(request $request)
    {
        $user = Auth::user();
        $validator= Validator::make($request->all(),[
            'OTP'=>'required|min:4|max:4',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        $user_voucher = User_Voucher::where('OTP',$request->OTP)->first();
        if(is_Null($user_voucher))
        {
            return response()->json([$user_voucher,'status'=>200 ,'message' => 'Empty'] , status:200);
        }
        if($user_voucher->valid = 0 )
        {
            return response()->json(['status'=>200 ,'message' => 'Voucher expired'] , status:200);
        }

        $voucher = $user_voucher->voucher;

        if($user->role_id == 2 && $voucher->user_id != $user->id){
            return response()->json(['status'=>404 ,'message' => 'you cannot consume vouchers that are not yours'] , status:404);
        }
        else if($user->role_id == 3){
            $cashier = Cashier::where('user_id' , $user->id)->first();
            if($voucher->user_id != $cashier->partner_id){
                return response()->json(['status'=>404 ,'message' => 'you cannot consume vouchers that are not yours'] , status:404);
            }
        }
        return response()->json([$voucher , 'status'=>200 ,'message' => 'success'] , status:200);
    }

    public function consume_voucher(Request $request)
    {
        $user = Auth::user();
        $user_voucher = User_Voucher::where('OTP',$request->OTP)->first();
        $voucher = $user_voucher->voucher;

        if($user->role_id == 2 && $voucher->user_id != $user->id){
            return response()->json(['status'=>404 ,'message' => 'you cannot consume vouchers that are not yours'] , status:404);
        }
        else if($user->role_id == 3){
            $cashier = Cashier::where('user_id' , $user->id)->first();
            if($voucher->user_id != $cashier->partner_id){
                return response()->json(['status'=>404 ,'message' => 'you cannot consume vouchers that are not yours'] , status:404);
            }
        }

        $user_voucher_delete = User_Voucher::where('id',$user_voucher->id)->delete();

        //send notification
        $to_user = User::whereNotNull('device_token')->where('id',$user_voucher->user_id)->first();
        if(!is_Null($to_user)){
            $this->send_notify($to_user->device_token,"Loyality System","consumed voucher successfully");
            $notify = Notification::query()->create([
                'title' => "Loyality System",
                'body' => "consumed voucher successfully",
                'user_id' => $user_voucher->user_id,
            ]);
        }

        return response()->json(['status'=>200 ,'message' => 'Voucher Consumed successfully'] , status:200);
    }

    public function consume_offer(Request $request)
    {
        $validator= Validator::make($request->all(),[
            'OTP'=>'required|min:4|max:4',
            'offer_points'=>'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        //$offer = Offer::find($request->offer_id);

        if($request->role == 4)
        {
            $client = Client::where('OTP',$request->OTP)->first();
            if(is_Null($client))
            {
                return response()->json([$client,'status'=>200 ,'message' => 'Empty'] , status:200);
            }
            if(now() > $client->OTP_exp_date)
            {
                $client->OTP=null;
                $client->save();
                return response()->json(['status'=>200 ,'message' => 'OTP Not Valid'] , status:200);
            }
            if($client->points < $request->offer_points)
            {
                return response()->json(['status'=>400 ,'message' => 'Not enough Points'] , status:400);
            }
            $client->points -= $request->offer_points;
            $client->save();

            $to_user = User::whereNotNull('device_token')->where('id',$client->user_id)->first();
            if($client->points > 0){
                $client->points_exp_date=now()->addMonths(6);
                $client->save();

                //notification to client
                if(!is_Null($to_user)){
                    $this->send_notify($to_user->device_token,"Loyality System","Your expiring points have been renewed to \n $client->points_exp_date");
                    $notify = Notification::query()->create([
                        'title' => "Loyality System",
                        'body' => "Your expiring points have been renewed to \n $client->points_exp_date",
                        'user_id' => $client->user_id,
                    ]);
                }
            }

            $history= History::query()->create([
                'operation' => "buy_offer",
                'transfer_points' => $request->offer_points,
                'transfer_time' =>now(),
                'invoice' => 0.0,
                'from_user' => $client->user_id,
                'to_user' => Auth::id()
            ]);

            if(!is_Null($to_user)){
                $this->send_notify($to_user->device_token,"Loyality System","consumed offer successfully");
                $notify = Notification::query()->create([
                    'title' => "Loyality System",
                    'body' => "consumed offer successfully",
                    'user_id' => $client->user_id,
                ]);
            }
            return response()->json(['user_points' => $client->points ,'status'=>200 ,'message' => 'Success'] , status:200);
        }

        if($request->role == 5)
        {
            $charity = Charity::where('OTP',$request->OTP)->first();
            if(is_Null($charity))
            {
                return response()->json([$charity,'status'=>200 ,'message' => 'Empty'] , status:200);
            }
            if(now() > $charity->OTP_exp_date)
            {
                $charity->OTP=null;
                $charity->save();
                return response()->json(['status'=>200 ,'message' => 'OTP Not Valid'] , status:200);
            }
            if($charity->points < $request->offer_points)
            {
                return response()->json(['status'=>400 ,'message' => 'No Points'] , status:400);
            }
            $charity->points -= $request->offer_points;
            $charity->save();

            $to_user = User::whereNotNull('device_token')->where('id',$charity->user_id)->first();

            if($charity->points > 0){
                $charity->points_exp_date=now()->addMonths(6);
                $charity->save();

                //notification to client
                if(!is_Null($to_user)){
                    $this->send_notify($to_user->device_token,"Loyality System","Your expiring points have been renewed to \n $charity->points_exp_date");
                    $notify = Notification::query()->create([
                        'title' => "Loyality System",
                        'body' => "Your expiring points have been renewed to \n $charity->points_exp_date",
                        'user_id' => $charity->user_id,
                    ]);
                }
            }

            $history= History::query()->create([
                'operation' => "buy_offer",
                'transfer_points' => $request->offer_points,
                'transfer_time' =>now(),
                'invoice' => 0.0,
                'from_user' =>Auth::id(),
                'to_user' => $charity->user_id
            ]);

            //notification to client
            if(!is_Null($to_user)){
                $this->send_notify($to_user->device_token,"Loyality System","consumed offer successfully");
                $notify = Notification::query()->create([
                    'title' => "Loyality System",
                    'body' => "consumed offer successfully",
                    'user_id' => $charity->user_id,
                ]);
            }
            return response()->json(['user_points' => $charity->points ,'status'=>200 ,'message' => 'Success'] , status:200);
        }
    }

    public function get_my_vouchers_of_one_client(request $request)
    {
        $response = [];
        $person = User::where("id",Auth::id())->first();

        if($person->role_id == 2)
        {
            $user_partner = $person;
        }
        else if ($person->role_id == 3)
        {
            $cashier = Cashier::where('user_id',$person->id)->first();
            $user_partner = User::where('id',$cashier->partner_id)->first();
           // $user_partner = $user->id;
        }
        //$partner = Partner::where('user_id' , $user_partner->id)->first();
        $client_id = $request->client_id;

        $user_vouchers = User_Voucher::where('user_id',$client_id)->get();
        foreach($user_vouchers as $user_voucher)
        {
            $vouchers = Voucher::where('user_id',$user_partner->id)->where('id',$user_voucher->voucher_id)->get();
            foreach($vouchers as $voucher)
            {
                $response[]=([
                    'voucher' => $voucher->name,
                    'partner_name' => $user_partner->name,
                    'points' => $voucher->point,
                    'creation_date' => $user_voucher->created_at,
                    'expiry_date' => $user_voucher->exp_date,
                ]);
            }
        }
        if(sizeof($response) === 0) {
            return response()->json([$response,'status'=>200 ,'message' => 'Empty'] , status:200);
        }

        return response()->json([$response ,'status'=>200 ,'message' => 'Success'] , status:200);
    }
}
