<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblClientReceipt $model */

$this->title = 'Update Tbl Client Receipt: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Client Receipts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbl-client-receipt-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
