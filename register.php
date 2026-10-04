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
  <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-3JSN5T407F"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-3JSN5T407F');
</script>
</head>

<body>

<?php
$messageText="";
$hasError=false;
if(isset($_POST['btnSignUp'])){
    $email=strtolower(trim($_POST['email']??''));
    $mobileNumber = trim($_POST['mobile_number']??'');
    $clubId=$_POST['club_id'];
    
    $hasError=true;
    
    if (!$objectFunctions->isValidEmail($email)) {
       $hasError=true;
       $messageText="Opps! Please enter valid email address (e.g: example@domain.com).";
    } elseif (!$objectFunctions->isValidMobile($mobileNumber)) {
       $hasError=true;
       $messageText="Opps! Please enter valid phone number (e.g: 9XXXXXXXXX).";
    } elseif($_POST['club_id']=='0'){
    $hasError=true;
    $messageText="Opps! Looks like you missed to choose your club.";
  }
  elseif(empty($objectUser->checkExists(0,$email,$mobileNumber))){
      $arrayData = array('email'=>$email,'club_id'=>$clubId,'mobile_number'=>$mobileNumber,'is_active'=>1);
      $objectUser->insertUpdate($arrayData);
      $subject="Thank you for registering with ".SITE_NAME;
      $content="Dear $email<br />
      Thank you for registering to ".SITE_NAME.". The site admin will soon verify you by call or email and once confirmed you will be registered to this site and you will be informed by email. OR you may also directly call/whatsapp to site admin Rajan Maharjan (9851122778) for confirming your registration.";
      $objectFunctions->sendEmail($email,$subject, $content);
      $hasError=false;
      $messageText = "Thank you for registering with ".SITE_NAME.". You should receive an email regarding successful registration. Please check your inbox or junk of your email address.";
  }
  else{
    $hasError=true;
    $messageText="Opps! Looks like either your mobile number or email address is already registered. You may try to <a href='".SITE_PATH."login.php'>login directly</a>.";
  }
    
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
              <h4>Already a Toastmaster? Want to get access to directory?</h4>
              <p class="text-warning">Should you have any issue login to this portal or registration. Please send screenshot of the issue/error to mail@rajanmaharjan.com.np or whatsapp to 9851122778</p>
              
              <h6 class="font-weight-light">Signing up is easy. It only takes a few seconds and email verification</h6>
              <form method="POST" action="" class="pt-3" name="registration-form" id="registration-form">
                   <?php if($messageText!=''){?>
              <div id="message_box" style="margin-bottom:10px" class="btn btn-<?php echo ($hasError?"danger":"success")?>"><?php echo $messageText?></div>
              <?php }?>
              <div class="form-group">
                  <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="Email registered in toastmaster.org" required value="<?php echo isset($_POST['email'])?$_POST['email']:'' ?>">
                </div>
                <div class="form-group">
                  <select class="form-control" id="club_id" name="club_id" required>
                    <option value='0'>Club Name</option>
                    <?php 
                    $clubList = $objectClub->selectAll();
                    foreach($clubList as $singleClub){
                      $curArea = $objectClub->getCurrentArea($singleClub->club_id);
                      echo '<option value="'.$singleClub->club_id.'">'.$singleClub->club_name.' ('.$curArea.')</option>';
                    }
                    ?>
                  </select>
                </div>
                <div class="form-group">
                  <input type="number" class="form-control form-control-lg" id="mobile_number" name="mobile_number" placeholder="Mobile Number" minlength="10" maxlength="10" required value="<?php echo isset($_POST['mobile_number'])?$_POST['mobile_number']:'' ?>">
                </div>
                <div class="mt-3">
                  <button type="submit" class="btn btn-primary  btn-block" name="btnSignUp" id="btnSignUp">SIGN UP</button>
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
