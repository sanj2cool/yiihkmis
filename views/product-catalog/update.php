<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblProductCatalog $model */

$this->title = 'Update Tbl Product Catalog: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Product Catalogs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbl-product-catalog-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
