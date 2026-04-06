<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\TblProductCategory;
use app\models\TblProduct;
use app\models\TblProductCrossRef;
use app\models\TblCycleCountCategory;
use app\models\TblProductImage;
use yii\helpers\ArrayHelper;
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
      <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#tabitemqty" role="tab"><span class="hidden-sm-up"><i class="fa-solid fa-dolly"></i></span> <span class="hidden-xs-down">Item Received/Allotted</span></a> </li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane fade show active" id="home" role="tabpanel">
        <div class="card shadow rounded mt-2">
          <div class="card-header p-4 border-bottom border-300 bg-soft">
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
                </div>
                <div class="col-lg-3">
                  <?= $form->field($model, 'sku')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-lg-3">
                  <?= $form->field($model, 'fk_category_id')->dropDownList($catsarr,['prompt'=>'Select']) ?>
                </div>
                <div class="col-lg-3">
                  <?= $form->field($model, 'fk_cycle_count_category_id')->dropDownList($cyclecatarr,['prompt'=>'Select']) ?>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-lg-3">
                  <?= $form->field($model, 'quantity_in_stock')->textInput() ?>
                  <?= $form->field($model, 'status')->dropDownList([1=>"Active",2=>"Inactive"],['prompt' => 'Select']) ?>
                </div>
                <div class="col-lg-9">
                  <?= $form->field($model, 'description')->textarea(['rows' => 4]) ?>
                </div>
              </div>
            </div>
        </div>


        <div class="card shadow rounded mt-2">
          <div class="card-header p-4 border-bottom border-300 bg-soft">
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
                          <div class="col-lg-3">
                            <label for="">Item</label>
                            <select class="form-control product-select" name="cross_ref[]">
                              <option value="">Select</option>
                              <?php
                                foreach($allprods as $p){
                                  if($p['id'] == $gc->fk_cross_ref_id){
                                      echo '<option value="'.$p['id'].'" selected="selected">'.$p['name'].' ['.$p['sku'].']</option>';
                                  }else{
                                      echo '<option value="'.$p['id'].'">'.$p['name'].' ['.$p['sku'].']</option>';
                                  }
                                }
                               ?>
                            </select>
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
                          <div class="col-lg-3">
                            <select class="form-control product-select" name="cross_ref[]">
                              <option value="">Select</option>
                              <?php
                                foreach($allprods as $p){
                                  if($p['id'] == $gc->fk_cross_ref_id){
                                      echo '<option value="'.$p['id'].'" selected="selected">'.$p['name'].' ['.$p['sku'].']</option>';
                                  }else{
                                      echo '<option value="'.$p['id'].'">'.$p['name'].' ['.$p['sku'].']</option>';
                                  }
                                }
                               ?>
                            </select>
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
                      <div class="col-lg-3">
                        <label for="">Item</label>
                        <select class="form-control product-select" name="cross_ref[]">
                          <option value="">Select</option>
                          <?php
                            foreach($allprods as $p){
                              echo '<option value="'.$p['id'].'">'.$p['name'].' ['.$p['sku'].']</option>';
                            }
                           ?>
                        </select>
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
                    <div class="col-lg-3">
                      <label for="">Item</label>
                      <select class="form-control product-select" name="cross_ref[]">
                        <option value="">Select</option>
                        <?php
                          foreach($allprods as $p){
                            echo '<option value="'.$p['id'].'">'.$p['name'].' ['.$p['sku'].']</option>';
                          }
                         ?>
                      </select>
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
                      <th>
                      Sr. No.
                      </th>
                      <th>
                      Image
                      </th>
                      </tr>
                      </thead>
                      <tbody>';
                      $j = 1;
                      foreach($getimgs as $gi){
                        echo '<tr>
                        <td>
                        '.$j.'
                        </td>
                        <td>
                        <a href="product-images/'.$gi->url.'" target="_blank">
                        <img src="product-images/'.$gi->url.'" width="150px"/>
                        </a>
                        </td>
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
<?php
$product_str = '';
foreach($allprods as $p){
  $product_str .= "<option value='".$p['id']."'>".$p['name']." [".$p['sku']."]</option>";
}
  $this->registerJs('
    $(document).on("click",".btn_add_extra_ref",function(e){
      e.preventDefault();
      let content = "<div class=\"extra_div_cross\"><hr /><div class=\"row\"><div class=\"col-lg-3\"><select class=\"form-control product-select\" name=\"cross_ref[]\"><option value=\"\">Select</option>'.$product_str.'</select></div><div class=\"col-lg-3\"><button type=\"button\" class=\"btn btn-primary btn-sm btn_add_extra_ref\"><i class=\"fas fa-plus\"></i></button>&nbsp;<button type=\"button\" class=\"btn btn-primary btn-sm btn_remove_extra_ref\"><i class=\"fas fa-minus\"></i></button></div></div></div>";
      $("#extra_cross_ref").append(content);
    });
    $(document).on("click",".btn_remove_extra_ref",function(e){
      e.preventDefault();
      $(this).parent().parent().parent(".extra_div_cross").remove();
    });
  ');
 ?>
