<?php
include "files.inc.php";
$class="error-message-box";
$newPassword = $objectFunctions->generateStrongPassword(8);
$accode = $_GET['hcRM'] ?? '';

// Whitelist validation: a reset token must be exactly a 64-char hex string
// (see forgot-password.php, which now generates it via random_bytes(32)).
// Anything else is rejected before it ever touches a SQL query - this closes
// the unauthenticated SQL injection that previously existed here.
$requestDetail = array();
if(preg_match('/^[a-f0-9]{64}$/', $accode)){
	$safeAccode = $objectUser->escape($accode);
	$requestDetail = $objectUser->selectRow('unlock_request',array('email_id','requested_on'),"hash_key='$safeAccode'");
}

if (empty($requestDetail)) {
	$_SESSION['message'] = "Your request is INVALID.";
	$_SESSION['hasError']=true;
	}
else{
	$senderEmail = $requestDetail->email_id;
	$safeSenderEmail = $objectUser->escape($senderEmail);
	$requestedOn = $requestDetail->requested_on;
	if((time()-strtotime($requestedOn))>(60*10)){
		$_SESSION['message'] = "Your session for request has been expired. Please request again.";
		$_SESSION['hasError']=true;
		}
	else{

		$objectFunctions->update('members',array('pass_word'=>password_hash($newPassword, PASSWORD_DEFAULT), 'pwd_reset_request'=>'Y'), "email='$safeSenderEmail'");

		$subject="Congratulation! You have secured your account for ".SITE_NAME;

		$objectFunctions->update('unlock_request',array('hash_key'=>''), "hash_key='".$objectUser->escape($accode)."'");

		$body="As per your request, your password has been changed to <strong>".htmlspecialchars($newPassword, ENT_QUOTES, 'UTF-8')."</strong> We strongly recommend you to change your password at first login.
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