<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TblProductCatalog $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-product-catalog-form">

      <?php $form = ActiveForm::begin([
      'options' => ['enctype' => 'multipart/form-data']
    ]); ?>

    <div class="card shadow rounded mt-4">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-lg-4 mb-2">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
          </div>
          <div class="col-lg-4 mb-2">
            <?= $form->field($model, 'catalog_year')->textInput(['maxlength' => 4,'type'=>'number']) ?>
          </div>
          <div class="col-lg-4 mb-2">
            <?= $form->field($model, 'pdf_file')->fileInput(['class'=>'form-control','accept'=>'application/pdf'])->label('Upload Catalog') ?>
          </div>
          <div class="col-lg-12">
            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
          </div>
        </div>
      </div>
    </div>
    <div class="card shadow rounded p-3 mt-4 mb-4">
            <input type="submit" name="new_exit" value="Create & Exit" class="btn btn-secondary btn-sm"/>

    </div>

    <?php ActiveForm::end(); ?>

</div>
