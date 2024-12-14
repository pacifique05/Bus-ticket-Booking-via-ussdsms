<?php

require 'database.php';
require 'util.php';

// Simulate incoming SMS payload
$phone = $_POST['from'] ?? '';
$message = $_POST['text'] ?? '';

if (strtolower(substr($message, 0, 9)) == "register ") {
    $details = explode(",", substr($message, 9)); // "register full_name,id_number"
    if (count($details) == 2) {
        $fullName = trim($details[0]);
        $idNumber = trim($details[1]);

        if (Database::registerPassenger($phone, $fullName, $idNumber)) {
            Util::sendSMS($phone, "Registration successful! Welcome, $fullName.");
            echo "Registration successful.";
        } else {
            echo "Registration failed.";
        }
    } else {
        echo "Invalid format. Use: 'register FullName,IDNumber'";
    }
} else {
    echo "Unknown command.";
}

?>
