<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\TblVendor $model */

$this->title = $model->company_name;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Vendors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$session = Yii::$app->session;
$user_company = $session['userCompany'];
$fk_user_id = $session['userId'];
$menuaccess = \app\models\TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>9,'status'=>1])->one();
?>
<div class="tbl-vendor-view">

  <div class="card shadow rounded mt-2">
    <div class="card-body p-3">
      <div class="row">
        <div class="col-lg-6">
          <h3><?= Html::encode($this->title) ?></h3>
        </div>
        <div class="col-lg-6 text-end">
          <button class="btn btn-success btn-sm"
          id="btn-generate-statement"
          data-url="<?= Yii::$app->urlManager->createUrl([
            'vendor/statement-form',
            'vendor_id' => $model->id
            ]) ?>">
            <i class="fas fa-file-lines"></i> Generate Statement
          </button>
          <?php
          if(isset($menuaccess) && $menuaccess->edit_crud == 1){
            ?>
            <?= Html::a('<i class="fas fa-pen"></i> Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']) ?>
            <?php
          }
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
      <div class="card-header p-3 border-bottom border-300 bg-info-subtle">
        <div class="row g-3 justify-content-between align-items-center">
          <div class="col-12 col-md">
            <h4 class="text-900 mb-0" data-anchor="data-anchor" id="basic-details">Basic Details</h4>
          </div>
        </div>
      </div>
      <div class="card-body p-3">
        <div class="row">
          <div class="col-lg-3 mb-2">
            <label class="fw-bold">Company Name</label><br />
            <?=$model->company_name?>
          </div>
          <div class="col-lg-3 mb-2">
            <label class="fw-bold">Contact Name</label><br />
            <?=$model->contact_name ? $model->contact_name : '(not set)'?>
          </div>
          <div class="col-lg-3 mb-2">
            <label class="fw-bold">Contact Title</label><br />
            <?=$model->contact_title ? $model->contact_title : '(not set)'?>
          </div>
          <div class="col-lg-3 mb-2">
            <label class="fw-bold">Terms</label><br />
            <?php
            $getterms = \app\models\TblTerms::find()->where(['id'=>$model->fk_terms_id,'status'=>1])->one();
            if(isset($getterms) && $getterms->title != ""){
              echo $getterms->title;
            }else{
              echo "(not set)";
            }
            ?>
          </div>

        </div>
        <div class="row">
          <div class="col-lg-3 mb-2">
            <label class="fw-bold">Email</label><br />
            <?=$model->email ? $model->email : '(not set)'?>
          </div>
          <div class="col-lg-3 mb-2">
            <label class="fw-bold">Phone</label><br />
            <?=$model->phone ? $model->phone : '(not set)'?>
          </div>
          <div class="col-lg-3 mb-2">
            <label class="fw-bold">Address</label><br />
            <?=$model->address ? $model->address : '(not set)'?>
          </div>

          <div class="col-lg-3 mb-2">
            <label class="fw-bold">City</label><br />
            <?=$model->city ? $model->city : '(not set)'?>
          </div>
          <div class="col-lg-3 mb-2">
            <label class="fw-bold">Province</label><br />
            <?=$model->state ? $model->state : '(not set)'?>
          </div>
          <div class="col-lg-3 mb-2">
            <label class="fw-bold">Postal Code</label><br />
            <?=$model->postal_code ? $model->postal_code : '(not set)'?>
          </div>
        </div>

      </div>
    </div>
  </div>
  <!-- THE VENDORS ARE LOCATION SPECIFIC ONLY -->
  <!-- -as per the logged in location -->
  <?php
  $ownership_company = \app\models\TblOwnershipCompany::find()->where(['status'=>1,'id'=>$user_company])->orderBy(['company_name'=>SORT_ASC])->one();
  if(isset($ownership_company) && $ownership_company->id != ""){
    $vendorSummary = (new \yii\db\Query())
    ->select([
      'total_invoices' => 'COUNT(DISTINCT vi.id)',
      'total_invoice_amount' => 'SUM(vi.total_amount)',
      'total_paid_amount' => 'IFNULL(SUM(vap.amount_received),0)',
      'total_pending_amount' => '
      SUM(vi.total_amount) - IFNULL(SUM(vap.amount_received),0)
      ',
      'last_payment_date' => 'MAX(vap.ar_date)'
    ])
    ->from('tbl_vendor_invoice vi')
    ->leftJoin(
      'tbl_vendor_account_payable vap',
      'vap.fk_vendor_invoice_id = vi.id AND vap.status = 1'
      )
      ->where([
        'vi.status' => 1,
        'vi.fk_vendor_id' => $model->id,
        'vi.fk_location_id' => $user_company
      ])
      ->groupBy('vi.fk_vendor_id')
      ->one();

      $overdueAmount = (new \yii\db\Query())
      ->from([
        't' => (new \yii\db\Query())
        ->select([
          'total_amount',
          'pending_amount' => 'vi.total_amount - IFNULL(SUM(vap.amount_received),0)'
        ])
        ->from('tbl_vendor_invoice vi')
        ->leftJoin(
          'tbl_vendor_account_payable vap',
          'vap.fk_vendor_invoice_id = vi.id AND vap.status = 1'
          )
          ->where([
            'vi.status' => 1,
            'vi.fk_vendor_id' => $model->id,
            'vi.fk_location_id' => $user_company
          ])
          ->andWhere(['<', 'vi.due_date', date('Y-m-d')])
          ->groupBy('vi.id')
          ->having('vi.total_amount > IFNULL(SUM(vap.amount_received),0)')
        ])
        ->sum('pending_amount');


        ?>



        <?php
        //---show the transactions block ----
        $getinvoices = \app\models\TblVendorInvoice::find()
        ->where('status != 0')
        ->andWhere(['fk_vendor_id' => $model->id])
        ->andWhere(['fk_location_id'=>$user_company])
        ->orderBy(['crt_time' => SORT_ASC])
        ->all();

        ?>

        <div class="card shadow rounded mt-2 mb-4">
          <div class="card-header p-3 border-bottom border-300 bg-warning-subtle">
            <div class="row g-3 justify-content-between align-items-center">
              <div class="col-12 col-md">
                <h4 class="text-900 mb-0" data-anchor="data-anchor">Purchase Invoices</h4>
              </div>
            </div>
          </div>
          <div class="card-body p-3">
            <div class="table-responsive">
              <table class="table table-bordered table-sm">
                <thead class="table-dark">
                  <tr>
                    <th>Invoice Number</th>
                    <th>Invoice Date</th>
                    <th>Due Date</th>
                    <th>Terms</th>
                    <th>Total Amount</th>
                    <th>Paid Amount</th>
                    <th>Pending Amount</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $invoiceIds = array_column($getinvoices, 'id');

                  $paidAmounts = [];

                  if (!empty($invoiceIds)) {
                    $paidRows = (new \yii\db\Query())
                    ->select([
                      'fk_vendor_invoice_id',
                      'paid_amount' => 'SUM(amount_received)'
                    ])
                    ->from('tbl_vendor_account_payable')
                    ->where(['status' => 1])
                    ->andWhere(['in', 'fk_vendor_invoice_id', $invoiceIds])
                    ->groupBy('fk_vendor_invoice_id')
                    ->all();

                    $paidAmounts = [];
                    foreach ($paidRows as $row) {
                      $paidAmounts[$row['fk_vendor_invoice_id']] = (float) $row['paid_amount'];
                    }


                  }
                  if (!empty($getinvoices)) {

                    foreach ($getinvoices as $invoice) {

                      // ---- TOTAL INVOICE AMOUNT ----
                      $totalAmount = (float) $invoice->total_amount;
                      $paidAmount  = isset($paidAmounts[$invoice->id])
                      ? (float) $paidAmounts[$invoice->id]
                      : 0;

                      $pendingAmount = max(0, $totalAmount - $paidAmount);
                      ?>
                      <tr>
                        <td><?= Html::encode($invoice->vendor_invoice_number) ?>  | <a href="<?=Url::to(['vendor-invoice/view','id'=>$invoice->id])?>" target="_blank"><i class="fa-solid fa-up-right-from-square"></i></a></td>

                        <td>
                          <?= $invoice->invoice_date?>
                        </td>

                        <td>
                          <?= $invoice->due_date ?>
                        </td>

                        <td></td>

                        <td >
                          <?= $totalAmount ?>
                        </td>

                        <td >
                          <?= $paidAmount ?>
                        </td>

                        <td >
                          <?= $pendingAmount ?>
                        </td>
                      </tr>
                      <?php
                    }

                  } else {
                    ?>
                    <tr>
                      <td colspan="7" class="text-center text-muted">
                        No purchase invoices found.
                      </td>
                    </tr>
                    <?php
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>


        <div class="card shadow rounded mt-2 mb-4">
          <div class="card-header p-3 border-bottom border-300 bg-success-subtle">
            <div class="row g-3 justify-content-between align-items-center">
              <div class="col-12 col-md">
                <h4 class="text-900 mb-0" data-anchor="data-anchor">Transactions</h4>
              </div>
            </div>
          </div>
          <div class="card-body p-3">
            <div class="table-responsive mt-0">
              <table class="table table-bordered table-sm">
                <thead class="table-dark">
                  <tr>
                    <th>Transaction Date</th>
                    <th>Transaction Type</th>
                    <th>Transaction Number</th>
                    <th>Total</th>
                    <th>Paid Status</th>
                    <th>Balance</th>
                  </tr>
                </thead>
                <tbody>
                  <?php



                  $invoice_arr = [];
                  $ar_arr = [];

                  if (isset($getinvoices) && count($getinvoices) > 0) {
                    foreach ($getinvoices as $gi) {
                      // Calculate total payments received for this invoice
                      $totalPayments = \app\models\TblVendorAccountPayable::find()
                      ->where(['fk_vendor_invoice_id' => $gi->id])
                      ->andWhere('status = 1')
                      ->sum('amount_received');

                      // Determine the paid status based on total amount and payments
                      if ($totalPayments >= $gi->total_amount) {
                        $paid_status = "Paid";
                      } elseif ($totalPayments > 0) {
                        $paid_status = "Partially Paid";
                      } else {
                        $paid_status = "Unpaid";
                      }

                      $invoice_arr[] = [
                        'date' => date('Y-m-d', strtotime($gi->crt_time)),
                        'type' => "Invoice",
                        'number' => $gi->vendor_invoice_number,
                        'amount' => $gi->total_amount,
                        'paid_status' => $paid_status
                      ];
                    }
                  }

                  $getar = \app\models\TblVendorAccountPayable::find()
                  ->where('status != 0')
                  ->andWhere('fk_vendor_invoice_id in (select id from tbl_vendor_invoice where fk_vendor_id = ' . $model->id . ' and status != 0 and fk_location_id = '.$user_company.')')
                  ->orderBy(['crt_time' => SORT_ASC])
                  ->all();

                  if (isset($getar) && count($getar) > 0) {
                    foreach ($getar as $gi) {
                      $getinvoice = \app\models\TblVendorInvoice::find()->where(['id' => $gi->fk_vendor_invoice_id])->one();
                      $invoice_no = isset($getinvoice) && $getinvoice->id != "" ? " (Invoice #" . $getinvoice->vendor_invoice_number . ")" : " (Invoice #.)";

                      $ar_arr[] = [
                        'date' => date('Y-m-d', strtotime($gi->crt_time)),
                        'type' => "Amount Paid",
                        'number' => $gi->id . $invoice_no,
                        'amount' => $gi->amount_received
                      ];
                    }
                  }

                  // Merge and sort arrays
                  $mergedArray = array_merge($invoice_arr, $ar_arr);
                  usort($mergedArray, function ($a, $b) {
                    return strtotime($a['date']) - strtotime($b['date']);
                  });

                  // Output
                  if (count($mergedArray) > 0) {
                    if ($mergedArray[0]['type'] == "Invoice") {
                      $starting_balance = $mergedArray[0]['amount'];
                    } elseif ($mergedArray[0]['type'] == "Amount Paid" || $mergedArray[0]['type'] == "Bad Debt.") {
                      $starting_balance = -$mergedArray[0]['amount'];
                    }
                    $i = 0;
                    foreach ($mergedArray as $v) {
                      if ($i > 0) {
                        // if ($v['type'] == "Invoice") {
                        //   $starting_balance += $v['amount'];
                        // } else {
                        //   $starting_balance -= $v['amount'];
                        // }
                        if ($v['type'] == "Invoice") {
                          $starting_balance = bcadd($starting_balance, $v['amount'], 2);
                        } else {
                          $starting_balance = bcsub($starting_balance, $v['amount'],2);
                        }
                      }
                      echo '<tr>
                      <td>' . $v['date'] . '</td>
                      <td>' . $v['type'] . '</td>
                      <td>' . $v['number'] . '</td>
                      <td>' . $v['amount'] . '</td>';
                      if ($v['type'] == "Invoice") {
                        echo '<td>' . $v['paid_status'] . '</td>';
                      } else {
                        echo '<td>-</td>';
                      }
                      echo '<td>' . $starting_balance . '</td>
                      </tr>';
                      $i++;
                    }
                  }else{
                    echo '<tr>
                    <td colspan="6">
                    No transactions yet.
                    </td>
                    </tr>';
                  }

                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <?php
      }
      ?>
      <div class="modal fade" id="vendorStatementModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="vendorStatementModalLabel">Vendor Statement</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div id="vendorStatementModalContent"></div>
            </div>
          </div>
        </div>
      </div>
      <?php
      $this->registerJs(<<<JS
        $('#btn-generate-statement').on('click', function () {
          let url = $(this).data('url');

          $('#vendorStatementModal').modal('show')
          .find('#vendorStatementModalContent')
          .html('<div class="text-center p-4">Loading...</div>')
          .load(url);
        });
        JS);
        ?>



        <div class="card shadow rounded mt-2 mb-4">
          <div class="card-header p-3 border-bottom border-300 bg-danger-subtle">
            <div class="row g-3 justify-content-between align-items-center">
              <div class="col-12 col-md">
                <h5 class="text-900 mb-0">Expenses</h5>
              </div>
            </div>
          </div>
          <div class="card-body p-3 table-responsive">
            <table class="table table-bordered table-striped display" id="expense_table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Expense Category</th>
                  <th>Receipt No.</th>
                  <th>Date</th>
                  <th>Amount</th>
                  <th>Tax</th>
                  <th>Final Amount</th>
                  <th>Payment Method</th>
                  <th>Notes</th>
                  <th>File(s)</th>
                </tr>
              </thead>
              <tbody>
                <?php
                //---GET THE EXPENSES AS PER VENDOR ----
                $getexpenses = \app\models\TblExpense::find()->where(['fk_vendor_id'=>$model->id,'status'=>1])->orderBy(['date'=>SORT_DESC])->all();
                if(isset($getexpenses) && count($getexpenses) > 0){
                  foreach($getexpenses as $index => $g){
                    $cat = $g->expenseCategory->title ?? '';
                    $pay_method = $g->payMethod->title ?? '';
                    $location = $g->location->title ?? '';
                    $tax_rate = $g->taxRate->tax_rate ? $g->taxRate->tax_rate.'%' : '';
                    if (empty($g->expenseFiles)) {
                        $files = '<span class="text-muted">—</span>';
                    }else{
                      $count = count($g->expenseFiles);
                      $files = Html::button(
                          'View Files ('.$count.')',
                          [
                              'class' => 'btn btn-sm btn-primary expense-file-preview',
                              'data-files' => json_encode(array_map(function ($file) {
                                  return [
                                      'url' => 'https://hkmis.ca/web/expenses/' . $file->file_upload,
                                      'type' => strtolower(pathinfo($file->file_upload, PATHINFO_EXTENSION))
                                  ];
                              }, $g->expenseFiles)),
                          ]
                      );
                    }

                    echo '<tr>
                    <td>'.($index+1).' | <a class="unformat text-sm" href="'.Url::to(['expense/view','id'=>$g->id]).'" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a></td>
                    <td>'.$cat.'</td>
                    <td>'.$g->receipt_no.'</td>
                    <td>'.$g->date.'</td>
                    <td>'.$g->amount.'</td>
                    <td>'.$g->tax_amount.' ('.$tax_rate.')'.'</td>
                    <td>'.$g->final_amount.'</td>
                    <td>'.$pay_method.'</td>
                    <td>'.$g->notes.'</td>
                    <td>'.$files.'</td>
                    </tr>';
                  }
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal fade" id="expenseFileModal" tabindex="-1">
          <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Expense File</h5>
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
                                Open / Download
                            </a>
                        </div>
                `;

                if (file.type === 'pdf') {
                    html += `
                        <iframe src="${file.url}" style="width:100%; height:65vh;" frameborder="0"></iframe>
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
