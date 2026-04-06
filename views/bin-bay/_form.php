<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TblBinBay $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-bin-bay-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'crt_by')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mod_by')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'crt_time')->textInput() ?>

    <?= $form->field($model, 'mod_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
