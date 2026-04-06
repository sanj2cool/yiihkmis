<?php
use yii\helpers\Html;

/*
Expected variables:
$pendingInvoices
$aging
$totalOutstanding
$from_date
$to_date
$company      // array: name, address, phone, email, logo(optional)
$vendor       // vendor model or array
*/

$getcompany = \app\models\TblOwnershipCompany::find()->where(['id'=>$vendor->fk_location_id])->one();
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
$mode = $mode ?? 'modal';
?>

<div class="vendor-statement" id="vendor-statement-print-area">

  <!-- ================= HEADER ================= -->
  <div class="statement-header">
  <table width="100%" style="margin-bottom:15px;">
    <tr>
      <!-- COMPANY DETAILS -->
      <td style="vertical-align:top; width:50%;">
        <?php if (!empty($bill_logo) && file_exists('ownercompany-logo/'.$bill_logo)): ?>
            <img src="<?= 'ownercompany-logo/'.$bill_logo ?>" style="height:60px;margin-bottom:6px">
        <?php endif; ?>


      </td>

      <!-- STATEMENT TITLE -->
      <td style="vertical-align:top; text-align:right; width:50%;">
        <div style="font-size:18px;font-weight:bold;">
          Vendor Statement
        </div>

        <div style="font-size:12px;">
          <strong>Statement Date:</strong>
          <?= date('Y-m-d') ?><br>

          <strong>Period:</strong>
          <?= $from_date ?>
          to
          <?= $to_date ?>
        </div>
      </td>
    </tr>
  </table>



  <table width="100%" style="margin-bottom:15px;">
    <tr>
      <!-- COMPANY DETAILS -->
      <td style="vertical-align:top; width:50%;">

        <div style="font-size:15px;font-weight:bold;">
          <?= Html::encode($bill_from_cname) ?>
        </div>

        <div style="font-size:12px; line-height:1.4;">
          <?= Html::encode($bill_from_address) ?><br>
          <?= Html::encode($bill_from_city) ?>, <?= Html::encode($bill_from_province) ?> <?= Html::encode($bill_from_postal_code) ?><br>
          Phone: <?= Html::encode($bill_from_phone) ?><br>
          Email: <?= Html::encode($bill_email) ?>
        </div>
      </td>

      <!-- STATEMENT TITLE -->
      <td style="vertical-align:top; text-align:right; width:50%;">
        <strong>Vendor:</strong>
        <div style="font-size:15px;font-weight:bold;"><?= Html::encode($vendor->company_name ?? '') ?></div>
        <div style="font-size:12px; line-height:1.4;">
          <?php if (!empty($vendor->address)): ?>
            <?= nl2br(Html::encode($vendor->address)) ?><br>
            <?= Html::encode($vendor->city) ?>, <?= Html::encode($vendor->state) ?> <?= Html::encode($vendor->postal_code) ?><br>
          <?php endif; ?>

          <?php if (!empty($vendor->phone)): ?>
            Phone: <?= Html::encode($vendor->phone) ?><br>
          <?php endif; ?>

          <?php if (!empty($vendor->email)): ?>
            Email: <?= Html::encode($vendor->email) ?>
          <?php endif; ?>
      </div>
      </td>
    </tr>
  </table>
</div>



  <!-- ================= STATEMENT BODY ================= -->

  <?php if (empty($pendingInvoices)): ?>

    <div class="alert alert-success">
      No outstanding invoices found for the selected period.
    </div>

  <?php else: ?>

    <!-- OUTSTANDING INVOICES -->
    <table class="statement-invoices table table-sm">

      <thead class="table-dark">
        <tr>
          <th>Invoice Date</th>
          <th>Invoice #</th>
          <th>Vendor Invoice #</th>
          <th>Due Date</th>
          <th class="text-end">Outstanding Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pendingInvoices as $inv): ?>
          <tr>
            <td><?= $inv['invoice_date'] ?></td>
            <td><?= Html::encode($inv['invoice_number']) ?></td>
            <td><?= Html::encode($inv['purchase_invoice_no']) ?></td>
            <td><?= $inv['due_date'] ?></td>
            <td class="text-end text-danger fw-bold">
              <?= number_format($inv['pending_amount'],2,'.',',') ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <th colspan="4" class="text-end">Total Outstanding</th>
          <th class="text-end text-danger fw-bold">
            <?= number_format($totalOutstanding,2,'.',',') ?>
          </th>
        </tr>
      </tfoot>
    </table>

    <!-- ================= AGING SUMMARY ================= -->
    <h6 class="mt-4">Aging Summary</h6>
    <table class="statement-aging table table-sm w-100">

      <thead class="table-secondary">
        <tr>
          <th class="text-end">Current</th>
          <th class="text-end">1–30</th>
          <th class="text-end">31–60</th>
          <th class="text-end">61–90</th>
          <th class="text-end">90+</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="text-end"><?= number_format($aging['current'],2,'.',',') ?></td>
          <td class="text-end"><?= number_format($aging['1_30'],2,'.',',') ?></td>
          <td class="text-end"><?= number_format($aging['31_60'],2,'.',',') ?></td>
          <td class="text-end"><?= number_format($aging['61_90'],2,'.',',') ?></td>
          <td class="text-end fw-bold text-danger">
            <?=number_format($aging['90_plus'],2,'.',',') ?>
          </td>
        </tr>
      </tbody>
    </table>

  <?php endif; ?>

  <!-- FOOTER -->
  <div style="margin-top:20px;font-size:11px;color:#666;">
    This is a system generated statement and does not require a signature.
  </div>

