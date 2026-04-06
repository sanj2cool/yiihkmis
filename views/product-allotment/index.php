<?php

use app\models\TblProductAllotment;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use app\models\TblProduct;
use app\models\TblEmployee;
use app\models\TblAllotmentStatus;
use yii\helpers\ArrayHelper;
/** @var yii\web\View $this */
/** @var app\models\TblProductAllotmentSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Item Allotments';
$this->params['breadcrumbs'][] = $this->title;
$template = '{edit} {delete}';


//-----get employees------------
$allemps = TblEmployee::find()->where(['status'=>1])->all();
$emparr = ArrayHelper::map($allemps,'id','name');

//-----get Status------------
$allstatus = TblAllotmentStatus::find()->where(['status'=>1])->all();
$statusarr = ArrayHelper::map($allstatus,'id','title');
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

        <?= Html::a('Create Item Allotment', ['create'], ['class' => 'btn btn-danger']) ?>

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
<div class="tbl-product-allotment-index table-responsive">


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
                      'class' => 'text-success'
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
                    $url ='index.php?r=product-allotment/update&id='.$model->id;
                    return $url;
                  }
                  if ($action === 'view') {
                    $url ='index.php?r=product-allotment/view&id='.$model->id;
                    return $url;
                  }
                  if ($action === 'delete') {
                    $url ='index.php?r=product-allotment/delete&id='.$model->id;
                    return $url;
                  }
                }
              ],
            // 'id',
            // 'fk_product_id',
            [
          'attribute' => 'fk_product_id',
          'value' => function($dataProvider){
            $getprod = TblProduct::find()->where(['id'=>$dataProvider->fk_product_id,'status'=>1])->one();
            if(isset($getprod) && $getprod->name != ""){
              return $getprod->name.' ['.$getprod->sku.']';
            }else{
              return "(not set)";
            }
          }
        ],
            // 'fk_vendor_invoice_id',
            'no_of_items',
            'allotment_date',
            // 'allotted_by',
            [
              'attribute' => 'allotted_by',
              'value' => function($dataProvider){
                $getprepby = TblEmployee::find()->where(['id'=>$dataProvider->allotted_by])->one();
                if(isset($getprepby) && $getprepby->name != ""){
                  return $getprepby->name;
                }else{
                  return "(not set)";
                }
              }
            ],
            // 'fk_allotment_status_id',
            [
              'attribute' => 'fk_allotment_status_id',
              'value' => function($dataProvider){
                $getprepby = TblAllotmentStatus::find()->where(['id'=>$dataProvider->fk_allotment_status_id])->one();
                if(isset($getprepby) && $getprepby->title != ""){
                  return $getprepby->title;
                }else{
                  return "(not set)";
                }
              }
            ],
            //'remarks:ntext',
            //'ip',
            //'status',
            //'crt_by',
            //'mod_by',
            //'crt_time',
            //'mod_time',
            // [
            //     'class' => ActionColumn::className(),
            //     'urlCreator' => function ($action, TblProductAllotment $model, $key, $index, $column) {
            //         return Url::toRoute([$action, 'id' => $model->id]);
            //      }
            // ],
        ],
    ]); ?>


</div>
</div>
</div>
