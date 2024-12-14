<?php

require 'vendor/autoload.php';
use AfricasTalking\SDK\AfricasTalking;

class SMS {
    private static $username = "sandbox"; 
    private static $apiKey = "atsk_fbba39fba525dcf9141aa20099a7ed8b3bc90023d17b4f2ec51aad1291e6e3e163432686"; 

    public static function send($to, $message, $from = null) {
        $AT = new AfricasTalking(self::$username, self::$apiKey);
        $sms = $AT->sms();

        try {
            $sms->send([
                'to' => $to,
                'message' => $message,
                'from' => $from
            ]);
        } catch (Exception $e) {
            error_log("Error sending SMS: " . $e->getMessage());
        }
    }
}

?>
