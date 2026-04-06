<?php
$this->title = "Unpaid Invoice Report";
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\TblTerms;
use app\models\TblClient;
use app\models\TblUser;
use app\models\TblInvoice;
use app\models\TblLocation;
use app\models\TblEmployee;
use yii\db\Expression;
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
    <form name="frm_reservation" id="" action="<?= Yii::$app->urlManager->createUrl(['report/unpaid-invoice']) ?>" method="post" autocomplete="off">
      <input type="hidden" name="_csrf-backend" value="<?=Yii::$app->request->getCsrfToken()?>" />
      <div class="row">
        <div class="col-lg-2">
          <label for="">From Date<span class="text-danger">*</span></label>
          <input type="text" class="form-control datetimepicker" required="required" value="<?php if(isset($from_date)){ echo $from_date;} ?>" name="from_date" data-options='{"enableTime":false,"dateFormat":"Y-m-d","disableMobile":true}'>
          </div>
          <div class="col-lg-2">
            <label for="">To Date<span class="text-danger">*</span></label>
            <input type="text" class="form-control datetimepicker" required="required" value="<?php if(isset($to_date)){ echo $to_date;} ?>" name="to_date" data-options='{"enableTime":false,"dateFormat":"Y-m-d","disableMobile":true}'>
            </div>
            <div class="col-md-3">
              <label>Customer<span class="text-danger">*</span></label>
              <select class="form-control" id="customer" name="customer" required="required" data-choices='data-choices' data-options='{"removeItemButton":true,"placeholder":true}'>
                <option value="">Select</option>
                <option value="0" <?php if(isset($customer)){ if($customer == 0){ echo 'selected="selected"';}}else{ echo 'selected="selected"';}?>>All</option>
                <?php
                $allclients = TblClient::find()->where('status != 0')->orderBy(['company_name'=>SORT_ASC])->all();
                if(isset($allclients) && count($allclients) > 0){
                  foreach($allclients as $ac){
                    if(isset($customer) && $customer == $ac->id){
                      echo '<option value="'.$ac->id.'" selected="selected">'.$ac->company_name.'</option>';
                    }else{
                      echo '<option value="'.$ac->id.'">'.$ac->company_name.'</option>';
                    }
                  }
                }
                ?>
              </select>
            </div>
            <div class="col-md-3">
              <label>Created By<span class="text-danger">*</span></label>
              <select class="form-select" id="crt_by" name="crt_by" required="required">
                <option value="">Select</option>
                <option value="0" <?php if(isset($crt_by)){ if($crt_by == 0){ echo 'selected="selected"';}}else{ echo 'selected="selected"';}?>>All</option>
                <?php
                $allusers = TblUser::find()
                            ->where(['status'=>1])
                            // ->andWhere('fk_employee_id in (select id from tbl_employee where status = 1 and fk_location_id = '.$location_selected.')')
                            ->orderBy(['username'=>SORT_ASC])->all();
                if(isset($allusers) && count($allusers) > 0){
                  foreach($allusers as $at){
                    if($at->alias != ""){
                      $name = $at->alias;
                    }else{
                      $name = $at->username;
                    }
                    if(isset($crt_by) && $crt_by == $at->id){
                      echo '<option value="'.$at->id.'" selected="selected">'.$name.'</option>';
                    }else{
                      echo '<option value="'.$at->id.'">'.$name.'</option>';
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
        </form>
        <hr />
        <div class="table-responsive">
          <table id="invoice-list" class="display table table-hover table-bordered" data-page-length="20">
            <thead>
              <tr>
                <th>Customer Details</th>
                <th>Invoice Number</th>
                <th>Invoice Date</th>
                <th>Due Date</th>
                <th>Invoice Amount</th>
                <th>Total Amount Received</th>
                <!-- <th>Credit Applied</th> -->
                <th>Outstanding Amount</th>
              </tr>
              <tr>
                <td class="filterhead">Customer Details</td>
                <td class="filterhead">Invoice Number</td>
                <td class="filterhead">Invoice Date</td>
                <td class="filterhead">Due Date</td>
                <td class="filterhead">Invoice Amount</td>
                <td class="filterhead">Total Amount Received</td>
                <!-- <td class="filterhead">Credit Applied</td> -->
                <td class="filterhead">Outstanding Amount</td>
              </tr>
            </thead>
            <tbody>
              <?php
              use yii\db\Query;
              // Apply filters
              if (!empty($from_date) && !empty($to_date)) {

                $subQueryReceivables = (new Query())
                ->select(['fk_invoice_id', new Expression('SUM(amount_received) AS total_amount_received')])
                ->from('tbl_account_receivable')
                ->where(['status' => 1])
                ->groupBy('fk_invoice_id');

                // $subQueryCredits = (new Query())
                // ->select(['fk_invoice_id', new Expression('SUM(amount_applied) AS total_amount_credit')])
                // ->from('tbl_invoice_credit_applied')
                // ->where(['status' => 1])
                // ->groupBy('fk_invoice_id');

                $query = (new Query())
                ->select([
                  'c.company_name',
                  'c.id',
                  'i.id AS invoice_id',
                  'i.invoice_number',
                  'i.invoice_date',
                  'i.due_date',
                  'i.total_amount',
                  'COALESCE(ar.total_amount_received, 0) AS total_amount_received',
                  new Expression('i.total_amount - COALESCE(ar.total_amount_received, 0) AS outstanding_amount')
                ])
                ->from('tbl_invoice i')
                ->leftJoin('tbl_client c', 'i.fk_client_id = c.id')
                ->leftJoin(['ar' => $subQueryReceivables], 'ar.fk_invoice_id = i.id')
                // ->leftJoin(['cr' => $subQueryCredits], 'cr.fk_invoice_id = i.id')
                ->where('i.status != 0')
                // ->andWhere(['i.fk_location_id'=>$location_selected])
                ->groupBy([
                  'c.company_name',
                  'c.id',
                  'i.invoice_number',
                  'i.invoice_date',
                  'i.due_date',
                  'i.total_amount'
                ])
                ->having('outstanding_amount > 0')
                ->orderBy(['due_date'=>SORT_DESC]);
                $query->andWhere(['between', 'i.invoice_date', $from_date, $to_date]);
                if (!empty($customer)) {
                  if($customer != 0){
                    $query->andWhere(['c.id' => $customer]);
                  }

                }
                // Execute the query and fetch results
                $results = $query->all();
                foreach($results as $r){
                  echo '<tr>
                  <td>'.$r['company_name'].'</td>
                  <td>'.$r['invoice_number'].' |
                  <a target="_blank" href="'.Url::to(['invoice/view','id'=>$r['invoice_id']]).'"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a></td>
                  <td>'.$r['invoice_date'].'</td>
                  <td>'.$r['due_date'].'</td>
                  <td>'.$r['total_amount'].'</td>
                  <td>'.$r['total_amount_received'].'</td>
                  <td>'.$r['outstanding_amount'].'</td>
                  </tr>';
                }
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
      var table = $('#invoice-list').DataTable({
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
      $('#invoice-list thead .filterhead').each( function () {
        var title = $(this).text();
        $(this).html( '<input type=\"text\" class=\"column_search form-control\"/>' );
      } );
      // Apply the invoice
      $( '#invoice-list thead'  ).on( 'keyup', '.column_search',function () {
        table
        .column( $(this).parent().index() )
        .search( this.value )
        .draw();
      } );
      // Restore state
      var state = table.state.loaded();
      if ( state ) {

        $('#invoice-list thead .filterhead').each( function () {
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
