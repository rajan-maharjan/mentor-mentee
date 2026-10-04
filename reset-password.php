<?php
include "files.inc.php";
error_reporting(E_ALL);
$class="error-message-box";
$newPassword = $objectFunctions->generateStrongPassword(8);
$accode = $_GET['hcRM'];
$requestDetail = $objectUser->selectRow('unlock_request',array('email_id','requested_on'),"hash_key='$accode'");

if (!isset($requestDetail)) {
	$_SESSION['message'] = "Your request is INVALID.";
	$_SESSION['hasError']=true;
	}
else{
	$senderEmail = $requestDetail->email_id;
	$requestedOn = $requestDetail->requested_on;
	if((time()-strtotime($requestedOn))>(60*10)){
		$_SESSION['message'] = "Your session for request has been expired. Please request again.";
		$_SESSION['hasError']=true;
		}
	else{
	    
		$objectFunctions->update('members',array('pass_word'=>md5($newPassword), 'pwd_reset_request'=>'Y'), "email='$senderEmail'");
		
		$subject="Congratulation! You have secured your account for ".SITE_NAME;			
		
		$objectFunctions->update('unlock_request',array('hash_key'=>''), "hash_key='$accode'");
		
		$body="As per your request, your password has been changed to <strong>$newPassword</strong> We strongly recommend you to change your password at first login. 
		Please do not reply to this email as this is a system generated message.";
		
		$objectFunctions->sendEmail($senderEmail, $subject, $body);
		$_SESSION['hasError']=false;
		echo $_SESSION['message'] = "SUCCESS! Your new password has been sent to your email. Please check your INBOX or JUNK mail.";
				
	}
unset($_POST);
$_POST = array();
}
?>
<script language='javascript'>window.location='<?php echo SITE_PATH?>login.php'</script>