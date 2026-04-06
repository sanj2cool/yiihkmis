<?php
use yii\helpers\Url;
$this->title = "Reports";
?>
<div class="row">
  <div class="col-sm-12">
    <div class="card mb-3">
      <div class="card-header bg-light">
        <h5 class="mb-0">Items Creation Reports</h5>
      </div>
      <div class="card-body border-top p-0">

        <div class="row g-0 align-items-center border-bottom py-2 px-3">
          <div class="col-md mt-1 mt-md-0">
            <a class="unformat" href="<?=Url::to(['report/inventory-count-report'])?>">Inventory Count Report <sup>
              <badge class="badge bg-danger">New</badge>
            </sup></a> |
            <a class="unformat text-sm" href="<?=Url::to(['report/inventory-count-report'])?>" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
            <p>
              A report listing basic item details along with the current inventory count
            </p>
          </div>
          <div class="col-md-auto">
            <p class="mb-0">
              <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

            </p>
          </div>
        </div>



        <div class="row g-0 align-items-center border-bottom py-2 px-3">
          <div class="col-md mt-1 mt-md-0">
            <a class="unformat" href="<?=Url::to(['report/low-inventory-count-report'])?>">Low Inventory Count Report <sup>
              <badge class="badge bg-danger">New</badge>
            </sup></a> |
            <a class="unformat text-sm" href="<?=Url::to(['report/low-inventory-count-report'])?>" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
            <p>
              A report listing all the items which are below the reorder quantity.
            </p>
          </div>
          <div class="col-md-auto">
            <p class="mb-0">
              <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

            </p>
          </div>
        </div>


        <div class="row g-0 align-items-center border-bottom py-2 px-3">
          <div class="col-md mt-1 mt-md-0">
            <a class="unformat" href="<?=Url::to(['report/critical-low-inventory-count-report'])?>">Critically Low Inventory Count Report <sup>
              <badge class="badge bg-danger">New</badge>
            </sup></a> |
            <a class="unformat text-sm" href="<?=Url::to(['report/critical-low-inventory-count-report'])?>" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
            <p>
              A report listing all the items which are below the 80% threshold value of reorder quantity.
            </p>
          </div>
          <div class="col-md-auto">
            <p class="mb-0">
              <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

            </p>
          </div>
        </div>


      </div>
    </div>
  </div>
</div><!-- item creation row ended -->




<div class="row">
  <div class="col-sm-12">
    <div class="card mb-3">
      <div class="card-header bg-light">
        <h5 class="mb-0">Customer Reports</h5>
      </div>
      <div class="card-body border-top p-0">

        <div class="row g-0 align-items-center border-bottom py-2 px-3">
          <div class="col-md mt-1 mt-md-0">
            <a class="unformat" href="<?=Url::to(['report/customer-list'])?>">Customer List<sup>
              <badge class="badge bg-danger">New</badge>
            </sup></a> |
            <a class="unformat text-sm" href="<?=Url::to(['report/customer-list'])?>" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
            <p>
              Detailed list of all customers
            </p>
          </div>
          <div class="col-md-auto">
            <p class="mb-0">
              <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

            </p>
          </div>
        </div>



        <div class="row g-0 align-items-center border-bottom py-2 px-3">
          <div class="col-md mt-1 mt-md-0">
            <a class="unformat" href="<?=Url::to(['report/customer-account-statements'])?>">Customer Account Statements <sup>
              <badge class="badge bg-danger">New</badge>
            </sup></a> |
            <a class="unformat text-sm" href="<?=Url::to(['report/customer-account-statements'])?>" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
            <p>
              Summary of customer transactions, Outstanding balances and due dates.
            </p>
          </div>
          <div class="col-md-auto">
            <p class="mb-0">
              <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

            </p>
          </div>
        </div>


        <div class="row g-0 align-items-center border-bottom py-2 px-3">
          <div class="col-md mt-1 mt-md-0">
            <a class="unformat" href="<?=Url::to(['report/customer-aging-report'])?>">Customer Aging Report <sup>
              <badge class="badge bg-danger">New</badge>
            </sup></a> |
            <a class="unformat text-sm" href="<?=Url::to(['report/customer-aging-report'])?>" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
            <p>
              Aging of outstanding invoices Categorized by 15, 30, 60, 90 days, etc.
            </p>
          </div>
          <div class="col-md-auto">
            <p class="mb-0">
              <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

            </p>
          </div>
        </div>





      </div>
    </div>
  </div>
</div><!-- item creation row ended -->




<div class="row">
  <div class="col-sm-12">
    <div class="card mb-3">
      <div class="card-header bg-light">
        <h5 class="mb-0">User/Employee Reports</h5>
      </div>
      <div class="card-body border-top p-0">

        <div class="row g-0 align-items-center border-bottom py-2 px-3">
          <div class="col-md mt-1 mt-md-0">
            <a class="unformat" href="<?=Url::to(['report/attendance'])?>">Attendance Report<sup>
              <badge class="badge bg-danger">New</badge>
            </sup></a> |
            <a class="unformat text-sm" href="<?=Url::to(['report/attendance'])?>" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
            <p>
              A report listing all the attendance record for each user over a period of time selected.

            </p>
          </div>
          <div class="col-md-auto">
            <p class="mb-0">
              <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

            </p>
          </div>
        </div>










      </div>
    </div>
  </div>
</div><!-- item creation row ended -->
