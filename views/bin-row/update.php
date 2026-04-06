<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblBinRow $model */

$this->title = 'Update Tbl Bin Row: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Bin Rows', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbl-bin-row-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
