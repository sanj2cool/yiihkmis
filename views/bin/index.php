<?php

use app\models\TblBin;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\TblBinSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Tbl Bins';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-bin-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Tbl Bin', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'fk_area_id',
            'fk_row_id',
            'fk_bay_id',
            'fk_level_id',
            //'fk_position_id',
            //'ip',
            //'status',
            //'crt_by',
            //'mod_by',
            //'crt_time',
            //'mod_time',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, TblBin $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
