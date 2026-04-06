<?php

namespace app\controllers;
use Yii;
use app\models\TblUser;
use app\models\TblUserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\TblMenuAccess;
use app\models\TblUserLocation;
use app\models\TblReportAccess;
/**
 * UserController implements the CRUD actions for TblUser model.
 */
class UserController extends Controller
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
        $menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>15,'status'=>1])->one();
        if($this->action->id == "index"){
          //check if user has permission to access the page
          if(isset($menuaccess) && $menuaccess->view_crud == 1){

          }else{
            return $this->redirect(['site/index']);
          }
        }else if($this->action->id == "create"){
          $menuaccess_c = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>16,'status'=>1])->one();
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
        }else if($this->action->id == "updatepassword"){
          $menuaccess_up = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>17,'status'=>1])->one();
          if(isset($menuaccess_up) && $menuaccess_up->view_crud == 1){

          }else{
            return $this->redirect(['site/index']);
          }
        }
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
      }
    /**
     * Lists all TblUser models.
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
        $searchModel = new TblUserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionResetFilters()
    {
        $searchModel = new TblUserSearch();
        $searchModel->clearSearchState(); // Clear the session data
        return $this->redirect(['index']); // Redirect back to the index page
    }
    /**
     * Displays a single TblUser model.
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
     * Creates a new TblUser model.
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
        $model = new TblUser();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                // return $this->redirect(['view', 'id' => $model->id]);
                //check if username laready exosts
              $checkUser = TblUser::find()->where(['username'=>$model->username,'status'=>1])->all();
              if(isset($checkUser) && count($checkUser) > 0){
                  //send back with the error message
                  Yii::$app->getSession()->setFlash('error', 'Username already exists');
                  return $this->render('create', [
                      'model' => $model,
                  ]);
              }else{
                  $model->password = md5(hash('sha256',$model->password));
                  // $passwordHash = Yii::$app->getSecurity()->generatePasswordHash($model->password);
                  // $authKey = Yii::$app->getSecurity()->generateRandomString();
                  // $model->password = $passwordHash;
                  // $model->auth_key = $authKey;
                  if($model->save()){
                    $fk_user_id = $model->id;
                    $locations = $_POST['location'];
                    for($j=0;$j<count($locations);$j++){
                      $modelLocation[$j] = new TblUserLocation();
                      $modelLocation[$j]->fk_user_id = $fk_user_id;
                      $modelLocation[$j]->fk_location_id = $locations[$j];
                      $modelLocation[$j]->save();
                    }
                      $count = $_POST['menuitems'];
                      for($i = 0;$i<$count;$i++){
                        if(isset($_POST['menuview_'.$i]) && $_POST['menuview_'.$i] != ""){
                          if($_POST['menuview_'.$i] != ""){
                              $view = $_POST['menuview_'.$i];
                          }else{
                              $view = 0;
                          }
                          if($_POST['menuedit_'.$i] != ""){
                              $edit = $_POST['menuedit_'.$i];
                          }else{
                              $edit = 0;
                          }
                          if($_POST['menucreate_'.$i] != ""){
                              $create = $_POST['menucreate_'.$i];
                          }else{
                              $create = 0;
                          }
                          if($_POST['menudelete_'.$i] != ""){
                              $delete = $_POST['menudelete_'.$i];
                          }else{
                              $delete = 0;
                          }
                          $menuaccess[$i] = new TblMenuAccess();
                          $menuaccess[$i]->fk_user_id = $fk_user_id;
                          $menuaccess[$i]->fk_menu_id = $_POST['menuitem_'.$i];
                          $menuaccess[$i]->view_crud = $view;
                          $menuaccess[$i]->edit_crud = $edit;
                          $menuaccess[$i]->create_crud = $create;
                          $menuaccess[$i]->delete_crud = $delete;
                          $menuaccess[$i]->save(true);
                        }
                      }//------for loop ended ----------
                      $report_count = $_POST['reportitems'];
                      for($j = 0; $j < $report_count; $j++){
                        if(isset($_POST['reportview_'.$j]) && $_POST['reportview_'.$j] != ""){
                          if(isset($_POST['reportview_'.$j]) && $_POST['reportview_'.$j] != ""){
                            $view = $_POST['reportview_'.$j];
                          }else{
                            $view = 0;
                          }
                          $reportaccess[$j] = new TblReportAccess();
                          $reportaccess[$j]->fk_user_id = $fk_user_id;
                          $reportaccess[$j]->fk_report_id = $_POST['reportitem_'.$j];
                          $reportaccess[$j]->view_crud = $view;
                          $reportaccess[$j]->save(true);
                        }//----menu view ended-----
                      }//----for loop ended------

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
                      return $this->render('create', [
                          'model' => $model,
                      ]);
                  }
              }//no user exists with this name
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TblUser model.
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

        if ($this->request->isPost && $model->load($this->request->post())) {
            // return $this->redirect(['view', 'id' => $model->id]);
            //check if username laready exosts
          $checkUser = TblUser::find()->where(['username'=>$model->username,'status'=>1])->andWhere('id != '.$model->id)->all();
          if(isset($checkUser) && count($checkUser) > 0){
              //send back with the error message
              Yii::$app->getSession()->setFlash('error', 'Username already exists');
              return $this->render('update', [
                  'model' => $model,
              ]);
          }else{
              // return $this->redirect(['view', 'id' => $model->id]);
              if($model->save()){
                  //delete all entries of menu access first and then add again
                  // TblMenuAccess::deleteAll(['fk_user_id' => $model->id]);
                  TblMenuAccess::updateAll(array('status' => 0),'fk_user_id='.$model->id);
                  $fk_user_id = $model->id;
                  TblUserLocation::updateAll(array('status' => 0),'fk_user_id='.$model->id);
                  $locations = $_POST['location'];
                  for($j=0;$j<count($locations);$j++){
                    $modelLocation[$j] = new TblUserLocation();
                    $modelLocation[$j]->fk_user_id = $fk_user_id;
                    $modelLocation[$j]->fk_location_id = $locations[$j];
                    $modelLocation[$j]->save();
                  }
                  $count = $_POST['menuitems'];
                  for($i = 0;$i<$count;$i++){
                      if(isset($_POST['menuview_'.$i]) && $_POST['menuview_'.$i] != ""){
                          if($_POST['menuview_'.$i] != ""){
                              $view = $_POST['menuview_'.$i];
                          }else{
                              $view = 0;
                          }
                          if($_POST['menuedit_'.$i] != ""){
                              $edit = $_POST['menuedit_'.$i];
                          }else{
                              $edit = 0;
                          }
                          if($_POST['menucreate_'.$i] != ""){
                              $create = $_POST['menucreate_'.$i];
                          }else{
                              $create = 0;
                          }
                          if(isset($_POST['menudelete_'.$i]) && $_POST['menudelete_'.$i] != ""){
                              $delete = $_POST['menudelete_'.$i];
                          }else{
                              $delete = 0;
                          }
                          $menuaccess[$i] = new TblMenuAccess();
                          $menuaccess[$i]->fk_user_id = $fk_user_id;
                          $menuaccess[$i]->fk_menu_id = $_POST['menuitem_'.$i];
                          $menuaccess[$i]->view_crud = $view;
                          $menuaccess[$i]->edit_crud = $edit;
                          $menuaccess[$i]->create_crud = $create;
                          $menuaccess[$i]->delete_crud = $delete;
                          if($menuaccess[$i]->save(true)){
                          }else{
                          // print_r($menuaccess[$i]->getErrors());
                          }
                      }
                  }
                  TblReportAccess::updateAll(array('status' => 0),'fk_user_id='.$model->id);
                  $report_count = $_POST['reportitems'];
                  for($j = 0; $j < $report_count; $j++){
                    if(isset($_POST['reportview_'.$j]) && $_POST['reportview_'.$j] != ""){
                      if(isset($_POST['reportview_'.$j]) && $_POST['reportview_'.$j] != ""){
                        $view = $_POST['reportview_'.$j];
                      }else{
                        $view = 0;
                      }
                      $reportaccess[$j] = new TblReportAccess();
                      $reportaccess[$j]->fk_user_id = $fk_user_id;
                      $reportaccess[$j]->fk_report_id = $_POST['reportitem_'.$j];
                      $reportaccess[$j]->view_crud = $view;
                      $reportaccess[$j]->save(true);
                    }//----menu view ended-----
                  }//----for loop ended------

                  if(isset($_POST['update'])){
                      return $this->redirect(['update', 'id' => $model->id]);
                  }else if(isset($_POST['new'])){
                      return $this->redirect(['create']);
                  }else if(isset($_POST['exit'])){
                      return $this->redirect(['index']);
                  }else{
                      return $this->redirect(['update', 'id' => $model->id]);
                  }
              }else{
                  return $this->render('update', [
                      'model' => $model,
                  ]);
              }
          }//else of no user existing ended
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TblUser model.
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

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblUser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return TblUser the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblUser::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionUpdatepassword()
    {
        $session = Yii::$app->session;
        if(!isset($session['username'])){
          Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
          return $this->redirect(['site/login']);
        }
        // $modelUserLog = new TblUserlog();
        // $modelUserLog->username=$session['username'];
        // $modelUserLog->login_success_status=1;
        // $modelUserLog->url=Yii::$app->getRequest()->getUrl();
        // $modelUserLog->ip=Yii::$app->getRequest()->getUserIP();
        // $modelUserLog->save(true);
        if(isset($_POST['user_select']) && $_POST['user_select'] != "" && isset($_POST['new_password']) && $_POST['new_password'] != "" && isset($_POST['confirm_password']) && $_POST['confirm_password'] != ""){
          $user_id = $_POST['user_select'];
          $password = $_POST['new_password'];
          $confirm_password = $_POST['confirm_password'];
          if($password == $confirm_password){
            // $new_password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
            $new_password_hash = md5(hash('sha256',$password));
            $getuser = TblUser::find()->where(['id'=>$user_id,'status'=>1])->one();
            $getuser->password = $new_password_hash;
            if($getuser->save()){
              //password updated successfully
              Yii::$app->getSession()->setFlash('success', 'Password updated successfully.');
            }else{
              //something went wrong please try again
              Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again');
            }
          }else{
              Yii::$app->getSession()->setFlash('error', 'Password and confirm password does not match.');
          }
        }
        return $this->render('updatepassword');
    }
}
