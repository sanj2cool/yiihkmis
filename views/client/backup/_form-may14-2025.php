<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\TblPreferredPaymentMethod;
use app\models\TblTerms;
use app\models\TblCreditLimit;
use app\models\TblInvoice;
use app\models\TblAccountReceivable;
/** @var yii\web\View $this */
/** @var app\models\TblClient $model */
/** @var yii\widgets\ActiveForm $form */

$prefpayarr = TblPreferredPaymentMethod::find()->select(['title', 'id'])->where('status != 0')->indexBy('id')->column();
$termsarr = TblTerms::find()->select(['title', 'id'])->where('status != 0')->indexBy('id')->column();
$creditarr = TblCreditLimit::find()->select(['title', 'id'])->where('status != 0')->indexBy('id')->column();
$tierarr = [1=>"Tier 1",2=>"Tier 2",3=>"Tier 3",4=>"Tier 4",5=>"Tier 5"];
?>

<div class="tbl-client-form">
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
      <ul class="nav nav-underline fs-9" role="tablist">
        <li class="nav-item"> <a class="nav-link active" data-bs-toggle="tab" href="#home" role="tab"><span class="hidden-sm-up"><i class="fas fa-list"></i></span> <span class="hidden-xs-down">Client Details</span></a> </li>
        <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#tabsummary" role="tab"><span class="hidden-sm-up"><i class="fas fa-file-invoice-dollar"></i></span> <span class="hidden-xs-down">Summary of Transactions</span></a> </li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane fade show active" id="home" role="tabpanel">
          <div class="card shadow rounded mt-2">
            <div class="card-header p-4 border-bottom border-300 bg-soft">
              <div class="row g-3 justify-content-between align-items-center">
                <div class="col-12 col-md">
                  <h4 class="text-900 mb-0" data-anchor="data-anchor" id="basic-details">Basic Details</h4>
                </div>
              </div>
            </div>
            <div class="card-body p-3">
              <div class="row">
                <div class="col-lg-4">
                  <?= $form->field($model, 'company_name')->textInput(['maxlength' => true])->label('Company Name<span class="text-danger">*</span>') ?>
                </div>
                <div class="col-lg-4">
                  <?= $form->field($model, 'contact_name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-lg-4">
                  <?= $form->field($model, 'contact_title')->textInput(['maxlength' => true]) ?>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-4">
                  <?= $form->field($model, 'email')->textInput(['maxlength' => true])->label('Email<span class="text-danger">*</span>') ?>
                </div>
                <div class="col-lg-4">
                  <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-lg-4">
                  <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-4">
                  <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-lg-4">
                  <?= $form->field($model, 'state')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-lg-4">
                  <?= $form->field($model, 'postal_code')->textInput(['maxlength' => true]) ?>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow rounded mt-2">
            <div class="card-header p-4 border-bottom border-300 bg-soft">
              <div class="row g-3 justify-content-between align-items-center">
                <div class="col-12 col-md">
                  <h4 class="text-900 mb-0" data-anchor="data-anchor" id="basic-details">Payment & Pricing Details</h4>
                </div>
              </div>
            </div>
            <div class="card-body p-3">

              <div class="row">
                <div class="col-lg-3">
                  <?= $form->field($model, 'fk_terms_id')->dropDownList($termsarr,['prompt'=>'Select']) ?>
                </div>
                <div class="col-lg-3">
                  <?= $form->field($model, 'fk_preferred_payment_method_id')->dropDownList($prefpayarr,['prompt'=>'Select']) ?>
                </div>
                <div class="col-lg-3">
                  <?= $form->field($model, 'credit_limit')->dropDownList($creditarr,['prompt'=>'Select']) ?>
                </div>
                <div class="col-lg-3">
                  <?= $form->field($model, 'tier_id')->dropDownList($tierarr,['prompt'=>'Select'])->label('Pricing Tier <sup><badge class="badge bg-danger">New</badge></sup>') ?>
                </div>
              </div>

            </div>
          </div>

          <div class="card shadow rounded mt-2">
            <div class="card-header p-4 border-bottom border-300 bg-soft">
              <div class="row g-3 justify-content-between align-items-center">
                <div class="col-12 col-md">
                  <h4 class="text-900 mb-0" data-anchor="data-anchor" id="basic-details">Notes</h4>
                </div>
              </div>
            </div>
            <div class="card-body p-3">
              <?= $form->field($model, 'notes')->textarea(['rows' => 6])->label(false) ?>
            </div>
          </div>
        </div><!-- home tab ended -->
        <div class="tab-pane fade" id="tabsummary" role="tabpanel">

          <?php
          if(!$model->isNewRecord){
            ?>
            <div class="row mt-2">
              <div class="col-12 text-end">
                <a href="<?=Url::to(['client/send-statement','id'=>$model->id])?>"><button type="button" class="btn btn-primary btn-sm">Send Statement</button></a>
              </div>
            </div>
            <div class="table-responsive mt-3">
              <table class="table table-bordered table-sm">
                <thead class="table-dark">
                  <tr>
                    <th>Transaction Date</th>
                    <th>Transaction Type</th>
                    <th>Transaction Number</th>
                    <th>Total</th>
                    <th>Paid Status</th>
                    <th>Balance</th>

                  </tr>
                </thead>
                <tbody>
                  <?php
                  $getinvoices = TblInvoice::find()
                  ->where('status != 0')
                  ->andWhere(['fk_client_id' => $model->id])
                  ->orderBy(['crt_time' => SORT_ASC])
                  ->all();

                  $invoice_arr = [];
                  $ar_arr = [];

                  if (isset($getinvoices) && count($getinvoices) > 0) {
                    foreach ($getinvoices as $gi) {
                      // Calculate total payments received for this invoice
                      $totalPayments = TblAccountReceivable::find()
                      ->where(['fk_invoice_id' => $gi->id])
                      ->sum('amount_received');

                      // Determine the paid status based on total amount and payments
                      if ($totalPayments >= $gi->total_amount) {
                        $paid_status = "Paid";
                      } elseif ($totalPayments > 0) {
                        $paid_status = "Partially Paid";
                      } else {
                        $paid_status = "Unpaid";
                      }

                      $invoice_arr[] = [
                        'date' => date('Y-m-d', strtotime($gi->crt_time)),
                        'type' => "Invoice",
                        'number' => $gi->invoice_number,
                        'amount' => $gi->total_amount,
                        'paid_status' => $paid_status
                      ];
                    }
                  }

                  $getar = TblAccountReceivable::find()
                  ->where('status != 0')
                  ->andWhere('fk_invoice_id in (select id from tbl_invoice where fk_client_id = ' . $model->id . ' and status != 0)')
                  ->orderBy(['crt_time' => SORT_ASC])
                  ->all();

                  if (isset($getar) && count($getar) > 0) {
                    foreach ($getar as $gi) {
                      $getinvoice = TblInvoice::find()->where(['id' => $gi->fk_invoice_id])->one();
                      $invoice_no = isset($getinvoice) && $getinvoice->id != "" ? " (Invoice #" . $getinvoice->invoice_number . ")" : " (Invoice #.)";

                      $ar_arr[] = [
                        'date' => date('Y-m-d', strtotime($gi->crt_time)),
                        'type' => "Amount Received",
                        'number' => $gi->id . $invoice_no,
                        'amount' => $gi->amount_received
                      ];
                    }
                  }

                  // Merge and sort arrays
                  $mergedArray = array_merge($invoice_arr, $ar_arr);
                  usort($mergedArray, function ($a, $b) {
                    return strtotime($a['date']) - strtotime($b['date']);
                  });

                  // Output
                  if (count($mergedArray) > 0) {
                    if ($mergedArray[0]['type'] == "Invoice") {
                      $starting_balance = $mergedArray[0]['amount'];
                    } elseif ($mergedArray[0]['type'] == "Amount Received" || $mergedArray[0]['type'] == "Bad Debt.") {
                      $starting_balance = -$mergedArray[0]['amount'];
                    }
                    $i = 0;
                    foreach ($mergedArray as $v) {
                      if ($i > 0) {
                        if ($v['type'] == "Invoice") {
                          $starting_balance += $v['amount'];
                        } else {
                          $starting_balance -= $v['amount'];
                        }
                      }
                      echo '<tr>
                      <td>' . $v['date'] . '</td>
                      <td>' . $v['type'] . '</td>
                      <td>' . $v['number'] . '</td>
                      <td>' . $v['amount'] . '</td>';
                      if ($v['type'] == "Invoice") {
                        echo '<td>' . $v['paid_status'] . '</td>';
                      } else {
                        echo '<td>-</td>';
                      }
                      echo '<td>' . $starting_balance . '</td>
                      </tr>';
                      $i++;
                    }
                  }

                  ?>
                </tbody>
              </table>
            </div>
            <?php
          }//-======if for checking update ended======
          ?>
        </div><!-- summary of transactions ended -->
      </div><!-- tab content ended -->
    </div><!-- card body ended -->
  </div><!-- card ended  -->









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
