<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblBinRow $model */

$this->title = 'Create Tbl Bin Row';
$this->params['breadcrumbs'][] = ['label' => 'Tbl Bin Rows', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-bin-row-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
