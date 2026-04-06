<?php
$this->title = "Expenses Report";
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\TblUser;
use app\models\TblLocation;
use app\models\TblUserLocation;

$session = Yii::$app->session;
$user_company = $session['userCompany'];

$fk_user_id = $session['userId'];
// Step 1: Get the locations of the logged-in user
$userLocations = TblUserLocation::find()
->select('fk_location_id')
->where(['fk_user_id' => $fk_user_id])
->andWhere(['status'=>1])
->column(); // Gets an array of location IDs
// print_r($userLocations);
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
    <form name="frm_reservation" id="" action="<?= Yii::$app->urlManager->createUrl(['report/expenses']) ?>" method="post" autocomplete="off">
      <input type="hidden" name="_csrf-backend" value="<?=Yii::$app->request->getCsrfToken()?>" />
      <div class="row justify-content-center">
        <div class="col-lg-2 mb-2">
          <label for="">From Date<span class="text-danger">*</span></label>
          <input type="text" class="form-control datetimepicker" required="required" value="<?php if(isset($from_date)){ echo $from_date;} ?>" name="from_date" data-options='{"enableTime":false,"dateFormat":"Y-m-d","disableMobile":true}'>
          </div>
          <div class="col-lg-2 mb-2">
            <label for="">To Date<span class="text-danger">*</span></label>
            <input type="text" class="form-control datetimepicker" required="required" value="<?php if(isset($to_date)){ echo $to_date;} ?>" name="to_date" data-options='{"enableTime":false,"dateFormat":"Y-m-d","disableMobile":true}'>
            </div>
            <div class="col-lg-2 mb-2">
              <label for="">Expense Category<span class="text-danger">*</span></label>
              <select class="form-select" id="category_selected" name="category_selected" required="required">
                <option value="">Select</option>
                <option value="0" <?php if(isset($category_selected) && $category_selected == 0){echo 'selected="selected"';}else{ echo 'selected="selected"'; }?>>All</option>
                <?php
                $categories = \app\models\TblExpenseCategory::find()->where(['status'=>1])->orderBy(['title'=>SORT_ASC])->all();
                if(isset($categories) && count($categories) > 0){
                  foreach($categories as $l){
                    if(isset($category_selected) && $category_selected == $l->id){
                      echo '<option value="'.$l->id.'" selected="selected">'.$l->title.'</option>';
                    }else{
                      echo '<option value="'.$l->id.'">'.$l->title.'</option>';
                    }

                  }
                }
                ?>
              </select>
            </div>
            <div class="col-lg-3 mb-2">
              <label for="">Vendor<span class="text-danger">*</span></label>
              <select class="form-select" id="vendor_selected" name="vendor_selected" required="required">
                <option value="">Select</option>
                <option value="0" <?php if(isset($vendor_selected) && $vendor_selected == 0){echo 'selected="selected"';}else{ echo 'selected="selected"'; }?>>All</option>
                <?php
                $vendors = \app\models\TblVendor::find()
                              ->where(['status'=>1])
                              ->andWhere(['fk_location_id'=>$user_company])
                              ->orderBy(['company_name'=>SORT_ASC])->all();
                if(isset($vendors) && count($vendors) > 0){
                  foreach($vendors as $l){
                    if(isset($vendor_selected) && $vendor_selected == $l->id){
                      echo '<option value="'.$l->id.'" selected="selected">'.$l->company_name.'</option>';
                    }else{
                      echo '<option value="'.$l->id.'">'.$l->company_name.'</option>';
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

        <?php
          $this->registerJs('
            //--------choices for check-in by, check-out by -------
            const element4 = document.getElementById("category_selected");
            const choices4 = new Choices(element4, {
              removeItemButton: true,
              placeholder: true,
              allowHTML: true
            });
            const element3 = document.getElementById("vendor_selected");
            const choices3 = new Choices(element3, {
              removeItemButton: true,
              placeholder: true,
              allowHTML: true
            });

          ');
        ?>

        <hr />
        <div class="table-responsive">
          <table id="customer-list" class="display table table-hover table-bordered" data-page-length="20">
            <thead>
              <tr>
                <th>#</th>
                <th>Vendor Name</th>
                <th>Expense Category</th>
                <th>Receipt No.</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Tax</th>
                <th>Final Amount</th>
                <th>Payment Method</th>
                <th>Notes</th>
                <th style="min-width:150px;">Files</th>
                <th>Created By</th>
                <th>Created At</th>
              </tr>
              <tr>
                <td class="filterhead"></td>
                <td class="filterhead">Vendor Name</td>
                <td class="filterhead">Expense Category</td>
                <td class="filterhead">Receipt No.</td>
                <td class="filterhead">Date</td>
                <td class="filterhead">Amount</td>
                <td class="filterhead">Tax</td>
                <td class="filterhead">Final Amount</td>
                <td class="filterhead">Payment Method</td>
                <td class="filterhead">Notes</td>
                <td class="filterhead">Files</td>
                <td class="filterhead">Created By</td>
                <td class="filterhead">Created At</td>
              </tr>
            </thead>
            <tbody>
              <?php
              if(isset($from_date) && isset($to_date)){

                $get = \app\models\TblExpense::find()
                  ->where(['between', 'date', $from_date, $to_date])
                  ->andWhere(['status' => 1])
                  ->andFilterWhere([
                      'fk_location_id' => $user_company ?: null,
                      'fk_expense_category_id' => $category_selected ?: null,
                      'fk_vendor_id' => $vendor_selected ?: null,
                  ])
                  ->orderBy(['date' => SORT_DESC])
                  ->all();
                if(isset($get) && count($get) > 0){
                  foreach($get as $index => $g){
                    $cat = $g->expenseCategory->title ?? '';
                    $pay_method = $g->payMethod->title ?? '';
                    $location = $g->location->title ?? '';
                    $tax_rate = $g->taxRate->tax_rate ? $g->taxRate->tax_rate.'%' : '';
                    if (empty($g->expenseFiles)) {
                        $files = '<span class="text-muted">—</span>';
                    }else{
                      $count = count($g->expenseFiles);
                      $files = Html::button(
                          'View Files ('.$count.')',
                          [
                              'class' => 'btn btn-sm btn-primary expense-file-preview',
                              'data-files' => json_encode(array_map(function ($file) {
                                  return [
                                      'url' => 'https://hkmis.ca/web/expenses/' . $file->file_upload,
                                      'type' => strtolower(pathinfo($file->file_upload, PATHINFO_EXTENSION))
                                  ];
                              }, $g->expenseFiles)),
                          ]
                      );
                    }
                    $crt_by = $g->createdByUser->alias ?? '(not set)';
                    $crt_time = date('Y-m-d h:ia',strtotime($g->crt_time));
                    $vendor = $g->vendor->company_name ?? '';
                    echo '<tr>
                    <td>'.($index+1).' | <a class="unformat text-sm" href="'.Url::to(['expense/view','id'=>$g->id]).'" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a></td>
                    <td>'.$vendor.'</td>
                    <td>'.$cat.'</td>
                    <td>'.$g->receipt_no.'</td>
                    <td>'.$g->date.'</td>
                    <td>'.$g->amount.'</td>
                    <td>'.$g->tax_amount.' ('.$tax_rate.')'.'</td>
                    <td>'.$g->final_amount.'</td>
                    <td>'.$pay_method.'</td>
                    <td>'.$g->notes.'</td>
                    <td>'.$files.'</td>
                    <td>'.$crt_by.'</td>
                    <td>'.$crt_time.'</td>
                    </tr>';
                  }
                }
              }//---if for filters set ended ----
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="modal fade" id="expenseFileModal" tabindex="-1">
      <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Expense File</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body text-center" id="expenseFileContent">
            <!-- dynamic -->
          </div>
        </div>
      </div>
    </div>
    <script>
    document.addEventListener('click', function (e) {

        if (!e.target.classList.contains('expense-file-preview')) return;

        let files = JSON.parse(e.target.dataset.files);
        let html = '';

        files.forEach((file, index) => {

            html += `
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>File ${index + 1}</strong>
                        <a href="${file.url}" target="_blank" class="btn btn-sm btn-outline-secondary">
                            Open / Download
                        </a>
                    </div>
            `;

            if (file.type === 'pdf') {
                html += `
                    <iframe src="${file.url}" style="width:100%; height:65vh;" frameborder="0"></iframe>
                `;
            } else {
                html += `
                    <div class="text-center">
                        <img src="${file.url}" class="img-fluid" alt="Expense File">
                    </div>
                `;
            }

            html += `</div>`;
        });

        document.getElementById('expenseFileContent').innerHTML = html;

        let modal = new bootstrap.Modal(document.getElementById('expenseFileModal'));
        modal.show();
    });
    </script>
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
        stateSave: true
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
