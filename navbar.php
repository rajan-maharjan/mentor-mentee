<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
<div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
  <a class="navbar-brand brand-logo mr-5" href="<?php echo SITE_PATH?>"><img src="<?php echo IMAGE_PATH?>logo.png" class="mr-2" alt="logo"/></a>
  <a class="navbar-brand brand-logo-mini" href="<?php echo SITE_PATH?>"><img src="<?php echo IMAGE_PATH?>logo-mini.png" alt="logo"/></a>
</div>
<div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
  <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
    <span class="icon-menu"></span>
  </button>
  <ul class="navbar-nav mr-lg-2">
    <li class="nav-item nav-search d-none d-lg-block">
      <div class="input-group">
        <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
          <span class="input-group-text" id="search">
            <i class="icon-search"></i>
          </span>
        </div>
        <form name="search-tm" method="POST" action="<?php echo SITE_PATH?>search.html">
        <input type="text" name="full_name" class="form-control" id="navbar-search-input" placeholder="Search TMs" aria-label="search" aria-describedby="search">
        <input type="hidden" name="btnSearch" value="1">  
      </form>
      </div>
    </li>
  </ul>
  <ul class="navbar-nav navbar-nav-right">      
    <li class="nav-item nav-profile dropdown">
      <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
      <?php      
        $singleMember = $objectUser->getDetail($_SESSION['session_user_id']); 
        $profilePic=isset($singleMember->profile_picture)?$singleMember->profile_picture:'';
        if(trim($profilePic??'')=='')
          $profilePic='noimage'.$singleMember->gender.'.png';
        ?>
      <img src="<?php echo IMAGE_PATH?>members/<?php echo $profilePic?>" alt="profile"/>
      </a>
      <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
        <a class="dropdown-item" href="<?php echo SITE_PATH?>profile.html">
          <i class="ti-settings text-primary"></i>
          My Profile
        </a>
        <a class="dropdown-item" href="<?php echo SITE_PATH?>change-password.html">
          <i class="ti-settings text-password"></i>
          Change Password
        </a>
        <a class="dropdown-item" href="<?php echo SITE_PATH?>logout.php">
          <i class="ti-power-off text-primary"></i>
          Logout
        </a>
      </div>
    </li>    
  </ul>
  <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
     <span class="icon-menu"></span>
  </button>
</div>
</nav>