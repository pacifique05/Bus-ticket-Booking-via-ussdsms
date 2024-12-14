<?php

require 'sms.php';

// Example usage for sending bulk SMS
$recipients = ["+250700000001", "+250700000002"]; // Replace with actual numbers
$message = "Don't miss our upcoming discounts on tickets!";

foreach ($recipients as $recipient) {
    SMS::send($recipient, $message);
}

echo "Bulk SMS sent.";

?>
