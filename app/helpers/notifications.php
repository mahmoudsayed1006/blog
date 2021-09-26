<?php
use App\Models\User\User;

 function sendNotification($notifi){
    //$notifi = json_decode(json_encode($notifi));
   
    $url = 'https://fcm.googleapis.com/fcm/send';
    $FcmToken = User::where([['id','=',$notifi["targetUser"]]])->select('token')->first();
    //error_log($FcmToken->token);
    $serverKey = env('FCM_KEY');
    //error_log("token".$FcmToken->token);
    $payload = [
        "registration_ids" =>$FcmToken->token,
        "notification" => [
            "title" => $notifi["text"],
            "sound" => 'default',
            "itemID" => $notifi["subject"],
            "body" => $notifi["subjectType"],
            "info" =>array_key_exists("info", $notifi)?$notifi["info"]:"",
            "priority" =>'high',
            "content_available" => array_key_exists("content_available", $notifi)?$notifi["content_available"]:false,
        ],
        "data" => [
            "title" => $notifi["text"],
            "sound" => 'default',
            "itemID" => $notifi["subject"],
            "body" => $notifi["subjectType"],
            "info" =>array_key_exists("info", $notifi)?$notifi["info"]:"",
            "priority" =>'high',
            "content_available" => array_key_exists("content_available", $notifi)?$notifi["content_available"]:false,
        ],
    ];
    $encodedData = json_encode($payload);

    $headers = [
        'Authorization:key=' . $serverKey,
        'Content-Type: application/json',
    ];

    $ch = curl_init();
   
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    // Disabling SSL Certificate support temporarly
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        
    curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);

    // Execute post
    $result = curl_exec($ch);

    if ($result === FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }        

    // Close connection
    curl_close($ch);

    // FCM response
    error_log($result);        
 }

?>