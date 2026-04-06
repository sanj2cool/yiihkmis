<?php
$this->title = "AP Payment Schedule Report";
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\TblTerms;
use app\models\TblVendor;
use app\models\TblUser;
use app\models\TblVendorInvoice;
use app\models\TblVendorAccountPayable;
use app\models\TblPaymentMethodVendor;
use app\models\TblLocation;
use app\models\TblEmployee;

$session = Yii::$app->session;
$employee_id = $session['employeeId'];
$getrole = TblEmployee::find()->where(['id'=>$employee_id,'status'=>1])->one();
$location_selected = 0;
if(isset($getrole) && $getrole->fk_location_id != ""){
  $location_selected = $getrole->fk_location_id;
}
?>
<div class="card">
  <div class="card-header bg-light">
    <div class="row align-items-center">
      <div class="col">
        <h5 class="mb-0"><?= Html::encode($this->title) ?></h5>
      </div>
    </div>
  </div>
  <div class="card-body border-top">
    <form name="frm_reservation" id="" action="<?= Yii::$app->urlManager->createUrl(['report/ap-payment-schedule']) ?>" method="post" autocomplete="off">
      <input type="hidden" name="_csrf-backend" value="<?=Yii::$app->request->getCsrfToken()?>" />
      <div class="row justify-content-center">
        <div class="col-lg-2">
          <label for="">From Date<span class="text-danger">*</span></label>
          <input type="text" class="form-control datetimepicker" required="required" value="<?php if(isset($from_date)){ echo $from_date;} ?>" name="from_date" data-options='{"enableTime":false,"dateFormat":"Y-m-d","disableMobile":true}'>
          </div>
          <div class="col-lg-2">
            <label for="">To Date<span class="text-danger">*</span></label>
            <input type="text" class="form-control datetimepicker" required="required" value="<?php if(isset($to_date)){ echo $to_date;} ?>" name="to_date" data-options='{"enableTime":false,"dateFormat":"Y-m-d","disableMobile":true}'>
            </div>
            <div class="col-md-3">
              <label>Vendor<span class="text-danger">*</span></label>
              <select class="form-control" id="vendor" name="vendor" required="required" data-choices='data-choices' data-options='{"removeItemButton":true,"placeholder":true}'>
                <option value="">Select</option>
                <option value="0" <?php if(isset($vendor)){ if($vendor == 0){ echo 'selected="selected"';}}else{ echo 'selected="selected"';}?>>All</option>
                <?php
                $allvendors = TblVendor::find()->where(['status'=>1])->all();
                if(isset($allvendors) && count($allvendors) > 0){
                  foreach($allvendors as $at){
                    if(isset($vendor) && $vendor == $at->id){
                      echo '<option value="'.$at->id.'" selected="selected">'.$at->company_name.'</option>';
                    }else{
                      echo '<option value="'.$at->id.'">'.$at->company_name.'</option>';
                    }
                  }
                }
                ?>
              </select>
            </div>
            <div class="col-md-2 pt-4">
              <button type="submit" class="btn btn-primary">Search</button>
            </div>
          </div>
          <div class="row">
            <div class="col-6 text-danger" style="font-size:12px;" align="center">
              *From and To Dates filters are for Invoice Due Date
            </div>
          </div>
        </form>
        <hr />
        <div class="table-responsive">
          <table id="customer-list" class="display table table-hover table-bordered border border-1" data-page-length="20">
            <thead>
              <tr>
                <th>Vendor Details</th>
                <th>Invoice Number</th>
                <th>Invoice Date</th>
                <th>Due Date</th>
                <th>Invoice Amount</th>
                <th>Amount Paid</th>
                <th>Outstanding Amount</th>
                <th>Payment Status</th>
              </tr>
              <tr>
                <td class="filterhead">Vendor Details</td>
                <td class="filterhead">Invoice Number</td>
                <td class="filterhead">Invoice Date</td>
                <td class="filterhead">Due Date</td>
                <td class="filterhead">Invoice Amount</td>
                <td class="filterhead">Amount Paid</td>
                <td class="filterhead">Outstanding Amount</td>
                <td class="filterhead">Payment Status</td>
              </tr>
            </thead>
            <tbody>
              <?php
              use yii\db\Query;
              // Subquery to calculate the total amount paid per invoice
              $subquery = (new Query())
              ->select([
                'ap.fk_vendor_invoice_id',
                'SUM(ap.amount_received) AS total_amount_paid'
              ])
              ->from('tbl_vendor_account_payable ap')
              ->groupBy('ap.fk_vendor_invoice_id');

              // Main query
              $query = (new Query())
              ->select([
                'v.company_name AS vendor_name',
                'v.id AS vendor_id',
                'i.id as invoice_id',
                'i.vendor_invoice_number',
                'i.invoice_date',
                'i.due_date',
                'i.total_amount AS invoice_amount',
                'COALESCE(ap.total_amount_paid, 0) AS amount_paid',
                'i.total_amount - COALESCE(ap.total_amount_paid, 0) AS outstanding_amount',
                'CASE
                WHEN i.total_amount - COALESCE(ap.total_amount_paid, 0) = 0 THEN \'Paid\'
                WHEN i.total_amount - COALESCE(ap.total_amount_paid, 0) > 0 AND COALESCE(ap.total_amount_paid, 0) > 0 THEN \'Partially Paid\'
                ELSE \'Unpaid\'
                END AS payment_status'
              ])
              ->from('tbl_vendor_invoice i')
              ->leftJoin('tbl_vendor v', 'i.fk_vendor_id = v.id')
              ->leftJoin(['ap' => $subquery], 'i.id = ap.fk_vendor_invoice_id')
              ->where(['!=', 'i.status', 0])
              // ->andWhere(['i.fk_location_id'=>$location_selected])
              ->groupBy([
                'v.company_name',
                'v.id',
                'i.vendor_invoice_number',
                'i.invoice_date',
                'i.due_date',
                'i.total_amount',
                'ap.total_amount_paid'
              ])
              ->orderBy(['i.due_date' => SORT_ASC]);

              // Apply date range filter if provided
              if (!empty($from_date) && !empty($to_date)) {
                $query->andWhere(['between', 'i.due_date', $from_date, $to_date]);
              }

              // Apply vendor filter if provided
              if (!empty($vendor) && $vendor != 0) {
                $query->andWhere(['v.id' => $vendor]);
              }

              // Execute the query and fetch results
              $results = $query->all();
              foreach($results as $r){
                // echo $r['name'].' '.$r['id'].' '.$r['serial_number'].'<br>';
                echo '<tr>
                <td>'.$r['vendor_name'].'</td>
                <td>'.$r['vendor_invoice_number'].' |
                <a target="_blank" href="'.Url::to(['vendor-invoice/view','id'=>$r['invoice_id']]).'"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a></td>
                <td>'.$r['invoice_date'].'</td>
                <td>'.$r['due_date'].'</td>
                <td>'.$r['invoice_amount'].'</td>
                <td>'.$r['amount_paid'].'</td>
                <td>'.$r['outstanding_amount'].'</td>
                <td>'.$r['payment_status'].'</td>
                </tr>';
              }

              ?>
            </tbody>
            <tfoot>
              <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
    <?php
    $this->registerCssFile('https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css');
    $this->registerJsFile('https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js',['depends' => [yii\web\JqueryAsset::className()]]);
    $this->registerJsFile('https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js',['depends' => [yii\web\JqueryAsset::className()]]);
    $this->registerJsFile('https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js',['depends' => [yii\web\JqueryAsset::className()]]);
    $this->registerJsFile('https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js',['depends' => [yii\web\JqueryAsset::className()]]);
    $this->registerJs("
    $(document).ready(function() {
      // DataTable
      var table = $('#customer-list').DataTable({
        dom: 'Bfrtip',
        buttons: [
          'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        columnDefs: [{
          targets: \"_all\",
          orderable: true
        }],
        orderCellsTop: true,
        stateSave: true,
        footerCallback: function (row, data, start, end, display) {
          var api = this.api(), data;

          // Remove formatting to get integer data
          var intVal = function (i) {
            return typeof i === 'string'
            ? i.replace(/[\$,]/g, '') * 1
            : typeof i === 'number'
            ? i
            : 0;
          };

          // Total over all pages
          total = api
          .column(4) // Change this to your totals column index
          .data()
          .reduce((a, b) => intVal(a) + intVal(b), 0);

          // Total over this page
          pageTotal = api
          .column(4, { search: 'applied' }) // Filtered data total
          .data()
          .reduce((a, b) => intVal(a) + intVal(b), 0);
          var formatNumber = function (num) {
            return num.toLocaleString(undefined, {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2 // Allows up to 2 decimal places but not forced
            });
          };
          // Update footer
          $(api.column(4).footer()).html(
            'Total: $' + formatNumber(pageTotal) + ' (All: $' + formatNumber(total) + ')'
          );

          // Total over all pages
          total_ar = api
          .column(5) // Change this to your totals column index
          .data()
          .reduce((a, b) => intVal(a) + intVal(b), 0);

          // Total over this page
          pageTotal_ar = api
          .column(5, { search: 'applied' }) // Filtered data total
          .data()
          .reduce((a, b) => intVal(a) + intVal(b), 0);
          // Update footer
          $(api.column(5).footer()).html(
            'Total: $' + formatNumber(pageTotal_ar) + ' (All: $' + formatNumber(total_ar) + ')'
          );

          // Total over all pages
          total_oa = api
          .column(6) // Change this to your totals column index
          .data()
          .reduce((a, b) => intVal(a) + intVal(b), 0);

          // Total over this page
          pageTotal_oa = api
          .column(6, { search: 'applied' }) // Filtered data total
          .data()
          .reduce((a, b) => intVal(a) + intVal(b), 0);
          // Update footer
          $(api.column(6).footer()).html(
            'Total: $' + formatNumber(pageTotal_oa) + ' (All: $' + formatNumber(total_oa) + ')'
          );



        }
      });
      $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1 btn-sm');
      $('#customer-list thead .filterhead').each( function () {
        var title = $(this).text();
        $(this).html( '<input type=\"text\" class=\"column_search form-control\"/>' );
      } );
      // Apply the search
      $( '#customer-list thead'  ).on( 'keyup', '.column_search',function () {
        table
        .column( $(this).parent().index() )
        .search( this.value )
        .draw();
      } );
      // Restore state
      var state = table.state.loaded();
      if ( state ) {

        $('#customer-list thead .filterhead').each( function () {
          var colSearch = state.columns[$(this).index()].search;

          if ( colSearch.search ) {
            $( 'input',this ).val( colSearch.search );
          }
        } );
        table.draw();
      }
    });
    ");
    ?>
