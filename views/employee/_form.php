<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\TblRoleMaster;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\TblEmployee $model */
/** @var yii\widgets\ActiveForm $form */
$rolearr = TblRoleMaster::find()->select(['title', 'id'])->where('status != 0')->indexBy('id')->column();

$locations = \app\models\TblOwnershipCompany::find()->where(['status'=>1])->all();
$locarr = ArrayHelper::map($locations,'id','location_name');

?>

<div class="tbl-employee-form">
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
  <?php $form = ActiveForm::begin([
    'options' => [
      'autocomplete' => 'off',
      'enctype' => 'multipart/form-data'
    ],
  ]); ?>
  <div class="card shadow rounded mt-2">
    <div class="card-body p-3">
      <div class="row">
        <div class="col-lg-4">
          <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label('Name<span class="text-danger">*</span>') ?>
        </div>
        <div class="col-lg-4">
          <?= $form->field($model, 'email')->textInput(['maxlength' => true])->label('Email<span class="text-danger">*</span>') ?>
        </div>
        <div class="col-lg-4">
          <?= $form->field($model, 'phone_no')->textInput(['maxlength' => true]) ?>
        </div>
      </div>

    </div>
  </div>

  <div class="card shadow rounded mt-2">
    <div class="card-body p-3">

      <div class="row">
      
        <div class="col-lg-4">
          <?= $form->field($model, 'fk_role_id')->dropDownList($rolearr,['prompt'=>'Select','class'=>'form-select'])->label('Role<span class="text-danger">*</span>') ?>
        </div>
        <div class="col-lg-4">
          <?= $form->field($model, 'status')->dropDownList([1=>"Active",2=>"Inactive"],['prompt' => 'Select','class'=>'form-select']) ?>
        </div>
      </div>

    </div>
  </div>

  <div class="card shadow rounded mt-2">
    <div class="card-body p-3">
      <div class="row">
        <div class="col-lg-6">
          <label for="">Driver's License</label>
          <input type="file" name="file_url" class="form-control" accept=".pdf,image/*">
          <?php
            if(!$model->isNewRecord && $model->driver_license != ""){
                echo '<a href="emp-doc/'.$model->driver_license.'" target="_blank">View Driver\'s License</a>';
            }
           ?>
        </div>
        <div class="col-lg-6">
          <label for="">Health Card</label>
          <input type="file" name="file_url_health" class="form-control" accept=".pdf,image/*">
          <?php
            if(!$model->isNewRecord && $model->health_card != ""){
                echo '<a href="emp-doc/'.$model->health_card.'" target="_blank">View Health Card</a>';
            }
           ?>
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
