<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TblPurchaseOrderSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-purchase-order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'po_number') ?>

    <?= $form->field($model, 'fk_vendor_id') ?>

    <?= $form->field($model, 'po_date') ?>

    <?= $form->field($model, 'hst') ?>

    <?php // echo $form->field($model, 'subtotal') ?>

    <?php // echo $form->field($model, 'hst_amount') ?>

    <?php // echo $form->field($model, 'total_amount') ?>

    <?php // echo $form->field($model, 'comments') ?>

    <?php // echo $form->field($model, 'purchase_status') ?>

    <?php // echo $form->field($model, 'payment_status') ?>

    <?php // echo $form->field($model, 'approved_by') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'ip') ?>

    <?php // echo $form->field($model, 'crt_time') ?>

    <?php // echo $form->field($model, 'crt_by') ?>

    <?php // echo $form->field($model, 'mod_time') ?>

    <?php // echo $form->field($model, 'mod_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
