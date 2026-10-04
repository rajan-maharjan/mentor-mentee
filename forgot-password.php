<!DOCTYPE html>
<html lang="en">
<?php
$relativePath='';
include "files.inc.php";
?>
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?php echo SITE_NAME?></title>
  <!-- plugins:css -->

  <link rel="stylesheet" href="<?php echo CSS_PATH?>style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="<?php echo IMAGE_PATH?>favicon.png" />
</head>
<body>

<?php
$messageText="";
$hasError=false;

if(isset($_POST['btnResetPwd'])){
  $email_mobile=strtolower(trim($_POST['email_mobile']??''));
  $hasError=true;

if($objectFunctions->isValidateContact($email_mobile)==false){
    $hasError=true;
    $messageText="Opps! Please enter valid mobile number (e.g: 9XXXXXXXX0) or valid email address (e.g: example@domain.com).";
}
else{
      $userInformation = $objectUser->getmemberIdFromEmailOrMobile($email_mobile);

      if(!empty($userInformation)){
        $insertArrayData = array();
        $insertArrayData['requested_for'] = 'PWD_RST';
        $insertArrayData['requested_ip'] = $_SERVER['REMOTE_ADDR'];
        $insertArrayData['requested_on'] = date("Y-m-d H:i:s");
        $insertArrayData['email_id'] = $userInformation->email;
        $insertArrayData['hash_key'] = $acccode = bin2hex(random_bytes(32));
        $objectFunctions->insert('unlock_request',$insertArrayData);

        $activationLink = SITE_PATH."reset-password.php?hcRM=".$acccode;

    	$subject="Confirmation to change password: ".SITE_NAME;

        $content="Dear $userInformation->full_name<br />
        Please be informed that either you or someone else have requested to change the password of ".SITE_NAME.". If you have made the request then please <a href='$activationLink' target='_blank'><strong>click here</strong></a>
        or <strong>copy paste</strong> the $activationLink to your browser within 10 minutes.<br />
      However, if you have <strong>NOT</strong> initiated this request then please <strong>IGNORE/DISCARD</strong> this email and your password will <strong>NOT BE</strong> change. Please do not reply to this email as this is a system generated message. The request was made from/with following details.";

          $objectFunctions->sendEmail($insertArrayData['email_id'],$subject, $content);
          $hasError=false;
      }
      else{
        $hasError=false;
      }
      // Same message regardless of whether the account exists, to avoid
      // leaking which emails/phone numbers are registered members.
      $messageText = "If that email or mobile number is registered, we've sent password reset instructions to the associated email. Please check your inbox or junk mail.";
    }
}

if(isset($_SESSION['messageText'])){
    $messageText= $_SESSION['messageText'];
    $hasError = $_SESSION['hasError'];
}

?>

<script src="js/jquery.validate.min.js"></script>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
              <div class="brand-logo">
                <img src="images/logo.png" alt="logo">
              </div>
              <h4>Forgot your password?</h4>
              <h6 class="font-weight-light">Don't worry this will not reset you password of toastmaster international website - https://toastmasters.org/. This is different entity than TI</h6>
              <form method="POST" action="" class="pt-3" name="password-reset-form" id="password-reset-form">
                  <?php if($messageText!=''){?>
              <div id="message_box" style="margin-bottom:10px" class="btn btn-<?php echo ($hasError?"danger":"success")?>"><?php echo $messageText?></div>
              <?php } ?>

              <div class="form-group">
                  <input type="email||number" class="form-control form-control-lg" id="email_mobile" name="email_mobile" placeholder="Enter your email / mobile registered"
                  required value="<?php echo isset($_POST['email_mobile'])?htmlspecialchars($_POST['email_mobile'], ENT_QUOTES, 'UTF-8'):'' ?>">
                </div>
                <div class="mt-3">
                  <input type="submit" class="btn btn-primary  btn-block" name="btnResetPwd" id="btnResetPwd" value="SEND RESET LINK" />
                </div>
                <div class="text-center mt-4 font-weight-light">
                  Already have an account? <a href="login.php" class="text-primary">Login</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
    <footer class="footer">
  <div class="d-sm-flex justify-content-center justify-content-sm-between">
    <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2022.  Premium <a href="https://www.bootstrapdash.com/" target="_blank">Bootstrap admin template</a> from BootstrapDash. All rights reserved.</span>
    <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Concept by Sandeep Dhawa and Developed by <a href="https://rajanmaharjan.com.np" target="_blank">Rajan Maharjan</a> on 2022<i class="ti-heart text-danger ml-1"></i></span>
  </div>
</footer>
</body>

</html>
