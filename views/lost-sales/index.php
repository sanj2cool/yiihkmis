<?php

use app\models\TblLostSalesModule;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\TblEmployee;
use app\models\TblProduct;
/** @var yii\web\View $this */
/** @var app\models\TblLostSalesModuleSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Lost Sales';
$this->params['breadcrumbs'][] = $this->title;

$template = '';
use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>66,'status'=>1])->one();
$menuaccess_c = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>67,'status'=>1])->one();
if(isset($menuaccess) && $menuaccess->edit_crud == 1){
  $template .= ' {edit}';
}
if(isset($menuaccess) && $menuaccess->delete_crud == 1){
  $template .= ' {delete}';
}
//-----get employees------------
$session = Yii::$app -> session;
$fk_location_id = $session['userCompany'];

$allemps = TblEmployee::find()->where(['status'=>1,'fk_location_id'=>$fk_location_id])->all();
$emparr = ArrayHelper::map($allemps,'id','name');
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
        if(isset($menuaccess_c) && $menuaccess_c->create_crud == 1){
         ?>
        <?= Html::a('Create new entry', ['create'], ['class' => 'btn btn-primary btn-sm']) ?>
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
<div class="tbl-lost-sales-module-index">


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
                  $url ='index.php?r=lost-sales/update&id='.$model->id;
                  return $url;
                }
                if ($action === 'view') {
                  $url ='index.php?r=lost-sales/view&id='.$model->id;
                  return $url;
                }
                if ($action === 'delete') {
                  $url ='index.php?r=lost-sales/delete&id='.$model->id;
                  return $url;
                }
              }
            ],
            // 'id',
            // 'prepared_by',
            [
              'attribute' => 'prepared_by',
              // 'label' => 'Approved By',
              'value' => function($dataProvider){
                $getprepby = TblEmployee::find()->where(['id'=>$dataProvider->prepared_by])->one();
                if(isset($getprepby) && $getprepby->name != ""){
                  return $getprepby->name;
                }else{
                  return "(not set)";
                }
              },
              'filter' => Html::activeDropDownList($searchModel, 'prepared_by', $emparr,['class'=>'form-select','prompt' => 'Select']),
            ],
            'date',
            // 'ip',
            // 'status',
            //'crt_by',
            //'mod_by',
            //'crt_time',
            //'mod_time',
            // 'fk_product_id',
            [
              'attribute' => 'fk_product_id',
              'value' => function($dataProvider){
                $getprod = TblProduct::find()->where(['id'=>$dataProvider->fk_product_id])->one();
                if(isset($getprod) && $getprod->name != ""){
                  return $getprod->name.' ['.$getprod->sku.']';
                }else{
                  return "(not set)";
                }
              },
            ],
            'non_product',
            'description:ntext',
            // [
            //     'class' => ActionColumn::className(),
            //     'urlCreator' => function ($action, TblLostSalesModule $model, $key, $index, $column) {
            //         return Url::toRoute([$action, 'id' => $model->id]);
            //      }
            // ],
        ],
    ]); ?>


</div>
</div>
</div>
