<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\TblProductCatalog $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => ' Catalogs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>77,'status'=>1])->one();

?>
<div class="tbl-product-catalog-view">

  <div class="card shadow rounded mt-2">
      <div class="card-body p-3">
      <div class="row">
        <div class="col-lg-6">
          <h3><?= Html::encode($this->title) ?></h3>
        </div>
        <div class="col-lg-6 text-end">

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
            // 'file_name',
            // 'file_path',
            [
              'attribute' => 'file_path',
              'label' => 'Uploaded File',
              'format' => 'raw',
              'value' => function ($model) {
                if($model->file_path != ""){
                  return Html::a(
                    'View File',
                    '#',
                    [
                      'class' => 'view-file-link btn btn-sm btn-primary',
                      'data-file' => $model->file_path,
                    ]
                  );
                }else{
                  return '';
                }

              },
            ],
            'catalog_year',
            'description:ntext',
            // 'is_active',
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
            // 'fk_location_id',
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
