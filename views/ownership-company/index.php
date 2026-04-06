<?php

use app\models\TblOwnershipCompany;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\TblOwnershipCompanySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Tbl Ownership Companies';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-ownership-company-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Tbl Ownership Company', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'company_name',
            'email:email',
            'phone',
            'address',
            //'city',
            //'state',
            //'postal_code',
            //'country',
            //'hst_number',
            //'status',
            //'ip',
            //'crt_time',
            //'crt_by',
            //'mod_time',
            //'mod_by',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, TblOwnershipCompany $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
