<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\TblClient;
use app\models\TblInvoiceItem;
use app\models\TblProduct;
use app\models\TblTaxRate;
use app\models\TblOwnershipCompany;
/** @var yii\web\View $this */
/** @var app\models\TblVendorInvoice $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$this->title = "#".$model->invoice_number.' Copy';

$getvendor = TblClient::find()->where(['id'=>$model->fk_client_id])->one();
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

$getcompany = TblOwnershipCompany::find()->where(['id'=>$model->fk_bill_from_id])->one();
if(isset($getcompany) && $getcompany->id != ""){
  $bill_from_cname = $getcompany->company_name;
  $bill_from_address = $getcompany->address;
  $bill_from_city = $getcompany->city;
  $bill_from_province = $getcompany->state;
  $bill_from_postal_code = $getcompany->postal_code;
  $bill_from_phone = $getcompany->phone;
}else{
  //---by default keep it hk trailer parts ---
  $bill_from_cname = "HK Trailer Parts";
  $bill_from_address = "120 Orenda Rd #1d";
  $bill_from_city = "Brampton";
  $bill_from_province = "ON";
  $bill_from_postal_code = "L6W 1W2";
  $bill_from_phone = "647-282-6031";
}
?>
<div class="tbl-vendor-invoice-view">

  <div class="card shadow rounded mt-2">
    <div class="card-body p-3">
      <div class="row">
        <div class="col-lg-6">
          <h3><?= Html::encode($this->title) ?></h3>
        </div>
        <div class="col-lg-6 text-end">
          <!-- <button onclick="printInvoice()" class="btn btn-info btn-sm">Print Invoice</button> -->
          <button id="print_invoice" class="btn btn-info btn-sm"><i class="fas fa-print"></i> Print Invoice</button>
          <?= Html::a('<i class="fas fa-envelope"></i> Send Invoice', ['send-invoice', 'id' => $model->id], ['class' => 'btn btn-success btn-sm']) ?>
          <?= Html::a('<i class="fas fa-pen"></i> Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']) ?>
          <?= Html::a('<i class="fas fa-trash"></i> Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger btn-sm',
            'data' => [
              'confirm' => 'Are you sure you want to delete this item?',
              'method' => 'post',
            ],
            ]) ?>
            <?= Html::a('<i class="fas fa-chevron-left"></i> Back to List', ['index'], ['class' => 'btn btn-secondary btn-sm']) ?>
          </div>
        </div>
      </div>
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
            <h1>Invoice</h1>
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
                <?=$bill_from_cname?><br>
                <?=$bill_from_address?>, <?=$bill_from_city?><br />
                <?=$bill_from_province?> <?=$bill_from_postal_code?><br>

                Cell: <?=$bill_from_phone?>
              </p>
            </div>
            <div style="text-align:right;">
              <h4>Invoice Details</h4>
              <p>
                <strong>Invoice #:</strong> <span><?=$model->invoice_number?></span><br>
                <strong>Invoice Date:</strong> <span><?=$model->invoice_date?></span><br>
                <strong>Due Date:</strong> <span><?=$model->due_date?></span>
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
                $getitems = TblInvoiceItem::find()->where(['fk_invoice_id'=>$model->id,'status'=>1])->all();
                if(isset($getitems) && count($getitems) > 0){
                  foreach($getitems as $gi){
                    // if($gi->type == 2){
                    //---its product---
                    $getprod = TblProduct::find()->where(['id'=>$gi->fk_product_id])->one();
                    if(isset($getprod) && $getprod->id != ""){
                      $item = $getprod->name.' ['.$getprod->sku.']';
                    }else{
                      $item = "";
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
            <p>If you have any questions about this invoice, please contact us at invoice@hktrailerparts.com</p>
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
<?php
$this->registerJs('
$("#print_invoice").click(function(){
  w=window.open();
  w.document.write($("#print_section").html());
  w.print();
  w.close();
});
');
?>
