<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
/** @var yii\web\View $this */
/** @var app\models\TblClientReceipt $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Client Receipts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
//menu item id 81
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id])->andWhere(['fk_menu_id'=>64])->one();


?>
<div class="tbl-client-receipt-view">

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
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#summaryModal">
                  <i class="fas fa-pen"></i> Update
                </button>
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

    <div class="card shadow rounded mt-2">
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
            // 'fk_client_id',
            [
                'attribute' => 'client',
                'value' => $model->client->company_name, // Accessing the related customer name
                'label' => 'Customer', // Custom label
            ],
            [
              'attribute' => 'fk_bill_from_id',
              'label' => 'Receiving Company',
              'value' => function($model){
                $getcompany = \app\models\TblOwnershipCompany::find()->where(['id'=>$model->fk_bill_from_id])->one();
                if(isset($getcompany) && $getcompany->id != ""){
                  return $getcompany->location_name;
                }else{
                  return '<i>(not set)</i>';
                }
              },
            ],
            // 'fk_invoice_id',
            'amount_received',
            'ar_date',
            // 'fk_payment_method_id',
            [
              'attribute' => 'fk_payment_method_id',
              'value' => function($model){
                $getpstatus = \app\models\TblPreferredPaymentMethod::find()->where(['id'=>$model->fk_payment_method_id])->one();
                if(isset($getpstatus) && $getpstatus->title != ""){
                  return $getpstatus->title;
                }else{
                  return "(not set)";
                }
              },

            ],
            // 'fk_bill_from_id',
            'transaction_reference_number',
            'notes:ntext',
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

<div class="card shadow rounded mt-2 mb-2">
  <div class="card-header pt-3 pb-2 bg-warning-subtle">
    <div class="row">
      <div class="col-lg-12">
        <h5>
          Attachments
        </h5>
      </div>
    </div>
  </div>
  <div class="card-body p-3">
    <?php
    $getimgs = \app\models\TblClientReceiptImage::find()->where(['status'=>1,'fk_receipt_id'=>$model->id])->all();
    if(isset($getimgs) && count($getimgs) > 0){
      echo '<table class="table table-bordered mt-3">
      <thead>
      <tr>
      <th>Sr. No.</th>
      <th>Document</th>
      </tr>
      </thead>
      <tbody>';
      $j = 1;
      foreach($getimgs as $gi){
        $uploaded_file = "https://hkmis.ca/web/receipt-doc/".$gi->file_name;

        echo '<tr>
        <td>
        '.$j.'
        </td>
        <td>';
        $fileExtension = strtolower(pathinfo($uploaded_file, PATHINFO_EXTENSION));
        if ($fileExtension === 'pdf') {
          echo '<a class="btn btn-primary btn-sm mt-2" href="javascript:void(0);" style="cursor:pointer;" onclick="handleFileAction(\'' . $uploaded_file . '\')">View </a>';
        }else{
          echo '<input type="hidden" id="img_'.$gi->id.'" value="receipt-doc/'.$gi->file_name.'">
          <a style="cursor:pointer;" class="img_open_popup btn btn-primary btn-sm mt-2" id="'.$gi->id.'">
          View
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
    }else{
      echo '<p>
      No files uploaded yet.
      </p>';
    }
    ?>
  </div>
</div>

<div class="card mb-4">
  <div class="card-header pt-3 pb-2 bg-info-subtle">
    <div class="row">
      <div class="col-lg-12">
        <h5>
          Settled Invoices
        </h5>
      </div>
    </div>
  </div>
  <div class="card-body p-3 table-responsive">
    <!-- TblDriverInvoiceReceivable -->
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Sr. No.</th>
          <th>Invoice #</th>
          <th>Total Amount</th>
          <th>Settled Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $getinvoices = \app\models\TblAccountReceivable::find()->where(['fk_client_receipt_id'=>$model->id,'status'=>1])->all();
        if(isset($getinvoices) && count($getinvoices) > 0){
          $i = 1;
          foreach($getinvoices as $gi){

            $getinvoice = \app\models\TblInvoice::find()->where(['id'=>$gi->fk_invoice_id,'status'=>1])->one();
            if(isset($getinvoice) && $getinvoice->id != ""){
              $invoice_number = $getinvoice->invoice_number;
              $total_amount = $getinvoice->total_amount;
            }else{
              $invoice_number = "";
              $total_amount = 0;
              $balance = 0;
              $driver_company = "";
              $pay_period = "";
            }
            echo '<tr>
            <td>'.$i.'</td>
            <td>'.$invoice_number.' | <a href="'.Url::to(['invoice/view','id'=>$gi->fk_invoice_id]).'" target="_blank"><i class="fa-solid fa-up-right-from-square"></i></a></td>
            <td>'.$total_amount.'</td>
            <td>'.$gi->amount_received.'</td>
            </tr>';
            $i++;
          }//---for loop ended ----
        }else{
          echo '<tr>
          <td colspan="4">No Records found</td>
          </tr>';
        }
        ?>
      </tbody>
    </table>
  </div>
</div>
<div class="modal fade" id="summaryModal" tabindex="-1" aria-labelledby="summaryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title8" id="summaryModalLabel">Update Payment</h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form name="frm_reservation" action="<?= Yii::$app->urlManager->createUrl(['client-receipt/edit-transaction','id'=>$model->id]) ?>" method="post" autocomplete="off" enctype="multipart/form-data">
        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-12 mb-2">
              <label for="">Payment Method</label>
              <select class="form-select" name="payment_method" required>
                <option value="">Select</option>
                <?php
                $allpaymethods = \app\models\TblPreferredPaymentMethod::find()->where(['status'=>1])->orderBy(['title'=>SORT_ASC])->all();
                if(isset($allpaymethods) && count($allpaymethods) > 0){
                  foreach($allpaymethods as $ap){
                    if($ap->id == $model->fk_payment_method_id){
                      echo '<option value="'.$ap->id.'" selected="selected">'.$ap->title.'</option>';
                    }else{
                      echo '<option value="'.$ap->id.'">'.$ap->title.'</option>';
                    }
                  }
                }
                ?>
              </select>
            </div>
            <div class="col-lg-12 mb-2">
              <label for="">Transaction Reference Number</label>
              <input type="text" name="transaction_reference_number" value="<?=$model->transaction_reference_number?>" class="form-control" maxlength="100">
            </div>
            <div class="col-lg-12 mb-2">
              <label for="">Notes</label>
              <textarea class="form-control" name="notes" rows="3"><?=$model->notes?></textarea>
            </div>
            <input type="hidden" name="deleted_image_ids" id="deleted_image_ids" value="">

            <div class="row">
              <div class="col-lg-8 mb-2">
                <label for="">Document</label>
                <input type="file" name="file_url[]" class="form-control" accept="image/*,application/pdf">
                <span style="color:#f00;font-size:11px;">Only images & pdf files allowed</span>
              </div>
              <div class="col-lg-4 mt-4 mb-2">
                <button type="button" name="button" class="btn btn-secondary btn-sm btn_add_before_image">
                  <i class="fa-solid fa-plus"></i>
                </button>
              </div>
            </div><!-- row ended -->
            <div id="extra_before"></div>
            <?php
            $getbeforeimage = \app\models\TblClientReceiptImage::find()
            ->where(['fk_receipt_id' => $model->id, 'status' => 1])
            ->all();

            if (!empty($getbeforeimage)) {
              ?>
              <div class="row">
                <div class="col-12">

                  <ul class="sortable">
                    <?php foreach ($getbeforeimage as $gb):
                      $filePath = "receipt-doc/" . $gb->file_name;
                      $extension = strtolower(pathinfo($gb->file_name, PATHINFO_EXTENSION));
                      ?>
                      <li id="file_row_<?= $gb->id ?>" style="position:relative;">

                        <?php if ($extension === 'pdf'): ?>
                          <!-- PDF FILE -->
                          <div style="width:100px; text-align:center;">
                            <i class="fas fa-file-pdf fa-3x text-danger"></i><br>
                            <a href="<?= $filePath ?>" target="_blank" class="btn btn-sm btn-primary mt-1">
                              View PDF
                            </a>
                          </div>

                        <?php else: ?>
                          <!-- IMAGE FILE -->
                          <a style="cursor:pointer;" class="img_open_popup" id="<?= $gb->id ?>">
                            <input type="hidden" id="img_<?= $gb->id ?>" value="<?= $filePath ?>">
                            <img src="<?= $filePath ?>" alt="Media" style="width:100px;height:auto;">
                          </a>
                        <?php endif; ?>

                        <!-- existing image id -->
                        <input type="hidden" name="image_ids[]" value="<?= $gb->id ?>">

                        <!-- delete button -->
                        <button type="button"
                        class="delete-button btn btn-sm btn-danger"
                        data-id="<?= $gb->id ?>"
                        style="position:absolute; top:0; right:0;">
                        <i class="fas fa-trash"></i>
                      </button>

                    </li>
                  <?php endforeach; ?>
                </ul>

              </div>
            </div>
          <?php } ?>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary btn-sm">Submit</button>
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>

      </div>
    </form>
  </div>
</div>
</div>
<?php
$this->registerJs("
var deletedIds = [];

$(document).on('click', '.delete-button', function () {
var id = $(this).data('id');

if (deletedIds.indexOf(id) === -1) {
  deletedIds.push(id);
}

$('#deleted_image_ids').val(deletedIds.join(','));

// hide row visually
$('#file_row_' + id).fadeOut(300);
});
");
?>
<style>
.sortable {
list-style-type: none;
margin: 0;
padding: 0;
display: flex;
flex-wrap: wrap;
gap: 10px; /* Space between items */
}

