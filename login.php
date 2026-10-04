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
  <title><?php echo SITE_NAME?> Admin</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="./css/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="./images/favicon.png" />
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
if(isset($_POST['btnLogin'])){
  $email=strtolower(trim($_POST['email']??''));
  $password = trim($_POST['password']??'');
  $hasError=true;
  
  if($objectFunctions->isValidateContact($email)==false){
        $hasError=true;
        $messageText="Opps! Please enter valid mobile number (e.g: 9XXXXXXXX0) or valid email address (e.g: example@domain.com).";
    }
    else{
        $objectFunctions->sql= "Insert into tin_member_login_logs (member_email) values ('$email')";
        $objectFunctions->execute();
    	
     
      $userData = $objectUser->authenticate($email,$password);
     
      if(false==empty($userData)){ 
          $_SESSION['session_fullname']=$userData->full_name;
          $_SESSION['session_email']=$email;
          $_SESSION['session_user_id']=$userData->id;
          $_SESSION['session_member_id']=$userData->member_id;
         
          if($userData->pwd_reset_request=="Y"){
            echo "<script language='javascript'>window.location='".SITE_PATH."change-password.html';</script>";
            die("AFTER");
          }
          else if(trim($userData->member_id)=="" || trim($userData->show_mobile)==""){
            echo "<script language='javascript'>alert('Please update your TI membership number first'); window.location='".SITE_PATH."profile.html';</script>";
            die("AFTER");
          }
          else{
              echo "<script language='javascript'>window.location='".SITE_PATH."';</script>";
              die();
          }
      }
      else{
        $hasError=true;
        $messageText="Opps! Looks like your email/mobile and password combination is incorrect. If you cannot remember your password, you can <a href='".SITE_PATH."forgot-password.php'>reset it</a> and get the new one in your email";
      }
    } 
}

if(isset($_SESSION['message'])){
    $messageText= $_SESSION['message'];
    $hasError = $_SESSION['hasError'];
    session_destroy();
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
                <img src="./images/logo.png" alt="logo">
                <div style="float:right;widht:200px;"><a href="https://toastmastersnepal.org/"><button class="btn btn-block btn-primary" name="btnBack" id="btnBack">TIN Website</button></a></div>
              </div>
              <h4>A directory of Toastmasters Members</h4>
              <p class="text-warning">This is completely different portal and its login is different from Toastmasters International. Do not confuse with username/password that is used for Toastmastsers International website - https://www.toastmasters.org/</p>
              <form class="pt-3" method="POST" action="" name="login-form" id="login-form">
                  <?php if($messageText!=''){?>
                            <div id="message_box" class="text-<?php echo ($hasError?"danger":"success")?>"><?php echo $messageText?></div>
              <?php } ?>
                <div class="form-group">
                  <input type="text" class="form-control form-control-lg" id="email" name="email" placeholder="Email / Mobile Number" required>
                </div>
                <div class="form-group">
                  <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Password" minlength="5" required>
                </div>
                <div class="mt-3">
                  <button class="btn btn-block btn-primary" name="btnLogin" id="btnLogin">SIGN IN</button>
                </div>
                <div class="my-2 d-flex justify-content-between align-items-center">
                  <div class="form-check">
                    <label class="form-check-label text-muted">
                      <input type="checkbox" class="form-check-input">
                      Keep me signed in
                    </label>
                  </div>
                  <a href="<?php echo SITE_PATH?>forgot-password.php" class="auth-link text-black">Forgot password?</a>
                </div>
                
                <div class="text-center mt-4 font-weight-light">
                  Don't have an account? <a href="register.php" class="text-primary">Request One</a>
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
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="./vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="./js/off-canvas.js"></script>
  <script src="./js/hoverable-collapse.js"></script>
  <script src="./js/template.js"></script>
  <script src="./js/settings.js"></script>
  <script src="./js/todolist.js"></script>
  <!-- endinject -->
  <footer class="footer">
  <div class="d-sm-flex justify-content-center justify-content-sm-between">
    <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2022.  Premium <a href="https://www.bootstrapdash.com/" target="_blank">Bootstrap admin template</a> from BootstrapDash. All rights reserved.</span>
    <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Concept by Sandeep Dhawa and Developed by <a href="https://rajanmaharjan.com.np" target="_blank">Rajan Maharjan</a> on 2022<i class="ti-heart text-danger ml-1"></i></span>
  </div>
</footer>
</body>

</html>
