<?php

use app\models\TblProduct;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use app\models\TblProductCategory;
use yii\helpers\ArrayHelper;
use yii\db\Query;
/** @var yii\web\View $this */
/** @var app\models\TblProductSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Items Creation - Dev';
$this->params['breadcrumbs'][] = $this->title;

$template = '{view}';
$status = [1=>"Active",2=>"Inactive"];
$catsarr = TblProductCategory::find()->select(['title', 'id'])->where('status != 0')->indexBy('id')->column();




//=======get the mennu items with parent_id = 18 =======
//====order by priority =======
use app\models\TblMenu;
$getmenuitems = TblMenu::find()->where(['parent_id'=>18,'status'=>1])->orderBy(['priority'=>SORT_ASC])->all();

//======current menu id is 19=======
if(isset($getmenuitems) && count($getmenuitems) > 0){
  echo '<div class="card theme-wizard mb-5" data-theme-wizard="data-theme-wizard">
  <div class="card-header bg-body-white pt-3 pb-2 border-bottom-0">
  <ul class="nav justify-content-between nav-wizard nav-wizard-success" role="tablist">';
  foreach($getmenuitems as $gm){
    if($gm->id == 19){
      echo '<li class="nav-item" role="presentation"><a class="nav-link active fw-semibold" href="'.Url::to([$gm->url]).'">
      <div class="text-center d-inline-block">
      <span class="nav-item-circle-parent">
      <span class="nav-item-circle"><span class="'.$gm->class.'"></span>
      </span>
      </span><span class="d-none d-md-block mt-1 fs-9">'.$gm->title.'</span></div>
      </a></li>';
    }else{
      if($gm->id == 23){
        echo '<li class="nav-item" role="presentation"><a class="nav-link fw-semibold done complete" href="'.Url::to([$gm->url]).'">
        <div class="text-center d-inline-block">
        <span class="nav-item-circle-parent">
        <span class="nav-item-circle"><span class="'.$gm->class.'"></span>
        </span>
        </span><span class="d-none d-md-block mt-1 fs-9">'.$gm->title.'</span></div>
        </a></li>';
      }else{
        echo '<li class="nav-item" role="presentation"><a class="nav-link fw-semibold" href="'.Url::to([$gm->url]).'">
        <div class="text-center d-inline-block">
        <span class="nav-item-circle-parent">
        <span class="nav-item-circle"><span class="'.$gm->class.'"></span>
        </span>
        </span><span class="d-none d-md-block mt-1 fs-9">'.$gm->title.'</span></div>
        </a></li>';
      }

    }
  }
  echo '  </ul>
  </div>
  </div>';
}//======if isset ended ========

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

use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$user_company = $session['userCompany'];
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>19,'status'=>1])->one();

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
        <a href="https://hkmis.ca/scan-barcode.html" target="_blank" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Scan Item Barcode"><button type="button" class="btn btn-success btn-sm">Scan Barcode</button></a>
        <?= Html::a('Import Data', ['import'], ['class' => 'btn btn-info btn-sm']) ?>
        <?php
        if(isset($menuaccess) && $menuaccess->create_crud == 1){
          ?>
          <?= Html::a('Create Item', ['create'], ['class' => 'btn btn-primary btn-sm']) ?>
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

    <div class="tbl-product-index table-responsive">


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
                $url ='index.php?r=product-copy/update&id='.$model->id;
                return $url;
              }
              if ($action === 'view') {
                $url ='index.php?r=product-copy/view&id='.$model->id;
                return $url;
              }
              if ($action === 'delete') {
                $url ='index.php?r=product-copy/delete&id='.$model->id;
                return $url;
              }
            }
          ],
          // 'id',
          'name',
          'description:ntext',
          [
            'label' => 'Cross References',
            'format' => 'html',
            'attribute' => 'crossRef',
            'value' => function($dataProvider) {
              $groupNames = [];
              foreach ($dataProvider->crossRef as $group) {
                $groupNames[] = $group->cross_ref.'<br />';
              }
              if(count($groupNames) > 0){
                return implode("\n", $groupNames);
              }else{
                return "";
              }

            },
            'enableSorting' => true,
          ],
          [
            'attribute' => 'brands',
            'label' => 'Brands',
            'value' => function ($model) {
              $mechanicNames = ArrayHelper::getColumn($model->brands, 'title');
              return implode(', ', $mechanicNames);
            },
            'filter' => \yii\helpers\Html::activeTextInput($searchModel, 'brands', ['class' => 'form-control']),
            'enableSorting' => true,
          ],

          [
            'attribute' => 'categories',
            'label' => 'Categories',
            'value' => function ($model) {
              $mechanicNames = ArrayHelper::getColumn($model->categories, 'title');
              return implode(', ', $mechanicNames);
            },
            'filter' => \yii\helpers\Html::activeTextInput($searchModel, 'categories', ['class' => 'form-control']),
            'enableSorting' => true,
          ],

          // 'fk_category_id',
          // [
          //   'attribute' => 'fk_category_id',
          //   'label' => 'Category',
          //   'value' => function($dataProvider){
          //     $getcat = TblProductCategory::find()->where(['id'=>$dataProvider->fk_category_id,'status'=>1])->one();
          //     if(isset($getcat) && $getcat->id != ""){
          //       return $getcat->title;
          //     }else{
          //       return "(not set)";
          //     }
          //   },
          //   'filter' => Html::activeDropDownList($searchModel, 'fk_category_id', $categoryList,['class'=>'form-select','prompt' => 'Select', 'encode' => false]),
          // ],
          [
            'attribute' => 'assemblycat',
            'label' => 'Assembly Category',
            'format' => 'raw',
            'value' => function($model){
              if($model->assemblycat){
                return $model->assemblycat->title;
              }else{
                return '<i>(not set)</i>';
              }
            }
          ],
          'internal_sku',
          [
            'attribute' => 'current_qty',
            'label' => 'Current Qty Brampton',
            'value' => function ($model) {
              return $model->current_qty;
            },
            'filter' => Html::activeTextInput($searchModel, 'current_qty', [
                 'class' => 'form-control',
                 'placeholder' => 'Search'
             ]),
            'enableSorting' => true,
            'visible' => $user_company == 1,
          ],
          [
            'attribute' => 'current_qty_montreal',
            'value' => function ($model) {
              return $model->current_qty_montreal;
            },
            'filter' => Html::activeTextInput($searchModel, 'current_qty_montreal', [
                 'class' => 'form-control',
                 'placeholder' => 'Search'
             ]),
            'enableSorting' => true,
            'visible' => $user_company == 2,
          ],
          [
            'label' => 'Image',
            'format' => 'raw',
            'attribute' => 'has_image',   // <-- add this line
            'filter' => Html::activeDropDownList(
               $searchModel,
               'has_image',
               [
                   '1' => 'Images Exist',
                   '0' => 'No Image'
               ],
               ['prompt' => 'Select','class'=>'form-select'] // <-- this adds the prompt
           ),
            'value' => function ($model) {
              $img = $model->firstImage ? $model->firstImage->url : null;

              if (!$img) return '(no image)';
              $url = Yii::getAlias('@web/product-images/' . $model->firstImage->url);

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
            },
          ],
          // 'quantity_in_stock',
          // 'cost_price',
          //'default_price',
          //'ip',
          //'status',
          //'crt_by',
          //'mod_by',
          //'crt_time',
          //'mod_time',
          // [
          //     'class' => ActionColumn::className(),
          //     'urlCreator' => function ($action, TblProduct $model, $key, $index, $column) {
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
