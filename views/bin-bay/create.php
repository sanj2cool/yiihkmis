<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblBinBay $model */

$this->title = 'Create Tbl Bin Bay';
$this->params['breadcrumbs'][] = ['label' => 'Tbl Bin Bays', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-bin-bay-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
