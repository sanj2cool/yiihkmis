<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblClientReceipt $model */

$this->title = 'Receive Payment';
$this->params['breadcrumbs'][] = ['label' => 'Tbl Client Receipts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-client-receipt-create">

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
