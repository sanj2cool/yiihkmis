<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\TblProductCategory;
use app\models\TblProduct;
use app\models\TblProductCrossRef;
use app\models\TblProductAlternate;
use app\models\TblCycleCountCategory;
use app\models\TblProductImage;
use app\models\TblProductRelated;
use app\models\TblProductCompetition;
use yii\helpers\ArrayHelper;


use app\models\TblVendor;
use app\models\TblVendorInvoice;
use app\models\TblBin;
use app\models\TblBinArea;
use app\models\TblBinBay;
use app\models\TblBinLevel;
use app\models\TblBinPosition;
use app\models\TblBinRow;
use app\models\TblProductReceiving;
use app\models\TblBinLocations;
use app\models\TblUser;
use app\models\TblProductBrand;
use app\models\TblProductAssignedCategory;
use app\models\TblBrand;
use app\models\TblProductAssemblyCategory;

use yii\db\Query;
/** @var yii\web\View $this */
/** @var app\models\TblProduct $model */
/** @var yii\widgets\ActiveForm $form */
$catsarr = TblProductCategory::find()->select(['title', 'id'])->where('status != 0')->indexBy('id')->column();

$catscyclearr = TblCycleCountCategory::find()->select(['title', 'id'])->where('status != 0')->indexBy('id')->column();
$allcats = TblCycleCountCategory::find()->where(['status'=>1])->all();
// $truckarr = ArrayHelper::map($alltrucks,'id','unit_no');
$cyclecatarr = ArrayHelper::map($allcats, 'id', function($model) {
    return $model->title . ' [' . $model->description.']';
});
if(!$model->isNewRecord){
  //-------opening it update time remove this product from cross ref
  $allprods = TblProduct::find()->where(['status'=>1])->andWhere('id != '.$model->id)->all();
  $prodarr = ArrayHelper::map($allprods, 'id', function($model) {
      return $model->name . ' | SKU ' . $model->sku;
  });
}else{
  $allprods = TblProduct::find()->where(['status'=>1])->all();
  $prodarr = ArrayHelper::map($allprods, 'id', function($model) {
      return $model->name . ' | SKU ' . $model->sku;
  });
}
// print_r($allprods);
function getCategories($parent_id = null, $level = 0, $categories = []) {
    // Create a new query using Yii2's Query builder
    $query = (new Query())
        ->select(['id', 'title'])
        ->from('tbl_product_category')
        ->where(['parent_id' => $parent_id]) // Adjust for null parent_id
        ->andWhere('status != 0')
        ->orderBy(['title' => SORT_ASC])
        ->all();
        foreach ($query as $category) {
               // Add the category to the list with an indent based on its level
               $categories[$category['id']] = str_repeat('&nbsp;&nbsp;&nbsp;', $level).str_repeat('--', $level) . ' ' . ucwords(strtolower($category['title']));

               // Recursively fetch child categories
               $categories = getCategories($category['id'], $level + 1, $categories);
           }

    return $categories;
}
$categoryList = getCategories();

$allassemblycats = TblProductAssemblyCategory::find()->where(['status'=>1])->orderBy(['title'=>SORT_ASC])->all();
$assemblycatsarr = ArrayHelper::map($allassemblycats,'id','title');
?>

