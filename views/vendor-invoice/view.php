<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\TblVendor;
use app\models\TblVendorInvoiceItem;
use app\models\TblProduct;
use app\models\TblTaxRate;
/** @var yii\web\View $this */
/** @var app\models\TblVendorInvoice $model */

$this->title = $model->vendor_invoice_number;
$this->params['breadcrumbs'][] = ['label' => 'Vendor Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$this->title = "#".$model->vendor_invoice_number;

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
  $tax_desc = $gethst->description;
}else{
  $hst = "0%";
  $tax_desc = "Tax";
}
use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>12,'status'=>1])->one();

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

use yii\db\Query;
$totalRevenue = (new Query())
->select(['SUM(ROUND(amount_received, 2)) AS total_revenue'])
->from('tbl_vendor_account_payable')
->where(['!=', 'status', 0])
->andWhere('fk_vendor_invoice_id = '.$model->id)
->scalar();
// echo $totalRevenue;
if($totalRevenue > 0 && $totalRevenue != ""){
  $amount_received = $totalRevenue;
}else{
  $amount_received = 0;
}
$balance_due = $model->total_amount-$amount_received;


?>
<div class="tbl-vendor-invoice-view">

  <div class="card shadow rounded mt-2">
    <div class="card-body p-3">
      <div class="row">
        <div class="col-lg-6">
          <h3><?= Html::encode($this->title) ?></h3>
        </div>
        <div class="col-lg-6 text-end">
          <?php
          if(!empty($model->invoiceFiles)){
            $count = count($model->invoiceFiles);
            echo Html::button(
              '<i class="fas fa-file-invoice"></i> View Files ('.$count.')',
              [
                'class' => 'btn btn-sm btn-primary expense-file-preview',
                'data-files' => json_encode(array_map(function ($file) {
                  return [
                    'url' => 'https://hkmis.ca/web/vendor-invoices/' . $file->invoice_file,
                    'type' => strtolower(pathinfo($file->invoice_file, PATHINFO_EXTENSION))
                  ];
                }, $model->invoiceFiles)),
              ]
            );
          }

          ?>
          <button onclick="printInvoice()" class="btn btn-info btn-sm"><i class="fas fa-print"></i> Print Invoice</button>
          <?php
          if(isset($menuaccess) && $menuaccess->edit_crud == 1){
            ?>
            <?= Html::a('<i class="fas fa-pen"></i> Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']) ?>
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

    <div class="card shadow rounded mt-2">
      <div class="card-body p-3" id="print_section">
        <style>
        .container-cust {
          font-family: Arial, sans-serif;
          font-size:13px;
          margin: 0;
          padding: 0;
          box-sizing: border-box;
          width: 90%;
          margin: auto;
          padding: 20px;
          max-width: 800px;

          position: relative; /* Required for pseudo-element */
          border: 1px solid lightgrey;
          border-radius: 5px;
          background-color: white; /* Ensures readability */
          overflow: hidden; /* Ensures watermark stays within the box */
        }
        <?php
        if($balance_due == 0 || $balance_due < 0){
          ?>
          .container-cust::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 100%;
            background-image: url('images/paid-invoice.png'); /* Replace with your image URL */
            background-size: contain; /* Adjust to 'cover' if needed */
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.1; /* Adjust opacity for watermark effect */
            transform: translate(-50%, -50%);
            z-index: 2; /* Keeps it behind the content */
            pointer-events: none; /* Ensures it doesn't interfere with interactions */
          }

          .container-cust * {
            position: relative; /* Ensures text stays above the watermark */
            z-index: 1;
          }
          <?php
        }
         ?>
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
          <div class="header-cust" style="margin-bottom:80px;">
            <h1>Invoice</h1>
          </div>
          <div class="section-cust">
            <div>
              <h4>From:</h4>
              <p>
                <?=$vendor_name?><br>
                <?=$vendor_address?><br>
                <?=$vendor_state_postal?><br>
                <?=$vendor_phone?>
              </p>
            </div>
            <div>
              <h4>To:</h4>
              <p>
                <?=$location_name?><br>
                <?=$location_address?><br>
                <?=$location_state_postal?><br>
                <?=$location_phone?>
              </p>
            </div>
            <div style="text-align:right;">
              <h4>Invoice Details</h4>
              <p>
                <strong>Invoice #:</strong> <span><?=$model->vendor_invoice_number?></span><br>
                <strong>Vendor Invoice #:</strong> <span><?=$model->purchase_invoice_no?></span><br>
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
                $getitems = TblVendorInvoiceItem::find()->where(['fk_vendor_invoice_id'=>$model->id,'status'=>1])->all();
                if(isset($getitems) && count($getitems) > 0){
                  foreach($getitems as $gi){
                    // if($gi->type == 2){
                    //---its product---
                    $getprod = TblProduct::find()->where(['id'=>$gi->fk_product_id])->one();
                    if(isset($getprod) && $getprod->id != ""){
                      $item = $getprod->name.' ['.$getprod->internal_sku.']';
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
              <p style="margin-bottom:0px;"><strong>Subtotal:</strong> $<?=$model->subtotal?></p>
              <?php
              $new_subtotal = $model->subtotal;
              //-----let's check if any discount has been applied to the invoice ----
              if($model->discount_type == 1 && $model->discount_value != ""){
                //----its discount value ----
                $dis_value = $model->discount_value;
                $new_subtotal = $new_subtotal-$dis_value;
                ?>
                <p style="margin-bottom:0px;"><strong>Discount:</strong> -$<?=number_format($dis_value,2,'.',',')?></p>
                <?php
              }else if($model->discount_type == 2 && $model->discount_value != ""){
                $dis_value = $model->subtotal*($model->discount_value/100);
                $new_subtotal = $new_subtotal-$dis_value;
                ?>
                <p style="margin-bottom:0px;"><strong>Discount:</strong> -$<?=number_format($dis_value,2,'.',',')?></p>
                <?php
              }
              ?>
              <p style="margin-bottom:0px;"><strong><?=$tax_desc?> (<?=$hst?>) on $<?=number_format($new_subtotal,2,'.',',')?>:</strong> $<?=$model->hst_amount?></p>
              <p style="margin-bottom:0px;"><strong>Total Amount:</strong> $<?=$model->total_amount?></p>
              <p style="margin-bottom:0px;"><strong>Amount Paid:</strong> $<?=number_format($amount_received,2,'.',',')?></p>
              <p style="margin-bottom:0px;"><strong>Balance Due:</strong> $<?=number_format($balance_due,2,'.',',')?></p>
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
<!-- Modal for PDF display -->
<div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pdfModalLabel">Purchase Invoice</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <iframe id="pdfViewer" src="" width="100%" height="800px"></iframe>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="expenseFileModal" tabindex="-1">
  <div class="modal-dialog modal-fullscreen modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Uploaded File(s)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center" id="expenseFileContent">
        <!-- dynamic -->
      </div>
    </div>
  </div>
</div>
<script>
document.addEventListener('click', function (e) {

  if (!e.target.classList.contains('expense-file-preview')) return;

  let files = JSON.parse(e.target.dataset.files);
  let html = '';

  files.forEach((file, index) => {

    html += `
    <div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
    <strong>File ${index + 1}</strong>
    <a href="${file.url}" target="_blank" class="btn btn-sm btn-outline-secondary">
    Open
    </a>
    </div>
    `;

    if (file.type === 'pdf') {
      html += `
      <iframe src="${file.url}" style="width:100%; height:80vh;" frameborder="0"></iframe>
      `;
    } else {
      html += `
      <div class="text-center">
      <img src="${file.url}" class="img-fluid" alt="Expense File">
      </div>
      `;
    }

    html += `</div>`;
  });

  document.getElementById('expenseFileContent').innerHTML = html;

  let modal = new bootstrap.Modal(document.getElementById('expenseFileModal'));
  modal.show();
});
</script>
