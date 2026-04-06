<?php

use app\models\TblProductReceiving;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use app\models\TblProduct;
use app\models\TblVendor;
use app\models\TblVendorInvoice;
use app\models\TblBin;
use app\models\TblBinArea;
use app\models\TblBinBay;
use app\models\TblBinLevel;
use app\models\TblBinPosition;
use app\models\TblBinRow;
use app\models\TblLocation;
use app\models\TblBinLocations;
use app\models\TblOwnershipCompany;
use yii\helpers\ArrayHelper;
/** @var yii\web\View $this */
/** @var app\models\TblProductReceivingSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Items Receiving';
$this->params['breadcrumbs'][] = $this->title;
$template = '{view}';
use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>59,'status'=>1])->one();
// if(isset($menuaccess) && $menuaccess->edit_crud == 1){
//   $template .= ' {edit}';
// }
// if(isset($menuaccess) && $menuaccess->delete_crud == 1){
//   $template .= ' {delete}';
// }
// $all_locations = TblLocation::find()->where(['status'=>1])->all();
// $locationarr = ArrayHelper::map($all_locations,'id','title');

$allbillfrom = TblOwnershipCompany::find()->where(['status'=>1])->orderBy(['company_name'=>SORT_ASC])->all();
$locationarr = ArrayHelper::map($allbillfrom,'id','location_name');

//-----get products --------
$allbins = TblBinLocations::find()->where(['status'=>1])->all();
// $truckarr = ArrayHelper::map($alltrucks,'id','unit_no');
$binarr = ArrayHelper::map($allbins, 'id', function($model) {
  // return $model->unit_no . ' | Plate No. ' . $model->plate;
  return $model->area.' - '.$model->row.' - '.$model->bay.' - '.$model->level.' - '.$model->position;
});
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
        <?= Html::a('Create Item Receving', ['create'], ['class' => 'btn btn-primary btn-sm']) ?>

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


    <div class="tbl-product-receiving-index table-responsive">
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
                $url ='index.php?r=product-receiving/update&id='.$model->id;
                return $url;
              }
              if ($action === 'view') {
                $url ='index.php?r=product-receiving/view&id='.$model->id;
                return $url;
              }
              if ($action === 'delete') {
                $url ='index.php?r=product-receiving/delete&id='.$model->id;
                return $url;
              }
            }
          ],
          // 'id',
          // 'fk_product_id',
          // [
          //   'attribute' => 'fk_product_id',
          //   'value' => function($dataProvider){
          //     $getprod = TblProduct::find()->where(['id'=>$dataProvider->fk_product_id,'status'=>1])->one();
          //     if(isset($getprod) && $getprod->name != ""){
          //       return $getprod->name.' ['.$getprod->sku.']';
          //     }else{
          //       return "(not set)";
          //     }
          //   }
          // ],
          [
    'attribute' => 'product',
    'value' => function($dataProvider) {
        return $dataProvider->product ? $dataProvider->product->name . ' (' . $dataProvider->product->internal_sku . ')' : null;
    },
    'label' => 'Item',
],
          // 'fk_vendor_invoice_id',
          // [
          //   'attribute' => 'fk_vendor_invoice_id',
          //   'value' => function($dataProvider){
          //     $getveninv = TblVendorInvoice::find()->where(['id'=>$dataProvider->fk_vendor_invoice_id,'status'=>1])->one();
          //     if(isset($getveninv) && $getveninv->vendor_invoice_number != ""){
          //       return $getveninv->vendor_invoice_number;
          //     }else{
          //       return "(not set)";
          //     }
          //   }
          // ],
          [
            'attribute' => 'vendorinvoice',
            'value' => 'vendorinvoice.vendor_invoice_number'
          ],
          [
            'attribute' => 'vendor',
            'value' => 'vendor.company_name'
          ],
          'no_of_items',
          'price_per_item',
          // 'fk_bin_id',
          // [
          //   'attribute' => 'fk_bin_id',
          //   'label' => 'BIN (Area - Location - Bay - Level - Position)',
          //   'value' => function($dataProvider){
          //     $getbin = TblBinLocations::find()->where(['id'=>$dataProvider->fk_bin_id,'status'=>1])->one();
          //     if(isset($getbin) && $getbin->id != ""){
          //       return $getbin->area.' - '.$getbin->row.' - '.$getbin->bay.' - '.$getbin->level.' - '.$getbin->position;
          //     }else{
          //       return "(not set)";
          //     }
          //   }
          // ],
          'bin_location',
          // [
          //     'attribute' => 'bin',
          //     'value' => function($dataProvider) {
          //         return $dataProvider->bin ? $dataProvider->bin->area . ' - ' . $dataProvider->bin->row . ' - '.$dataProvider->bin->bay.' - '.$dataProvider->bin->level.' - '.$dataProvider->bin->position : null;
          //     },
          //     'label' => 'BIN (Area - Row - Bay - Level - Position)',
          // ],
          [
            'attribute' => 'fk_location_id',
            'value' => function($dataProvider){
              $getveninv = TblOwnershipCompany::find()->where(['id'=>$dataProvider->fk_location_id,'status'=>1])->one();
              if(isset($getveninv) && $getveninv->location_name != ""){
                return $getveninv->location_name;
              }else{
                return "(not set)";
              }
            },
            'filter' => Html::activeDropDownList($searchModel, 'fk_location_id', $locationarr,['class'=>'form-control','prompt' => 'Select']),
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
          //     'urlCreator' => function ($action, TblProductReceiving $model, $key, $index, $column) {
          //         return Url::toRoute([$action, 'id' => $model->id]);
          //      }
          // ],
        ],
      ]); ?>


    </div>
  </div>
</div>
