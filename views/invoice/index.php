<?php

use app\models\TblInvoice;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use app\models\TblTerms;
use app\models\TblClient;
use app\models\TblOwnershipCompany;
/** @var yii\web\View $this */
/** @var app\models\TblInvoiceSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Invoices';
$this->params['breadcrumbs'][] = $this->title;
$template = '{view}';
$termsarr = TblTerms::find()->select(['title', 'id'])->where('status != 0')->indexBy('id')->column();
$paymentStatusArr = [
    TblInvoice::STATUS_PAID => 'Paid',
    TblInvoice::STATUS_PARTIALLY_PAID => 'Partially Paid',
    TblInvoice::STATUS_UNPAID => 'Unpaid'
];

$allbillfrom = TblOwnershipCompany::find()->where(['status'=>1])->orderBy(['company_name'=>SORT_ASC])->all();
$billfromarr = ArrayHelper::map($allbillfrom,'id','location_name');

use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>22,'status'=>1])->one();

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
        <?= Html::a('Create Invoice', ['create'], ['class' => 'btn btn-primary btn-sm']) ?>
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
<div class="tbl-invoice-index table-responsive">
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
                  return Html::a('<i class=" fas fa-eye"></i>', $url, [
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
                  $url ='index.php?r=invoice/update&id='.$model->id;
                  return $url;
                }
                if ($action === 'view') {
                  $url ='index.php?r=invoice/view&id='.$model->id;
                  return $url;
                }
                if ($action === 'delete') {
                  $url ='index.php?r=invoice/delete&id='.$model->id;
                  return $url;
                }
              }
            ],
            // 'id',
            'invoice_number',
            'po_number',
            // 'fk_bill_from_id',
            // [
            //   'attribute' => 'fk_bill_from_id',
            //   'label' => 'Bill From Company',
            //   'value' => function($dataProvider){
            //     $getcompany = TblOwnershipCompany::find()->where(['id'=>$dataProvider->fk_bill_from_id])->one();
            //     if(isset($getcompany) && $getcompany->id != ""){
            //       return $getcompany->location_name;
            //     }else{
            //       return '<i>(not set)</i>';
            //     }
            //   },
            //   'filter' => Html::activeDropDownList($searchModel, 'fk_bill_from_id', $billfromarr,['class'=>'form-control','prompt' => 'Select']),
            // ],
            // 'fk_client_id',
            [
              'attribute' => 'customer',
              'value' => 'customer.company_name'
            ],
            // [
            //   'attribute' => 'fk_client_id',
            //   'value' => function($dataProvider){
            //     $getpstatus = TblClient::find()->where(['id'=>$dataProvider->fk_client_id])->one();
            //     if(isset($getpstatus) && $getpstatus->company_name != ""){
            //       return $getpstatus->company_name;
            //     }else{
            //       return "(not set)";
            //     }
            //   },
            // ],
            // 'fk_terms_id',
            [
              'attribute' => 'fk_terms_id',
              'value' => function($dataProvider){
                $getpstatus = TblTerms::find()->where(['id'=>$dataProvider->fk_terms_id])->one();
                if(isset($getpstatus) && $getpstatus->title != ""){
                  return $getpstatus->title;
                }else{
                  return "(not set)";
                }
              },
              'filter' => Html::activeDropDownList($searchModel, 'fk_terms_id', $termsarr,['class'=>'form-select','prompt' => 'Select']),
            ],
            'invoice_date',
            'due_date',
            //'hst',
            //'discount_type',
            //'discount_value',
            //'subtotal',
            //'hst_amount',
            'total_amount',
            [
                'attribute' => 'total_received',
                'format' => 'raw',
                'value' => function($model) {
                    return $model->getTotalReceived(); // Use the method or directly access $model->total_received
                },
                'label' => 'Amount Received',
                'filter' => Html::activeTextInput($searchModel, 'total_received', ['class' => 'form-control']),
            ],
            [
              'attribute' => 'payment_status',
              'format' => 'raw',
              'value' => function($model) {
                  return $model->getPaymentStatusBadge();
              },
              'label' => 'Payment Status',
              'filter' => Html::activeDropDownList($searchModel, 'payment_status', $paymentStatusArr, ['class' => 'form-select', 'prompt' => 'Select'])
            ],
            [
                'attribute' => 'emailSentStatus',
                'format' => 'raw',
                'value' => function($model) {
                    return $model->getEmailSentStatus();
                },
                'filter' => [
                    'Sent' => 'Sent',
                    'Not Sent' => 'Not Sent'
                ],
            ],
            //'comments:ntext',
            //'status',
            //'ip',
            //'crt_time',
            //'crt_by',
            //'mod_time',
            //'mod_by',
            // [
            //     'class' => ActionColumn::className(),
            //     'urlCreator' => function ($action, TblInvoice $model, $key, $index, $column) {
            //         return Url::toRoute([$action, 'id' => $model->id]);
            //      }
            // ],
        ],
    ]); ?>


</div>

</div>

</div>
