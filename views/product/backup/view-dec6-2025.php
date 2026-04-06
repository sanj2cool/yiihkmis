<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\db\Query;
use app\models\TblProductCategory;
use app\models\TblProduct;
use app\models\TblProductCrossRef;
use app\models\TblProductAlternate;
use app\models\TblCycleCountCategory;
use app\models\TblProductImage;
use app\models\TblProductRelated;
use app\models\TblProductCompetition;
use app\models\TblVendor;
use app\models\TblUser;
use app\models\TblVendorInvoice;
use app\models\TblProductReceiving;
use app\models\TblBinLocations;
use app\models\TblInvoice;
use app\models\TblInvoiceItem;
use app\models\TblProductBrand;
use app\models\TblBrand;

/** @var yii\web\View $this */
/** @var app\models\TblProduct $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

// $query = (new Query())
//   ->select([
//       'product_id' => 'p.id',
//       'current_qty' => 'IFNULL(SUM(pr.no_of_items), 0) - IFNULL(SUM(pa.no_of_items), 0)'
//   ])
//   ->from('tbl_product p')
//   ->leftJoin('tbl_product_receiving pr', 'p.id = pr.fk_product_id')
//   ->leftJoin('tbl_product_allotment pa', 'p.id = pa.fk_product_id')
//   ->where(['p.id' => $model->id])
//   ->groupBy('p.id')
//   ->one();
//
//   SELECT p.id, IFNULL(SUM(pr.no_of_items), 0) - IFNULL(SUM(it.quantity), 0) as current_qty from tbl_product AS p
// left join tbl_product_receiving pr on p.id = pr.fk_product_id and pr.status = 1
// left join tbl_invoice_item it on p.id = it.fk_product_id  and it.status = 1
// where p.status != 0
// group by p.id;

$session = Yii::$app->session;
$user_company = $session['userCompany'];

$query = (new Query())
->select([
  'p.id',
  '(IFNULL((SELECT SUM(pr.no_of_items)
  FROM tbl_product_receiving pr
  WHERE pr.fk_product_id = p.id AND pr.status = 1 and pr.fk_location_id = '.$user_company.'), 0)
  - IFNULL((SELECT SUM(it.quantity)
  FROM tbl_invoice_item it
  WHERE it.fk_product_id = p.id AND it.status = 1 AND it.fk_invoice_id IN (SELECT id FROM tbl_invoice WHERE status = 1 and fk_bill_from_id = '.$user_company.')), 0)) as current_qty'
])
->from('tbl_product p')
->where(['p.status' => 1, 'p.id' => $model->id])
->one();

// print_r($query);
if(isset($query['current_qty']) && $query['current_qty'] != ""){
  $current_qty = $query['current_qty'];
}else{
  $current_qty = 0;
}

use yii\helpers\Url;

//------get the recent most cost price from product receving --------
$getreceived_recent = TblProductReceiving::find()->where(['status'=>1,'fk_product_id'=>$model->id])->orderBy(['id'=>SORT_DESC])->one();
if(isset($getreceived_recent) && $getreceived_recent->id != ""){
  $cost_price = $getreceived_recent->price_per_item;
  $tier1_price = 0.00;
  $tier2_price = 0.00;
  $tier3_price = 0.00;
  $tier4_price = 0.00;
  $tier5_price = 0.00;

  $tier6_price = 0.00;
  $tier7_price = 0.00;
  $tier8_price = 0.00;
  $tier9_price = 0.00;
  $tier10_price = 0.00;
  $tier11_price = 0.00;
  //-------tier 1 price -------
  if($model->tier_1_markup != ""){
    $tier1_price = $cost_price+($cost_price*($model->tier_1_markup/100));
  }
  //-------tier 2 price -------
  if($model->tier_2_markup != ""){
    $tier2_price = $cost_price+($cost_price*($model->tier_2_markup/100));
  }
  //-------tier 3 price -------
  if($model->tier_3_markup != ""){
    $tier3_price = $cost_price+($cost_price*($model->tier_3_markup/100));
  }
  //-------tier 4 price -------
  if($model->tier_4_markup != ""){
    $tier4_price = $cost_price+($cost_price*($model->tier_4_markup/100));
  }
  //-------tier 5 price -------
  if($model->tier_5_markup != ""){
    $tier5_price = $cost_price+($cost_price*($model->tier_5_markup/100));
  }
  if($model->tier_6_markup != ""){
    $tier6_price = $cost_price+($cost_price*($model->tier_6_markup/100));
  }
  if($model->tier_7_markup != ""){
    $tier7_price = $cost_price+($cost_price*($model->tier_7_markup/100));
  }
  if($model->tier_8_markup != ""){
    $tier8_price = $cost_price+($cost_price*($model->tier_8_markup/100));
  }
  if($model->tier_9_markup != ""){
    $tier9_price = $cost_price+($cost_price*($model->tier_9_markup/100));
  }
  if($model->tier_10_markup != ""){
    $tier10_price = $cost_price+($cost_price*($model->tier_10_markup/100));
  }
  if($model->tier_11_markup != ""){
    $tier11_price = $cost_price+($cost_price*($model->tier_11_markup/100));
  }
}else{
  $cost_price = 0;
  $tier1_price = 0.00;
  $tier2_price = 0.00;
  $tier3_price = 0.00;
  $tier4_price = 0.00;
  $tier5_price = 0.00;
  $tier6_price = 0.00;
  $tier7_price = 0.00;
  $tier8_price = 0.00;
  $tier9_price = 0.00;
  $tier10_price = 0.00;
  $tier11_price = 0.00;
}

$getselectedbrands = TblProductBrand::find()->where(['fk_product_id'=>$model->id,'status'=>1])->all();
$brands_str = "";
if(isset($getselectedbrands) && count($getselectedbrands) > 0){
  foreach($getselectedbrands as $gs){
    $getbtitle = TblBrand::find()->where(['id'=>$gs->fk_brand_id,'status'=>1])->one();
    if(isset($getbtitle) && $getbtitle->title != ""){
      if($brands_str == ""){
        $brands_str .= $getbtitle->title;
      }else{
        $brands_str .= ", ".$getbtitle->title;
      }
    }
  }//---for loop ended----
}//---if isset ended-----


use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>19,'status'=>1])->one();

?>

<div class="card shadow rounded mt-2">
  <div class="card-body p-3">
    <div class="row">
      <div class="col-lg-6">
        <h3><?= Html::encode($this->title) ?></h3>
      </div>
      <div class="col-lg-6 text-end">
        <?php
        if(isset($menuaccess) && $menuaccess->edit_crud == 1){
          ?>
          <?= Html::a('<i class="fas fa-pen"></i> Update', ['update', 'id' => $model->id], ['class' => 'btn btn-info btn-sm']) ?>
          <?php
        }
        ?>

        <?php
        if(isset($menuaccess) && $menuaccess->delete_crud == 1){
          ?>
          <?= Html::a('<i class="fas fa-trash"></i> Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger btn-sm',
            'data' => [
              'confirm' => 'Are you sure you want to delete this item?',
              'method' => 'post',
            ],
            ]) ?>
            <?php

          }
          ?>

          <?= Html::a('<i class="fas fa-chevron-left"></i> Back to List', ['index'], ['class' => 'btn btn-secondary btn-sm']) ?>
        </div>
      </div>
    </div>
  </div>

  <div class="tbl-product-view mb-3">

    <div class="card shadow rounded mt-2">
      <div class="card-header p-3 border-bottom border-300 bg-primary-subtle">
        <div class="row g-3 justify-content-between align-items-center">
          <div class="col-6 col-md">
            <h4 class="text-900 mb-0" data-anchor="data-anchor" id="basic-details">Basic Details</h4>
          </div>
          <div class="col-6 text-end">
            <h4 class="text-danger">Current Qty in Stock: <?=$current_qty?></h4>
          </div>
        </div>
      </div>
      <div class="card-body p-3 table-responsive">
        <table class="table table-bordered table-hover table-sm">
          <tr>
            <td>
              <strong>Name:</strong>
              <p>
                <?=$model->name?>
              </p>
            </td>

            <td>
              <strong>SKU:</strong>
              <p>
                <?=$model->internal_sku?>
              </p>
            </td>
            <td colspan="2">
              <strong>Brands:</strong>
              <p>
                <?=$brands_str?>
              </p>
            </td>
            <td>
              <strong>Category:</strong>
              <p>
                <?php
                $getcat = TblProductCategory::find()->where(['id'=>$model->fk_category_id,'status'=>1])->one();
                if(isset($getcat) && $getcat->title != ""){
                  echo $getcat->title;
                }else{
                  echo "(not set)";
                }
                ?>
              </p>
            </td>
            <td>
              <strong>Cycle Count Category:</strong>
              <p>
                <?php
                $getcatcycle = TblCycleCountCategory::find()->where(['id'=>$model->fk_cycle_count_category_id,'status'=>1])->one();
                if(isset($getcatcycle) && $getcatcycle->title != ""){
                  echo $getcatcycle->title;
                }else{
                  echo "(not set)";
                }
                ?>
              </p>
            </td>
            <td>
              <strong>Status:</strong>
              <p>
                <?php
                if($model->status == 1){
                  echo "Active";
                }else if($model->status == 2){
                  echo "Inactive";
                }else{
                  echo "(not set)";
                }
                ?>
              </p>
            </td>

          </tr>
          <tr>
            <td>
              <strong>Assembly Category:</strong>
              <p>
                <?=$model->assemblycat ? $model->assemblycat->title : '(not set)'?>
              </p>
            </td>
            <td>
              <strong>Reorder Qty:</strong>
              <p>
                <?=$model->quantity_in_stock?>
              </p>
            </td>
            <td>
              <strong>Max Qty in Stock:</strong>
              <p>
                <?php
                echo $model->max_qty_in_stock
                ?>
              </p>
            </td>

            <td>
              <strong>Show on Website:</strong>
              <p>
                <?php
                if($model->show_on_website == 1){
                  echo "Yes";
                }else if($model->show_on_website == 2){
                  echo "No";
                }else{
                  echo "(not set)";
                }
                ?>
              </p>
            </td>
            <td>
              <strong>Description:</strong>
              <p>
                <?=$model->description?>
              </p>
            </td>
            <td>
              <strong>Website/Invoice Description</strong>
              <p>
                <?=$model->website_description?>
              </p>
            </td>
            <td>
              <strong>Default Pricing Tier</strong>
              <p>
                <?php
                if($model->default_tier == 1){
                  echo "Tier 1";
                }else if($model->default_tier == 2){
                  echo "Tier 2";
                }else if($model->default_tier == 3){
                  echo "Tier 3";
                }else if($model->default_tier == 4){
                  echo "Tier 4";
                }else if($model->default_tier == 5){
                  echo "Tier 5";
                }else if($model->default_tier == 6){
                  echo "Tier 6";
                }else if($model->default_tier == 7){
                  echo "Tier 7";
                }else if($model->default_tier == 8){
                  echo "Tier 8";
                }else if($model->default_tier == 9){
                  echo "Tier 9";
                }else if($model->default_tier == 10){
                  echo "Tier 10";
                }else if($model->default_tier == 11){
                  echo "Tier 11";
                }else{
                  echo "(not set)";
                }
                ?>
              </p>
            </td>

          </tr>
        </table>
      </div><!-- card body ended-->
    </div><!-- card ended -->


    <div class="card shadow rounded mt-2">
      <div class="card-header p-3 border-bottom border-300 bg-success-subtle">
        <div class="row g-3 justify-content-between align-items-center">
          <div class="col-12 col-md">
            <h4 class="text-900 mb-0" data-anchor="data-anchor" id="basic-details">Selling Price Details</h4>
          </div>
        </div>
      </div>
      <div class="card-body p-3 table-responsive">
        <table class="table table-bordered table-sm">
          <tr>
            <td>
              <strong>Tier 1 Pricing</strong>
              <p>
                <?=number_format($tier1_price,2,'.','')?> (<?=$model->tier_1_markup?>%)
              </p>
            </td>
            <td>
              <strong>Tier 2 Pricing</strong>
              <p>
                <?=number_format($tier2_price,2,'.','')?> (<?=$model->tier_2_markup?>%)
              </p>
            </td>
            <td>
              <strong>Tier 3 Pricing</strong>
              <p>
                <?=number_format($tier3_price,2,'.','')?> (<?=$model->tier_3_markup?>%)
              </p>
            </td>
            <td>
              <strong>Tier 4 Pricing</strong>
              <p>
                <?=number_format($tier4_price,2,'.','')?> (<?=$model->tier_4_markup?>%)
              </p>
            </td>
            <td>
              <strong>Tier 5 Pricing</strong>
              <p>
                <?=number_format($tier5_price,2,'.','')?> (<?=$model->tier_5_markup?>%)
              </p>
            </td>
            <td>
              <strong>Tier 6 Pricing</strong>
              <p>
                <?=number_format($tier6_price,2,'.','')?> (<?=$model->tier_6_markup?>%)
              </p>
            </td>
          </tr>
          <tr>

            <td>
              <strong>Tier 7 Pricing</strong>
              <p>
                <?=number_format($tier7_price,2,'.','')?> (<?=$model->tier_7_markup?>%)
              </p>
            </td>
            <td>
              <strong>Tier 8 Pricing</strong>
              <p>
                <?=number_format($tier8_price,2,'.','')?> (<?=$model->tier_8_markup?>%)
              </p>
            </td>
            <td>
              <strong>Tier 9 Pricing</strong>
              <p>
                <?=number_format($tier9_price,2,'.','')?> (<?=$model->tier_9_markup?>%)
              </p>
            </td>
            <td>
              <strong>Tier 10 Pricing</strong>
              <p>
                <?=number_format($tier10_price,2,'.','')?> (<?=$model->tier_10_markup?>%)
              </p>
            </td>
            <td colspan="2">
              <strong>Tier 11 Pricing</strong>
              <p>
                <?=number_format($tier11_price,2,'.','')?> (<?=$model->tier_11_markup?>%)
              </p>
            </td>
          </tr>
        </table>
      </div>
    </div><!-- markup card ended -->

    <div class="row">
      <div class="col-lg-6">
        <div class="card shadow rounded mt-2">
          <div class="card-header p-3 border-bottom border-300 bg-danger-subtle">
            <div class="row g-3 justify-content-between align-items-center">
              <div class="col-12 col-md">
                <h4 class="text-900 mb-0" data-anchor="data-anchor" id="basic-details">Cross Reference</h4>
              </div>

            </div>
          </div>
          <div class="card-body p-3 table-responsive">
            <table class="table table-bordered table-hover table-sm">
              <thead>
                <tr>
                  <th>
                    Item Name
                  </th>
                </tr>
              </thead>
              <tbody>
                <?php
                $getcrossrefs = TblProductCrossRef::find()->where(['status'=>1,'fk_product_id'=>$model->id])->all();
                if(isset($getcrossrefs) && count($getcrossrefs) > 0){
                  foreach($getcrossrefs as $gc){
                    echo '<tr>
                    <td>
                    '.$gc->cross_ref.'
                    </td>
                    </tr>';
                  }
                }else{
                  echo '<tr>
                  <td>
                  No cross references found.
                  </td>
                  </tr>';
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div><!-- cross reference col-6 ended -->
      <div class="col-lg-6">
        <div class="card shadow rounded mt-2">
          <div class="card-header p-3 border-bottom border-300 bg-danger-subtle">
            <div class="row g-3 justify-content-between align-items-center">
              <div class="col-12 col-md">
                <h4 class="text-900 mb-0" data-anchor="data-anchor" id="basic-details">Alternate Products</h4>
              </div>

            </div>
          </div>
          <div class="card-body p-3 table-responsive">
            <table class="table table-bordered table-hover table-sm">
              <thead>
                <tr>
                  <th>
                    Item Name
                  </th>
                </tr>
              </thead>
              <tbody>
                <?php
                $getaltproducts = TblProductAlternate::find()->where(['status'=>1,'fk_product_id'=>$model->id])->all();
                if(isset($getaltproducts) && count($getaltproducts) > 0){
                  foreach($getaltproducts as $gc){
                    $getproduct = TblProduct::find()->where(['id'=>$gc->fk_alt_product_id,'status'=>1])->one();
                    if(isset($getproduct) && $getproduct->id != ""){
                      echo '<tr>
                      <td>
                      '.$getproduct->name.' ['.$getproduct->sku.'] |
                      <a class="unformat text-sm" href="'.Url::to(['product/view','id'=>$gc->fk_alt_product_id]).'" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
                      </td>
                      </tr>';
                    }
                  }
                }else{
                  echo '<tr>
                  <td>
                  No alternate products found.
                  </td>
                  </tr>';
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div><!-- alt product col-6 ended -->
    </div>


    <div class="row">
      <div class="col-lg-6">
        <div class="card shadow rounded mt-2">
          <div class="card-header p-3 border-bottom border-300 bg-info-subtle">
            <div class="row g-3 justify-content-between align-items-center">
              <div class="col-12 col-md">
                <h4 class="text-900 mb-0" data-anchor="data-anchor" id="basic-details">Related Products</h4>
              </div>

            </div>
          </div>
          <div class="card-body p-3 table-responsive">
            <table class="table table-bordered table-hover table-sm">
              <thead>
                <tr>
                  <th>
                    Item Name
                  </th>
                </tr>
              </thead>
              <tbody>
                <?php
                $getrelatedproducts = TblProductRelated::find()->where(['status'=>1,'fk_product_id'=>$model->id])->all();
                if(isset($getrelatedproducts) && count($getrelatedproducts) > 0){
                  foreach($getrelatedproducts as $gc){
                    $getproduct = TblProduct::find()->where(['id'=>$gc->fk_related_product_id,'status'=>1])->one();
                    if(isset($getproduct) && $getproduct->id != ""){
                      echo '<tr>
                      <td>
                      '.$getproduct->name.' ['.$getproduct->sku.'] |
                      <a class="unformat text-sm" href="'.Url::to(['product/view','id'=>$gc->fk_related_product_id]).'" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
                      </td>
                      </tr>';
                    }
                  }
                }else{
                  echo '<tr>
                  <td>
                  No related products found.
                  </td>
                  </tr>';
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div><!-- related products col-6 ended -->
      <div class="col-lg-6">
        <div class="card shadow rounded mt-2">
          <div class="card-header p-3 border-bottom border-300 bg-info-subtle">
            <div class="row g-3 justify-content-between align-items-center">
              <div class="col-12 col-md">
                <h4 class="text-900 mb-0" data-anchor="data-anchor" id="basic-details">Competitor Pricing</h4>
              </div>

            </div>
          </div>
          <div class="card-body p-3 table-responsive">
            <table class="table table-bordered table-hover table-sm">
              <thead>
                <tr>
                  <th>Seller Name</th>
                  <th>Seller Pricing</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $getproductcompt = TblProductCompetition::find()->where(['status'=>1,'fk_product_id'=>$model->id])->all();
                if(isset($getproductcompt) && count($getproductcompt) > 0){
                  foreach($getproductcompt as $gc){
                    echo '<tr>
                    <td>'.$gc->seller_name.'</td>
                    <td>'.$gc->selling_price.'</td>
                    </tr>';
                  }
                }else{
                  echo '<tr>
                  <td colspan="2">
                  No competitor pricing found.
                  </td>
                  </tr>';
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div><!-- competitor pricing  col-6 ended -->
    </div><!-- row ended -->

    <div class="card shadow rounded mt-2">
      <div class="card-header p-3 border-bottom border-300 bg-secondary-subtle">
        <div class="row g-3 justify-content-between align-items-center">
          <div class="col-12 col-md">
            <h4 class="text-900 mb-0" data-anchor="data-anchor" id="basic-details">Item Received History</h4>
          </div>

        </div>
      </div>
      <div class="card-body p-3 table-responsive">
        <table class="table table-bordered table-sm">
          <thead>
            <tr>
              <th>Date</th>
              <th>Received By</th>
              <th>Vendor Invoice</th>
              <th>Vendor</th>
              <th>No. of Items</th>
              <th>Bin #</th>
              <th>Location</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $total_items_received = 0;
            $getproductsreceived = TblProductReceiving::find()->where(['status'=>1,'fk_product_id'=>$model->id,'fk_location_id'=>$user_company])->orderBy(['crt_time'=>SORT_DESC])->all();
            if(isset($getproductsreceived) && count($getproductsreceived) > 0){
              foreach($getproductsreceived as $rs){
                //-----prep by ----------
                $getprepby = TblUser::find()->where(['id'=>$rs->crt_by])->one();
                if(isset($getprepby) && $getprepby->alias != ""){
                  $prep_by = $getprepby->alias;
                }else{
                  $prep_by = "(not set)";
                }
                $getveninv = TblVendorInvoice::find()->where(['id'=>$rs->fk_vendor_invoice_id,'status'=>1])->one();
                if(isset($getveninv) && $getveninv->vendor_invoice_number != ""){
                  $vendor_invoice = $getveninv->vendor_invoice_number;
                  //=====get vendor ===================
                  $getvendor = TblVendor::find()->where(['id'=>$getveninv->fk_vendor_id,'status'=>1])->one();
                  if(isset($getvendor) && $getvendor->company_name != ""){
                    $vendor = $getvendor->company_name;
                  }else{
                    $vendor = "(not set)";
                  }
                }else if($rs->fk_vendor_id != ""){
                  $getvendor = TblVendor::find()->where(['id'=>$rs->fk_vendor_id,'status'=>1])->one();
                  if(isset($getvendor) && $getvendor->company_name != ""){
                    $vendor = $getvendor->company_name;
                  }else{
                    $vendor = "(not set)";
                  }
                  $vendor_invoice = "(not set)";
                }else{
                  $vendor_invoice = "(not set)";
                  $vendor = "(not set)";
                }
                $getbin = TblBinLocations::find()->where(['id'=>$rs->fk_bin_id,'status'=>1])->one();
                if(isset($getbin) && $getbin->id != ""){
                  //-------get area ------

                  $bin = $getbin->area.' - '.$getbin->row.' - '.$getbin->bay.' - '.$getbin->level.' - '.$getbin->position;
                }else{
                  $bin = "(not set)";
                }
                $getveninv = \app\models\TblOwnershipCompany::find()->where(['id'=>$rs->fk_location_id,'status'=>1])->one();
                if(isset($getveninv) && $getveninv->location_name != ""){
                  $location_name = $getveninv->location_name;
                }else{
                  $location_name = "(not set)";
                }
                $total_items_received += $rs->no_of_items;
                echo '<tr>
                <td>'.date('d M, Y',strtotime($rs->crt_time)).' |
                <a class="unformat text-sm" href="'.Url::to(['product-receiving/update','id'=>$rs->id]).'" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
                </td>
                <td>'.$prep_by.'</td>
                <td>'.$vendor_invoice.'</td>
                <td>'.$vendor.'</td>
                <td>'.$rs->no_of_items.'</td>
                <td>'.$bin.'</td>
                <td>'.$location_name.'</td>
                </tr>';
              }
            }else{
              echo "<tr>
              <td colspan='7'>No records yet.</td>
              </tr>";
            }
            ?>

          </tbody>
          <tfoot>
            <tr>
              <td colspan="4" align="right">
                Total Items:
              </td>
              <td>
                <?=$total_items_received?>
              </td>
              <td colspan="2">

              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div><!-- Card ended for item receiving -->


    <div class="card shadow rounded mt-2">
      <div class="card-header p-3 border-bottom border-300 bg-success-subtle">
        <div class="row g-3 justify-content-between align-items-center">
          <div class="col-12 col-md">
            <h4 class="text-900 mb-0" data-anchor="data-anchor" id="basic-details">Item Sales History</h4>
          </div>

        </div>
      </div>
      <div class="card-body p-3 table-responsive">
        <table class="table table-bordered table-sm">
          <thead>
            <tr>
              <th>Invoice #</th>
              <th>Invoice Date</th>
              <th>Client</th>
              <th>Qty</th>
              <th>Unit Price</th>
              <th>Total Price</th>
              <th>Location</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // SELECT i.id as invoice_id, i.invoice_number, it.quantity, it.unit_price, it.total_price, i.invoice_date,i.total_amount,c.company_name
            // FROM `tbl_invoice_item` AS it
            // left join tbl_invoice AS i on i.id = it.fk_invoice_id
            // left join tbl_client AS c on i.fk_client_id = c.id
            // where it.status = 1 and i.status = 1 and it.fk_product_id = 291;
            $total_qty_sold = 0;
            $saleshistory = (new Query())
            ->select([
              'i.id as invoice_id',
              'i.invoice_number',
              'it.quantity',
              'it.unit_price',
              'it.total_price',
              'i.invoice_date',
              'i.total_amount',
              'c.company_name',
              'i.fk_bill_from_id'
            ])
            ->from('tbl_invoice_item it')
            ->leftJoin('tbl_invoice AS i','i.id = it.fk_invoice_id')
            ->leftJoin('tbl_client AS c','i.fk_client_id = c.id')
            ->where(['it.status' => 1, 'i.status'=>1,'it.fk_product_id' => $model->id])
            ->andWhere(['i.fk_bill_from_id'=>$user_company])
            ->orderBy(['i.id'=>SORT_DESC])
            ->all();
            // print_r($saleshistory);
            if(count($saleshistory) > 0){
              for($s = 0; $s < count($saleshistory); $s++){
                $total_qty_sold += $saleshistory[$s]['quantity'];
                // fk_bill_from_id
                $getveninv = \app\models\TblOwnershipCompany::find()->where(['id'=>$saleshistory[$s]['fk_bill_from_id'],'status'=>1])->one();
                if(isset($getveninv) && $getveninv->location_name != ""){
                  $location_name = $getveninv->location_name;
                }else{
                  $location_name = "(not set)";
                }
                echo '<tr>
                <td>'.$saleshistory[$s]['invoice_number'].' |
                <a class="unformat text-sm" href="'.Url::to(['invoice/view','id'=>$saleshistory[$s]['invoice_id']]).'" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
                </td>
                <td>'.$saleshistory[$s]['invoice_date'].'</td>
                <td>'.$saleshistory[$s]['company_name'].'</td>
                <td>'.$saleshistory[$s]['quantity'].'</td>
                <td>'.$saleshistory[$s]['unit_price'].'</td>
                <td>'.$saleshistory[$s]['total_price'].'</td>
                <td>'.$location_name.'</td>
                </tr>';
              }
            }
            ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3" align="right">Total Qty:</td>
              <td><?=$total_qty_sold?></td>
              <td colspan="3">

              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div><!-- Card ended for item receiving -->


    <div class="card shadow rounded mt-2">
      <div class="card-header p-3 border-bottom border-300 bg-danger-subtle">
        <div class="row g-3 justify-content-between align-items-center">
          <div class="col-12 col-md">
            <h4 class="text-900 mb-0" data-anchor="data-anchor" id="basic-details">Images</h4>
          </div>
        </div>
      </div>
      <div class="card-body p-3 table-responsive">
        <?php
        $getimgs = TblProductImage::find()->where(['status'=>1,'fk_product_id'=>$model->id])->all();
        if(isset($getimgs) && count($getimgs) > 0){
          echo '<table class="table table-bordered mt-3">
          <thead>
          <tr>
          <th>Sr. No.</th>
          <th>Image</th>
          </tr>
          </thead>
          <tbody>';
          $j = 1;
          foreach($getimgs as $gi){
            echo '<tr>
            <td>
            '.$j.'
            </td>
            <td>
            <input type="hidden" id="img_'.$gi->id.'" value="product-images/'.$gi->url.'">
            <a style="cursor:pointer;" class="img_open_popup" id="'.$gi->id.'">
            <img src="product-images/'.$gi->url.'" width="150px"/>
            </a>
            </td>
            </tr>';
            $j++;
          }
          echo '
          </tbody>
          </table>';
        }else{
          echo '<p>
          No images uploaded yet.
          </p>';
        }
        ?>

      </div><!-- card body ended-->
    </div><!-- card ended -->


  </div>



  <!-- product image modal -->
  <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="modal-title" id="exampleModalLabel">Item Image</h2>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-12" id="product_img_div" align="center">

            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <?php
  $this->registerJs('
  $(document).on("click",".img_open_popup",function(e){
    e.preventDefault();
    let id = $(this).attr("id");
    let img_src = $("#img_"+id).val();
    let content = "<img src=\""+img_src+"\" class=\"img-fluid\"/>";
    $("#product_img_div").html(content);
    var imageModal = new bootstrap.Modal(document.getElementById(\'imageModal\'), {});
      imageModal.show();

    });
    ');
    ?>
