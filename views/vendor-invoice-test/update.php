<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblVendorInvoice $model */

$this->title = 'Update Purchase Invoice: ' . $model->vendor_invoice_number;
$this->params['breadcrumbs'][] = ['label' => 'Vendor Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbl-vendor-invoice-update">

  <div class="card shadow rounded mt-2">
      <div class="card-body p-3">
      <div class="row">
        <div class="col-lg-6">
          <h3><?= Html::encode($this->title) ?></h3>
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
