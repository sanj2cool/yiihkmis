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
          <?= \app\components\CurrencyConverter::widget([
    'amount' => 0
]); ?>
        </h3>
      </div>
      <div class="col-lg-6 text-end">
        <?= Html::a('Reset Filters', ['reset-filters'], ['class' => 'btn btn-warning btn-sm']) ?>

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
          [
    'label' => 'Cost Per Product',
    'format' => 'raw',
    'value' => function ($model) {

        return '<span class="cost-per-item"
            data-price="'.$model->price_per_item.'"
            data-qty="'.$model->no_of_items.'"
            data-shipping="'.($model->shipping ?? 0).'"
            data-other="'.($model->other_charges ?? 0).'"
            data-custom="'.($model->custom_charges ?? 0).'"
            data-usa="'.($model->vendor && $model->vendor->is_usa ? 1 : 0).'"
        ></span>';
    }
],
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
          [
    'attribute' => 'shipping',
    'format' => 'raw',
    'value' => function($model) {
        return '<input type="text" class="form-control shipping-input" 
                data-id="'.$model->id.'" 
                value="'.$model->shipping.'">';
    }
],
[
    'attribute' => 'other_charges',
    'format' => 'raw',
    'value' => function($model) {
        return '<input type="text" class="form-control other-input" 
                data-id="'.$model->id.'" 
                value="'.$model->other_charges.'">';
    }
],
[
    'attribute' => 'custom_charges',
    'format' => 'raw',
    'value' => function($model) {
        return '<input type="text" class="form-control custom-input" 
                data-id="'.$model->id.'" 
                value="'.$model->custom_charges.'">';
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
          //     'urlCreator' => function ($action, TblProductReceiving $model, $key, $index, $column) {
          //         return Url::toRoute([$action, 'id' => $model->id]);
          //      }
          // ],
        ],
      ]); ?>


    </div>
  </div>
</div>


<?php 
$js = <<<JS

function calculateCosts() {

    let rate = parseFloat($('.js-currency .cc-rate').val()) || 1;
    let toggle = $('.js-currency .cc-toggle').is(':checked');

    $('.cost-per-item').each(function () {

        
        let price = parseFloat($(this).attr('data-price')) || 0;
    let qty   = parseFloat($(this).attr('data-qty')) || 1;
          let isUSA = parseInt($(this).attr('data-usa')) || 0;
        let ship  = parseFloat($(this).attr('data-shipping')) || 0;
let other = parseFloat($(this).attr('data-other')) || 0;
let custom= parseFloat($(this).attr('data-custom')) || 0;

        let product_total = price * qty;

        if (isUSA ) {
            product_total = product_total * rate;
        }

        let final = product_total + ship + other + custom;
        let cost = final / qty;

        $(this).text(cost.toFixed(2));
    });
}

// ✅ initial load
$(window).on('load', function () {
    calculateCosts();
});

// ✅ when rate changes
$(document).on('input keyup change', '.js-currency .cc-rate', function () {
    calculateCosts();
});

// ✅ when toggle changes
$(document).on('change', '.js-currency .cc-toggle', function () {
    //calculateCosts();
});

// ✅ if using PJAX
$(document).on('pjax:end', function () {
    calculateCosts();
});


$(document).on('change', '.shipping-input, .other-input, .custom-input', function() {

    var input = $(this);
    var id = input.data('id');
    var value = input.val();

    let field = '';

    if (input.hasClass('shipping-input')) field = 'shipping';
    if (input.hasClass('other-input')) field = 'other_charges';
    if (input.hasClass('custom-input')) field = 'custom_charges';

    $.post("index.php?r=product-receiving/update-field", {
        id: id,
        field: field,
        value: value,
        _csrf: yii.getCsrfToken()
    })
    .done(function(res){

        if(res.success){

            let attr = field.replace('_charges', '');

            input.closest('tr').find('.cost-per-item')
                .attr('data-' + attr, value);

            calculateCosts();
        }
    });
});


JS;

$this->registerJs($js);
