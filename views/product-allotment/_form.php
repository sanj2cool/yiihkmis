<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\TblProduct;
use app\models\TblEmployee;
use app\models\TblAllotmentStatus;
use yii\helpers\ArrayHelper;
/** @var yii\web\View $this */
/** @var app\models\TblProductAllotment $model */
/** @var yii\widgets\ActiveForm $form */
//-----get products --------
$allprods = TblProduct::find()->where(['status'=>1])->all();
// $truckarr = ArrayHelper::map($alltrucks,'id','unit_no');
$prodarr = ArrayHelper::map($allprods, 'id', function($model) {
  // return $model->unit_no . ' | Plate No. ' . $model->plate;
  return $model->name.' ['.$model->sku.']';
});


//-----get employees------------
$allemps = TblEmployee::find()->where(['status'=>1])->all();
$emparr = ArrayHelper::map($allemps,'id','name');

//-----get Status------------
$allstatus = TblAllotmentStatus::find()->where(['status'=>1])->all();
$statusarr = ArrayHelper::map($allstatus,'id','title');
?>

<div class="tbl-product-allotment-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="card shadow rounded mt-2">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-lg-4">
            <?= $form->field($model, 'fk_product_id')->dropDownList($prodarr,['prompt'=>'Select']) ?>
          </div>
          <div class="col-lg-4">
              <?= $form->field($model, 'no_of_items')->textInput(['onkeyup'=>'checkNum(this);']) ?>
          </div>
          <div class="col-lg-4">
              <?= $form->field($model, 'allotment_date')->textInput() ?>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-lg-4">
            <?= $form->field($model, 'allotted_by')->dropDownList($emparr,['prompt'=>'Select']) ?>
          </div>
          <div class="col-lg-4">
            <?= $form->field($model, 'fk_allotment_status_id')->dropDownList($statusarr,['prompt'=>'Select']) ?>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow rounded mt-2">
      <div class="card-body p-3">
            <?= $form->field($model, 'remarks')->textarea(['rows' => 6]) ?>
      </div>
    </div>


    <div class="card shadow rounded p-3 mt-4 mb-4">
            <div class="row text-center">
              <?php
              if($model->isNewRecord){
                ?>
                <div class="d-grid gap-2 col-4 mx-auto pe-1">
                  <input type="submit" name="new_update" value="Create & Edit" class="btn btn-danger btn-sm"/>
                </div>
                <div class="d-grid gap-2 col-4 mx-auto pe-1 ps-1">
                  <input type="submit" name="new_new" value="Create & New" class="btn btn-success btn-sm"/>
                </div>
                <div class="d-grid gap-2 col-4 mx-auto ps-1">
                  <input type="submit" name="new_exit" value="Create & Exit" class="btn btn-secondary btn-sm"/>
                </div>
                <?php
              }else{
                ?>
                <div class="d-grid gap-2 col-4 mx-auto pe-1">
                  <input type="submit" name="update" value="Update & Edit" class="btn btn-danger btn-sm"/>
                </div>
                <div class="d-grid gap-2 col-4 mx-auto pe-1 ps-1">
                  <input type="submit" name="new" value="Update & New" class="btn btn-success btn-sm"/>
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
$this->registerJs('
  // Initialize Choices.js
  //--------------create client code starts -----------------------
  const element = document.getElementById("tblproductallotment-fk_product_id");
  const choices = new Choices(element, {
    removeItemButton: true,
    placeholder: true
  });
  //----------DATE PICKER FOR SERVICE DATE ------------------
  var service_date_picker = $("#tblproductallotment-allotment_date").flatpickr({
    disableMobile: "true",
    dateFormat: "Y-m-d"
  });

');
 ?>
