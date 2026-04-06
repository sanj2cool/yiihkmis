<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\TblEmployee;
use app\models\TblProduct;
/** @var yii\web\View $this */
/** @var app\models\TblLostSalesModule $model */
/** @var yii\widgets\ActiveForm $form */
if($model->isNewRecord){
  $model->date = date('Y-m-d');
}
$session = Yii::$app -> session;
$fk_location_id = $session['userCompany'];
//-----get employees------------
$allemps = TblEmployee::find()->where(['status'=>1,'fk_location_id'=>$fk_location_id])->all();
$emparr = ArrayHelper::map($allemps,'id','name');

//-----get tax rates  --------
$alltaxrates = TblProduct::find()->where(['status'=>1])->all();
// $truckarr = ArrayHelper::map($alltrucks,'id','unit_no');
$taxarr = ArrayHelper::map($alltaxrates, 'id', function($model) {
  // return $model->unit_no . ' | Plate No. ' . $model->plate;
  return $model->name.' ['.$model->sku.']';
});
?>

<div class="tbl-lost-sales-module-form">
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
          <div class="col-lg-6">
                <?= $form->field($model, 'prepared_by')->dropDownList($emparr,['prompt'=>'Select','class'=>'form-select']) ?>
          </div>
          <div class="col-lg-6">
            <?= $form->field($model, 'date')->textInput() ?>
          </div>
        </div>
      </div>
    </div>


    <div class="card shadow rounded mt-2">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-lg-3">
              <?= $form->field($model, 'fk_product_id')->dropDownList($taxarr,['prompt'=>'Select','data-choices'=>'data-choices','data-options'=>'{"removeItemButton":true,"placeholder":true}']) ?>
          </div>
          <div class="col-lg-3">
            <?= $form->field($model, 'non_product')->textInput(['maxlength' => true]) ?>
          </div>
          <div class="col-lg-6">
            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
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
  $this->registerJs('

  var invoice_date_picker = $("#tbllostsalesmodule-date").flatpickr({
    disableMobile: "true",
    dateFormat: "Y-m-d"
  });
  ');
 ?>
