<?php
@session_start();
$_SESSION['session_email'] = '';
$_SESSION['session_fullname']='';
unset($_SESSION);
echo "<script language='javascript'>alert('Successfull logout');window.location='login.php'</script>";
?>