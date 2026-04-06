<?php

use app\models\TblTask;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\TblTaskStatus;
use app\models\TblUser;
/** @var yii\web\View $this */
/** @var app\models\TblTaskSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Tasks';
$this->params['breadcrumbs'][] = $this->title;
$template = '';

$allstatus = TblTaskStatus::find()->where('status = 1')->orderBy(['status_name'=>SORT_ASC])->all();
$statusarr = ArrayHelper::map($allstatus,'id','status_name');

$session = Yii::$app -> session;
$fk_location_id = $session['userCompany'];

$allemps = TblUser::find()
          ->where('status != 0')
          ->andWhere('id in (select fk_user_id from tbl_user_location where status = 1 and fk_location_id = '.$fk_location_id.')')
          ->orderBy(['username'=>SORT_ASC])->all();
$emparr = ArrayHelper::map($allemps,'id','username');

use app\models\TblMenuAccess;
$session = Yii::$app->session;
$fk_user_id = $session['userId'];
$menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>48,'status'=>1])->one();
if(isset($menuaccess) && $menuaccess->edit_crud == 1){
  $template .= ' {edit}';
}
if(isset($menuaccess) && $menuaccess->delete_crud == 1){
  $template .= ' {delete}';
}
$menuaccess1 = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>49,'status'=>1])->one();
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
              if(isset($menuaccess1) && $menuaccess1->create_crud == 1){
               ?>
                <?= Html::a('Create Task', ['create'], ['class' => 'btn btn-primary btn-sm me-1 mb-1']) ?>
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
<div class="tbl-task-index table-responsive">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
          [
              'class' => ActionColumn::className(),
              'template' => $template,
              'buttons' => [
                'edit' => function ($url, $model) {
                    return Html::a('<i class=" fas fa-pencil-alt text-primary"></i>', $url, [
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
                    $url ='index.php?r=task/update&id='.$model->id;
                    return $url;
                }
              if ($action === 'delete') {
                    $url ='index.php?r=task/delete&id='.$model->id;
                    return $url;
                }
              }
            ],
          // 'id',

          'title',
          'description:ntext',
          'due_date',
          'time',
          // 'assigned_to',
          [
            'attribute' => 'assigned_to',
            'value' => function($dataProvider){
              $getemployee = TblUser::find()->where(['id'=>$dataProvider->assigned_to])->andWhere('status != 0')->one();
              if(isset($getemployee) && $getemployee->username != ""){
                return $getemployee->username;
              }else{
                return "(not set)";
              }
            },

            'filter' => Html::activeDropDownList($searchModel, 'assigned_to', $emparr,['class'=>'form-select','prompt' => 'Select'])
          ],
          // 'status_id',
          [
            'attribute' => 'status_id',
            'format' => 'html',
            'value' => function($dataProvider){
              $getemployee = TblTaskStatus::find()->where(['id'=>$dataProvider->status_id])->andWhere('status != 0')->one();
              if(isset($getemployee) && $getemployee->status_name != ""){
                return '<span class="badge '.$getemployee->icon.'">'.$getemployee->status_name.'</span>';
              }else{
                return "(not set)";
              }
            },

            'filter' => Html::activeDropDownList($searchModel, 'status_id', $statusarr,['class'=>'form-select','prompt' => 'Select'])
          ],
        ],
    ]); ?>


</div>
</div>
</div>
