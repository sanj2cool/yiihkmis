<?php

use app\models\TblVendorInvoice;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use app\models\TblTerms;
use app\models\TblVendor;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\TblVendorInvoiceSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Purchase Invoices';
$this->params['breadcrumbs'][] = $this->title;
$allterms = TblTerms::find()->where(['status'=>1])->orderBy(['priority'=>SORT_ASC])->all();
$termarr = ArrayHelper::map($allterms,'id','title');
$template = '{view}';
use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>12,'status'=>1])->one();
if(isset($menuaccess) && $menuaccess->edit_crud == 1){
  $template .= ' {edit}';
}
if(isset($menuaccess) && $menuaccess->delete_crud == 1){
  $template .= ' {delete}';
}

$allterms = TblTerms::find()->where(['status'=>1])->orderBy(['priority'=>SORT_ASC])->all();
$termarr = ArrayHelper::map($allterms,'id','title');
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
        <?= Html::a('Create Purchase Invoice', ['create'], ['class' => 'btn btn-primary btn-sm']) ?>
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

<div class="tbl-vendor-invoice-index table-responsive">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            [
              'class' => ActionColumn::className(),
              'template' => '{view}',
              'buttons' => [
                'view' => function ($url, $model) {
                  return Html::a('<i class=" fas fa-eye"></i>', $url, [
                    'title' => Yii::t('app', 'view'),
                    'class' => 'text-info'
                  ]);
                },
                'edit' => function ($url, $model) {
                  return Html::a('<i class=" fas fa-pencil-alt"></i>', $url, [
                    'title' => Yii::t('app', 'edit'),
                    'class' => 'text-primary'
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
                  $url ='index.php?r=vendor-invoice/update&id='.$model->id;
                  return $url;
                }
                if ($action === 'view') {
                  $url ='index.php?r=vendor-invoice/view&id='.$model->id;
                  return $url;
                }
                if ($action === 'delete') {
                  $url ='index.php?r=vendor-invoice/delete&id='.$model->id;
                  return $url;
                }
              }
            ],
            // 'id',
            'vendor_invoice_number',
            'purchase_invoice_no',
            // 'fk_vendor_id',
            [
              'attribute' => 'vendor',
              'value' => 'vendor.company_name'
            ],
            // [
            //   'attribute' => 'fk_vendor_id',
            //   'label' => 'Vendor',
            //   'value' => function($dataProvider){
            //     $getvendor = TblVendor::find()->where(['id'=>$dataProvider->fk_vendor_id,'status'=>1])->one();
            //     if(isset($getvendor) && $getvendor->company_name != ""){
            //       return $getvendor->company_name;
            //     }else{
            //       return "(not set)";
            //     }
            //   }
            // ],
            // 'fk_terms_id',
            [
              'attribute' => 'fk_terms_id',
              'label' => 'Terms',
              'value' => function($dataProvider){
                $geterm = TblTerms::find()->where(['id'=>$dataProvider->fk_terms_id,'status'=>1])->one();
                if(isset($geterm) && $geterm->title != ""){
                  return $geterm->title;
                }else{
                  return "(not set)";
                }
              },
              'filter' => Html::activeDropDownList($searchModel, 'fk_terms_id', $termarr,['class'=>'form-select','prompt' => 'Select']),
            ],
            'invoice_date',
            'due_date',
            //'hst',
            //'subtotal',
            //'hst_amount',
            'total_amount',
            [
              'attribute' => 'invoice_upload',
              'label' => 'Uploaded Invoice',
              'format' => 'raw',
              'value' => function ($dataProvider) {
                if($dataProvider->invoice_file != ""){
                  return Html::a(
                    'View File',
                    '#',
                    [
                      'class' => 'view-file-link btn btn-sm btn-primary p-1 w-100',
                      'data-file' => "https://hkmis.ca/web/vendor-invoices/".$dataProvider->invoice_file,
                    ]
                  );
                }else{
                  return '';
                }

              },
            ],
            //'comments:ntext',
            //'status',
            //'ip',
            //'crt_time',
            //'crt_by',
            //'mod_time',
            //'mod_by',
            // [
            //     'class' => ActionColumn::className(),
            //     'urlCreator' => function ($action, TblVendorInvoice $model, $key, $index, $column) {
            //         return Url::toRoute([$action, 'id' => $model->id]);
            //      }
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
        <h5 class="modal-title" id="fileModalLabel">Uploaded Invoice</h5>
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
