<?php

use app\models\TblProductCategory;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\db\Query;
/** @var yii\web\View $this */
/** @var app\models\TblProductCategorySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Categories Copy';
$this->params['breadcrumbs'][] = $this->title;
// $template = '{edit} {delete}';
$status = [1=>"Active",2=>"Inactive"];
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
$template = '{view}';
use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>23,'status'=>1])->one();
// if(isset($menuaccess) && $menuaccess->edit_crud == 1){
//   $template .= ' {edit}';
// }
// if(isset($menuaccess) && $menuaccess->delete_crud == 1){
//   $template .= ' {delete}';
// }
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
          <?= Html::a('Create Category', ['create'], ['class' => 'btn btn-primary btn-sm']) ?>
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
    <div class="tbl-product-category-index table-responsive">


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
                $url ='index.php?r=product-category/update&id='.$model['id'];
                return $url;
              }
              if ($action === 'view') {
                $url ='index.php?r=product-category/view&id='.$model['id'];
                return $url;
              }
              if ($action === 'delete') {
                $url ='index.php?r=product-category/delete&id='.$model['id'];
                return $url;
              }
            }
          ],
          // 'id',
          // 'title',
          [
            'attribute' => 'title',
            'format' => 'raw',
            'value' => function($model){
              $hasChildren = TblProductCategory::find()->where(['parent_id' => $model->id, 'status' => 1])->exists();
              $title = $model->getIndentedTitle();
              if ($hasChildren) {
                $url = Url::to(['product-category/index', 'parent_id' => $model->id]);
                return $title . ' <a class="unformat text-sm" href="'.$url.'"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" title="Open in New Tab"></i></a>';
              }
              return $title;
            }
          ],
          [
            'attribute' => 'description',
            'value' => function ($model) {
              return Html::encode($model->description);
            },
            'contentOptions' => ['style' => 'max-width: 300px; word-wrap: break-word;'],
          ],
          [
            'attribute' => 'parent_id',
            'format' => 'raw',
            'value' => function($model){
              $parent = TblProductCategory::findOne(['id' => $model->parent_id, 'status' => 1]);
              return $parent ? $parent->title : '<i>(not set)</i>';
            },
            'filter' => Html::activeDropDownList(
              $searchModel,
              'parent_id',
              $categoryList,
              ['class'=>'form-select','prompt'=>'Select', 'encode' => false]
            ),
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
          // ],
          [
            'attribute' => 'show_on_website',
            'format' => 'raw',
            'value' => function($model){
              return $model->show_on_website == 1 ? 'Yes' : ($model->show_on_website == 2 ? 'No' : '<i>(not set)</i>');
            },
            'filter' => Html::activeDropDownList(
              $searchModel,
              'show_on_website',
              [1 => 'Yes', 2 => 'No'],
              ['class'=>'form-select','prompt'=>'Select']
            ),
          ],
          [
            'attribute' => 'productCount',
            'label' => 'Number of Products',
            'format' => 'raw',
            'value' => function($model) {
              $url = Url::to(['product-copy/index', 'TblProductSearch[categories]' => $model->title]);
              return $model->productCount > 0
              ? $model->productCount . ' <a class="unformat text-sm" href="'.$url.'" target="_blank"><i class="fas fa-external-link-alt"></i></a>'
              : $model->productCount;
            },
            'contentOptions' => ['style' => 'text-align:center;'],
          ],

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

          // 'parent_id',
          // 'ip',
          //'status',
          //'crt_by',
          //'mod_by',
          //'crt_time',
          //'mod_time',
          // [
          //     'class' => ActionColumn::className(),
          //     'urlCreator' => function ($action, TblProductCategory $model, $key, $index, $column) {
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
