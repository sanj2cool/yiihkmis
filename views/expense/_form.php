<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\TblLocation;
use app\models\TblExpenseCategory;
use app\models\TblPreferredPaymentMethod;
use app\models\TblTaxRate;
use app\models\TblVendor;


/** @var yii\web\View $this */
/** @var app\models\TblExpense $model */
/** @var yii\widgets\ActiveForm $form */


$session = Yii::$app->session;
$user_company = $session['userCompany'];
$fk_user_id = $session['userId'];

$selected_location = $user_company;

//---GET VENDORS AS PER THE LOCATION ONLY -----

$allclients = TblVendor::find()->where(['status'=>1,'fk_location_id'=>$user_company])->orderBy(['company_name'=>SORT_ASC])->all();
$clientarr = ArrayHelper::map($allclients,'id','company_name');

$gethst = TblLocation::find()->where(['id'=>$selected_location])->one()->tax_id;
$model->fk_tax_rate_id = $gethst;

$categories = TblExpenseCategory::find()->where(['status'=>1])->orderBy(['title'=>SORT_ASC])->all();
$categoriesarr = ArrayHelper::map($categories,'id','title');

$allpaymethods = TblPreferredPaymentMethod::find()->where(['status'=>1])->orderBy(['title'=>SORT_ASC])->all();
$paymethodarr = ArrayHelper::map($allpaymethods,'id','title');

//-----get tax rates  --------
$alltaxrates = TblTaxRate::find()->where(['status'=>1])->all();
$taxarr = ArrayHelper::map($alltaxrates, 'id', function($model) {
  return $model->tax_rate.'% ['.$model->province_state.']';
});

//---NEED TO GET VENDORS AS PER LOCATION ----

if($model->isNewRecord){
  $model->date = date('Y-m-d');
}

?>

