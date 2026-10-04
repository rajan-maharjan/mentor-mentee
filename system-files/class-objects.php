<?php

use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\Exception;


/* define of new object for each classes */
$objectAppointment      =   new appointments();
$objectClub             =   new clubs();
$objectFunctions 		= 	new functions();
$objectMeetings         =   new meetings();
$objectMemberClub       =   new MemberClubRole();
$objectRequest 			= 	new requests();
$objectSkill            =   new skills();
$objectUser 			= 	new users();
$objectSendMail         =   new PHPMailer(true);
?>