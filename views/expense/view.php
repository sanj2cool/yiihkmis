<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\TblExpense $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Expenses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>78,'status'=>1])->one();

?>
<div class="tbl-expense-view">

  <div class="card shadow rounded mt-2">
      <div class="card-body p-3">
      <div class="row">
        <div class="col-lg-6">
          <h3><?= Html::encode($this->title) ?></h3>
        </div>
        <div class="col-lg-6 text-end">
          <?php
            if(isset($menuaccess) && $menuaccess->edit_crud == 1){
              ?>
                <?= Html::a('<i class="fas fa-pen"></i> Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']) ?>
              <?php
            }
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
    <div class="card shadow rounded mt-2 mb-2">
      <div class="card-header p-3 border-bottom border-300 bg-success-subtle">
        <div class="row g-3 justify-content-between align-items-center">
          <div class="col-12 col-md">
            <h5 class=" mb-0" id="basic-details">Basic Details</h5>
          </div>
        </div>
      </div>
        <div class="card-body p-3">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            // 'vendor_name',
            [
              'attribute' => 'vendor',
              'label' => 'Vendor',
              'value' => function($model){
                  return $model->vendor->company_name ?? '';
              }

            ],
            [
              'attribute' => 'fk_expense_category_id',
              'format' => 'raw',
              'value' => function($model){
                $getcat = \app\models\TblExpenseCategory::find()->where(['id'=>$model->fk_expense_category_id,'status'=>1])->one();
                if(isset($getcat) && $getcat->id != ""){
                  return $getcat->title;
                }else{
                  return '(not set)';
                }
              },
            ],
            'date',
            'amount',
            // 'fk_tax_rate_id',
            [
              'attribute' => 'fk_tax_rate_id',
              'format' => 'raw',
              'value' => function($model){
                $getcat = \app\models\TblTaxRate::find()->where(['id'=>$model->fk_tax_rate_id,'status'=>1])->one();
                if(isset($getcat) && $getcat->id != ""){
                  return $getcat->tax_rate.'% ['.$getcat->province_state.']';
                }else{
                  return '(not set)';
                }
              },

            ],
            'tax_amount',
            'final_amount',
            [
              'attribute' => 'fk_payment_method_id',
              'format' => 'raw',
              'value' => function($model){
                $getcat = \app\models\TblPreferredPaymentMethod::find()->where(['id'=>$model->fk_payment_method_id,'status'=>1])->one();
                if(isset($getcat) && $getcat->id != ""){
                  return $getcat->title;
                }else{
                  return '(not set)';
                }
              },

            ],
            [
              'attribute' => 'fk_location_id',
              'label' => 'Location',
              'value' => function($model){
                $getloc = \app\models\TblLocation::find()->where(['id'=>$model->fk_location_id])->one();
                if(isset($getloc) && $getloc->title != ""){
                  return $getloc->title;
                }else{
                  return "(not set)";
                }
              },

            ],
            // 'fk_location_id',
            // 'fk_expense_category_id',
            // 'fk_payment_method_id',
            'receipt_no',
            'notes:ntext',
            // 'ip',
            // 'status',
            // 'crt_by',
            // 'crt_time',
            // 'mod_by',
            // 'mod_time',
        ],
    ]) ?>

</div>
</div>

<div class="card shadow rounded mt-2 mb-4">
  <div class="card-header p-3 border-bottom border-300 bg-primary-subtle">
    <div class="row g-3 justify-content-between align-items-center">
      <div class="col-12 col-md">
        <h5 class="mb-0" id="upload-images">Uploaded Files</h5>
      </div>
    </div>
  </div>
  <div class="card-body p-3">

    <?php

        //=====get documents ======
        $getdocs = \app\models\TblExpenseFile::find()->where(['fk_expense_id'=>$model->id,'status'=>1])->all();
        if(isset($getdocs) && count($getdocs) > 0){
          echo '<table class="table table-bordered mt-3">
            <thead>
            <tr>
            <th>Sr. No.</th>
            <th>Document</th>
            </tr>
            </thead>
            <tbody>';
            $j = 1;
            foreach($getdocs as $gi){
              echo '<tr>
              <td>
              '.$j.'
              </td>
              <td>';
              $uploaded_file = "https://hkmis.ca/web/expenses/".$gi->file_upload;
              // Get the file extension
              $fileExtension = strtolower(pathinfo($uploaded_file, PATHINFO_EXTENSION));
              // Display appropriate button based on the file type
              if ($fileExtension === 'pdf') {
                echo '<a class="btn btn-primary btn-sm" href="javascript:void(0);" style="cursor:pointer;" onclick="handleFileAction(\'' . $uploaded_file . '\')">View File</a>';
              }else if($gi->file_upload != ""){
                echo '<a style="cursor:pointer;" class="img_open_popup btn btn-primary btn-sm" id="'.$gi->id.'">
                <input type="hidden" id="img_'.$gi->id.'" value="https://hkmis.ca/web/expenses/'.$gi->file_upload.'">
                View File
                </a>';
              }
              echo '

              </td>
              </tr>';
              $j++;
            }
            echo '
            </tbody>
          </table>';
        }//=====if isset ended =======
        else{
          echo 'No files uploaded';
        }
     ?>
  </div>
</div>
</div>

<!-- Modal for PDF display -->
<div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfModalLabel">Document</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="pdfViewer" src="" width="100%" height="800px"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- product image modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="exampleModalLabel">Image</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-12" id="product_img_div">

          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script>
function handleFileAction(fileName) {
  const fileExtension = fileName.split('.').pop().toLowerCase();

  if (fileExtension === 'pdf') {
    // Open PDF in Bootstrap Modal
    $('#pdfViewer').attr('src', fileName);
    $('#pdfModal').modal('show');
  } else {
    alert('Unsupported file type!');
  }
}
</script>
<?php
  $this->registerJs('

  $(document).on("click",".img_open_popup",function(e){
    e.preventDefault();
    let id = $(this).attr("id");
    let img_src = $("#img_"+id).val();
    let content = "<img src=\""+img_src+"\" class=\"img-fluid\"/>";
    $("#product_img_div").html(content);
    var imageModal = new bootstrap.Modal(document.getElementById(\'imageModal\'), {});
    imageModal.show();

  });


  ');
 ?>
