<?php

use app\models\TblSlider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\TblSliderSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Website Sliders';
$this->params['breadcrumbs'][] = $this->title;
$template = '';
use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>73,'status'=>1])->one();
if(isset($menuaccess) && $menuaccess->edit_crud == 1){
  $template .= ' {edit}';
}
if(isset($menuaccess) && $menuaccess->delete_crud == 1){
  $template .= ' {delete}';
}
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
        <?php
        if(isset($menuaccess) && $menuaccess->create_crud == 1){
         ?>
        <?= Html::a('Create New', ['create'], ['class' => 'btn btn-primary']) ?>
        <?php
          }
         ?>
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
<div class="tbl-slider-index">



    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            [
              'class' => ActionColumn::className(),
              'template' => $template,
              'buttons' => [
                'edit' => function ($url, $model) {
                  return Html::a('<i class=" fas fa-pencil-alt"></i>', $url, [
                    'title' => Yii::t('app', 'edit'),
                    'class' => 'text-primary'
                  ]);
                },
                'view' => function ($url, $model) {
                  return Html::a('<i class=" fas fa-file"></i>', $url, [
                    'title' => Yii::t('app', 'view'),
                    'class' => 'text-info'
                  ]);
                },
                'delete' => function ($url, $model, $key) {
                  $options = [
                    'title' => Yii::t('yii', 'Delete'),
                    'aria-label' => Yii::t('yii', 'Delete'),
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                    'class' => 'text-danger'
                  ];
                  return Html::a('<i class="fas fa-trash"></i>', $url, $options);
                }
              ],
              'urlCreator' => function ($action, $model, $key, $index) {
                if ($action === 'edit') {
                  $url ='index.php?r=slider/update&id='.$model->id;
                  return $url;
                }

                if ($action === 'delete') {
                  $url ='index.php?r=slider/delete&id='.$model->id;
                  return $url;
                }
              }
            ],
            // 'id',
            // 'url:url',
            [
              'attribute' => 'url',
              'format' => 'raw',
              'value' => function($dataProvider){
                return '<img src="slider-images/'.$dataProvider->url.'" width="200px"/>';
              }
            ],
            'sequence',
            // 'status_val',
            [
              'attribute' => 'status_val',
              'format' => 'raw',
              'value' => function($dataProvider){
                if($dataProvider->status_val == 1){
                  return '<span class="badge bg-success">Active</span>';
                }else if($dataProvider->status_val == 2){
                  return '<span class="badge bg-danger">Inactive</span>';
                }else{
                  return '<i>(not set)</i>';
                }
              },
              'filter' => Html::activeDropDownList($searchModel, 'status_val', [1=>"Active",2=>"Inactive"],['class'=>'form-control','prompt' => 'Select', 'encode' => false]),
            ]
            // 'ip',
            //'status',
            //'crt_by',
            //'mod_by',
            //'crt_time',
            //'mod_time',
            // [
            //     'class' => ActionColumn::className(),
            //     'urlCreator' => function ($action, TblSlider $model, $key, $index, $column) {
            //         return Url::toRoute([$action, 'id' => $model->id]);
            //      }
            // ],
        ],
    ]); ?>


  </div>
</div>
</div>
