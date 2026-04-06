<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TblExpenseSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-expense-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'date') ?>

    <?= $form->field($model, 'amount') ?>

    <?= $form->field($model, 'fk_tax_rate_id') ?>

    <?= $form->field($model, 'tax_amount') ?>

    <?php // echo $form->field($model, 'final_amount') ?>

    <?php // echo $form->field($model, 'fk_location_id') ?>

    <?php // echo $form->field($model, 'fk_vendor_id') ?>

    <?php // echo $form->field($model, 'fk_expense_category_id') ?>

    <?php // echo $form->field($model, 'vendor_name') ?>

    <?php // echo $form->field($model, 'fk_payment_method_id') ?>

    <?php // echo $form->field($model, 'receipt_no') ?>

    <?php // echo $form->field($model, 'notes') ?>

    <?php // echo $form->field($model, 'ip') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'crt_by') ?>

    <?php // echo $form->field($model, 'crt_time') ?>

    <?php // echo $form->field($model, 'mod_by') ?>

    <?php // echo $form->field($model, 'mod_time') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
