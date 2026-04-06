<?php

namespace app\controllers;
use Yii;
use app\models\TblAttendance;
use app\models\TblAttendanceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\TblMenuAccess;
/**
 * AttendanceController implements the CRUD actions for TblAttendance model.
 */
class AttendanceController extends Controller
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
        $menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>69,'status'=>1])->one();
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
     * Lists all TblAttendance models.
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
        $searchModel = new TblAttendanceSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionResetFilters()
    {
        $searchModel = new TblAttendanceSearch();
        $searchModel->clearSearchState(); // Clear the session data
        return $this->redirect(['index']); // Redirect back to the index page
    }
    /**
     * Displays a single TblAttendance model.
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
     * Creates a new TblAttendance model.
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
        $model = new TblAttendance();

        if ($this->request->isPost) {
          if ($model->load($this->request->post())) {
            //--------NEED TO CHECK IF ENTRY FOR SAME DATE, EMPLOYEE AND CHECK-IN TIME  ALREADY EXISTS -----
            //--------THERE WOULD BE JUST ONE ENTRY FOR SAME TIMELINE ------
            if($model->out_time != ""){
              $in_time_forunix = $model->date.' '.$model->in_time;
              $out_time_forunix = $model->date.' '.$model->out_time;
              $check = TblAttendance::find()
                      ->where(['fk_user_id'=>$model->fk_user_id])
                      ->andWhere(['status'=>1])
                      ->andWhere('(UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(date," ",in_time), "%Y-%m-%d %h:%i %p")) <= UNIX_TIMESTAMP(STR_TO_DATE("'.$in_time_forunix.'", "%Y-%m-%d %h:%i %p")) and  UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(date," ",out_time), "%Y-%m-%d %h:%i %p")) >= UNIX_TIMESTAMP(STR_TO_DATE("'.$out_time_forunix.'", "%Y-%m-%d %h:%i %p")))
                      or (UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(date," ",in_time), "%Y-%m-%d %h:%i %p")) >= UNIX_TIMESTAMP(STR_TO_DATE("'.$in_time_forunix.'", "%Y-%m-%d %h:%i %p")) and UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(date," ",out_time), "%Y-%m-%d %h:%i %p")) >= UNIX_TIMESTAMP(STR_TO_DATE("'.$out_time_forunix.'", "%Y-%m-%d %h:%i %p")) and UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(date," ",in_time), "%Y-%m-%d %h:%i %p")) <= UNIX_TIMESTAMP(STR_TO_DATE("'.$out_time_forunix.'", "%Y-%m-%d %h:%i %p")))
                      or (UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(date," ",in_time), "%Y-%m-%d %h:%i %p")) <= UNIX_TIMESTAMP(STR_TO_DATE("'.$in_time_forunix.'", "%Y-%m-%d %h:%i %p")) and UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(date," ",out_time), "%Y-%m-%d %h:%i %p")) <= UNIX_TIMESTAMP(STR_TO_DATE("'.$out_time_forunix.'", "%Y-%m-%d %h:%i %p")) and UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(date," ",out_time), "%Y-%m-%d %h:%i %p")) >= UNIX_TIMESTAMP(STR_TO_DATE("'.$in_time_forunix.'", "%Y-%m-%d %h:%i %p")))
                      or (UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(date," ",in_time), "%Y-%m-%d %h:%i %p")) >= UNIX_TIMESTAMP(STR_TO_DATE("'.$in_time_forunix.'", "%Y-%m-%d %h:%i %p")) and UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(date," ",out_time), "%Y-%m-%d %h:%i %p")) <= UNIX_TIMESTAMP(STR_TO_DATE("'.$out_time_forunix.'", "%Y-%m-%d %h:%i %p")))')
                      ->all();
            }else{
              //out time not entered
              //entered in time between existing in and out times of other entries
              $check = TblAttendance::find()
                      ->where(['fk_user_id'=>$model->fk_user_id])
                      ->andWhere(['status'=>1])
                      ->andWhere( 'UNIX_TIMESTAMP(STR_TO_DATE("'.$in_time_forunix.'", "%Y-%m-%d %h:%i %p")) between UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(date," ",in_time), "%Y-%m-%d %h:%i %p")) and UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(date," ",out_time), "%Y-%m-%d %h:%i %p"))')
                      ->all();
            }
            if(isset($check) && count($check) > 0){
              // Handle overlap scenario
              Yii::$app->session->setFlash('error', 'The new check-in time conflicts with an existing attendance record for this user.');
              return $this->redirect(['attendance/create']);
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
                //--------return back to create -----------
                return $this->redirect(['attendance/create']);
              }//------else of model not saved ended ----------
            }//--------else for checking overlaps ended


          }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TblAttendance model.
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
          // Check for overlaps excluding the current record
          $in_time_forunix = $model->date.' '.$model->in_time;
          if($model->out_time != ""){
            $out_time_forunix = $model->date.' '.$model->out_time;
            $check = TblAttendance::find()
                    ->where(['!=','id',$id])
                    ->andWhere(['fk_user_id'=>$model->fk_user_id])
                    ->andWhere(['status'=>1])
                    ->andWhere('(UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(date," ",in_time), "%Y-%m-%d %h:%i %p")) <= UNIX_TIMESTAMP(STR_TO_DATE("'.$in_time_forunix.'", "%Y-%m-%d %h:%i %p")) and  UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(date," ",out_time), "%Y-%m-%d %h:%i %p")) >= UNIX_TIMESTAMP(STR_TO_DATE("'.$out_time_forunix.'", "%Y-%m-%d %h:%i %p")))
                    or (UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(date," ",in_time), "%Y-%m-%d %h:%i %p")) >= UNIX_TIMESTAMP(STR_TO_DATE("'.$in_time_forunix.'", "%Y-%m-%d %h:%i %p")) and UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(date," ",out_time), "%Y-%m-%d %h:%i %p")) >= UNIX_TIMESTAMP(STR_TO_DATE("'.$out_time_forunix.'", "%Y-%m-%d %h:%i %p")) and UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(date," ",in_time), "%Y-%m-%d %h:%i %p")) <= UNIX_TIMESTAMP(STR_TO_DATE("'.$out_time_forunix.'", "%Y-%m-%d %h:%i %p")))
                    or (UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(date," ",in_time), "%Y-%m-%d %h:%i %p")) <= UNIX_TIMESTAMP(STR_TO_DATE("'.$in_time_forunix.'", "%Y-%m-%d %h:%i %p")) and UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(date," ",out_time), "%Y-%m-%d %h:%i %p")) <= UNIX_TIMESTAMP(STR_TO_DATE("'.$out_time_forunix.'", "%Y-%m-%d %h:%i %p")) and UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(date," ",out_time), "%Y-%m-%d %h:%i %p")) >= UNIX_TIMESTAMP(STR_TO_DATE("'.$in_time_forunix.'", "%Y-%m-%d %h:%i %p")))
                    or (UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(date," ",in_time), "%Y-%m-%d %h:%i %p")) >= UNIX_TIMESTAMP(STR_TO_DATE("'.$in_time_forunix.'", "%Y-%m-%dY %h:%i %p")) and UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(date," ",out_time), "%Y-%m-%d %h:%i %p")) <= UNIX_TIMESTAMP(STR_TO_DATE("'.$out_time_forunix.'", "%Y-%m-%d %h:%i %p")))')
                    ->all();
          }else{
            //out time not entered so check in time conditions only
            //how to check?
            //entered in time between existing in and out times of other entries
            $check = TblAttendance::find()
                    ->where(['!=','id',$id])
                    ->andWhere(['fk_user_id'=>$model->fk_user_id])
                    ->andWhere(['status'=>1])
                    ->andWhere( 'UNIX_TIMESTAMP(STR_TO_DATE("'.$in_time_forunix.'", "%Y-%m-%d %h:%i %p")) between UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(date," ",in_time), "%Y-%m-%d %h:%i %p")) and UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(date," ",out_time), "%Y-%m-%d %h:%i %p"))')
                    ->all();
          }
            if(isset($check) && count($check) > 0){
              // Handle overlap scenario
              Yii::$app->session->setFlash('error', 'The new check-in time conflicts with an existing attendance record for this user.');
              return $this->redirect(['attendance/update','id'=>$model->id]);
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
              }else{
                //--------return back to create -----------
                return $this->redirect(['attendance/update','id'=>$model->id]);
              }//------else of model not saved ended ----------
            }//--------else for checking overlaps ended
            // return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TblAttendance model.
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
        $model = TblAttendance::find()->where(['id'=>$id])->one();
        $model->status = 0;
        if($model->save()){
            Yii::$app->getSession()->setFlash('success', 'Record deleted successfully.');
        }else{
            Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblAttendance model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return TblAttendance the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblAttendance::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
