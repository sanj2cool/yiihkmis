<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\TblTaskStatus;
use app\models\TblUser;
/** @var yii\web\View $this */
/** @var app\models\TblTask $model */
/** @var yii\widgets\ActiveForm $form */
$allstatus = TblTaskStatus::find()->where('status = 1')->orderBy(['status_name'=>SORT_ASC])->all();
$statusarr = ArrayHelper::map($allstatus,'id','status_name');

$session = Yii::$app -> session;
$fk_location_id = $session['userCompany'];

$allemps = TblUser::find()
          ->where('status != 0')
          ->andWhere('id in (select fk_user_id from tbl_user_location where status = 1 and fk_location_id = '.$fk_location_id.')')
          ->orderBy(['username'=>SORT_ASC])->all();
$emparr = ArrayHelper::map($allemps,'id','username');

if($model->isNewRecord){
  $model->due_date = date('Y-m-d');
}
?>

<div class="tbl-task-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="card shadow rounded mt-4">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-lg-4">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true])->label('Title<span class="text-danger">*</span>') ?>
          </div>
          <div class="col-lg-8">
            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
          </div>
        </div>
      </div>
    </div>
    <div class="card shadow rounded mt-4">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-lg-3">
                <?= $form->field($model, 'assigned_to')->dropDownList($emparr,['prompt'=>'Select','class'=>'form-select'])->label('Assigned To<span class="text-danger">*</span>') ?>
          </div>
          <div class="col-lg-3">
            <?= $form->field($model, 'due_date')->textInput() ?>
          </div>
          <div class="col-lg-3">
            <?= $form->field($model, 'time')->textInput() ?>
          </div>
          <div class="col-lg-3">
            <?= $form->field($model, 'status_id')->dropDownList($statusarr,['prompt'=>'Select','class'=>'form-select'])->label('Status<span class="text-danger">*</span>') ?>
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
            <input type="submit" name="new_update" value="Create & Edit" class="btn btn-primary btn-sm"/>
          </div>
          <div class="d-grid gap-2 col-4 mx-auto pe-1 ps-1">
            <input type="submit" name="new_new" value="Create & New" class="btn btn-info btn-sm"/>
          </div>
          <div class="d-grid gap-2 col-4 mx-auto ps-1">
            <input type="submit" name="new_exit" value="Create & Exit" class="btn btn-secondary btn-sm"/>
          </div>
          <?php
        }else{
          ?>
          <div class="d-grid gap-2 col-4 mx-auto pe-1">
            <input type="submit" name="update" value="Update & Edit" class="btn btn-primary btn-sm"/>
          </div>
          <div class="d-grid gap-2 col-4 mx-auto pe-1 ps-1">
            <input type="submit" name="new" value="Update & New" class="btn btn-info btn-sm"/>
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
var att_date_picker = $("#tbltask-due_date").flatpickr({
  disableMobile: "true",
  dateFormat: "Y-m-d"
});
flatpickr("#tbltask-time", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "h:i K",  // Format for 12-hour time with AM/PM
    time_24hr: false      // Disables 24-hour format
});
');
?>
