<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\TblPreferredPaymentMethod;
use app\models\TblTerms;
use app\models\TblCreditLimit;
use app\models\TblInvoice;
use app\models\TblAccountReceivable;
use app\models\TblClientLogin;
use app\models\TblOwnershipCompany;

/** @var yii\web\View $this */
/** @var app\models\TblClient $model */

$this->title = $model->company_name;
$this->params['breadcrumbs'][] = ['label' => 'Customers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>13,'status'=>1])->one();
$menuaccess_invoice = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>22,'status'=>1])->one();
$menuaccess_ar = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>64,'status'=>1])->one();
?>
<div class="tbl-client-view">
  <div class="card shadow rounded mt-2">
    <div class="card-body p-3">
      <div class="row">
        <div class="col-lg-4">
          <h3><?= Html::encode($this->title) ?></h3>
        </div>
        <div class="col-lg-8 text-end">

          <?php
          if(isset($menuaccess) && $menuaccess->edit_crud == 1){
            ?>
            <?= Html::a('<i class="fas fa-pen"></i> Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-info btn-sm']) ?>
            <?php
          }
          ?>

          <?php
          if(isset($menuaccess) && $menuaccess->delete_crud == 1){
            ?>
            <?= Html::a('<i class="fas fa-trash"></i> Delete', ['delete', 'id' => $model->id], [
              'class' => 'btn btn-danger btn-sm',
              'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
              ],
              ]) ?>
              <?php
            }
            ?>

            <?= Html::a('<i class="fas fa-chevron-left"></i> Back to List', ['index'], ['class' => 'btn btn-secondary btn-sm']) ?>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow rounded mt-2">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-lg-12 text-center">
            <?php
            if(isset($menuaccess_ar) && $menuaccess_ar->create_crud == 1){
              if($model->status == 1){
                ?>
                <?= Html::a('<i class="fas fa-dollar-sign"></i> Receive Payment', ['client-receipt/create', 'client_id' => $model->id], ['class' => 'btn btn-success btn-sm','target'=>"_blank"]) ?>
                <?php
              }else{
                ?>
                <button type="button" class="btn btn-success btn-sm" disabled>
                  <i class="fas fa-dollar-sign"></i> Receive Payment
                </button>
                <?php
              }


            }
            ?>
            <?php
            if(isset($menuaccess_invoice) && $menuaccess_invoice->create_crud == 1){
              if($model->status == 1){
                ?>
                <?= Html::a('<i class="fas fa-file-invoice"></i> Create Invoice', ['invoice/create', 'client_id' => $model->id], ['class' => 'btn btn-primary btn-sm','target'=>"_blank"]) ?>
                <?php
              }else{
                ?>
                <button type="button" class="btn btn-primary btn-sm" disabled>
                  <i class="fas fa-file-invoice"></i> Create Invoice
                </button>
                <?php
              }

            }
            ?>

            <!-- <?= Html::a('<i class="fas fa-file-invoice-dollar"></i> Send Transaction Summary', ['client/send-statement-view', 'id' => $model->id], ['class' => 'btn btn-danger btn-sm']) ?> -->
            <?php
                if($model->status == 1){
                  ?>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#transactionModal">
                      <i class="fas fa-file-invoice-dollar"></i> Send Transaction Summary
                    </button>
                  <?php
                }else{
                  ?>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#transactionModal" disabled>
                      <i class="fas fa-file-invoice-dollar"></i> Send Transaction Summary
                    </button>
                  <?php
                }
             ?>
             <button class="btn btn-info btn-sm"
                     id="btn-generate-client-statement"
                     data-url="<?= Yii::$app->urlManager->createUrl([
                         'client/statement-form',
                         'client_id' => $model->id
                     ]) ?>">
                 <i class="fas fa-file-invoice-dollar"></i> Generate Statement
             </button>



            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#summaryModal">
              <i class="fas fa-user-lock"></i> Send Credentials for Website
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="customerStatementModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="customerStatementModalLabel">Customer Statement</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div id="customerStatementModalContent"></div>
          </div>
        </div>
      </div>
    </div>
    <?php
    $this->registerJs(<<<JS
      $('#btn-generate-client-statement').on('click', function () {
        let url = $(this).data('url');

        $('#customerStatementModal').modal('show')
        .find('#customerStatementModalContent')
        .html('<div class="text-center p-4">Loading...</div>')
        .load(url);
      });
      JS);
      ?>


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
    <div class="card shadow rounded mt-2">
      <div class="card-header p-3 border-bottom border-300 bg-info-subtle">
        <div class="row g-3 justify-content-between align-items-center">
          <div class="col-12 col-md">
            <h4 class="text-900 mb-0" data-anchor="data-anchor" id="basic-details">Basic Details</h4>
          </div>
        </div>
      </div>
      <div class="card-body p-3">
        <div class="row">
          <div class="col-lg-3 mb-2">
            <label class="fw-bold">Company Name</label><br />
            <?=$model->company_name?>
          </div>
          <div class="col-lg-3 mb-2">
            <label class="fw-bold">Contact Name</label><br />
            <?=$model->contact_name ? $model->contact_name : '(not set)'?>
          </div>
          <div class="col-lg-3 mb-2">
            <label class="fw-bold">Contact Title</label><br />
            <?=$model->contact_title ? $model->contact_title : '(not set)'?>
          </div>
          <div class="col-lg-3 mb-2">
            <label class="fw-bold">Status</label><br />
            <?php
              if($model->status == 1){
                echo '<span class="badge bg-success">Active</span>';
              }else if($model->status == 2){
                echo '<span class="badge bg-danger">Inactive</span>';
              }else if($model->status == 3){
                echo '<span class="badge bg-warning">In Review</span>';
              }else{
                echo '<span class="badge bg-secondary">(not set)</span>';
              }
             ?>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-3 mb-2">
            <label class="fw-bold">Email</label><br />
            <?=$model->email ? $model->email : '(not set)'?>
          </div>
          <div class="col-lg-3 mb-2">
            <label class="fw-bold">Phone</label><br />
            <?=$model->phone ? $model->phone : '(not set)'?>
          </div>
          <div class="col-lg-3 mb-2">
            <label class="fw-bold">Address</label><br />
            <?=$model->address ? $model->address : '(not set)'?>
          </div>

          <div class="col-lg-3 mb-2">
            <label class="fw-bold">City</label><br />
            <?=$model->city ? $model->city : '(not set)'?>
          </div>
          <div class="col-lg-3 mb-2">
            <label class="fw-bold">State</label><br />
            <?=$model->state ? $model->state : '(not set)'?>
          </div>
          <div class="col-lg-3 mb-2">
            <label class="fw-bold">Postal Code</label><br />
            <?=$model->postal_code ? $model->postal_code : '(not set)'?>
          </div>
        </div>


      </div>
    </div>

    <div class="card shadow rounded mt-2">
      <div class="card-header p-3 border-bottom border-300 bg-primary-subtle">
        <div class="row g-3 justify-content-between align-items-center">
          <div class="col-12 col-md">
            <h4 class="text-900 mb-0" data-anchor="data-anchor" id="basic-details">Payment Details</h4>
          </div>
        </div>
      </div>
      <div class="card-body p-3">

        <div class="row">
          <div class="col-lg-3 mb-2">
            <label class="fw-bold">Terms</label><br />
            <?php
            $getterms = TblTerms::find()->where(['id'=>$model->fk_terms_id,'status'=>1])->one();
            if(isset($getterms) && $getterms->title != ""){
              echo $getterms->title;
            }else{
              echo "(not set)";
            }
            ?>
          </div>
          <div class="col-lg-3 mb-2">
            <label class="fw-bold">Preferred Payment Method</label><br />
            <?php
            $getmethodpay = TblPreferredPaymentMethod::find()->where(['id'=>$model->fk_preferred_payment_method_id,'status'=>1])->one();
            if(isset($getmethodpay) && $getmethodpay->title != ""){
              echo $getmethodpay->title;
            }else{
              echo "(not set)";
            }
            ?>
          </div>
          <div class="col-lg-3 mb-2">
            <label class="fw-bold">Credit Limit</label><br />
            <?php
            $getcredit = TblCreditLimit::find()->where(['id'=>$model->credit_limit,'status'=>1])->one();
            if(isset($getcredit) && $getcredit->title != ""){
              echo $getcredit->title;
            }else{
              echo "(not set)";
            }
            ?>
          </div>
          <div class="col-lg-3 mb-2">
            <label class="fw-bold">Pricing Tier</label><br />
            <?php
              $tierarr = [1=>"Tier 1",2=>"Tier 2",3=>"Tier 3",4=>"Tier 4",5=>"Tier 5",6=>"Tier 6",7=>"Tier 7",8=>"Tier 8",9=>"Tier 9",10=>"Tier 10",11=>"Tier 11"];
              echo $tierarr[$model->tier_id];
             ?>
          </div>
        </div>

      </div>
    </div>

    <div class="card shadow rounded mt-2">
      <div class="card-header p-3 border-bottom border-300 bg-danger-subtle">
        <div class="row g-3 justify-content-between align-items-center">
          <div class="col-12 col-md">
            <h4 class="text-900 mb-0" data-anchor="data-anchor" id="basic-details">Notes</h4>
          </div>
        </div>
      </div>
      <div class="card-body p-3">
        <?=$model->notes?>
      </div>
    </div>
    <?php
      $ownership_companies = TblOwnershipCompany::find()->where(['status'=>1])->orderBy(['company_name'=>SORT_ASC])->all();
      if(isset($ownership_companies) && count($ownership_companies) > 0){
        foreach($ownership_companies as $oc){
          ?>
          <div class="card shadow rounded mt-2 mb-4">
            <div class="card-header p-3 border-bottom border-300 bg-success-subtle">
              <div class="row g-3 justify-content-between align-items-center">
                <div class="col-12 col-md">
                  <h4 class="text-900 mb-0" data-anchor="data-anchor">Summary of Transactions - <?=$oc->location_name?></h4>
                </div>
              </div>
            </div>
            <div class="card-body p-3">
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
                    ->andWhere(['fk_bill_from_id'=>$oc->id])
                    ->orderBy(['crt_time' => SORT_ASC])
                    ->all();

                    $invoice_arr = [];
                    $ar_arr = [];

                    if (isset($getinvoices) && count($getinvoices) > 0) {
                      foreach ($getinvoices as $gi) {
                        // Calculate total payments received for this invoice
                        $totalPayments = TblAccountReceivable::find()
                        ->where(['fk_invoice_id' => $gi->id])
                        ->andWhere('status = 1')
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
                    ->andWhere('fk_invoice_id in (select id from tbl_invoice where fk_client_id = ' . $model->id . ' and status != 0 and fk_bill_from_id = '.$oc->id.')')
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
                          // if ($v['type'] == "Invoice") {
                          //   $starting_balance += $v['amount'];
                          // } else {
                          //   $starting_balance -= $v['amount'];
                          // }
                          if ($v['type'] == "Invoice") {
                            $starting_balance = bcadd($starting_balance, $v['amount'], 2);
                          } else {
                            $starting_balance = bcsub($starting_balance, $v['amount'],2);
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
                    }else{
                      echo '<tr>
                      <td colspan="7">
                      No transactions yet.
                      </td>
                      </tr>';
                    }

                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <?php
        }//---for loop ended -----
      }//---if isset ended -------
     ?>


  </div>

<?php
$checkLogin = TblClientLogin::find()->where(['fk_client_id'=>$model->id,'status'=>1])->one();
if(isset($checkLogin) && $checkLogin->id != ""){
  $username_email = $checkLogin->username;
}else{
  $username_email = strtolower($model->email);
}
 ?>

  <!-- Modal -->
  <div class="modal fade" id="summaryModal" tabindex="-1" aria-labelledby="summaryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title8" id="summaryModalLabel">Send Credentials</h3>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form name="frm_reservation" id="" action="<?= Yii::$app->urlManager->createUrl(['client/send-credentials']) ?>" method="post" autocomplete="off">
          <input type="hidden" name="_csrf-backend" value="<?=Yii::$app->request->getCsrfToken()?>" />
          <div class="modal-body">
            <div class="row">
              <div class="col-lg-12 mb-2">
                <label for="">Email for Username</label>
                <input type="hidden" name="client_id" value="<?=$model->id?>">
                <input type="email" name="user_email" class="form-control" value="<?=$username_email?>">

                <p>
                  A valid email-id is required.
                </p>
              </div>
              <div class="col-lg-12">
                <p><i>The Customer's status will be changed to Active (if not active), Are you sure you want to send the credentials?</i></p>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary btn-sm">Send</button>
          </div>
        </form>
      </div>
    </div>
  </div>


  <!-- Modal -->
  <div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title8" id="transactionModalLabel">Send Transaction Summary</h3>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form name="frm_reservation" id="" action="<?= Yii::$app->urlManager->createUrl(['client/send-statement-company-specific','id'=>$model->id]) ?>" method="post" autocomplete="off">
          <input type="hidden" name="_csrf-backend" value="<?=Yii::$app->request->getCsrfToken()?>" />
          <div class="modal-body">
            <div class="row">
              <div class="col-lg-12 mb-2">
                <label for="">Select Company</label>
                <select class="form-select" name="ownership_company" required>
                  <option value="">Select</option>
                  <?php
                    if(isset($ownership_companies) && count($ownership_companies) > 0){
                      foreach($ownership_companies as $oc){
                        echo '<option value="'.$oc->id.'">'.$oc->location_name.'</option>';
                      }
                    }
                   ?>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary btn-sm">Send</button>
          </div>
        </form>
      </div>
    </div>
  </div>
