<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblBinPosition $model */

$this->title = 'Create Tbl Bin Position';
$this->params['breadcrumbs'][] = ['label' => 'Tbl Bin Positions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-bin-position-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
