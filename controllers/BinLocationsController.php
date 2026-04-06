<?php

namespace app\controllers;
use Yii;
use app\models\TblBinLocations;
use app\models\TblBinLocationsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\TblMenuAccess;
/**
 * BinLocationsController implements the CRUD actions for TblBinLocations model.
 */
class BinLocationsController extends Controller
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
    public function beforeAction($action) {
        $session = Yii::$app->session;
        if(!isset($session['username'])){
          Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
          return $this->redirect(['site/login']);
        }
        $fk_user_id = $session['userId'];
        $menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>68,'status'=>1])->one();
        if($this->action->id == "index"){
          //check if user has permission to access the page
          if(isset($menuaccess) && $menuaccess->view_crud == 1){

          }else{
            return $this->redirect(['site/index']);
          }
        }else if($this->action->id == "create"){
          if(isset($menuaccess) && $menuaccess->create_crud == 1){

          }else{
            return $this->redirect(['site/index']);
          }
        }else if($this->action->id == "update"){

          if(isset($menuaccess) && $menuaccess->edit_crud == 1){

          }else{
            return $this->redirect(['site/index']);
          }
        }else if($this->action->id == "delete"){

          if(isset($menuaccess) && $menuaccess->delete_crud == 1){

          }else{
            return $this->redirect(['site/index']);
          }
        }
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
      }
    /**
     * Lists all TblBinLocations models.
     *
     * @return string
     */
    public function actionIndex()
    {
      $session = Yii::$app->session;
      if(!isset($session['username'])){
        // Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
        return $this->redirect(['site/login']);
      }

        $searchModel = new TblBinLocationsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TblBinLocations model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
      $session = Yii::$app->session;
      if(!isset($session['username'])){
        // Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
        return $this->redirect(['site/login']);
      }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TblBinLocations model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
      $session = Yii::$app->session;
      if(!isset($session['username'])){
        // Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
        return $this->redirect(['site/login']);
      }

        $model = new TblBinLocations();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
              //--------need to check if same entry already exists --------
              $existingEntry = TblBinLocations::find()
                  // ->where([
                  //     'LOWER(area)' => strtolower($model->area),
                  //     'LOWER(row)' => strtolower($model->row),
                  //     'LOWER(bay)' => strtolower($model->bay),
                  //     'LOWER(level)' => strtolower($model->level),
                  //     'LOWER(position)' => strtolower($model->position),
                  // ])
                  ->where(['status'=>1])
                  ->andWhere('LOWER(area) = :area', [':area' => strtolower($model->area)])
                   ->andWhere('LOWER(`row`) = :row', [':row' => strtolower($model->row)])
                   ->andWhere('LOWER(bay) = :bay', [':bay' => strtolower($model->bay)])
                   ->andWhere('LOWER(level) = :level', [':level' => strtolower($model->level)])
                   ->andWhere('LOWER(position) = :position', [':position' => strtolower($model->position)])
                  ->all(); // Use `one()` to get a single result
                  if(isset($existingEntry) && count($existingEntry) > 0){
                    Yii::$app->getSession()->setFlash('error', 'The entry already exists on the given values.');
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                  }else{
                    if($model->save()){
                      if(isset($_POST['new_update'])){
                          return $this->redirect(['update', 'id' => $model->id]);
                      }else if(isset($_POST['new_new'])){
                          return $this->redirect(['create']);
                      }else if(isset($_POST['new_exit'])){
                          return $this->redirect(['index']);
                      }else{
                          return $this->redirect(['update', 'id' => $model->id]);
                      }
                    }else{
                      Yii::$app->getSession()->setFlash('error', 'Technical Error. Please try again');
                      return $this->render('create', [
                          'model' => $model,
                      ]);
                    }

                  }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TblBinLocations model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
      $session = Yii::$app->session;
      if(!isset($session['username'])){
        // Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
        return $this->redirect(['site/login']);
      }

        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post())) {
          //--------need to check if same entry already exists --------
          $existingEntry = TblBinLocations::find()
              ->where(['status'=>1])
              ->andWhere('LOWER(area) = :area', [':area' => strtolower($model->area)])
               ->andWhere('LOWER(`row`) = :row', [':row' => strtolower($model->row)])
               ->andWhere('LOWER(bay) = :bay', [':bay' => strtolower($model->bay)])
               ->andWhere('LOWER(level) = :level', [':level' => strtolower($model->level)])
               ->andWhere('LOWER(position) = :position', [':position' => strtolower($model->position)])
              ->andWhere('id != '.$model->id)
              ->all(); // Use `one()` to get a single result
              if(isset($existingEntry) && count($existingEntry) > 0){
                Yii::$app->getSession()->setFlash('error', 'The entry already exists on the given values.');
                return $this->render('update', [
                    'model' => $model,
                ]);
              }else{
                if($model->save()){
                  if(isset($_POST['update'])){
                      return $this->redirect(['update', 'id' => $model->id]);
                  }else if(isset($_POST['new'])){
                      return $this->redirect(['create']);
                  }else if(isset($_POST['exit'])){
                      return $this->redirect(['index']);
                  }else{
                      return $this->redirect(['update', 'id' => $model->id]);
                  }
                }else {
                  // code...
                  Yii::$app->getSession()->setFlash('error', 'Technical Error. Please try again');
                  return $this->render('update', [
                      'model' => $model,
                  ]);
                }
              }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TblBinLocations model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
      $session = Yii::$app->session;
      if(!isset($session['username'])){
        // Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
        return $this->redirect(['site/login']);
      }

        // $this->findModel($id)->delete();
        $model = TblBinLocations::find()->where(['id'=>$id])->one();
        $model->status = 0;
        if($model->save()){
          Yii::$app->getSession()->setFlash('success', 'Record deleted successfully');
        }else{
          Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblBinLocations model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return TblBinLocations the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblBinLocations::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
