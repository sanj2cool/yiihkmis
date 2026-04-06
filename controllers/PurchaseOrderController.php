<?php

namespace app\controllers;
use Yii;
use app\models\TblPurchaseOrder;
use app\models\TblPurchaseOrderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\TblPurchaseOrderItem;
use app\models\TblMenuAccess;
/**
 * PurchaseOrderController implements the CRUD actions for TblPurchaseOrder model.
 */
class PurchaseOrderController extends Controller
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
        $menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>11,'status'=>1])->one();
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
     * Lists all TblPurchaseOrder models.
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
        $searchModel = new TblPurchaseOrderSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionResetFilters()
    {
        $searchModel = new TblPurchaseOrderSearch();
        $searchModel->clearSearchState(); // Clear the session data
        return $this->redirect(['index']); // Redirect back to the index page
    }
    /**
     * Displays a single TblPurchaseOrder model.
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

    /**
     * Creates a new TblPurchaseOrder model.
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
        $model = new TblPurchaseOrder();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
              //---------invoice items entries start -------------
              $product = Yii::$app->request->post('product');
              $description = Yii::$app->request->post('desc');
              $qty = Yii::$app->request->post('qty');
              $rate = Yii::$app->request->post('rate');
              $amount = Yii::$app->request->post('amount');
              if(isset($product) && count($product) > 0){
                for($i = 0; $i < count($product); $i++){
                  $modelItem[$i] = new TblPurchaseOrderItem();
                  $modelItem[$i]->fk_purchase_order_id = $model->id;
                  $modelItem[$i]->fk_product_id = $product[$i];
                  $modelItem[$i]->description = $description[$i];
                  $modelItem[$i]->quantity = $qty[$i];
                  $modelItem[$i]->unit_price = $rate[$i];
                  $modelItem[$i]->total_price = $amount[$i];
                  if($modelItem[$i]->save()){
                  }else{
                    // print_r($modelItem[$i]->getErrors());
                    // die();
                  }
                }//----for loop ended-------
              }//-----items exists-------
              //---------invoice items entries end ---------------
              if(isset($_POST['new_update'])){
                  return $this->redirect(['update', 'id' => $model->id]);
              }else if(isset($_POST['new_new'])){
                  return $this->redirect(['create']);
              }else if(isset($_POST['new_exit'])){
                  return $this->redirect(['index']);
              }else{
                  return $this->redirect(['update', 'id' => $model->id]);
              }
                // return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TblPurchaseOrder model.
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
          //---------invoice items entries start -------------
          TblPurchaseOrderItem::updateAll(array('status' => 0),'fk_purchase_order_id='.$model->id);
          $product = Yii::$app->request->post('product');
          $description = Yii::$app->request->post('desc');
          $qty = Yii::$app->request->post('qty');
          $rate = Yii::$app->request->post('rate');
          $amount = Yii::$app->request->post('amount');
          if(isset($product) && count($product) > 0){
            for($i = 0; $i < count($product); $i++){
              $modelItem[$i] = new TblPurchaseOrderItem();
              $modelItem[$i]->fk_purchase_order_id = $model->id;
              $modelItem[$i]->fk_product_id = $product[$i];
              $modelItem[$i]->description = $description[$i];
              $modelItem[$i]->quantity = $qty[$i];
              $modelItem[$i]->unit_price = $rate[$i];
              $modelItem[$i]->total_price = $amount[$i];
              if($modelItem[$i]->save()){
              }else{
                // print_r($modelItem[$i]->getErrors());
                // die();
              }
            }//----for loop ended-------
          }//-----items exists-------
          //---------invoice items entries end ---------------

          if(isset($_POST['update'])){
              return $this->redirect(['update', 'id' => $model->id]);
          }else if(isset($_POST['new'])){
              return $this->redirect(['create']);
          }else if(isset($_POST['exit'])){
              return $this->redirect(['index']);
          }else{
              return $this->redirect(['update', 'id' => $model->id]);
          }
            // return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TblPurchaseOrder model.
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
        $model = TblPurchaseOrder::find()->where(['id'=>$id])->one();
        $model->status = 0;
        if($model->save()){
          TblPurchaseOrderItem::updateAll(array('status' => 0),'fk_purchase_order_id='.$id);
          Yii::$app->getSession()->setFlash('success', 'Record deleted successfully.');
        }else{
          Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblPurchaseOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return TblPurchaseOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblPurchaseOrder::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
