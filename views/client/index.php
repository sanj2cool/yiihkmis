<?php

use app\models\TblClient;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use app\models\TblPreferredPaymentMethod;
use app\models\TblTerms;
use app\models\TblCreditLimit;
/** @var yii\web\View $this */
/** @var app\models\TblClientSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Clients';
$this->params['breadcrumbs'][] = $this->title;
// {sendcreds}
$template = '{view}';
$status = [1=>"Active",2=>"Inactive"];
$prefpayarr = TblPreferredPaymentMethod::find()->select(['title', 'id'])->where('status != 0')->indexBy('id')->column();
$termsarr = TblTerms::find()->select(['title', 'id'])->where('status != 0')->indexBy('id')->column();
$creditarr = TblCreditLimit::find()->select(['title', 'id'])->where('status != 0')->indexBy('id')->column();
use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>13,'status'=>1])->one();
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
        <?= Html::a('Create Client', ['create'], ['class' => 'btn btn-primary btn-sm']) ?>
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

<div class="tbl-client-index table-responsive">


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
                    'class' => 'text-primary'
                  ]);
                },
                'view' => function ($url, $model) {
                  return Html::a('<i class=" fas fa-eye"></i>', $url, [
                    'title' => Yii::t('app', 'view'),
                    'class' => 'text-info'
                  ]);
                },
                'sendcreds' => function ($url, $model) {
                  return Html::a('<i class=" fas fa-envelope"></i>', $url, [
                    'title' => Yii::t('app', 'Send Credentials'),
                    'class' => 'text-info',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top'
                  ]);
                },
                'delete' => function ($url, $model, $key) {
                  if ($model->id == 2) {
                        return ''; // Return an empty string to hide the button
                    } else {
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
                }
              ],
              'urlCreator' => function ($action, $model, $key, $index) {
                if ($action === 'edit') {
                  $url ='index.php?r=client/update&id='.$model->id;
                  return $url;
                }
                if ($action === 'view') {
                  $url ='index.php?r=client/view&id='.$model->id;
                  return $url;
                }
                if ($action === 'sendcreds') {
                  $url ='index.php?r=client/send-credentials&id='.$model->id;
                  return $url;
                }
                if ($action === 'delete') {
                  $url ='index.php?r=client/delete&id='.$model->id;
                  return $url;
                }
              }
            ],
            [
              'attribute' => 'status',
              'label' => 'Status',
              'format' => 'raw',
              'value' => function($dataProvider){
                if($dataProvider->status == 1){
                  return '<span class="badge bg-success">Active</span>';
                }else if($dataProvider->status == 2){
                  return '<span class="badge bg-danger">Inactive</span>';
                }else if($dataProvider->status == 3){
                  return '<span class="badge bg-warning">In Review</span>';
                }else{
                  return '<span class="badge bg-secondary">(not set)</span>';
                }
              },
              'filter' => Html::activeDropDownList($searchModel, 'status', [1=>"Active",2=>"Inactive",3=>"In Review"],['class'=>'form-select','prompt' => 'Select']),
            ],
            // 'id',
            'company_name',
            'contact_name',
            'contact_title',
            'email:email',
            'phone',
            'address',
            'city',
            //'state',
            //'postal_code',
            //'country',
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
            // 'fk_preferred_payment_method_id',
            [
              'attribute' => 'fk_preferred_payment_method_id',
              'value' => function($dataProvider){
                $getpstatus = TblPreferredPaymentMethod::find()->where(['id'=>$dataProvider->fk_preferred_payment_method_id])->one();
                if(isset($getpstatus) && $getpstatus->title != ""){
                  return $getpstatus->title;
                }else{
                  return "(not set)";
                }
              },
              'filter' => Html::activeDropDownList($searchModel, 'fk_preferred_payment_method_id', $prefpayarr,['class'=>'form-select','prompt' => 'Select']),

            ],
            // 'credit_limit',
            [
              'attribute' => 'credit_limit',
              'value' => function($dataProvider){
                $getpstatus = TblCreditLimit::find()->where(['id'=>$dataProvider->credit_limit])->one();
                if(isset($getpstatus) && $getpstatus->title != ""){
                  return $getpstatus->title;
                }else{
                  return "(not set)";
                }
              },
              'filter' => Html::activeDropDownList($searchModel, 'credit_limit', $creditarr,['class'=>'form-select','prompt' => 'Select']),
            ],
            //'notes:ntext',
            //'status',
            //'ip',
            //'crt_time',
            //'crt_by',
            //'mod_time',
            //'mod_by',
            // [
            //     'class' => ActionColumn::className(),
            //     'urlCreator' => function ($action, TblClient $model, $key, $index, $column) {
            //         return Url::toRoute([$action, 'id' => $model->id]);
            //      }
            // ],
        ],
    ]); ?>


</div>

</div>

</div>
