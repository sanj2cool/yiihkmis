<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblBinLevel $model */

$this->title = 'Update Tbl Bin Level: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Bin Levels', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbl-bin-level-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
