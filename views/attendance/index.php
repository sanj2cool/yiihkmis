<?php

use app\models\TblAttendance;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\TblUser;
/** @var yii\web\View $this */
/** @var app\models\TblAttendanceSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Attendance Entries';
$this->params['breadcrumbs'][] = $this->title;

$session = Yii::$app->session;
$user_company = $session['userCompany'];

$allemps = TblUser::find()
->where('status != 0')
->andWhere('id in (select fk_user_id from tbl_user_location where status = 1 and fk_location_id = '.$user_company.')')
->orderBy(['username'=>SORT_ASC])->all();
$emparr = ArrayHelper::map($allemps,'id','username');

$template = '';
use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>69,'status'=>1])->one();
if(isset($menuaccess) && $menuaccess->edit_crud == 1){
  $template .= ' {edit}';
}
if(isset($menuaccess) && $menuaccess->delete_crud == 1){
  $template .= ' {delete}';
}
?>
<div class="card">
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
          <?= Html::a('Create Attendance Entry', ['create'], ['class' => 'btn btn-primary btn-sm me-1 mb-1']) ?>
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
    <div class="tbl-attendance-index table-responsive">

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
              'edit' => function ($url, $model) {
                return Html::a('<i class=" fas fa-pencil-alt"></i>', $url, [
                  'title' => Yii::t('app', 'edit'),
                ]);
              },
              'delete' => function ($url, $model, $key) {
                $options = [
                  'title' => Yii::t('yii', 'Delete'),
                  'aria-label' => Yii::t('yii', 'Delete'),
                  'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                  'data-method' => 'post',
                  'data-pjax' => '0',
                ];
                return Html::a('<i class="fas fa-trash text-danger"></i>', $url, $options);
              }
            ],
            'urlCreator' => function ($action, $model, $key, $index) {
              if ($action === 'edit') {
                $url ='index.php?r=attendance/update&id='.$model->id;
                return $url;
              }
              if ($action === 'delete') {
                $url ='index.php?r=attendance/delete&id='.$model->id;
                return $url;
              }
            }
          ],
          [
            'attribute' => 'fk_user_id',
            'value' => function($dataProvider){
              $getemployee = TblUser::find()->where(['id'=>$dataProvider->fk_user_id])->andWhere('status != 0')->one();
              if(isset($getemployee) && $getemployee->username != ""){
                return $getemployee->username;
              }else{
                return "(not set)";
              }
            },

            'filter' => Html::activeDropDownList($searchModel, 'fk_user_id', $emparr,['class'=>'form-select','prompt' => 'Select'])
          ],
          // 'id',
          'date',
          'in_time',
          'out_time',

          // 'fk_user_id',
          //'att_status',
          //'ip',
          //'status',
          //'crt_by',
          //'crt_time',
          //'mod_by',
          //'mod_time',
          // [
          //     'class' => ActionColumn::className(),
          //     'urlCreator' => function ($action, TblAttendance $model, $key, $index, $column) {
          //         return Url::toRoute([$action, 'id' => $model->id]);
          //      }
          // ],
        ],
      ]); ?>


    </div>
  </div>
</div>
