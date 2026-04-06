<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\TblClient;
use app\models\TblPreferredPaymentMethod;
use app\models\TblInvoice;
use app\models\TblOwnershipCompany;
use yii\db\Query;
/** @var yii\web\View $this */
/** @var app\models\TblAccountReceivable $model */

$this->title = 'Receive Payment';
$this->params['breadcrumbs'][] = ['label' => 'Receive Payments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$session = Yii::$app->session;
$user_company = $session['userCompany'];

$req = Yii::$app->request;
$client_id_received = $req->get('client_id');
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$fk_preferred_payment_method_id = "";
//------if request is coming from client preselect the location, client id and pending invoices----
if($client_id_received != ""){
  //----get location -----
  $get_location = TblClient::find()->where(['id'=>$client_id_received])->andWhere(['fk_location_id'=>$user_company])->andWhere('status != 0')->one();
  if(isset($get_location) && $get_location->id != ""){
    $fk_preferred_payment_method_id = $get_location->fk_preferred_payment_method_id;
    $clients = TblClient::find()
    ->where(['status'=>1])
    ->andWhere(['fk_location_id'=>$user_company])
    ->all();
  }else{
    $clients = TblClient::find()
    ->where(['status'=>1])
    ->andWhere(['fk_location_id'=>$user_company])
    ->all();
  }
}else{
  //------simple request ---------
  $clients = TblClient::find()
  ->where(['status'=>1])
  ->andWhere(['fk_location_id'=>$user_company])
  ->all();
}
$allpaymethods = TblPreferredPaymentMethod::find()->where(['status'=>1])->orderBy(['title'=>SORT_ASC])->all();
?>
<div class="tbl-account-receivable-create">
  <div class="card shadow rounded mt-2">
    <div class="card-body p-3">
      <div class="row">
        <div class="col-lg-6">
          <h3><?= Html::encode($this->title) ?></h3>
        </div>
        <div class="col-lg-6 text-end">
          <?= Html::a('Back to List', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
        </div>
      </div>
    </div>
  </div>
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
  <form method="post" action="<?=Url::to(['account-receivable/create-multiple'])?>" enctype="multipart/form-data" id="form_multiple">
    <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
    <div class="card shadow rounded mt-4">
      <div class="card-header pt-3 pb-2 bg-info-subtle">
        <div class="row">
          <div class="col-lg-12">
            <h3>Basic Details</h3>
          </div>
        </div>
      </div>
      <div class="card-body p-3">
        <div class="row">
          <div class="col-lg-3 mb-2">
            <label for="">Customer<span class="text-danger">*</span></label>
            <select class="form-control" name="client" id="client_select" required>
              <option value="">Select</option>
              <?php
              if(isset($clients) && count($clients) > 0){
                foreach($clients as $c){
                  if(isset($client_id_received) && $client_id_received == $c->id){
                    echo '<option value="'.$c->id.'" selected="selected">'.$c->company_name.'</option>';
                  }else{
                    echo '<option value="'.$c->id.'">'.$c->company_name.'</option>';
                  }
                }//---for loop ended -----
              }//----if isset ended ----
              ?>
            </select>
            <div id="client_error"class="text-danger"></div>
          </div>

          <div class="col-lg-3 mb-2">
            <label for="">Receivable Date<span class="text-danger">*</span></label>
            <input type="text" name="receivable_date" id="receivable_date" value="<?=date('Y-m-d')?>" class="form-control" required>
          </div>
          <div class="col-lg-3 mb-2">
            <label for="">Payment Method<span class="text-danger">*</span></label>
            <select class="form-select" name="mode_of_payment" id="mode_of_payment" required>
              <option value="">Select</option>
              <?php
              if(isset($allpaymethods) && count($allpaymethods) > 0){
                foreach($allpaymethods as $l){
                  if(isset($fk_preferred_payment_method_id) && $fk_preferred_payment_method_id == $l->id){
                    echo '<option value="'.$l->id.'" selected="selected">'.$l->title.'</option>';
                  }else{
                    echo '<option value="'.$l->id.'">'.$l->title.'</option>';
                  }
                }//---for loop ended ----
              }//-----if isset ended ----
              ?>
            </select>
          </div>
          <div class="col-lg-3 mb-2">
            <label for="">Received Amount<span class="text-danger">*</span></label>
            <input type="text" name="total_amount" id="total_amount" class="form-control floatNumberField" onkeyup="checkDec(this);" required="required" />
            <div id="total_error" class="text-danger"></div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12 mb-2">
            <label for="">Notes</label>
            <textarea name="notes" rows="3" class="form-control"></textarea>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow rounded mt-4">
      <div class="card-header pt-3 pb-2 bg-success-subtle">
        <div class="row">
          <div class="col-lg-12">
            <h3>Invoice Details</h3>
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

      <div class="col-12">
        <input type="submit" name="new_exit" value="Settle" class="btn btn-primary btn-sm w-100"/>
      </div>


    </div>
  </form>

</div>
<?php
$js = <<<JS
// $('body').on('submit', '#form_multiple', function (e) {
$('#form_multiple').submit(function(e){
  e.preventDefault();
  // alert("befor submit");
  $("#client_error").html("");
  $("#total_error").html("");
  var form = $(this);
  //get value of radio button payroll_type by name and if it is 1 skip this part else check this validation
  var invoice_id = $('#client_select').val();
  if(invoice_id == ""){
    //check if sub task is selected or not
    $("#client_error").html('Please select Client.');
    $(window).scrollTop($('#client_error').offset().top);
    return false;
  }else{
    //check if total amount is equal to the sum of entered amounts
    var total_amount = $("#total_amount").val();
    var pending_amount = 0;
    $("input[name='pending_amount[]']").each(function(){
      if($(this).val() != ""){
        pending_amount += parseFloat($(this).val());
      }
    });
    pending_amount = pending_amount.toFixed(2);
    console.log("pending amount::"+pending_amount);
    if(parseFloat(total_amount) == parseFloat(pending_amount)){
      e.currentTarget.submit();
      // alert("values matched");
    }else{
      $("#total_error").html("Total Amount should match the settle amount(s)");
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
var invoice_date_picker = $("#receivable_date").flatpickr({
  disableMobile: "true",
  dateFormat: "Y-m-d"
});

//--------------create client code starts -----------------------
const element2 = document.getElementById("client_select");
const choices2 = new Choices(element2, {
  removeItemButton: true,
  placeholder: true,
  allowHTML: true
});

$("#bill_from_company").change(function(){
  getclientinvoices();
});
$("#client_select").change(function(){
  getclientinvoices();
});
function getclientinvoices(){
  let client_id = $("#client_select").val();
  // let bill_from_company = $("#bill_from_company").val();
  let bill_from_company = 0;
  let error = 0;
  // $("#billfrom_error").html("");
  $("#client_error").html("");
  if(client_id == ""){
    $("#client_error").html("Please select customer");
    error++;
  }
  // if(bill_from_company == ""){
  //   $("#billfrom_error").html("Please select receiving company");
  //   error++;
  // }
  if(error == 0){
    $.post("index.php?r=account-receivable%2Fget-pending-invoice",{client_id:client_id,bill_from_company:bill_from_company},function(r){
      console.log(r);
      $("#invoice_pending_data").html(r);
    });
  }
}

$("#total_amount").keyup(function(){
  var total_amount = $("#total_amount").val();
  console.log("entered total amount::"+total_amount);
  if(total_amount != ""){
    $("input[name=\'pending_amount[]\']").each(function(){
      // console.log("looping through input with same name");
      var id = $(this).attr("id");
      console.log("id::"+id);
      var idarr = id.split("_");
      var counter = idarr[2];
      var pending_amount = $("#invoice_pending_amount_"+counter).val();
      console.log("pending amount:::"+parseFloat(pending_amount));
      if(parseFloat(pending_amount) < parseFloat(total_amount)){
        console.log("pending amount is less than total amount");
        $("#settle_amount_"+counter).val(pending_amount);
        total_amount = total_amount-pending_amount;
        console.log("new total::"+total_amount);
      }else{
        console.log("pending amount greater than total amount");
        //pending amount is greater than total left amount its okay assign the left amount to the invoice
        $("#settle_amount_"+counter).val(parseFloat(total_amount).toFixed(2));
        total_amount = 0;
      }
    });
  }else{
    $("input[name=\'pending_amount[]\']").each(function(){
      // console.log("looping through input with same name");
      var id = $(this).attr("id");
      console.log("id::"+id);
      var idarr = id.split("_");
      var counter = idarr[2];
      $("#settle_amount_"+counter).val(0);
    });
  }

});

');
?>
