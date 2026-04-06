<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblBinBay $model */

$this->title = 'Update Tbl Bin Bay: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Bin Bays', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbl-bin-bay-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
