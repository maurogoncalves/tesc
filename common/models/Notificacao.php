<?php

namespace common\models;

use Yii;
// COMO USAR ONESIGNAL: 
//Notificacao::sendOneSignal('title','content',['24277125-a405-4bbe-9fd9-1b66ad3e3a3e']); 


// COMO USAR O FIREBASE
//Modelo de mensagem 
// $msg = [
//       'message'   => 'here is a message. message',
//       'title'   => 'This is a title. title',
//       'subtitle'  => 'This is a subtitle. subtitle',
//       'tickerText'  => 'Ticker text here...Ticker text here...Ticker text here',
//       'vibrate' => 1, 
//       'sound'   => 1,
//       'largeIcon' => 'large_icon',
//       'smallIcon' => 'small_icon'
//     ];
//Notificacao::sendFirebase($msg, ['fcq8SEWkDpk:APA91bGKvoEyi6jnkMo07LAaz4v5UNtM8Lfnt7zP-WIcoNfLss-e083QbbdUMsmNNTK5AFs3MFYe81jArbD7rWm1uovn5OSwmUgQrFQLsxh2ObsaI3R8MKMcY_H4s7BqqJOTjhXir7wM']);

class Notificacao
{
 

    public static $oneSignalAppId = '';
    public static $oneSignalApiToken = '';
    //notificacao firebase tesc
    //public static $firebaseApiToken = 'AIzaSyBiZYL7RnfXP4SGgR7B990-mj89xYru-lY';  //Chave de API da Web
    public static $firebaseApiToken = 'AIzaSyAsxYZrFvLI1Ajklgf-G02rf7F4H7rj71U';  //Chave de API da Web


    public static function sendOneSignal($heading, $content, $usuarios, $data=[]){
        //Seta um default para os dados do push
        if(!$data)
          $data = ['foo'=>'bar'];
 
          // Remove Usuários duplicados do array
         $usuarios = array_unique($usuarios);

         $content = [
            "en" => $content
         ];

        $fields = [
            'app_id' => self::$oneSignalAppId,
            'include_player_ids' => $usuarios,
            'data' => $data,
            'large_icon' =>"ic_launcher_round.png",
            'headings'=> ["en" => $heading],
            'contents' => $content,
            'android_background_data' => false,
            'delayed_option'=> "immediate",
            'android_accent_color'=> "FFFAE315"    
        ];

       $fields = json_encode($fields);
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
       curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8','Authorization: Basic '.self::$oneSignalApiToken));
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
       curl_setopt($ch, CURLOPT_HEADER, FALSE);
       curl_setopt($ch, CURLOPT_POST, TRUE);
       curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    
       $response = curl_exec($ch);
       curl_close($ch);
       $x= json_decode($response);
        if(isset($x->errors)){
          
          $fp = fopen($_SERVER['DOCUMENT_ROOT'].'/uploads/pushlog.txt', 'a');
          fwrite($fp, PHP_EOL.'['.date('d/m/Y H:i:s').'] ['.implode("|",$peoples).'] ['.implode("|",$x->errors).']');
          fclose($fp);
        }
       return $response;
    }


    public static function sendFirebase($msg, $registrationIds){
          $fields = array
          (
            'registration_ids'  => $registrationIds,
            'data'      => $msg
          );
           
          $headers = array
          (
            'Authorization: key=' .self::$firebaseApiToken,
            'Content-Type: application/json'
          );
           
          $ch = curl_init();
          curl_setopt( $ch,CURLOPT_URL, 'fcm.googleapis.com/fcm/send' );
          curl_setopt( $ch,CURLOPT_POST, true );
          curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
          curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
          curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
          curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
          $result = curl_exec($ch );
          print_r($result);
          curl_close( $ch );
          if($result)
            return json_decode($result);
          return [];
    }

    public static function sendDirectFirebase($registrationId, $text, $title='Atenção!'){
        $msg = [
              'message'   => $text,
              'title'   => $title,
              'subtitle'  => '',
              'tickerText'  => $text,
              'vibrate' => 1,
              'sound'   => 1,
              'idSolicitacao' => '',
              'play' => 0, 
              'anexo' => '',
              'largeIcon' => 'large_icon',
              'smallIcon' => 'small_icon'
        ];
        return self::sendFirebase($msg, [$registrationId]);

    }
    
}