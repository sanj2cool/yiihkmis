<?php
  $this->title = "Customer Account Statements";
  use yii\helpers\Html;
  use yii\helpers\Url;
  use app\models\TblTerms;
  use app\models\TblClient;
  use app\models\TblUser;
  use app\models\TblInvoice;
  use app\models\TblInvoiceProject;
  use app\models\TblAccountReceivable;
  use app\models\TblOwnershipCompany;
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
        <form name="frm_reservation" id="" action="<?= Yii::$app->urlManager->createUrl(['report/customer-account-statements']) ?>" method="post" autocomplete="off">
            <input type="hidden" name="_csrf-backend" value="<?=Yii::$app->request->getCsrfToken()?>" />
            <div class="row justify-content-center">
                <div class="col-lg-2">
                    <label for="">From Date<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" required="required" value="<?php if(isset($from_date)){ echo $from_date;} ?>" name="from_date" id="from_date">
                </div>
                <div class="col-lg-2">
                    <label for="">To Date<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" required="required" value="<?php if(isset($to_date)){ echo $to_date;} ?>" name="to_date" id="to_date">
                </div>
                <?php
                // JavaScript to initialize Flatpickr on the #datepicker input
                $this->registerJs("
                    flatpickr('#from_date', {
                        disableMobile: true,
                        dateFormat: 'Y-m-d',
                        enableTime: false
                    });
                    flatpickr('#to_date', {
                        disableMobile: true,
                        dateFormat: 'Y-m-d',
                        enableTime: false
                    });
                ");
                ?>
                <div class="col-md-3">
                    <label>Customer<span class="text-danger">*</span></label>
                    <select class="form-control" id="customer" name="customer" required="required" data-choices='data-choices' data-options='{"removeItemButton":true,"placeholder":true}'>
                        <option value="0" <?php if(isset($customer)){ if($customer == 0){ echo 'selected="selected"';}}else{ echo 'selected="selected"';}?>>All</option>
                        <?php
                           $allclients = TblClient::find()
                                        ->where('status != 0')
                                        ->andWhere(['fk_location_id'=>$user_company])
                                        ->orderBy(['company_name'=>SORT_ASC])->all();
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

                <div class="col-md-2 pt-4">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>
        <hr />
        <h3 class="mb-3">Summary of Transactions</h3>
        <div class="table-responsive">
            <table id="summary-trans" class="display table table-hover table-bordered" data-page-length="20">
                <thead>
                    <tr>
                        <th>Transaction Date</th>
                        <th>Transaction Type</th>
                        <th>Transaction Number</th>
                        <th>Total Amount</th>
                        <th>Balance</th>
                    </tr>
                    <tr>
                        <td class="filterhead">Transaction Date</td>
                        <td class="filterhead">Transaction Type</td>
                        <td class="filterhead">Transaction Number</td>
                        <td class="filterhead">Total Amount</td>
                        <td class="filterhead">Balance</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if(isset($from_date) && isset($to_date) && isset($customer)){
                          $bill_company = $user_company;

                            // if($bill_company != 0){
                              $getinvoices = TblInvoice::find()
                                              ->where('status != 0')
                                              ->andWhere("DATE(crt_time) BETWEEN '".$from_date."' AND '".$to_date."'")
                                              ->andWhere(['fk_client_id'=>$customer])
                                              ->andWhere(['fk_bill_from_id'=>$bill_company])
                                              ->orderBy(['crt_time'=>SORT_ASC])->all();
                            // }else{
                            //   $getinvoices = TblInvoice::find()
                            //                   ->where('status != 0')
                            //                   ->andWhere("DATE(crt_time) BETWEEN '".$from_date."' AND '".$to_date."'")
                            //                   ->andWhere(['fk_client_id'=>$customer])
                            //                   ->orderBy(['crt_time'=>SORT_ASC])->all();
                            // }

                            // echo count($getinvoices);
                            $invoice_arr = [];
                            $ar_arr = [];
                            if(isset($getinvoices) && count($getinvoices) > 0){
                                foreach($getinvoices as $gi){
                                    $invoice_arr[] = ['date'=>date('Y-m-d',strtotime($gi->crt_time)),"type"=>"Invoice","number"=>$gi->invoice_number,"amount"=>$gi->total_amount];
                                }
                            }

                            // if($bill_company != 0){
                              $getar = TblAccountReceivable::find()
                                      ->where('status != 0')
                                      ->andWhere("DATE(crt_time) BETWEEN '".$from_date."' AND '".$to_date."'")
                                      ->andWhere('fk_invoice_id in (select id from tbl_invoice where fk_client_id = '.$customer.' and status != 0 and fk_bill_from_id = '.$bill_company.')')
                                      ->orderBy(['crt_time'=>SORT_ASC])
                                      ->all();
                            // }else{
                            //   $getar = TblAccountReceivable::find()
                            //           ->where('status != 0')
                            //           ->andWhere("DATE(crt_time) BETWEEN '".$from_date."' AND '".$to_date."'")
                            //           ->andWhere('fk_invoice_id in (select id from tbl_invoice where fk_client_id = '.$customer.' and status != 0)')
                            //           ->orderBy(['crt_time'=>SORT_ASC])
                            //           ->all();
                            // }


                            // echo count($getar);
                            if(isset($getar) && count($getar) > 0){
                                foreach($getar as $gi){
                                    //-------get the invoice number ------
                                    $getinvoice = TblInvoice::find()->where(['id'=>$gi->fk_invoice_id])->one();
                                    if(isset($getinvoice) && $getinvoice->id != ""){
                                        $invoice_no = " (Invoice #".$getinvoice->invoice_number.")";
                                    }else{
                                        $invoice_no = " (Invoice #.)";
                                    }
                                    // if($gi->is_bad_debt == 1){
                                    //     $ar_arr[] = ['date'=>date('Y-m-d',strtotime($gi->crt_time)),"type"=>"Bad Debt.","number"=>$gi->id.$invoice_no,"amount"=>$gi->amount_received];
                                    // }else{
                                        $ar_arr[] = ['date'=>date('Y-m-d',strtotime($gi->crt_time)),"type"=>"Amount Received","number"=>$gi->id.$invoice_no,"amount"=>$gi->amount_received];
                                    // }

                                }
                            }
                            // Merge the arrays
                            $mergedArray = array_merge($invoice_arr, $ar_arr);
                            // Define a comparison function
                            function compareByDate($a, $b) {
                                return strtotime($a['date']) - strtotime($b['date']);
                            }
                            // Sort the merged array
                            usort($mergedArray, 'compareByDate');
                            if(count($mergedArray) > 0){
                                if($mergedArray[0]['type'] == "Invoice"){
                                    //-----the first entry is invoice
                                    $starting_balance = $mergedArray[0]['amount'];
                                }else if($mergedArray[0]['type'] == "Amount Received"){
                                    $starting_balance = -$mergedArray[0]['amount'];
                                }else if($mergedArray[0]['type'] == "Bad Debt."){
                                    $starting_balance = -$mergedArray[0]['amount'];
                                }
                                $i = 0;
                                foreach($mergedArray as $v){
                                    if($i > 0){
                                        if($v['type'] == "Invoice"){
                                            $starting_balance = $starting_balance+$v['amount'];
                                        }else{
                                            $starting_balance = $starting_balance-$v['amount'];
                                        }
                                    }
                                    echo '<tr>
                                    <td>'.$v['date'].'</td>
                                    <td>'.$v['type'].'</td>
                                    <td>'.$v['number'].'</td>
                                    <td>'.$v['amount'].'</td>
                                    <td>'.$starting_balance.'</td>
                                    </tr>';
                                    $i++;
                                }
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
        <hr />
        <h3 class="mb-3">Outstanding Balances and Due Date</h3>
        <div class="table-responsive">
            <table id="outstanding-bal" class="display table table-hover table-bordered" data-page-length="20">
                <thead>
                    <tr>
                        <th>Invoice Date</th>
                        <th>Invoice Number</th>
                        <th>Invoice Amount</th>
                        <th>Payment Received</th>
                        <th>Outstanding Balance</th>
                        <th>Due Date</th>
                    </tr>
                    <tr>
                        <td class="filterhead">Invoice Date</td>
                        <td class="filterhead">Invoice Number</td>
                        <td class="filterhead">Invoice Amount</td>
                        <td class="filterhead">Payment Received</td>
                        <td class="filterhead">Outstanding Balance</td>
                        <td class="filterhead">Due Date</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        //-------this is according to the invoice date ---------------
                        if(isset($from_date) && isset($to_date) && isset($customer)){

                            if($bill_company != 0){
                              $getallinvoices = TblInvoice::find()
                                              ->where('status != 0')
                                              ->andWhere("DATE(invoice_date) BETWEEN '".$from_date."' AND '".$to_date."'")
                                              ->andWhere(['fk_client_id'=>$customer])
                                              ->andWhere(['fk_bill_from_id'=>$bill_company])
                                              ->orderBy(['invoice_date'=>SORT_ASC])->all();
                            }else{
                              $getallinvoices = TblInvoice::find()
                                              ->where('status != 0')
                                              ->andWhere("DATE(invoice_date) BETWEEN '".$from_date."' AND '".$to_date."'")
                                              ->andWhere(['fk_client_id'=>$customer])
                                              ->orderBy(['invoice_date'=>SORT_ASC])->all();
                            }

                            //------need to get invoices which are partially paid or totally unpaid ------
                            if(isset($getallinvoices) && count($getallinvoices) > 0){
                                foreach($getallinvoices as $gi){
                                    //----------now get the amount paid for the invoice if any -------------
                                    $a_r = TblAccountReceivable::find()->where(['fk_invoice_id'=>$gi->id])->andWhere('status != 0')->all();
                                    $a_r_amount = 0;
                                    foreach($a_r as $ar){
                                        $a_r_amount += $ar->amount_received;
                                    }
                                    if(!($gi->total_amount == $a_r_amount)){
                                        $amount_pending = $gi->total_amount-$a_r_amount;
                                        echo '<tr>
                                        <td>'.$gi->invoice_date.'</td>
                                        <td>'.$gi->invoice_number.'</td>
                                        <td>'.$gi->total_amount.'</td>
                                        <td>'.$a_r_amount.'</td>
                                        <td>'.$amount_pending.'</td>
                                        <td>'.$gi->due_date.'</td>
                                        </tr>';
                                    }
                                }
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
        <hr />
        <h3 class="mb-3">Client Information</h3>
        <div class="table-responsive">
            <table id="customer-list" class="display table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>Company Name</th>
                        <th>Contact Person Name</th>
                        <th>Phone Number</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Terms</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if(isset($customer)){
                            $getcustomer = TblClient::find()->where(['id'=>$customer])->andWhere('status != 0')->one();
                            if(isset($getcustomer) && $getcustomer->id != ""){
                                $getterms = TblTerms::find()->where(['id'=>$getcustomer->fk_terms_id,'status'=>1])->one();
                                if(isset($getterms) && $getterms->id != ""){
                                    $terms = $getterms->title;
                                }else{
                                    $terms = "(not set)";
                                }
                                echo '<tr>
                                <td>'.$getcustomer->company_name.'</td>
                                <td>'.$getcustomer->contact_name.'</td>
                                <td>'.$getcustomer->phone.'</td>
                                <td>'.$getcustomer->email.'</td>
                                <td>'.$getcustomer->address.'</td>
                                <td>'.$terms.'</td>
                                </tr>';
                            }
                        }
                    ?>

                </tbody>
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
    // DataTable no 1
    var table = $('#summary-trans').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        columnDefs: [{
            targets: \"_all\",
            orderable: true
        }],
        orderCellsTop: true,
        stateSave: true
    });
    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1 btn-sm');
    $('#summary-trans thead .filterhead').each( function () {
       var title = $(this).text();
       $(this).html( '<input type=\"text\" class=\"column_search form-control\"/>' );
    });
    // Apply the search
    $( '#summary-trans thead'  ).on( 'keyup', '.column_search',function () {
        table
            .column( $(this).parent().index() )
            .search( this.value )
            .draw();
    });
        // Restore state
    var state = table.state.loaded();
    if ( state ) {
        $('#summary-trans thead .filterhead').each( function () {
            var colSearch = state.columns[$(this).index()].search;
            if ( colSearch.search ) {
                $( 'input',this ).val( colSearch.search );
            }
        });
        table.draw();
    }
    // DataTable no 2
    var table2 = $('#outstanding-bal').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        columnDefs: [{
            targets: \"_all\",
            orderable: true
        }],
        orderCellsTop: true,
        stateSave: true
    });
    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1 btn-sm');
    $('#outstanding-bal thead .filterhead').each( function () {
        var title = $(this).text();
        $(this).html( '<input type=\"text\" class=\"column_search form-control\"/>' );
     });
     // Apply the search
     $( '#outstanding-bal thead'  ).on( 'keyup', '.column_search',function () {
         table2
             .column( $(this).parent().index() )
             .search( this.value )
             .draw();
     });
         // Restore state
     var state2 = table2.state.loaded();
     if ( state2 ) {
         $('#outstanding-bal thead .filterhead').each( function () {
             var colSearch = state.columns[$(this).index()].search;
             if ( colSearch.search ) {
                 $( 'input',this ).val( colSearch.search );
             }
         });
         table2.draw();
     }
    // DataTable no 3
});
");
?>
