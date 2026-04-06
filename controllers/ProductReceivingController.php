<?php

namespace app\controllers;
use Yii;
use app\models\TblProductReceiving;
use app\models\TblProductReceivingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\TblMenuAccess;
/**
 * ProductReceivingController implements the CRUD actions for TblProductReceiving model.
 */
class ProductReceivingController extends BaseController
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
    // public function beforeAction($action) {
    //     $session = Yii::$app->session;
    //     if(!isset($session['username'])){
    //       Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
    //       return $this->redirect(['site/login']);
    //     }
    //     $fk_user_id = $session['userId'];
    //     $menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>59,'status'=>1])->one();
    //     if($this->action->id == "index"){
    //       //check if user has permission to access the page
    //       if(isset($menuaccess) && $menuaccess->view_crud == 1){
    //
    //       }else{
    //         return $this->redirect(['site/index']);
    //       }
    //     }else if($this->action->id == "create"){
    //       if(isset($menuaccess) && $menuaccess->create_crud == 1){
    //
    //       }else{
    //         return $this->redirect(['site/index']);
    //       }
    //     }else if($this->action->id == "update"){
    //
    //       if(isset($menuaccess) && $menuaccess->edit_crud == 1){
    //
    //       }else{
    //         return $this->redirect(['site/index']);
    //       }
    //     }else if($this->action->id == "delete"){
    //
    //       if(isset($menuaccess) && $menuaccess->delete_crud == 1){
    //
    //       }else{
    //         return $this->redirect(['site/index']);
    //       }
    //     }
    //     $this->enableCsrfValidation = false;
    //     return parent::beforeAction($action);
    //   }
    /**
     * Lists all TblProductReceiving models.
     *
     * @return string
     */
    public function actionIndex()
    {
      $session = Yii::$app->session;
        if(!isset($session['username'])){
          Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
          return $this->redirect(['site/login']);
        }
        $searchModel = new TblProductReceivingSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionResetFilters()
    {
        $searchModel = new TblProductReceivingSearch();
        $searchModel->clearSearchState(); // Clear the session data
        return $this->redirect(['index']); // Redirect back to the index page
    }
    /**
     * Displays a single TblProductReceiving model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
      $session = Yii::$app->session;
        if(!isset($session['username'])){
          Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
          return $this->redirect(['site/login']);
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionViewTest($id)
    {
      $session = Yii::$app->session;
        if(!isset($session['username'])){
          Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
          return $this->redirect(['site/login']);
        }
        return $this->render('view-test', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TblProductReceiving model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
      $session = Yii::$app->session;
        if(!isset($session['username'])){
          Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
          return $this->redirect(['site/login']);
        }
          return $this->redirect(['index']);
          
        $model = new TblProductReceiving();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                // return $this->redirect(['view', 'id' => $model->id]);
              if(isset($_POST['new_update'])){
                  return $this->redirect(['update', 'id' => $model->id]);
              }else if(isset($_POST['new_new'])){
                  return $this->redirect(['create']);
              }else if(isset($_POST['new_exit'])){
                  return $this->redirect(['index']);
              }else{
                  return $this->redirect(['update', 'id' => $model->id]);
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
     * Updates an existing TblProductReceiving model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
      $session = Yii::$app->session;
        if(!isset($session['username'])){
          Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
          return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            // return $this->redirect(['view', 'id' => $model->id]);
            if(isset($_POST['update'])){
                return $this->redirect(['update', 'id' => $model->id]);
            }else if(isset($_POST['new'])){
                return $this->redirect(['create']);
            }else if(isset($_POST['exit'])){
                return $this->redirect(['index']);
            }else{
                return $this->redirect(['update', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TblProductReceiving model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
      $session = Yii::$app->session;
        if(!isset($session['username'])){
          Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
          return $this->redirect(['site/login']);
        }
        // $this->findModel($id)->delete();
        //---IF THE ENTRY HAS CONNECTION WITH INVOICE_ID IT SHOULD NOT BE DELETED HERE ----

        $model = TblProductReceiving::find()->where(['id'=>$id])->one();
        if(empty($model->fk_vendor_invoice_id)){
          $model->status = 0;
          if($model->save()){
            Yii::$app->getSession()->setFlash('success', 'Record deleted successfully.');
          }else{
            Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
          }
        }else{
          Yii::$app->getSession()->setFlash('error', 'This record is related to Purchase Invoice and can not be deleted from this module. Please go to related purchase invoice.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblProductReceiving model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return TblProductReceiving the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblProductReceiving::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionEditEntry($id){
      $session = Yii::$app->session;
      if(!isset($session['username'])){
        // Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
        return $this->redirect(['site/login']);
      }

      if(Yii::$app->request->post()){

        $bin_location = $_POST['bin_location'] ?? '';
        $remarks = $_POST['remarks'] ?? '';
        if(!empty($bin_location) || !empty($remarks)){
          $modelReceipt = TblProductReceiving::find()->where(['id'=>$id])->one();
          $modelReceipt->bin_location = $bin_location;
          $modelReceipt->remarks = $remarks;
          if($modelReceipt->save()){

            Yii::$app->getSession()->setFlash('success', 'Record updated successfully.');
          }else{
            Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
          }
        }else{
          Yii::$app->getSession()->setFlash('error', 'Please add bin location or remarks.');
        }


      }else{
        //---need bank account selected -----
        Yii::$app->getSession()->setFlash('error', 'Please add bin location or remarks.');
      }
      return $this->redirect(['view-test', 'id' => $id]);
    }
}
