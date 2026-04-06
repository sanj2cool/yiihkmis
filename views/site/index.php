<?php

/** @var yii\web\View $this */
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\db\Query;
use app\models\TblAttendance;
use yii\helpers\Url;
use app\models\TblUser;
use app\models\TblEmployee;
use yii\web\session;
$session = Yii::$app->session;
$user_company = Yii::$app->session->get('userCompany');

$this->title = 'HK Trailer Parts';
//-----get total no of stocks --------
// Subquery for current_qty calculation
$subquery = (new Query())
    ->select([
        'p.id',
        new \yii\db\Expression(
            'IFNULL((SELECT SUM(pr.no_of_items)
                     FROM tbl_product_receiving pr
                     WHERE pr.fk_product_id = p.id AND pr.status = 1 and pr.fk_location_id = '.$user_company.'), 0)
             - IFNULL((SELECT SUM(it.quantity)
                       FROM tbl_invoice_item it
                       WHERE it.fk_product_id = p.id AND it.status = 1 AND it.fk_invoice_id in (select id from tbl_invoice where fk_bill_from_id = '.$user_company.')), 0)
             AS current_qty'
        )
    ])
    ->from('tbl_product p')
    ->where(['p.status' => 1])
    ->groupBy('p.id');
    // Main query to sum the current_qty
    $query = (new Query())
        ->select(['SUM(subquery.current_qty) AS total_qty'])
        ->from(['subquery' => $subquery]);

    // Execute the query
    $totalQty = $query->scalar();
    //------------need to check the if employee/user is a mechanic ----------
    $userID = Yii::$app->session->get('userId');
    $getUser = TblUser::find()->where(['id'=>$userID])->one();
    if(isset($getUser) && $getUser->id != ""){
      $employee_id = $getUser->fk_employee_id;
      if($employee_id != ""){
        $getemployee = TblEmployee::find()->where(['id'=>$employee_id,'status'=>1])->one();
        if(isset($getemployee) && $getemployee->id != ""){
          $role_id = $getemployee->fk_role_id;
          $employee_id = $getemployee->id;
        }else{
          $role_id = 0;
          $employee_id = 0;
        }
      }else{
        $role_id = 0;
        $employee_id = 0;
      }
    }else{
      $role_id = 0;
      $employee_id = 0;
    }
    // echo $role_id;
?>

<div class="px-3 mb-5">
  <div class="row justify-content-between">
    <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-xxl-0 border-bottom-xxl-0 border-end border-bottom pb-4 pb-xxl-0 ">
      <span class="fs-5 lh-1 fa-solid fa-cubes text-danger"></span>
      <h1 class="fs-5 pt-3"><?=number_format($totalQty)?></h1>
      <p class="fs-9 mb-0">Total Items in Stock</p>
    </div>
    <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-bottom-xxl-0 border-bottom border-end border-end-md-0 pb-4 pb-xxl-0 pt-4 pt-md-0">
      <span class="fs-5 lh-1 fa-solid fa-receipt text-primary"></span>
      <h1 class="fs-5 pt-3">
        <?php
            $subqueryAP = (new Query())
            ->select(['fk_vendor_invoice_id', 'SUM(amount_received) AS total_amount_paid'])
            ->from('tbl_vendor_account_payable')
            ->groupBy('fk_vendor_invoice_id');

            $totalOutstandingPayables = (new Query())
            ->select(['SUM(i.total_amount - COALESCE(ap.total_amount_paid, 0)) AS total_outstanding'])
            ->from(['i' => 'tbl_vendor_invoice'])
            ->leftJoin(['ap' => $subqueryAP], 'i.id = ap.fk_vendor_invoice_id')
            ->where(['!=', 'i.status', 0])
            ->andWhere(['i.fk_location_id'=>$user_company])
            ->scalar();
            if($totalOutstandingPayables > 0 && $totalOutstandingPayables != ""){
              echo "$".number_format($totalOutstandingPayables,2,'.',',');
            }else{
              echo "$0.00";
            }
         ?>
      </h1>
      <p class="fs-9 mb-0">Total Pending Payables</p>
    </div>
    <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-md border-end-xxl-0 border-bottom border-bottom-md-0 pb-4 pb-xxl-0 pt-4 pt-xxl-0">
      <span class="fs-5 lh-1 fas fa-file-invoice text-info"></span>
      <h1 class="fs-5 pt-3">
        <?php
        $subquery = (new Query())
        ->select([
          'ir.fk_invoice_id',
          'ROUND(SUM(ir.amount_received), 2) AS total_amount_received'
        ])
        ->from('tbl_account_receivable ir')
        ->where(['!=', 'ir.status', 0])
        ->groupBy('ir.fk_invoice_id');

        $totalOutstandingReceivables = (new Query())
        ->select([
          'SUM(CASE
          WHEN ROUND(i.total_amount - COALESCE(ir.total_amount_received, 0), 2) < 0
          THEN 0
          ELSE ROUND(i.total_amount - COALESCE(ir.total_amount_received, 0), 2)
          END) AS total_outstanding'
        ])
        ->from('tbl_invoice i')
        ->leftJoin(['ir' => $subquery], 'i.id = ir.fk_invoice_id')
        ->where(['!=', 'i.status', 0])
        ->andWhere(['i.fk_bill_from_id'=>$user_company])
        ->scalar();
        if($totalOutstandingReceivables > 0 && $totalOutstandingReceivables != ""){
          echo "$".number_format($totalOutstandingReceivables,2,'.',',');
        }else{
          echo "$0.00";
        }
         ?>
      </h1>
      <p class="fs-9 mb-0">Total Pending Receivables</p>
    </div>
    <div class="col-6 col-md-4 col-xxl-2 text-center border-translucent border-start-xxl border-end border-end-xxl-0 pb-md-4 pb-xxl-0 pt-4 pt-xxl-0">
      <span class="fs-5 lh-1 fas fa-list text-success"></span>
      <h1 class="fs-5 pt-3">
        <?php
        $totalOrders = (new Query())
        ->select('count(*)')
        ->from('tbl_purchase_order')
        ->where(['!=', 'status', 0])
        ->andWhere('purchase_status in (1,2)')
        ->andWhere(['fk_location_id'=>$user_company])
        ->scalar();
        // echo $totalRevenue;
        if($totalOrders > 0 && $totalOrders != ""){
          echo $totalOrders;
        }else{
          echo "0";
        }
         ?>
      </h1>
      <p class="fs-9 mb-0">Open Purchase Orders</p>
    </div>
  </div>
