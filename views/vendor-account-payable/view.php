<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\TblVendorAccountPayable $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Vendor Account Payables', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
//menu item id 81
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id])->andWhere(['fk_menu_id'=>63])->one();

?>
<div class="tbl-vendor-account-payable-view">

  <div class="card shadow rounded mt-2">
        <div class="card-body p-3">
            <div class="row">
                <div class="col-lg-6">
                    <h3><?= Html::encode($this->title) ?></h3>
                </div>
                <div class="col-lg-6 text-end">
                  <?php
                    if(isset($menuaccess) && $menuaccess->edit_crud == 1){
                      ?>
                      <?= Html::a('<i class="fas fa-pen"></i> Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']) ?>
                      <?php
                    }
                    if(isset($menuaccess) && $menuaccess->delete_crud == 1){
                      ?>
                      <?= Html::a('<i class="fas fa-trash"></i> Delete', ['delete', 'id' => $model->id], [
                          'class' => 'btn btn-danger btn-sm',
                          'data' => [
                              'confirm' => 'Are you sure you want to delete this item?',
                              'method' => 'post',
                          ],
                      ]) ?>
                      <?php
                    }
                   ?>


                    <?= Html::a('<i class="fas fa-chevron-left"></i> Back to List', ['index'], ['class' => 'btn btn-secondary btn-sm']) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow rounded mt-2">
          <div class="card-body p-3">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            // 'fk_vendor_invoice_id',
            [
              'attribute' => 'invoice',
              'value' => function($model){
                return $model->invoice ? $model->invoice->vendor_invoice_number : '';
              },
              'label' => 'Invoice #'
          ],
          [
              'attribute' => 'client',
              'value' => function($model){
                return $model->client ? $model->client->company_name : '';
              },
              'label' => 'Vendor', // Custom label
          ],
            'amount_received',
            'ar_date',
            // 'fk_payment_method_id',
            [
              'attribute' => 'fk_payment_method_id',
              'value' => function($model){
                $getpstatus = \app\models\TblPreferredPaymentMethod::find()->where(['id'=>$model->fk_payment_method_id])->one();
                if(isset($getpstatus) && $getpstatus->title != ""){
                  return $getpstatus->title;
                }else{
                  return "(not set)";
                }
              },

            ],
            'notes:ntext',
            // 'ip',
            // 'status',
            // 'crt_by',
            // 'mod_by',
            // 'crt_time',
            // 'mod_time',
        ],
    ]) ?>

</div>
</div>
</div>
