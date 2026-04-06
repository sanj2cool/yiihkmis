<?php
  $this->title = "Purchase Invoice Summary";
  use yii\helpers\Html;
  use yii\helpers\Url;
  use app\models\TblTerms;
  use app\models\TblVendor;
  use app\models\TblUser;
  use app\models\TblVendorInvoice;
  use app\models\TblVendorAccountPayable;
  use app\models\TblPreferredPaymentMethod;
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
        <form name="frm_reservation" id="" action="<?= Yii::$app->urlManager->createUrl(['report/purchase-invoice-summary']) ?>" method="post" autocomplete="off">
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
                <div class="col-md-3">
                    <label>Created By<span class="text-danger">*</span></label>
                    <select class="form-control" id="crt_by" name="crt_by" required="required">
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
            <table id="customer-list" class="display table table-hover table-bordered" data-page-length="20">
                <thead>
                    <tr>
                        <th>Act.</th>
                        <th>Vendor Details</th>
                        <th>Invoice Number</th>
                        <th>Invoice Date</th>
                        <th>Due Date</th>
                        <th>Total Amount</th>
                        <th>Terms</th>
                        <th>Created By</th>
                        <th>Created Time</th>
                    </tr>
                    <tr>
                        <td class="filterhead"></td>
                        <td class="filterhead">Vendor Details</td>
                        <td class="filterhead">Invoice Number</td>
                        <td class="filterhead">Invoice Date</td>
                        <td class="filterhead">Due Date</td>
                        <td class="filterhead">Total Amount</td>
                        <td class="filterhead">Terms</td>
                        <td class="filterhead">Created By</td>
                        <td class="filterhead">Created Time</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    //------get all customers -----
                    if(isset($from_date) && isset($to_date) && isset($vendor) && isset($crt_by)){
                        if($vendor == 0 && $crt_by == 0){
                            //------all the terms and crt by
                            $model = TblVendorInvoice::find()
                                    ->where('status != 0')
                                    // ->andWhere(['fk_location_id'=>$location_selected])
                                    ->andWhere("DATE(invoice_date) BETWEEN '".$from_date."' AND '".$to_date."'")
                                    ->orderBy(['id'=>SORT_DESC])->all();
                        }else if($vendor == 0 && $crt_by != 0){
                            $model = TblVendorInvoice::find()
                                    ->where('status != 0')
                                    // ->andWhere(['fk_location_id'=>$location_selected])
                                    ->andWhere(['crt_by'=>$crt_by])
                                    ->andWhere("DATE(invoice_date) BETWEEN '".$from_date."' AND '".$to_date."'")
                                    ->orderBy(['id'=>SORT_DESC])->all();
                        }else if($vendor != 0 && $crt_by == 0){
                            $model = TblVendorInvoice::find()
                                    ->where('status != 0')
                                    // ->andWhere(['fk_location_id'=>$location_selected])
                                    ->andWhere(['fk_vendor_id'=>$vendor])
                                    ->andWhere("DATE(invoice_date) BETWEEN '".$from_date."' AND '".$to_date."'")
                                    ->orderBy(['id'=>SORT_DESC])->all();
                        }else{
                            $model = TblVendorInvoice::find()
                            ->where('status != 0')
                            // ->andWhere(['fk_location_id'=>$location_selected])
                            ->andWhere(['crt_by'=>$crt_by])
                            ->andWhere(['fk_vendor_id'=>$vendor])
                            ->andWhere("DATE(invoice_date) BETWEEN '".$from_date."' AND '".$to_date."'")
                            ->orderBy(['id'=>SORT_DESC])->all();
                        }

                    }else{
                        $model = TblVendorInvoice::find()->where('status != 0')->andWhere(['fk_location_id'=>$location_selected])->orderBy(['id'=>SORT_DESC])->all();
                    }

                    foreach($model as $m){

                        $getterms = TblTerms::find()->where(['id'=>$m->fk_terms_id,'status'=>1])->one();
                        if(isset($getterms) && $getterms->id != ""){
                            $terms = $getterms->title;
                        }else{
                            $terms = "(not set)";
                        }
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
                        $getvendor = TblVendor::find()->where(['id'=>$m->fk_vendor_id])->andWhere('status != 0')->one();
                        if(isset($getvendor) && $getvendor->id != ""){
                            $vendor = $getvendor->company_name;
                        }else{
                            $vendor = "";
                        }
                        echo '<tr>
                        <td><a href="'.Url::to(['vendor-invoice/view','id'=>$m->id]).'" title="View" target="_blank">View</a></td>
                        <td>'.$vendor.'</td>
                        <td>'.$m->vendor_invoice_number.'</td>
                        <td>'.$m->invoice_date.'</td>
                        <td>'.$m->due_date.'</td>
                        <td>'.$m->total_amount.'</td>
                        <td>'.$terms.'</td>
                        <td>'.$crt_by.'</td>
                        <td>'.date('d-F-Y h:ia',strtotime($m->crt_time)).'</td>
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
                        <th></th>
                        <th>5</th>
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
      .column(5) // Change this to your totals column index
      .data()
      .reduce((a, b) => intVal(a) + intVal(b), 0);

      // Total over this page
      pageTotal = api
      .column(5, { search: 'applied' }) // Filtered data total
      .data()
      .reduce((a, b) => intVal(a) + intVal(b), 0);
      var formatNumber = function (num) {
        return num.toLocaleString(undefined, {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2 // Allows up to 2 decimal places but not forced
        });
      };
      // Update footer
      $(api.column(5).footer()).html(
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
