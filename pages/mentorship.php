<div class="row">
          <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Mentorship request to me</h4>
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Requested By</th>
                          <th>Requested On</th>
                          <th>Status</th>
                          <th>Options</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                      $requestList = $objectRequest->selectAll(1, "requested_to='".(int)$_SESSION['session_user_id']."'");
                      $counter=1;
                      foreach($requestList as $rowData){
                      ?>
                        <tr>
                          <td class="py-1">
                          <?php echo $counter++;?>.
                          <?php
                          $singleMember = $objectUser->getDetail($rowData->requested_by);
                          $profilePic=isset($singleMember->profile_picture)?$singleMember->profile_picture:'';
                          if(trim($profilePic)=='')
                              $profilePic='noimage'.$singleMember->gender.'.png';
                          ?>
                          <a href="<?php echo htmlspecialchars($singleMember->profile_link, ENT_QUOTES, 'UTF-8')?>" target="_blank">
                          <img src="images/members/<?php echo htmlspecialchars($profilePic, ENT_QUOTES, 'UTF-8')?>" alt="image"/> <br />
                          <?php echo htmlspecialchars($singleMember->full_name, ENT_QUOTES, 'UTF-8')?>
                      </a>
                        </td>
                          <td>
                          <?php echo htmlspecialchars($rowData->requested_on, ENT_QUOTES, 'UTF-8')?>
                          </td>
                          <td>
                          <?php
                          switch($rowData->status){
                            case 'P':echo 'Pending';break;
                            case 'A':echo 'Approved';break;
                            case 'C':echo 'Completed';break;
                            default:echo 'N/A';
                          }
                          ?>
                          </td>
                          <td>
                          <?php if ($rowData->status=='P'){?>
                            <a href="javascript:void(0)" id="<?php echo (int)$rowData->request_id?>" class="btn btn-success btn-rounded btn-fw approve-request">Approve</a>
                            <a href="javascript:void(0)" id="<?php echo (int)$rowData->request_id?>" class="btn btn-danger btn-rounded btn-fw cancel-request">Cancel</a>
                          <?php } ?>
                          </td>
                        </tr>
                        <?php } if($counter==1){ ?>
                        <tr>
                            <td colspan="4" style="color:red;text-align:center;font-weight:bold">You do not have any mentor request</td>
                        </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
        </div>