<div class="tbl-product-form">
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

    <ul class="nav nav-underline fs-9" role="tablist">
      <li class="nav-item"> <a class="nav-link active" data-bs-toggle="tab" href="#home" role="tab"><span class="hidden-sm-up"><i class="fas fa-list"></i></span> <span class="hidden-xs-down">Item Details</span></a> </li>
      <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#tabimages" role="tab"><span class="hidden-sm-up"><i class="fa-solid fa-camera"></i></span> <span class="hidden-xs-down">Images</span></a> </li>
      <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#tabitemqty" role="tab"><span class="hidden-sm-up"><i class="fa-solid fa-dolly"></i></span> <span class="hidden-xs-down">Item Received History</span></a> </li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane fade show active" id="home" role="tabpanel">
        <div class="card shadow rounded mt-2">
          <div class="card-header p-3 border-bottom border-300 bg-primary-subtle">
           <div class="row g-3 justify-content-between align-items-center">
             <div class="col-12 col-md">
               <h4 class="text-900 mb-0" data-anchor="data-anchor" id="basic-details">Basic Details</h4>
             </div>
           </div>
         </div>
            <div class="card-body p-3">
              <div class="row">
                <div class="col-lg-3">
                  <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                  <p style="font-size:12px;" class="text-danger">
                    Press tab to check for duplicate or similar items
                  </p>
                </div>

                <div class="col-lg-3">
                  <label for="">Brand<span class="text-danger">*</span></label>
                  <select class="form-select" name="brand[]" id="brands" multiple required>
                    <option value="">Select</option>
                    <?php
                      $getbrands = TblBrand::find()->select(['title', 'id'])->where('status != 0')->all();
                      if(isset($getbrands) && count($getbrands) > 0){
                        foreach($getbrands as $gb){
                          $check = TblProductBrand::find()->where(['fk_product_id'=>$model->id,'status'=>1,'fk_brand_id'=>$gb->id])->one();
                          if(isset($check) && $check->id != ""){
                              echo '<option value="'.$gb->id.'" selected="selected">'.$gb->title.'</option>';
                          }else{
                              echo '<option value="'.$gb->id.'">'.$gb->title.'</option>';
                          }

                        }
                      }
                     ?>
                  </select>
                </div>
                <div class="col-lg-3">

                  <label for="">Category<span class="text-danger">*</span></label>
                  <select class="form-select" name="categories[]" id="categories" multiple required>
                    <option value="">Select</option>
                    <?php
                      if(isset($categoryList) && count($categoryList) > 0){
                        // print_r($categoryList);
                        foreach($categoryList as $index=>$val){
                          // echo $categoryList[$index];
                          // echo $index;
                          $check = TblProductAssignedCategory::find()->where(['fk_product_id'=>$model->id,'status'=>1,'fk_category_id'=>$index])->one();
                          if(isset($check) && $check->id != ""){
                              echo '<option value="'.$index.'" selected="selected">'.$categoryList[$index].'</option>';
                          }else{
                              echo '<option value="'.$index.'">'.$categoryList[$index].'</option>';
                          }

                        }
                      }
                     ?>
                  </select>

                </div>
                <div class="col-lg-3">
                  <?= $form->field($model, 'fk_cycle_count_category_id')->dropDownList($cyclecatarr,['prompt'=>'Select']) ?>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-lg-3 mb-2">
                  <?= $form->field($model, 'fk_assembly_category_id')->dropDownList($assemblycatsarr,['prompt'=>'Select', 'encode' => false,'class'=>'form-select'])->label('Assembly Category <sup><span class="badge bg-danger">New</span></sup>') ?>
                </div>
                <div class="col-lg-2">
                  <?= $form->field($model, 'quantity_in_stock')->textInput(['onkeyup'=>'checkNum(this);']) ?>
                </div>
                <div class="col-lg-2">

                  <?= $form->field($model, 'max_qty_in_stock')->textInput(['onkeyup'=>'checkNum(this);']) ?>
                </div>
                <div class="col-lg-2">
                  <?= $form->field($model, 'status')->dropDownList([1=>"Active",2=>"Inactive"],['prompt' => 'Select']) ?>
                </div>
                <div class="col-lg-3">
                  <?= $form->field($model, 'show_on_website')->dropDownList([1=>"Yes",2=>"No"],['prompt'=>'Select', 'class' => 'form-select']) ?>
                </div>
                <div class="col-lg-6">
                  <?= $form->field($model, 'description')->textarea(['rows' => 4]) ?>
                </div>
                <div class="col-lg-6">
                  <div class="row">
                    <div class="col-lg-6">
                      <label for="">Website/Invoice Description</label>
                    </div>
                    <div class="col-lg-6 text-end">
                      <input type="checkbox" id="checkbox_description"> Same as Description
                    </div>
                  </div>
                  <?= $form->field($model, 'website_description')->textarea(['rows' => 4])->label(false) ?>
                </div>
              </div>
            </div>
        </div>
        <?php
          $this->registerJs('
            document.getElementById("checkbox_description").addEventListener("click", function () {
              if (this.checked) {
                let description = document.getElementById("tblproduct-description").value;
                document.getElementById("tblproduct-website_description").value = description;
                // console.log("Checkbox is checked ✅");
              } else {
                // console.log("Checkbox is unchecked ❌");
              }
            });
          ');
         ?>

        <div class="card shadow rounded mt-2">
          <div class="card-header p-3 border-bottom border-300 bg-success-subtle">
           <div class="row g-3 justify-content-between align-items-center">
             <div class="col-12 col-md">
               <h4 class="text-900 mb-0" data-anchor="data-anchor" id="pricing-details">Markup Details</h4>
             </div>
           </div>
         </div>
            <div class="card-body p-3">
              <div class="row">
                <div class="col-lg-2 mb-2">
                  <?= $form->field($model, 'default_tier')->dropDownList([1=>"Tier 1",2=>"Tier 2",3=>"Tier 3",4=>"Tier 4",5=>"Tier 5",6=>"Tier 6",7=>"Tier 7",8=>"Tier 8",9=>"Tier 9",10=>"Tier 10",11=>"Tier 11"],['prompt'=>'Select'])->label('Default Tier') ?>
                </div>
                <div class="col-lg-2 mb-2">
                  <?= $form->field($model, 'tier_1_markup')->textInput(['class'=>'floatNumberField form-control','onkeyup'=>'checkDec(this);']) ?>
                </div>
                <div class="col-lg-2 mb-2">
                  <?= $form->field($model, 'tier_2_markup')->textInput(['class'=>'floatNumberField form-control','onkeyup'=>'checkDec(this);']) ?>
                </div>
                <div class="col-lg-2 mb-2">
                  <?= $form->field($model, 'tier_3_markup')->textInput(['class'=>'floatNumberField form-control','onkeyup'=>'checkDec(this);']) ?>
                </div>
                <div class="col-lg-2 mb-2">
                  <?= $form->field($model, 'tier_4_markup')->textInput(['class'=>'floatNumberField form-control','onkeyup'=>'checkDec(this);']) ?>
                </div>
                <div class="col-lg-2 mb-2">
                  <?= $form->field($model, 'tier_5_markup')->textInput(['class'=>'floatNumberField form-control','onkeyup'=>'checkDec(this);']) ?>
                </div>

              </div>
              <div class="row">
                <div class="col-lg-2 mb-2">
                  <?= $form->field($model, 'tier_6_markup')->textInput(['class'=>'floatNumberField form-control','onkeyup'=>'checkDec(this);'])->label('Tier 6 Markup %') ?>
                </div>
                <div class="col-lg-2 mb-2">
                  <?= $form->field($model, 'tier_7_markup')->textInput(['class'=>'floatNumberField form-control','onkeyup'=>'checkDec(this);'])->label('Tier 7 Markup %') ?>
                </div>
                <div class="col-lg-2 mb-2">
                  <?= $form->field($model, 'tier_8_markup')->textInput(['class'=>'floatNumberField form-control','onkeyup'=>'checkDec(this);'])->label('Tier 8 Markup %') ?>
                </div>
                <div class="col-lg-2 mb-2">
                  <?= $form->field($model, 'tier_9_markup')->textInput(['class'=>'floatNumberField form-control','onkeyup'=>'checkDec(this);'])->label('Tier 9 Markup %') ?>
                </div>
                <div class="col-lg-2 mb-2">
                  <?= $form->field($model, 'tier_10_markup')->textInput(['class'=>'floatNumberField form-control','onkeyup'=>'checkDec(this);'])->label('Tier 10 Markup %') ?>
                </div>
                <div class="col-lg-2 mb-2">
                  <?= $form->field($model, 'tier_11_markup')->textInput(['class'=>'floatNumberField form-control','onkeyup'=>'checkDec(this);'])->label('Tier 11 Markup %') ?>
                </div>
              </div>

            </div>
          </div>




<div class="row">
  <div class="col-lg-6">
    <div class="card shadow rounded mt-2">
      <div class="card-header p-3 border-bottom border-300 bg-danger-subtle">
       <div class="row g-3 justify-content-between align-items-center">
         <div class="col-12 col-md">
           <h4 class="text-900 mb-0" data-anchor="data-anchor" id="basic-details">Cross Reference</h4>
         </div>
       </div>
     </div>
        <div class="card-body p-3">
          <?php
            if(!$model->isNewRecord){
              //----update time -------------
              $getcrossrefs = TblProductCrossRef::find()->where(['status'=>1,'fk_product_id'=>$model->id])->all();
              if(isset($getcrossrefs) && count($getcrossrefs) > 0){
                $i = 0;
                foreach($getcrossrefs as $gc){
                  if($i == 0){
                    ?>
                    <div class="row">
                      <div class="col-lg-6">
                        <label for="">Item</label>
                      <input type="text" name="cross_ref[]" class="form-control" value="<?=$gc->cross_ref?>">
                      </div>
                      <div class="col-lg-3 mt-4">
                        <button type="button" class="btn btn-primary btn-sm btn_add_extra_ref">
                          <i class="fas fa-plus"></i>
                        </button>
                      </div>
                    </div><!--row ended  -->
                    <?php
                  }else{
                    ?>
                    <div class="extra_div_cross">
                    <hr />
                    <div class="row">
                      <div class="col-lg-6">
                      <input type="text" name="cross_ref[]" class="form-control" value="<?=$gc->cross_ref?>">
                      </div>
                      <div class="col-lg-3">
                        <button type="button" class="btn btn-primary btn-sm btn_add_extra_ref">
                          <i class="fas fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-primary btn-sm btn_remove_extra_ref">
                          <i class="fas fa-minus"></i>
                        </button>
                      </div>
                    </div><!--row ended  -->
                    </div>
                    <?php
                  }//-----any other record ended -------
                  $i++;
                }//------for loop ended -----
              }else{
                //-----no records -----
                ?>
                <div class="row">
                  <div class="col-lg-6">
                    <label for="">Item</label>
                    <input type="text" name="cross_ref[]" class="form-control">
                  </div>
                  <div class="col-lg-3 mt-4">
                    <button type="button" class="btn btn-primary btn-sm btn_add_extra_ref">
                      <i class="fas fa-plus"></i>
                    </button>
                  </div>
                </div><!--row ended  -->
                <?php
              }//----else of no records ended
            }else{
              //-------new record --------
              ?>
              <div class="row">
                <div class="col-lg-6">
                  <label for="">Item</label>
                  <input type="text" name="cross_ref[]" class="form-control">
                </div>
                <div class="col-lg-3 mt-4">
                  <button type="button" class="btn btn-primary btn-sm btn_add_extra_ref">
                    <i class="fas fa-plus"></i>
                  </button>
                </div>
              </div><!--row ended  -->
              <?php
            }
           ?>

          <div id="extra_cross_ref">

          </div>

        </div>
      </div>
  </div>
  <div class="col-lg-6">
    <div class="card shadow rounded mt-2">
      <div class="card-header p-3 border-bottom border-300 bg-danger-subtle">
       <div class="row g-3 justify-content-between align-items-center">
         <div class="col-12 col-md">
           <h4 class="text-900 mb-0" data-anchor="data-anchor" id="basic-details">Alternate Products</h4>
         </div>
       </div>
     </div>
        <div class="card-body p-3">
          <?php
            if(!$model->isNewRecord){
              //----update time -------------
              $getcrossrefs = TblProductAlternate::find()->where(['status'=>1,'fk_product_id'=>$model->id])->all();
              if(isset($getcrossrefs) && count($getcrossrefs) > 0){
                $i = 0;
                foreach($getcrossrefs as $gc){
                  if($i == 0){
                    ?>
                    <div class="row">
                      <div class="col-lg-6">
                        <label for="">Item</label>
                        <select class="form-control product-select" name="prod_alt[]">
                          <option value="">Select</option>
                          <?php
                            foreach($allprods as $p){
                              if($p['id'] == $gc->fk_alt_product_id){
                                  echo '<option value="'.$p['id'].'" selected="selected">'.$p['name'].' ['.$p['sku'].']</option>';
                              }else{
                                  echo '<option value="'.$p['id'].'">'.$p['name'].' ['.$p['sku'].']</option>';
                              }
                            }
                           ?>
                        </select>
                      </div>
                      <div class="col-lg-3 mt-4">
                        <button type="button" class="btn btn-primary btn-sm btn_add_extra_alt">
                          <i class="fas fa-plus"></i>
                        </button>
                      </div>
                    </div><!--row ended  -->
                    <?php
                  }else{
                    ?>
                    <div class="extra_div_alt">
                    <hr />
                    <div class="row">
                      <div class="col-lg-6">
                        <select class="form-control product-select" name="prod_alt[]">
                          <option value="">Select</option>
                          <?php
                            foreach($allprods as $p){
                              if($p['id'] == $gc->fk_alt_product_id){
                                  echo '<option value="'.$p['id'].'" selected="selected">'.$p['name'].' ['.$p['sku'].']</option>';
                              }else{
                                  echo '<option value="'.$p['id'].'">'.$p['name'].' ['.$p['sku'].']</option>';
                              }
                            }
                           ?>
                        </select>
                      </div>
                      <div class="col-lg-3">
                        <button type="button" class="btn btn-primary btn-sm btn_add_extra_alt">
                          <i class="fas fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-primary btn-sm btn_remove_extra_alt">
                          <i class="fas fa-minus"></i>
                        </button>
                      </div>
                    </div><!--row ended  -->
                    </div>
                    <?php
                  }//-----any other record ended -------
                  $i++;
                }//------for loop ended -----
              }else{
                //-----no records -----
                ?>
                <div class="row">
                  <div class="col-lg-6">
                    <label for="">Item</label>
                    <select class="form-control product-select" name="prod_alt[]">
                      <option value="">Select</option>
                      <?php
                        foreach($allprods as $p){
                          echo '<option value="'.$p['id'].'">'.$p['name'].' ['.$p['sku'].']</option>';
                        }
                       ?>
                    </select>
                  </div>
                  <div class="col-lg-3 mt-4">
                    <button type="button" class="btn btn-primary btn-sm btn_add_extra_alt">
                      <i class="fas fa-plus"></i>
                    </button>
                  </div>
                </div><!--row ended  -->
                <?php
              }//----else of no records ended
            }else{
              //-------new record --------
              ?>
              <div class="row">
                <div class="col-lg-6">
                  <label for="">Item</label>
                  <select class="form-control product-select" name="prod_alt[]">
                    <option value="">Select</option>
                    <?php
                      foreach($allprods as $p){
                        echo '<option value="'.$p['id'].'">'.$p['name'].' ['.$p['sku'].']</option>';
                      }
                     ?>
                  </select>
                </div>
                <div class="col-lg-3 mt-4">
                  <button type="button" class="btn btn-primary btn-sm btn_add_extra_alt">
                    <i class="fas fa-plus"></i>
                  </button>
                </div>
              </div><!--row ended  -->
              <?php
            }
           ?>

          <div id="extra_alt">

          </div>

        </div>
      </div>
  </div>
</div>




<div class="row">
  <div class="col-lg-6">
    <div class="card shadow rounded mt-2">
      <div class="card-header p-3 border-bottom border-300 bg-info-subtle">
       <div class="row g-3 justify-content-between align-items-center">
         <div class="col-12 col-md">
           <h4 class="text-900 mb-0" data-anchor="data-anchor" id="basic-details">Related Products</h4>
         </div>
       </div>
     </div>
        <div class="card-body p-3">
          <?php
            if(!$model->isNewRecord){
              //----update time -------------
              $getrelated = TblProductRelated::find()->where(['status'=>1,'fk_product_id'=>$model->id])->all();
              if(isset($getrelated) && count($getrelated) > 0){
                $i = 0;
                foreach($getrelated as $gc){
                  if($i == 0){
                    ?>
                    <div class="row">
                      <div class="col-lg-6">
                        <label for="">Item</label>
                        <select class="form-control product-select" name="prod_related[]">
                          <option value="">Select</option>
                          <?php
                            foreach($allprods as $p){
                              if($p['id'] == $gc->fk_related_product_id){
                                  echo '<option value="'.$p['id'].'" selected="selected">'.$p['name'].' ['.$p['sku'].']</option>';
                              }else{
                                  echo '<option value="'.$p['id'].'">'.$p['name'].' ['.$p['sku'].']</option>';
                              }
                            }
                           ?>
                        </select>
                      </div>
                      <div class="col-lg-3 mt-4">
                        <button type="button" class="btn btn-primary btn-sm btn_add_extra_related">
                          <i class="fas fa-plus"></i>
                        </button>
                      </div>
                    </div><!--row ended  -->
                    <?php
                  }else{
                    ?>
                    <div class="extra_div_related">
                    <hr />
                    <div class="row">
                      <div class="col-lg-6">
                        <select class="form-control product-select" name="prod_related[]">
                          <option value="">Select</option>
                          <?php
                            foreach($allprods as $p){
                              if($p['id'] == $gc->fk_related_product_id){
                                  echo '<option value="'.$p['id'].'" selected="selected">'.$p['name'].' ['.$p['sku'].']</option>';
                              }else{
                                  echo '<option value="'.$p['id'].'">'.$p['name'].' ['.$p['sku'].']</option>';
                              }
                            }
                           ?>
                        </select>
                      </div>
                      <div class="col-lg-3">
                        <button type="button" class="btn btn-primary btn-sm btn_add_extra_related">
                          <i class="fas fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-primary btn-sm btn_remove_extra_related">
                          <i class="fas fa-minus"></i>
                        </button>
                      </div>
                    </div><!--row ended  -->
                    </div>
                    <?php
                  }//-----any other record ended -------
                  $i++;
                }//------for loop ended -----
              }else{
                //-----no records -----
                ?>
                <div class="row">
                  <div class="col-lg-6">
                    <label for="">Item</label>
                    <select class="form-control product-select" name="prod_related[]">
                      <option value="">Select</option>
                      <?php
                        foreach($allprods as $p){
                          echo '<option value="'.$p['id'].'">'.$p['name'].' ['.$p['sku'].']</option>';
                        }
                       ?>
                    </select>
                  </div>
                  <div class="col-lg-3 mt-4">
                    <button type="button" class="btn btn-primary btn-sm btn_add_extra_related">
                      <i class="fas fa-plus"></i>
                    </button>
                  </div>
                </div><!--row ended  -->
                <?php
              }//----else of no records ended
            }else{
              //-------new record --------
              ?>
              <div class="row">
                <div class="col-lg-6">
                  <label for="">Item</label>
                  <select class="form-control product-select" name="prod_related[]">
                    <option value="">Select</option>
                    <?php
                      foreach($allprods as $p){
                        echo '<option value="'.$p['id'].'">'.$p['name'].' ['.$p['sku'].']</option>';
                      }
                     ?>
                  </select>
                </div>
                <div class="col-lg-3 mt-4">
                  <button type="button" class="btn btn-primary btn-sm btn_add_extra_related">
                    <i class="fas fa-plus"></i>
                  </button>
                </div>
              </div><!--row ended  -->
              <?php
            }
           ?>

          <div id="extra_related">

          </div>

        </div>
      </div>
  </div>
  <div class="col-lg-6">
    <div class="card shadow rounded mt-2">
      <div class="card-header p-3 border-bottom border-300 bg-info-subtle">
       <div class="row g-3 justify-content-between align-items-center">
         <div class="col-12 col-md">
           <h4 class="text-900 mb-0" data-anchor="data-anchor" id="competitor-details">Competitor Pricing</h4>
         </div>
       </div>
     </div>
        <div class="card-body p-3">
          <?php
            if(!$model->isNewRecord){
              //----update time -------------
              $getproductcompt = TblProductCompetition::find()->where(['status'=>1,'fk_product_id'=>$model->id])->all();
              if(isset($getproductcompt) && count($getproductcompt) > 0){
                $i = 0;
                foreach($getproductcompt as $gc){
                  if($i == 0){
                    ?>
                    <div class="row">
                      <div class="col-lg-5">
                        <label for="">Seller Name</label>
                      <input type="text" name="seller_name[]" class="form-control" value="<?=$gc->seller_name?>">
                      </div>
                      <div class="col-lg-4">
                        <label for="">Seller Pricing</label>
                      <input type="text" name="seller_price[]" class="form-control floatNumberField" value="<?=$gc->selling_price?>" onkeyup="checkDec(this);">
                      </div>
                      <div class="col-lg-3 mt-4">
                        <button type="button" class="btn btn-primary btn-sm btn_add_extra_seller">
                          <i class="fas fa-plus"></i>
                        </button>
                      </div>
                    </div><!--row ended  -->
                    <?php
                  }else{
                    ?>
                    <div class="extra_div_seller">
                    <hr />
                    <div class="row">
                      <div class="col-lg-5">
                      <input type="text" name="seller_name[]" class="form-control" value="<?=$gc->seller_name?>">
                      </div>
                      <div class="col-lg-4">
                      <input type="text" name="seller_price[]" class="form-control floatNumberField" value="<?=$gc->selling_price?>" onkeyup="checkDec(this);">
                      </div>
                      <div class="col-lg-3">
                        <button type="button" class="btn btn-primary btn-sm btn_add_extra_seller">
                          <i class="fas fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-primary btn-sm btn_remove_extra_seller">
                          <i class="fas fa-minus"></i>
                        </button>
                      </div>
                    </div><!--row ended  -->
                    </div>
                    <?php
                  }//-----any other record ended -------
                  $i++;
                }//------for loop ended -----
              }else{
                //-----no records -----
                ?>
                <div class="row">
                  <div class="col-lg-5">
                    <label for="">Seller Name</label>
                    <input type="text" name="seller_name[]" class="form-control">
                  </div>
                  <div class="col-lg-4">
                    <label for="">Seller Pricing</label>
                    <input type="text" name="seller_price[]" class="form-control floatNumberField" onkeyup="checkDec(this);">
                  </div>
                  <div class="col-lg-3 mt-4">
                    <button type="button" class="btn btn-primary btn-sm btn_add_extra_seller">
                      <i class="fas fa-plus"></i>
                    </button>
                  </div>
                </div><!--row ended  -->
                <?php
              }//----else of no records ended
            }else{
              //-------new record --------
              ?>
              <div class="row">
                <div class="col-lg-5">
                  <label for="">Seller Name</label>
                  <input type="text" name="seller_name[]" class="form-control">
                </div>
                <div class="col-lg-4">
                  <label for="">Seller Pricing</label>
                  <input type="text" name="seller_price[]" class="form-control floatNumberField" onkeyup="checkDec(this);">
                </div>
                <div class="col-lg-3 mt-4">
                  <button type="button" class="btn btn-primary btn-sm btn_add_extra_seller">
                    <i class="fas fa-plus"></i>
                  </button>
                </div>
              </div><!--row ended  -->
              <?php
            }
           ?>

          <div id="extra_seller_comp">

          </div>

        </div>
      </div>
  </div><!-- col-6 ended -->
</div><!-- row ended -->








      </div><!-- basic details tab ended -->
      <div class="tab-pane fade" id="tabimages" role="tabpanel">
        <div class="card shadow rounded mt-2">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-lg-4">
                  <label for="">Upload Images</label>
                  <input type="file" name="file_url[]" accept="image/*" class="form-control" multiple="multiple">
                </div>
              </div>
              <?php
                if(!$model->isNewRecord){
                  //----update time =----
                  $getimgs = TblProductImage::find()->where(['status'=>1,'fk_product_id'=>$model->id])->all();
                  if(isset($getimgs) && count($getimgs) > 0){
                    echo '<table class="table table-bordered mt-3">
                      <thead>
                        <tr>
                          <th>Sr. No.</th>
                          <th>Image</th>
                          <th>Act.</th>
                        </tr>
                      </thead>
                      <tbody>';
                      $j = 1;
                      foreach($getimgs as $gi){
                        echo '<tr>
                          <td>'.$j.'</td>
                          <td><a href="product-images/'.$gi->url.'" target="_blank"><img src="product-images/'.$gi->url.'" width="150px"/></a></td>
                          <td><input type="hidden" name="image_ids[]" value="'.$gi->id.'"><a  class="delete-button text-danger" data-id="'.$gi->id.'" style="cursor:pointer;"><i class="fas fa-trash"></i></a></td>
                        </tr>';
                        $j++;
                      }
                      echo '
                      </tbody>
                    </table>';
                  }
                }
               ?>
            </div><!--card body ended  -->
          </div><!-- card ended -->
      </div><!-- images tab ended -->
      <div class="tab-pane fade" id="tabitemqty" role="tabpanel">
        <div class="card shadow rounded mt-2">
            <div class="card-body p-3">
              <?php
                if(!$model->isNewRecord){
                  ?>
                  <p align="right">
                    <?= Html::a('Create Item Receiving', ['product-receiving/create','truck_id'=>$model->id], ['target' => '_blank','class' => 'btn btn-primary btn-sm']) ?>
                  </p>
                  <?php


                  ?>
                  <div class="table-responsive">
                <table class="table table-bordered table-sm">
                  <thead class="table-dark">
                    <tr>
                      <th>Date</th>
                      <th>Received By</th>
                      <th>Vendor Invoice</th>
                      <th>Vendor</th>
                      <th>No. of Items</th>
                      <th>Price per item</th>
                      <th>Bin #</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      if(!$model->isNewRecord){
                        $getproductsreceived = TblProductReceiving::find()->where(['status'=>1,'fk_product_id'=>$model->id])->orderBy(['crt_time'=>SORT_DESC])->all();
                        if(isset($getproductsreceived) && count($getproductsreceived) > 0){
                          foreach($getproductsreceived as $rs){
                            //-----prep by ----------
                            $getprepby = TblUser::find()->where(['id'=>$rs->crt_by])->one();
                            if(isset($getprepby) && $getprepby->alias != ""){
                              $prep_by = $getprepby->alias;
                            }else{
                              $prep_by = "(not set)";
                            }
                            $getveninv = TblVendorInvoice::find()->where(['id'=>$rs->fk_vendor_invoice_id,'status'=>1])->one();
                            if(isset($getveninv) && $getveninv->vendor_invoice_number != ""){
                              $vendor_invoice = $getveninv->vendor_invoice_number;
                              //=====get vendor ===================
                              $getvendor = TblVendor::find()->where(['id'=>$getveninv->fk_vendor_id,'status'=>1])->one();
                              if(isset($getvendor) && $getvendor->company_name != ""){
                                $vendor = $getvendor->company_name;
                              }else{
                                $vendor = "(not set)";
                              }
                            }else if($rs->fk_vendor_id != ""){
                              $getvendor = TblVendor::find()->where(['id'=>$rs->fk_vendor_id,'status'=>1])->one();
                              if(isset($getvendor) && $getvendor->company_name != ""){
                                $vendor = $getvendor->company_name;
                              }else{
                                $vendor = "(not set)";
                              }
                              $vendor_invoice = "(not set)";
                            }else{
                              $vendor_invoice = "(not set)";
                              $vendor = "(not set)";
                            }
                            $getbin = TblBinLocations::find()->where(['id'=>$rs->fk_bin_id,'status'=>1])->one();
                            if(isset($getbin) && $getbin->id != ""){
                              //-------get area ------

                              $bin = $getbin->area.' - '.$getbin->row.' - '.$getbin->bay.' - '.$getbin->level.' - '.$getbin->position;
                            }else{
                              $bin = "(not set)";
                            }
                            echo '<tr>
                            <td>'.date('d M, Y',strtotime($rs->crt_time)).'</td>
                            <td>'.$prep_by.'</td>
                            <td>'.$vendor_invoice.'</td>
                            <td>'.$vendor.'</td>
                            <td>'.$rs->no_of_items.'</td>
                            <td>'.$rs->price_per_item.'</td>
                            <td>'.$bin.'</td>
                            </tr>';
                          }
                        }else{
                          echo "<tr>
                          <td colspan='6'>No records yet.</td>
                          </tr>";
                        }
                      }else{
                        echo "<tr>
                        <td colspan='6'>No records yet.</td>
                        </tr>";
                      }

                     ?>
                  </tbody>
                </table>
              </div>
                  <?php
                }//----update time------
               ?>
            </div>
          </div>
      </div>

    </div><!-- tab content ended  -->

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

