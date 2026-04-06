<?php

namespace app\controllers;

use app\models\TblSlider;
use app\models\TblSliderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
use app\models\TblMenuAccess;
/**
 * SliderController implements the CRUD actions for TblSlider model.
 */
class SliderController extends Controller
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
        $menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>73,'status'=>1])->one();
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
     * Lists all TblSlider models.
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
        $searchModel = new TblSliderSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TblSlider model.
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
     * Creates a new TblSlider model.
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
        $model = new TblSlider();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                if(isset($_POST['hd_img_1']) && $_POST['hd_img_1'] != ""){
                  $model->url = $_POST['hd_img_1'];
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
                    Yii::$app->getSession()->setFlash('error','Something went wrong. Please try again.');
                    //-----not saved ---------
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                  }
                }else{
                    Yii::$app->getSession()->setFlash('error','Please upload the file and try again.');
                  //----- no image ---------
                  return $this->render('create', [
                      'model' => $model,
                  ]);
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
     * Updates an existing TblSlider model.
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
          //------
          $modelUpdate = TblSlider::find()->where(['id'=>$model->id])->one();
          $modelUpdate->url = $_POST['hd_img_1'];
          $modelUpdate->save();

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
     * Deletes an existing TblSlider model.
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
        $model = TblSlider::find()->where(['id'=>$id])->one();
        $model->status = 0;
        if($model->save()){
          Yii::$app->getSession()->setFlash('success', 'Record deleted successfully.');
        }else{
          Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the TblSlider model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return TblSlider the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblSlider::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionUploadfile(){
      //first try with simple uploading and then move on to with and without watermark uploading
      $data = $_POST['image'];

      $image_array_1 = explode(";", $data);
      $image_array_2 = explode(",", $image_array_1[1]);
      $data = base64_decode($image_array_2[1]);

      $getpos = strpos($image_array_1[0],"/");
      $imgtype = substr($image_array_1[0],$getpos+1);
      // echo $imgtype;
      // die();
      // echo $imagetype;
      $image_name = '../web/slider-images/' . time() . '.'.$imgtype;
      file_put_contents($image_name, $data);
      $imgname = time().'.'.$imgtype;

      echo $imgname;

    }
}
