<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TblBinLocations $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-bin-locations-form">
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

    <div class="card shadow rounded mt-2">
      <div class="card-body p-3">
        <div class="row">
          <div class="col">
              <?= $form->field($model, 'area')->textInput(['maxlength' => true]) ?>

          </div>
          <div class="col">
            <?= $form->field($model, 'row')->textInput(['maxlength' => true]) ?>

          </div>
          <div class="col">
            <?= $form->field($model, 'bay')->textInput(['maxlength' => true]) ?>

          </div>
          <div class="col">
            <?= $form->field($model, 'level')->textInput(['maxlength' => true]) ?>

          </div>
          <div class="col">
            <?= $form->field($model, 'position')->textInput(['maxlength' => true]) ?>

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
