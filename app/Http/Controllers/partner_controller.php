<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Image;
use App\Models\Offer;
use App\Models\Cashier;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class partner_controller extends Controller
{

    public function add_offer(Request $request)
    {
        $validator= Validator::make($request->all(),[
            'title'=>'required|string',
            'points'=>'required',
            'description'=>'required|string',
            'start_time'=>'required',
            'end_time'=>'required'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        if($request->start_time >= $request->end_time)
        {
            return response()->json(['status'=>400 ,'message' => 'the starting time needs to be earlier!'] , status:400);
        }
        if(($request->start_time < now()) || ($request->end_time < now()))
        {
            return response()->json(['status'=>400 ,'message' => 'wrong timing!'] , status:400);
        }
        else{
            $offer = Offer::create([
                'title'=>$request->title,
                'description'=>$request->description,
                'start_time'=>$request->start_time,
                'end_time'=>$request->end_time,
                'points'=>$request->points,
                'user_id'=>Auth::id(),
            ]);
            return response()->json([$offer,'status'=>200 ,'message' => 'offer added successfully'] , status:200);
        }
    }

    public function add_offer_images(request $request)
    {
        $validator= Validator::make($request->all(),[
            'images' => 'required',
            'images.*' => 'max:10000|mimes:jpeg,png,jpg,gif,svg'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        else{
            $new_images=null;
            if($request->hasfile('images')){
                foreach($request->file('images') as $image)
                {
                    $img = Cloudinary::upload($image->getRealPath())->getSecurePath();
                    //$img = time() . '.' . $image->getClientOriginalExtension();
                    //$img = $image->store('upload','public');
                    //$image->move(public_path('upload'),$img);

                    $new_images=Image::create([
                        'name'=>$img,
                        'offer_id'=>$request->offer_id,
                    ]);
                    $imgs[]=$new_images;
                }
            }
            if(sizeof($imgs) == 0)
            {
                return response()->json([$imgs,'status'=>200 ,'message' => 'empty!'],status:200);
            }
            return response()->json([$imgs,'status'=>200 ,'message' => 'success'],status:200);
        }
    }

    public function add_voucher_request(Request $request)
    {
        $validator= Validator::make($request->all(),[
            'name'=>'required|string',
            'description'=>'required|string',
            'point'=>'required|numeric',
            'discount'=>'required|numeric'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        else{
            $voucher = Voucher::create([
                'name' => $request->name,
                'description' => $request->description,
                'point'=>$request->point,
                'discount'=>$request->discount,
                'user_id'=>Auth::id()
            ]);
            return response()->json([$voucher,'status'=>200 ,'message' => 'Voucher request sent successfully'] , status:200);
        }
    }

    public function show_my_cashier($partner_id){
        $cashiers = Cashier::where('partner_id',$partner_id)->get();
        if($cashiers->isEmpty())
        {
            return response()->json([$cashiers,'status'=>200 ,'message' => 'Empty'] , status:200);
        }
        foreach($cashiers as $cashier){
            $cashier->User;
        }
        return response()->json([$cashiers,'status'=>200 ,'message' => 'success'] , status:200);
    }

    public function delete_cashier($id)
    {
        $user = User::where('id',$id)->where('role_id',3)->first();
        $mailcontroller = resolve(MailController::class);
        $mailcontroller->sendEmail(" Dear user,  you were removed by your maneger from loyalty system.
                                    \nfor more information contact us.
                                    \n .عزيزي المشترك لقد تم حذفك من قبل مديرك",$user->email);
        $user->delete();
        return response()->json(['status'=>200 ,'message' => 'User deleted successfully'] , status:200);
    }
}
