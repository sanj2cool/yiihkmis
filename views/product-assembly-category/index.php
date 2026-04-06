<?php

use app\models\TblProductAssemblyCategory;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\TblProductAssemblyCategorySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Assembly Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card mb-3">
  <div class="card-header pt-3 pb-2">
    <div class="row">
      <div class="col-lg-6">
        <h3>
          <?= Html::encode($this->title) ?>
        </h3>
      </div>
      <div class="col-lg-6 text-end">
        <?= Html::a('Reset Filters', ['reset-filters'], ['class' => 'btn btn-warning btn-sm']) ?>
        <?= Html::a('Create Assembly Category', ['create'], ['class' => 'btn btn-primary btn-sm']) ?>

      </div>
    </div>
  </div>
  <div class="card-body">
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
<div class="tbl-product-assembly-category-index table-responsive">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
              'class' => ActionColumn::className(),
              'template' => '{view}',
              'buttons' => [

                'view' => function ($url, $model) {
                  return Html::a('<i class=" fas fa-eye"></i>', $url, [
                    'title' => Yii::t('app', 'view'),
                    'class' => 'text-info'
                  ]);
                }
              ],
              'urlCreator' => function ($action, $model, $key, $index) {

                if ($action === 'view') {
                  $url ='index.php?r=product-assembly-category/view&id='.$model['id'];
                  return $url;
                }

              }
            ],
            // 'id',
            'title',
            'description:ntext',
            // 'image_url:url',
            [
              'attribute' => 'image_url',
              'format' => 'html',
              'value' => function ($model) {
                if($model->image_url != ''){
                  return Html::img('assembly-category/'.$model->image_url, [
                      'width' => '80',
                      'height' => '80',
                      'style' => 'object-fit: cover; border-radius: 8px;'
                  ]);
                }else{
                  return '';
                }

              },
          ],
          [
            'attribute' => 'exploded_image_url',
            'format' => 'html',
            'value' => function ($model) {
              if($model->exploded_image_url != ''){
                return Html::img('assembly-category/'.$model->exploded_image_url, [
                    'width' => '80',
                    'height' => '80',
                    'style' => 'object-fit: cover; border-radius: 8px;'
                ]);
              }else{
                return '';
              }

            },
        ],
          // exploded_image_url
            // 'show_on_website',
            //'img_width',
            //'translate_x',
            //'translate_y',
            //'ip',
            // 'status',
            [
              'attribute' => 'status',
              'format' => 'raw',
              'value' => function($model){
                return $model->status == 1 ? '<span class="badge bg-success">Active</span>' : ($model->status == 2 ? '<span class="badge bg-danger">Inactive</span>' : '<i>(not set)</i>');
              },
              'filter' => Html::activeDropDownList(
                $searchModel,
                'status',
                [1 => 'Active', 2 => 'Inactive'],
                ['class'=>'form-select','prompt'=>'Select']
              ),
            ],
            //'crt_by',
            //'mod_by',
            //'crt_time',
            //'mod_time',
            // [
            //     'class' => ActionColumn::className(),
            //     'urlCreator' => function ($action, TblProductAssemblyCategory $model, $key, $index, $column) {
            //         return Url::toRoute([$action, 'id' => $model->id]);
            //      }
            // ],
        ],
    ]); ?>


</div>
</div>
</div>
