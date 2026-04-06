<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\TblProduct;
use app\models\TblLocation;
use app\models\TblVendor;
use app\models\TblVendorInvoice;
use app\models\TblBin;
use app\models\TblBinArea;
use app\models\TblBinBay;
use app\models\TblBinLevel;
use app\models\TblBinPosition;
use app\models\TblBinRow;
use app\models\TblBinLocations;
use app\models\TblOwnershipCompany;
use yii\helpers\ArrayHelper;
/** @var yii\web\View $this */
/** @var app\models\TblProductReceiving $model */
/** @var yii\widgets\ActiveForm $form */
$bin_arr = [];
//-----get products --------
$allprods = TblProduct::find()->where(['status'=>1])->all();
// $truckarr = ArrayHelper::map($alltrucks,'id','unit_no');
$prodarr = ArrayHelper::map($allprods, 'id', function($model) {
  // return $model->unit_no . ' | Plate No. ' . $model->plate;
  return $model->name.' ['.$model->sku.']';
});
//-----get invoice vendors --------
$allinvoices = TblVendorInvoice::find()->where(['status'=>1])->all();
$invoicearr = ArrayHelper::map($allinvoices, 'id', function($model) {
  $getvendor = TblVendor::find()->where(['id'=>$model->fk_vendor_id,'status'=>1])->one();
  if(isset($getvendor) && $getvendor->company_name != ""){
      return $model->vendor_invoice_number.' ['.$getvendor->company_name.']';
  }else{
      return $model->vendor_invoice_number;
  }
});
//-----get bins --------
// $allbins = TblBin::find()->where(['status'=>1])->all();
// $binarr = ArrayHelper::map($allbins, 'id', function($model) {
//   //-------get area ------
//   $getarea = TblBinArea::find()->where(['status'=>1,'id'=>$model->fk_area_id])->one();
//   if(isset($getarea) && $getarea->title != ""){
//     $area = $getarea->title;
//   }else{
//     $area = "";
//   }
//   //---------get row ------
//   $getrow = TblBinRow::find()->where(['status'=>1,'id'=>$model->fk_row_id])->one();
//   if(isset($getrow) && $getrow->title != ""){
//     $row = $getrow->title;
//   }else{
//     $row = "";
//   }
//   //---------get bay -------
//   $getbay = TblBinBay::find()->where(['status'=>1,'id'=>$model->fk_bay_id])->one();
//   if(isset($getbay) && $getbay->title != ""){
//     $bay = $getbay->title;
//   }else{
//     $bay = "";
//   }
//   //---------get level -------
//   $getlevel = TblBinLevel::find()->where(['status'=>1,'id'=>$model->fk_level_id])->one();
//   if(isset($getlevel) && $getlevel->title != ""){
//     $level = $getlevel->title;
//   }else{
//     $level = "";
//   }
//   //---------get position -------
//   $getposition = TblBinPosition::find()->where(['status'=>1,'id'=>$model->fk_position_id])->one();
//   if(isset($getposition) && $getposition->title != ""){
//     $position = $getposition->title;
//   }else{
//     $position = "";
//   }
//   return $area.' - '.$row.' - '.$bay.' - '.$level.' - '.$position;
// });



//-----get products --------
$allbins = TblBinLocations::find()->where(['status'=>1])->all();
// $truckarr = ArrayHelper::map($alltrucks,'id','unit_no');
$binarr = ArrayHelper::map($allbins, 'id', function($model) {
  // return $model->unit_no . ' | Plate No. ' . $model->plate;
  return $model->area.' - '.$model->row.' - '.$model->bay.' - '.$model->level.' - '.$model->position;
});

// $all_locations = TblLocation::find()->where(['status'=>1])->all();
// $locationarr = ArrayHelper::map($all_locations,'id','title');

$allbillfrom = TblOwnershipCompany::find()->where(['status'=>1])->orderBy(['company_name'=>SORT_ASC])->all();
$locationarr = ArrayHelper::map($allbillfrom,'id','location_name');

