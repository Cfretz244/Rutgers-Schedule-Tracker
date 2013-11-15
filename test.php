<?php

require 'twilio-php/Services/Twilio.php';

$message = "Welcome to the Rutgers Schedule Tracker. Your classes will be checked every 30 seconds, and you will be notified if any of them open.";

$sid = 'AC3c4e47084e170e028847ee3dbfef6cd0';

$token = '10c79fad2ebb7911cb8c1f9c2a5f1ad8';

$client = new Services_Twilio($sid, $token);

$client->account->sms_messages->create(
    '2674332999',
    '8138920100',
    $message
);
?>
