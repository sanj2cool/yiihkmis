<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\TblUser;
/** @var yii\web\View $this */
/** @var app\models\TblAttendance $model */
/** @var yii\widgets\ActiveForm $form */
//---get the user as per the current location session only -----
$session = Yii::$app->session;
$user_company = $session['userCompany'];

$allemps = TblUser::find()
          ->where('status != 0')
          ->andWhere('id in (select fk_user_id from tbl_user_location where status = 1 and fk_location_id = '.$user_company.')')
          ->orderBy(['username'=>SORT_ASC])->all();
$emparr = ArrayHelper::map($allemps,'id','username');
if($model->isNewRecord){
  $model->date = date('Y-m-d');
}
?>

<div class="tbl-attendance-form">
  <div id="msg">

    <?php
    if(Yii::$app -> session -> getFlash('success')!=null){
      ?>

      <div class="alert alert-outline-success d-flex align-items-center" role="alert">
        <span class="fas fa-check-circle text-success fs-5 me-3"></span>
        <p class="mb-0 flex-1"><?= Yii::$app -> session -> getFlash('success'); ?></p>

        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php
    }else if(Yii::$app -> session -> getFlash('error')!=null){
      ?>
      <div class="alert alert-outline-danger d-flex align-items-center" role="alert">
        <span class="fas fa-times-circle text-danger fs-5 me-3"></span>
        <p class="mb-0 flex-1"><?= Yii::$app -> session -> getFlash('error'); ?></p>
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>

      <?php
    }
    ?>
  </div>
    <?php $form = ActiveForm::begin(); ?>

    <div class="card shadow rounded mt-4">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-lg-4">
            <?= $form->field($model, 'fk_user_id')->dropDownList($emparr,['prompt'=>'Select','class'=>'form-select'])->label('User<span class="text-danger">*</span>')  ?>
          </div>
        </div>
      </div>
    </div>
    <div class="card shadow rounded mt-4">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-lg-4">
            <?= $form->field($model, 'date')->textInput()->label('Date<span class="text-danger">*</span>')  ?>
          </div>
          <div class="col-lg-4">
            <?= $form->field($model, 'in_time')->textInput(['maxlength' => true])->label('Check-in Time<span class="text-danger">*</span>')  ?>
          </div>
          <div class="col-lg-4">
            <?= $form->field($model, 'out_time')->textInput(['maxlength' => true]) ?>
          </div>
        </div>
      </div>
    </div>




    <div class="card shadow rounded p-3 mt-4 mb-4">
      <div class="row text-center">
        <?php
        if($model->isNewRecord){
          ?>
          <div class="d-grid gap-2 col-4 mx-auto pe-1">
            <input type="submit" name="new_update" value="Create & Edit" class="btn btn-info btn-sm"/>
          </div>
          <div class="d-grid gap-2 col-4 mx-auto pe-1 ps-1">
            <input type="submit" name="new_new" value="Create & New" class="btn btn-primary btn-sm"/>
          </div>
          <div class="d-grid gap-2 col-4 mx-auto ps-1">
            <input type="submit" name="new_exit" value="Create & Exit" class="btn btn-secondary btn-sm"/>
          </div>
          <?php
        }else{
          ?>
          <div class="d-grid gap-2 col-4 mx-auto pe-1">
            <input type="submit" name="update" value="Update & Edit" class="btn btn-info btn-sm"/>
          </div>
          <div class="d-grid gap-2 col-4 mx-auto pe-1 ps-1">
            <input type="submit" name="new" value="Update & New" class="btn btn-primary btn-sm"/>
          </div>
          <div class="d-grid gap-2 col-4 mx-auto ps-1">
            <input type="submit" name="exit" value="Update & Exit" class="btn btn-secondary btn-sm"/>
          </div>
          <?php
        }
        ?>

      </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs('
var att_date_picker = $("#tblattendance-date").flatpickr({
  disableMobile: "true",
  dateFormat: "Y-m-d"
});
flatpickr("#tblattendance-in_time", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "h:i K",  // Format for 12-hour time with AM/PM
    time_24hr: false      // Disables 24-hour format
});
flatpickr("#tblattendance-out_time", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "h:i K",  // Format for 12-hour time with AM/PM
    time_24hr: false      // Disables 24-hour format
});
');
?>