$allvendors = TblVendor::find()->where('status != 0')->orderBy(['company_name'=>SORT_ASC])->all();
$vendorarr = ArrayHelper::map($allvendors,'id','company_name');
?>

<div class="tbl-product-receiving-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="card shadow rounded mt-2">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-lg-3">
            <?= $form->field($model, 'fk_vendor_invoice_id')->dropDownList($invoicearr,['prompt'=>'Select']) ?>
          </div>
          <div class="col-lg-3">
            <?= $form->field($model, 'fk_vendor_id')->dropDownList($vendorarr,['prompt'=>'Select','data-choices'=>'data-choices','data-options'=>'{"removeItemButton":true,"placeholder":true}']) ?>
          </div>
          <div class="col-lg-3">
            <?= $form->field($model, 'fk_product_id')->dropDownList($prodarr,['prompt'=>'Select']) ?>
          </div>

          <div class="col-lg-3">
            <?= $form->field($model, 'no_of_items')->textInput(['onkeyup'=>'checkNum(this);']) ?>
          </div>
        </div>
        <div class="row mt-2">
          <div class="col-lg-4">
            <?= $form->field($model, 'price_per_item')->textInput(['class'=>'form-control floatNumberField','onkeyup'=>'checkDec(this);']) ?>
          </div>
          <div class="col-lg-4">
            <?= $form->field($model, 'fk_bin_id')->dropDownList($binarr,['prompt'=>'Select']) ?>
          </div>
          <div class="col-lg-4">
            <?= $form->field($model, 'fk_location_id')->dropDownList($locationarr,['prompt'=>'Select'])->label('Location <sup><span class="badge bg-primary">New</span></sup>') ?>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow rounded mt-2">
      <div class="card-body p-3">
            <?= $form->field($model, 'remarks')->textarea(['rows' => 6]) ?>
      </div>
    </div>

    <div class="card shadow rounded p-3 mt-4 mb-4">
        <div class="row text-center">
          <?php
          if($model->isNewRecord){
            ?>
            <div class="d-grid gap-2 col-4 mx-auto pe-1">
              <input type="submit" name="new_update" value="Create & Edit" class="btn btn-primary btn-sm"/>
            </div>
            <div class="d-grid gap-2 col-4 mx-auto pe-1 ps-1">
              <input type="submit" name="new_new" value="Create & New" class="btn btn-info btn-sm"/>
            </div>
            <div class="d-grid gap-2 col-4 mx-auto ps-1">
              <input type="submit" name="new_exit" value="Create & Exit" class="btn btn-secondary btn-sm"/>
            </div>
            <?php
          }else{
            ?>
            <div class="d-grid gap-2 col-4 mx-auto pe-1">
              <input type="submit" name="update" value="Update & Edit" class="btn btn-primary btn-sm"/>
            </div>
            <div class="d-grid gap-2 col-4 mx-auto pe-1 ps-1">
              <input type="submit" name="new" value="Update & New" class="btn btn-info btn-sm"/>
            </div>
            <div class="d-grid gap-2 col-4 mx-auto ps-1">
              <input type="submit" name="exit" value="Update & Exit" class="btn btn-secondary btn-sm"/>
            </div>
            <?php
          }
          ?>

        </div>
      </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs('
  // Initialize Choices.js
  //--------------create client code starts -----------------------
  const element = document.getElementById("tblproductreceiving-fk_bin_id");
  const choices = new Choices(element, {
    removeItemButton: true,
    placeholder: true
  });

  //--------------create client code starts -----------------------
  const element2 = document.getElementById("tblproductreceiving-fk_product_id");
  const choices2 = new Choices(element2, {
    removeItemButton: true,
    placeholder: true
  });

  //--------------create client code starts -----------------------
  const element3 = document.getElementById("tblproductreceiving-fk_location_id");
  const choices3 = new Choices(element3, {
    removeItemButton: true,
    placeholder: true
  });

  //--------------create client code starts -----------------------
  const element4 = document.getElementById("tblproductreceiving-fk_vendor_invoice_id");
  const choices4 = new Choices(element4, {
    removeItemButton: true,
    placeholder: true
  });


');
 ?>
