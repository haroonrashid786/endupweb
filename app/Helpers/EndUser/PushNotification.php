<?php


namespace App\Helpers\EndUser;

use App\Models\EndUsers;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

trait PushNotification{

    public function sendPushNotification($id,$title,$message,$url)
    {
        if (!empty($id)) {
            if (is_array($id)) {
                $fcmToken = EndUsers::whereIn('id', $id)->pluck('device_token')->toArray();
            } else {
                $fcmToken = EndUsers::where('id', $id)->pluck('device_token')->toArray();
            }

        $fcmServerKey = config::get('enduser.FCM_SERVER_KEY');
      
        foreach ($fcmToken as $token) {
            if (!empty($token)) {

        $headers = [
            'Authorization' => 'key=' . $fcmServerKey,
            'Content-Type' => 'application/json',
        ];
    
        $notification = [
            'title' => 'Notification Title',
            'body' => 'Notification Body',
            'sound' => 'default',
            "icon" => "new",
        ];
    
        $data = [
            "title" => '$title',
            "type" => '$type',
            "message" => '$message',
            "body" =>  '$socketData'
        ];

        $payload = [
            'to' => $token,
            'notification' => $notification,
            'data' => $data,
        ];
    
        $response = Http::withHeaders($headers)->post('https://fcm.googleapis.com/fcm/send', $payload);
        info($response);
        return $response;
        if ($response->successful()) {
            return "Notification sent successfully.";
        } else {
            return "Failed to send notification.";
        }
    }
        }
    }

}


}
?>

