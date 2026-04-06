<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\TblTerms;
use app\models\TblClient;
use yii\helpers\ArrayHelper;
use app\models\TblTaxRate;
use app\models\TblInvoice;
use app\models\TblInvoiceItem;
use app\models\TblProduct;
use app\models\TblOwnershipCompany;
/** @var yii\web\View $this */
/** @var app\models\TblInvoice $model */
/** @var yii\widgets\ActiveForm $form */
$allterms = TblTerms::find()->where(['status'=>1])->orderBy(['priority'=>SORT_ASC])->all();
$termarr = ArrayHelper::map($allterms,'id','title');
$allclients = TblClient::find()->where(['status'=>1])->orderBy(['company_name'=>SORT_ASC])->all();
$clientarr = ArrayHelper::map($allclients,'id','company_name');

$allbillfrom = TblOwnershipCompany::find()->where(['status'=>1])->orderBy(['company_name'=>SORT_ASC])->all();
$billfromarr = ArrayHelper::map($allbillfrom,'id','location_name');

//-----get tax rates  --------
$alltaxrates = TblTaxRate::find()->where(['status'=>1])->all();
// $truckarr = ArrayHelper::map($alltrucks,'id','unit_no');
$taxarr = ArrayHelper::map($alltaxrates, 'id', function($model) {
    // return $model->unit_no . ' | Plate No. ' . $model->plate;
    return $model->tax_rate.'% ['.$model->province_state.']';
});
if($model->isNewRecord){
  $req = Yii::$app->request;
  $client_id_received = $req->get('client_id');
  if($client_id_received != ""){
    $model->fk_client_id = $client_id_received;
    $get_location = TblClient::find()->where(['id'=>$client_id_received])->andWhere('status != 0')->one();
    if(isset($get_location) && $get_location->id != ""){
      $model->fk_terms_id = $get_location->fk_terms_id;
      $model->invoice_date = date('Y-m-d');
      $invoice_date = date('Y-m-d');
      $getdays = TblTerms::find()->where(['id'=>$get_location->fk_terms_id])->andWhere(['status'=>1])->one();
      $due_date = "";
      if(isset($getdays) && $getdays->id != ""){
        $no_of_days = $getdays->days;
        if($no_of_days > 0){
          $due_date = date('Y-m-d', strtotime($invoice_date. ' + '.$no_of_days.' days'));
        }else{
          $due_date = $invoice_date;
        }
      }else{
        $due_date = $invoice_date;
      }

      $model->due_date = $due_date;
    }else{
      $model->invoice_date = date('Y-m-d');
      $model->due_date = date('Y-m-d');
    }
  }else{
    $model->invoice_date = date('Y-m-d');
    $model->due_date = date('Y-m-d');
  }


    $get = TblInvoice::find()->where(['status'=>1])->orderBy(['id'=>SORT_DESC])->one();
    if(isset($get) && $get->id != ""){
        $invoice_no = intval($get->invoice_number)+1;
        $new_number = sprintf('%04d', $invoice_no);
    }else{
        $invoice_no = 1001;
        $new_number = sprintf('%04d', $invoice_no);
    }
    $model->invoice_number = $new_number;
    $model->hst = 9;

}

?>