.sortable li {
position: relative;
background-color: #f8f9fa; /* Light gray background */
border-radius: 5px;
padding: 10px;
width: 200px; /* Set a fixed width for consistency */
box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
text-align: center;
}

.sortable li img {
width: 100%; /* Make the image fill the li width */
height: auto;
border-radius: 5px;
}

.delete-button {
position: absolute;
top: 5px;
right: 5px;
background: rgba(220, 53, 69, 0.9); /* Semi-transparent red */
border: none;
color: white;
cursor: pointer;
border-radius: 50%;
width: 24px;
height: 24px;
display: flex;
align-items: center;
justify-content: center;
padding: 0;
}


</style>


<?php
$this->registerJs('
$(document).on("click",".btn_add_before_image",function(e){
e.preventDefault();
let content = "<div class=\"row mt-2 rows_extra_before\"><div class=\"col-lg-8 mb-2\"><label>Document</label><input type=\"file\" name=\"file_url[]\" class=\"form-control\" accept=\"image/*,application/pdf\"></div><div class=\"col-lg-4 mt-4 mb-2\"><button type=\"button\" name=\"button\" class=\"btn btn-secondary btn-sm btn_add_before_image\" ><i class=\"fa-solid fa-plus\"></i></button>&nbsp;<button type=\"button\" name=\"button\" class=\"btn btn-secondary btn-sm btn_remove_before_image\"><i class=\"fa-solid fa-minus\"></i></button></div></div>";
$("#extra_before").append(content);
});
//-------BEFORE IMAGE REMOVE ROW -----

$(document).on("click",".btn_remove_before_image",function(e){
e.preventDefault();
$(this).parent().parent(".rows_extra_before").remove();
});
');
?>


<!-- product image modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-fullscreen">
<div class="modal-content">
  <div class="modal-header">
    <h2 class="modal-title" id="exampleModalLabel">Document</h2>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
  </div>
  <div class="modal-body">
    <div class="row">
      <div class="col-lg-12" id="product_img_div" align="center">

      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
  </div>
</div>
</div>
</div>
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
<!-- Modal for PDF display -->
<div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
<div class="modal-dialog modal-fullscreen" role="document">
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