<div class="tbl-expense-form">
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

  <div class="card shadow rounded mt-2">
    <div class="card-header p-3 border-bottom border-300 bg-success-subtle">
      <div class="row g-3 justify-content-between align-items-center">
        <div class="col-12 col-md">
          <h5 class=" mb-0" id="basic-details">Basic Details</h5>
        </div>
      </div>
    </div>
    <div class="card-body p-3">


      <div class="row">

        <div class="col-lg-4 mb-2">
          <div class="row">
            <div class="col-12"><label for="">Vendor<span class="text-danger">*</span></label></div>

          </div>
          <?= $form->field($model, 'fk_vendor_id')->dropDownList($clientarr,['prompt' => 'Select','class'=>'form-select'])->label(false) ?>
        </div>
        <div class="col-lg-4 mb-2">
          <?= $form->field($model, 'date')->textInput()->label('Date<span class="text-danger">*</span>') ?>
        </div>

        <div class="col-lg-4 mb-2">
          <?= $form->field($model, 'receipt_no')->textInput(['maxlength' => true]) ?>
        </div>

      </div>
      <div class="row">
        <div class="col-lg-3 mb-2">
          <?= $form->field($model, 'amount')->textInput(['maxlength' => true,'class'=>'form-control floatNumberField','onkeyup'=>'checkDec(this);'])->label('Amount<span class="text-danger">*</span>') ?>
        </div>
        <div class="col-lg-3 mb-2">
          <?= $form->field($model, 'fk_tax_rate_id')->dropDownList($taxarr,['prompt'=>'Select','class'=>'form-select'])->label('Tax Rate<span class="text-danger">*</span>') ?>
        </div>
        <div class="col-lg-3 mb-2">
          <?= $form->field($model, 'tax_amount')->textInput(['maxlength' => true,'readonly'=>true,'class'=>'form-control bg-light'])->label('Tax Amount<span class="text-danger">*</span>') ?>
        </div>
        <div class="col-lg-3 mb-2">
          <?= $form->field($model, 'final_amount')->textInput(['maxlength' => true,'readonly'=>true,'class'=>'form-control bg-light'])->label('Final Amount<span class="text-danger">*</span>') ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-3 mb-2">
          <?= $form->field($model, 'fk_expense_category_id')->dropDownList($categoriesarr,['prompt'=>'Select','class'=>'form-select'])->label('Expense Category<span class="text-danger">*</span>') ?>
        </div>
        <div class="col-lg-3 mb-2">
          <?= $form->field($model, 'fk_payment_method_id')->dropDownList($paymethodarr,['prompt'=>'Select','class'=>'form-select'])->label('Payment Method<span class="text-danger">*</span>') ?>
        </div>
        <div class="col-lg-6 mb-2">
          <?= $form->field($model, 'notes')->textarea(['rows' => 3]) ?>
        </div>

      </div>
      <?= $form->field($model, 'fk_location_id')->hiddenInput(['value'=>$user_company])->label(false) ?>
    </div><!-- card body ended -->
  </div><!-- card ended -->
  <div class="card shadow rounded mt-2">
    <div class="card-header p-3 border-bottom border-300 bg-primary-subtle">
      <div class="row g-3 justify-content-between align-items-center">
        <div class="col-12 col-md">
          <h5 class="mb-0" id="upload-images">Upload Files</h5>
        </div>
      </div>
    </div>
    <div class="card-body p-3">
      <div class="row">
        <div class="col-lg-3 mb-2">
          <label for="">Upload File</label>
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
      <?php
      if (!$model->isNewRecord) {

        $getdocs = \app\models\TblExpenseFile::find()
        ->where(['fk_expense_id' => $model->id, 'status' => 1])
        ->all();

        if (!empty($getdocs)) {

          echo '<table class="table table-bordered mt-3">
          <thead>
          <tr>
          <th style="width:80px;">#</th>
          <th>Document</th>
          <th style="width:80px;">Act.</th>
          </tr>
          </thead>
          <tbody>';

          $i = 1;

          foreach ($getdocs as $doc) {

            $fileUrl = Yii::$app->request->baseUrl . '/expenses/' . $doc->file_upload;
            $ext = strtolower(pathinfo($doc->file_upload, PATHINFO_EXTENSION));

            echo '<tr>
            <td>' . $i . '</td>
            <td>
            <button
            type="button"
            class="btn btn-sm btn-primary expense-file-preview"
            data-url="' . $fileUrl . '"
            data-type="' . $ext . '">
            View File
            </button>
            </td>
            <td>
            <input type="hidden" name="image_ids[]" value="' . $doc->id . '">
            <a class="text-danger delete-button" data-id="' . $doc->id . '" style="cursor:pointer;">
            <i class="fas fa-trash"></i>
            </a>
            </td>
            </tr>';

            $i++;
          }

          echo '</tbody></table>';
        }
      }
      ?>

    </div>
  </div>

  <div class="card shadow rounded p-3 mt-2 mb-4">
    <div class="row text-center">
      <?php
      if($model->isNewRecord){
        ?>
        <div class="d-grid gap-2 col-4 mx-auto pe-1">
          <input type="submit" name="new_update" value="Create & Edit" class="btn btn-primary btn-sm"/>
        </div>
        <div class="d-grid gap-2 col-4 mx-auto pe-1 ps-1">
          <input type="submit" name="new_new" value="Create & New" class="btn btn-info btn-sm"/>
        </div>
        <div class="d-grid gap-2 col-4 mx-auto ps-1">
          <input type="submit" name="new_exit" value="Create & Exit" class="btn btn-secondary btn-sm"/>
        </div>
        <?php
      }else{
        ?>
        <div class="d-grid gap-2 col-4 mx-auto pe-1">
          <input type="submit" name="update" value="Update & Edit" class="btn btn-primary btn-sm"/>
        </div>
        <div class="d-grid gap-2 col-4 mx-auto pe-1 ps-1">
          <input type="submit" name="new" value="Update & New" class="btn btn-info btn-sm"/>
        </div>
        <div class="d-grid gap-2 col-4 mx-auto ps-1">
          <input type="submit" name="exit" value="Update & Exit" class="btn btn-secondary btn-sm"/>
        </div>
        <?php
      }
      ?>

    </div>
  </div>

  <?php ActiveForm::end(); ?>

</div>


<!-- customer add modal ended -->
<div class="modal fade" id="expenseFileModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Expense File</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="expenseFileContent"></div>
    </div>
  </div>
</div>
<script>
document.addEventListener('click', function (e) {

  if (!e.target.classList.contains('expense-file-preview')) return;

  let url = e.target.dataset.url;
  let type = e.target.dataset.type;
  let html = `
  <div class="d-flex justify-content-between align-items-center mb-2">
  <strong>Preview</strong>
  <a href="${url}" target="_blank" class="btn btn-sm btn-outline-secondary">
  Open / Download
  </a>
  </div>
  `;

  if (type === 'pdf') {
    html += `<iframe src="${url}" style="width:100%; height:70vh;" frameborder="0"></iframe>`;
  } else {
    html += `<div class="text-center">
    <img src="${url}" class="img-fluid" alt="Expense File">
    </div>`;
  }

  document.getElementById('expenseFileContent').innerHTML = html;

  new bootstrap.Modal(document.getElementById('expenseFileModal')).show();
});
</script>


