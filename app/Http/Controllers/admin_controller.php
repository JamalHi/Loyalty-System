<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Cashier;
use App\Models\Charity;
use App\Models\Client;
use App\Models\History;
use App\Models\Partner;
use App\Models\User;
use App\Models\User_Voucher;
use App\Models\Voucher;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


class admin_controller extends Controller
{
    public function show_users(Request $request)
    {

            if($request->role_id==2)
            {
                $users = User::query()->where('role_id',2)->get();
                if($request->status!=null)
                {
                    $users = $users->where('active',$request->status);
                }
                if($users->isEmpty())
                {
                    return response()->json([$users,'status'=>200 ,'message' => 'Empty'] , status:200);
                }
                foreach ($users as $user)
                {
                    $partner = Partner::where('user_id',$user->id)->first();
                    $partner_res[]=([
                        'user' => $user,
                        'partner'=>$partner
                    ]);
                }
                return response()->json([$partner_res,'status'=>200 ,'message' => 'Success'] , status:200);
            }
            if($request->role_id==3)
            {
                $users = User::query()->where('role_id',3)->get();
                if($request->status!=null)
                {
                    $users = $users->where('active',$request->status);
                }
                if($users->isEmpty())
                {
                    return response()->json([$users ,'status'=>200 ,'message' => 'Empty'] , status:200);
                }
                foreach ($users as $user)
                {
                    $cashier = Cashier::where('user_id',$user->id)->first();
                    $cashier_res[]=([
                        'user' => $user,
                        'cashier'=>$cashier
                    ]);
                }
                return response()->json([$cashier_res,'status'=>200 ,'message' => 'Success'] , status:200);
            }
            if($request->role_id==4)
            {
                $users = User::query()->where('role_id',4)->get();
                if($request->status!=null)
                {
                    $users = $users->where('active',$request->status);
                }
                if($users->isEmpty())
                {
                    return response()->json([$users,'status'=>200 ,'message' => 'Empty'] , status:200);
                }
                foreach ($users as $user)
                {
                    $client = Client::where('user_id',$user->id)->first();
                    $client_res[]=([
                        'user' => $user,
                        'client'=>$client
                    ]);
                }
                return response()->json([$client_res,'status'=>200 ,'message' => 'Success'] , status:200);
            }
            if($request->role_id==5)
            {
                $users = User::query()->where('role_id',5)->get();
                if($users->isEmpty())
                {
                    return response()->json([$users,'status'=>200 ,'message' => 'Empty'] , status:200);
                }
                if($request->status!=null)
                {
                    $users = $users->where('active',$request->status);
                }
                foreach ($users as $user)
                {
                    $charity = Charity::where('user_id',$user->id)->first();
                    $charity_res[]=([
                        'user' => $user,
                        'charity'=>$charity
                    ]);
                }
                return response()->json([$charity_res,'status'=>200 ,'message' => 'Success'] , status:200);
            }
            return response()->json(['status'=>200 ,'message' => 'test'] , status:200);

    }

    public function user_search(Request $request)
    {
        $query = $request->user_name;
        $users = User::where('name','LIKE','%'.$query.'%')->get();
        if($users->isEmpty())
        {
            return response()->json([$users,'status'=>200 ,'message' => 'Empty'] , status:200);
        }
        foreach($users as $user){
            if($user->role_id == 2)
            {
                $partner = Partner::where('user_id',$user->id)->first();
                $data[]=([
                    'user' => $user,
                    'Partner'=>$partner
                ]);
            }
            if($user->role_id == 3)
            {
                $cashier = Cashier::where('user_id',$user->id)->first();
                $data[]=([
                    'user' => $user,
                    'cashier'=>$cashier
                ]);
            }
            if($user->role_id == 4)
            {
                $client = Client::where('user_id',$user->id)->first();
                $data[]=([
                    'user' => $user,
                    'client'=>$client
                ]);
            }
            if($user->role_id == 5)
            {
                $charity = Charity::where('user_id',$user->id)->first();
                $data[]=([
                    'user' => $user,
                    'charity'=>$charity
                ]);
            }
        }
        if(sizeof($data)==0)
        {
            return response()->json([$data,'status'=>200 ,'message' => 'Empty'] , status:200);
        }
        return response()->json([$data,'status'=>200 ,'message' => 'success'] , status:200);
    }

