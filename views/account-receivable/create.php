<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblAccountReceivable $model */

$this->title = 'Create Account Receivable';
$this->params['breadcrumbs'][] = ['label' => 'Account Receivables', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-account-receivable-create">

  <div class="card shadow rounded mt-2">
        <div class="card-body p-3">
            <div class="row">
                <div class="col-lg-6">
                    <h3><?= Html::encode($this->title) ?></h3>
                </div>
                <div class="col-lg-6 text-end">
                    <?= Html::a('Back to List', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
                </div>
            </div>
        </div>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
