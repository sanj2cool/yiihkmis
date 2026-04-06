<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblOwnershipCompany $model */

$this->title = 'Update Tbl Ownership Company: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Ownership Companies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbl-ownership-company-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