<!-- product name modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="exampleModalLabel">Items with same or similar name</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="product_modal_div">

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?php
$product_str = '';
foreach($allprods as $p){

  // $product_str .= "<option value='".$p['id']."'>".$p['name']." [".$p['sku']."]</option>";
  $productId = htmlspecialchars($p['id'], ENT_QUOTES); // Escape ID
   $productName = htmlspecialchars($p['name'], ENT_QUOTES); // Escape name
   $productSKU = htmlspecialchars(str_replace(array("\r", "\n"), ' ', $p['sku']), ENT_QUOTES);
  $product_str .= "<option value='" . $productId . "'>" . $productName . "[".$productSKU."]</option>";
}
  $this->registerJs('
  // Initialize Choices.js on all existing .product-select elements
  $(".product-select").each(function() {
      new Choices(this, {
       allowHTML: true
   });  // Initialize Choices.js on the current element
  });

  new Choices("#tblproduct-fk_cycle_count_category_id", {
      allowHTML: true,  // Enable HTML rendering inside options
  });

  new Choices("#tblproduct-status", {
      allowHTML: true,  // Enable HTML rendering inside options
  });


  new Choices("#brands", {
      allowHTML: true,  // Enable HTML rendering inside options
  });
  new Choices("#categories",{
    allowHTML: true,
    shouldSort: false
  })


//=================cross ref products ===============================
    $(document).on("click",".btn_add_extra_ref",function(e){
      e.preventDefault();
      let content = "<div class=\"extra_div_cross\"><hr /><div class=\"row\"><div class=\"col-lg-6\"><input type=\"text\" name=\"cross_ref[]\" class=\"form-control\"></div><div class=\"col-lg-3\"><button type=\"button\" class=\"btn btn-primary btn-sm btn_add_extra_ref\"><i class=\"fas fa-plus\"></i></button>&nbsp;<button type=\"button\" class=\"btn btn-primary btn-sm btn_remove_extra_ref\"><i class=\"fas fa-minus\"></i></button></div></div></div>";
      $("#extra_cross_ref").append(content);
    });
    $(document).on("click",".btn_remove_extra_ref",function(e){
      e.preventDefault();
      $(this).parent().parent().parent(".extra_div_cross").remove();
    });
//=============alternate products ===============================
    $(document).on("click",".btn_add_extra_alt",function(e){
      e.preventDefault();
      let content = "<div class=\"extra_div_alt\"><hr /><div class=\"row\"><div class=\"col-lg-6\"><select class=\"form-control product-select\" name=\"prod_alt[]\"><option value=\"\">Select</option>'.$product_str.'</select></div><div class=\"col-lg-3\"><button type=\"button\" class=\"btn btn-primary btn-sm btn_add_extra_alt\"><i class=\"fas fa-plus\"></i></button>&nbsp;<button type=\"button\" class=\"btn btn-primary btn-sm btn_remove_extra_alt\"><i class=\"fas fa-minus\"></i></button></div></div></div>";
      $("#extra_alt").append(content);
      // Initialize Choices.js on the newly added select element, if not already initialized
       let newSelect = $("#extra_alt").find(".product-select").last();

       // Check if the select element is already initialized with Choices.js
       if (!newSelect[0].hasAttribute(\'data-choice\')) {
           new Choices(newSelect[0], {
            allowHTML: true
        });  // Initialize Choices on the last appended select if not already initialized
       }
    });
    $(document).on("click",".btn_remove_extra_alt",function(e){
      e.preventDefault();
      $(this).parent().parent().parent(".extra_div_alt").remove();
    });

//=====================related products =============================
    $(document).on("click",".btn_add_extra_related",function(e){
      e.preventDefault();
      let content = "<div class=\"extra_div_related\"><hr /><div class=\"row\"><div class=\"col-lg-6\"><select class=\"form-control product-select\" name=\"prod_related[]\"><option value=\"\">Select</option>'.$product_str.'</select></div><div class=\"col-lg-3\"><button type=\"button\" class=\"btn btn-primary btn-sm btn_add_extra_related\"><i class=\"fas fa-plus\"></i></button>&nbsp;<button type=\"button\" class=\"btn btn-primary btn-sm btn_remove_extra_related\"><i class=\"fas fa-minus\"></i></button></div></div></div>";
      $("#extra_related").append(content);

        // Initialize Choices.js on the newly added select element, if not already initialized
         let newSelect = $("#extra_related").find(".product-select").last();

         // Check if the select element is already initialized with Choices.js
         if (!newSelect[0].hasAttribute(\'data-choice\')) {
             new Choices(newSelect[0], {
            allowHTML: true
        });  // Initialize Choices on the last appended select if not already initialized
         }

    });
    $(document).on("click",".btn_remove_extra_related",function(e){
      e.preventDefault();
      $(this).parent().parent().parent(".extra_div_related").remove();
    });
//====================seller competitors ===================
  $(document).on("click",".btn_add_extra_seller",function(e){
    e.preventDefault();
    let content = "<div class=\"extra_div_seller\"><hr /><div class=\"row\"><div class=\"col-lg-5\"><input type=\"text\" name=\"seller_name[]\" class=\"form-control\"></div><div class=\"col-lg-4\"><input type=\"text\" name=\"seller_price[]\" class=\"form-control floatNumberField\" onkeyup=\"checkDec(this);\"></div><div class=\"col-lg-3\"><button type=\"button\" class=\"btn btn-primary btn-sm btn_add_extra_seller\"><i class=\"fas fa-plus\"></i></button>&nbsp;<button type=\"button\" class=\"btn btn-primary btn-sm btn_remove_extra_seller\"><i class=\"fas fa-minus\"></i></button></div></div></div>";
      $("#extra_seller_comp").append(content);
  });
  $(document).on("click",".btn_remove_extra_seller",function(e){
    e.preventDefault();
    $(this).parent().parent().parent(".extra_div_seller").remove();
  });

  //--------------check for product name ----------
$("#tblproduct-name").blur(function(){
  let productName = $("#tblproduct-name").val();
  if(productName != ""){
    // Get the CSRF token from the meta tag
  const csrfToken = document.querySelector(\'meta[name="csrf-token"]\').getAttribute(\'content\');
    fetch(\'index.php?r=product/checkpartname\', {
        method: \'POST\',
        headers: {
         \'Content-Type\': \'application/json\',
         \'X-CSRF-Token\': csrfToken // Add CSRF token in the headers
       },
        body: JSON.stringify({name: productName})
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);
        let obj = JSON.parse(data);
        console.log(obj.return_status);
        $("#product_modal_div").html("");
        if(obj.return_status >= 1){
          // Handle the response
          let data_exists = 0;
          if(obj.product != ""){
            //------product with same name --------
            $("#product_modal_div").append(obj.product);
            data_exists++;
          }

          if(obj.similar != ""){
            //-------similar product names ------------
            if(data_exists >= 1){
                $("#product_modal_div").append("<br />"+obj.similar);
            }else{
                $("#product_modal_div").append(obj.similar);
            }
            data_exists++;
          }
          if(obj.crossref != ""){
            //-----product with same cross ref --------
            if(data_exists >= 1){
                $("#product_modal_div").append("<br />"+obj.crossref);
            }else{
                $("#product_modal_div").append(obj.crossref);
            }

          }
          var productModal = new bootstrap.Modal(document.getElementById(\'productModal\'), {});
          productModal.show();
        }//---------return status is more than one ---------

    })
    .catch(error => {
        console.error(\'Error:\', error);
    });
  }//-----if of checking product name not empty ended

});
// Handle delete button click
$(".delete-button").click(function() {
  var button = $(this);  // Reference to the button clicked
  var imageId = button.data(\'id\');

  // Show a confirmation dialog
  if (confirm("Are you sure you want to delete this image?")) {

    // Optionally, send an AJAX request to delete the image from the server
    $.post(\'index.php?r=product/delpics\', { del_id: imageId }, function(response) {
      console.log(\'Image deleted:\', response);
      if(response=="yes"){
        button.closest(\'tr\').remove(); // Remove the image from the list
      }else{
        alert("Something went wrong. Please try again.");
      }
    });
  }

});


  ');
 ?>
