<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\User;
use App\Models\Client;
use App\Models\Charity;
use App\Models\History;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

use App\Traits\NotificationTrait;

class client_controller extends Controller
{
    use NotificationTrait;

    public function show_all_charities()
    {
        $users = Cache::remember('Charities' ,60*60*24,function(){
            return  User::where('role_id', 5)->get();
        });

        if($users->isEmpty())
        {
            return response()->json([$users,'status'=>200 ,'message' => 'Empty'] , status:200);
        }
        // $user = User::where('role_id',5)->get();
        foreach ($users as $user) {
            $user->Charity;
        }

        return response()->json([$users,'status'=>200 ,'message' => 'Success'] , status:200);
    }

    public function watch_ad()
    {
        $user_id = Auth::id();
        $client = Client::where('user_id', $user_id)->first();
        if($client->ad_view_counter >= 3)
        {
            return response()->json(['status'=>400 ,'message' => "can't watch more than 3 ads in a day !"] , status:400);
        }

        $ads_valid = Ad::where('valid',1)->get()->random()->id;
        if(is_Null($ads_valid))
        {
            return response()->json([$ads_valid,'status'=>200 ,'message' => 'No ads to show'] , status:200);
        }
        $ad = Ad::where('id',$ads_valid)->first();

        return response()->json([$ad,'status'=>200 ,'message' => 'Success'] , status:200);
    }

    //new
    public function get_points_from_ad($user_id , $ad_id){
        $client = Client::where('user_id',$user_id)->first();
        $ad = Ad::find($ad_id);

        $ad->view_count +=1;
        $ad->save();
        if($client->special_exp_date < now()){
            $client->special_points = 0;
            $client->special_exp_date = now()->addMonths(6);
            $client->save();
        }
        $client->ad_view_counter +=1;
        $client->special_points +=1;
        $client->save();

        return response()->json(['status'=>200 ,'message' => '+1 special point.'] , status:200);
    }

    public function donate_special_points(Request $request)
    {
        $user = User::find(Auth::id());
        $client = Client::where('user_id', $user->id)->first();
        $user_charity = User::where('id',$request->id)->first();
        $charity = Charity::where('user_id',$user_charity->id)->first();
        if($request->points > $client->special_points )
        {
            return response()->json(['status'=>400 ,'message' => 'Not enough Special points'] , status:400);
        }
        if($request->points <= 0){
            return response()->json(['status'=>400 ,'message' => 'Invalid operation'] , status:400);
        }
        $client->special_points -= $request->points;
        $client->save();

        if($charity->points_exp_date < now()){
            $charity->points = 0;
            $charity->points_exp_date = now()->addMonths(6);
            $charity->save();
        }

        $point_with_percentage = $request->points * 30/100;
        $charity->points += $point_with_percentage;
//        $charity->points_exp_date = now()->addMonths(6);
        $charity->save();

        if($request->points >= 21){
            $gift =floor($request->points / 21);
            $client->points += $gift;
            $client->save();
        }

        $history= History::query()->create([
            'operation' => "donate points",
            'transfer_points' => $request->points,
            'transfer_time' =>now(),
            'from_user' => $user->id,
            'to_user' => $charity->user_id
        ]);

        //notification to client
        if($user_charity->device_token != null){
            $this->send_notify($user_charity->device_token,"Loyality System","you recieved $point_with_percentage points from $user->name");
            $notify = Notification::query()->create([
                'title' => "Loyality System",
                'body' => "you recieved $point_with_percentage points from $user->name",
                'user_id' => $user->id,
            ]);
        }
        return response()->json(['status'=>200 ,'message' => 'Success'] , status:200);
    }

    public function transfer_point_to_friend(Request $request)
    {
        $user = User::find(Auth::id());
        $client = Client::where('user_id', $user->id)->first();
        $user2 = User::where('email',$request->email)->first();
        if(is_Null($user2))
        {
            return response()->json([$user2,'status'=>400 ,'message' => "user Doesn't exist"] , status:400);
        }
        else if ($user2->id == $user->id)
        {
            return response()->json(['status'=>403 ,'message' => "you can't do that"] , status:403);
        }
        else if ($user2->role_id != 4)
        {
            return response()->json(['status'=>403 ,'message' => "you can only share you points with a customer."] , status:403);
        }
        $client2 = Client::where('user_id',$user2->id)->first();

        if($request->points > $client->points )
        {
            return response()->json(['status'=>400 ,'message' => 'Not enough  points'] , status:400);
        }
        if($request->points <= 0){
            return response()->json(['status'=>400 ,'message' => 'Invalid operation'] , status:400);
        }
        $client->points -= $request->points;
        $client->save();
        if($client2->points_exp_date < now()){
            $client2->points = 0;
            $client2->points_exp_date = now()->addMonths(6);
            $client2->save();
        }
        $client2->points += $request->points;
        //$client->points_exp_date=now()->addMonths(6);
        $client2->save();
        $history= History::query()->create([
            'operation' => "transfer",
            'transfer_points' => $request->points,
            'transfer_time' =>now(),
            'from_user' => $user->id,
            'to_user' => $user2->id
        ]);

        //notification to client
        if($user2->device_token != null){
            $this->send_notify($user2->device_token,"Loyality System","you recieved $request->points points from $user->name");
            $notify = Notification::query()->create([
                'title' => "Loyality System",
                'body' => "you recieved $request->points points from $user->name",
                'user_id' => $user2->id,
            ]);
        }
        return response()->json(['status'=>200 ,'message' => 'Success'] , status:200);
    }
}
