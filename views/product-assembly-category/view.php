<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\TblProductAssemblyCategory $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Product Assembly Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tbl-product-assembly-category-view">
  <div class="card shadow rounded mt-2">
      <div class="card-body p-3">
      <div class="row">
        <div class="col-lg-6">
          <h3><?= Html::encode($this->title) ?></h3>
        </div>
        <div class="col-lg-6 text-end">
          <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-success btn-sm']) ?>
              <?= Html::a('Back to List', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
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
            'description:ntext',
            // 'image_url:url',
            [
              'attribute' => 'image_url',
              'format' => 'html',
              'value' => function ($model) {
                if($model->image_url != ''){
                  return Html::img('assembly-category/'.$model->image_url, [
                      'width' => '80',
                      'height' => '80',
                      'style' => 'object-fit: cover; border-radius: 8px;'
                  ]);
                }else{
                  return '';
                }
              },
          ],
          [
            'attribute' => 'exploded_image_url',
            'format' => 'html',
            'value' => function ($model) {
              if($model->exploded_image_url != ''){
                return Html::img('assembly-category/'.$model->exploded_image_url, [
                    'width' => '80',
                    'height' => '80',
                    'style' => 'object-fit: cover; border-radius: 8px;'
                ]);
              }else{
                return '';
              }

            },
        ],
          [
            'attribute' => 'status',
            'format' => 'raw',
            'value' => function($model){
              return $model->status == 1 ? '<span class="badge bg-success">Active</span>' : ($model->status == 2 ? '<span class="badge bg-danger">Inactive</span>' : '<i>(not set)</i>');
            },

          ],
            // 'show_on_website',
            // 'img_width',
            // 'translate_x',
            // 'translate_y',
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
