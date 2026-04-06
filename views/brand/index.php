<?php

use app\models\TblBrand;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\TblBrandSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Brands';
$this->params['breadcrumbs'][] = $this->title;
$template = '{view}';
$statusarr = [1=>"Active",2=>"Inactive"];

use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>74,'status'=>1])->one();

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
        <?php
        if(isset($menuaccess) && $menuaccess->create_crud == 1){
         ?>
        <?= Html::a('Create Brand', ['create'], ['class' => 'btn btn-primary']) ?>
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
<div class="tbl-brand-index table-responsive">



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
                  return Html::a('<i class=" fas fa-eye"></i>', $url, [
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
                  $url ='index.php?r=brand/update&id='.$model['id'];
                  return $url;
                }
                if ($action === 'view') {
                  $url ='index.php?r=brand/view&id='.$model['id'];
                  return $url;
                }
                if ($action === 'delete') {
                  $url ='index.php?r=brand/delete&id='.$model['id'];
                  return $url;
                }
              }
            ],
            // 'id',
            'title',
            'description',
            // 'ip',
            // 'status',
            [
              'attribute' => 'show_on_website',
              'label' => 'Show on Website',
              'format' => 'raw',
              'value' => function($dataProvider){
                if($dataProvider->show_on_website == 1){
                  return "Yes";
                }else if($dataProvider->show_on_website == 2){
                  return "No";
                }else{
                  return '<i>(not set)</i>';
                }
              },
              'filter' => Html::activeDropDownList($searchModel, 'show_on_website', [1=>"Yes",2=>"No"],['class'=>'form-control','prompt' => 'Select', 'encode' => false]),
            ],
            // [
            //     'attribute' => 'image_url',
            //     'format' => 'html',
            //     'label' => 'Image',
            //     'value' => function ($model) {
            //       if($model->image_url != ""){
            //         return Html::img('brand-images/'.$model->image_url, [
            //             'width' => '80',        // adjust size
            //             'height' => '80',
            //             'style' => 'object-fit:cover;border-radius:6px;'
            //         ]);
            //       }else{
            //         return '';
            //       }
            //
            //     },
            // ],
            [
              'label' => 'Image',
              // 'attribute' => 'image_url',
              'attribute' => 'has_image',
              'filter' => Html::activeDropDownList(
                 $searchModel,
                 'has_image',
                 [
                     '1' => 'Images Exist',
                     '0' => 'No Image'
                 ],
                 ['prompt' => 'Select','class'=>'form-select'] // <-- this adds the prompt
             ),
              'format' => 'raw',
              'value' => function ($model) {
                  if($model->image_url != ""){
                    $url = Yii::getAlias('@web/brand-images/' . $model->image_url);
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
            //   'attribute' => 'status',
            //   'format' => 'raw',
            //   'value' => function($dataProvider){
            //     if($dataProvider->status == 1){
            //       return '<span class="badge bg-success">Active</span>';
            //     }else if($dataProvider->status == 2){
            //       return '<span class="badge bg-danger">Inactive</span>';
            //     }else{
            //       return '(not set)';
            //     }
            //
            //   },
            //   'filter' => Html::activeDropDownList($searchModel, 'status', $statusarr,['class'=>'form-control','prompt' => 'Select', 'encode' => false]),
            // ]
            //'crt_by',
            //'mod_by',
            //'crt_time',
            //'mod_time',
            // [
            //     'class' => ActionColumn::className(),
            //     'urlCreator' => function ($action, TblBrand $model, $key, $index, $column) {
            //         return Url::toRoute([$action, 'id' => $model->id]);
            //      }
            // ],
        ],
    ]); ?>


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
