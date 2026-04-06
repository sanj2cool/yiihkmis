<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TblClientReceiptSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-client-receipt-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'fk_client_id') ?>

    <?= $form->field($model, 'fk_invoice_id') ?>

    <?= $form->field($model, 'amount_received') ?>

    <?= $form->field($model, 'ar_date') ?>

    <?php // echo $form->field($model, 'fk_payment_method_id') ?>

    <?php // echo $form->field($model, 'fk_bill_from_id') ?>

    <?php // echo $form->field($model, 'notes') ?>

    <?php // echo $form->field($model, 'ip') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'crt_by') ?>

    <?php // echo $form->field($model, 'mod_by') ?>

    <?php // echo $form->field($model, 'crt_time') ?>

    <?php // echo $form->field($model, 'mod_time') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?php /* Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) */ ?>
		<?= Html::a('Reset', ['index'], [
        'class' => 'btn btn-outline-secondary',
        'data-pjax' => 0,
    ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