<div class="tbl-invoice-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="card shadow rounded mt-4">
      <div class="card-header pt-3 pb-2 bg-success-subtle">
          <div class="row">
              <div class="col-lg-6">
                  <h4>Basic Details</h4>
              </div>
          </div>
      </div>
        <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-4 mb-2">
              <?= $form->field($model, 'invoice_number')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-4 mb-2">
              <?= $form->field($model, 'fk_bill_from_id')->dropDownList($billfromarr,['prompt' => 'Select'])->label('Bill From Company <sup><span class="badge bg-danger">New</span></sup>') ?>
            </div>
            <div class="col-lg-4 mb-2">
              <?= $form->field($model, 'fk_client_id')->dropDownList($clientarr,['prompt'=>'Select','data-choices'=>'data-choices','data-options'=>'{"removeItemButton":true,"placeholder":true,"allowHTML": true}']) ?>
            </div>

          </div>

          <div class="row">
            <div class="col-lg-3 mb-2">
              <?= $form->field($model, 'fk_terms_id')->dropDownList($termarr,['prompt'=>'Select']) ?>
            </div>
            <div class="col-lg-3 mb-2">
              <?= $form->field($model, 'invoice_date')->textInput() ?>
            </div>
            <div class="col-lg-3 mb-2">
              <?= $form->field($model, 'due_date')->textInput() ?>
            </div>
            <div class="col-lg-3 mb-2">
              <?= $form->field($model, 'hst')->dropDownList($taxarr,['prompt'=>'Select']) ?>
            </div>
          </div>

        </div>
      </div>


        <div class="card shadow rounded mt-4">
            <div class="card-header pt-3 pb-2 bg-primary-subtle">
                <div class="row">
                    <div class="col-lg-6">
                        <h4>Item Details</h4>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <?php
                    if(!$model->isNewRecord){
                        $getitems = TblInvoiceItem::find()->where(['fk_invoice_id'=>$model->id,'status'=>1])->all();
                        if(isset($getitems) && count($getitems) > 0){
                            $i = 0;
                            foreach($getitems as $gi){
                                if($i == 0){
                                    ?>
                                    <div class="row">
                                        <div class="col-lg-5 mb-2" id="product_div_<?=$i?>">
                                            <label>Product</label>
                                            <select class="form-control product_select" name="product[]" id="product_<?=$i?>">
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
                                        <div class="col-lg-3 mb-2">
                                          <label>Tier</label>
                                          <select class="form-control tier_select" name="product_tier[]" id="tier_select_<?=$i?>">
                                            <option value="">Select</option>
                                            <option value="1" <?php if($gi->tier_id == 1){ echo 'selected="selected"';} ?>>Tier 1</option>
                                            <option value="2" <?php if($gi->tier_id == 2){ echo 'selected="selected"';} ?>>Tier 2</option>
                                            <option value="3" <?php if($gi->tier_id == 3){ echo 'selected="selected"';} ?>>Tier 3</option>
                                            <option value="4" <?php if($gi->tier_id == 4){ echo 'selected="selected"';} ?>>Tier 4</option>
                                            <option value="5" <?php if($gi->tier_id == 5){ echo 'selected="selected"';} ?>>Tier 5</option>
                                            <option value="6" <?php if($gi->tier_id == 6){ echo 'selected="selected"';} ?>>Tier 6</option>
                                            <option value="7" <?php if($gi->tier_id == 7){ echo 'selected="selected"';} ?>>Tier 7</option>
                                            <option value="8" <?php if($gi->tier_id == 8){ echo 'selected="selected"';} ?>>Tier 8</option>
                                            <option value="9" <?php if($gi->tier_id == 9){ echo 'selected="selected"';} ?>>Tier 9</option>
                                            <option value="10" <?php if($gi->tier_id == 10){ echo 'selected="selected"';} ?>>Tier 10</option>
                                            <option value="11" <?php if($gi->tier_id == 11){ echo 'selected="selected"';} ?>>Tier 11</option>
                                          </select>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <label>Description</label>
                                            <input type="text" name="desc[]" id="desc_<?=$i?>" value="<?=$gi->description?>" placeholder="Description" required="required" class="form-control">
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                            <label>Qty</label>
                                            <input type="text" name="qty[]" id="qty_<?=$i?>" value="<?=$gi->quantity?>" placeholder="0" required="required" onkeyup="checkDec(this);" class="form-control floatNumberField qty">
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                            <label>Rate</label>
                                            <input type="text" name="rate[]" id="rate_<?=$i?>" value="<?=$gi->unit_price?>" placeholder="0" onkeyup="checkDec(this);" class="form-control floatNumberField rate">
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                            <label>Amount</label>
                                            <input type="text" name="amount[]" id="amount_<?=$i?>" value="<?=$gi->total_price?>" placeholder="0" class="form-control amount bg-light" tabindex="-1" readonly="readonly">
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                            <label>Act.</label><br>
                                            <button class="btn btn-primary btn_add_project btn-sm"><i class='fa fa-plus'></i></button>
                                        </div>
                                    </div>
                                    <?php
                                }else{
                                    ?>
                                    <div class="row row_extra mt-3">
                                      <hr class="text-primary" />
                                        <div class="col-lg-5 mb-2" id="product_div_<?=$i?>">
                                          <label>Product</label>
                                            <select class="form-control product_select" name="product[]" id="product_<?=$i?>">
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
                                        <div class="col-lg-3 mb-2">
                                          <label>Tier</label>
                                          <select class="form-control tier_select" name="product_tier[]" id="tier_select_<?=$i?>">
                                            <option value="">Select</option>
                                            <option value="1" <?php if($gi->tier_id == 1){ echo 'selected="selected"';} ?>>Tier 1</option>
                                            <option value="2" <?php if($gi->tier_id == 2){ echo 'selected="selected"';} ?>>Tier 2</option>
                                            <option value="3" <?php if($gi->tier_id == 3){ echo 'selected="selected"';} ?>>Tier 3</option>
                                            <option value="4" <?php if($gi->tier_id == 4){ echo 'selected="selected"';} ?>>Tier 4</option>
                                            <option value="5" <?php if($gi->tier_id == 5){ echo 'selected="selected"';} ?>>Tier 5</option>
                                            <option value="6" <?php if($gi->tier_id == 6){ echo 'selected="selected"';} ?>>Tier 6</option>
                                            <option value="7" <?php if($gi->tier_id == 7){ echo 'selected="selected"';} ?>>Tier 7</option>
                                            <option value="8" <?php if($gi->tier_id == 8){ echo 'selected="selected"';} ?>>Tier 8</option>
                                            <option value="9" <?php if($gi->tier_id == 9){ echo 'selected="selected"';} ?>>Tier 9</option>
                                            <option value="10" <?php if($gi->tier_id == 10){ echo 'selected="selected"';} ?>>Tier 10</option>
                                            <option value="11" <?php if($gi->tier_id == 11){ echo 'selected="selected"';} ?>>Tier 11</option>
                                          </select>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <label>Description</label>
                                            <input type="text" name="desc[]" id="desc_<?=$i?>" value="<?=$gi->description?>" placeholder="Description" required="required" class="form-control">
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                            <label>Qty</label>
                                            <input type="text" name="qty[]" id="qty_<?=$i?>" value="<?=$gi->quantity?>" placeholder="0" required="required" onkeyup="checkDec(this);" class="form-control floatNumberField qty">
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                            <label>Rate</label>
                                            <input type="text" name="rate[]" id="rate_<?=$i?>" value="<?=$gi->unit_price?>" placeholder="0" onkeyup="checkDec(this);" class="form-control floatNumberField rate">
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                            <label>Amount</label>
                                            <input type="text" name="amount[]" id="amount_<?=$i?>" value="<?=$gi->total_price?>" tabindex="-1" placeholder="0" class="form-control amount bg-light" readonly="readonly">
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                          <label>Act.</label><br>
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
                               <div class="col-lg-5 mb-2" id="product_div_0">
                                   <label>Product</label>
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
                               <div class="col-lg-3 mb-2">
                                 <label>Tier</label>
                                 <select class="form-control tier_select" name="product_tier[]" id="tier_select_0">
                                   <option value="">Select</option>
                                   <option value="1">Tier 1</option>
                                   <option value="2">Tier 2</option>
                                   <option value="3">Tier 3</option>
                                   <option value="4">Tier 4</option>
                                   <option value="5">Tier 5</option>
                                   <option value="6">Tier 6</option>
                                   <option value="7">Tier 7</option>
                                   <option value="8">Tier 8</option>
                                   <option value="9">Tier 9</option>
                                   <option value="10">Tier 10</option>
                                   <option value="11">Tier 11</option>
                                 </select>
                               </div>
                               <div class="col-lg-4 mb-2">
                                   <label>Description</label>
                                   <input type="text" name="desc[]" id="desc_0" placeholder="Description" required="required" class="form-control">
                               </div>
                               <div class="col-lg-3 mb-2">
                                   <label>Qty</label>
                                   <input type="text" name="qty[]" id="qty_0" placeholder="0" required="required" onkeyup="checkDec(this);" class="form-control floatNumberField qty">
                               </div>
                               <div class="col-lg-3 mb-2">
                                   <label>Rate</label>
                                   <input type="text" name="rate[]" id="rate_0" placeholder="0" onkeyup="checkDec(this);" class="form-control floatNumberField rate">
                               </div>
                               <div class="col-lg-3 mb-2">
                                   <label>Amount</label>
                                   <input type="text" name="amount[]" id="amount_0" placeholder="0" class="form-control amount bg-light" tabindex="-1" readonly="readonly">
                               </div>
                               <div class="col-lg-3 mb-2">
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
                            <div class="col-lg-5 mb-2" id="product_div_0">
                                <label>Product</label>
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
                            <div class="col-lg-3 mb-2">
                              <label>Tier</label>
                              <select class="form-control tier_select" name="product_tier[]" id="tier_select_0">
                                <option value="">Select</option>
                                <option value="1">Tier 1</option>
                                <option value="2">Tier 2</option>
                                <option value="3">Tier 3</option>
                                <option value="4">Tier 4</option>
                                <option value="5">Tier 5</option>
                                <option value="6">Tier 6</option>
                                <option value="7">Tier 7</option>
                                <option value="8">Tier 8</option>
                                <option value="9">Tier 9</option>
                                <option value="10">Tier 10</option>
                                <option value="11">Tier 11</option>
                              </select>
                            </div>
                            <div class="col-lg-4 mb-2">
                                <label>Description</label>
                                <input type="text" name="desc[]" id="desc_0" placeholder="Description" required="required" class="form-control">
                            </div>
                            <div class="col-lg-3 mb-2">
                                <label>Qty</label>
                                <input type="text" name="qty[]" id="qty_0" placeholder="0" required="required" onkeyup="checkDec(this);" class="form-control floatNumberField qty">
                            </div>
                            <div class="col-lg-3 mb-2">
                                <label>Rate</label>
                                <input type="text" name="rate[]" id="rate_0" placeholder="0" onkeyup="checkDec(this);" class="form-control floatNumberField rate">
                            </div>
                            <div class="col-lg-3 mb-2">
                                <label>Amount</label>
                                <input type="text" name="amount[]" id="amount_0" placeholder="0" class="form-control amount bg-light" tabindex="-1" readonly="readonly">
                            </div>
                            <div class="col-lg-3 mb-2">
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
        <div class="card shadow rounded mt-4">
          <div class="card-header pt-3 pb-2 bg-danger-subtle">
              <div class="row">
                  <div class="col-lg-6">
                      <h4>Additional Details</h4>
                  </div>
              </div>
          </div>
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-lg-8">
                        <?= $form->field($model, 'comments')->textarea(['rows' => 6]) ?>
                    </div>
                    <div class="col-lg-4 text-end">
                        <div class="row g-3" style="font-weight:600;">

                            <input type="hidden" id="subtotal" name="TblInvoice[subtotal]" value="<?=$subtotal?>">
                            <input type="hidden" id="hst_amount" name="TblInvoice[hst_amount]" value="<?=$hst_amount?>">
                            <input type="hidden" id="total" name="TblInvoice[total_amount]" value="<?=$total_amount?>">
                            <div class="col-8 mt-3">
                            Subtotal:
                            </div>
                            <div class="col-4 mt-3" id="subtotal_calc">
                            $<?=$subtotal?>
                            </div>
                        </div>
                        <div class="row g-3" style="font-weight:600;">
                            <div class="col-8 mt-3">
                            Taxes:
                            </div>
                            <div class="col-4 mt-3" id="hst_calc">
                            $<?=$hst_amount?>
                            </div>
                        </div>
                        <div class="row g-3" style="font-weight:600;">
                            <div class="col-8 mt-3">
                            Total:
                            </div>
                            <div class="col-4 mt-3" id="total_calc">
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
                <input type="submit" name="new_new" value="Create & Preview" class="btn btn-warning btn-sm"/>
              </div>
              <div class="d-grid gap-2 col-4 mx-auto ps-1">
                <input type="submit" name="new_exit" value="Create & Exit" class="btn btn-secondary btn-sm"/>
              </div>
              <?php
            }else{
                if(Yii::$app->controller->action->id == "copy"){
                    ?>
                    <div class="d-grid gap-2 col-4 mx-auto pe-1">
                      <input type="submit" name="new_update" value="Create & Edit" class="btn btn-primary btn-sm"/>
                    </div>
                    <div class="d-grid gap-2 col-4 mx-auto pe-1 ps-1">
                      <input type="submit" name="new_new" value="Create & Preview" class="btn btn-warning btn-sm"/>
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
                      <input type="submit" name="new" value="Update & Preview" class="btn btn-warning btn-sm"/>
                    </div>
                    <div class="d-grid gap-2 col-4 mx-auto ps-1">
                      <input type="submit" name="exit" value="Update & Exit" class="btn btn-secondary btn-sm"/>
                    </div>
                    <?php
                }
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
    // $product_str .= "<option value='".$gp->id."'>".$gp->name." [".$gp->description."]</option>";
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
            let str = "<div class=\'row row_extra mt-3\'><hr class=\"text-primary\" /><div class=\'col-lg-5 mb-2\' id=\'product_div_"+cc+"\'><label>Product</label><select class=\'form-control product_select\' name=\'product[]\' id=\'product_"+cc+"\'><option value=\'\'>Select</option>'.$product_str.'</select></div><div class=\"col-lg-3 mb-2\"><label>Tier</label><select class=\"form-control tier_select\" name=\"product_tier[]\" id=\"tier_select_"+cc+"\"><option value=\"\">Select</option><option value=\"1\">Tier 1</option><option value=\"2\">Tier 2</option><option value=\"3\">Tier 3</option><option value=\"4\">Tier 4</option><option value=\"5\">Tier 5</option><option value=\"6\">Tier 6</option><option value=\"7\">Tier 7</option><option value=\"8\">Tier 8</option><option value=\"9\">Tier 9</option><option value=\"10\">Tier 10</option><option value=\"11\">Tier 11</option></select></div><div class=\'col-lg-4 mb-2\'><label>Description</label><input type=\'text\' name=\'desc[]\' id=\'desc_"+cc+"\' placeholder=\'Description\' class=\'form-control\' required=\'required\'></div><div class=\'col-lg-3 mb-2\'><label>Qty</label><input type=\'text\' name=\'qty[]\' id=\'qty_"+cc+"\' placeholder=\'0\' class=\'form-control qty floatNumberField\' required=\'required\' onkeyup=\'checkDec(this);\'></div><div class=\'col-lg-3 mb-2\'><label>Rate</label><input type=\'text\' name=\'rate[]\' id=\'rate_"+cc+"\' placeholder=\'0\' class=\'form-control floatNumberField rate\' required=\'required\' onkeyup=\'checkDec(this);\'></div><div class=\'col-lg-3 mb-2\'><label>Amount</label><input type=\'text\' name=\'amount[]\' id=\'amount_"+cc+"\' placeholder=\'0\' class=\'form-control amount bg-light\' tabindex=\'-1\' readonly=\'readonly\'></div><div class=\'col-lg-3 mb-2\'><label>Act.</label><br><button class=\'btn btn-primary btn_add_project btn-sm\'><i class=\'fa fa-plus\'></i></button>&nbsp;&nbsp;<button class=\'btn btn-primary btn_remove_project btn-sm\'><i class=\'fa fa-minus\'></i></button></div></div>";
            $("#add_projects").append(str);
            var c= parseInt(cc)+1;
            $("#project_count").val(c);

            // Initialize Choices.js on the newly added select element, if not already initialized
             let newSelect = $("#add_projects").find(".product_select").last();

             // Check if the select element is already initialized with Choices.js
             if (!newSelect[0].hasAttribute(\'data-choice\')) {
                 new Choices(newSelect[0], {
                  allowHTML: true
              });  // Initialize Choices on the last appended select if not already initialized
             }

        });
        $(document).on("click",".btn_remove_project",function(e){
            e.preventDefault();
            $(this).parent().parent(".row_extra").remove();
            calculatetotalwithtaxes();
        });
        //--------------terms logic as per customer selection starts----------------
        var due_date_picker = $("#tblinvoice-due_date").flatpickr({
            disableMobile: "true",
            dateFormat: "Y-m-d"
          });
          var invoice_date_picker = $("#tblinvoice-invoice_date").flatpickr({
              disableMobile: "true",
              dateFormat: "Y-m-d"
            });
        $("#tblinvoice-fk_client_id").change(function(){
            var fk_client_id = $("#tblinvoice-fk_client_id").val();
            console.log("fk_client_id id::::"+fk_client_id);
            $.post("index.php?r=client/getclient",{fk_client_id:fk_client_id},function(r){
              console.log(r);
              var terms = r;
              if(terms !== 0){
                $("#tblinvoice-fk_terms_id").val(terms).change();
                setDueDateTerms();
              }//---------client has terms selected-------

            });
          });
          function setDueDateTerms(){
            var term_val = $("#tblinvoice-fk_terms_id").val();
            var invoice_date = $("#tblinvoice-invoice_date").val();
            //send post request to get no of days
            console.log("term_val:::"+term_val+":::invoice_date::"+invoice_date);
            $.post("index.php?r=vendor-invoice/gettermdays",{term_val:term_val,invoice_date:invoice_date},function(r){
              console.log(r);
              due_date_picker.setDate(r);
            });
          }
          $("#tblinvoice-fk_terms_id").change(function(){
            setDueDateTerms();
          });
          $("#tblinvoice-invoice_date").change(function(){
            setDueDateTerms();
          });
        //--------------terms logic as per customer selection ends------------------
        function calculate(idval){
            let qty = $("#qty_"+idval).val();
            let rate = $("#rate_"+idval).val();
            if(qty != "" && rate != ""){
                let amount = qty*rate;
              $("#amount_"+idval).val(amount.toFixed(2));
            }else{
              $("#amount_"+idval).val(0);
            }
        }
        $(document).on("blur",".qty",function(){
            let id = $(this).attr("id");
            let index = id.indexOf("_");
            let idval = id.substring(parseInt(index)+parseInt(1));
            calculate(idval);
            calculatetotalwithtaxes();
          });
          $(document).on("blur",".rate",function(){
            let id = $(this).attr("id");
            let index = id.indexOf("_");
            let idval = id.substring(parseInt(index)+parseInt(1));
            calculate(idval);
            calculatetotalwithtaxes();
          });
          $(document).on("keyup",".qty",function(){
              let id = $(this).attr("id");
              let index = id.indexOf("_");
              let idval = id.substring(parseInt(index)+parseInt(1));
              calculate(idval);
              calculatetotalwithtaxes();
            });
            $(document).on("keyup",".rate",function(){
              let id = $(this).attr("id");
              let index = id.indexOf("_");
              let idval = id.substring(parseInt(index)+parseInt(1));
              calculate(idval);
              calculatetotalwithtaxes();
            });
          $("#tblvendorinvoice-hst").blur(function(){
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
            var selectedOption = $("#tblinvoice-hst").find("option:selected");
            var selectedOptionId = $("#tblinvoice-hst").val();
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
          $(document).on("change",".type_select",function(e){
            e.preventDefault();
            let value = $(this).val();
            let id = $(this).attr("id");
            let index = id.indexOf("_");
            let idval = id.substring(parseInt(index)+parseInt(1));
            // console.log(idval);
            $("#desc_"+idval).val("");
            if(value == 1){
              if($("#title_div_"+idval).hasClass("hidden")){
                $("#title_div_"+idval).removeClass("hidden");
              }
              if(!$("#product_div_"+idval).hasClass("hidden")){
                $("#product_div_"+idval).addClass("hidden");
                $("#product_"+idval).val("");
              }
            }else if(value == 2){
              if($("#product_div_"+idval).hasClass("hidden")){
                $("#product_div_"+idval).removeClass("hidden");
              }
              if(!$("#title_div_"+idval).hasClass("hidden")){
                $("#title_div_"+idval).addClass("hidden");
                $("#title_"+idval).val("");
              }
            }else{
              if($("#title_div_"+idval).hasClass("hidden")){
                $("#title_div_"+idval).removeClass("hidden");
              }
              if(!$("#product_div_"+idval).hasClass("hidden")){
                $("#product_div_"+idval).addClass("hidden");
                $("#product_"+idval).val("");
              }
            }
          });
          $(document).on("change",".product_select",function(e){
            e.preventDefault();
            let value = $(this).val();
            let id = $(this).attr("id");
            let index = id.indexOf("_");
            let idval = id.substring(parseInt(index)+parseInt(1));
            let client_id = $("#tblinvoice-fk_client_id").val();
            $.post("index.php?r=invoice/getproductdesc",{product_id:value,client_id:client_id},function(r){
              // console.log(r);
              var obj = JSON.parse(r);
              var description = obj.description;
              var price = obj.price;
              var tier = obj.tier;
              $("#desc_"+idval).val(description);
              $("#rate_"+idval).val(price);
              $("#tier_select_"+idval).val(tier);
            });
          });
          $(document).on("change",".tier_select",function(e){
            e.preventDefault();
            let tier_id = $(this).val();
            let id = $(this).attr("id");
            let idarr = id.split("_");
            let idval = idarr[2];
            console.log("idval::::::"+idval);
            let product_id = $("#product_"+idval).val();
            console.log("product_id:::::"+product_id);
            $.post("index.php?r=invoice/getpdouctpricetier",{product_id:product_id,tier_id:tier_id},function(r){
              // console.log(r);
              var obj = JSON.parse(r);
              var price = obj.price;
              $("#rate_"+idval).val(price);
              calculate(idval);
              calculatetotalwithtaxes();

            });
          });

          $("#tblinvoice-fk_bill_from_id").change(function(){
            let bill_from = $("#tblinvoice-fk_bill_from_id").val();
            $.post("index.php?r=invoice/get-tax-ownership",{bill_from:bill_from},function(r){
              $("#tblinvoice-hst").val(r);
            });
          });
    ');
?>
