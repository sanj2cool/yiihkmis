<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblProductAllotment $model */

$this->title = 'Create Item Allotment';
$this->params['breadcrumbs'][] = ['label' => 'Item Allotments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-product-allotment-create">
  <div class="card shadow rounded mt-2">
      <div class="card-body p-3">
      <div class="row">
        <div class="col-lg-6">
          <h3><?= Html::encode($this->title) ?></h3>
        </div>
        <div class="col-lg-6 text-end">
              <?= Html::a('Back to List', ['index'], ['class' => 'btn btn-danger btn-sm']) ?>
        </div>
      </div>
    </div>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
