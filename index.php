<!DOCTYPE html>
<html lang="en">
<?php 
$relativePath  = "";
include "files.inc.php";
if(!isset($_SESSION['session_user_id']) or !isset($_SESSION['session_email']) or trim($_SESSION['session_user_id']??'')=='' or trim($_SESSION['session_email'])==''){
  echo "<script language='javascript'>window.location='login.php';</script>"; exit;
}
?>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3806824823543446"
     crossorigin="anonymous"></script>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-3JSN5T407F"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-3JSN5T407F');
</script>
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Toastmaster Portal :: Dashboard</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="<?php echo SITE_PATH?>vendors/feather/feather.css">
  
  <link rel="stylesheet" href="<?php echo SITE_PATH?>vendors/ti-icons/css/themify-icons.css">
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="<?php echo CSS_PATH?>style.css">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="<?php echo IMAGE_PATH?>favicon.png" />
  <script language="javascript">
    var _sitePath="<?php echo SITE_PATH?>";
  </script>
</head>
<body>
  <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
   <?php include "navbar.php"; ?>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_sidebar.html -->
      <?php include "sidebar.php"; ?>
      <!-- partial -->
      <div class="main-panel">
      <div class="content-wrapper">
        <?php
        if(isset($_GET['url1']) && trim($_GET['url1']??'')!=''){
          include "pages/".trim($_GET['url1']??'').".php";
        }
        else
          include "pages/dashboard.php";?>
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
        <?php include "footer.php"; ?>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <!-- plugins:js -->
  <script src="<?php echo JS_PATH?>vendor.bundle.base.js"></script>
  <script src="<?php echo JS_PATH?>template.js"></script>
  <script src="<?php echo JS_PATH?>off-canvas.js"></script>
  <script src="<?php echo JS_PATH?>jquery-ui.js"></script>    
  <script src="<?php echo JS_PATH?>jquery.bs.calendar.js"></script>
  <script src="<?php echo JS_PATH?>onload.js"></script>
  </body>

</html>

