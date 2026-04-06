<?php

use app\models\TblVendorAccountPayable;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use app\models\TblPreferredPaymentMethod;
/** @var yii\web\View $this */
/** @var app\models\TblVendorAccountPayableSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Vendor Account Payables';
$this->params['breadcrumbs'][] = $this->title;
use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
//menu item id 81
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id])->andWhere(['fk_menu_id'=>63])->one();
$temp_str = "";
if(isset($menuaccess) && $menuaccess->edit_crud == 1){
  $temp_str .= '{edit}';
}
if(isset($menuaccess) && $menuaccess->delete_crud == 1){
  $temp_str .= ' {delete}';
}
$template = $temp_str;
$prefpayarr = TblPreferredPaymentMethod::find()->select(['title', 'id'])->where('status != 0')->indexBy('id')->column();
?>
<div class="card mb-3">
  <div class="card-header pt-3 pb-2">
    <div class="row">
      <div class="col-lg-6">
        <h3>
          <?= Html::encode($this->title) ?>
        </h3>
      </div>
      <div class="col-lg-6 text-end">
        <?= Html::a('Reset Filters', ['reset-filters'], ['class' => 'btn btn-warning btn-sm']) ?>
        <?php
        if(isset($menuaccess) && $menuaccess->create_crud == 1){
         ?>
         <?= Html::a('Settle Payments', ['create-multiple'], ['class' => 'btn btn-secondary btn-sm']) ?>
        <?= Html::a('Create Account Payable', ['create'], ['class' => 'btn btn-primary btn-sm']) ?>
        <?php
          }
         ?>
      </div>
    </div>
  </div>
  <div class="card-body">
    <div id="msg">

      <?php
      if(Yii::$app -> session -> getFlash('success')!=null){
        ?>

        <div class="alert alert-outline-success d-flex align-items-center" role="alert">
          <span class="fas fa-check-circle text-success fs-5 me-3"></span>
          <p class="mb-0 flex-1"><?= Yii::$app -> session -> getFlash('success'); ?></p>

          <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
      }else if(Yii::$app -> session -> getFlash('error')!=null){
        ?>
        <div class="alert alert-outline-danger d-flex align-items-center" role="alert">
          <span class="fas fa-times-circle text-danger fs-5 me-3"></span>
          <p class="mb-0 flex-1"><?= Yii::$app -> session -> getFlash('error'); ?></p>
          <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <?php
      }
      ?>
      <div class="alert alert-warning d-flex align-items-center" role="alert">
        <span class="fas fa-exclamation-triangle text-white fs-5 me-3"></span>
        <p class="mb-0 flex-1">This module is under maintenance.</p>
      </div>
    </div>
<div class="tbl-vendor-account-payable-index table-responsive">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            [
                      'class' => ActionColumn::className(),
                      'template' => '{view}',
                      'buttons' => [
                          'edit' => function ($url, $model) {
                            return Html::a('<i class=" fas fa-pencil-alt"></i>', $url, [
                                        'title' => Yii::t('app', 'edit'),
                            ]);
                          },
                          'view' => function ($url, $model) {
                            return Html::a('<i class=" fas fa-eye"></i>', $url, [
                              'title' => Yii::t('app', 'view'),
                              'class' => 'text-info'
                            ]);
                          },
                          'delete' => function ($url, $model, $key) {
                              $options = [
                              'title' => Yii::t('yii', 'Delete'),
                              'aria-label' => Yii::t('yii', 'Delete'),
                              'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                              'data-method' => 'post',
                              'data-pjax' => '0',
                              ];
                              return Html::a('<i class="fas fa-trash text-danger"></i>', $url, $options);
                          }
                      ],
                      'urlCreator' => function ($action, $model, $key, $index) {
                          if ($action === 'edit') {
                              $url ='index.php?r=vendor-account-payable/update&id='.$model->id;
                              return $url;
                          }
                          if ($action === 'view') {
                            $url ='index.php?r=vendor-account-payable/view&id='.$model->id;
                            return $url;
                          }
                          if ($action === 'delete') {
                              $url ='index.php?r=vendor-account-payable/delete&id='.$model->id;
                              return $url;
                          }
                      }
                  ],
            // 'id',
            [
              'attribute' => 'invoice',
              'value' => 'invoice.vendor_invoice_number',
              'label' => 'Invoice #'
          ],
          [
              'attribute' => 'client',
              'value' => 'client.company_name', // Accessing the related customer name
              'label' => 'Vendor', // Custom label
          ],
            // 'fk_vendor_invoice_id',
            'amount_received',
            'ar_date',
            // 'fk_payment_method_id',
            [
              'attribute' => 'fk_payment_method_id',
              'value' => function($dataProvider){
                $getpstatus = TblPreferredPaymentMethod::find()->where(['id'=>$dataProvider->fk_payment_method_id])->one();
                if(isset($getpstatus) && $getpstatus->title != ""){
                  return $getpstatus->title;
                }else{
                  return "(not set)";
                }
              },
              'filter' => Html::activeDropDownList($searchModel, 'fk_payment_method_id', $prefpayarr,['class'=>'form-select','prompt' => 'Select']),

            ],
            //'notes:ntext',
            //'ip',
            //'status',
            //'crt_by',
            //'mod_by',
            //'crt_time',
            //'mod_time',
            // [
            //     'class' => ActionColumn::className(),
            //     'urlCreator' => function ($action, TblVendorAccountPayable $model, $key, $index, $column) {
            //         return Url::toRoute([$action, 'id' => $model->id]);
            //      }
            // ],
        ],
    ]); ?>


</div>
</div>
</div>
