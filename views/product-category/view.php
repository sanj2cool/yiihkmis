<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\TblProductCategory;
/** @var yii\web\View $this */
/** @var app\models\TblProductCategory $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Product Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>23,'status'=>1])->one();

?>
<div class="tbl-product-category-view">
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

        <?= DetailView::widget([
          'model' => $model,
          'attributes' => [
            'id',
            'title',
            'description:ntext',
            // 'parent_id',
            [
              'attribute' => 'parent_id',
              'format' => 'raw',
              'value' => function($model){
                $getcat = TblProductCategory::find()->where(['id'=>$model->parent_id,'status'=>1])->one();
                if(isset($getcat) && $getcat->id != ""){
                  return $getcat->title;
                }else{
                  return '<i>(not set)</i>';
                }
              },
            ],
            [
              'label' => 'Image',
              'attribute' => 'image_url',
              'format' => 'raw',
              'value' => function ($model) {
                if($model->image_url != ""){
                  $url = Yii::getAlias('@web/product-cat-images/' . $model->image_url);
                  return Html::a(
                    Html::img($url, [
                      'width' => '80',
                      'style' => 'cursor:pointer;border-radius:6px;',
                      'class' => 'thumb-img'
                    ]),
                    '#',
                    [
                      'class' => 'open-image',
                      'data-bs-toggle' => 'modal',
                      'data-bs-target' => '#imageModal',
                      'data-img' => $url,
                    ]
                  );
                }else{
                  return '';
                }
              },
            ],
            // [
            //   'attribute' => 'assemblycat',
            //   'label' => 'Assembly Category',
            //   'format' => 'raw',
            //   'value' => function($model){
            //     if($model->assemblycat){
            //       return $model->assemblycat->title;
            //     }else{
            //       return '<i>(not set)</i>';
            //     }
            //   }
            // ]
            // 'ip',
            // 'status',
            // 'crt_by',
            // 'mod_by',
            // 'crt_time',
            // 'mod_time',
          ],
          ]) ?>
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
