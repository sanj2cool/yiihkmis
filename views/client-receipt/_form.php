<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\TblClient;
use app\models\TblPreferredPaymentMethod;
use app\models\TblInvoice;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\TblClientReceipt $model */
/** @var yii\widgets\ActiveForm $form */

$session = Yii::$app->session;
$user_company = $session['userCompany'];
$allpaymethods = TblPreferredPaymentMethod::find()->where(['status'=>1])->orderBy(['title'=>SORT_ASC])->all();
$paymethodarr = ArrayHelper::map($allpaymethods,'id','title');

$allclients = TblClient::find()->where(['status'=>1,'fk_location_id'=>$user_company])->orderBy(['company_name'=>SORT_ASC])->all();
$clientarr = ArrayHelper::map($allclients,'id','company_name');
$model->ar_date = date('Y-m-d');

$req = Yii::$app->request;
$client_id_received = $req->get('client_id');
if(isset($client_id_received)){
  $model->fk_client_id = $client_id_received;
}
?>

<div class="tbl-client-receipt-form">
  <div id="msg">

    <?php
    if(Yii::$app -> session -> getFlash('success')!=null){
      ?>

      <div class="alert alert-outline-success d-flex align-items-center" role="alert">
        <span class="fas fa-check-circle text-success fs-5 me-3"></span>
        <p class="mb-0 flex-1"><?= Yii::$app -> session -> getFlash('success'); ?></p>

        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php
    }else if(Yii::$app -> session -> getFlash('error')!=null){
      ?>
      <div class="alert alert-outline-danger d-flex align-items-center" role="alert">
        <span class="fas fa-times-circle text-danger fs-5 me-3"></span>
        <p class="mb-0 flex-1"><?= Yii::$app -> session -> getFlash('error'); ?></p>
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>

      <?php
    }
    ?>
  </div>
  <?php $form = ActiveForm::begin([
      'options' => [
        'autocomplete' => 'off',
        'enctype' => 'multipart/form-data'
      ],
  ]); ?>
    <div class="card shadow rounded mt-4">
      <div class="card-header pt-3 pb-2 bg-info-subtle">
        <div class="row">
          <div class="col-lg-6">
            <h4>Basic Details</h4>
          </div>
        </div>
      </div>
      <div class="card-body p-3">
        <div class="row">
          <div class="col-lg-3 mb-2">
            <?= $form->field($model, 'fk_client_id')->dropDownList($clientarr,['prompt'=>'Select','class'=>'form-select']) ?>
            <div id="client_error" class="text-danger">

            </div>
          </div>
          <div class="col-lg-3 mb-2">
            <?= $form->field($model, 'ar_date')->textInput(['maxlength' => true]) ?>
          </div>
          <div class="col-lg-3 mb-2">
            <?= $form->field($model, 'fk_payment_method_id')->dropDownList($paymethodarr,['prompt'=>'Select','class'=>'form-select']) ?>
          </div>
          <div class="col-lg-3 mb-2">
            <?= $form->field($model, 'amount_received')->textInput(['maxlength' => true,'class'=>'form-control floatNumberField bg-light','onkeyup'=>'checkDec(this);','readonly'=>true]) ?>
            <div id="total_error" class="text-danger">

            </div>
          </div>
          <div class="col-lg-3 mb-2">
            <?= $form->field($model, 'transaction_reference_number')->textInput(['maxlength' => true]) ?>
          </div>
          <div class="col-lg-9 mb-2">
            <?= $form->field($model, 'notes')->textarea(['rows' => 3]) ?>
          </div>
        </div>
      </div>
    </div>
    <div class="card shadow rounded mt-2">
      <div class="card-header p-3 border-bottom border-300 bg-primary-subtle">
        <div class="row g-3 justify-content-between align-items-center">
          <div class="col-12 col-md">
            <h5 class="mb-0" id="upload-images">Attachments</h5>
          </div>
        </div>
      </div>
      <div class="card-body p-3">
        <div class="row">
          <div class="col-lg-3 mb-2">
            <label for="">Document</label>
            <input type="file" name="doc_file[]" class="form-control" accept="image/*,application/pdf">
          </div>
          <div class="col-lg-3 mb-2">
            <label for="">Act.</label><br />
            <button type="button" class="btn btn-primary btn-sm btn_add_doc_row">
              <i class="fas fa-plus"></i>
            </button>
          </div>
        </div>
        <div id="extra_doc_div">

        </div>

      </div>
    </div>

    <div class="card shadow rounded mt-4">
      <div class="card-header pt-3 pb-2 bg-success-subtle">
        <div class="row">
          <div class="col-lg-12">
            <h4>Invoice Details</h4>
          </div>
        </div>
      </div>
      <div class="card-body p-3" id="invoice_pending_data">
        <?php
        if(isset($client_id_received) && $client_id_received != ""){
          $client_id = $client_id_received;
          // Step 1: Fetch all invoices with related data in a single query
          $invoicesQuery = TblInvoice::find()
          ->alias('i')
          ->select(['i.id', 'i.invoice_number', 'i.total_amount', 'i.fk_client_id'])
          ->leftJoin(['ir' => 'tbl_account_receivable'], 'ir.fk_invoice_id = i.id AND ir.status != 0')
          ->leftJoin(['c' => 'tbl_client'], 'c.id = i.fk_client_id')
          ->addSelect(['SUM(ir.amount_received) AS total_amount_received', 'c.company_name AS client_name'])
          ->where('i.status != 0')
          ->andWhere('fk_client_id = '.$client_id)
          ->andWhere('i.total_amount > 0')
          ->groupBy(['i.id'])
          ->asArray()
          ->all();
          // print_r($invoicesQuery);
          $return_str = '<div class="table-responsive">
          <table class="table table-bordered table-striped">
          <thead>
          <tr class="table-dark">
            <th> <input type="checkbox" id="check_all_invoices" /> All</th>
          <th>Sr. No.</th>
          <th>Invoice #</th>
          <th>Total Amount</th>
          <th>Amount Received</th>
          <th>Amount Pending</th>
          <th>Settle</th>
          </tr>
          </thead>
          <tbody>';
          $i = 1;
          foreach($invoicesQuery as $invoice) {
            $totalAmountReceived = $invoice['total_amount_received'] ?? 0.00;
            $totalAmountPending = $invoice['total_amount']-$totalAmountReceived;
            if($totalAmountPending > 0){
              $return_str .= '<tr>
              <td>
              <input type="checkbox" class="check_invoice" data-id="'.$i.'"/>
              </td>
              <td>'.$i.'</td>
              <td>'.$invoice['invoice_number'].'</td>
              <td>'.$invoice['total_amount'].'</td>
              <td>'.$totalAmountReceived.'</td>
              <td>'.$totalAmountPending.'</td>
              <td>
              <input type="hidden" name="invoice_id[]" value="'.$invoice['id'].'" />
              <input type="hidden" name="invoice_pending_amount[]" value="'.number_format($totalAmountPending, 2, '.', '').'" id="invoice_pending_amount_'.$i.'" />
              <input type="text" class="form-control floatNumberField" onkeyup="checkDec(this);" name="pending_amount[]" id="settle_amount_'.$i.'" />
              </td>
              </tr>';
              $i++;
            }

          }//------for loop ended------
          $return_str .= '</tbody></table></div>';
          echo $return_str;
        }
        ?>
      </div>
    </div>

    <div class="row text-center mt-4 mb-4">
        <div class="form-group">
            <?= Html::submitButton('Settle', ['class' => 'btn btn-primary btn-sm w-100']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$js = <<<JS

const decimalPlaces = 2;
// $('body').on('submit', '#form_multiple', function (e) {
$('body').on('beforeSubmit', 'form#w0', function () {
  // e.preventDefault();
  // alert("befor submit");
  $("#client_error").html("");
  $("#total_error").html("");
  var form = $(this);
  //get value of radio button payroll_type by name and if it is 1 skip this part else check this validation
  var invoice_id = $('#tblclientreceipt-fk_client_id').val();
  if(invoice_id == ""){
    //check if sub task is selected or not
    $("#client_error").html('Please select Customer.');
    $(window).scrollTop($('#client_error').offset().top);
    return false;
  }else{
    //check if total amount is equal to the sum of entered amounts
    var total_amount = $("#tblclientreceipt-amount_received").val();
    var pending_amount = 0;
    $("input[name='pending_amount[]']").each(function(){
      if($(this).val() != ""){
        pending_amount += parseFloat($(this).val());
      }
    });
    console.log("pending amount::"+pending_amount);
    if(parseFloat(total_amount).toFixed(decimalPlaces) == parseFloat(pending_amount).toFixed(decimalPlaces)){
      $('#loader-overlay').css('display', 'flex');
      const submitButton = form.find('button[type="submit"], input[type="submit"]');
      submitButton.prop('disabled', true);
      // e.currentTarget.submit();
      return true;
    }else{
      $("#total_error").html("Amount should match the settle amount(s)");
      $(window).scrollTop($('#total_error').offset().top);
      return false;
    }
    // return true;

  }

});
JS;

$this->registerJs($js);
?>
<?php
  $this->registerJs('
    var deadline = $("#tblclientreceipt-ar_date").flatpickr({
      disableMobile: "true",
      enableTime: "false",
      dateFormat: "Y-m-d"
    });
    //--------choices for location, customer -------

    const element2 = document.getElementById("tblclientreceipt-fk_client_id");
    const choices2 = new Choices(element2, {
      removeItemButton: true,
      placeholder: true,
      allowHTML: true
    });

    $("#tblclientreceipt-fk_client_id").change(function(){
      let client_id = $("#tblclientreceipt-fk_client_id").val();
      $.post("index.php?r=account-receivable%2Fget-pending-invoice",{client_id:client_id},function(r){
        console.log(r);
        $("#invoice_pending_data").html(r);
      });
    });



    //--------images plus/minus button code started -------------------
    $(document).on("click",".btn_add_doc_row",function(e){
      e.preventDefault();

      let content = "<div class=\"row extra_row_doc\"><hr /><div class=\"col-lg-3 mb-2\"><input type=\"file\" name=\"doc_file[]\" class=\"form-control\" accept=\"image/*,application/pdf\"></div><div class=\"col-lg-3 mb-2\"><button type=\"button\" class=\"btn btn-primary btn-sm btn_add_doc_row\"><i class=\"fas fa-plus\"></i></button>&nbsp;<button type=\"button\" class=\"btn btn-danger btn-sm btn_remove_doc_row\"><i class=\"fas fa-minus\"></i></button></div></div>";
      $("#extra_doc_div").append(content);

    });
    $(document).on("click",".btn_remove_doc_row",function(e){
      e.preventDefault();
      $(this).parent().parent(".extra_row_doc").remove();
    });
  ');
  $js = <<<JS
    // Handle single checkbox change
    $(document).on('change', '.check_invoice', function() {
      var counter = $(this).data('id');
      var pending = parseFloat($("#invoice_pending_amount_" + counter).val()) || 0;

      if ($(this).is(':checked')) {
        $("#settle_amount_" + counter).val(pending.toFixed(2));
        $(this).closest('tr').addClass('table-info');
      } else {
        $("#settle_amount_" + counter).val('');
        $(this).closest('tr').removeClass('table-info');
      }

      updateTotalPaid();
    });

    // Handle "Mark All" checkbox
    $(document).on('change', '#check_all_invoices', function() {
      var isChecked = $(this).is(':checked');

      $(".check_invoice").each(function() {
        var counter = $(this).data('id');
        var pending = parseFloat($("#invoice_pending_amount_" + counter).val()) || 0;

        $(this).prop('checked', isChecked);
        if (isChecked) {
          $("#settle_amount_" + counter).val(pending.toFixed(2));
          $(this).closest('tr').addClass('table-info');
        } else {
          $("#settle_amount_" + counter).val('');
          $(this).closest('tr').removeClass('table-info');
        }
      });

      updateTotalPaid();
    });

    // Update totalPaid when any settle_amount input changes manually
    $(document).on('input', "input[name='pending_amount[]']", function() {
      var counter = $(this).attr('id').split("_")[2];
      var value = parseFloat($(this).val()) || 0;
      var pending = parseFloat($("#invoice_pending_amount_" + counter).val()) || 0;

      // If user enters more than pending, reset to pending
      if (value > pending) {
        $(this).val(pending.toFixed(2));
        value = pending;
      }

      // Update checkbox highlight based on value
      if (value > 0) {
        $(".check_invoice[data-id='" + counter + "']").prop('checked', true);
        $(this).closest('tr').addClass('table-info');
      } else {
        $(".check_invoice[data-id='" + counter + "']").prop('checked', false);
        $(this).closest('tr').removeClass('table-info');
      }

      updateTotalPaid();
    });

    // Function to sum all settle_amount inputs
    function updateTotalPaid() {
      var totalPaid = 0;
      $("input[name='pending_amount[]']").each(function() {
        var counter = $(this).attr('id').split("_")[2];
        var val = parseFloat($("#settle_amount_" + counter).val()) || 0;
        totalPaid += val;
      });

      $("#tblclientreceipt-amount_received").val(totalPaid.toFixed(2));
    }
  JS;
  $this->registerJs($js);
  ?>
