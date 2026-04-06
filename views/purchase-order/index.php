<?php

use app\models\TblPurchaseOrder;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\TblEmployee;
use app\models\TblPurchaseStatus;
use app\models\TblPurchasePaymentStatus;
use app\models\TblVendor;
/** @var yii\web\View $this */
/** @var app\models\TblPurchaseOrderSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Purchase Orders';
$this->params['breadcrumbs'][] = $this->title;
$template = '{view} ';
use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>11,'status'=>1])->one();
if(isset($menuaccess) && $menuaccess->edit_crud == 1){
  $template .= ' {edit}';
}
if(isset($menuaccess) && $menuaccess->delete_crud == 1){
  $template .= ' {delete}';
}
//-----get employees------------
$allemps = TblEmployee::find()->where(['status'=>1])->all();
$emparr = ArrayHelper::map($allemps,'id','name');
//-------get purchase status ----
$allpstatuses = TblPurchaseStatus::find()->where(['status'=>1])->all();
$pstatusarr = ArrayHelper::map($allpstatuses,'id','title');

//-------get payment status ----
$allpaymentstatuses = TblPurchasePaymentStatus::find()->where(['status'=>1])->all();
$paymentstatusarr = ArrayHelper::map($allpaymentstatuses,'id','title');
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
        <?= Html::a('Create Purchase Order', ['create'], ['class' => 'btn btn-primary btn-sm']) ?>
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
    </div>


<div class="tbl-purchase-order-index table-responsive">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            [
              'class' => ActionColumn::className(),
              'template' => $template,
              'buttons' => [
                'view' => function ($url, $model) {
                  return Html::a('<i class=" fas fa-file-pdf"></i>', $url, [
                    'title' => Yii::t('app', 'view'),
                    'class' => 'text-info'
                  ]);
                },
                'edit' => function ($url, $model) {
                  return Html::a('<i class=" fas fa-pencil-alt"></i>', $url, [
                    'title' => Yii::t('app', 'edit'),
                    'class' => 'text-primary'
                  ]);
                },
                'delete' => function ($url, $model, $key) {
                  $options = [
                    'title' => Yii::t('yii', 'Delete'),
                    'aria-label' => Yii::t('yii', 'Delete'),
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                    'class' => 'text-danger'
                  ];
                  return Html::a('<i class="fas fa-trash"></i>', $url, $options);
                }
              ],
              'urlCreator' => function ($action, $model, $key, $index) {
                if ($action === 'edit') {
                  $url ='index.php?r=purchase-order/update&id='.$model->id;
                  return $url;
                }
                if ($action === 'view') {
                  $url ='index.php?r=purchase-order/view&id='.$model->id;
                  return $url;
                }
                if ($action === 'delete') {
                  $url ='index.php?r=purchase-order/delete&id='.$model->id;
                  return $url;
                }
              }
            ],
            // 'id',
            'po_number',
            // 'fk_vendor_id',
            [
              'attribute' => 'vendor',
              'value' => 'vendor.company_name'
            ],
            // [
            //   'attribute' => 'fk_vendor_id',
            //   'label' => 'Vendor',
            //   'value' => function($dataProvider){
            //     $getvendor = TblVendor::find()->where(['id'=>$dataProvider->fk_vendor_id,'status'=>1])->one();
            //     if(isset($getvendor) && $getvendor->company_name != ""){
            //       return $getvendor->company_name;
            //     }else{
            //       return "(not set)";
            //     }
            //   }
            // ],
            'po_date',
            'expected_delivery_date',
            // 'hst',
            //'subtotal',
            //'hst_amount',
            'total_amount',
            //'comments:ntext',
            // 'purchase_status',
            [
              'attribute' => 'purchase_status',
              'label' => 'Purchase Status',
              'value' => function($dataProvider){
                $getpstatus = TblPurchaseStatus::find()->where(['id'=>$dataProvider->purchase_status])->one();
                if(isset($getpstatus) && $getpstatus->title != ""){
                  return $getpstatus->title;
                }else{
                  return "(not set)";
                }
              },
              'filter' => Html::activeDropDownList($searchModel, 'purchase_status', $pstatusarr,['class'=>'form-select','prompt' => 'Select']),

            ],
            // 'payment_status',
            [
              'attribute' => 'payment_status',
              'label' => 'Payment Status',
              'value' => function($dataProvider){
                $getpstatus = TblPurchasePaymentStatus::find()->where(['id'=>$dataProvider->payment_status])->one();
                if(isset($getpstatus) && $getpstatus->title != ""){
                  return $getpstatus->title;
                }else{
                  return "(not set)";
                }
              },
              'filter' => Html::activeDropDownList($searchModel, 'payment_status', $paymentstatusarr,['class'=>'form-select','prompt' => 'Select']),

            ],
				[
  'attribute' => 'crt_by',
  'label' => 'Created By',
  'value' => function ($model) {
      return $model->getCreatedByName();
  },
],
						// 'approved_by',
            [
              'attribute' => 'approved_by',
              'label' => 'Approved By',
              'value' => function($dataProvider){
                $getprepby = TblEmployee::find()->where(['id'=>$dataProvider->approved_by])->one();
                if(isset($getprepby) && $getprepby->name != ""){
                  return $getprepby->name;
                }else{
                  return "(not set)";
                }
              },
              'filter' => Html::activeDropDownList($searchModel, 'approved_by', $emparr,['class'=>'form-select','prompt' => 'Select']),
            ],
            'internal_notes',
            //'status',
            //'ip',
            //'crt_time',
            //'crt_by',
            //'mod_time',
            //'mod_by',
            // [
            //     'class' => ActionColumn::className(),
            //     'urlCreator' => function ($action, TblPurchaseOrder $model, $key, $index, $column) {
            //         return Url::toRoute([$action, 'id' => $model->id]);
            //      }
            // ],
        ],
    ]); ?>


</div>
</div>
</div>
