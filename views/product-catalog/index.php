<?php

use app\models\TblProductCatalog;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\TblProductCatalogSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Catalogs';
$this->params['breadcrumbs'][] = $this->title;

use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$template = '';
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>77,'status'=>1])->one();
if(isset($menuaccess) && $menuaccess->edit_crud == 1){
  $template .= '{activate} {deactivate}';
}
$template .= ' {view}';
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
        <?= Html::a('Reset Filters', ['reset-filters'], ['class' => 'btn btn-warning btn-sm']) ?>
        <?php
        if(isset($menuaccess) && $menuaccess->create_crud == 1){
          ?>
          <?= Html::a('Create Catalog', ['create'], ['class' => 'btn btn-primary btn-sm']) ?>
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

    <div class="tbl-product-catalog-index table-responsive">


      <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
          ['class' => 'yii\grid\SerialColumn'],
          [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Actions',
            'template' => $template,
            'buttons' => [

              // SET ACTIVE BUTTON
              'activate' => function ($url, $model) {
                if ($model->is_active == 0) {
                    return Html::a(
                      '<i class="fa-solid fa-circle-check text-success"></i>',
                      ['product-catalog/set-active', 'id' => $model->id],
                      [
                        'class' => '',
                        'data-confirm' => 'Set this catalog as active?',
                        'title' => 'Set Active'
                      ]
                    );


                }
                return ''; // hide when already active
              },

              // DEACTIVATE BUTTON
              'deactivate' => function ($url, $model) {
                if ($model->is_active == 1) {
                    return Html::a(
                      '<i class="fa-solid fa-circle-xmark text-warning"></i>',
                      ['product-catalog/deactivate', 'id' => $model->id],
                      [
                        'class' => '',
                        'data-confirm' => 'Deactivate this catalog?',
                        'title' => 'Deactivate'
                      ]
                    );


                }
                return ''; // hide when inactive
              },
              // VIEW BUTTON
              'view' => function ($url, $model) {
                return Html::a(
                  '<i class="fas fa-eye text-info"></i>',
                  ['product-catalog/view', 'id' => $model->id],
                  ['class' => '']
                );
              },


              // DELETE BUTTON
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

              if ($action === 'delete') {
                $url ='index.php?r=product-catalog/delete&id='.$model->id;
                return $url;
              }
            }
          ],

          // 'id',
          'title',
          // 'file_name',
          // 'file_path',
          [
            'attribute' => 'file_path',
            'label' => 'Uploaded File',
            'format' => 'raw',
            'value' => function ($dataProvider) {
              if($dataProvider->file_path != ""){
                return Html::a(
                  'View File',
                  '#',
                  [
                    'class' => 'view-file-link btn btn-sm btn-primary p-1 w-100',
                    'data-file' => $dataProvider->file_path,
                  ]
                );
              }else{
                return '';
              }

            },
          ],
          'catalog_year',
          'description:ntext',
          // ACTIVE STATUS COLUMN
          [
            'attribute' => 'is_active',
            'format' => 'raw',
            'label' => 'Active Status',
            'value' => function ($model) {
              if ($model->is_active == 1) {
                return '<span class="badge bg-success">Active</span>';
              } else {
                return '<span class="badge bg-danger">Inactive</span>';
              }
            }
          ],



          //'is_active',
          //'fk_location_id',
          //'ip',
          //'status',
          //'crt_by',
          //'mod_by',
          //'crt_time',
          //'mod_time',
          // [
          //   'class' => ActionColumn::className(),
          //   'urlCreator' => function ($action, TblProductCatalog $model, $key, $index, $column) {
          //     return Url::toRoute([$action, 'id' => $model->id]);
          //   }
          // ],
        ],
      ]); ?>

    </div>
  </div>
</div>

<!-- Bootstrap Modal -->
<div class="modal fade" id="fileModal" tabindex="-1" aria-labelledby="fileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="fileModalLabel">Uploaded File</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center" id="fileModalBody">
        <!-- File content will load here -->
      </div>
    </div>
  </div>
</div>
<?php
$script = <<<JS
$(document).on('click', '.view-file-link', function(e) {
  e.preventDefault();
  var fileUrl = $(this).data('file');
  // 🔧 Remove leading slash if present
  if (fileUrl.charAt(0) === '/') {
    fileUrl = fileUrl.substring(1);
  }
  var modalBody = $('#fileModalBody');

  // Detect file type
  var extension = fileUrl.split('.').pop().toLowerCase();

  var content = '';
  if (['jpg', 'jpeg', 'png', 'gif', 'webp'].indexOf(extension) !== -1) {
    content = '<img src="' + fileUrl + '" alt="File" style="max-width:100%; height:auto;"/>';
  } else if (extension === 'pdf') {
    content = '<iframe src="' + fileUrl + '" width="100%" height="900px"></iframe>';
  } else {
    content = '<a href="' + fileUrl + '" target="_blank">Download File</a>';
  }

  modalBody.html(content);
  $('#fileModal').modal('show');
});
JS;

$this->registerJs($script);
?>
