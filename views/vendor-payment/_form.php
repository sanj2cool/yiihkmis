<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\TblVendor;
use app\models\TblPreferredPaymentMethod;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\TblVendorPayment $model */
/** @var yii\widgets\ActiveForm $form */

$session = Yii::$app -> session;
$fk_location_id = $session['userCompany'];

$allvendors = TblVendor::find()->where('status != 0')->andWhere(['fk_location_id'=>$fk_location_id])->orderBy(['company_name'=>SORT_ASC])->all();
$vendorarr = ArrayHelper::map($allvendors,'id','company_name');

$allpaymethods = TblPreferredPaymentMethod::find()->where(['status'=>1])->orderBy(['title'=>SORT_ASC])->all();
$paymethodarr = ArrayHelper::map($allpaymethods,'id','title');

if($model->isNewRecord){
  $model->ar_date = date('Y-m-d');
}
?>

<div class="tbl-vendor-payment-form">

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
      <div class="card-header pt-3 pb-2 bg-success-subtle">
        <div class="row">
          <div class="col-lg-12">
            <h4>Basic Details</h4>
          </div>
        </div>
      </div>
      <div class="card-body p-3">
        <div class="row">
          <div class="col-lg-3 mb-2">
            <?= $form->field($model, 'fk_vendor_id')->dropDownList($vendorarr,['prompt'=>'Select','class'=>'form-select']) ?>
            <div id="client_error"class="text-danger">
            </div>
          </div>

          <div class="col-lg-3 mb-2">
            <?= $form->field($model, 'ar_date')->textInput(['maxlength' => true]) ?>
          </div>
          <?php
            $this->registerJs('
            var invoice_date_picker = $("#tblvendorpayment-ar_date").flatpickr({
              disableMobile: "true",
              dateFormat: "Y-m-d"
            });
            ');
           ?>
          <div class="col-lg-3 mb-2">
            <?= $form->field($model, 'fk_payment_method_id')->dropDownList($paymethodarr,['prompt'=>'Select','class'=>'form-select']) ?>
          </div>
          <div class="col-lg-3 mb-2">
            <?= $form->field($model, 'amount_received')->textInput(['maxlength' => true,'class'=>'form-control floatNumberField bg-light','readonly'=>true,'onkeyup'=>'checkDec(this);']) ?>
            <div id="total_error" class="text-danger">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12 mb-2">
            <?= $form->field($model, 'notes')->textarea(['rows' => 3]) ?>
          </div>

        </div>
      </div>
    </div>

    <div class="card shadow rounded mt-4">
      <div class="card-header pt-3 pb-2 bg-warning-subtle">
        <div class="row">
          <div class="col-lg-12">
            <h4>Attachments</h4>
          </div>
        </div>
      </div>
      <div class="card-body p-3" >
        <div class="row">
          <div class="col-lg-4 mb-2">
            <label for="">Image</label>
            <input type="file" name="file_url[]" class="form-control" accept="image/*,application/pdf">
            <span style="color:#f00;font-size:11px;">Only images & pdf files allowed</span>
          </div>
          <div class="col-4 mt-4 mb-2">
            <button type="button" name="button" class="btn btn-secondary btn-sm btn_add_before_image">
              <i class="fa-solid fa-plus"></i>
            </button>
          </div>
        </div><!-- row ended -->
        <div id="extra_before"></div>
    </div>


    </div>

    <div class="card shadow rounded mt-4">
      <div class="card-header pt-3 pb-2 bg-info-subtle">
        <div class="row">
          <div class="col-lg-12">
            <h4>Invoice Details</h4>
          </div>
        </div>
      </div>
      <div class="card-body p-3" >
        <div class="table-responsive" id="invoice_pending_data">
        <?php
        if(isset($client_id_received) && $client_id_received != ""){
          $client_id = $client_id_received;
          // Step 1: Fetch all invoices with related data in a single query
          $invoicesQuery = TblVendorInvoice::find()
          ->alias('i')
          ->select(['i.id', 'i.vendor_invoice_number', 'i.total_amount', 'i.fk_client_id'])
          ->leftJoin(['ir' => 'tbl_vendor_account_payable'], 'ir.fk_vendor_invoice_id = i.id AND ir.status != 0')
          ->leftJoin(['c' => 'tbl_vendor'], 'c.id = i.fk_vendor_id')
          ->addSelect(['SUM(ir.amount_received) AS total_amount_received', 'c.company_name AS client_name'])
          ->where('i.status != 0')
          ->andWhere('fk_vendor_id = '.$client_id)
          ->andWhere('i.total_amount > 0')
          ->groupBy(['i.id'])
          ->asArray()
          ->all();
          // print_r($invoicesQuery);
          $return_str = '<div class="table-responsive">
          <table class="table table-bordered table-striped">
          <thead>
          <tr class="table-dark">
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
              <td>'.$i.'</td>
              <td>'.$invoice['vendor_invoice_number'].'</td>
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
    </div>

    <div class=" text-center mt-4 mb-4">
        <?= Html::submitButton('Settle', ['class' => 'btn btn-primary btn-sm w-100']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<style>
/* Add this to your CSS file or <style> tag */
.highlighted-row {
  background-color: #c7ebff; /* light green */
}
</style>

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
  var invoice_id = $('#tblvendorpayment-fk_vendor_id').val();
  if(invoice_id == ""){
    //check if sub task is selected or not
    $("#client_error").html('Please select Vendor.');
    $(window).scrollTop($('#client_error').offset().top);
    return false;
  }else{
    //check if total amount is equal to the sum of entered amounts
    var total_amount = $("#tblvendorpayment-amount_received").val();
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

$js_calculate = <<<JS
//--------------create client code starts -----------------------
const element3 = document.getElementById("tblvendorpayment-fk_vendor_id");
const choices3 = new Choices(element3, {
  removeItemButton: true,
  placeholder: true,
  searchResultLimit: 10,
  shouldSort: false,
  searchFloor: 1,
  allowHTML: true,
  fuseOptions: {
    threshold: 0.1,
    tokenize: true,
    matchAllTokens: true,
    includeScore: true,
    findAllMatches: true
  }
});
$("#tblvendorpayment-fk_vendor_id").change(function(){
  let vendor_id = $("#tblvendorpayment-fk_vendor_id").val();
  $("#tblvendorpayment-amount_paid").val(0);
  $.post("index.php?r=vendor-payment/get-pending-invoice",{vendor_id:vendor_id},function(r){
    // console.log(r);
    $("#invoice_pending_data").html(r);
  });//---post ended --------
});
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

  $("#tblvendorpayment-amount_received").val(totalPaid.toFixed(2));
}
$(document).on("click",".btn_add_before_image",function(e){
  e.preventDefault();
  let content = "<div class=\"row mt-2 rows_extra_before\"><div class=\"col-lg-4 mb-2\"><label>Image</label><input type=\"file\" name=\"file_url[]\" class=\"form-control\" accept=\"image/*,application/pdf\"></div><div class=\"col-4 mt-4 mb-2\"><button type=\"button\" name=\"button\" class=\"btn btn-secondary btn-sm btn_add_before_image\" ><i class=\"fa-solid fa-plus\"></i></button>&nbsp;<button type=\"button\" name=\"button\" class=\"btn btn-secondary btn-sm btn_remove_before_image\"><i class=\"fa-solid fa-minus\"></i></button></div></div>";
  $("#extra_before").append(content);
});
//-------BEFORE IMAGE REMOVE ROW -----

$(document).on("click",".btn_remove_before_image",function(e){
  e.preventDefault();
  $(this).parent().parent(".rows_extra_before").remove();
});
//------keyup function ended --------
JS;
$this->registerJs($js_calculate);
?>
