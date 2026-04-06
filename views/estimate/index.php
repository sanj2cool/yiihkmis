<?php

use app\models\TblEstimate;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use app\models\TblTerms;
use app\models\TblClient;
use app\models\TblOwnershipCompany;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\TblEstimateSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Estimates';
$this->params['breadcrumbs'][] = $this->title;

$template = '{view}';
$termsarr = TblTerms::find()->select(['title', 'id'])->where('status != 0')->indexBy('id')->column();

$allbillfrom = TblOwnershipCompany::find()->where(['status'=>1])->orderBy(['company_name'=>SORT_ASC])->all();
$billfromarr = ArrayHelper::map($allbillfrom,'id','location_name');

use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>75,'status'=>1])->one();

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
          <?= Html::a('Create Estimate', ['create'], ['class' => 'btn btn-primary btn-sm']) ?>
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
    <div class="tbl-estimate-index table-responsive">


      <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
          ['class' => 'yii\grid\SerialColumn'],

          // 'id',
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
                $url ='index.php?r=estimate/update&id='.$model->id;
                return $url;
              }
              if ($action === 'view') {
                $url ='index.php?r=estimate/view&id='.$model->id;
                return $url;
              }
              if ($action === 'delete') {
                $url ='index.php?r=estimate/delete&id='.$model->id;
                return $url;
              }
            }
          ],
          'estimate_number',
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
          //   'filter' => Html::activeDropDownList($searchModel, 'fk_bill_from_id', $billfromarr,['class'=>'form-select','prompt' => 'Select']),
          // ],
          // 'fk_client_id',
          [
            'attribute' => 'customer',
            'value' => 'customer.company_name'
          ],
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
          //'fk_terms_id',
          'estimate_date',
          'due_date',
          //'hst',
          //'discount_type',
          //'discount_value',
          //'subtotal',
          //'hst_amount',
          'total_amount',
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
          //   'class' => ActionColumn::className(),
          //   'urlCreator' => function ($action, TblEstimate $model, $key, $index, $column) {
          //     return Url::toRoute([$action, 'id' => $model->id]);
          //   }
          // ],
        ],
      ]); ?>


    </div>
  </div>
</div>
