<?php

namespace app\controllers;
use Yii;
use app\models\TblExpense;
use app\models\TblExpenseSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ExpenseController implements the CRUD actions for TblExpense model.
 */
class ExpenseController extends BaseController
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
     * Lists all TblExpense models.
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
        $searchModel = new TblExpenseSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionResetFilters()
    {
        $searchModel = new TblExpenseSearch();
        $searchModel->clearSearchState(); // Clear the session data
        return $this->redirect(['index']); // Redirect back to the index page
    }

    /**
     * Displays a single TblExpense model.
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
     * Creates a new TblExpense model.
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
        $model = new TblExpense();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {

              //-------------image file uploading started ---------
              if(isset($_FILES['doc_file'])){
                $doc_file = $_FILES['doc_file'];
              }else{
                $doc_file = [];
              }
              $msg_doc = "";
              $target_dir = "../web/expenses/";
              for($i=0;$i<count($doc_file);$i++){
                if(isset($_FILES["doc_file"]["name"][$i]) && $_FILES["doc_file"]["name"][$i] != ""){
                  $mc=md5(microtime());
                  $f_name = $mc.basename($_FILES["doc_file"]["name"][$i]);
                  $basename = basename($_FILES["doc_file"]["name"][$i]);
                  $file_name = $this->clean($f_name);
                  $target_file = $target_dir.$file_name;
                  $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                  if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                        && $imageFileType != "gif" && $imageFileType != "webp" && $imageFileType != "HEIC" && $imageFileType != "heic" && $imageFileType != "pdf") {
                      $msg_doc = "Sorry, only Image and pdf files are allowed.";
                  }else{
                    if(move_uploaded_file($_FILES["doc_file"]["tmp_name"][$i], $target_file)){
                      //=======save the file in the table here =========
                      //====$file_name======
                      $modelDoc = new \app\models\TblExpenseFile();
                      $modelDoc->fk_expense_id = $model->id;
                      $modelDoc->file_upload = $file_name;
                      $modelDoc->save();
                    }
                  }//-----else for checking file type ended ----
                }//=====file has been uplaoded
              }//=====for loop ended======

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
    function clean($string) {
      $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
      return preg_replace('/[^A-Za-z0-9.\-]/', '', $string); // Removes special chars.
    }
    public function actionDelpics(){
      $del_id = $_POST['del_id'];
      $model = \app\models\TblExpenseFile::find()->where(['id'=>$del_id])->one();
      $model->status = 0;
      if($model->save()){
        return "yes";
      }else{
        return "no";
      }
    }

    /**
     * Updates an existing TblExpense model.
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
            //-------------image file uploading started ---------
            if(isset($_FILES['doc_file'])){
              $doc_file = $_FILES['doc_file'];
            }else{
              $doc_file = [];
            }
            $msg_doc = "";
            $target_dir = "../web/expenses/";
            for($i=0;$i<count($doc_file);$i++){
              if(isset($_FILES["doc_file"]["name"][$i]) && $_FILES["doc_file"]["name"][$i] != ""){
                $mc=md5(microtime());
                $f_name = $mc.basename($_FILES["doc_file"]["name"][$i]);
                $basename = basename($_FILES["doc_file"]["name"][$i]);
                $file_name = $this->clean($f_name);
                $target_file = $target_dir.$file_name;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                      && $imageFileType != "gif" && $imageFileType != "webp" && $imageFileType != "HEIC" && $imageFileType != "heic" && $imageFileType != "pdf") {
                    $msg_doc = "Sorry, only Image and pdf files are allowed.";
                }else{
                  if(move_uploaded_file($_FILES["doc_file"]["tmp_name"][$i], $target_file)){
                    //=======save the file in the table here =========
                    //====$file_name======
                    $modelDoc = new \app\models\TblExpenseFile();
                    $modelDoc->fk_expense_id = $model->id;
                    $modelDoc->file_upload = $file_name;
                    $modelDoc->save();
                  }
                }//-----else for checking file type ended ----
              }//=====file has been uplaoded
            }//=====for loop ended======

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
     * Deletes an existing TblExpense model.
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
        $model = TblExpense::find()->where(['id'=>$id])->one();
        $model->status = 0;
        if($model->save()){
          \app\models\TblExpenseFile::updateAll(array('status' => 0),'fk_expense_id='.$id);
          Yii::$app->getSession()->setFlash('success', 'Record deleted successfully');
        }else{
          // print_r($model->getErrors());
          // die();
          Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblExpense model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return TblExpense the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblExpense::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
