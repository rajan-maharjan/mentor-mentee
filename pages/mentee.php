<div class="row">
  <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Your request for mentorship.</h4>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Requested To</th>
                  <th>Requested On</th>
                  <th>Status</th>
                  <th>Options</th>
                </tr>
              </thead>
              <tbody>
              <?php
              $requestList = $objectRequest->selectAll(1, "requested_by='".(int)$_SESSION['session_user_id']."'");
              $counter=1;
              foreach($requestList as $rowData){
              ?>
                <tr>
                  <td class="py-1">
                  <?php echo $counter++;?>.
                  <?php echo htmlspecialchars($objectUser->getName($rowData->requested_to), ENT_QUOTES, 'UTF-8');?>
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
                  <a href="javascript:void(0)" id="<?php echo (int)$rowData->request_id?>" class="btn btn-danger btn-rounded btn-fw cancel-request">Cancel Request</a>
                  <?php } if ($rowData->status=='A'){?>
                    <a href="<?php echo SITE_PATH?>appointment/<?php echo base64_encode((string)(int)$rowData->request_id)?>.html" class="btn btn-success btn-rounded btn-fw">Add Meeting Details</a>
                  <?php }?>
                  </td>
                </tr>
                <?php } if($counter==1){ ?>
                <tr>
                    <td colspan="4" style="color:red;text-align:center;font-weight:bold">You have not made any request for any Mentors</td>
                </tr>
                <?php } ?>

              </tbody>
            </table>
          </div>
        </div>
      </div>
  </div>
</div>