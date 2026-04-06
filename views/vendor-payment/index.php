<?php

use app\models\TblVendorPayment;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use app\models\TblPreferredPaymentMethod;
use app\models\TblVendor;
use yii\helpers\ArrayHelper;
/** @var yii\web\View $this */
/** @var app\models\TblVendorPaymentSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Vendor Payments';
$this->params['breadcrumbs'][] = $this->title;

use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
//menu item id 81
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id])->andWhere(['fk_menu_id'=>63])->one();
$temp_str = "";
if(isset($menuaccess) && $menuaccess->edit_crud == 1){
  $temp_str .= '{edit}';
}
if(isset($menuaccess) && $menuaccess->delete_crud == 1){
  $temp_str .= ' {delete}';
}
$template = $temp_str;
$prefpayarr = TblPreferredPaymentMethod::find()->select(['title', 'id'])->where('status != 0')->indexBy('id')->column();
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
          <?= Html::a('Settle Payment', ['create'], ['class' => 'btn btn-primary btn-sm']) ?>

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

    <div class="tbl-vendor-payment-index table-responsive">


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
                        }
                    ],
                    'urlCreator' => function ($action, $model, $key, $index) {

                        if ($action === 'view') {
                          $url ='index.php?r=vendor-payment/view&id='.$model->id;
                          return $url;
                        }
                    }
                ],
          // 'id',
          // 'fk_vendor_invoice_id',
          // 'fk_vendor_id',
          [
            'attribute' => 'vendor',
            'value' => 'vendor.company_name'
          ],
          'amount_received',
          'ar_date',
          // 'fk_payment_method_id',
          [
            'attribute' => 'fk_payment_method_id',
            'value' => function($dataProvider){
              $getpstatus = TblPreferredPaymentMethod::find()->where(['id'=>$dataProvider->fk_payment_method_id])->one();
              if(isset($getpstatus) && $getpstatus->title != ""){
                return $getpstatus->title;
              }else{
                return "(not set)";
              }
            },
            'filter' => Html::activeDropDownList($searchModel, 'fk_payment_method_id', $prefpayarr,['class'=>'form-select','prompt' => 'Select']),

          ],
          //'fk_location_id',
          'notes:ntext',
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

              if (empty($model->receiptFiles)) {
                return '<span class="text-muted">—</span>';
              }
              $count = count($model->receiptFiles);
              return Html::button(
                'View Files ('.$count.')',
                [
                  'class' => 'btn btn-sm btn-primary expense-file-preview',
                  'data-files' => json_encode(array_map(function ($file) {
                    return [
                      'url' => 'https://hkmis.ca/web/vendor-payment-files/' . $file->file_url,
                      'type' => strtolower(pathinfo($file->file_url, PATHINFO_EXTENSION))
                    ];
                  }, $model->receiptFiles)),
                ]
              );
            }
          ],
          //'ip',
          //'status',
          //'crt_by',
          //'mod_by',
          //'crt_time',
          //'mod_time',
          // [
          //   'class' => ActionColumn::className(),
          //   'urlCreator' => function ($action, TblVendorPayment $model, $key, $index, $column) {
          //     return Url::toRoute([$action, 'id' => $model->id]);
          //   }
          // ],
        ],
      ]); ?>

    </div>
  </div>
</div>

<div class="modal fade" id="expenseFileModal" tabindex="-1">
  <div class="modal-dialog modal-fullscreen modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Uploaded File(s)</h5>
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
