<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblBin $model */

$this->title = 'Update Tbl Bin: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Bins', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbl-bin-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
