<?php

use app\models\TblExpense;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\TblLocation;
use app\models\TblExpenseCategory;
use app\models\TblPreferredPaymentMethod;
use app\models\TblUser;


/** @var yii\web\View $this */
/** @var app\models\TblExpenseSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Expenses';
$this->params['breadcrumbs'][] = $this->title;

$template = '{view}';
use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>78,'status'=>1])->one();
// if(isset($menuaccess) && $menuaccess->edit_crud == 1){
//   $template .= ' {edit}';
// }
// if(isset($menuaccess) && $menuaccess->delete_crud == 1){
//   $template .= ' {delete}';
// }
//get locations
$location = TblLocation::find()->where(['status'=>1])->andWhere('id in (select fk_location_id from tbl_user_location where fk_user_id = '.$fk_user_id.' and status = 1)')->orderBy(['title'=>SORT_ASC])->all();
$locationsarr = ArrayHelper::map($location,'id','title');
$loc_arr = \app\models\TblUserLocation::find()
    ->select('fk_location_id')
    ->where([
        'fk_user_id' => $fk_user_id,
        'status' => 1,
    ])
    ->column();
$allusers = TblUser::find()
    ->alias('u')
    ->innerJoin(
        'tbl_user_location ul',
        'ul.fk_user_id = u.id AND ul.status = 1'
    )
    ->where([
        'u.status' => 1,
        'ul.fk_location_id' => $loc_arr, // array of allowed locations
    ])
    ->orderBy(['u.alias' => SORT_ASC])
    ->distinct()
    ->all();

$usersarr = ArrayHelper::map($allusers, 'id', 'alias');

$categories = TblExpenseCategory::find()->where(['status'=>1])->orderBy(['title'=>SORT_ASC])->all();
$categoriesarr = ArrayHelper::map($categories,'id','title');

$allpaymethods = TblPreferredPaymentMethod::find()->where(['status'=>1])->orderBy(['title'=>SORT_ASC])->all();
$paymethodarr = ArrayHelper::map($allpaymethods,'id','title');



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
          <?= Html::a('Create Expense', ['create'], ['class' => 'btn btn-primary btn-sm']) ?>
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

    <div class="tbl-expense-index table-responsive">


      <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
          ['class' => 'yii\grid\SerialColumn'],
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
                $url ='index.php?r=expense/update&id='.$model->id;
                return $url;
              }
              if ($action === 'view') {
                $url ='index.php?r=expense/view&id='.$model->id;
                return $url;
              }
              if ($action === 'delete') {
                $url ='index.php?r=expense/delete&id='.$model->id;
                return $url;
              }
            }
          ],
          // 'id',
          // 'vendor_name',
          [
            'attribute' => 'vendor',
            'label' => 'Vendor',
            'value' => 'vendor.company_name'
          ],
          // 'fk_expense_category_id',
          [
            'attribute' => 'fk_expense_category_id',
            'format' => 'raw',
            'value' => function($dataProvider){
              $getcat = TblExpenseCategory::find()->where(['id'=>$dataProvider->fk_expense_category_id,'status'=>1])->one();
              if(isset($getcat) && $getcat->id != ""){
                return $getcat->title;
              }else{
                return '(not set)';
              }
            },
            'filter' => Html::activeDropDownList($searchModel, 'fk_expense_category_id',$categoriesarr,['class'=>'form-select','prompt' => 'Select']),
          ],
          'date',
          // 'amount',
          // 'tax_amount',
          'final_amount',
          // 'fk_payment_method_id',
          [
            'attribute' => 'fk_payment_method_id',
            'format' => 'raw',
            'value' => function($dataProvider){
              $getcat = TblPreferredPaymentMethod::find()->where(['id'=>$dataProvider->fk_payment_method_id,'status'=>1])->one();
              if(isset($getcat) && $getcat->id != ""){
                return $getcat->title;
              }else{
                return '(not set)';
              }
            },
            'filter' => Html::activeDropDownList($searchModel, 'fk_payment_method_id',$paymethodarr,['class'=>'form-select','prompt' => 'Select']),
          ],
          // 'fk_location_id',
          // [
          //   'attribute' => 'fk_location_id',
          //   'label' => 'Location',
          //   'value' => function($dataProvider){
          //     $getloc = TblLocation::find()->where(['id'=>$dataProvider->fk_location_id])->one();
          //     if(isset($getloc) && $getloc->title != ""){
          //       return $getloc->title;
          //     }else{
          //       return "(not set)";
          //     }
          //   },
          //   'filter' => Html::activeDropDownList($searchModel, 'fk_location_id', $locationsarr,['class'=>'form-select','prompt' => 'Select']),
          // ],
          [
            'label' => 'Files',
            'format' => 'raw',
            'headerOptions' => [
              'style' => 'min-width:120px; white-space:nowrap;'
            ],
            'contentOptions' => [
              'style' => 'min-width:120px; white-space:nowrap;'
            ],
            'value' => function ($model) {

              if (empty($model->expenseFiles)) {
                return '<span class="text-muted">—</span>';
              }
              $count = count($model->expenseFiles);
              return Html::button(
                'View Files ('.$count.')',
                [
                  'class' => 'btn btn-sm btn-primary expense-file-preview',
                  'data-files' => json_encode(array_map(function ($file) {
                    return [
                      'url' => 'https://hkmis.ca/web/expenses/' . $file->file_upload,
                      'type' => strtolower(pathinfo($file->file_upload, PATHINFO_EXTENSION))
                    ];
                  }, $model->expenseFiles)),
                ]
              );
            }
          ],
          // [
          //   'attribute' => 'crt_by',
          //   'value' => function($dataProvider){
          //     $getpstatus = TblUser::find()->where(['id'=>$dataProvider->crt_by])->one();
          //     if(isset($getpstatus) && $getpstatus->alias != ""){
          //       return $getpstatus->alias;
          //     }else{
          //       return "(not set)";
          //     }
          //   },
          //   'filter' => Html::activeDropDownList($searchModel, 'crt_by', $usersarr,['class'=>'form-select','prompt' => 'Select']),
          // ],
          [
            'attribute' => 'crt_by',
            'label' => 'Created By',
            'value' => function ($model) {
                return $model->createdByUser->alias ?? '(not set)';
            },
            'filter' => Html::activeDropDownList(
                $searchModel,
                'crt_by',
                $usersarr,
                ['class' => 'form-select', 'prompt' => 'Select']
            ),
        ],

          // 'crt_time'
          [
            'attribute' => 'crt_time_display',
            'label' => 'Created At',
            'value' => function ($model) {
              // return Yii::$app->formatter->asDatetime(
              //   $model->crt_time,
              //   'php:Y-m-d h:ia'
              // );
              return date('Y-m-d h:ia',strtotime($model->crt_time));
            },
            'filter' => Html::activeTextInput(
              $searchModel,
              'crt_time_display',
              ['class' => 'form-control']
            ),
            'contentOptions' => ['style' => 'white-space:nowrap;'],
          ],


          //'receipt_no',
          //'notes:ntext',
          //'ip',
          //'status',
          //'crt_by',
          //'crt_time',
          //'mod_by',
          //'mod_time',
          // [
          //   'class' => ActionColumn::className(),
          //   'urlCreator' => function ($action, TblExpense $model, $key, $index, $column) {
          //     return Url::toRoute([$action, 'id' => $model->id]);
          //   }
          // ],
        ],
      ]); ?>
    </div>
  </div>

</div>

<div class="modal fade" id="expenseFileModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Expense File</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center" id="expenseFileContent">
        <!-- dynamic -->
      </div>
    </div>
  </div>
</div>
<script>
document.addEventListener('click', function (e) {

  if (!e.target.classList.contains('expense-file-preview')) return;

  let files = JSON.parse(e.target.dataset.files);
  let html = '';

  files.forEach((file, index) => {

    html += `
    <div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
    <strong>File ${index + 1}</strong>
    <a href="${file.url}" target="_blank" class="btn btn-sm btn-outline-secondary">
    Open
    </a>
    </div>
    `;

    if (file.type === 'pdf') {
      html += `
      <iframe src="${file.url}" style="width:100%; height:65vh;" frameborder="0"></iframe>
      `;
    } else {
      html += `
      <div class="text-center">
      <img src="${file.url}" class="img-fluid" alt="Expense File">
      </div>
      `;
    }

    html += `</div>`;
  });

  document.getElementById('expenseFileContent').innerHTML = html;

  let modal = new bootstrap.Modal(document.getElementById('expenseFileModal'));
  modal.show();
});
</script>
