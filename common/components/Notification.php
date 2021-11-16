<?php

namespace common\components;
use autoxloo\fcm\FCMFacade;

class Notification {

    /**
     * @inheritdoc
     */
    public static function send($registrationId, $titulo, $mensagem, $tipoNotificacao)
    {
        // API access key from Google API's Console
        define( 'API_ACCESS_KEY', 'AIzaSyCIV2XLfT4ORbKFAlCP7rwuxZemJUw5BSk' );
        $registrationIds = array( $registrationId );
        // prep the bundle
        $msg = array
        (
            'message'   => $mensagem,
            'title'     => $titulo,
            'subtitle'  => '',
            'tickerText'    => '',
            'vibrate'   => 1,
            'sound'     => 'default',
            'largeIcon' => 'large_icon',
            'smallIcon' => 'small_icon',
            // 'additionalData' => array (
                    "force-start" => "true",
                    "dismissed" => false,
                    "coldstart" => true,
                    "foreground" => false,
                    'tipoNotificacao' => $tipoNotificacao,
                    "content-available" => 1,
                    "priority" => "high"
                // )
        );

        $fields = array
        (
            // 'registration_ids'  => $registrationIds,
            'data'          => $msg,
            'to'  => $registrationId,
            'priority' => 'high',
            // 'notification' => $msg
        );
         
        $headers = array
        (
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );
         
        $ch = curl_init();
        // curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        // echo $result;
        return $result;
    }
}