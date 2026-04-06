<?php
$this->title = "Vendor Payment History";
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\TblTerms;
use app\models\TblVendor;
use app\models\TblUser;
use app\models\TblVendorInvoice;
// use app\models\TblVendorAccountPayable;
use app\models\TblPreferredPaymentMethod;
use app\models\TblLocation;
use app\models\TblEmployee;
use app\models\TblVendorAccountPayable;
use app\models\TblAccounts;
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
    <form name="frm_reservation" id="" action="<?= Yii::$app->urlManager->createUrl(['report/vendor-payment-history']) ?>" method="post" autocomplete="off">
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
                $allvendors = TblVendor::find()
                ->where(['status'=>1])
                ->andWhere(['fk_location_id'=>$user_company])
                ->all();
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
        </form>
        <hr />
        <div class="table-responsive">
          <table id="customer-list" class="display table table-hover table-bordered" data-page-length="20">
            <thead>
              <tr>
                <th>Sr. No.</th>
                <!-- <th>Invoice Number</th> -->
                <th>Vendor Details</th>
                <th>Amount Paid</th>
                <th>Paid Date</th>
                <th>Payment Method</th>
                <th>Bank Account</th>
              </tr>
              <tr>

                <td class="filterhead">Sr. No.</td>
                <!-- <td class="filterhead">Invoice Number</td> -->
                <td class="filterhead">Vendor Details</td>
                <td class="filterhead">Amount Paid</td>
                <td class="filterhead">Paid Date</td>
                <td class="filterhead">Payment Method</td>
                <td class="filterhead">Bank Account</td>
              </tr>
            </thead>
            <tbody>
              <?php
              $total_payment = 0;
              $i = 1;

              $query = TblVendorAccountPayable::find()
              ->alias('vp')
              ->select(['vp.*', 'vi.fk_vendor_id'])
              ->joinWith(['vendorInvoice vi']) // assuming relation defined in model
              ->where(['!=', 'vp.status', 0])

              ->andWhere(['vi.status' => 1])
              ->andWhere(['vi.fk_location_id' => $user_company]);

              if(isset($vendor) && $vendor != 0){
                $query->andWhere(['vi.fk_vendor_id' => $vendor]);
              }
              if(isset($from_date) && isset($to_date)){
                $query->andWhere(['between', 'vp.ar_date', $from_date, $to_date]);
              }

              $model = $query->orderBy(['vp.id' => SORT_DESC])->all();
              foreach($model as $m){
                // print_r($m);
                $getInvoice = $m->getVendorInvoice()->one(); // <- note the `one()`

                if ($getInvoice) {
                  $getVendor = TblVendor::find()
                  ->where(['id' => $getInvoice->fk_vendor_id, 'status' => 1])
                  ->one();

                  $vendor_name = ($getVendor && $getVendor->company_name != "")
                  ? $getVendor->company_name
                  : "";
                } else {
                  $vendor_name = "";
                }


                $getpaymethod = TblPreferredPaymentMethod::find()->where(['id'=>$m->fk_payment_method_id])->andWhere('status != 0')->one();
                if(isset($getpaymethod) && $getpaymethod->id != ""){
                  $paymethod = $getpaymethod->title;
                }else{
                  $paymethod = "";
                }
                // $getaccount = TblAccounts::find()->where(['id'=>$m->fk_account_id])->andWhere('status != 0')->one();
                // if(isset($getaccount) && $getaccount->id != ""){
                //   $account = $getaccount->title;
                // }else{
                $account = "";
                // }
                $total_payment += $m->amount_received;
                echo '<tr>
                <td>'.$i.'</td>
                <td>'.$vendor_name.' |
                <a target="_blank" href="'.Url::to(['vendor-account-payable/view','id'=>$m->id]).'"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a></td>
                <td>'.$m->amount_received.'</td>
                <td>'.$m->ar_date.'</td>
                <td>'.$paymethod.'</td>
                <td>'.$account.'</td>
                </tr>';
                $i++;
              }


              ?>
            </tbody>
            <tfoot>
              <tr>
                <th></th>
                <th></th>
                <th>Amount Paid</th>
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
          .column(2) // Change this to your totals column index
          .data()
          .reduce((a, b) => intVal(a) + intVal(b), 0);

          // Total over this page
          pageTotal = api
          .column(2, { search: 'applied' }) // Filtered data total
          .data()
          .reduce((a, b) => intVal(a) + intVal(b), 0);
          var formatNumber = function (num) {
            return num.toLocaleString(undefined, {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2 // Allows up to 2 decimal places but not forced
            });
          };
          // Update footer
          $(api.column(2).footer()).html(
            'Total: $' + formatNumber(pageTotal) + ' (All: $' + formatNumber(total) + ')'
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
