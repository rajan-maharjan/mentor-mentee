<div class="row">
          <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Toastmaster Clubs in Nepal</h4>
                  <div><a href='<?php echo SITE_PATH?>clubs.html' target="_blank">View Detail of ALL Clubs</a></div>
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th style="width:10px">S.No.</th>
                          <th>Name</th>
                          <th>Area</th>
                          <th>Meeting Venue</th>
                          <th>President</th>
                          <th>Contact</th>
                          <th style="width:310px">TI Details Link</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                      $whereCond = '';
                      if(isset($_GET['url2']) && trim($_GET['url2'])!=''){
                        $divD=trim($_GET['url2']);
                      }
                      else $divD="";

                      $clubList = $objectClub->selectAll($divD);
                      $counter=1;
                      foreach($clubList as $singleClub){
                      ?>
                        <tr>
                            <td><?php echo $counter++;?>.</td>
                            <td>
                              <?php echo htmlspecialchars($singleClub->club_name, ENT_QUOTES, 'UTF-8')?>
                            </td>
                            <td>
                              <?php echo htmlspecialchars($singleClub->current_area, ENT_QUOTES, 'UTF-8')?>
                            </td>
                            <td>
                              <?php echo htmlspecialchars($singleClub->meeting_venue, ENT_QUOTES, 'UTF-8')?>
                            </td>
                          <td>
                              <?php
                              $presidentDetail = $objectClub->getPresident($singleClub->club_id);
                              if(! empty($presidentDetail)){
                               ?>
                              <a href="<?php echo htmlspecialchars($presidentDetail->profile_link, ENT_QUOTES, 'UTF-8')?>" target="_blank">
                              <?php echo htmlspecialchars($presidentDetail->full_name, ENT_QUOTES, 'UTF-8')?>
                            </a>
                            <?php  } else echo "n/a";?>
                          </td>
                          <td><?php
                                    if(isset($singleClub->club_whatspp_phone) && trim($singleClub->club_whatspp_phone)!='')
                                        echo htmlspecialchars($singleClub->club_whatspp_phone, ENT_QUOTES, 'UTF-8');
                                    else if(!empty($presidentDetail) && (isset($presidentDetail->email) || isset($presidentDetail->mobile_number)) ){
                                      echo htmlspecialchars($presidentDetail->email, ENT_QUOTES, 'UTF-8');
                                      echo ($presidentDetail->show_mobile=='Y')?(' / '.htmlspecialchars($presidentDetail->mobile_number, ENT_QUOTES, 'UTF-8')):'';
                                    }
                                    else "n/a";
                                    ?></td>
                          <td>
                              <a href="https://dashboards.toastmasters.org/ClubReport.aspx?id=<?php echo (int)$singleClub->club_id?>" class="btn btn-info btn-rounded btn-fw" target="_blank">TI Dashboard</a> |
                              <a href="https://toastmasters.org/Find-a-Club/<?php echo (int)$singleClub->club_id?>" class="btn btn-info btn-rounded btn-fw" target="_blank">TI Website</a></td>
                        </tr>
                        <?php } ?>

                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
        </div>