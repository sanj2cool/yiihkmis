<?php

use app\models\TblAccountReceivable;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use app\models\TblPreferredPaymentMethod;
use app\models\TblOwnershipCompany;
use yii\helpers\ArrayHelper;
/** @var yii\web\View $this */
/** @var app\models\TblAccountReceivableSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Account Receivables';
$this->params['breadcrumbs'][] = $this->title;

use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
//menu item id 81
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id])->andWhere(['fk_menu_id'=>64])->one();
$temp_str = "";
if(isset($menuaccess) && $menuaccess->edit_crud == 1){
  $temp_str .= '{edit}';
}
if(isset($menuaccess) && $menuaccess->delete_crud == 1){
  $temp_str .= ' {delete}';
}
$template = '{view}';
$prefpayarr = TblPreferredPaymentMethod::find()->select(['title', 'id'])->where('status != 0')->indexBy('id')->orderBy(['title'=>SORT_ASC])->column();
$allbillfrom = TblOwnershipCompany::find()->where(['status'=>1])->orderBy(['company_name'=>SORT_ASC])->all();
$billfromarr = ArrayHelper::map($allbillfrom,'id','location_name');

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
          <!-- <?= Html::a('Receive Payments', ['create-multiple'], ['class' => 'btn btn-secondary btn-sm']) ?> -->
          <!-- <?= Html::a('Create Account Receivable', ['create'], ['class' => 'btn btn-primary btn-sm']) ?> -->
          <?php
        }
        ?>
      </div>
    </div>
  </div>
  <div class="card-body">
    <div class="alert alert-danger d-flex align-items-center" role="alert">
      <span class="fas fa-exclamation-triangle text-white fs-5 me-3"></span>
      <p class="mb-0 flex-1">MODULE IS UNDER MAINTENANCE.</p>
    </div>
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
    <div class="tbl-account-receivable-index  table-responsive">



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
              'edit' => function ($url, $model) {
                return Html::a('<i class=" fas fa-pencil-alt"></i>', $url, [
                  'title' => Yii::t('app', 'edit'),
                ]);
              },
              'view' => function ($url, $model) {
                return Html::a('<i class="fas fa-eye text-info"></i>', $url, [
                  'title' => Yii::t('app', 'view'),
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
              if ($action === 'view') {
                $url ='index.php?r=account-receivable/view&id='.$model->id;
                return $url;
              }
              if ($action === 'edit') {
                $url ='index.php?r=account-receivable/update&id='.$model->id;
                return $url;
              }
              if ($action === 'delete') {
                $url ='index.php?r=account-receivable/delete&id='.$model->id;
                return $url;
              }
            }
          ],
          // 'id',
          // 'fk_invoice_id',
          [
            'attribute' => 'invoice',
            'value' => 'invoice.invoice_number',
            'label' => 'Invoice #'
          ],
          [
            'attribute' => 'client',
            'value' => 'client.company_name', // Accessing the related customer name
            'label' => 'Customer', // Custom label
          ],
          [
            'attribute' => 'fk_bill_from_id',
            'label' => 'Receiving Company',
            'value' => function($dataProvider){
              $getcompany = TblOwnershipCompany::find()->where(['id'=>$dataProvider->fk_bill_from_id])->one();
              if(isset($getcompany) && $getcompany->id != ""){
                return $getcompany->location_name;
              }else{
                return '<i>(not set)</i>';
              }
            },
            'filter' => Html::activeDropDownList($searchModel, 'fk_bill_from_id', $billfromarr,['class'=>'form-select','prompt' => 'Select']),
          ],
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
          //     'urlCreator' => function ($action, TblAccountReceivable $model, $key, $index, $column) {
          //         return Url::toRoute([$action, 'id' => $model->id]);
          //      }
          // ],
        ],
      ]); ?>


    </div>
  </div>
</div>
