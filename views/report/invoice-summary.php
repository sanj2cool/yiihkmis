<?php
$this->title = "Sales Invoice Summary";
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\TblTerms;
use app\models\TblClient;
use app\models\TblUser;
use app\models\TblInvoice;
use app\models\TblAccountReceivable;
use app\models\TblLocation;
use app\models\TblEmployee;

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
    <form name="frm_reservation" id="" action="<?= Yii::$app->urlManager->createUrl(['report/invoice-summary']) ?>" method="post" autocomplete="off">
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
                <th>Act.</th>
                <th>Customer Details</th>
                <th>Invoice Number</th>
                <th>Invoice Date</th>
                <th>Due Date</th>
                <th>Total Amount</th>
                <th>Amount Received</th>
                <th>Due Amount Till <?php if(isset($to_date)){ echo $to_date;}?></th>
                <th>Terms</th>
                <th>Created By</th>
                <th>Created Time</th>
              </tr>
              <tr>
                <td class="filterhead"></td>
                <td class="filterhead">Customer Details</td>
                <td class="filterhead">Invoice Number</td>
                <td class="filterhead">Invoice Date</td>
                <td class="filterhead">Due Date</td>
                <td class="filterhead">Total Amount</td>
                <td class="filterhead">Amount Received</td>
                <td class="filterhead">Due Amount Till</td>
                <td class="filterhead">Terms</td>
                <td class="filterhead">Created By</td>
                <td class="filterhead">Created Time</td>
              </tr>
            </thead>
            <tbody>
              <?php
              //------get all customers -----
              if(isset($from_date) && isset($to_date) && isset($customer) && isset($crt_by)){
                if($customer == 0 && $crt_by == 0){
                  //------all the terms and crt by
                  $model = TblInvoice::find()
                  ->where('status != 0')
                  ->andWhere("DATE(invoice_date) BETWEEN '".$from_date."' AND '".$to_date."'")
                  ->andWhere(['fk_bill_from_id'=>$user_company])
                  ->orderBy(['id'=>SORT_DESC])->all();
                }else if($customer == 0 && $crt_by != 0){
                  $model = TblInvoice::find()
                  ->where('status != 0')
                  ->andWhere(['crt_by'=>$crt_by])
                  ->andWhere("DATE(invoice_date) BETWEEN '".$from_date."' AND '".$to_date."'")
                  ->andWhere(['fk_bill_from_id'=>$user_company])
                  ->orderBy(['id'=>SORT_DESC])->all();
                }else if($crt_by == 0 && $customer != 0){
                  $model = TblInvoice::find()
                  ->where('status != 0')
                  ->andWhere(['fk_client_id'=>$customer])
                  ->andWhere("DATE(invoice_date) BETWEEN '".$from_date."' AND '".$to_date."'")
                  ->andWhere(['fk_bill_from_id'=>$user_company])
                  ->orderBy(['id'=>SORT_DESC])->all();
                }else{
                  $model = TblInvoice::find()
                  ->where('status != 0')
                  ->andWhere(['crt_by'=>$crt_by])
                  ->andWhere(['fk_client_id'=>$customer])
                  ->andWhere("DATE(invoice_date) BETWEEN '".$from_date."' AND '".$to_date."'")
                  ->andWhere(['fk_bill_from_id'=>$user_company])
                  ->orderBy(['id'=>SORT_DESC])->all();
                }
                foreach($model as $m){
                  $getcrtby = TblUser::find()->where(['id'=>$m->crt_by])->andWhere('status != 0')->one();
                  if(isset($getcrtby) && $getcrtby->id != ""){
                    if($getcrtby->alias != ""){
                      $crt_by = $getcrtby->alias;
                    }else{
                      $crt_by = $getcrtby->username;
                    }
                  }else{
                    $crt_by = "(not set)";
                  }
                  $getcustomer = TblClient::find()->where(['id'=>$m->fk_client_id])->andWhere('status != 0')->one();
                  if(isset($getcustomer) && $getcustomer->id != ""){
                    $customer = $getcustomer->company_name;
                    $getterms = TblTerms::find()->where(['id'=>$getcustomer->fk_terms_id,'status'=>1])->one();
                    if(isset($getterms) && $getterms->id != ""){
                      $terms = $getterms->title;
                    }else{
                      $terms = "(not set)";
                    }
                  }else{
                    $terms = "(not set)";
                    $customer = "(not set)";
                  }
                  //------GET ALL THE MONEY RECEIVED -------
                  $a_r = TblAccountReceivable::find()->where(['fk_invoice_id'=>$m->id])->andWhere('status != 0')->all();
                  $a_r_amount = 0;
                  foreach($a_r as $ar){
                    $a_r_amount += $ar->amount_received;
                  }
                  $due_amount = $m->total_amount-$a_r_amount;
                  $amount_due = 0;
                  if($m->due_date <= $to_date){
                    //---due date has passed -----
                    $amount_due = number_format($due_amount,2,'.',',');
                  }

                  echo '<tr>
                  <td><a href="'.Url::to(['invoice/view','id'=>$m->id]).'" title="View" target="_blank">View</a></td>
                  <td>'.$customer.'</td>
                  <td>'.$m->invoice_number.'</td>
                  <td>'.$m->invoice_date.'</td>
                  <td>'.$m->due_date.'</td>
                  <td>'.$m->total_amount.'</td>
                  <td>'.$a_r_amount.'</td>
                  <td>'.$amount_due.'</td>
                  <td>'.$terms.'</td>
                  <td>'.$crt_by.'</td>
                  <td>'.date('d-F-Y h:ia',strtotime($m->crt_time)).'</td>
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
                <th>5</th>
                <th>6</th>
                <th>7</th>
                <th></th>
                <th></th>
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
