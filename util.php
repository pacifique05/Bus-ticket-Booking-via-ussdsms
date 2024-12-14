<?php

require_once 'sms.php';

class Util {
    public static $GoBack = "98";
    public static $BackToMainMenu = "99";

    public static function sendSMS($phone, $message) {
        SMS::send($phone, $message, "TicketSys");
    }
}

?>
