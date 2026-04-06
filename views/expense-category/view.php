<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\TblExpenseCategory $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Expense Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>79,'status'=>1])->one();

?>
<div class="tbl-expense-category-view">

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
            'title',
            'description:ntext',
            [
              'attribute' => 'status',
              'label' => 'Status',
              'format' => 'raw',
              'value' => function($model){
                  if($model->status == 1){
                    return '<span class="badge bg-success">Active</span>';
                  }else if($model->status == 2){
                    return '<span class="badge bg-danger">Inactive</span>';
                  }else{
                    return '(not set)';
                  }
              },

            ],
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
</div>
