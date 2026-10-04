<?php
$requestId = base64_decode($_GET['url2']);

if(isset($_POST['btnAddAppointmentDetail'])){
    $arrayField['appointment_date'] = $_POST['meeting_date'];
    $arrayField['agenda_discussed'] = $_POST['meeting_detail'];
    $arrayField['meeting_type'] = $_POST['meeting_type'];
    $arrayField['is_active'] = '1';
    $arrayField['request_id'] = base64_decode($_POST['request_id']);
    $objectAppointment->insertUpdate($arrayField);
    echo "<script>window.location='".SITE_PATH."appointment/".$_POST['request_id'].".html';</script>";
}
?>
<div class="col-12 grid-margin">
    <div class="card">
    <div class="card-body">
        <h4 class="card-title">Add Appointment Information</h4>
        <form class="form-sample" method="POST" action="">
            <p class="card-description">Record your Meeting details</p>
            <div class="row">
                <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Date</label>
                    <div class="col-sm-9">
                    <input type="text" class="form-control" name="meeting_date" id="meeting_date" />
                    <input type="hidden" value="<?php echo $_GET['url2']?>" name="request_id" />
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
              $appointmentList = $objectAppointment->selectAll(1, "request_id='".$requestId."'");
              $counter=1;
              foreach($appointmentList as $rowData){
              ?>
                <tr>   
                  <td class="py-1">
                  <?php echo $counter++;?>.                           
                  <?php echo $rowData->appointment_date;?>                        
                </td>                        
                  <td>
                  <?php echo ($rowData->meeting_type=='O'?'ONLINE':'PHYSICAL')?>
                  </td>
                  <td><?php  echo $rowData->agenda_discussed; ?>
                  </td>
                  <td>            
                  <a href="javascript:void(0)" id="<?php echo $rowData->id?>" class="btn btn-danger btn-rounded btn-fw delete-appointment">Delete this</a>
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