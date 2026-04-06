<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\TblVendor;
use app\models\TblPurchaseOrderItem;
use app\models\TblProduct;
use app\models\TblTaxRate;

/** @var yii\web\View $this */
/** @var app\models\TblPurchaseOrder $model */

$this->title = $model->po_number;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Purchase Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$getvendor = TblVendor::find()->where(['id'=>$model->fk_vendor_id])->one();
if(isset($getvendor) && $getvendor->id != ""){
    $vendor_name = $getvendor->company_name;
    $vendor_address = $getvendor->address.' '.$getvendor->city;
    $vendor_state_postal = $getvendor->state.' '.$getvendor->postal_code;
    $vendor_phone = $getvendor->phone;
}else{
    $vendor_name = "";
    $vendor_address = "";
    $vendor_state_postal = "";
    $vendor_phone = "";
}
$gethst = TblTaxRate::find()->where(['id'=>$model->hst,'status'=>1])->one();
if(isset($gethst) && $gethst->id != ""){
  $hst = $gethst->tax_rate.'%';
}else{
  $hst = "0%";
}

use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>11,'status'=>1])->one();

$getlocation = \app\models\TblOwnershipCompany::find()->where(['id'=>$model->fk_location_id])->one();
if(isset($getlocation) && $getlocation->id != ""){
  $location_name = $getlocation->company_name;
  $location_address = $getlocation->address.', '.$getlocation->city;
  $location_state_postal = $getlocation->state.' '.$getlocation->postal_code;
  $location_phone = $getlocation->phone;
}else{
  $location_name = 'HK Trailer Parts';
  $location_address = '120 Orenda Rd #1d, Brampton';
  $location_state_postal = 'ON L6W 1W2';
  $location_phone = '647-968-6031';
}
?>
<div class="tbl-purchase-order-view">

  <div class="card shadow rounded mt-2">
          <div class="card-body p-3">
              <div class="row">
                  <div class="col-lg-6">
                      <h3><?= Html::encode($this->title) ?></h3>
                  </div>
                  <div class="col-lg-6 text-end">
                  <button onclick="printInvoice()" class="btn btn-info btn-sm">Print Order</button>
                  <?php
                    if(isset($menuaccess) && $menuaccess->edit_crud == 1){
                      ?>
                      <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']) ?>
                      <?php
                    }
                   ?>

                  <?php
                    if(isset($menuaccess) && $menuaccess->delete_crud == 1){
                      ?>
                      <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                          'class' => 'btn btn-danger btn-sm',
                          'data' => [
                              'confirm' => 'Are you sure you want to delete this item?',
                              'method' => 'post',
                          ],
                      ]) ?>
                      <?php
                    }
                   ?>

                      <?= Html::a('Back to List', ['index'], ['class' => 'btn btn-secondary btn-sm']) ?>
					  <a href="<?= \yii\helpers\Url::to(['vendor-invoice/create-from-po', 'id' => $model->id]) ?>"
   class="btn btn-success">
    Convert to Invoice
</a>

<?php if ($model->is_converted == 1): ?>
    <small class="text-muted d-block mt-1">Already converted once</small>
