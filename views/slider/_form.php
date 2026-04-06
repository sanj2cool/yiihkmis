<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TblSlider $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-slider-form">
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
    }else if(Yii::$app -> session -> getFlash('error_item')!=null){
      ?>
      <div class="alert alert-danger d-flex align-items-center mt-2 p-2" role="alert">
        <span class="fas fa-times-circle text-white fs-2 me-3"></span>
        <p class="mb-0 flex-1 text-white">Item(s) with same name already exists.</p>
        <!-- <button class="btn-close text-white" type="button" data-bs-dismiss="alert" aria-label="Close"></button> -->
      </div>
        <div class="bg-white border p-4 rounded">
          <?=Yii::$app -> session -> getFlash('error_item')?>
        </div>
      <?php
    }
    ?>
  </div>

    <?php $form = ActiveForm::begin([
        'options' => [
          'autocomplete' => 'off',
          'enctype' => 'multipart/form-data'
        ],
    ]); ?>

    <div class="card shadow rounded mt-2">

        <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-6 mb-2">
                <?= $form->field($model, 'sequence')->textInput() ?>
            </div>
            <div class="col-lg-6 mb-2">
              <?= $form->field($model, 'status_val')->dropDownList([1=>"Active",2=>"Inactive"],['prompt'=>'Select']) ?>

            </div>
          </div>
          <div class="row">
            <div class="col-lg-4">
              <label for="">Upload Image</label>
              <div class="image_area">
                <!-- <form method="post"> -->
                <label for="upload_image">
                  <img src="image.png" id="uploaded_image" class="img-fluid img-square" />
                  <div class="overlay">
                    <div class="text">Click here</div>
                  </div>
                  <input type="file" name="image1" class="image" id="upload_image" style="display:none" />
                </label>
                <!-- </form> -->
              </div>
              <input type="hidden" name="hd_img_1" id="hd_img_1" value="" />
            </div>
            <div class="col-lg-4">
              <br />
              <?php
                if(!$model->isNewRecord){
                  echo '<img src="slider-images/'.$model->url.'" width="500px"/>';
                }
               ?>
            </div>
          </div>
          <div class="row">
            <div class="col mb-3" style="color:#f00;font-size:14px;">
              <strong>* Image sizes:</strong>&nbsp;&nbsp;
              <strong>Landscape: 1920 X 800</strong>&nbsp;&nbsp;
              <!-- <strong>Portrait: 1200 X 1600</strong> -->


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
<style>

.image_area {
  position: relative;
}

img {
  display: block;
  max-width: 100%;
}

.preview {
  overflow: hidden;
  width: 160px;
  height: 160px;
  margin: 10px;
  border: 1px solid red;
}
.preview2{
  overflow: hidden;
  width: 160px;
  height: 160px;
  margin: 10px;
  border: 1px solid red;
}
.preview3{
  overflow: hidden;
  width: 160px;
  height: 160px;
  margin: 10px;
  border: 1px solid red;
}
.preview4{
  overflow: hidden;
  width: 160px;
  height: 160px;
  margin: 10px;
  border: 1px solid red;
}
.preview5{
  overflow: hidden;
  width: 160px;
  height: 160px;
  margin: 10px;
  border: 1px solid red;
}
.preview6{
  overflow: hidden;
  width: 160px;
  height: 160px;
  margin: 10px;
  border: 1px solid red;
}
.modal-lg{
  max-width: 1000px !important;
}

.overlay {
  position: absolute;
  bottom: 10px;
  left: 0;
  right: 0;
  background-color: rgba(255, 255, 255, 0.5);
  overflow: hidden;
  height: 0;
  transition: .5s ease;
  width: 100%;
}

.image_area:hover .overlay {
  height: 50%;
  cursor: pointer;
}

.text {
  color: #333;
  font-size: 20px;
  position: absolute;
  top: 50%;
  left: 50%;
  -webkit-transform: translate(-50%, -50%);
  -ms-transform: translate(-50%, -50%);
  transform: translate(-50%, -50%);
  text-align: center;
}

</style>

<!-- Modal1 -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Crop Image Before Upload</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="container">
          <div class="row">
            <div class="col-md-8">
              <img src="" id="sample_image" />
            </div>
            <div class="col-md-4">
              <div class="preview"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" id="crop" class="btn btn-primary btn-sm">Crop Landscape</button> -->
        <!-- <button type="button" id="crop_p" class="btn btn-primary btn-sm">Crop Portrait</button> -->
        <button type="button" id="upload_img" class="btn btn-info btn-sm">Upload</button>
        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- Model1 End -->

<?php
$this->registerJsFile('/web/js/cropplugin.js',['depends' => [yii\web\JqueryAsset::className()]]);
 ?>
