<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\TblProductCategory;
use app\models\TblProductAssemblyCategory;
use yii\helpers\ArrayHelper;
use yii\db\Query;
/** @var yii\web\View $this */
/** @var app\models\TblProductCategory $model */
/** @var yii\widgets\ActiveForm $form */
// $allcats = TblProductCategory::find()->where(['status'=>1])->all();
// $catsarr = ArrayHelper::map($allcats,'id','title');
$catsarr = TblProductCategory::find()->select(['title', 'id'])->where('status != 0')->indexBy('id')->column();
function getCategories($excludeId = null, $parent_id = null, $level = 0, $categories = [])
{
    $query = (new Query())
        ->select(['id', 'title'])
        ->from('tbl_product_category')
        ->where(['parent_id' => $parent_id])
        ->andWhere(['!=', 'status', 0]);

    // 🚫 Exclude current category completely
    if (!empty($excludeId)) {
        $query->andWhere(['!=', 'id', $excludeId]);
    }

    $rows = $query->orderBy(['title' => SORT_ASC])->all();

    foreach ($rows as $category) {
        $categories[$category['id']] =
            str_repeat('&nbsp;&nbsp;&nbsp;', $level)
            . str_repeat('--', $level)
            . ' '
            . ucwords(strtolower($category['title']));

        // 🔁 Recurse normally
        $categories = getCategories(
            $excludeId,
            $category['id'],
            $level + 1,
            $categories
        );
    }

    return $categories;
}
$excludeId = !$model->isNewRecord ? $model->id : null;

// ⚠️ Always start from ROOT (parent_id = null)
$categoryList = getCategories($excludeId);
// $categoryList = getCategories();

$allassemblycats = TblProductAssemblyCategory::find()->where(['status'=>1])->all();
$assemblycatsarr = ArrayHelper::map($allassemblycats,'id','title');
?>

<div class="tbl-product-category-form">
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
  <?php $form = ActiveForm::begin([
      'options' => [
        'autocomplete' => 'off',
        'enctype' => 'multipart/form-data'
      ],
  ]); ?>
    <div class="card shadow rounded mt-2">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-3 mb-2">
              <?= $form->field($model, 'title')->textInput(['maxlength' => true])->label('Title<span class="text-danger">*</span>') ?>
            </div>
            <div class="col-lg-3 mb-2">
              <?= $form->field($model, 'parent_id')->dropDownList($categoryList,['prompt'=>'Select', 'encode' => false,'class'=>'form-select']) ?>
              <?php
                $this->registerJs('
                  new Choices("#tblproductcategory-parent_id", {
                      allowHTML: true,  // Enable HTML rendering inside options
                  });
                ');
               ?>
            </div>
            <div class="col-lg-3 mb-2">
              <?= $form->field($model, 'show_on_website')->dropDownList([1=>"Yes",2=>"No"],['prompt'=>'Select', 'encode' => false,'class'=>'form-select']) ?>
            </div>
            <!-- <div class="col-lg-3 mb-2">
              <?= $form->field($model, 'fk_assembly_category_id')->dropDownList($assemblycatsarr,['prompt'=>'Select', 'encode' => false,'class'=>'form-select'])->label('Assembly Category <sup><span class="badge bg-danger">New</span></sup>') ?>
            </div> -->
            <div class="col-lg-3 mb-2">
              <label for="">Category Image</label>
              <input type="file" name="cat_img" accept="image/*" class="form-control">
              <p class="fs-sm">
                The preferred image size is 200 X 200
              </p>
              <?php

                if(!$model->isNewRecord && $model->image_url != ""){
                  $url = Yii::getAlias('@web/product-cat-images/' . $model->image_url);
                  echo '  <a href="#" class="open-image" data-bs-toggle="modal" data-bs-target="#imageModal" data-img="'.$url.'">
                    <img src="product-cat-images/'.$model->image_url.'" width="100px"/>
                    </a>';
                }
               ?>
            </div>

            <div class="col-lg-12 mb-2">
              <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
            </div>
          </div>


        </div>
    </div>
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="exampleModalToggleLabel">Image</h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-3">
        <img id="modalImage" src="" class="img-fluid w-100" style="border-radius:6px;">
      </div>
    </div>
    </div>
    </div>
    <?php
    $js = <<<JS
    $(document).on('click', '.open-image', function (e) {
    e.preventDefault();
    let imgSrc = $(this).data('img');
    $('#modalImage').attr('src', imgSrc);
    });
    JS;

    $this->registerJs($js);

    ?>



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
