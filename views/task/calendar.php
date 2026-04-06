<?php
  use yii\helpers\Url;
  use yii\helpers\Html;
  use app\models\TblTaskStatus;
  use app\models\TblUser;
  $this->title = "Calendar";
  $event_str = "";
 ?>
 <div id='calendar'></div>
 <!-- Modal -->
<div class="modal fade" id="calendarModal" tabindex="-1" aria-labelledby="calendarModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="calendarModalLabel">Add Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="post" action="<?=Url::to(['task/create-task-calendar'])?>">
        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
      <div class="modal-body">
        <div class="container-fluid">



          <div class="row">
            <div class="col-lg-4">
              <label for="">Title<span class="text-danger">*</span></label>
              <input type="text" name="TblTask[title]" class="form-control" id="tbltask-title" required>
            </div>
            <div class="col-lg-8">
              <label for="">Description</label>
              <textarea name="TblTask[description]" rows="4" id="tbltask-description" class="form-control"></textarea>
            </div>
          </div>
          <div class="row mt-2">
            <div class="col-lg-6">
              <label for="">Due Date</label>
              <input type="text" name="TblTask[due_date]" class="form-control" id="tbltask-due_date">
            </div>
            <div class="col-lg-6">
              <label for="">Time</label>
              <input type="text" name="TblTask[time]" class="form-control" id="tbltask-time">
            </div>
          </div>
          <div class="row mt-2">
            <div class="col-lg-6">
              <label for="">Assigned To<span class="text-danger">*</span></label>
              <select class="form-control" name="TblTask[assigned_to]" id="tbltask-assigned_to" required>
                <option value="">Select</option>
                <?php
                  $allemps = TblUser::find()->where('status != 0')->orderBy(['username'=>SORT_ASC])->all();
                  if(isset($allemps) && count($allemps) > 0){
                    foreach($allemps as $ae){
                      echo '<option value="'.$ae->id.'">'.$ae->username.'</option>';
                    }
                  }
                 ?>
              </select>
            </div>
            <div class="col-lg-6">
              <label for="">Status<span class="text-danger">*</span></label>
              <select class="form-control" name="TblTask[status_id]" id="tbltask-status_id" required>
                <option value="">Select</option>
                <?php
                  $allstatus = TblTaskStatus::find()->where('status = 1')->orderBy(['status_name'=>SORT_ASC])->all();
                  if(isset($allstatus) && count($allstatus) > 0){
                    foreach($allstatus as $as){
                      echo '<option value="'.$as->id.'">'.$as->status_name.'</option>';
                    }
                  }
                 ?>
              </select>
            </div>
          </div>
          </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>
      </form>
    </div>
  </div>
</div>
<!-- UPDATE MODAL STARTS -->
<div class="modal fade" id="calendarModalUpdate" tabindex="-1" aria-labelledby="calendarModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="calendarModalLabel">Update Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="post" action="<?=Url::to(['task/update-task-calendar'])?>">
        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-4">
              <label for="">Title<span class="text-danger">*</span></label>
              <input type="hidden" name="task_id" id="task_id">
              <input type="text" name="task_title" class="form-control" id="task_title" required>
            </div>
            <div class="col-lg-8">
              <label for="">Description</label>
              <textarea name="task_description" rows="4" id="task_description" class="form-control"></textarea>
            </div>
          </div>
          <div class="row mt-2">
            <div class="col-lg-6">
              <label for="">Due Date</label>
              <input type="text" name="task_due_date" class="form-control" id="task_due_date">
            </div>
            <div class="col-lg-6">
              <label for="">Time</label>
              <input type="text" name="task_time" class="form-control" id="task_time">
            </div>
          </div>
          <div class="row mt-2">
            <div class="col-lg-6">
              <label for="">Assigned To<span class="text-danger">*</span></label>
              <select class="form-control" name="task_assigned_to" id="task_assigned_to" required>
                <option value="">Select</option>
                <?php
                  $allemps = TblUser::find()->where('status != 0')->orderBy(['username'=>SORT_ASC])->all();
                  if(isset($allemps) && count($allemps) > 0){
                    foreach($allemps as $ae){
                      echo '<option value="'.$ae->id.'">'.$ae->username.'</option>';
                    }
                  }
                 ?>
              </select>
            </div>
            <div class="col-lg-6">
              <label for="">Status<span class="text-danger">*</span></label>
              <select class="form-control" name="task_status_id" id="task_status_id" required>
                <option value="">Select</option>
                <?php
                  $allstatus = TblTaskStatus::find()->where('status = 1')->orderBy(['status_name'=>SORT_ASC])->all();
                  if(isset($allstatus) && count($allstatus) > 0){
                    foreach($allstatus as $as){
                      echo '<option value="'.$as->id.'">'.$as->status_name.'</option>';
                    }
                  }
                 ?>
              </select>
            </div>
          </div>
          </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>
      </form>
    </div>
  </div>
</div>
<!-- UPDATE MODAL ENDS -->

<style>
/* General styles for all views */
.fc-event.bg-secondary {
    background-color: #6c757d !important;
    color: #fff !important;
}

.fc-event.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

.fc-event.bg-primary {
    background-color: #007bff !important;
    color: #fff !important;
}

/* Week and Day view specific styles */
.fc-timeGridWeek .fc-event.bg-secondary,
.fc-timeGridDay .fc-event.bg-secondary {
    background-color: #6c757d !important;
    color: #fff !important;
}

.fc-timeGridWeek .fc-event.bg-warning,
.fc-timeGridDay .fc-event.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

.fc-timeGridWeek .fc-event.bg-primary,
.fc-timeGridDay .fc-event.bg-primary {
    background-color: #007bff !important;
    color: #fff !important;
}

/* Ensure event text color is visible */
.fc-event {
    color: #fff !important; /* Default text color */
}

</style>
 <?php

 // Register the FullCalendar and jQuery scripts
 $this->registerJsFile('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js');
 $this->registerJsFile('@web/js/calendar.js', ['depends' => [\yii\web\JqueryAsset::class]]);

 ?>
 <?php
 $this->registerJs('
 var att_date_picker = $("#tbltask-due_date,#task_due_date").flatpickr({
   disableMobile: "true",
   dateFormat: "Y-m-d"
 });
 flatpickr("#tbltask-time,#task_time", {
     enableTime: true,
     noCalendar: true,
     dateFormat: "h:i K",  // Format for 12-hour time with AM/PM
     time_24hr: false      // Disables 24-hour format
 });
 ');
 ?>
