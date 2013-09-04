<?php
	require 'twilio-php/Services/Twilio.php';
	$sid = 'AC3c4e47084e170e028847ee3dbfef6cd0';
	$token = '10c79fad2ebb7911cb8c1f9c2a5f1ad8';
	$client = new Services_Twilio($sid, $token);
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
	while(true) {
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
					}
				}
			}
		}
		sleep(10);
	}
?>