<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\TblEmployee;
use app\models\TblProduct;
use app\models\TblTaxRate;
use app\models\TblVendor;
use app\models\TblPurchaseOrder;
use app\models\TblPurchaseOrderItem;
use app\models\TblPurchaseStatus;
use app\models\TblPurchasePaymentStatus;
/** @var yii\web\View $this */
/** @var app\models\TblPurchaseOrder $model */
/** @var yii\widgets\ActiveForm $form */

$session = Yii::$app -> session;
$fk_location_id = $session['userCompany'];

//-----get employees------------
$allemps = TblEmployee::find()->where(['status'=>1,'fk_location_id'=>$fk_location_id])->all();
$emparr = ArrayHelper::map($allemps,'id','name');

$allvendors = TblVendor::find()->where('status != 0')->andWhere(['fk_location_id'=>$fk_location_id])->orderBy(['company_name'=>SORT_ASC])->all();
$vendorarr = ArrayHelper::map($allvendors,'id','company_name');

//-------get purchase status ----
$allpstatuses = TblPurchaseStatus::find()->where(['status'=>1])->all();
$pstatusarr = ArrayHelper::map($allpstatuses,'id','title');

//-------get payment status ----
$allpaymentstatuses = TblPurchasePaymentStatus::find()->where(['status'=>1])->all();
$paymentstatusarr = ArrayHelper::map($allpaymentstatuses,'id','title');


//-----get tax rates  --------
$alltaxrates = TblTaxRate::find()->where(['status'=>1])->all();
// $truckarr = ArrayHelper::map($alltrucks,'id','unit_no');
$taxarr = ArrayHelper::map($alltaxrates, 'id', function($model) {
  // return $model->unit_no . ' | Plate No. ' . $model->plate;
  return $model->tax_rate.'% ['.$model->province_state.']';
});
if($model->isNewRecord){
  $model->expected_delivery_date = date('Y-m-d');
  $model->po_date = date('Y-m-d');
  $get = TblPurchaseOrder::find()->where(['status'=>1])->orderBy(['id'=>SORT_DESC])->one();
  if(isset($get) && $get->id != ""){
    $invoice_no = intval($get->po_number)+1;
    $new_number = sprintf('%04d', $invoice_no);
  }else{
    $invoice_no = 1001;
    $new_number = sprintf('%04d', $invoice_no);
  }
  $model->po_number = $new_number;
  $model->hst = 9;
}
?>

