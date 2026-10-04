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
                              <?php echo $singleClub->club_name?>
                            </td>   
                            <td>
                              <?php echo $singleClub->current_area?>
                            </td>
                            <td>
                              <?php echo $singleClub->meeting_venue?>
                            </td>
                          <td>
                              <?php                              
                              $presidentDetail = $objectClub->getPresident($singleClub->club_id);
                              if(! empty($presidentDetail)){
                               ?>
                              <a href="<?php echo $presidentDetail->profile_link?>" target="_blank">
                              <?php echo $presidentDetail->full_name?>
                            </a>
                            <?php  } else echo "n/a";?>
                          </td>
                          <td><?php 
                                    if(isset($singleClub->club_whatspp_phone) && trim($singleClub->club_whatspp_phone)!='')
                                        echo $singleClub->club_whatspp_phone;
                                    else if(!empty($presidentDetail) && (isset($presidentDetail->email) || isset($presidentDetail->mobile_number)) ){
                                      echo $presidentDetail->email;
                                      echo ($presidentDetail->show_mobile=='Y')?(' / '.$presidentDetail->mobile_number):'';
                                    }
                                    else "n/a";
                                    ?></td>
                          <td>
                              <a href="https://dashboards.toastmasters.org/ClubReport.aspx?id=<?php echo $singleClub->club_id?>" class="btn btn-info btn-rounded btn-fw" target="_blank">TI Dashboard</a> | 
                              <a href="https://toastmasters.org/Find-a-Club/<?php echo $singleClub->club_id?>" class="btn btn-info btn-rounded btn-fw" target="_blank">TI Website</a></td>
                        </tr>
                        <?php } ?>
                        
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
        </div>