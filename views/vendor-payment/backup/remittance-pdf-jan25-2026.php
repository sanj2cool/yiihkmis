<?php
use yii\helpers\Html;

$getcompany = \app\models\TblOwnershipCompany::find()->where(['id'=>$payment->fk_location_id])->one();
if(isset($getcompany) && $getcompany->id != ""){
  $bill_from_cname = $getcompany->company_name;
  $bill_from_address = $getcompany->address;
  $bill_from_city = $getcompany->city;
  $bill_from_province = $getcompany->state;
  $bill_from_postal_code = $getcompany->postal_code;
  $bill_from_phone = $getcompany->phone;
  $bill_logo = $getcompany->logo;
  $bill_hst_no = $getcompany->hst_number;
  $bill_email = $getcompany->email;
}else{
  //---by default keep it hk trailer parts ---
  $bill_from_cname = "HK Trailer Parts";
  $bill_from_address = "120 Orenda Rd #1d";
  $bill_from_city = "Brampton";
  $bill_from_province = "ON";
  $bill_from_postal_code = "L6W 1W2";
  $bill_from_phone = "647-282-6031";
  $bill_logo = "hktrailerparts-logo.png";
  $bill_hst_no = "787330810RT0001";
  $bill_email = "info@hktrailerparts.com";
}

 ?>
<!-- ================= HEADER ================= -->
<div class="header">
    <table width="100%" style="valign:top;">
        <tr>
          <td width="20%" valign="top">
                <?php if (!empty($bill_logo) && file_exists('/var/www/html/web/ownercompany-logo/'.$bill_logo)): ?>
                    <img src="<?= '/var/www/html/web/ownercompany-logo/'.$bill_logo ?>" style="height:72px;">
                <?php endif; ?>
            </td>
            <td width="50%" valign="top">
                <h4><?= Html::encode($bill_from_cname) ?></h4>
                <?= Html::encode($bill_from_address) ?><br>
                <?= Html::encode($bill_from_city) ?>, <?= Html::encode($bill_from_province) ?> <?= Html::encode($bill_from_postal_code) ?><br>
                Phone: <?= Html::encode($bill_from_phone) ?><br>
                Email: <?= Html::encode($bill_email) ?>
            </td>
            <td width="30%" class="right" valign="top">
                <h4>Remit To</h4>
                <strong><?= Html::encode($vendor->company_name) ?></strong><br>
                <?= nl2br(Html::encode($vendor->address)) ?><br>
                <?= Html::encode($vendor->city) ?>, <?= Html::encode($vendor->state) ?> <?= Html::encode($vendor->postal_code) ?><br>
                Phone: <?= Html::encode($vendor->phone) ?><br>
                Email: <?= Html::encode($vendor->email) ?>
            </td>
        </tr>
    </table>
</div>

<!-- ================= VENDOR ================= -->


<!-- ================= PAYMENT SUMMARY ================= -->
<!-- ================= PAYMENT SUMMARY ================= -->
<div style="margin-bottom:15px;">
    <div class="section-title" style="text-align:center;">Remittance Advice</div>

    <table class="table">
        <thead>
            <tr>
                <th width="25%">Payment ID</th>
                <th width="25%">Payment Date</th>
                <th width="25%">Payment Method</th>
                <th width="25%" class="right">Amount Paid</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= $payment->id ?></td>
                <td><?= $payment->ar_date ?></td>
                <td><?= Html::encode($paymentMethod) ?></td>
                <td class="right"><strong>$<?= number_format($totalAmount, 2) ?></strong></td>
            </tr>
        </tbody>
    </table>
</div>

<!-- ================= INVOICE DETAILS ================= -->
<div class="section-title" style="text-align:center;">Settled Invoice(s) Details</div>
<table class="table">
    <thead>
        <tr>
            <th width="25%">Invoice #</th>
            <th width="25%">Invoice Date</th>
            <th width="25%">Invoice Total</th>
            <th width="25%" class="right">Amount Paid</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $row): ?>
        <tr>
            <td><?= Html::encode($row->invoice->vendor_invoice_number) ?></td>
            <td><?=$row->invoice->invoice_date ?></td>
            <td>$<?= number_format($row->invoice->total_amount, 2) ?></td>
            <td class="right">$<?= number_format($row->amount_received, 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" class="right"><strong>Total Paid</strong></td>
            <td class="right"><strong>$<?= number_format($totalAmount, 2) ?></strong></td>
        </tr>
    </tfoot>
</table>

<!-- ================= FOOTER ================= -->
<div style="margin-top:25px; font-size:10px; color:#666;">
    This remittance advice is provided for informational purposes only.
    Please retain this document for your records.
</div>
