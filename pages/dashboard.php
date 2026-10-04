
  <div class="row">
    <div class="col-md-12 grid-margin">
      <div class="row">
        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
          <h3 class="font-weight-bold">Welcome <?php echo htmlspecialchars($_SESSION['session_fullname'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
          <h6 class="font-weight-normal mb-0">This is your dashboard</h6>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6 grid-margin stretch-card">
      <div class="card tale-bg">
        <div class="card-people mt-auto">
          <img src="images/people.svg" alt="people">
          <div class="weather-info">
          <div class="time">
              <h1 class="animated fadeInLeft"><?php echo date("H:i")?></h1>
              <p class="animated fadeInRight"><?php echo date("D, j M Y");?></p>
            </div>
            <?php
            "https://api.open-meteo.com/v1/forecast?latitude=27.64&longitude=85.33&hourly=temperature_2m";
            ?>
            <div class="d-flex">
              <div>
                <h2 class="mb-0 font-weight-normal"><i class="icon-sun mr-2"></i>22<sup>C</sup></h2>
              </div>
              <div class="ml-2">
                <h4 class="location font-weight-normal">Kathmandu</h4>
                <h6 class="font-weight-normal">Nepal</h6>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 grid-margin transparent">
      <div class="row">
        <div class="col-md-6 mb-4 stretch-card transparent">
          <div class="card card-dark-blue">
            <div class="card-body">
              <p class="mb-4">Total Clubs</p>
              <p class="fs-30 mb-2"><?php
              echo count($objectClub->selectAll());
              ?></p>

            </div>
          </div>
        </div>
        <div class="col-md-6 mb-4 stretch-card transparent">
          <div class="card card-dark-brown">
            <div class="card-body">
              <p class="mb-4">Total Members</p>
              <p class="fs-30 mb-2">
              <?php
              echo count($objectUser->selectAll(1));
              ?>
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 mb-4 mb-lg-0 stretch-card transparent">
          <div class="card card-light-brown">
            <div class="card-body">
              <p class="mb-4">Available INDIVIDUAL Mentors</p>
              <p class="fs-30 mb-2"><?php echo count($objectUser->getIndividualMentors()); ?></p>
              </div>
          </div>
        </div>
        <div class="col-md-6 stretch-card transparent">
          <div class="card card-light-blue">
            <div class="card-body">
              <p class="mb-4">Available CLUB Mentors</p>
              <p class="fs-30 mb-2"><?php echo count($objectUser->getClubMentors()); ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Recently Joined Members</h4>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>
                    Name
                  </th>
                  <th>
                    Club
                  </th>
                  <th>
                    Email
                  </th>
                  <th>
                    Contact Number
                  </th>
                  <th>Option</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $usersList = $objectUser->getLatestMembers(1);
                $counter=0;
                foreach($usersList as $singleUser){
                  ?>
                <tr>
                  <td class="py-1">
                  <?php echo ++$counter;?>
                </td>
                  <td>
                  <?php echo $singleUser->full_name;?>
                  </td>
                  <td>
                  <?php
                  $clubFound=$objectClub->getMembersClubDetail($singleUser->member_id);
                  if(!empty($clubFound))
                    echo $clubFound->club_name;
                  else echo "N/A";?>
                  </td>
                  <td>
                      <?php if($singleUser->show_email=="Y"){?>
                      <a href="mailto:<?php echo $singleUser->email?>"><?php echo $singleUser->email?></a>
                      <?php } else { echo "Not displayed due to privacy";} ?>
                    </td>
                  <td>
                  <?php echo ($singleUser->show_mobile=="Y"?$singleUser->mobile_number:"Not disclosed by user");?>
                  </td>
                  <td><?php if( trim($singleUser->profile_link??'')!=''){ ?><a type="button" class="btn btn-primary btn-rounded btn-fw" href="<?php echo $singleUser->profile_link?>">Personal Info</a> <?php } ?></td>
                </tr>
                <?php } ?>

              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
