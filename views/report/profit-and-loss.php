<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $from string */
/* @var $to string */
/* @var $totalSales float */
/* @var $totalReceived float */
/* @var $totalPurchases float */
/* @var $totalPaid float */
/* @var $totalCOGS float */
/* @var $grossProfit float */
/* @var $netProfit float */

$this->title = "Profit & Loss Report ($from to $to)";
?>
<div class="card">
  <div class="card-header bg-light">
    <div class="row align-items-center">
      <div class="col">
        <h5 class="mb-0"><?= Html::encode($this->title) ?></h5>
      </div>
    </div>
  </div>
  <div class="card-body border-top">
    <form name="frm_reservation" id="" action="<?= Yii::$app->urlManager->createUrl(['report/profit-and-loss']) ?>" method="post" autocomplete="off">
      <input type="hidden" name="_csrf-backend" value="<?=Yii::$app->request->getCsrfToken()?>" />
      <div class="row justify-content-center">
        <div class="col-lg-3">
          <label for="">From Date<span class="text-danger">*</span></label>
          <input type="text" class="form-control datetimepicker" required="required" value="<?php if(isset($from)){ echo $from;} ?>" name="from_date" data-options='{"enableTime":false,"dateFormat":"Y-m-d","disableMobile":true}'>
          </div>
          <div class="col-lg-3">
            <label for="">To Date<span class="text-danger">*</span></label>
            <input type="text" class="form-control datetimepicker" required="required" value="<?php if(isset($to)){ echo $to;} ?>" name="to_date" data-options='{"enableTime":false,"dateFormat":"Y-m-d","disableMobile":true}'>
          </div>
          <div class="col-md-2 pt-4">
              <button type="submit" class="btn btn-primary">Search</button>
          </div>
        </div>
      </form>
      <hr />
      <div class="report-profit-loss table-responsive">
        <table class="table table-bordered table-striped">
          <tr>
            <th>Metric</th>
            <th>Amount</th>
          </tr>
          <tr>
            <td>Total Sales</td>
            <td><?= Yii::$app->formatter->asCurrency($totalSales) ?></td>
          </tr>
          <tr>
            <td>Total Customer Payments Received</td>
            <td><?= Yii::$app->formatter->asCurrency($totalReceived) ?></td>
          </tr>
          <tr>
            <td>Total Vendor Purchases</td>
            <td><?= Yii::$app->formatter->asCurrency($totalPurchases) ?></td>
          </tr>
          <tr>
            <td>Total Vendor Payments Made</td>
            <td><?= Yii::$app->formatter->asCurrency($totalPaid) ?></td>
          </tr>
          <tr>
            <td>Cost of Goods Sold (COGS)</td>
            <td><?= Yii::$app->formatter->asCurrency($totalCOGS) ?></td>
          </tr>
          <tr>
            <td>Profit/Loss (Total Sales - COGS)</td>
            <td><?= Yii::$app->formatter->asCurrency($grossProfit) ?></td>
          </tr>
          <!-- <tr>
            <td>Net Profit</td>
            <td><?= Yii::$app->formatter->asCurrency($netProfit) ?></td>
          </tr> -->
        </table>
      </div>
    </div>
  </div>