    public function delete_user($id)
    {
        $user = User::find($id);
        $mailcontroller = resolve(MailController::class);
        $mailcontroller->sendEmail(" Dear user ,  you were removed by our admins from loyalty system.
                                    \nfor more information contact us.
                                    \n .عزيزي المشترك , لقد تم حذفك من قبل مشرفنا
                                    \n .للاستفسار تواصل معنا عبر الايميل ", $user->email);
        $user->delete();
        return response()->json(['status'=>200 ,'message' => 'User deleted successfully'] , status:200);
    }

    public function block_user($id)
    {
        $user = User::find($id);
        $user->active = 0;
        $mailcontroller = resolve(MailController::class);
        $mailcontroller->sendEmail(" Dear user ,  you were blocked by our admins from loyalty system.
                                    \n you need to verify your email.
                                    \n .عزيزي المشترك , لقد تم حظرك من قبل مشرفنا
                                    \n .عليك تأكيد حسابك ", $user->email);
        $user->save();
        return response()->json(['status'=>200 ,'message' => 'Account blocked'] , status:200);
    }

    public function show_vouchers_request()
    {
        $vouchers = Voucher::where('accept',0)->orderBy('id','DESC')->get();
        if($vouchers->isEmpty())
        {
            return response()->json([$vouchers,'status'=>200 ,'message' => 'Empty'] , status:200);
        }
        return response()->json([$vouchers,'status'=>200 ,'message' => 'Success'] , status:200);
    }

    public function accept_deny_voucher_request(Request $request,$id)
    {
        $voucher = Voucher::find($id);
        if($request->accept == 1)
        {
            $voucher->accept = 1;
            $voucher->save();
            return response()->json(['status'=>200 ,'message' => 'Voucher accepted'] , status:200);
        }
        else
        {
            $voucher->accept = 0;
            $voucher->save();
            return response()->json(['status'=>200 ,'message' => 'Voucher denied'] , status:200);
        }
    }

    public function point_management()
    {
    }

    public function edit_service(Request $request)
    {
        $user = User::find($request->user_id);
        $partner = Partner::where('user_id',$user->id)->first();
        $partner->service = $request->service;
        $partner->save();
        return response()->json(['status'=>200 ,'message' => 'Service updated'] , status:200);
    }

    public function add_advertisement(Request $request)
    {
        $validator= Validator::make($request->all(),[
            'title'=>'required',
            'description'=>'required',
            'media'=>'required|file|mimes:mov,mp4,avi,wmv'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        if($request->hasFile('media') ){
            //$media = Cloudinary::upload($request->file('media')->getRealPath())->getSecurePath();
            $media = time() . '.' . $request->media->getClientOriginalExtension();
            $media = $request->file('media')->store('ads','public');
            $request->media->move(public_path('ads'),$media);
        }
        $ad = Ad::create([
            'title' => $request->title,
            'description' => $request->description,
            'media' => $media,
            'user_id' => Auth::id()
        ]);
        return response()->json([$ad,'status'=>200 ,'message' => 'Advertisement added'] , status:200);
    }

    public function show_all_ads()
    {
        $ads = Ad::all();
        if($ads->isEmpty())
        {
            return response()->json([$ads,'status'=>200 ,'message' => 'Empty'] , status:200);
        }
        return response()->json([$ads,'status'=>200 ,'message' => 'Success'] , status:200);
    }

    public function update_ad_status(Request $request , $id)
    {
        $ad = Ad::find($id);
        if($request->status == 1)
        {
            $ad->valid = 1;
            $ad->save();
            return response()->json(['status'=>200 ,'message' => 'Advertisement valid'] , status:200);
        }
        else
        {
            $ad->valid = 0;
            $ad->save();
            return response()->json(['status'=>200 ,'message' => 'Advertisement blocked'] , status:200);
        }
    }

    public function add_points_to_partner(Request $request)
    {
        $user = User::where('email',$request->email)->first();
        if(is_Null($user))
        {
            return response()->json([$user,'status'=> 200 , 'message'=>'Empty'],status:200);
        }
        $partner = Partner::where('user_id' , $user->id)->first();
        $validator = Validator::make($request->all() , [
            'points' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->all(),status:400);
        }
        $partner->points += $request->points;
        $partner->save();


        $history= History::query()->create([
            'operation' => "buy points",
            'transfer_points' => $request->points,
            'transfer_time' =>now(),
            'from_user' => Auth::id(),
            'to_user' => $user->id
        ]);

        return response()->json(['status' => '200' , 'message'=>'Success'],status:200);
    }

    public function show_client_vouchers($id)
    {
        $user_voucher = User_Voucher::where('user_id',$id)->get();
        if($user_voucher->isEmpty())
        {
            return response()->json([$user_voucher,'status'=>200 ,'message' => 'Empty'] , status:200);
        }
        foreach ($user_voucher as $voucher){
            $v = $voucher->voucher;
            $v->partner;
        }

        return response()->json([$user_voucher,'status'=>200 ,'message' => 'Success'] , status:200);
    }

    public function show_sevices(){
        $partners = Partner::get();
        if($partners->isEmpty())
        {
            return response()->json([$partners,'status'=>200 ,'message' => 'Empty'] , status:400);
        }
        foreach($partners as $partner){
            $user = User::where('id',$partner->user_id)->first();
            $response[] = ([
                'user_id' => $user->id,
                'partner_name' => $user->name,
                'service' => $partner->service,
            ]);
        }
        return response()->json([$response,'status'=>200 ,'message' => 'Success'] , status:200);
    }
}
