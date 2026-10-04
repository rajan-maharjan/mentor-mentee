<?php
$relativePath ='';
include "files.inc.php";

if($_POST['choice']=='sendrequest'){
	$mem_id = $_POST['receiver'];	
	$memberDetail = $objectUser->getDetail($mem_id);
	$senderDetail = $objectUser->getDetail($_SESSION['session_user_id']);
	$body= "Dear TM ".$memberDetail->full_name.",<br />
	As per your profile in mentor-mentee relationship management system, you are open to accept as individual mentor. Hence one of the Toastmasters would like to have you as mentor for ".(($senderDetail->gender=='M')?'him':'her').". Please find detail below of ".(($senderDetail->gender=='M')?'him':'her').".<br /><br />
	Name: ".$_SESSION['session_fullname']."<br />
	Email: ".$senderDetail->email."<br />
	Mobile: ".($senderDetail->show_mobile=='Y'?($senderDetail->mobile_number):'')."<br />";
	
	$insertData['requested_to']=$mem_id;
	$insertData['requested_by']=$_SESSION['session_user_id'];
	$insertData['requested_on']=date("Y-m-d H:i:s");
	$insertData['status']='P';
	$insertData['is_active']='1';
	$objectUser->insertMentorMentee($insertData);
	echo "Request Sent.";
	$objectFunctions->sendEmail(SITE_ADMIN_EMAIL," Request to be mentor for ".$_SESSION['session_fullname'],$body);
	
	}
elseif($_POST['choice']=='cancelrequest'){
	$request_id = $_POST['request_id'];
	$requestDetail=$objectRequest->getDetail($request_id);
	$requestedBy=$requestDetail->requested_by;

	$mentorDetail = $objectUser->getDetail($requestDetail->requested_to);
	$menteeDetail = $objectUser->getDetail($requestDetail->requested_by);

	if($requestDetail->requested_by!=$_SESSION['session_user_id']){		
		$body= "Dear TM ".$menteeDetail->full_name.",<br />
		Your mentorship request to ".$mentorDetail->full_name." has been cancelled/rejected due to ".(($mentorDetail->gender=='M')?'his':'her')." other priority tasks. The TM ".$mentorDetail->full_name." have expressed extreme sorry for the same. <br />You may request mentorship to other TMs available in the portal <a href ='".SITE_PATH."'>".SITE_NAME."</a><br />
		Thank you.";
		$emailSend = $objectFunctions->sendEmail(SITE_ADMIN_EMAIL," Cancellation of mentor request to ".$menteeDetail->full_name,$body);

		$body= "Dear TM ".$mentorDetail->full_name.",<br />
				Thank you for giving time and checking the ".SITE_NAME.". We understand that you might have rejected due to your busy schedule and other priority tasks. However we request you to keep on visiting the portal <a href ='".SITE_PATH."'>".SITE_NAME."</a> and share your mentorship knowledge with the needy one. If incase you have mistakenly rejected/canceled the request, you may directly contact requestor on below detail:<br ><br />
				Name: ".$menteeDetail->full_name."<br />
				Email: ".$menteeDetail->email."<br />
				Mobile: ".($menteeDetail->show_mobile=='Y'?($menteeDetail->mobile_number):'')."<br />
		Thank you.";
		$emailSend = $objectFunctions->sendEmail(SITE_ADMIN_EMAIL," Cancellation of mentor request by ".$menteeDetail->full_name,$body);
		}
		else{
			$body= "Dear TM ".$mentorDetail->full_name.",<br />
			We understand that you have cancelled the request due to some reason. If you are looking for any other mentors you may access and search in the portal <a href ='".SITE_PATH."'>".SITE_NAME."</a><br />
			Thank you.";
			$emailSend = $objectFunctions->sendEmail(SITE_ADMIN_EMAIL," Cancellation of mentor request to ".$menteeDetail->full_name,$body);
		}
	if($emailSend==false)
		echo "Email could NOT be sent.";
	$objectRequest->cancelRequest($request_id);
	echo 1;
	}

elseif($_POST['choice']=='approverequest'){
	$request_id = $_POST['request_id'];
	$objectRequest->approveRequest($request_id);
	echo 1;
	}
elseif($_POST['choice']=='deleteappointment'){
	$objectAppointment->remove($_POST['request_id']);
	echo 1;
	}
?>