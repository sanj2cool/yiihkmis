<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\TblTerms;
/** @var yii\web\View $this */
/** @var app\models\TblVendor $model */
/** @var yii\widgets\ActiveForm $form */
$allterms = TblTerms::find()->where(['status'=>1])->orderBy(['priority'=>SORT_ASC])->all();
$termarr = ArrayHelper::map($allterms,'id','title');
?>

<div class="tbl-vendor-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="card shadow rounded mt-2">
        <div class="card-body p-3">
            <div class="row">
                <div class="col-lg-4">
                    <?= $form->field($model, 'company_name')->textInput(['maxlength' => true])->label('Company Name<span class="text-danger">*</span>') ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'contact_name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'contact_title')->textInput(['maxlength' => true]) ?>
                </div>

            </div>


        </div>
    </div>
    <div class="card shadow rounded mt-2">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-3">
                <?= $form->field($model, 'email')->textInput(['maxlength' => true])->label('Email<span class="text-danger">*</span>') ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>
            </div>
          </div>
          <div class="row mt-2">
              <div class="col-lg-4">
                  <?= $form->field($model, 'state')->textInput(['maxlength' => true]) ?>
              </div>
              <div class="col-lg-4">
                  <?= $form->field($model, 'postal_code')->textInput(['maxlength' => true]) ?>
              </div>
              <div class="col-lg-4">
                  <?= $form->field($model, 'country')->textInput(['maxlength' => true]) ?>
              </div>
          </div>
        </div>
      </div>
      <div class="card shadow rounded mt-2">
          <div class="card-body p-3">
            <div class="row">

                <div class="col-lg-12">
                    <?= $form->field($model, 'fk_terms_id')->dropDownList($termarr,['prompt'=>'Select']) ?>
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