<div class="tbl-purchase-order-form">
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
  <?php $form = ActiveForm::begin(); ?>
  <div class="card shadow rounded mt-2">
    <div class="card-body p-3">
      <div class="row">
        <div class="col-lg-2">
          <?= $form->field($model, 'po_number')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-2">
          <?= $form->field($model, 'po_date')->textInput() ?>
        </div>
        <div class="col-lg-4">
          <?= $form->field($model, 'fk_vendor_id')->dropDownList($vendorarr,['prompt'=>'Select','data-choices'=>'data-choices','data-options'=>'{"removeItemButton":true,"placeholder":true}']) ?>
        </div>
        <div class="col-lg-4">
          <?= $form->field($model, 'hst')->dropDownList($taxarr,['prompt'=>'Select','class'=>'form-select']) ?>
        </div>
      </div>

    </div><!-- card body ended -->
  </div><!-- card ended -->

  <div class="card shadow rounded mt-2">
    <div class="card-body p-3">
      <div class="row">

        <div class="col-lg-3">
          <?= $form->field($model, 'purchase_status')->dropDownList($pstatusarr,['prompt'=>'Select','class'=>'form-select']) ?>
        </div>
        <div class="col-lg-3">
          <?= $form->field($model, 'payment_status')->dropDownList($paymentstatusarr,['prompt'=>'Select','class'=>'form-select']) ?>

        </div>
        <div class="col-lg-3">
          <?= $form->field($model, 'approved_by')->dropDownList($emparr,['prompt'=>'Select','class'=>'form-select']) ?>
        </div>
        <div class="col-lg-3">
          <?= $form->field($model, 'expected_delivery_date')->textInput() ?>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow rounded mt-2">
    <div class="card-header pt-3 pb-2">
      <div class="row">
        <div class="col-lg-6">
          <h3>Add Items</h3>
        </div>
      </div>
    </div>
    <div class="card-body p-3">
      <?php
      if(!$model->isNewRecord){
        $getitems = TblPurchaseOrderItem::find()->where(['fk_purchase_order_id'=>$model->id,'status'=>1])->all();
        if(isset($getitems) && count($getitems) > 0){
          $i = 0;
          foreach($getitems as $gi){
            if($i == 0){
              ?>
              <div class="row">
                <div class="col-lg-3" id="product_div_0">
                  <label>Item</label>
                  <select class="form-control product_select" name="product[]" id="product_0">
                    <option value="">Select</option>
                    <?php
                    $getproducts = TblProduct::find()->where(['status'=>1])->orderBy(['id'=>SORT_ASC])->all();
                    if(isset($getproducts) && count($getproducts) > 0){
                      foreach($getproducts as $gp){
                        if($gi->fk_product_id != "" && $gi->fk_product_id == $gp->id){
                          echo '<option value="'.$gp->id.'" selected="selected">'.$gp->name.' ['.$gp->description.']</option>';
                        }else{
                          echo '<option value="'.$gp->id.'">'.$gp->name.' ['.$gp->description.']</option>';
                        }
                      }
                    }
                    ?>
                  </select>
                  <?php
                  //------get the current product qty and qty entered here and then we will check if qty has been changed ------
                  if($gi->fk_product_id != ""){
                    $getprod = TblProduct::find()->where(['id'=>$gi->fk_product_id])->one();
                    if(isset($getprod) && $getprod->id != ""){
                      echo '<input type="hidden" name="product_qty[]" value="'.$getprod->quantity_in_stock.'" />
                      <input type="hidden" name="old_qty[]" value="'.$gi->quantity.'" />';
                    }else{
                      echo '<input type="hidden" name="product_qty[]" value="0" />
                      <input type="hidden" name="old_qty[]" value="0" />';
                    }
                  }else{
                    echo '<input type="hidden" name="product_qty[]" value="0" />
                    <input type="hidden" name="old_qty[]" value="0" />';
                  }
                  ?>
                </div>
                <div class="col-lg-3">
                  <label>Description</label>
                  <input type="text" name="desc[]" id="desc_<?=$i?>" value="<?=$gi->description?>" placeholder="Description" required="required" class="form-control">
                </div>
                <div class="col-lg-1">
                  <label>Qty</label>
                  <input type="text" name="qty[]" id="qty_<?=$i?>" value="<?=$gi->quantity?>" placeholder="0" required="required" onkeyup="checkNum(this);" class="form-control qty">
                </div>
                <div class="col-lg-1">
                  <label>Rate</label>
                  <input type="text" name="rate[]" id="rate_<?=$i?>" value="<?=$gi->unit_price?>" placeholder="0" onkeyup="checkDec(this);" class="form-control floatNumberField rate">
                </div>
                <div class="col-lg-1">
                  <label>Amount</label>
                  <input type="text" name="amount[]" id="amount_<?=$i?>" value="<?=$gi->total_price?>" placeholder="0" class="form-control amount" readonly="readonly">
                </div>
                <div class="col-lg-2">
                  <label>Act.</label><br>
                  <button class="btn btn-primary btn_add_project btn-sm"><i class='fa fa-plus'></i></button>
                </div>
              </div>
              <?php
            }else{
              ?>
              <div class="row row_extra mt-3">
                <div class="col-lg-3" id="product_div_0">
                  <select class="form-control product_select" name="product[]" id="product_0">
                    <option value="">Select</option>
                    <?php
                    $getproducts = TblProduct::find()->where(['status'=>1])->orderBy(['id'=>SORT_ASC])->all();
                    if(isset($getproducts) && count($getproducts) > 0){
                      foreach($getproducts as $gp){
                        if($gi->fk_product_id != "" && $gi->fk_product_id == $gp->id){
                          echo '<option value="'.$gp->id.'" selected="selected">'.$gp->name.' ['.$gp->description.']</option>';
                        }else{
                          echo '<option value="'.$gp->id.'">'.$gp->name.' ['.$gp->description.']</option>';
                        }
                      }
                    }
                    ?>
                  </select>
                  <?php
                  //------get the current product qty and qty entered here and then we will check if qty has been changed ------
                  if($gi->fk_product_id != ""){
                    $getprod = TblProduct::find()->where(['id'=>$gi->fk_product_id])->one();
                    if(isset($getprod) && $getprod->id != ""){
                      echo '<input type="hidden" name="product_qty[]" value="'.$getprod->quantity_in_stock.'" />
                      <input type="hidden" name="old_qty[]" value="'.$gi->quantity.'" />';
                    }else{
                      echo '<input type="hidden" name="product_qty[]" value="0" />
                      <input type="hidden" name="old_qty[]" value="0" />';
                    }
                  }else{
                    echo '<input type="hidden" name="product_qty[]" value="0" />
                    <input type="hidden" name="old_qty[]" value="0" />';
                  }
                  ?>
                </div>
                <div class="col-lg-3">
                  <input type="text" name="desc[]" id="desc_<?=$i?>" value="<?=$gi->description?>" placeholder="Description" required="required" class="form-control">
                </div>
                <div class="col-lg-1">
                  <input type="text" name="qty[]" id="qty_<?=$i?>" value="<?=$gi->quantity?>" placeholder="0" required="required" onkeyup="checkNum(this);" class="form-control qty">
                </div>
                <div class="col-lg-1">
                  <input type="text" name="rate[]" id="rate_<?=$i?>" value="<?=$gi->unit_price?>" placeholder="0" onkeyup="checkDec(this);" class="form-control floatNumberField rate">
                </div>
                <div class="col-lg-1">
                  <input type="text" name="amount[]" id="amount_<?=$i?>" value="<?=$gi->total_price?>" placeholder="0" class="form-control amount" readonly="readonly">
                </div>
                <div class="col-lg-2">
                  <button class="btn btn-primary btn_add_project btn-sm"><i class='fa fa-plus'></i></button>
                  &nbsp;&nbsp;<button class='btn btn-primary btn_remove_project btn-sm'><i class='fa fa-minus'></i></button>
                </div>
              </div>
              <?php
            }
            $i++;
          }
          echo '<div id="add_projects"></div>
          <input type="hidden" id="project_count" value="'.count($getitems).'">';
        }else{
          //---------no item found ------------
          ?>
          <div class="row">
            <div class="col-lg-3" id="product_div_0">
              <label>Item</label>
              <select class="form-control product_select" name="product[]" id="product_0">
                <option value="">Select</option>
                <?php
                $getproducts = TblProduct::find()->where(['status'=>1])->orderBy(['id'=>SORT_ASC])->all();
                if(isset($getproducts) && count($getproducts) > 0){
                  foreach($getproducts as $gp){
                    echo '<option value="'.$gp->id.'">'.$gp->name.' ['.$gp->description.']</option>';
                  }
                }
                ?>
              </select>
            </div>
            <div class="col-lg-3">
              <label>Description</label>
              <input type="text" name="desc[]" id="desc_0" placeholder="Description" required="required" class="form-control">
            </div>
            <div class="col-lg-1">
              <label>Qty</label>
              <input type="text" name="qty[]" id="qty_0" placeholder="0" required="required" onkeyup="checkNum(this);" class="form-control qty">
            </div>
            <div class="col-lg-1">
              <label>Rate</label>
              <input type="text" name="rate[]" id="rate_0" placeholder="0" onkeyup="checkDec(this);" class="form-control floatNumberField rate">
            </div>
            <div class="col-lg-1">
              <label>Amount</label>
              <input type="text" name="amount[]" id="amount_0" placeholder="0" class="form-control amount" readonly="readonly">
            </div>
            <div class="col-lg-2">
              <label>Act.</label><br>
              <button class="btn btn-primary btn_add_project btn-sm"><i class='fa fa-plus'></i></button>
            </div>
          </div>
          <div id="add_projects"></div>
          <input type="hidden" id="project_count" value="1">
          <?php
        }
      }else{
        //----------its a new record ------------
        ?>
        <div class="row">
          <div class="col-lg-3" id="product_div_0">
            <label>Item</label>
            <select class="form-control product_select" name="product[]" id="product_0">
              <option value="">Select</option>
              <?php
              $getproducts = TblProduct::find()->where(['status'=>1])->orderBy(['id'=>SORT_ASC])->all();
              if(isset($getproducts) && count($getproducts) > 0){
                foreach($getproducts as $gp){
                  echo '<option value="'.$gp->id.'">'.$gp->name.' ['.$gp->description.']</option>';
                }
              }
              ?>
            </select>
          </div>
          <div class="col-lg-3">
            <label>Description</label>
            <input type="text" name="desc[]" id="desc_0" placeholder="Description" required="required" class="form-control">
          </div>
          <div class="col-lg-1">
            <label>Qty</label>
            <input type="text" name="qty[]" id="qty_0" placeholder="0" required="required" onkeyup="checkNum(this);" class="form-control qty">
          </div>
          <div class="col-lg-1">
            <label>Rate</label>
            <input type="text" name="rate[]" id="rate_0" placeholder="0" onkeyup="checkDec(this);" class="form-control floatNumberField rate">
          </div>
          <div class="col-lg-1">
            <label>Amount</label>
            <input type="text" name="amount[]" id="amount_0" placeholder="0" class="form-control amount" readonly="readonly">
          </div>
          <div class="col-lg-2">
            <label>Act.</label><br>
            <button class="btn btn-primary btn_add_project btn-sm"><i class='fa fa-plus'></i></button>
          </div>
        </div>
        <div id="add_projects"></div>
        <input type="hidden" id="project_count" value="1">
        <?php
      }
      ?>
    </div>
  </div>
  <?php
  if(!$model->isNewRecord){
    $subtotal = $model->subtotal;
    $hst_amount = $model->hst_amount;
    $total_amount = $model->total_amount;
  }else{
    $subtotal = 0.00;
    $hst_amount = 0.00;
    $total_amount = 0.00;
  }
  ?>
  <div class="card shadow rounded mt-2">
    <div class="card-body p-3">
      <div class="row">
        <div class="col-lg-5">
          <?= $form->field($model, 'comments')->textarea(['rows' => 4]) ?>
        </div>
        <div class="col-lg-5">
          <?= $form->field($model, 'internal_notes')->textarea(['rows' => 4]) ?>
        </div>
        <div class="col-lg-2 text-end">
          <div class="row g-3" style="font-weight:600;">

            <input type="hidden" id="subtotal" name="TblPurchaseOrder[subtotal]" value="<?=$subtotal?>">
            <input type="hidden" id="hst_amount" name="TblPurchaseOrder[hst_amount]" value="<?=$hst_amount?>">
            <input type="hidden" id="total" name="TblPurchaseOrder[total_amount]" value="<?=$total_amount?>">
            <div class="col-6 mt-3">
              Subtotal:
            </div>
            <div class="col-6 mt-3" id="subtotal_calc">
              $<?=$subtotal?>
            </div>
          </div>
          <div class="row g-3" style="font-weight:600;">
            <div class="col-6 mt-3">
              Taxes:
            </div>
            <div class="col-6 mt-3" id="hst_calc">
              $<?=$hst_amount?>
            </div>
          </div>
          <div class="row g-3" style="font-weight:600;">
            <div class="col-6 mt-3">
              Total:
            </div>
            <div class="col-6 mt-3" id="total_calc">
              $<?=$total_amount?>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>


  <div class="card shadow rounded p-3 mt-4 mb-4">
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
<?php
$product_str = "";
$getproducts = TblProduct::find()->where(['status'=>1])->orderBy(['id'=>SORT_ASC])->all();
if(isset($getproducts) && count($getproducts) > 0){
  foreach($getproducts as $gp){
    $productId = htmlspecialchars($gp->id, ENT_QUOTES); // Escape ID
    $productName = htmlspecialchars($gp->name, ENT_QUOTES); // Escape name
    $productSKU = htmlspecialchars(str_replace(array("\r", "\n"), ' ', $gp->description), ENT_QUOTES); // Escape description and replace line breaks
    // $product_str .= "<option value='".$gp->id."'>".$gp->name." [".$gp->sku."]</option>";
    $product_str .= "<option value='" . $productId . "'>" . $productName . "[".$productSKU."]</option>";
  }
}
$this->registerJs('
// Initialize Choices.js on all existing .product-select elements
$(".product_select").each(function() {
  new Choices(this, {
    allowHTML: true
  });  // Initialize Choices.js on the current element
});
$(document).on("click",".btn_add_project",function(e){
  e.preventDefault();
  let cc = $("#project_count").val();
  let str = "<div class=\'row row_extra mt-3\'><div class=\'col-lg-3\' id=\'product_div_"+cc+"\'><select class=\'form-control product_select\' name=\'product[]\' id=\'product_"+cc+"\'><option value=\'\'>Select</option>'.$product_str.'</select></div><div class=\'col-lg-3\'><input type=\'text\' name=\'desc[]\' id=\'desc_"+cc+"\' placeholder=\'Description\' class=\'form-control\' required=\'required\'></div><div class=\'col-lg-1\'><input type=\'text\' name=\'qty[]\' id=\'qty_"+cc+"\' placeholder=\'0\' class=\'form-control qty\' required=\'required\' onkeyup=\'checkNum(this);\'></div><div class=\'col-lg-1\'><input type=\'text\' name=\'rate[]\' id=\'rate_"+cc+"\' placeholder=\'0\' class=\'form-control floatNumberField rate\' required=\'required\' onkeyup=\'checkDec(this);\'></div><div class=\'col-lg-1\'><input type=\'text\' name=\'amount[]\' id=\'amount_"+cc+"\' placeholder=\'0\' class=\'form-control amount\' readonly=\'readonly\'></div><div class=\'col-lg-2\'><button class=\'btn btn-primary btn_add_project btn-sm\'><i class=\'fa fa-plus\'></i></button>&nbsp;&nbsp;<button class=\'btn btn-primary btn_remove_project btn-sm\'><i class=\'fa fa-minus\'></i></button></div></div>";
  $("#add_projects").append(str);
  var c= parseInt(cc)+1;
  $("#project_count").val(c);

    // Initialize Choices.js on the newly added select
    let newSelect = $("#product_" + cc);
    if (!newSelect[0].hasAttribute("data-choice")) {
      new Choices(newSelect[0], {
        allowHTML: true
      });
    }
});
$(document).on("click",".btn_remove_project",function(e){
  e.preventDefault();
  $(this).parent().parent(".row_extra").remove();
  calculatetotalwithtaxes();
});
//--------------terms logic as per customer selection starts----------------

var invoice_date_picker = $("#tblpurchaseorder-po_date,#tblpurchaseorder-expected_delivery_date").flatpickr({
  disableMobile: "true",
  dateFormat: "Y-m-d"
});

//--------------terms logic as per customer selection ends------------------
function calculate(idval){
  let qty = $("#qty_"+idval).val();
  let rate = $("#rate_"+idval).val();
  if(qty != "" && rate != ""){
    let amount = qty*rate;
    $("#amount_"+idval).val(amount.toFixed(2));
  }
}
$(document).on("keyup",".qty",function(){
  let id = $(this).attr("id");
  let index = id.indexOf("_");
  let idval = id.substring(parseInt(index)+parseInt(1));
  calculate(idval);
  calculatetotalwithtaxes()
});
$(document).on("keyup",".rate",function(){
  let id = $(this).attr("id");
  let index = id.indexOf("_");
  let idval = id.substring(parseInt(index)+parseInt(1));
  calculate(idval);
  calculatetotalwithtaxes()
});
$("#tblpurchaseorder-hst").blur(function(){
  calculatetotalwithtaxes();
});
function calculatetotalwithtaxes(){
  let sub_total = 0;
  let total_hst = 0;
  $(".amount").each(function(){
    quantity = parseFloat($(this).val());
    if (!isNaN(quantity) && quantity != "") {
      sub_total += quantity;
    }
  });//each for amount ended
  sub_total = sub_total.toFixed(2);

  //--------new code for hst ----------
  var selectedOption = $("#tblpurchaseorder-hst").find("option:selected");
  var selectedOptionId = $("#tblpurchaseorder-hst").val();
  var displayValue = selectedOption.text().match(/\d+\.\d+/);
  console.log("display value:::::::::"+displayValue);
  var taxPercentage = parseFloat(displayValue);

  if(taxPercentage !== "" && $.isNumeric(taxPercentage)){
    total_hst = sub_total*(taxPercentage/100);
  }

  total_hst = total_hst.toFixed(2);

  let total_amount = parseFloat(sub_total)+parseFloat(total_hst);
  document.getElementById("subtotal_calc").innerHTML = "$"+sub_total;
  document.getElementById("hst_calc").innerHTML = "$"+total_hst;
  document.getElementById("total_calc").innerHTML = "$"+total_amount.toFixed(2);
  //-----subtotal hst_amount total-----
  document.getElementById("subtotal").value = sub_total;
  document.getElementById("hst_amount").value = total_hst;
  document.getElementById("total").value = total_amount;
}

$(document).on("change",".product_select",function(e){
  e.preventDefault();
  let value = $(this).val();
  let id = $(this).attr("id");
  let index = id.indexOf("_");
  let idval = id.substring(parseInt(index)+parseInt(1));
  $.post("index.php?r=invoice/getproductdesc",{product_id:value},function(r){
    // console.log(r);
    var obj = JSON.parse(r);
    var description = obj.description;
    var price = obj.price;
    var tier = obj.tier;
    $("#desc_"+idval).val(description);
  });
});
');
?>
