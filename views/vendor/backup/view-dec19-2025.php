<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\TblVendor $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Vendors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tbl-vendor-view">

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
            'company_name',
            'contact_name',
            'contact_title',
            'email:email',
            'phone',
            'address',
            'city',
            'state',
            'postal_code',
            'country',
            'fk_terms_id',
            'status',
            'ip',
            'crt_time',
            'crt_by',
            'mod_time',
            'mod_by',
        ],
    ]) ?>

</div>
