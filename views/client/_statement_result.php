<?php
use yii\helpers\Html;

/*
Expected variables:
$pendingInvoices
$aging
$totalOutstanding
$from_date
$to_date
$client            // TblCustomer model
$company           // array with company details
$mode              // 'modal' | 'pdf'
*/

$mode = $mode ?? 'modal';
?>

<div id="client-statement-print-area">

  <!-- ================= HEADER ================= -->
  <div class="statement-header">
    <table width="100%" style="margin-bottom:15px;">
      <tr>
        <!-- COMPANY -->
        <td style="width:50%; vertical-align:top;">
          <?php if (!empty($company['logo']) && file_exists('ownercompany-logo/'.$company['logo'])): ?>
              <img src="<?= 'ownercompany-logo/'.$company['logo'] ?>" style="height:60px;margin-bottom:6px">
          <?php endif; ?>

        </td>

        <!-- TITLE -->
        <td style="width:50%; text-align:right; vertical-align:top;">
          <div style="font-size:18px;font-weight:bold;">
            Customer Statement
          </div>

          <div style="font-size:12px;">
            <strong>Statement Date:</strong> <?= date('Y-m-d') ?><br>
            <strong>Period:</strong>
            <?= $from_date ?>
            to
            <?=$to_date ?>
          </div>
        </td>
      </tr>
    </table>
  </div>


  <div class="statement-header">
    <table width="100%" style="margin-bottom:15px;">
      <tr>
        <!-- COMPANY -->
        <td style="width:50%; vertical-align:top;">

          <div style="font-size:15px;font-weight:bold;">
            <?= Html::encode($company['name']) ?>
          </div>

          <div style="font-size:12px;line-height:1.4;">
            <?= nl2br(Html::encode($company['address'])) ?><br>
            <?= Html::encode($company['phone']) ?><br>
            <?= Html::encode($company['email']) ?>
          </div>
        </td>

        <!-- TITLE -->
        <td style="width:50%; text-align:right; vertical-align:top;">
          <div style="font-size:18px;font-weight:bold;">
            Customer
          </div>

          <div style="font-size:12px;">
            <?= Html::encode($client->company_name) ?><br>
            <?= nl2br(Html::encode($client->address)) ?><br>
            <?= Html::encode($client->city) ?>,
            <?= Html::encode($client->state) ?>
            <?= Html::encode($client->postal_code) ?><br>
            <?php if (!empty($client->phone)): ?>
              Phone: <?= Html::encode($client->phone) ?><br>
            <?php endif; ?>
            <?php if (!empty($client->email)): ?>
              Email: <?= Html::encode($client->email) ?>
            <?php endif; ?>
          </div>
        </td>
      </tr>
    </table>
  </div>



  <!-- ================= BODY ================= -->
  <?php if (empty($pendingInvoices)): ?>

    <div class="alert alert-success">
      No outstanding invoices found for the selected period.
    </div>

  <?php else: ?>

    <!-- INVOICE TABLE -->
    <table class="statement-invoices table table-sm">
      <thead class="table-dark">
        <tr>
          <th>Invoice Date</th>
          <th>Invoice #</th>
          <th>Due Date</th>
          <th class="text-end">Invoiced Amount</th>
          <th class="text-end">Outstanding Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pendingInvoices as $inv): ?>
          <tr>
            <td><?= $inv['invoice_date'] ?></td>
            <td><?= Html::encode($inv['invoice_number']) ?></td>
            <td><?= $inv['due_date'] ?></td>
            <td class="text-end">
              <?= number_format($inv['total_amount'], 2) ?>
            </td>
            <td class="text-end fw-bold text-danger">
              <?= number_format($inv['pending_amount'], 2) ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <th colspan="4" class="text-end">Total Outstanding</th>
          <th class="text-end fw-bold text-danger">
            <?= number_format($totalOutstanding, 2) ?>
          </th>
        </tr>
      </tfoot>
    </table>

    <!-- AGING -->
    <h6 class="mt-4">Aging Summary</h6>
    <table class="statement-aging table table-sm">
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
          <td class="text-end"><?= number_format($aging['current'],2) ?></td>
          <td class="text-end"><?= number_format($aging['1_30'],2) ?></td>
          <td class="text-end"><?= number_format($aging['31_60'],2) ?></td>
          <td class="text-end"><?= number_format($aging['61_90'],2) ?></td>
          <td class="text-end fw-bold text-danger">
            <?= number_format($aging['90_plus'],2) ?>
          </td>
        </tr>
      </tbody>
    </table>

  <?php endif; ?>

</div>

<?php if ($mode === 'modal'): ?>
  <input type="hidden" id="stmt-from-date" value="<?= $from_date ?>">
  <input type="hidden" id="stmt-to-date" value="<?= $to_date ?>">
  <input type="hidden" id="stmt-client-id" value="<?= (int)$client->id ?>">
  <input type="hidden" id="stmt-ocompany-id" value="<?= (int)$user_company ?>">


  <div class="no-print mt-3 d-flex justify-content-end gap-2">
      <button class="btn btn-outline-secondary btn-sm" onclick="printClientStatement()">Print</button>
      <button class="btn btn-success btn-sm" onclick="emailClientStatement()">Email Statement</button>
      <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
  </div>

  <style>
  @media print {
    body * {
      visibility: hidden;
    }
    #client-statement-print-area,
    #client-statement-print-area * {
      visibility: visible;
    }
  }
  </style>
  <script>
  function emailClientStatement() {

      var clientId = document.getElementById('stmt-client-id').value;
      var fromDate = document.getElementById('stmt-from-date').value;
      var toDate   = document.getElementById('stmt-to-date').value;
      var userCompany = document.getElementById('stmt-ocompany-id').value;

      if (!fromDate || !toDate) {
          alert('Statement dates missing.');
          return;
      }

      if (!confirm('Send this statement to the customer via email?')) {
          return;
      }

      $.ajax({
          url: 'index.php?r=client/email-statement',
          type: 'POST',
          dataType: 'json',
          data: {
              client_id: clientId,
              from_date: fromDate,
              to_date: toDate,
              user_company: userCompany
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
  function printClientStatement() {

      var content = document.getElementById('client-statement-print-area').innerHTML;

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
              <title>Customer Statement</title>
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
