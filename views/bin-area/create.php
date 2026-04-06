<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblBinArea $model */

$this->title = 'Create Tbl Bin Area';
$this->params['breadcrumbs'][] = ['label' => 'Tbl Bin Areas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-bin-area-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
