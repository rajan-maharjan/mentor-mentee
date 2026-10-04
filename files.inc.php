<?php
//phpinfo();
ini_set("date.timezone", "Asia/Kathmandu");
#error_reporting(E_ALL);
session_start();

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