<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Offer;
use App\Models\Client;
use App\Models\Cashier;
use App\Models\Charity;
use App\Models\History;
use App\Models\Notification;
use App\Models\Voucher;
use App\Models\User_Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

use App\Traits\NotificationTrait;


class charity_client_controller extends Controller
{
    use NotificationTrait;

    public function show_partners()
    {
        $user = Cache::remember('Partners' ,60*60*24,function(){
            return  User::where('role_id',2)->get();
        });

        foreach ($user as $key) {
            $key->partner;
        }
        if(is_Null($user))
        {
            return response()->json([$user,'status'=>200 ,'message' => 'Empty'] , status:200);
        }

        return response()->json([$user,'status'=>200 ,'message' => 'Success'] , status:200);
    }

    public function buy_voucher($id)
    {
        $voucher = Voucher::where('id',$id)->first();
        $user1 = Auth::user();
        if($user1->role_id == 4)
        {
            $user = Client::where('user_id',Auth::id())->first();
        }
        else if($user1->role_id == 5)
        {
            $user = Charity::where('user_id',Auth::id())->first();
        }
        if($user->points < $voucher->point)
        {
            return response()->json(['status'=>400 ,'message' => 'Not enough Points'] , status:400);
        }

        $user_voucher = User_Voucher::where('user_id' , $user->user_id)
                                    ->where('voucher_id',$voucher->id)
                                    ->where('valid',1)->first();
        if(!is_Null($user_voucher))
        {
            return response()->json(['status'=>400 ,'message' => 'You already have this voucher'] , status:400);
        }
        $user->update(['points' => $user->points - $voucher->point]);
        //$date = $client->points_exp_date;
        //$startMonth = $date;
        //$endMonth = $startMonth->addMonths(10);
        //$client->update(['points_exp_date' => $endMonth]);
        if($user->points > 0)
        {
            $user->update(['points_exp_date' => now()->addMonths(6)]);
            $user->save();

            //notification to client
            if($user1->device_token != null){
                $this->send_notify($user1->device_token,"Loyality System","Your expiring points have been renewed to $user->points_exp_date ");
                $notify = Notification::query()->create([
                    'title' => "Loyality System",
                    'body' => "Your expiring points have been renewed to $user->points_exp_date ",
                    'user_id' => $user1->id,
                ]);
            }
        }

        $voucher->counter = $voucher->counter+1;
        $voucher->save();

        $user_voucher = User_Voucher::query()->create([
            'exp_date' => now()->addMonths(2),
            'user_id' => Auth::id(),
            'voucher_id' => $id
        ]);
        $history= History::query()->create([
            'operation' => "buy voucher",
            'transfer_points' => $voucher->point,
            'transfer_time' =>now(),
            'from_user' => Auth::id(),
            'to_user' => $voucher->user_id,
        ]);

        return response()->json([$voucher,'status'=>200 ,'message' => 'Success'] , status:200);
    }

    public function use_voucher($id)
    {
        $user_id = Auth::id();
        //$client = Client::where('user_id' , Auth::id())->first();
        $user_voucher = User_voucher::where('voucher_id',$id)->where('user_id',$user_id)->first();

        if(is_Null($user_voucher))
        {
            return response()->json([$user_voucher,'message' => 'you need to buy this voucher first' , 'status' => 400],status:400);
        }

        if($user_voucher->valid == 0)
        {
            return response()->json(['message' => 'Voucher expired' , 'status' => 200] , status:200);
        }
        $otp = (String)rand(1000,9999);
        $check = User_Voucher::where('OTP' , $otp)->first();
        while(!is_Null($check)){
            $otp = (String)rand(1000,9999);
            $check = User_Voucher::where('OTP' , $otp)->first();
        }
        $user_voucher->OTP = $otp;
        $user_voucher->OTP_exp_date = now()->addDays(1);
        $user_voucher->save();

        return response()->json([(String)$user_voucher->OTP,'message' => 'Success' , 'status' => 200] , status:200);
    }

