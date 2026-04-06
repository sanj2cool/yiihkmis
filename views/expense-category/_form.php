<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TblExpenseCategory $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-expense-category-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="card shadow rounded mt-2">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-lg-8 mb-2">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
          </div>
          <div class="col-lg-4 mb-2">
            <?= $form->field($model, 'status')->dropDownList([1=>"Active",2=>"Inactive"],['prompt'=>'Select','class'=>'form-select']) ?>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <?= $form->field($model, 'description')->textarea(['rows' => 4]) ?>
          </div>
        </div>
      </div><!-- card body ended -->
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
