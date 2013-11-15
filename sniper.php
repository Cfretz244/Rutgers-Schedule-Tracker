<?php
require 'twilio-php/Services/Twilio.php';
require 'mail_user.php';
set_time_limit(600);
$rawNumbers = '575:201:90-750:203:06|07-750:205:01';
$sid = 'AC3c4e47084e170e028847ee3dbfef6cd0';
$token = '10c79fad2ebb7911cb8c1f9c2a5f1ad8';
$client = new Services_Twilio($sid, $token);
$date = date('l, F jS, Y. g:i: ');
$message = $date.'Script starting...';
// $client->account->sms_messages->create(
// 	'2674332999',
// 	'2152370055',
// 	$message
// );
ob_start();
$names = array(
    0 => 'Chris Fretz',
);
$emails = array(
    0 => 'cfretz@icloud.com',
);
$subject = 'Script Starting';
if($result === false) {
    echo 'mail failed to send';
}
$explodedNumbers = explode('-', $rawNumbers);
$classes = array();
foreach($explodedNumbers as $class) {
    $classInfo = explode(':', $class);
    if(!isset($classes[$classInfo[0]])) {
        $sectionArray = array();
        $courseArray = array(
            $classInfo[1] => $sectionArray,
        );
        $classes[$classInfo[0]] = $courseArray;
    } else {
        $sectionArray = array();
        $classes[$classInfo[0]][$classInfo[1]] = $sectionArray;
    }
    $sections = explode('|', $classInfo[2]);
    foreach($sections as $section) {
        array_push($classes[$classInfo[0]][$classInfo[1]], $section);
    }
}
print_r($classes);
for($i = 0; $i < 20; $i++) {
    $subjectKeys = array_keys($classes);
    foreach($subjectKeys as $subjectKey) {
        $subjectData = json_decode(file_get_contents("http://sis.rutgers.edu/soc/courses.json?subject=".$subjectKey."&semester=12014&campus=NB&level=U"));
        $subjectArray = $classes[$subjectKey];
        $classKeys = array_keys($subjectArray);
        foreach($classKeys as $classKey) {
            foreach($subjectData as $classData) {
                if($classData->courseNumber != $classKey) {
                    continue;
                }
                $classArray = $subjectArray[$classKey];
                $sectionKeys = array_keys($classArray);
                foreach($classData->sections as $sectionData) {
                    foreach($sectionKeys as $sectionKey) {
                        $section = $classArray[$sectionKey];
                        if($sectionData->number == $section && $sectionData->openStatus == true) {
                            $message = 'Hot diggity! Section number '.$section.' of '.$classData->title.' is open! Go! Go! Go!'."\n";
                            $client->account->sms_messages->create(
                                '2674332999',
                                '8138920100',
                                $message
                            );
                            $subject = 'Class Opening';
                            mailUserWithParameters($names, $emails, $subject, $message);
                            $names = array(
                                0 => 'Miriam Wallach',
                            );
                            $emails = array(
                                0 => 'miriamwallach1@gmail.com',
                            );
                            $result = mailUserWithParameters($names, $emails, $subject, $message);
                            break;
                        }
                    }
                }
            }
        }
    }
    sleep(30);
}
$arrayContents = ob_get_contents();
ob_end_clean();
file_put_contents('array.txt', $arrayContents);
?>
