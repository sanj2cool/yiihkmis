<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TblProductAllotmentSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-product-allotment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'fk_product_id') ?>

    <?= $form->field($model, 'fk_vendor_invoice_id') ?>

    <?= $form->field($model, 'no_of_items') ?>

    <?= $form->field($model, 'allotment_date') ?>

    <?php // echo $form->field($model, 'allotted_by') ?>

    <?php // echo $form->field($model, 'fk_allotment_status_id') ?>

    <?php // echo $form->field($model, 'remarks') ?>

    <?php // echo $form->field($model, 'ip') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'crt_by') ?>

    <?php // echo $form->field($model, 'mod_by') ?>

    <?php // echo $form->field($model, 'crt_time') ?>

    <?php // echo $form->field($model, 'mod_time') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
