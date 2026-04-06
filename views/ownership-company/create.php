<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblOwnershipCompany $model */

$this->title = 'Create Tbl Ownership Company';
$this->params['breadcrumbs'][] = ['label' => 'Tbl Ownership Companies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-ownership-company-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
