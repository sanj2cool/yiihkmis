<?php

namespace app\controllers;
use Yii;
use app\models\TblProductCatalog;
use app\models\TblProductCatalogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * ProductCatalogController implements the CRUD actions for TblProductCatalog model.
 */
class ProductCatalogController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all TblProductCatalog models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new TblProductCatalogSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TblProductCatalog model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TblProductCatalog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    // public function actionCreate()
    // {
    //     $model = new TblProductCatalog();
    //
    //     if ($this->request->isPost) {
    //         if ($model->load($this->request->post()) && $model->save()) {
    //             return $this->redirect(['view', 'id' => $model->id]);
    //         }
    //     } else {
    //         $model->loadDefaultValues();
    //     }
    //
    //     return $this->render('create', [
    //         'model' => $model,
    //     ]);
    // }

    /**
     * Updates an existing TblProductCatalog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TblProductCatalog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    // public function actionDelete($id)
    // {
    //     $this->findModel($id)->delete();
    //
    //     return $this->redirect(['index']);
    // }
    public function actionCreate()
     {
         $model = new TblProductCatalog();
         $model->is_active = 0;

         if ($model->load(Yii::$app->request->post())) {
             $model->pdf_file = UploadedFile::getInstance($model, 'pdf_file');

             if ($model->upload()) {

                 // Deactivate existing active catalog
                 TblProductCatalog::updateAll(['is_active' => 0], ['is_active' => 1]);

                 $model->is_active = 1;
                 // $model->created_by = Yii::$app->user->id;

                 if ($model->save(false)) {
                     Yii::$app->session->setFlash('success', 'Catalog uploaded successfully.');
                     return $this->redirect(['index']);
                 }
             }
         }

         return $this->render('create', ['model' => $model]);
     }

     public function actionSetActive($id)
     {
         TblProductCatalog::updateAll(['is_active' => 0], ['is_active' => 1]); // deactivate old

         $model = TblProductCatalog::findOne($id);
         $model->is_active = 1;
         $model->save(false);

         Yii::$app->session->setFlash('success', 'Catalog set as active.');
         return $this->redirect(['index']);
     }
     public function actionDeactivate($id)
     {
         $model = TblProductCatalog::findOne($id);

         if ($model && $model->is_active == 1) {
             $model->is_active = 0;
             $model->save();

             Yii::$app->session->setFlash('success', 'Catalog has been deactivated.');
         }

         return $this->redirect(['index']);
     }

     public function actionDelete($id)
     {
       $session = Yii::$app->session;
       if(!isset($session['username'])){
         Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
         return $this->redirect(['site/login']);
       }
         // $this->findModel($id)->delete();
         $model = TblProductCatalog::find()->where(['id'=>$id])->one();
         $model->status = 0;
         if($model->save()){
           Yii::$app->getSession()->setFlash('success', 'Record deleted successfully.');
         }else{
           Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
         }
         return $this->redirect(['index']);
     }

    /**
     * Finds the TblProductCatalog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return TblProductCatalog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblProductCatalog::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
