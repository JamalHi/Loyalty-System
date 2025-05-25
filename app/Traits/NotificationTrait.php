<?php
namespace app\Traits;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

trait NotificationTrait {
    public function send_notify($deviceToken,$title,$body)
    {
        $messaging = app('firebase.messaging');

        $notification = Notification::create($title, $body);

        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification($notification);

        $messaging->send($message);

        return response()->json(['status' => 'Notification sent']);
    }
}
