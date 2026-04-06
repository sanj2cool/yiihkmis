<?php
$this->title = "Detailed Sales Invoice Report";
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\TblTerms;
use app\models\TblClient;
use app\models\TblUser;
use app\models\TblInvoice;
use app\models\TblLocation;
use app\models\TblEmployee;

$session = Yii::$app->session;
$user_company = $session['userCompany'];
?>
<div class="card mb-4">
  <div class="card-header bg-light">
    <div class="row align-items-center">
      <div class="col">
        <h5 class="mb-0"><?= Html::encode($this->title) ?></h5>
      </div>
    </div>
  </div>
  <div class="card-body border-top">
    <form name="frm_reservation" id="" action="<?= Yii::$app->urlManager->createUrl(['report/detailed-sales-invoice']) ?>" method="post" autocomplete="off">
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
                $allclients = TblClient::find()->where('status != 0')->andWhere(['fk_location_id'=>$user_company])->orderBy(['company_name'=>SORT_ASC])->all();
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
          <table id="invoice-list" class="display table table-hover table-bordered border border-1" data-page-length="20">
            <thead>
              <tr>
                <th>Customer Details</th>
                <th>Invoice Number</th>
                <th>Invoice Date</th>
                <th>Due Date</th>
                <th>Item Description</th>
                <th>Quantity</th>
                <th>Rate</th>
                <th>Total Price</th>
                <th>Invoice Amount</th>
                <th>Total Amount Received</th>
                <th>Outstanding Amount</th>
                <th>Payment Status</th>
              </tr>
              <tr>
                <td class="filterhead">Customer Details</td>
                <td class="filterhead">Invoice Number</td>
                <td class="filterhead">Invoice Date</td>
                <td class="filterhead">Due Date</td>
                <td class="filterhead">Item Description</td>
                <td class="filterhead">Quantity</td>
                <td class="filterhead">Rate</td>
                <td class="filterhead">Total Price</td>
                <td class="filterhead">Invoice Amount</td>
                <td class="filterhead">Total Amount Received</td>
                <td class="filterhead">Outstanding Amount</td>
                <td class="filterhead">Payment Status</td>
              </tr>
            </thead>
            <tbody>
              <?php
              use yii\db\Query;
              // Apply filters
              if (!empty($from_date) && !empty($to_date)) {

                // Subquery to calculate the total amount received per invoice
                $subquery = (new Query())
                ->select([
                  'ir.fk_invoice_id',
                  'SUM(ir.amount_received) AS total_amount_received'
                ])
                ->from('tbl_account_receivable ir')
                ->where(['!=', 'ir.status', 0])
                ->groupBy('ir.fk_invoice_id');

                // Main query
                $query = (new Query())
                ->select([
                  'c.company_name',
                  'c.id',
                  'i.id AS invoice_id',
                  'i.invoice_number',
                  'i.invoice_date',
                  'i.due_date',
                  'ii.item',
                  'ii.description',
                  'p.name as product_name',
                  'ii.fk_product_id',
                  'ii.quantity AS Qty',
                  'ii.unit_price AS UnitPrice',
                  'ii.total_price AS total_price',
                  'i.total_amount',
                  'COALESCE(ir.total_amount_received, 0) AS total_amount_received',
                  'i.total_amount - COALESCE(ir.total_amount_received, 0) AS outstanding_amount',
                  'CASE
                  WHEN i.total_amount - COALESCE(ir.total_amount_received, 0) = 0 THEN \'Paid\'
                  WHEN i.total_amount - COALESCE(ir.total_amount_received, 0) > 0 AND COALESCE(ir.total_amount_received, 0) > 0 THEN \'Partially Paid\'
                  ELSE \'Unpaid\'
                  END AS payment_status'
                ])
                ->from('tbl_invoice i')
                ->leftJoin('tbl_client c', 'i.fk_client_id = c.id')
                ->leftJoin('tbl_invoice_item ii', 'i.id = ii.fk_invoice_id')
                ->leftJoin('tbl_product p', 'p.id = ii.fk_product_id')
                ->leftJoin(['ir' => $subquery], 'i.id = ir.fk_invoice_id')
                ->where('i.status != 0')
                ->andWhere(['i.fk_bill_from_id' => $user_company])
                ->groupBy([
                  'c.company_name',
                  'c.id',
                  'i.id',
                  'i.invoice_number',
                  'i.invoice_date',
                  'i.due_date',
                  'ii.item',
                  'ii.quantity',
                  'ii.unit_price',
                  'i.total_amount',
                  'ir.total_amount_received'
                ])
                ->orderBy(['invoice_date' => SORT_DESC]);

                $query->andWhere(['between', 'i.invoice_date', $from_date, $to_date]);

                if (!empty($customer) && $customer != 0) {
                  $query->andWhere(['c.id' => $customer]);
                }

                // Execute the query and fetch results
                $results = $query->all();

                foreach($results as $r){
                  echo '<tr>
                  <td>'.$r['company_name'].'</td>
                  <td>'.$r['invoice_number'].' |
                  <a target="_blank" href="'.Url::to(['invoice/update','id'=>$r['invoice_id']]).'"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a></td>
                  <td>'.$r['invoice_date'].'</td>
                  <td>'.$r['due_date'].'</td>
                  <td>'.$r['item'].' '.$r['product_name'].'<br />'.$r['description'].'</td>
                  <td>'.$r['Qty'].'</td>
                  <td>'.$r['UnitPrice'].'</td>
                  <td>'.$r['total_price'].'</td>
                  <td>'.$r['total_amount'].'</td>
                  <td>'.$r['total_amount_received'].'</td>
                  <td>'.$r['outstanding_amount'].'</td>
                  <td>'.$r['payment_status'].'</td>
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
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>8</th>
                <th>9</th>
                <th>10</th>
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


          var formatNumber = function (num) {
            return num.toLocaleString(undefined, {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2 // Allows up to 2 decimal places but not forced
            });
          };


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
          .column(9) // Change this to your totals column index
          .data()
          .reduce((a, b) => intVal(a) + intVal(b), 0);

          // Total over this page
          pageTotal_31 = api
          .column(9, { search: 'applied' }) // Filtered data total
          .data()
          .reduce((a, b) => intVal(a) + intVal(b), 0);
          // Update footer
          $(api.column(9).footer()).html(
            'Total: $' + formatNumber(pageTotal_31) + ' (All: $' + formatNumber(total_31) + ')'
          );


          // Total over all pages
          total_61 = api
          .column(10) // Change this to your totals column index
          .data()
          .reduce((a, b) => intVal(a) + intVal(b), 0);

          // Total over this page
          pageTotal_61 = api
          .column(10, { search: 'applied' }) // Filtered data total
          .data()
          .reduce((a, b) => intVal(a) + intVal(b), 0);
          // Update footer
          $(api.column(10).footer()).html(
            'Total: $' + formatNumber(pageTotal_61) + ' (All: $' + formatNumber(total_61) + ')'
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
