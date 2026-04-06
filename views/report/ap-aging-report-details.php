<?php
  $this->title = "AP Aging Details Report";
  use yii\helpers\Html;
  use yii\helpers\Url;
  use app\models\TblTerms;
  use app\models\TblClient;
  $req = Yii::$app->request;
  $customer_id = $req->get('customer_id');
  $selected_start_date = $req->get('start_date');
  $selected_end_date = $req->get('end_date');
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

    <div class="table-responsive">
      <table id="example" class="display table table-hover table-bordered" data-page-length="20">
          <thead>
              <tr>
                  <th>Vendor Name</th>
                  <th>Purchase Invoice #</th>
                  <th>Vendor Invoice #</th>
                  <th>Invoice Date</th>
                  <th>Due Date</th>
                  <th>Total Amount</th>
                  <th>Amount Paid</th>
                  <th>Outstanding Amount</th>
                  <th>Current (0-15 days)</th>
                  <th>16-30 days</th>
                  <th>31-60 days</th>
                  <th>61-90 days</th>
                  <th>Over 90 days</th>
              </tr>
              <tr>
                  <td class="filterhead">Vendor Name</td>
                  <td class="filterhead">Invoice Number</td>
                  <td class="filterhead">Invoice Number</td>
                  <td class="filterhead">Invoice Date</td>
                  <td class="filterhead">Due Date</td>
                  <td class="filterhead">Total Amount</td>
                  <td class="filterhead">Amount Paid</td>
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

            // Initialize the query
            $query = (new Query())
                ->select([
                    'c.company_name',
                    'c.id',
                    'i.vendor_invoice_number',
                    'i.purchase_invoice_no',
                    'i.invoice_date',
                    'i.due_date',
                    'i.total_amount',
                    'COALESCE(SUM(ir.amount_received), 0) AS total_amount_received',
                    'i.total_amount - COALESCE(SUM(ir.amount_received), 0) AS outstanding_amount',
                    'SUM(CASE
                            WHEN DATEDIFF(CURDATE(), i.due_date) BETWEEN 0 AND 15 THEN i.total_amount - COALESCE(ir.amount_received, 0)
                            ELSE 0
                        END) AS current',
                    'SUM(CASE
                            WHEN DATEDIFF(CURDATE(), i.due_date) BETWEEN 16 AND 30 THEN i.total_amount - COALESCE(ir.amount_received, 0)
                            ELSE 0
                        END) AS days_16_30',
                    'SUM(CASE
                            WHEN DATEDIFF(CURDATE(), i.due_date) BETWEEN 31 AND 60 THEN i.total_amount - COALESCE(ir.amount_received, 0)
                            ELSE 0
                        END) AS days_31_60',
                    'SUM(CASE
                            WHEN DATEDIFF(CURDATE(), i.due_date) BETWEEN 61 AND 90 THEN i.total_amount - COALESCE(ir.amount_received, 0)
                            ELSE 0
                        END) AS days_61_90',
                    'SUM(CASE
                            WHEN DATEDIFF(CURDATE(), i.due_date) > 90 THEN i.total_amount - COALESCE(ir.amount_received, 0)
                            ELSE 0
                        END) AS over_90_days'
                ])
                ->from('tbl_vendor_invoice i')
                ->leftJoin('tbl_vendor c', 'i.fk_vendor_id = c.id')
                ->leftJoin('tbl_vendor_account_payable ir', 'i.id = ir.fk_vendor_invoice_id and ir.status = 1')
                // ->leftJoin('tbl_vendor_invoice_credit_applied ir_credit', 'i.id = ir_credit.fk_vendor_invoice_id and ir_credit.status = 1')
                ->where(['i.status' => 1])
                ->andWhere(['i.fk_location_id'=>$user_company])
                ->groupBy([
                    'c.company_name',
                    'c.id',
                    'i.id',
                    'i.vendor_invoice_number',
                    'i.invoice_date',
                    'i.due_date',
                    'i.total_amount'
                ])
                ->having('outstanding_amount > 0');
                // $query->andWhere('i.status != 0 and ir.status != 0');
            // Apply filters
            if (!empty($selected_start_date) && !empty($selected_end_date)) {
                $query->andWhere(['between', 'i.invoice_date', $selected_start_date, $selected_end_date]);
            }

            if (!empty($customer_id)) {
                if($customer_id != 0){
                  $query->andWhere(['c.id' => $customer_id]);
                }

            }

            // Execute the query and fetch results
            $results = $query->all();
            foreach($results as $r){
              // echo $r['name'].' '.$r['id'].' '.$r['serial_number'].'<br>';
              echo '<tr>
                <td>'.$r['company_name'].'</td>
                <td>'.$r['vendor_invoice_number'].'</td>
                <td>'.$r['purchase_invoice_no'].'</td>
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
              <th>7</th>
              <th>8</th>
              <th>9</th>
              <th>10</th>
              <th>11</th>
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
      .column(12) // Change this to your totals column index
      .data()
      .reduce((a, b) => intVal(a) + intVal(b), 0);

      // Total over this page
      pageTotal = api
      .column(12, { search: 'applied' }) // Filtered data total
      .data()
      .reduce((a, b) => intVal(a) + intVal(b), 0);
      var formatNumber = function (num) {
        return num.toLocaleString(undefined, {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2 // Allows up to 2 decimal places but not forced
        });
      };
      // Update footer
      $(api.column(12).footer()).html(
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

      // Total over all pages
      total_90 = api
      .column(11) // Change this to your totals column index
      .data()
      .reduce((a, b) => intVal(a) + intVal(b), 0);

      // Total over this page
      pageTotal_90 = api
      .column(11, { search: 'applied' }) // Filtered data total
      .data()
      .reduce((a, b) => intVal(a) + intVal(b), 0);
      // Update footer
      $(api.column(11).footer()).html(
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
