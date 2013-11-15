<?php

function mailUserWithParameters($names, $emails, $subject, $message) {
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'To: ';
    $to = '';
    for($i = 0; $i < count($names); $i++) {
        $headers .= $names[$i].' <'.$emails[$i].'>';
        $to .= $emails[$i];
        if($i == count($names) - 1) {
            $headers .= "\r\n";
        } else {
            $headers .= ", ";
            $to .= ", ";
        }
    }
    $headers .= 'From: Rutgers Schedule Tracker <cfretz@icloud.com>' . "\r\n";
    $result = mail($to, $subject, $message, $headers);
    return $result;
}

?>
