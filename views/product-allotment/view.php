<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\TblProductAllotment $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Product Allotments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tbl-product-allotment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'fk_product_id',
            'fk_vendor_invoice_id',
            'no_of_items',
            'allotment_date',
            'allotted_by',
            'fk_allotment_status_id',
            'remarks:ntext',
            'ip',
            'status',
            'crt_by',
            'mod_by',
            'crt_time',
            'mod_time',
        ],
    ]) ?>

</div>