<?php endif; ?>
                  </div>
              </div>
          </div>
      </div>

      <div class="card shadow rounded mt-2">
          <div class="card-body p-3" id="print_section">
          <style>
          .container-cust {
              font-family: Arial, sans-serif;
              font-size:13px;
              margin: 0;
              padding: 0;
              box-sizing: border-box;
          }
          .container-cust {
              width: 90%;
              margin: auto;
              padding: 20px;
              max-width: 800px;
          }
          .header-cust, .footer-cust {
              text-align: center;
              margin-bottom: 20px;
          }
          .section-cust {
              display: flex;
              justify-content: space-between;
              margin-bottom: 20px;
          }
          .section-cust div {
              width: 30%;
          }
          .addresses, .dates, .comments-totals {
              display: flex;
              justify-content: space-between;
              margin-bottom: 20px;
          }
          .addresses div, .dates div, .comments-totals div {
              width: 48%;
          }
          table {
              width: 100%;
              border-collapse: collapse;
              margin-bottom: 20px;
              table-layout: auto;
          }
          table, th, td {
              border: 1px solid #ddd;
          }
          th, td {
              padding: 8px;
              text-align: left;
          }
          th {
              background-color: #f2f2f2;
          }
          .footer-cust {
              font-size: 0.8em;
              color: #555;
          }
          .align-text-right p{
              text-align:right;
          }
          @media (max-width: 576px) {
              .section-cust {
                  flex-direction: column;
              }
              .section-cust div {
                  width: 100%;
                  margin-bottom: 10px;
              }
              .comments-totals {
                  flex-direction: column;
              }
              .comments-totals div {
                  width: 100%;
                  margin-bottom: 10px;
              }
          }
      </style>

      <div class="container-cust" style="border:1px solid lightgrey;border-radius:5px;">
          <div class="header-cust">
              <h1>Purchase Order</h1>
          </div>
          <div class="section-cust">
              <div>
                  <h4>To:</h4>
                  <p>
                      <?=$vendor_name?><br>
                      <?=$vendor_address?><br>
                      <?=$vendor_state_postal?><br>
                      <?=$vendor_phone?>
                  </p>
              </div>
              <div>
                  <h4>From:</h4>
                  <p>
                    <?=$location_name?><br>
                    <?=$location_address?><br>
                    <?=$location_state_postal?><br>
                    <?=$location_phone?>
                  </p>
              </div>
              <div style="text-align:right;">
                  <h4>Order Details</h4>
                  <p>
                      <strong>Order #:</strong> <span><?=$model->po_number?></span><br>
                      <strong>Order Date:</strong> <span><?=$model->po_date?></span><br>
                      <strong>Exp. Delivery Date:</strong> <span><?=$model->expected_delivery_date?></span>
                  </p>
              </div>
          </div>
          <div style="overflow-x: auto;">


          <table>
              <thead>
                  <tr>
                      <th>Item</th>
                      <th>Description</th>
                      <th>Quantity</th>
                      <th>Rate</th>
                      <th>Amount</th>
                  </tr>
              </thead>
              <tbody>
                  <?php
                      $getitems = TblPurchaseOrderItem::find()->where(['fk_purchase_order_id'=>$model->id,'status'=>1])->all();
                      if(isset($getitems) && count($getitems) > 0){
                          foreach($getitems as $gi){
                            // if($gi->type == 2){
                              //---its product---
                              $getprod = TblProduct::find()->where(['id'=>$gi->fk_product_id])->one();
                              if(isset($getprod) && $getprod->id != ""){
                                $item = $getprod->name.' ['.$getprod->sku.']';
                              }
                            // }else{
                            //   //----its item ------
                            //   $item = $gi->item;
                            // }
                            echo ' <tr>
                                <td>'.$item.'</td>
                                <td>'.$gi->description.'</td>
                                <td>'.$gi->quantity.'</td>
                                <td>$'.$gi->unit_price.'</td>
                                <td>$'.$gi->total_price.'</td>
                            </tr>';
                          }
                      }
                  ?>
              </tbody>
          </table>
          </div>

          <div class="comments-totals">
              <div>
                  <h4>Comments</h4>
                  <p><?=$model->comments?></p>
              </div>
              <div class="align-text-right">
                  <p><strong>Subtotal:</strong> $<?=$model->subtotal?></p>
                  <p><strong>HST (<?=$hst?>):</strong> $<?=$model->hst_amount?></p>
                  <p><strong>Total Amount:</strong> $<?=$model->total_amount?></p>
              </div>
          </div>

          <div class="footer-cust">
              <p>If you have any questions about this invoice, please contact us at [email address]</p>
          </div>
      </div>

          </div>
      </div>

</div>
<script>
        function printInvoice() {
            var printContents = document.querySelector('#print_section').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
</script>