<?php
$this->registerJs('

//----------DATE PICKER FOR REPAIR DATE ------------------
var check_in_time = $("#tblexpense-date").flatpickr({
  disableMobile: "true",
  enableTime: "false",
  dateFormat: "Y-m-d"
});

const element3 = document.getElementById("tblexpense-fk_tax_rate_id");
const choices3 = new Choices(element3, {
  removeItemButton: true,
  placeholder: true,
  allowHTML: true
});
//--------choices for location, customer -------
const element1 = document.getElementById("tblexpense-fk_expense_category_id");
const choices1 = new Choices(element1, {
  removeItemButton: true,
  placeholder: true,
  allowHTML: true
});

const element2 = document.getElementById("tblexpense-fk_payment_method_id");
const choices2 = new Choices(element2, {
  removeItemButton: true,
  placeholder: true,
  allowHTML: true
});

//--------------create client code starts -----------------------
const element21 = document.getElementById("tblexpense-fk_vendor_id");
const choices21 = new Choices(element21, {
  removeItemButton: true,
  placeholder: true,
  allowHTML: true
});

//--------images plus/minus button code started -------------------
$(document).on("click",".btn_add_doc_row",function(e){
  e.preventDefault();

  let content = "<div class=\"row extra_row_doc\"><hr /><div class=\"col-lg-3 mb-2\"><input type=\"file\" name=\"doc_file[]\" class=\"form-control\" accept=\"image/*,application/pdf\"></div><div class=\"col-lg-3 mb-2\"><button type=\"button\" class=\"btn btn-primary btn-sm btn_add_doc_row\"><i class=\"fas fa-plus\"></i></button>&nbsp;<button type=\"button\" class=\"btn btn-danger btn-sm btn_remove_doc_row\"><i class=\"fas fa-minus\"></i></button></div></div>";
  $("#extra_doc_div").append(content);
  docExpiry();
});
$(document).on("click",".btn_remove_doc_row",function(e){
  e.preventDefault();
  $(this).parent().parent(".extra_row_doc").remove();
});
// Handle delete button click
$(".delete-button").click(function() {
  var button = $(this);  // Reference to the button clicked
  var imageId = button.data(\'id\');

  // Show a confirmation dialog
  if (confirm("Are you sure you want to delete this image?")) {

    // Optionally, send an AJAX request to delete the image from the server
    $.post(\'index.php?r=expense/delpics\', { del_id: imageId }, function(response) {
      console.log(\'Image deleted:\', response);
      if(response=="yes"){
        button.closest(\'tr\').remove(); // Remove the image from the list
      }else{
        alert("Something went wrong. Please try again.");
      }
    });
  }

});


//--------images plus/minus button code ended -------------------
$("#tblexpense-fk_tax_rate_id").change(function(){
  let tax_id = $("#tblexpense-fk_tax_rate_id").val();
  if(tax_id != ""){
    calculateTotal();
  }
});
$("#tblexpense-amount").keyup(function(){
  calculateTotal();
});
function calculateTotal(){
  var selectedOption = $("#tblexpense-fk_tax_rate_id").find("option:selected");
  var selectedOptionId = $("#tblexpense-fk_tax_rate_id").val();
  var displayValue = selectedOption.text().match(/\d+\.\d+/);
  // console.log("display value:::::::::"+displayValue);
  var taxPercentage = 0;  // Default value in case the displayValue is null or empty

  if (displayValue && displayValue.length > 0) {
    taxPercentage = parseFloat(displayValue[0]);
  }
  let rate = $("#tblexpense-amount").val();
  if(rate != ""){
    let subtotal = parseFloat(rate);
    let tax_amount = parseFloat(subtotal)*(taxPercentage/100);
    tax_amount = tax_amount.toFixed(2);
    $("#tblexpense-tax_amount").val(tax_amount);
    let total_amount = parseFloat(subtotal)+parseFloat(tax_amount);
    total_amount = total_amount.toFixed(2);
    $("#tblexpense-final_amount").val(total_amount);
  }

}

');
?>
