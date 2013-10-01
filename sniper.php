<?php
	require 'twilio-php/Services/Twilio.php';
	require 'mail_user.php';
	set_time_limit(600);
	$_GET['classes'] = '198:205-750:206';
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
	$names = array(
		0 => 'Chris Fretz',
	);
	$emails = array(
		0 => 'cfretz@icloud.com',
	);
	$subject = 'Script Starting';
	$result = mailUserWithParameters($names, $emails, $subject, $message);
	if($result === false) {
		echo 'mail failed to send';
	}
	if(!$_GET['classes']) {
		die('classes must be set');
	}
	$getInfo = explode('-', $_GET['classes']);
	$classes = array();
	foreach($getInfo as $classInfo) {
		$info = explode(':', $classInfo);
		if(!in_array($info[0], array_keys($classes))) {
			$innerArray = array(
				0 => $info[1],
			);
			$classes[$info[0]] = $innerArray;
		} else {
			array_push($classes[$info[0]], $info[1]);
		}
	}
	$counter = 0;
	while($counter < 20) {
		$subjects = array_keys($classes);
		foreach($subjects as $subject) {
			$specificClasses = $classes[$subject];
			$subjectData = json_decode(file_get_contents('http://sis.rutgers.edu/soc/courses.json?subject='.$subject.'&semester=92013&campus=NB&level=U'));
			foreach($subjectData as $classData) {
				if(in_array($classData->courseNumber, $specificClasses)) {
					if($classData->openSections > 0) {
						$messageText = "Hot Diggity, ".$classData->title." is open! Go! Go! Go!";
						$message = $client->account->sms_messages->create(
							'2674332999',
							'2152370055',
							$messageText
						);
						$subject = 'Class Opening';
						$message = 'Hot diggity! <b>'.$classData->title.'</b> is open! Go! Go! Go!';
						mailUserWithParameters($names, $emails, $subject, $message);
					}
				}
			}
		}
		$counter++;
		sleep(30);
	}
?>