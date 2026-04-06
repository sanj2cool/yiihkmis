<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TblEstimateSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-estimate-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'estimate_number') ?>

    <?= $form->field($model, 'po_number') ?>

    <?= $form->field($model, 'fk_bill_from_id') ?>

    <?= $form->field($model, 'fk_client_id') ?>

    <?php // echo $form->field($model, 'fk_terms_id') ?>

    <?php // echo $form->field($model, 'estimate_date') ?>

    <?php // echo $form->field($model, 'due_date') ?>

    <?php // echo $form->field($model, 'hst') ?>

    <?php // echo $form->field($model, 'discount_type') ?>

    <?php // echo $form->field($model, 'discount_value') ?>

    <?php // echo $form->field($model, 'subtotal') ?>

    <?php // echo $form->field($model, 'hst_amount') ?>

    <?php // echo $form->field($model, 'total_amount') ?>

    <?php // echo $form->field($model, 'comments') ?>

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
