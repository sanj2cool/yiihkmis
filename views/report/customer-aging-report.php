<?php
  $this->title = "Customer Aging Report";
  use yii\helpers\Html;
  use yii\helpers\Url;
  use app\models\TblTerms;
  use app\models\TblClient;
  use app\models\TblOwnershipCompany;
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
    <form name="frm_reservation" id="" action="<?= Yii::$app->urlManager->createUrl(['report/customer-aging-report']) ?>" method="post" autocomplete="off">
		  <input type="hidden" name="_csrf-backend" value="<?=Yii::$app->request->getCsrfToken()?>" />
		  <div class="row justify-content-center">
        <div class="col-lg-2">
            <label for="">From Date<span class="text-danger">*</span></label>
            <input type="text" class="form-control" required="required" value="<?php if(isset($from_date)){ echo $from_date;} ?>" name="from_date"  id="from_date">
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


        <div class="col-md-2 pt-4">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
      </div>
	  </form>
    <hr />
    <div class="table-responsive">
      <table id="example" class="display table table-hover table-bordered" data-page-length="20">
          <thead>
              <tr>
                  <th>Customer Name</th>
                  <th>Invoice Number</th>
                  <th>Invoice Date</th>
                  <th>Due Date</th>
                  <th>Total Amount</th>
                  <th>Amount Received</th>
                  <th>Outstanding Amount</th>
                  <th>Current (0-15 days)</th>
                  <th>16-30 days</th>
                  <th>31-60 days</th>
                  <th>61-90 days</th>
                  <th>Over 90 days</th>
              </tr>
              <tr>
                  <td class="filterhead">Customer Name</td>
                  <td class="filterhead">Invoice Number</td>
                  <td class="filterhead">Invoice Date</td>
                  <td class="filterhead">Due Date</td>
                  <td class="filterhead">Total Amount</td>
                  <td class="filterhead">Amount Received</td>
                  <td class="filterhead">Outstanding Amount</td>
                  <td class="filterhead">Current (0-30 days)</td>
                  <td class="filterhead">16-30 days</td>
                  <td class="filterhead">31-60 days</td>
                  <td class="filterhead">61-90 days</td>
                  <td class="filterhead">Over 90 days</td>
              </tr>
          </thead>
          <tbody>
            <?php
            use yii\db\Query;
            $total_received = 0;
            $total_pending = 0;
            //--------new query logic-------
            $subquery = (new Query())
                ->select([
                    'fk_invoice_id',
                    'COALESCE(SUM(amount_received), 0) AS total_amount_received',
                    'SUM(CASE WHEN status != 0 THEN amount_received ELSE 0 END) AS valid_amount_received'
                ])
                ->from('tbl_account_receivable')
                ->groupBy('fk_invoice_id');
                $query = (new Query())
                    ->select([
                        'c.company_name as name',
                        'c.id',
                        'i.invoice_number',
                        'i.invoice_date',
                        'i.due_date',
                        'i.total_amount',
                        'COALESCE(sr.valid_amount_received, 0) AS total_amount_received',
                        'i.total_amount - COALESCE(sr.valid_amount_received, 0) AS outstanding_amount',
                        'SUM(CASE
                                WHEN DATEDIFF(CURDATE(), i.due_date) BETWEEN 0 AND 15 THEN i.total_amount - COALESCE(sr.valid_amount_received, 0)
                                ELSE 0
                            END) AS current',
                        'SUM(CASE
                                WHEN DATEDIFF(CURDATE(), i.due_date) BETWEEN 16 AND 30 THEN i.total_amount - COALESCE(sr.valid_amount_received, 0)
                                ELSE 0
                            END) AS days_16_30',
                        'SUM(CASE
                                WHEN DATEDIFF(CURDATE(), i.due_date) BETWEEN 31 AND 60 THEN i.total_amount - COALESCE(sr.valid_amount_received, 0)
                                ELSE 0
                            END) AS days_31_60',
                        'SUM(CASE
                                WHEN DATEDIFF(CURDATE(), i.due_date) BETWEEN 61 AND 90 THEN i.total_amount - COALESCE(sr.valid_amount_received, 0)
                                ELSE 0
                            END) AS days_61_90',
                        'SUM(CASE
                                WHEN DATEDIFF(CURDATE(), i.due_date) > 90 THEN i.total_amount - COALESCE(sr.valid_amount_received, 0)
                                ELSE 0
                            END) AS over_90_days'
                    ])
                    ->from('tbl_invoice i')
                    ->leftJoin('tbl_client c', 'i.fk_client_id = c.id')
                    ->leftJoin(['sr' => $subquery], 'i.id = sr.fk_invoice_id')
                    ->where('i.status != 0')
                    ->groupBy([
                        'c.company_name',
                        'c.id',
                        'i.id',
                        'i.invoice_number',
                        'i.invoice_date',
                        'i.due_date',
                        'i.total_amount'
                    ])
                    ->having('outstanding_amount > 0');
            // Apply filters
            if (!empty($from_date) && !empty($to_date)) {
                $query->andWhere(['between', 'i.invoice_date', $from_date, $to_date]);
            }
            // if (!empty($customerName)) {
            //     $query->andWhere(['c.customer_name' => $customerName]);
            // }
            if (!empty($customer)) {
                if($customer != 0){
                  $query->andWhere(['c.id' => $customer]);
                }
            }
            if (!empty($user_company)) {
                if($user_company != 0){
                  $query->andWhere(['i.fk_bill_from_id' => $user_company]);
                }
            }
            // if (!empty($invoiceNumber)) {
            //     $query->andWhere(['i.invoice_number' => $invoiceNumber]);
            // }
            // if (!empty($invoiceStatus)) {
            //     $query->andWhere(['i.invoice_status' => $invoiceStatus]);
            // }
            // if (!empty($minAmount) && !empty($maxAmount)) {
            //     $query->andWhere(['between', 'i.invoice_amount', $minAmount, $maxAmount]);
            // }
            // if (!empty($minOutstanding) && !empty($maxOutstanding)) {
            //     $query->andWhere(['between', 'i.invoice_amount - COALESCE(SUM(ir.amount_received), 0)', $minOutstanding, $maxOutstanding]);
            // }
            // if (!empty($agingCategory)) {
            //     switch ($agingCategory) {
            //         case 'current':
            //             $query->andHaving('DATEDIFF(CURDATE(), i.due_date) BETWEEN 0 AND 30');
            //             break;
            //         case '31-60':
            //             $query->andHaving('DATEDIFF(CURDATE(), i.due_date) BETWEEN 31 AND 60');
            //             break;
            //         case '61-90':
            //             $query->andHaving('DATEDIFF(CURDATE(), i.due_date) BETWEEN 61 AND 90');
            //             break;
            //         case 'over_90':
            //             $query->andHaving('DATEDIFF(CURDATE(), i.due_date) > 90');
            //             break;
            //     }
            // }
            // Execute the query and fetch results
            $results = $query->all();
            foreach($results as $r){
              // echo $r['name'].' '.$r['id'].' '.$r['serial_number'].'<br>';
              $total_received += $r['total_amount_received'];
              $total_pending += $r['outstanding_amount'];
              echo '<tr>
                <td>'.$r['name'].'</td>
                <td>'.$r['invoice_number'].'</td>
                <td>'.$r['invoice_date'].'</td>
                <td>'.$r['due_date'].'</td>
                <td>'.$r['total_amount'].'</td>
                <td>'.$r['total_amount_received'].'</td>
                <td>'.$r['outstanding_amount'].'</td>
                <td>'.$r['current'].'</td>
                <td>'.$r['days_16_30'].'</td>
                <td>'.$r['days_31_60'].'</td>
                <td>'.$r['days_61_90'].'</td>
                <td>'.$r['over_90_days'].'</td>
              </tr>';
            }
             ?>
          </tbody>
        </table>
    </div>
    <div class="row mt-2">
      <div class="col-12 text-center">
        <h2 class="text-danger">Total Amount Pending: <?=number_format($total_pending,2,'.',',')?></h2>

      </div>
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
    stateSave: true
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