    public function show_my_bought_voucher()
    {
        $user_voucher =  User_Voucher::where('user_id', Auth::id())->get();

        if(is_Null($user_voucher))
        {
            return response()->json([$user_voucher,'status'=>200 ,'message' => 'Empty'] , status:200);
        }

        foreach($user_voucher as $key)
        {
            if(now() > $key->exp_date)
            {
                $key->valid = 0;
                $key->save();
            }
            if(now() > $key->OTP_exp_date)
            {
                $key->OTP = null;
                $key->save();
            }
            $voucher = $key->voucher;
            $voucher->partner;
        }
        return response()->json([$user_voucher,'status'=>200 ,'message' => 'Success'] , status:200);
    }

    public function show_points_history()
    {
        $user = Auth::user();
        if($user->role_id == 1)
        {
            $response = [];

            $histories = History::orderBy('id','DESC')->get();
            if(is_Null($histories))
            {
                return response()->json([$histories,'status'=>200 ,'message' => 'Empty'] , status:200);
            }
            foreach($histories as $history)
            {
                $to =  User::where('id',$history->to_user)->first();
                $from = User::where('id',$history->from_user)->first();
                if($to->role_id == 3)
                    {
                        $cashier = Cashier::where('user_id' , $to->id)->first();
                        $partner = User::where('id',$cashier->partner_id)->first();
                        $response[]=([
                            "id" => $history->id,
                            "operation"=> $history->operation,
                            "transfer_points"=> $history->transfer_points,
                            "transfer_time"=> $history->transfer_time,
                            "invoice"=> $history->invoice,
                            "user_from"=>User::where('id',$history->from_user)->first(),
                            //"user_to_cashier"=>$to,
                            //"user_to_partner"=>$partner,
                            "user_to"=>([
                                "cashier"=>$to,
                                "partner"=>$partner
                            ]),
                            "created_at"=> $history->created_at,
                            "updated_at"=> $history->updated_at,
                        ]);

                    }

                    else if($from->role_id == 3)
                    {
                        $cashier = Cashier::where('user_id' , $from->id)->first();
                        $partner = User::where('id',$cashier->partner_id)->first();
                        $response[]=([
                            "id" => $history->id,
                            "operation"=> $history->operation,
                            "transfer_points"=> $history->transfer_points,
                            "transfer_time"=> $history->transfer_time,
                            "invoice"=> $history->invoice,
                            // "user_from_cashier"=>$from,
                            // "user_from_partner"=>$partner,
                            "user_from"=>([
                                "cashier"=>$from,
                                "partner"=>$partner
                            ]),
                            "user_to"=>User::where('id',$history->to_user)->first(),
                            "created_at"=> $history->created_at,
                            "updated_at"=> $history->updated_at,
                        ]);

                    }
                    else
                    {
                        $response[]=([
                            "id" => $history->id,
                            "operation"=> $history->operation,
                            "transfer_points"=> $history->transfer_points,
                            "transfer_time"=> $history->transfer_time,
                            "invoice"=> $history->invoice,
                            "user_from"=>User::where('id',$history->from_user)->first(),
                            "user_to"=>User::where('id',$history->to_user)->first(),
                            "created_at"=> $history->created_at,
                            "updated_at"=> $history->updated_at,
                        ]);
                    }
            }
            return response()->json([$response,'status'=>200 ,'message' => 'success'] , status:200);
        }

        if($user->role_id == 2)
        {
            $histories = null;

            $histories = History::Where('from_user' , Auth::id())->orWhere('to_user',Auth::id())->orderBy('id','DESC')->get();
            if(is_Null($histories))
            {
                return response()->json([$histories,'status'=>200 ,'message' => 'Empty'] , status:200);
            }
            foreach($histories as $history)
            {
                $history->userFrom;
                $history->userTo;
            }

            return response()->json([$histories,'status'=>200 ,'message' => 'success'] , status:200);
        }


        $from = $user->historiesFrom;
        $to = $user->historiesTo;

        if($from->isEmpty() && $to->isEmpty())
        {
            return response()->json(['status'=>200 ,'message' => 'Empty'] , status:200);
        }

        foreach($from as $f)
        {
            $f->userTo;
        }
        foreach($to as $t)
        {
            $t->userFrom;
        }

        return response()->json([$user,'status'=>200 ,'message' => 'success'] , status:200);
    }

