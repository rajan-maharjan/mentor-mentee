<?php
class functions extends common{

  	function __construct(){

		}

	function getJWTToken($username, $password) {
        $url = TIN_WEBSITE.'wp-json/jwt-auth/v1/token';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'username' => $username,
            'password' => $password,
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
        }

        curl_close($ch);

        $response = json_decode($result);
        return $response->token; // This is the JWT token
    }

	function maskEmail($email) {
        // Split the email into username and domain parts
        list($username, $domain) = explode("@", $email);
        $usernameLength = strlen($username);
        $visibleChars = 3;
        $maskedChars = $usernameLength - $visibleChars;


        $maskedUsername = substr($username, 0, $visibleChars) . str_repeat('*', $maskedChars);
        $maskedEmail = $maskedUsername . "@" . $domain;

        return $maskedEmail;
    }

	function sendEmail($receiverEmail, $subject, $content) {
		global $objectSendMail;

		try{
		    $objectSendMail->SMTPDebug = 0;                                       // Enable verbose debug output
            $objectSendMail->isSMTP();
		    $objectSendMail->Host       = EMAIL_HOST;                     // Specify main and backup SMTP servers
            $objectSendMail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $objectSendMail->Username   = ADMIN_EMAIL;               // SMTP username
            $objectSendMail->Password   = SMTP_PASSWORD;                  // SMTP password
            $objectSendMail->Port       = 587;
            $objectSendMail->isHTML(true);
            $objectSendMail->setFrom(NO_REPLY_EMAIL, SITE_NAME);


    		$objectSendMail->addAddress($receiverEmail);
    		#$objectSendMail->addAddress("friendship.rajan@gmail.com");
    		$objectSendMail->Subject = $subject;

    		$arrayReplaceFor=array("#PATH#","#LOGO#","#SITE_NAME#","#CONTENT#","#FOOTER#");
    		$arrayReplaceWith=array(
    					SITE_PATH,
    					"<img src='".IMAGE_PATH."logo.png' style='border:none;'>",
    					SITE_NAME,
    					$content,
    					''
    					);
    		$finalContent=str_replace($arrayReplaceFor,$arrayReplaceWith,file_get_contents("email.tmpl"));

    		$objectSendMail->Body = html_entity_decode($finalContent);
    	    $objectSendMail->send();
		 	} catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
	}

	function generateStrongPassword($length = 8){
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
		$count = strlen($chars);

		for ($i = 0, $result = ''; $i < $length; $i++) {
			$index = random_int(0, $count - 1);
			$result .= substr($chars, $index, 1);
		}

		return $result;
	}

	// Returns the CSRF token for the current session, creating one if needed.
	function getCsrfToken(){
		if(empty($_SESSION['csrf_token'])){
			$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
		}
		return $_SESSION['csrf_token'];
	}

	// Validates a token submitted by a form/AJAX call against the session's token.
	function validateCsrfToken($submittedToken){
		if(empty($_SESSION['csrf_token']) || empty($submittedToken)){
			return false;
		}
		return hash_equals($_SESSION['csrf_token'], $submittedToken);
	}

	function isValidEmail($input){
	    $emailRegex =  '/^[a-zA-Z0-9._]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,5}$/';
	    return preg_match($emailRegex, trim($input));
	}
	
	function isValidMobile($input){
	    //$phoneRegex = '/^9\d{9}$/';
        $phoneRegex = '/^(?:(?:\+?977|00977)?(?:9[78]\\d{8})|(?:\+?91|0091)?[6-9]\\d{9}|(?:\+?88|0088)?01[3-9]\\d{8})$/';
        return preg_match($phoneRegex, trim($input));
	}

	function isValidateContact($input) {
        $isCorrect = false;
        if ($this->isValidEmail($input)) {
           $isCorrect = true;
        } elseif ($this->isValidMobile($input)) {
            $isCorrect = true;
        } else {
            $isCorrect = false;
        }
        return $isCorrect;
    }


	function getPageName($page){
		switch($page){
			case 'fb-update':$pageName="updateFB";break;
			default: $pageName=$page;
		}
		return $pageName;
	}

	function filterPage(){
		if( ! isset($_GET['page']) || (isset($_GET['page']) && trim($_GET['page'])!='' && ! file_exists('pages/'.trim($_GET['page']).'.php'))){
			 echo "<script>window.location='index.php'</script>";
		}
		else
			return $_GET['page'];
	}

	function __destruct(){

	}

}