</div>
<?php if ($mode === 'modal'): ?>
<input type="hidden" id="stmt-from-date" value="<?= $from_date ?>">
<input type="hidden" id="stmt-to-date" value="<?= $to_date ?>">
<input type="hidden" id="stmt-vendor-id" value="<?= (int)$vendor->id ?>">

<div class="mt-3 d-flex justify-content-end gap-2">
  <button type="button"
        class="btn btn-success btn-sm"
        onclick="emailVendorStatement()">
    <i class="fas fa-envelope"></i> Email Statement
</button>

    <button type="button"
            class="btn btn-outline-secondary btn-sm"
            onclick="printVendorStatement()">
        <i class="fas fa-print"></i> Print
    </button>

    <button type="button"
            class="btn btn-secondary btn-sm"
            data-bs-dismiss="modal">
        Close
    </button>
</div>
<style>
@media print {
  body * {
    visibility: hidden;
  }
  #vendor-statement-print-area,
  #vendor-statement-print-area * {
    visibility: visible;
  }
}
</style>
<script>
function emailVendorStatement() {

    var vendorId = document.getElementById('stmt-vendor-id').value;
    var fromDate = document.getElementById('stmt-from-date').value;
    var toDate   = document.getElementById('stmt-to-date').value;

    if (!fromDate || !toDate) {
        alert('Statement dates missing.');
        return;
    }

    if (!confirm('Send this statement to the vendor via email?')) {
        return;
    }

    $.ajax({
        url: 'index.php?r=vendor/email-statement',
        type: 'POST',
        dataType: 'json',
        data: {
            vendor_id: vendorId,
            from_date: fromDate,
            to_date: toDate
        },
        beforeSend: function () {
            $('.btn').prop('disabled', true);
        },
        success: function (res) {
            alert(res.message || 'Email sent.');
        },
        error: function () {
            alert('Failed to send statement email.');
        },
        complete: function () {
            $('.btn').prop('disabled', false);
        }
    });
}
</script>

<script>
function printVendorStatement() {

    var content = document.getElementById('vendor-statement-print-area').innerHTML;

    // Create hidden iframe
    var iframe = document.createElement('iframe');
    iframe.style.position = 'fixed';
    iframe.style.right = '0';
    iframe.style.bottom = '0';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = '0';

    document.body.appendChild(iframe);

    var doc = iframe.contentWindow.document;
    doc.open();
    doc.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Vendor Statement</title>
            <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                color: #000;
                margin: 20px;
            }

        /* RESET */
         table {
            width: 100%;
            border-collapse: collapse;
        }

         table th, td {
            padding: 6px;
            vertical-align: top;
        }

        /* FORCE PRINT COLORS */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* HEADER + VENDOR (NO BORDERS) */
        .statement-header table,
        .statement-vendor table {
            border: none;
        }

        .statement-header td,
        .statement-vendor td {
            border: none;
            padding: 2px 0;
        }

        /* INVOICE TABLE HEADER */
        .statement-invoices thead th {
            background-color: #e9ecef !important; /* light gray */
            border-bottom: 2px solid #000;
            font-weight: bold;
        }

        /* INVOICE TABLE BODY */
        .statement-invoices tbody td {
            border-bottom: 1px solid #ccc;
        }

        /* INVOICE TABLE FOOTER */
        .statement-invoices tfoot th,
        .statement-invoices tfoot td {
            background-color: #f8f9fa !important;
            border-top: 2px solid #000;
            font-weight: bold;
        }

        /* AGING TABLE HEADER */
        .statement-aging thead th {
            background-color: #f1f3f5 !important;
            border-bottom: 1px solid #000;
            font-weight: bold;
        }

        /* AGING TABLE BODY */
        .statement-aging tbody td {
            border-top: 1px solid #ccc;
        }

        /* UTILITIES */
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .text-danger { color: #000; } /* force black in print */

            </style>
        </head>
        <body>
            ${content}
        </body>
        </html>
    `);
    doc.close();

    iframe.contentWindow.focus();
    iframe.contentWindow.print();

    // Cleanup iframe after print
    setTimeout(function () {
        document.body.removeChild(iframe);
    }, 1000);
}
</script>
<?php endif; ?>
