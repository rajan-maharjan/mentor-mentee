<?php
//phpinfo();
ini_set("date.timezone", "Asia/Kathmandu");
error_reporting(E_ALL);

// Harden session cookie: not readable by JS, and not sent cross-site, which
// mitigates session theft via XSS and some CSRF vectors. 'secure' is left to
// the server's HTTPS config (enable it once the site is served over TLS).
session_set_cookie_params([
	'httponly' => true,
	'samesite' => 'Lax',
]);
session_start();

// Global output-escaping helper used throughout the templates to prevent XSS.
if(!function_exists('h')){
	function h($v){
		return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
	}
}
#die("INSIDE FILES");
//require 'PHPMailer/Exception.php';
require 'PHPMailer/SMTP.php';


// Import PHPMailer classes into the global namespace

/* constant declaration */
include $relativePath."system-files/settings.php";
include $relativePath."system-files/constant.php";

include $relativePath.CLASS_PATH."connection.class.php";
$connectionObject = new connection($dbHostName,$dbUserName,$dbUserPwd,$dbName);

$connectionObject->dbConnect();
include $relativePath.CLASS_PATH."common.class.php";
include $relativePath.CLASS_PATH."appointment.class.php";
include $relativePath.CLASS_PATH."clubs.class.php";
include $relativePath.CLASS_PATH."functions.class.php";
include $relativePath.CLASS_PATH."meetings.class.php";
include $relativePath.CLASS_PATH."memberclubrole.class.php";
include $relativePath.CLASS_PATH."requests.class.php";
include $relativePath.CLASS_PATH."skills.class.php";
include $relativePath.CLASS_PATH."users.class.php";
include $relativePath.CLASS_PATH."phpmailer.class.php";

include $relativePath."system-files/class-objects.php";
?>