<?php

namespace app\controllers;
use Yii;
use app\models\TblProductAssemblyCategory;
use app\models\TblProductAssemblyCategorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use app\models\TblMenuAccess;

/**
 * ProductAssemblyCategoryController implements the CRUD actions for TblProductAssemblyCategory model.
 */
class ProductAssemblyCategoryController extends Controller
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
        $menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>23,'status'=>1])->one();
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
     * Lists all TblProductAssemblyCategory models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new TblProductAssemblyCategorySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TblProductAssemblyCategory model.
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
     * Creates a new TblProductAssemblyCategory model.
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
        $model = new TblProductAssemblyCategory();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
              $msg_doc = "";
              $target_dir = "../web/assembly-category/";
              if(isset($_FILES["cat_img"]) && $_FILES["cat_img"]["name"] != ""){
                $mc=md5(microtime());
                $f_name = $mc.basename($_FILES["cat_img"]["name"]);
                $basename = basename($_FILES["cat_img"]["name"]);
                $file_name = $this->clean($f_name);
                $target_file = $target_dir.$file_name;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                      && $imageFileType != "gif" && $imageFileType != "webp") {
                    $msg_doc = "Sorry, only Image files are allowed.";
                }else{
                  if(move_uploaded_file($_FILES["cat_img"]["tmp_name"], $target_file)){
                    $modelPic = TblProductAssemblyCategory::find()->where(['id'=>$model->id])->one();
                    $modelPic->image_url = $file_name;
                    $modelPic->save();
                  }
                }//-----else for checking file type ended ----
              }


              if(isset($_FILES["second_level_img"]) && $_FILES["second_level_img"]["name"] != ""){
                $mc=md5(microtime());
                $f_name = $mc.basename($_FILES["second_level_img"]["name"]);
                $basename = basename($_FILES["second_level_img"]["name"]);
                $file_name = $this->clean($f_name);
                $target_file = $target_dir.$file_name;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                      && $imageFileType != "gif" && $imageFileType != "webp") {
                    $msg_doc = "Sorry, only Image files are allowed.";
                }else{
                  if(move_uploaded_file($_FILES["second_level_img"]["tmp_name"], $target_file)){
                    $modelPic = TblProductAssemblyCategory::find()->where(['id'=>$model->id])->one();
                    $modelPic->exploded_image_url = $file_name;
                    $modelPic->save();
                  }
                }//-----else for checking file type ended ----
              }

              if($msg_doc != ""){
                Yii::$app->getSession()->setFlash('error', $msg_doc);
                return $this->redirect(['update', 'id' => $model->id]);
              }else{
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
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TblProductAssemblyCategory model.
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

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
          $msg_doc = "";
          $target_dir = "../web/assembly-category/";
          if(isset($_FILES["cat_img"]) && $_FILES["cat_img"]["name"] != ""){
            $mc=md5(microtime());
            $f_name = $mc.basename($_FILES["cat_img"]["name"]);
            $basename = basename($_FILES["cat_img"]["name"]);
            $file_name = $this->clean($f_name);
            $target_file = $target_dir.$file_name;
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                  && $imageFileType != "gif" && $imageFileType != "webp") {
                $msg_doc = "Sorry, only Image files are allowed.";
            }else{
              if(move_uploaded_file($_FILES["cat_img"]["tmp_name"], $target_file)){
                $modelPic = TblProductAssemblyCategory::find()->where(['id'=>$model->id])->one();
                $modelPic->image_url = $file_name;
                $modelPic->save();
              }
            }//-----else for checking file type ended ----
          }
          if(isset($_FILES["second_level_img"]) && $_FILES["second_level_img"]["name"] != ""){
            $mc=md5(microtime());
            $f_name = $mc.basename($_FILES["second_level_img"]["name"]);
            $basename = basename($_FILES["second_level_img"]["name"]);
            $file_name = $this->clean($f_name);
            $target_file = $target_dir.$file_name;
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                  && $imageFileType != "gif" && $imageFileType != "webp") {
                $msg_doc = "Sorry, only Image files are allowed.";
            }else{
              if(move_uploaded_file($_FILES["second_level_img"]["tmp_name"], $target_file)){
                $modelPic = TblProductAssemblyCategory::find()->where(['id'=>$model->id])->one();
                $modelPic->exploded_image_url = $file_name;
                $modelPic->save();
              }
            }//-----else for checking file type ended ----
          }
          if($msg_doc != ""){
            Yii::$app->getSession()->setFlash('error', $msg_doc);
            return $this->redirect(['update', 'id' => $model->id]);
          }else{
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
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TblProductAssemblyCategory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblProductAssemblyCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return TblProductAssemblyCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblProductAssemblyCategory::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    function clean($string) {
      $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
      return preg_replace('/[^A-Za-z0-9.\-]/', '', $string); // Removes special chars.
    }
}
