<?php
$this->title = "AP Aging Report";
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\TblTerms;
use app\models\TblVendor;
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
    <form name="frm_reservation" id="" action="<?= Yii::$app->urlManager->createUrl(['report/ap-aging-report']) ?>" method="post" autocomplete="off">
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
              <label>Vendor<span class="text-danger">*</span></label>
              <select class="form-control" id="vendor" name="vendor" required="required" data-choices='data-choices' data-options='{"removeItemButton":true,"placeholder":true}'>
                <option value="">Select</option>
                <option value="0" <?php if(isset($vendor)){ if($vendor == 0){ echo 'selected="selected"';}}else{ echo 'selected="selected"';}?>>All</option>
                <?php
                $allclients = TblVendor::find()->where('status != 0')->orderBy(['company_name'=>SORT_ASC])->all();
                if(isset($allclients) && count($allclients) > 0){
                  foreach($allclients as $ac){
                    if(isset($vendor) && $vendor == $ac->id){
                      echo '<option value="'.$ac->id.'" selected="selected">'.$ac->company_name.'</option>';
                    }else{
                      echo '<option value="'.$ac->id.'">'.$ac->company_name.'</option>';
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
          <table id="example" class="display table table-hover table-bordered border border-1" data-page-length="20">
            <thead>
              <tr>
                <th>Vendor Name</th>
                <th>Invoices Total Amount</th>
                <th>Total Amount Paid</th>
                <th>Total Outstanding Amount</th>
                <th>Current (0-15 days)</th>
                <th>16-30 days</th>
                <th>31-60 days</th>
                <th>61-90 days</th>
                <th>Over 90 days</th>
              </tr>
              <tr>
                <td class="filterhead">Customer Name</td>
                <td class="filterhead">Total Amount</td>
                <td class="filterhead">Amount Paid</td>
                <td class="filterhead">Outstanding Amount</td>
                <td class="filterhead">Current (0-15 days)</td>
                <td class="filterhead">16-30 days</td>
                <td class="filterhead">31-60 days</td>
                <td class="filterhead">61-90 days</td>
                <td class="filterhead">Over 90 days</td>
              </tr>
            </thead>
            <tbody>
              <?php
              use yii\db\Query;
              if (!empty($from_date) && !empty($to_date)) {
                // Subquery to calculate the total amount received per invoice
                $subquery = (new Query())
                ->select([
                  'i.fk_vendor_id',
                  'SUM(ir.amount_received) AS total_amount_received'
                ])
                ->from('tbl_vendor_account_payable ir')
                ->innerJoin('tbl_vendor_invoice i', 'i.id = ir.fk_vendor_invoice_id')
                ->where(['!=', 'ir.status', 0])
                ->groupBy('i.fk_vendor_id');
                if (!empty($from_date) && !empty($to_date)) {
                  $subquery->andWhere(['between', 'i.invoice_date', $from_date, $to_date]);
                }


                // $subquery_credit = (new Query())
                //     ->select([
                //         'i.fk_vendor_id',
                //         'SUM(ir_credit.amount_applied) AS total_credit_applied'
                //     ])
                //     ->from('tbl_vendor_invoice_credit_applied ir_credit')
                //     ->innerJoin('tbl_vendor_invoice i', 'i.id = ir_credit.fk_vendor_invoice_id')  // Ensure we get the client_id
                //     ->where(['ir_credit.status' => 1])
                //     ->groupBy('i.fk_vendor_id');
                //     if (!empty($from_date) && !empty($to_date)) {
                //       $subquery_credit->andWhere(['between', 'i.invoice_date', $from_date, $to_date]);
                //     }
                $query = (new Query())
                ->select([
                  'c.company_name',
                  'c.id as cust_id',
                  'SUM(i.total_amount) AS total_invoice_amount',
                  'COALESCE(SUM(ir.total_amount_received), 0) AS total_amount_received',
                  'SUM(i.total_amount) - COALESCE(SUM(ir.total_amount_received), 0) AS total_outstanding_amount',
                  'SUM(CASE
                  WHEN DATEDIFF(CURDATE(), i.due_date) BETWEEN 0 AND 15 THEN (i.total_amount - COALESCE(ir.total_amount_received, 0))
                  ELSE 0
                  END) AS current',
                  'SUM(CASE
                  WHEN DATEDIFF(CURDATE(), i.due_date) BETWEEN 16 AND 30 THEN (i.total_amount - COALESCE(ir.total_amount_received, 0))
                  ELSE 0
                  END) AS days_16_30',
                  'SUM(CASE
                  WHEN DATEDIFF(CURDATE(), i.due_date) BETWEEN 31 AND 60 THEN (i.total_amount - COALESCE(ir.total_amount_received, 0))
                  ELSE 0
                  END) AS days_31_60',
                  'SUM(CASE
                  WHEN DATEDIFF(CURDATE(), i.due_date) BETWEEN 61 AND 90 THEN (i.total_amount - COALESCE(ir.total_amount_received, 0))
                  ELSE 0
                  END) AS days_61_90',
                  'SUM(CASE
                  WHEN DATEDIFF(CURDATE(), i.due_date) > 90 THEN (i.total_amount - COALESCE(ir.total_amount_received, 0))
                  ELSE 0
                  END) AS over_90_days'
                ])
                ->from('tbl_vendor_invoice i')
                // ->leftJoin(['ir' => $subquery], 'i.id = ir.fk_vendor_invoice_id')
                ->leftJoin(['ir' => $subquery], 'i.fk_vendor_id = ir.fk_vendor_id')  // Join on client_id instead of invoice_id
                // ->leftJoin(['ir_credit' => $subquery_credit], 'i.fk_vendor_id = ir_credit.fk_vendor_id')  // Join on client_id instead of invoice_id
                ->leftJoin('tbl_vendor c', 'i.fk_vendor_id = c.id')
                ->where(['!=', 'i.status', 0])
                ->andWhere(['i.fk_location_id'=>$location_selected])
                ->groupBy([
                  'c.company_name',
                  'c.id'
                ])
                ->having('total_outstanding_amount > 0');

                // Apply filters
                if (!empty($from_date) && !empty($to_date)) {
                  $query->andWhere(['between', 'i.invoice_date', $from_date, $to_date]);
                }


                if (!empty($vendor)) {
                  if($vendor != 0){
                    $query->andWhere(['c.id' => $vendor]);
                  }

                }

                // Execute the query and fetch results
                $results = $query->all();
                foreach($results as $r){
                  // echo $r['name'].' '.$r['id'].' '.$r['serial_number'].'<br>';
                  echo '<tr>
                  <td>'.$r['company_name'].' |
                  <a target="_blank" href="'.Url::to(['report/ap-aging-report-details','customer_id'=>$r['cust_id'],'start_date'=>$from_date,'end_date'=>$to_date]).'"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a></td>
                  <td>'.$r['total_invoice_amount'].'</td>
                  <td>'.$r['total_amount_received'].'</td>
                  <td>'.$r['total_outstanding_amount'].'</td>
                  <td>'.$r['current'].'</td>
                  <td>'.$r['days_16_30'].'</td>
                  <td>'.$r['days_31_60'].'</td>
                  <td>'.$r['days_61_90'].'</td>
                  <td>'.$r['over_90_days'].'</td>
                  </tr>';
                }
              }
              ?>
            </tbody>
            <tfoot>
              <tr>
                <th></th>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>7</th>
                <th>8</th>
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
      var table = $('#example').DataTable({
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

          // Total over all pages
          total_cr = api
          .column(7) // Change this to your totals column index
          .data()
          .reduce((a, b) => intVal(a) + intVal(b), 0);

          // Total over this page
          pageTotal_cr = api
          .column(7, { search: 'applied' }) // Filtered data total
          .data()
          .reduce((a, b) => intVal(a) + intVal(b), 0);
          // Update footer
          $(api.column(7).footer()).html(
            'Total: $' + formatNumber(pageTotal_cr) + ' (All: $' + formatNumber(total_cr) + ')'
          );

          // Total over all pages
          total_16 = api
          .column(8) // Change this to your totals column index
          .data()
          .reduce((a, b) => intVal(a) + intVal(b), 0);

          // Total over this page
          pageTotal_16 = api
          .column(8, { search: 'applied' }) // Filtered data total
          .data()
          .reduce((a, b) => intVal(a) + intVal(b), 0);
          // Update footer
          $(api.column(8).footer()).html(
            'Total: $' + formatNumber(pageTotal_16) + ' (All: $' + formatNumber(total_16) + ')'
          );

          // Total over all pages
          total_31 = api
          .column(1) // Change this to your totals column index
          .data()
          .reduce((a, b) => intVal(a) + intVal(b), 0);

          // Total over this page
          pageTotal_31 = api
          .column(1, { search: 'applied' }) // Filtered data total
          .data()
          .reduce((a, b) => intVal(a) + intVal(b), 0);
          // Update footer
          $(api.column(1).footer()).html(
            'Total: $' + formatNumber(pageTotal_31) + ' (All: $' + formatNumber(total_31) + ')'
          );


          // Total over all pages
          total_61 = api
          .column(2) // Change this to your totals column index
          .data()
          .reduce((a, b) => intVal(a) + intVal(b), 0);

          // Total over this page
          pageTotal_61 = api
          .column(2, { search: 'applied' }) // Filtered data total
          .data()
          .reduce((a, b) => intVal(a) + intVal(b), 0);
          // Update footer
          $(api.column(2).footer()).html(
            'Total: $' + formatNumber(pageTotal_61) + ' (All: $' + formatNumber(total_61) + ')'
          );

          // Total over all pages
          total_90 = api
          .column(3) // Change this to your totals column index
          .data()
          .reduce((a, b) => intVal(a) + intVal(b), 0);

          // Total over this page
          pageTotal_90 = api
          .column(3, { search: 'applied' }) // Filtered data total
          .data()
          .reduce((a, b) => intVal(a) + intVal(b), 0);
          // Update footer
          $(api.column(3).footer()).html(
            'Total: $' + formatNumber(pageTotal_90) + ' (All: $' + formatNumber(total_90) + ')'
          );

        }
      });
      $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1 btn-sm');
      $('#example thead .filterhead').each( function () {
        var title = $(this).text();
        $(this).html( '<input type=\"text\" class=\"column_search form-control\"/>' );
      } );
      // Apply the search
      $( '#example thead'  ).on( 'keyup', '.column_search',function () {
        table
        .column( $(this).parent().index() )
        .search( this.value )
        .draw();
      } );
      // Restore state
      var state = table.state.loaded();
      if ( state ) {

        $('#example thead .filterhead').each( function () {
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