    public function show_points_history_partner_cashier(Request $request )
    {
        $user = Auth::user();
        if($user->role_id == 2)
        {
            $histories = null;
            $cashiers = Cashier::where('partner_id' , Auth::id())->get();
            $response = [];
            $count = 0;
            foreach($cashiers as $cashier)
            {
                $histories = History::where('from_user' , $cashier->user_id)->orWhere('to_user',$cashier->user_id)->orderBy('id','DESC')->get();
                foreach($histories as $history)
                {
                    $response[]=([
                        "id" => $history->id,
                        "operation"=> $history->operation,
                        "transfer_points"=> $history->transfer_points,
                        "transfer_time"=> $history->transfer_time,
                        "invoice"=> $history->invoice,
                        "user_from"=>$history->userFrom,
                        "user_to"=>$history->userTo,
                        "created_at"=> $history->created_at,
                        "updated_at"=> $history->updated_at,
                    ]);
                }
                $count++;
            }
            if(sizeof($response)===0){
                return response()->json([$response,'status'=>200 ,'message' => 'Empty'] , status:200);
            }

            $sako = collect($response)->sortByDesc('id')->values()->all();
            return response()->json([$sako,'status'=>200 ,'message' => 'success'] , status:200);
        }
    }

    public function generate_otp()
    {
        $this->delete_expired_otp();
        $user = User::find(Auth::id());
        if($user->role_id == 4)
        {
            $client = Client::where('user_id' , $user->id)->first();
            // $client->timestamps = false;
            $otp = (String)rand(1000,9999);
            $check = Client::where('OTP' , $otp)->first();
            while(!is_Null($check)){
                $otp = (String)rand(1000,9999);
                $check = Client::where('OTP' , $otp)->first();
            }
            $client->OTP = $otp;
            $client->OTP_exp_date = now()->addMinutes(5);
            $client->save();

            return response()->json([(String)$client->OTP,'message' => 'otp sent successfully' , 'status' => 200]);
        }

        else if($user->role_id == 5)
        {
            $charity = Charity::where('user_id' , $user->id)->first();
            $otp = (String)rand(1000,9999);
            $check = Charity::where('OTP' , $otp)->first();
            while(!is_Null($check)){
                $otp = (String)rand(1000,9999);
                $check = Charity::where('OTP' , $otp)->first();
            }
            $charity->OTP = $otp;
            $charity->OTP_exp_date = now()->addMinutes(5);
            $charity->save();

            return response()->json([(String)$charity->OTP,'message' => 'otp sent successfully' , 'status' => 200]);
        }
    }

    public function delete_expired_otp()
    {
        $clients = Client::all();
        foreach($clients as $client)
        {
            if(now()>$client->OTP_exp_date)
            {
                $client->OTP=null;
                $client->save();
            }
        }
        $charities = Charity::all();
        foreach($charities as $charite)
        {
            if(now()>$charite->OTP_exp_date)
            {
                $charite->OTP=null;
                $charite->save();
            }
        }
    }

    public function show_partner_offers($id)
    {
        //$offers = Cache::remember('Offers' ,60*60*24,function(){
            /*return*/$offers = Offer::where('user_id',$id)->get();
        //});
        foreach($offers as $offer)
        {
            if(now() > $offer->end_time)
            {
                $offer->delete();
            }
        }
        $offers = Offer::where('user_id',$id)->get();
        foreach($offers as $offer)
        {
            $offer->images;
        }
        if($offers->isEmpty())
        {
            return response()->json([$offers,'status'=>200 ,'message' => 'Empty'] , status:200);
        }
        return response()->json([$offers,'status'=>200 ,'message' => 'Success'] , status:200);
    }

    public function show_partner_vouchers($id)
    {
        //$vouchers = Cache::remember('Vouchers' ,60*60*24,function(){
            /*return*/$vouchers =  Voucher::where('user_id' , $id)->where('accept',1)->get();
        //});

        if($vouchers->isEmpty())
        {
            return response()->json([$vouchers,'status'=>200 ,'message' => 'Empty'] , status:200);
        }
        //$vouchers = Voucher::where('accept',1)->get();

        return response()->json([$vouchers,'status'=>200 ,'message' => 'Success'] , status:200);
    }
}
