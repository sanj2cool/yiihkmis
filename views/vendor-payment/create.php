<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblVendorPayment $model */

$this->title = 'Settle Vendor Payment';
$this->params['breadcrumbs'][] = ['label' => 'Tbl Vendor Payments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-vendor-payment-create">

  <div class="card shadow rounded mt-2">
    <div class="card-body p-3">
      <div class="row">
        <div class="col-lg-6">
          <h4><?= Html::encode($this->title) ?></h4>
        </div>
        <div class="col-lg-6 text-end">
          <?= Html::a('<i class="fas fa-chevron-left"></i> Back to List', ['index'], ['class' => 'btn btn-secondary btn-sm']) ?>
        </div>
      </div>
    </div>
  </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
