<?php

namespace app\controllers;
use Yii;
use app\models\TblEmployee;
use app\models\TblEmployeeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\TblMenuAccess;
/**
 * EmployeeController implements the CRUD actions for TblEmployee model.
 */
class EmployeeController extends Controller
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
        $menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>25,'status'=>1])->one();
        if($this->action->id == "index"){
          //check if user has permission to access the page
          if(isset($menuaccess) && $menuaccess->view_crud == 1){

          }else{
            return $this->redirect(['site/index']);
          }
        }else if($this->action->id == "create"){
          $menuaccess_c = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>26,'status'=>1])->one();
          if(isset($menuaccess_c) && $menuaccess_c->create_crud == 1){

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
     * Lists all TblEmployee models.
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
        $searchModel = new TblEmployeeSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionResetFilters()
    {
        $searchModel = new TblEmployeeSearch();
        $searchModel->clearSearchState(); // Clear the session data
        return $this->redirect(['index']); // Redirect back to the index page
    }
    /**
     * Displays a single TblEmployee model.
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
     * Creates a new TblEmployee model.
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
        $model = new TblEmployee();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
              //-----------file upload start -------
              $msg_doc = "";
              $target_dir = "../web/emp-doc/";
              if(isset($_FILES["file_url"]["name"]) && $_FILES["file_url"]["name"] != ""){
                $mc=md5(microtime());
                $f_name = $mc.basename($_FILES["file_url"]["name"]);
                $basename = basename($_FILES["file_url"]["name"]);
                $file_name = $this->clean($f_name);
                $target_file = $target_dir.$file_name;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                      && $imageFileType != "gif" && $imageFileType != "webp" && $imageFileType != "pdf" && $imageFileType != "doc" && $imageFileType != "docx" && $imageFileType != "txt" ) {
                    $msg_doc = "For Driver's License - only Images and PDF files are allowed.";
                }else{
                  if(move_uploaded_file($_FILES["file_url"]["tmp_name"], $target_file)){
                    // $model->file_url = $file_name;
                    //---update the model to save the file ------
                    $updateModel = TblEmployee::find()->where(['id'=>$model->id])->one();
                    $updateModel->driver_license = $file_name;
                    $updateModel->save();
                  }//------if for move uploaded file ended------
                }//-----else ended -----------
              }


              //-------second file upload start ---------
              if(isset($_FILES["file_url_health"]["name"]) && $_FILES["file_url_health"]["name"] != ""){
                $mc1=md5(microtime());
                $f_name1 = $mc1.basename($_FILES["file_url_health"]["name"]);
                $basename1 = basename($_FILES["file_url_health"]["name"]);
                $file_name1 = $this->clean($f_name1);
                $target_file1 = $target_dir.$file_name1;
                $imageFileType1 = strtolower(pathinfo($target_file1,PATHINFO_EXTENSION));
                if($imageFileType1 != "jpg" && $imageFileType1 != "png" && $imageFileType1 != "jpeg"
                      && $imageFileType1 != "gif" && $imageFileType1 != "webp" && $imageFileType1 != "pdf" && $imageFileType1 != "doc" && $imageFileType1 != "docx" && $imageFileType1 != "txt" ) {
                    $msg_doc .= "For Health - only Images and PDF files are allowed.";
                }else{
                  if(move_uploaded_file($_FILES["file_url_health"]["tmp_name"], $target_file1)){
                    // $model->file_url = $file_name;
                    //---update the model to save the file ------
                    $updateModel = TblEmployee::find()->where(['id'=>$model->id])->one();
                    $updateModel->health_card = $file_name1;
                    $updateModel->save();
                  }//------if for move uploaded file ended------
                }//-----else ended -----------
              }

              //-----------file upload end -------
                // return $this->redirect(['view', 'id' => $model->id]);
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
     * Updates an existing TblEmployee model.
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
            //-----------file upload start -------
            $msg_doc = "";
            $target_dir = "../web/emp-doc/";
            if(isset($_FILES["file_url"]["name"]) && $_FILES["file_url"]["name"] != ""){
              $mc=md5(microtime());
              $f_name = $mc.basename($_FILES["file_url"]["name"]);
              $basename = basename($_FILES["file_url"]["name"]);
              $file_name = $this->clean($f_name);
              $target_file = $target_dir.$file_name;
              $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
              if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                    && $imageFileType != "gif" && $imageFileType != "webp" && $imageFileType != "pdf" && $imageFileType != "doc" && $imageFileType != "docx" && $imageFileType != "txt" ) {
                  $msg_doc = "For Driver's License - only Images and PDF files are allowed.";
              }else{
                if(move_uploaded_file($_FILES["file_url"]["tmp_name"], $target_file)){
                  // $model->file_url = $file_name;
                  //---update the model to save the file ------
                  $updateModel = TblEmployee::find()->where(['id'=>$model->id])->one();
                  $updateModel->driver_license = $file_name;
                  $updateModel->save();
                }//------if for move uploaded file ended------
              }//-----else ended -----------
            }

            //-------second file upload start ---------
            if(isset($_FILES["file_url_health"]["name"]) && $_FILES["file_url_health"]["name"] != ""){
              $mc1=md5(microtime());
              $f_name1 = $mc1.basename($_FILES["file_url_health"]["name"]);
              $basename1 = basename($_FILES["file_url_health"]["name"]);
              $file_name1 = $this->clean($f_name1);
              $target_file1 = $target_dir.$file_name1;
              $imageFileType1 = strtolower(pathinfo($target_file1,PATHINFO_EXTENSION));
              if($imageFileType1 != "jpg" && $imageFileType1 != "png" && $imageFileType1 != "jpeg"
                    && $imageFileType1 != "gif" && $imageFileType1 != "webp" && $imageFileType1 != "pdf" && $imageFileType1 != "doc" && $imageFileType1 != "docx" && $imageFileType1 != "txt" ) {
                  $msg_doc .= "For Health - only Images and PDF files are allowed.";
              }else{
                if(move_uploaded_file($_FILES["file_url_health"]["tmp_name"], $target_file1)){
                  // $model->file_url = $file_name;
                  //---update the model to save the file ------
                  $updateModel = TblEmployee::find()->where(['id'=>$model->id])->one();
                  $updateModel->health_card = $file_name1;
                  $updateModel->save();
                }//------if for move uploaded file ended------
              }//-----else ended -----------
            }

            //-----------file upload end -------
            // return $this->redirect(['view', 'id' => $model->id]);
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
     * Deletes an existing TblEmployee model.
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
        $model = TblEmployee::find()->where(['id'=>$id])->one();
        $model->status = 0;
        if($model->save()){
          Yii::$app->getSession()->setFlash('success', 'Record deleted successfully');
        }else{
          Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
        }


        return $this->redirect(['index']);
    }

    /**
     * Finds the TblEmployee model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return TblEmployee the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblEmployee::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    function clean($string) {
      $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
      return preg_replace('/[^A-Za-z0-9.\-]/', '', $string); // Removes special chars.
    }
}
