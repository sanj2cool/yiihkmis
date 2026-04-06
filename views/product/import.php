<?php

use yii\helpers\Html;
use yii\helpers\Url;
/** @var yii\web\View $this */

$this->title = 'Import Items';
$this->params['breadcrumbs'][] = ['label' => 'Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-truck-inventory-create">

<div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-6">
                <h4 class="mb-0"><?= Html::encode($this->title) ?></h4>
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
          <span class="fas fa-check-circle text-success fs-3 me-3"></span>
          <p class="mb-0 flex-1"><?= Yii::$app -> session -> getFlash('success'); ?></p>

          <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
      }
      if(Yii::$app -> session -> getFlash('error')!=null){
        ?>
        <div class="alert alert-outline-danger d-flex align-items-center" role="alert">
          <span class="fas fa-times-circle text-danger fs-3 me-3"></span>
          <p class="mb-0 flex-1"><?= Yii::$app -> session -> getFlash('error'); ?></p>

          <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
      }
      ?>
    </div>
      <div class="row g-3 mb-3 mt-3">
        <div class="col-xl-7 col-xxl-6">
          <div class="card border border-300 h-100 w-100 overflow-hidden">
            <div class="card-body px-2 position-relative">
              <h3 class="mb-5">Things to remember</h3>
              <p class="text-700 fw-semi-bold pt-1">The limit for number of records to be uploaded in one time is 150 records. Any record after 150th place will not be processed.</p>
              <p class="text-700 fw-semi-bold">Download the empty sample file below and the data should be in the same order as it is in the sample csv file.</p>
              <a href="sample-file/items-HK-parts-sample.csv" download class="btn btn-primary w-100">Download Empty Sample File</a>
            </div>
            <div class="card-footer border-0 px-2 z-index-1 pt-1">
              <p class="text-700 fw-semi-bold">If you want to view how the data should look like download the below file which contains sample data for better understanding the flow of content.</p>
              <a href="sample-file/items-HK-parts-with-data.csv" download><button type="button" class="btn btn-primary w-100">Download Sample File with Data</button></a>
              <p class="text-700 fw-semi-bold pt-1">If the mentioned make, category or any other column value does not exists in our system it will be replaced with empty value.</p>
            </div>
          </div>
        </div>
        <div class="col-xl-5 col-xxl-6">
          <div class="card d-flex flex-column mb-5">
            <!-- <textarea class="form-control border-200 rounded-bottom-0 border-0 flex-1 fs-0" rows="7" placeholder="Write something..."></textarea> -->
            <form method="post" enctype="multipart/form-data" action="<?=Url::to(['product/uploadcsv'])?>">
              <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
            <div class="border-200 rounded-bottom-0 border-0 flex-1 fs-0 p-4 h-100">
              <label for="">Upload CSV</label>
              <input type="file" name="my-image" class="form-control" required accept=".csv">
            </div>
            <div class="card-footer p-3">
              <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-right">
                  <button class="btn btn-primary btn-sm px-6 px-sm-8">Upload File</button>
                </div>
              </div>
            </div>
          </form>
        </div><!-- form card ended  -->
          <div class="card d-flex flex-column mb-5">
            <div class="card-body px-2 position-relative">
              <h5>Required fields to add the record are:</h5>
              <div class="row">
                <div class="col-lg-12">
                  <ul>
                    <li>SKU</li>
                    <li>Name</li>
                    <li>Category</li>
                  </ul>
                </div>

              </div>

            </div>
          </div>


        </div>
      </div>
</div>
