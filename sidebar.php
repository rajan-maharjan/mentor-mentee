<nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item">
            <a class="nav-link" href="<?php echo SITE_PATH?>index.php">
              <i class="icon-grid menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo SITE_PATH?>agenda-list.html">
              <i class="icon-search menu-icon"></i>
              <span class="menu-title">My Club's Agenda Sheets</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
              <i class="icon-layout menu-icon"></i>
              <span class="menu-title">Division Wise Clubs</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-basic">
              <ul class="nav flex-column sub-menu">
                  <?php $divisionList = $objectClub->getDivisionList();
                  foreach($divisionList as $singleDiv){
                  ?>
                <li class="nav-item"> <a class="nav-link" href="<?php echo SITE_PATH?>clubs/<?php echo $singleDiv->division?>.html"><?php echo $singleDiv->division?> Division</a></li>
                <?php } ?>
              </ul>
            </div>
          </li>         
          <li class="nav-item">
            <a class="nav-link" href="<?php echo SITE_PATH?>clubs.html">
              <i class="icon-paper menu-icon"></i>
              <span class="menu-title">Clubs</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo SITE_PATH?>mentee.html">
              <i class="icon-paper menu-icon"></i>
              <span class="menu-title">My requests</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo SITE_PATH?>mentorship.html">
              <i class="icon-paper menu-icon"></i>
              <span class="menu-title">Request to me</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo SITE_PATH?>search.html">
              <i class="icon-search menu-icon"></i>
              <span class="menu-title">Search Members</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="http://toastmastersnepal.org" target="_blank">
              <i class="icon-grid menu-icon"></i>
              <span class="menu-title">Toastmasters Nepal Website</span>
            </a>
          </li>
        </ul>
      </nav>