</div>
<!-- stats ended -->
<!-- attendance and graph started -->
<div class="row d-flex align-items-stretch">
  <div class="col-lg-4">
    <div class="card shadow-none border mt-3" data-component-card="data-component-card">
      <div class="card-header p-4 border-bottom bg-body">
        <div class="row g-3 justify-content-between align-items-center">
          <div class="col-12 col-md">
            <h4 class="text-body mb-0" data-anchor="data-anchor">Attendance</h4>
          </div>
        </div>
      </div>
      <div class="card-body p-3">
        <?php
        //check if user has signed in for today
        $datetime = date("Y-m-d h:i a");
        // Convert datetime to Unix timestamp
        $timestamp = strtotime($datetime);
        // Subtract time from datetime
        // $time = $timestamp - (4 * 60 * 60);
        $a_date = date('Y-m-d');
        // echo $session->get('id');
        $check = TblAttendance::find()
                  ->where(['fk_user_id'=>$session->get('userId')])
                  ->andWhere(['date'=>$a_date])
                  ->andWhere(['fk_location_id'=>$user_company])
                  ->orderBy(['UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(date," ",in_time), "%Y-%m-%d %h:%i %p"))'=>SORT_ASC])
                  ->all();
        // print_r($check);
        if(isset($check) && count($check)>0){
          ?>
          <table class="table v-middle table-bordered table-striped">
            <thead>
              <tr>
                <th>Date</th>
                <th>In Time</th>
                <th>Out Time</th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach($check as $c){

                echo '<tr>
                <td>'.date('d-F-Y',strtotime(str_replace('-', '/', $c->date))).'</td>
                <td>'.$c->in_time.'</td>';
                if($c->out_time == "" || $c->out_time == NULL){
                  echo '<td>';
                  ?>
                  <form method="post" action="<?= Url::to(['site/signoutattendance']) ?>">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                    <input type="hidden" name="att_id" value="<?=$c->id?>"/>
                    <input type="submit" value="Sign Out" class="btn btn-primary">
                  </form>
                  <?php
                  echo '</td>';
                }else{
                  echo '<td>'.$c->out_time.'</td>';
                }
                echo '</tr>';
              }
              ?>
            </tbody>
          </table>
          <?php
          if($check[count($check)-1]->out_time != ""){
            //all the entries have sign in and sign out time so add sign in button here
            ?>
            <form method="post" action="<?= Url::to(['site/signinattendance']) ?>">
              <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
              <div class="row">
                <div class="col-lg-6">
                  <input type="submit" value="Sign In" class="btn btn-primary">
                </div>
              </div>
            </form>
            <?php
          }
        }else{
          //no entry exists
          ?>
          <form method="post" action="<?= Url::to(['site/signinattendance']) ?>">
            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
            <div class="row">
              <div class="col-lg-12" align="center">
                <input type="submit" value="Sign In" class="btn btn-primary w-100">
              </div>
            </div>
          </form>
          <?php
        }
        ?>
      </div>
    </div>
  </div><!-- attendance col-4 ended -->
  <div class="col-lg-8">
    <div class="card shadow-none border border-300 mt-3" data-component-card="data-component-card">
      <div class="card-header p-4 border-bottom border-300 bg-soft">
        <div class="row g-3 justify-content-between align-items-center">
          <div class="col-12 col-md">
            <h4 class="text-900 mb-0" data-anchor="data-anchor">Invoices and Account Receivables</h4>
          </div>
        </div>
      </div>
      <div class="card-body p-4">
        <div class="echart-bar-line-mixed-chart-example" style="min-height:350px"></div>
      </div>
    </div>

  </div><!-- graph col-8 ended -->
</div>
