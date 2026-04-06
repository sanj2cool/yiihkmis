<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\TblPreferredPaymentMethod;
use app\models\TblVendorInvoice;
use app\models\TblVendor;
use yii\helpers\ArrayHelper;
/** @var yii\web\View $this */
/** @var app\models\TblVendorAccountPayable $model */
/** @var yii\widgets\ActiveForm $form */
if($model->isNewRecord){
  $model->ar_date = date('Y-m-d');
}
$allpaymethods = TblPreferredPaymentMethod::find()->where(['status'=>1])->orderBy(['title'=>SORT_ASC])->all();
$paymethodarr = ArrayHelper::map($allpaymethods,'id','title');

$session = Yii::$app -> session;
$fk_location_id = $session['userCompany'];
?>

<div class="tbl-vendor-account-payable-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="card shadow rounded mt-4">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-lg-3">
            <label for="">Invoice</label>
            <select class="form-control" name="TblVendorAccountPayable[fk_vendor_invoice_id]" id="tblvendoraccountpayable-fk_vendor_invoice_id" required="required" data-choices="data-choices" data-options='{"removeItemButton":true,"placeholder":true}'>
              <option value="">Select</option>
              <?php
              use yii\db\Query;
              // Step 1: Fetch all invoices with related data in a single query
              $invoicesQuery = TblVendorInvoice::find()
              ->alias('i')
              ->select(['i.id', 'i.vendor_invoice_number', 'i.total_amount', 'i.fk_vendor_id'])
              ->leftJoin(['ir' => 'tbl_vendor_account_payable'], 'ir.fk_vendor_invoice_id = i.id AND ir.status != 0')
              ->leftJoin(['c' => 'tbl_vendor'], 'c.id = i.fk_vendor_id')
              ->addSelect(['SUM(ir.amount_received) AS total_amount_received', 'c.company_name AS client_name'])
              ->where('i.status != 0')
              ->andWhere(['i.fk_location_id'=>$fk_location_id])
              ->groupBy(['i.id'])
              ->asArray()
              ->all();

              foreach($invoicesQuery as $invoice) {
                $totalAmountReceived = $invoice['total_amount_received'] ?? 0.00;
                $client_info = $invoice['client_name'] ?? "";
                $selected = !$model->isNewRecord && $model->fk_vendor_invoice_id == $invoice['id'] ? 'selected="selected"' : '';
                echo '<option value="'.$invoice['id'].'" '.$selected.'>#'.$invoice['vendor_invoice_number'].' - '.$client_info.' - T $'.$invoice['total_amount'].' - R $'.$totalAmountReceived.'</option>';
              }
              ?>
            </select>
          </div>
          <div class="col-lg-3">
            <?= $form->field($model, 'amount_received')->textInput(['maxlength' => true,'class'=>'form-control floatNumberField','onkeyup'=>"checkDec(this);"]) ?>
          </div>
          <div class="col-lg-3">
            <?= $form->field($model, 'ar_date')->textInput(['maxlength' => true,'class'=>'form-control datetimepicker','data-options'=>'{"disableMobile":true,"dateFormat":"Y-m-d",altInput: true,altFormat: "F j, Y",}']) ?>
          </div>
          <div class="col-lg-3">
            <?= $form->field($model, 'fk_payment_method_id')->dropDownList($paymethodarr,['prompt'=>'Select','class'=>'form-select']) ?>
          </div>
        </div>


      </div>

    </div>



    <div class="card shadow rounded mt-4">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-lg-12">
            <?= $form->field($model, 'notes')->textArea(['maxlength' => true,'rows'=>'3']) ?>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow rounded p-3 mt-4">
      <div class="row text-center">
        <?php
        if($model->isNewRecord){
          ?>
          <div class="d-grid gap-2 col-4 mx-auto pe-1">
            <input type="submit" name="new_update" value="Create & Edit" class="btn btn-info btn-sm"/>
          </div>
          <div class="d-grid gap-2 col-4 mx-auto pe-1 ps-1">
            <input type="submit" name="new_new" value="Create & New" class="btn btn-primary btn-sm"/>
          </div>
          <div class="d-grid gap-2 col-4 mx-auto ps-1">
            <input type="submit" name="new_exit" value="Create & Exit" class="btn btn-secondary btn-sm"/>
          </div>
          <?php
        }else{
          ?>
          <div class="d-grid gap-2 col-4 mx-auto pe-1">
            <input type="submit" name="update" value="Update & Edit" class="btn btn-info btn-sm"/>
          </div>
          <div class="d-grid gap-2 col-4 mx-auto pe-1 ps-1">
            <input type="submit" name="new" value="Update & New" class="btn btn-primary btn-sm"/>
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
