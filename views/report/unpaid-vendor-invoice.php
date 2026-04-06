<?php
$this->title = "Unpaid Purchase Invoice Report";
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\TblTerms;
use app\models\TblVendor;
use app\models\TblUser;
use app\models\TblInvoice;
use yii\db\Expression;
$session = Yii::$app->session;
$user_company = $session['userCompany'];
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
    <form name="frm_reservation" id="" action="<?= Yii::$app->urlManager->createUrl(['report/unpaid-vendor-invoice']) ?>" method="post" autocomplete="off">
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
                $allclients = TblVendor::find()
                              ->where('status != 0')
                              ->andWhere(['fk_location_id'=>$user_company])
                              ->orderBy(['company_name'=>SORT_ASC])->all();
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
            <div class="col-md-3">
              <label>Created By<span class="text-danger">*</span></label>
              <select class="form-control" id="crt_by" name="crt_by" required="required">
                <option value="">Select</option>
                <option value="0" <?php if(isset($crt_by)){ if($crt_by == 0){ echo 'selected="selected"';}}else{ echo 'selected="selected"';}?>>All</option>
                <?php
                $allusers = TblUser::find()->where(['status'=>1])->orderBy(['username'=>SORT_ASC])->all();
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
                <th>Vendor Details</th>
                <th>Purchase Invoice #</th>
                <th>Vendor Invoice #</th>
                <th>Invoice Date</th>
                <th>Due Date</th>
                <th>Invoice Amount</th>
                <th>Total Amount Paid</th>
                <th>Outstanding Amount</th>
              </tr>
              <tr>
                <td class="filterhead">Vendor Details</td>
                <td class="filterhead">Invoice Number</td>
                <td class="filterhead">Invoice Number</td>
                <td class="filterhead">Invoice Date</td>
                <td class="filterhead">Due Date</td>
                <td class="filterhead">Invoice Amount</td>
                <td class="filterhead">Total Amount Paid</td>
                <td class="filterhead">Outstanding Amount</td>
              </tr>
            </thead>
            <tbody>
              <?php
              use yii\db\Query;
              // Apply filters
              if (!empty($from_date) && !empty($to_date)) {

                // $query = (new Query())
                // ->select([
                //   'c.company_name',
                //   'c.id',
                //   'i.id AS invoice_id',
                //   'i.vendor_invoice_number',
                //   'i.invoice_date',
                //   'i.due_date',
                //   'i.total_amount',
                //   'COALESCE(SUM(ir.amount_received), 0) AS total_amount_received',
                //   'COALESCE(SUM(irc.amount_applied), 0) AS total_amount_credit',
                //   'i.total_amount - COALESCE(SUM(ir.amount_received), 0) - COALESCE(SUM(irc.amount_applied), 0) AS outstanding_amount'
                // ])
                // ->from('tbl_vendor_invoice i')
                // ->leftJoin('tbl_vendor c', 'i.fk_vendor_id = c.id')
                // ->leftJoin('tbl_vendor_account_payable ir', 'i.id = ir.fk_vendor_invoice_id and ir.status = 1')
                // ->leftJoin('tbl_vendor_invoice_credit_applied irc','i.id = irc.fk_vendor_invoice_id and irc.status = 1')
                // ->where('i.status != 0')
                // ->groupBy([
                //   'c.company_name',
                //   'c.id',
                //   'i.vendor_invoice_number',
                //   'i.invoice_date',
                //   'i.due_date',
                //   'i.total_amount'
                // ])
                // ->having('outstanding_amount > 0')
                // ->orderBy(['due_date'=>SORT_DESC]);
                // $query->andWhere(['between', 'i.invoice_date', $from_date, $to_date]);
                // if (!empty($vendor)) {
                //   if($vendor != 0){
                //     $query->andWhere(['c.id' => $vendor]);
                //   }
                //
                // }
                $subQueryReceivables = (new Query())
                ->select(['fk_vendor_invoice_id', new Expression('SUM(amount_received) AS total_amount_received')])
                ->from('tbl_vendor_account_payable')
                ->where(['status' => 1])
                ->groupBy('fk_vendor_invoice_id');

                // $subQueryCredits = (new Query())
                // ->select(['fk_vendor_invoice_id', new Expression('SUM(amount_applied) AS total_amount_credit')])
                // ->from('tbl_vendor_invoice_credit_applied')
                // ->where(['status' => 1])
                // ->groupBy('fk_vendor_invoice_id');

                $query = (new Query())
                ->select([
                  'c.company_name',
                  'c.id',
                  'i.id AS invoice_id',
                  'i.vendor_invoice_number',
                  'i.purchase_invoice_no',
                  'i.invoice_date',
                  'i.due_date',
                  'i.total_amount',
                  'COALESCE(ar.total_amount_received, 0) AS total_amount_received',
                  // 'COALESCE(cr.total_amount_credit, 0) AS total_amount_credit',
                  new Expression('i.total_amount - COALESCE(ar.total_amount_received, 0) AS outstanding_amount')
                ])
                ->from('tbl_vendor_invoice i')
                ->leftJoin(['c' => 'tbl_vendor'], 'i.fk_vendor_id = c.id')
                ->leftJoin(['ar' => $subQueryReceivables], 'ar.fk_vendor_invoice_id = i.id')
                // ->leftJoin(['cr' => $subQueryCredits], 'cr.fk_vendor_invoice_id = i.id')
                ->where(['!=', 'i.status', 0])
                ->andWhere(['i.fk_location_id'=>$user_company])
                ->andWhere(['between', 'i.invoice_date', $from_date, $to_date]);

                if (!empty($vendor) && $vendor != 0) {
                  $query->andWhere(['c.id' => $vendor]);
                }

                // Wrap in an outer query if you want to use HAVING on calculated field
                $finalQuery = (new Query())
                ->from(['q' => $query])
                ->where(['>', 'outstanding_amount', 0])
                ->orderBy(['due_date' => SORT_DESC]);

                // Execute the query and fetch results
                $results = $finalQuery->all();
                foreach($results as $r){
                  echo '<tr>
                  <td>'.$r['company_name'].'</td>
                  <td>'.$r['vendor_invoice_number'].' |
                  <a target="_blank" href="'.Url::to(['vendor-invoice/view','id'=>$r['invoice_id']]).'"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a></td>
                  <td>'.$r['purchase_invoice_no'].'</td>
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

          var formatNumber = function (num) {
            return num.toLocaleString(undefined, {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2 // Allows up to 2 decimal places but not forced
            });
          };

          // Total over all pages
          total = api
          .column(7) // Change this to your totals column index
          .data()
          .reduce((a, b) => intVal(a) + intVal(b), 0);

          // Total over this page
          pageTotal = api
          .column(7, { search: 'applied' }) // Filtered data total
          .data()
          .reduce((a, b) => intVal(a) + intVal(b), 0);

          // Update footer
          $(api.column(7).footer()).html(
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
          pageTotal_ap = api
          .column(6, { search: 'applied' }) // Filtered data total
          .data()
          .reduce((a, b) => intVal(a) + intVal(b), 0);
          // Update footer
          $(api.column(6).footer()).html(
            'Total: $' + formatNumber(pageTotal_ap) + ' (All: $' + formatNumber(total_oa) + ')'
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
