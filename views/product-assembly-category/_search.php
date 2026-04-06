<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TblProductAssemblyCategorySearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-product-assembly-category-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'description') ?>

    <?= $form->field($model, 'image_url') ?>

    <?= $form->field($model, 'show_on_website') ?>

    <?php // echo $form->field($model, 'img_width') ?>

    <?php // echo $form->field($model, 'translate_x') ?>

    <?php // echo $form->field($model, 'translate_y') ?>

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
