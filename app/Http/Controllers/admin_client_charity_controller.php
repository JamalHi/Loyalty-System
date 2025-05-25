<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Partner;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\Cache;

class admin_client_charity_controller extends Controller
{
    public function show_all_offers()
    {
        $offers = Cache::remember('Offers' ,60*60*24,function(){
            return  Offer::all();
        });

        if($offers->isEmpty())
        {
            return response()->json([$offers,'status'=>200 ,'message' => 'Empty'] , status:200);
        }
        foreach($offers as $offer)
        {
            if(now() > $offer->end_time)
            {
                $offer->delete();
            }
            $offer->user;//new
            $offer->images;//new
        }
        return response()->json([$offers,'status'=>200 ,'message' => 'Success'] , status:200);
    }

    public function show_all_vouchers()
    {
        $vouchers = Cache::remember('Vouchers' ,60*60*24,function(){
            return  Voucher::where('accept',1)->get();
        });

        if($vouchers->isEmpty())
        {
            return response()->json([$vouchers,'status'=>200 ,'message' => 'Empty'] , status:200);
        }
        foreach($vouchers as $voucher){//new
            $voucher->partner;
        }

        return response()->json([$vouchers,'status'=>200 ,'message' => 'Success'] , status:200);
    }

    public function show_partner_details($id)
    {
        $user = User::where('role_id' , 2)->where('id',$id)->first();
        if(is_Null($user))
        {
            return response()->json([$user,'status'=>200 ,'message' => 'Empty'] , status:200);
        }
        $partner = Partner::where('user_id' , $user->id)->first();
        $response = ([
            'user' => $user,
            'partner' => $partner
        ]);
        return response()->json([$response,'status'=>200 ,'message' => 'Success'] , status:200);
    }

    public function most_bought_vouchers()
    {
        $vouchers = Cache::remember('Vouchers' ,60*60*24,function(){
            return voucher::orderBy('counter','desc')->take(10)->get();
        });
        if(is_Null($vouchers))
        {
            return response()->json([$vouchers,'status'=>200 ,'message' => 'there are no vouchers yet'] , status:200);
        }
        foreach($vouchers as $voucher){
            $voucher->partner;
        }
        return response()->json([$vouchers,'status'=>200 ,'message'=> 'success'] , status:200);
    }
}
