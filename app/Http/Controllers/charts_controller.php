<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Client;
use App\Models\Cashier;
use App\Models\Charity;
use App\Models\History;
use App\Models\Partner;
use Illuminate\Http\Request;


class charts_controller extends Controller
{
    public function chart (request $request)
    {
        $user = User::find($request->user_id);
        $points = 0;

        if($user->role_id == 2)
        {
            $partner = Partner::where('user_id',$user->id)->first();
            $points = $partner->points;
        }

        else if($user->role_id == 4)
        {
            $client = Client::where('user_id',$user->id)->first();
            $points = $client->points;
        }

        else if($user->role_id == 5)
        {
            $charity = Charity::where('user_id',$user->id)->first();
            $points = $charity->points;
        }

        $array = [] ;
        $now = Carbon::now();
        $today = Carbon::now();
        $time = Carbon::now();

        $history = History  ::where('from_user',$user->id)
                            ->orWhere('to_user',$user->id)
                            ->get()
                            ;

        for($i=0;$i<4;$i++)
        {
            $array["$now"] = $points;

            $t1 = $today;
            $t1 = $t1->subWeek()->toDateTimeString();
            $t2 = Carbon::parse($t1);

            foreach($history as $h)
            {
                if($h->transfer_time <= $now->toDateTimeString() && $h->transfer_time > $t1 )
                {
                    if(($h->operation == 'add') || ($h->operation == 'transfer' && $h->to_user == $user->id))
                        {
                            $points -= $h->transfer_points;
                        }
                    else if($h->operation == 'buy voucher' ||$h->operation == 'buy_offer'
                            || ($h->operation == 'transfer' && $h->from_user == $user->id))
                        {
                            $points += $h->transfer_points;
                        }
                }
            }
            $now = $t2;

        }
        $array["$now"] = $points;
        $reversed = array_reverse($array);

        return response()->json([ $reversed, 'status' => 200 , 'message' => 'success']);
    }

    public function user_count(request $request)
    {
        $clients = Client::get()->count();
        $charities = Charity::get()->count();
        $partners = Partner::get()->count();
        $cashiers = Cashier::get()->count();

        $data['clients']=$clients;
        $data['charities']=$charities;
        $data['partners']=$partners;
        $data['cashier']=$cashiers;

        return response()->json([ $data, 'status' => 200 , 'message' => 'success']);
    }

    public function test(request $request)
    {
        $array1 = array(100,500,70,10);
        $array2= array(1=>100,2=>500,3=>70,4=>10);
        if($request->num==1)
        {
            return response()->json([ $array1, 'status' => 200 , 'message' => 'success']);
        }
        else{
            return response()->json([ $array2, 'status' => 200 , 'message' => 'success']);
        }
    }
}
