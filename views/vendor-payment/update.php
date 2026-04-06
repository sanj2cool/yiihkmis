<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblVendorPayment $model */

$this->title = 'Update Tbl Vendor Payment: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Vendor Payments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbl-vendor-payment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
