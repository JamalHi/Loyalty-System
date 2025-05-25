<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification as Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class NotificationController extends Controller
{

    public function send(Request $request )
    {
        $deviceToken = $request->input('device_token');
        $title = $request->input('title');
        $body = $request->input('body');

        $messaging = app('firebase.messaging');

        $notification = Notification::create($title, $body);

        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification($notification);

        $messaging->send($message);

        return response()->json(['status'=>200,'message'=>'Notification sent']);
    }

    public function add_device_key(Request $request)
    {
        $user = User::find(Auth::id());

        $user->update(['device_token' => $request->device_token]);
        $user->save();

        return response()->json(['status'=>200,'message'=>'device key added successfully'],status:200);
    }

    public function get_notify()
    {
        $notify = Notify::where('user_id',Auth::id())->orderBy('id','DESC')->get();

        if($notify->isEmpty())
        {
            return response()->json([$notify,'status'=>200 ,'message' => 'Empty'] , status:200);
        }

        return response()->json([$notify,'status'=>200,'message'=>'success'],status:200);
    }
}
