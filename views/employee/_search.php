<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TblEmployeeSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-employee-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'email') ?>

    <?= $form->field($model, 'phone_no') ?>

    <?= $form->field($model, 'user_key') ?>

    <?php // echo $form->field($model, 'fk_role_id') ?>

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
