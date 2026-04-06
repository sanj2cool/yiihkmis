<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\TblBrand $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Brands', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>74,'status'=>1])->one();

?>
<div class="tbl-brand-view">
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
        <?= Yii::$app -> session -> getFlash('error'); ?>

      <?php
    }
    ?>
  </div>

    <div class="card shadow rounded mt-2">
        <div class="card-body p-3">
        <div class="row">
          <div class="col-lg-6">
            <h3><?= Html::encode($this->title) ?></h3>
          </div>
          <div class="col-lg-6 text-end">
            <?php
            if(isset($menuaccess) && $menuaccess->edit_crud == 1){
             ?>
            <?= Html::a('<i class="fas fa-pen"></i> Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']) ?>
            <?php
              }
             ?>
             <?php
               if(isset($menuaccess) && $menuaccess->delete_crud == 1){
                 ?>
                 <?= Html::a('<i class="fas fa-trash"></i> Delete', ['delete', 'id' => $model->id], [
                   'class' => 'btn btn-danger btn-sm',
                   'data' => [
                     'confirm' => 'Are you sure you want to delete this item?',
                     'method' => 'post',
                   ],
                   ]) ?>
                 <?php
               }
              ?>
                <?= Html::a('<i class="fas fa-chevron-left"></i> Back to List', ['index'], ['class' => 'btn btn-secondary btn-sm']) ?>
          </div>
        </div>
      </div>
      </div>

      <div class="card shadow rounded mt-2">
          <div class="card-body p-3">
            <div class="row">
              <div class="col-lg-4 mb-2">
                <label class="fw-bold">Title</label><br />
                    <?=$model->title?>
              </div>
              <div class="col-lg-4 mb-2">
                <label class="fw-bold">Description</label><br />
                    <?=$model->description?>
              </div>
              <div class="col-lg-4 mb-2">
                <label class="fw-bold">Image</label><br />
                <?php
                  if($model->image_url != ""){
                    $url = Yii::getAlias('@web/brand-images/' . $model->image_url);
                    echo '  <a href="#" class="open-image" data-bs-toggle="modal" data-bs-target="#imageModal" data-img="'.$url.'">
                      <img src="brand-images/'.$model->image_url.'" width="100px"/>
                      </a>';
                  }
                 ?>
              </div>
              <!-- <div class="col-lg-3 mb-2">
                <label class="fw-bold">Status</label><br />
                <?php
                    if($model->status == 1){
                      echo '<span class="badge bg-success">Active</span>';
                    }else if($model->status == 2){
                      echo '<span class="badge bg-danger">Inactive</span>';
                    }else{
                      echo '(not set)';
                    }
                 ?>

              </div> -->
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
