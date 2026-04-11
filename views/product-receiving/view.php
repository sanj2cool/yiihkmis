<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use app\models\TblProduct;
use app\models\TblVendor;
use app\models\TblVendorInvoice;
use app\models\TblOwnershipCompany;
use app\models\TblBinLocations;
/** @var yii\web\View $this */
/** @var app\models\TblProductReceiving $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Product Receivings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>59,'status'=>1])->one();


?>
<div class="tbl-product-receiving-view">

    <div class="card shadow rounded mt-2">
        <div class="card-body p-3">
            <div class="row">
                <div class="col-lg-4">
                    <h3><?= Html::encode($this->title) ?></h3>
                </div>
                <div class="col-lg-8 text-end">
                    <?php
          if(isset($menuaccess) && $menuaccess->edit_crud == 1){
            ?>
                    <button
                        type="button"
                        class="btn btn-primary btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#summaryModal">
                        <i class="fas fa-pen"></i>
                        Update
                    </button>
                    <?php
          }
          //---no delete option for entries associated with the vendor invoices -----

          if(isset($menuaccess) && $menuaccess->delete_crud == 1 && empty($model->fk_vendor_invoice_id)){
            ?>
                    <?= Html::a('<i class="fas fa-trash"></i> Delete', ['delete', 'id' => $model->id], [
              'class' => 'btn btn-danger btn-sm',
              'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
              ],
              ]) ?>
                <?php
            }else{
              ?>
                    <?= Html::a('<i class="fas fa-trash"></i> Delete', ['#', 'id' => $model->id], [
                'class' => 'btn btn-danger btn-sm disabled',
                'data' => [
                  'confirm' => 'Are you sure you want to delete this item?',
                  'method' => 'post',
                ],
                ]) ?>

                    <?php
            }
            ?>
                    <?= Html::a('<i class="fas fa-chevron-left"></i> Back to List', ['index'], ['class' => 'btn btn-secondary btn-sm']) ?>
                    <?php
              if(!empty($model->fk_vendor_invoice_id)){
                ?>
                    <p class="mb-0">
                        This entry is related to purchase invoice and will be deleted from there only.
                    </p>
                    <?php
              }
             ?>
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

            <button
                class="btn-close"
                type="button"
                data-bs-dismiss="alert"
                aria-label="Close"></button>
        </div>
    <?php
      }else if(Yii::$app -> session -> getFlash('error')!=null){
        ?>
        <div class="alert alert-outline-danger d-flex align-items-center" role="alert">
            <span class="fas fa-times-circle text-danger fs-5 me-3"></span>
            <p class="mb-0 flex-1"><?= Yii::$app -> session -> getFlash('error'); ?></p>
            <button
                class="btn-close"
                type="button"
                data-bs-dismiss="alert"
                aria-label="Close"></button>
        </div>

        <?php
      }
      ?>
    </div>
    <div class="card shadow rounded mt-2">
        <div class="card-body p-3">
        <?= DetailView::widget([
          'model' => $model,
          'attributes' => [
            'id',
            // 'fk_product_id',
            [
              'attribute' => 'product',
              'value' => function($model) {
                return $model->product ? $model->product->name . ' (' . $model->product->internal_sku . ')' : null;
              },
              'label' => 'Item',
            ],
            // 'fk_vendor_invoice_id',
            [
              'attribute' => 'vendorinvoice',
              'value' => function($model){
                return $model->vendorinvoice ? $model->vendorinvoice->vendor_invoice_number : '';
              }
            ],
            [
              'attribute' => 'vendor',
              'value' => function($model){
                return $model->vendor ? $model->vendor->company_name : '';
              }
            ],
            'no_of_items',
            'price_per_item',
            'bin_location',
            // [
            //   'attribute' => 'fk_bin_id',
            //   'label' => 'BIN (Area - Location - Bay - Level - Position)',
            //   'value' => function($model){
            //     $getbin = TblBinLocations::find()->where(['id'=>$model->fk_bin_id,'status'=>1])->one();
            //     if(isset($getbin) && $getbin->id != ""){
            //       return $getbin->area.' - '.$getbin->row.' - '.$getbin->bay.' - '.$getbin->level.' - '.$getbin->position;
            //     }else{
            //       return "(not set)";
            //     }
            //   }
            // ],
            [
              'attribute' => 'fk_location_id',
              'value' => function($model){
                $getveninv = TblOwnershipCompany::find()->where(['id'=>$model->fk_location_id,'status'=>1])->one();
                if(isset($getveninv) && $getveninv->location_name != ""){
                  return $getveninv->location_name;
                }else{
                  return "(not set)";
                }
              },

            ],
            'remarks:ntext',
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

<div
    class="modal fade"
    id="summaryModal"
    tabindex="-1"
    aria-labelledby="summaryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title8" id="summaryModalLabel">Update</h3>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form
                name="frm_reservation"
                id=""
                action="<?= Yii::$app->urlManager->createUrl(['product-receiving/edit-entry','id'=>$model->id]) ?>"
                method="post"
                autocomplete="off">
                <input
                    type="hidden"
                    name="_csrf"
                    value="<?=Yii::$app->request->getCsrfToken()?>"/>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 mb-2">
                            <label for="">Bin Location</label>
                            <input
                                type="text"
                                name="bin_location"
                                value="<?=$model->bin_location?>"
                                class="form-control">

                        </div>
                        <div class="col-lg-12 mb-2">
                            <label for="">Remarks</label>
                            <textarea class="form-control" name="remarks" rows="3"><?=$model->remarks?></textarea>
                        </div>

                        <div class="col-lg-12 mb-2">
                            <label>Shipping</label>
                            <input
                                type="number"
                                step="0.01"
                                name="shipping"
                                value="<?= $model->shipping ?>"
                                class="form-control">
                        </div>

                        <div class="col-lg-12 mb-2">
                            <label>Other Charges</label>
                            <input
                                type="number"
                                step="0.01"
                                name="other_charges"
                                value="<?= $model->other_charges ?>"
                                class="form-control">
                        </div>

                        <div class="col-lg-12 mb-2">
                            <label>Custom Charges</label>
                            <input
                                type="number"
                                step="0.01"
                                name="custom_charges"
                                value="<?= $model->custom_charges ?>"
                                class="form-control">
                        </div>
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