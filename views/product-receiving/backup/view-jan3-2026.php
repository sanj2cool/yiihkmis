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
