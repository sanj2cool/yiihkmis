<?php

use app\models\TblOrder;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\TblOrderStatus;
/** @var yii\web\View $this */
/** @var app\models\TblOrderSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
$template = '{view}';
use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>71,'status'=>1])->one();
if(isset($menuaccess) && $menuaccess->edit_crud == 1){
  $template .= ' {edit}';
}
if(isset($menuaccess) && $menuaccess->delete_crud == 1){
  $template .= ' {delete}';
}

$allstatuses = TblOrderStatus::find()->where(['status'=>1])->orderBy(['title'=>SORT_ASC])->all();
$statusarr = ArrayHelper::map($allstatuses,'id','title');

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
        <?= Html::a('Create Order', ['create'], ['class' => 'btn btn-primary btn-sm']) ?>
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
    <div class="tbl-order-index table-responsive">
      <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
          // ['class' => 'yii\grid\SerialColumn'],
          [
            'class' => ActionColumn::className(),
            'template' => $template,
            'buttons' => [
              'view' => function ($url, $model) {
                return Html::a('<i class=" fas fa-eye"></i>', $url, [
                  'title' => Yii::t('app', 'view'),
                  'class' => 'text-info'
                ]);
              },
              'edit' => function ($url, $model) {
                if($model->order_status < 3){
                  return Html::a('<i class=" fas fa-pencil-alt"></i>', $url, [
                    'title' => Yii::t('app', 'edit'),
                    'class' => 'text-primary'
                  ]);
                }else{
                  return '';
                }

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
                  if($model->order_status != 3 && $model->order_status != 5){
                      return Html::a('<i class="fas fa-trash"></i>', $url, $options);
                  }else{
                    return '';
                  }

              }
            ],
            'urlCreator' => function ($action, $model, $key, $index) {
              if ($action === 'edit') {
                $url ='index.php?r=order/update&id='.$model->id;
                return $url;
              }
              if ($action === 'view') {
                $url ='index.php?r=order/view&id='.$model->id;
                return $url;
              }
              if ($action === 'delete') {
                $url ='index.php?r=order/delete&id='.$model->id;
                return $url;
              }
            }
          ],
          // 'id',
          'order_number',
          // 'fk_client_id',
          [
            'attribute' => 'client',
            'value' => 'client.company_name'
          ],
          'order_date',
          // 'hst',
          //'subtotal',
          //'hst_amount',
          'total_amount',
          [
            'attribute' => 'order_status',
            'format' => 'raw',
            'value' => function($dataProvider){
              $getprepby = TblOrderStatus::find()->where(['id'=>$dataProvider->order_status])->one();
              if(isset($getprepby) && $getprepby->title != ""){
                return '<span class="badge '.$getprepby->description.'">'.$getprepby->title.'</span>';
              }else{
                return "(not set)";
              }
            },
            'filter' => Html::activeDropDownList($searchModel, 'order_status', $statusarr,['class'=>'form-select','prompt' => 'Select', 'encode' => false]),
          ],
          //'comments:ntext',
          //'order_status',
          //'status',
          //'ip',
          //'crt_time',
          //'crt_by',
          //'mod_time',
          //'mod_by',
          // [
          //   'class' => ActionColumn::className(),
          //   'urlCreator' => function ($action, TblOrder $model, $key, $index, $column) {
          //     return Url::toRoute([$action, 'id' => $model->id]);
          //   }
          // ],
        ],
      ]); ?>


    </div>

  </div>

</div>
