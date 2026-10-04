<?php
// Both the id in the URL and the posted id are decoded as integers only -
// this fixes a SQL injection where arbitrary base64-decoded bytes were
// previously concatenated straight into a query.
$requestId = (int) base64_decode($_GET['url2'] ?? '');

// Authorization check (fixes IDOR): a user may only view/add appointment
// details for a mentorship request they are actually a party to.
$currentRequest = $requestId > 0 ? $objectRequest->getDetail($requestId) : array();
$isAuthorizedForRequest = !empty($currentRequest)
    && in_array((int)$_SESSION['session_user_id'], array((int)$currentRequest->requested_by, (int)$currentRequest->requested_to), true);

if(isset($_POST['btnAddAppointmentDetail'])){
    if(!$objectFunctions->validateCsrfToken($_POST['csrf_token'] ?? '')){
        echo "<div style='color:red'>Your session has expired. Please reload the page and try again.</div>";
    }
    elseif(!$isAuthorizedForRequest){
        echo "<div style='color:red'>You are not authorized to add details to this request.</div>";
    }
    else{
        $postedRequestId = (int) base64_decode($_POST['request_id'] ?? '');
        $arrayField['appointment_date'] = $_POST['meeting_date'];
        $arrayField['agenda_discussed'] = $_POST['meeting_detail'];
        $arrayField['meeting_type'] = $_POST['meeting_type'];
        $arrayField['is_active'] = '1';
        $arrayField['request_id'] = $postedRequestId;
        $objectAppointment->insertUpdate($arrayField);
        echo "<script>window.location='".SITE_PATH."appointment/".htmlspecialchars($_POST['request_id'], ENT_QUOTES, 'UTF-8').".html';</script>";
    }
}
?>
<div class="col-12 grid-margin">
    <div class="card">
    <div class="card-body">
        <h4 class="card-title">Add Appointment Information</h4>
        <form class="form-sample" method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo $objectFunctions->getCsrfToken()?>" />
            <p class="card-description">Record your Meeting details</p>
            <div class="row">
                <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Date</label>
                    <div class="col-sm-9">
                    <input type="text" class="form-control" name="meeting_date" id="meeting_date" />
                    <input type="hidden" value="<?php echo htmlspecialchars($_GET['url2'] ?? '', ENT_QUOTES, 'UTF-8')?>" name="request_id" />
                    </div>
                </div>
                </div>
                <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Meeting Type</label>
                    <div class="col-sm-9">
                    <select class="form-control" name="meeting_type">
                        <option value="O">Online</option>
                        <option value="P">Physical</option>
                    </select>
                    </div>
                </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Discussion Agenda or Description</label>
                    <div class="col-sm-8">
                    <textarea name="meeting_detail" class="form-control" rows=10></textarea>
                    </div>
                </div>
                </div>
            </div>
            <button type="submit" value ="1" class="btn btn-primary mb-2" name="btnAddAppointmentDetail" id="btnAddAppointmentDetail">Add detail</button>
        </form>
    </div>
    </div>
</div>

  <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Appointment History</h4>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Meeting Date</th>
                  <th>Meeting Type</th>
                  <th>Agenda Discussed</th>
                  <th>Options</th>
                </tr>
              </thead>
              <tbody>
              <?php
              $appointmentList = $isAuthorizedForRequest ? $objectAppointment->selectAll(1, "request_id='".$requestId."'") : array();
              $counter=1;
              foreach($appointmentList as $rowData){
              ?>
                <tr>
                  <td class="py-1">
                  <?php echo $counter++;?>.
                  <?php echo htmlspecialchars($rowData->appointment_date, ENT_QUOTES, 'UTF-8');?>
                </td>
                  <td>
                  <?php echo ($rowData->meeting_type=='O'?'ONLINE':'PHYSICAL')?>
                  </td>
                  <td><?php  echo htmlspecialchars($rowData->agenda_discussed, ENT_QUOTES, 'UTF-8'); ?>
                  </td>
                  <td>
                  <a href="javascript:void(0)" id="<?php echo (int)$rowData->id?>" class="btn btn-danger btn-rounded btn-fw delete-appointment">Delete this</a>
                  </td>
                </tr>
                <?php } if($counter==1){ ?>
                <tr>
                    <td colspan="4" style="color:red;text-align:center;font-weight:bold">Opps!!!, you have not made any appointment or meeting with your mentor. Please initiate a meeting.</td>
                </tr>
                <?php } ?>

              </tbody>
            </table>
          </div>
        </div>
      </div>
  </div>