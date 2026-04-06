<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TblBrand $model */
/** @var yii\widgets\ActiveForm $form */
$statusarr = [1=>"Active",2=>"Inactive"];
?>

<div class="tbl-brand-form">
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
        <div class="col-lg-4 mb-2">
          <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-4 mb-2">
          <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-4">
          <label for="">Image</label>
          <input type="file" name="cat_img" accept="image/*" class="form-control">
          <p class="fs-sm">
            The preferred image size is 200 X 200
          </p>
          <?php
            if(!$model->isNewRecord && $model->image_url != ""){
              echo '  <a href="brand-images/'.$model->image_url.'" target="_blank">
                <img src="brand-images/'.$model->image_url.'" width="100px"/>
                </a>';
            }
           ?>
        </div>
      
        <div class="col-lg-4">
          <?= $form->field($model, 'show_on_website')->dropDownList([1=>"Yes",2=>"No"],['prompt'=>'Select', 'encode' => false]) ?>
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
