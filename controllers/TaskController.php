<?php

namespace app\controllers;
use Yii;
use app\models\TblTask;
use app\models\TblTaskSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\TblUserlog;
use app\models\TblTaskStatus;
use app\models\TblMenuAccess;
/**
 * TaskController implements the CRUD actions for TblTask model.
 */
class TaskController extends Controller
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
        $menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>48,'status'=>1])->one();
        if($this->action->id == "index"){
          //check if user has permission to access the page
          if(isset($menuaccess) && $menuaccess->view_crud == 1){

          }else{
            return $this->redirect(['site/index']);
          }
        }else if($this->action->id == "create"){
          $menuaccess1 = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>49,'status'=>1])->one();
          if(isset($menuaccess1) && $menuaccess1->create_crud == 1){

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
        }else if($this->action->id == "calendar"){
          $menuaccess2 = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>50,'status'=>1])->one();
          if(isset($menuaccess2) && $menuaccess2->delete_crud == 1){

          }else{
            return $this->redirect(['site/index']);
          }
        }
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
      }
    /**
     * Lists all TblTask models.
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
        $searchModel = new TblTaskSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionResetFilters()
    {
        $searchModel = new TblTaskSearch();
        $searchModel->clearSearchState(); // Clear the session data
        return $this->redirect(['index']); // Redirect back to the index page
    }
    /**
     * Displays a single TblTask model.
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
     * Creates a new TblTask model.
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
        $model = new TblTask();

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
     * Updates an existing TblTask model.
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
     * Deletes an existing TblTask model.
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
        $model = TblTask::find()->where(['id'=>$id])->one();
        $model->status = 0;
        if($model->save()){
          Yii::$app->getSession()->setFlash('success', 'Record deleted successfully');
        }else{
          Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblTask model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return TblTask the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblTask::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    //------------------CALENDAR SECTION STARTS ----------------
    public function actionCalendar()
    {
      $session = Yii::$app->session;
      if(!isset($session['username'])){
        Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
        return $this->redirect(['site/login']);
      }
      return $this->render('calendar');
    }
    public function actionFetchEvents()
   {
       Yii::$app->response->format = Response::FORMAT_JSON;
       $session = Yii::$app->session;
       $user_company = $session['userCompany'];

       $events = TblTask::find()->where('status != 0')->andWhere(['fk_location_id'=>$user_company])->all();

       $eventsArray = [];
       foreach ($events as $event) {
           // Combine date and time for FullCalendar
           $startDateTime = $event->due_date . ' ' . $event->time;
           $formattedDateTime = date('Y-m-d\TH:i:s', strtotime($startDateTime));
           $getstatusclass = TblTaskStatus::find()->where(['id'=>$event->status_id])->andWhere('status != 0')->one();
            $eventClass = '';
           if(isset($getstatusclass) && $getstatusclass->icon != ""){
              $eventClass = $getstatusclass->icon;
           }else{
              $eventClass = '';
           }
           $eventsArray[] = [
               'id' => $event->id,
               'title' => $event->title,
               'start' => $formattedDateTime,
               'classNames' => [$eventClass], // Add the class here
               // No 'end' field since there's no end date/time
           ];
       }

       return $eventsArray;
   }
   public function actionCreateTaskCalendar(){
     $session = Yii::$app->session;
     if(!isset($session['username'])){
       Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
       return $this->redirect(['site/login']);
     }

     $model = new TblTask();
     if ($model->load(Yii::$app->request->post())) {
       if($model->save()){
         Yii::$app->getSession()->setFlash('success', 'Task created successfully.');
       }else{
         Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
       }
     }
     return $this->redirect(['task/calendar']);
   }
   public function actionGetTaskDetails($id){
     // Fetch event details from database based on the $id
    $event = TblTask::findOne($id);

    if ($event) {
        // Format the response data
        $response = [
            'id' => $event->id,
            'title' => $event->title,
            'description' => $event->description,
            'startDate' => $event->due_date,
            'time' => $event->time,
            'status' => $event->status_id, // Optional
            'assigned_to' => $event->assigned_to, // Optional
        ];

        // Send JSON response
        return $this->asJson($response);
    } else {
        // Handle case where event is not found
        return $this->asJson(['error' => 'Event not found']);
    }
   }
   public function actionUpdateTaskCalendar(){
     $task_id = $_POST['task_id'];
     $task_title = $_POST['task_title'];
     $task_description = $_POST['task_description'];
     $task_due_date = $_POST['task_due_date'];
     $task_time = $_POST['task_time'];
     $task_assigned_to = $_POST['task_assigned_to'];
     $task_status_id = $_POST['task_status_id'];
     $model = TblTask::find()->where(['id'=>$task_id])->one();
     $model->title = $task_title;
     $model->description = $task_description;
     $model->due_date = $task_due_date;
     $model->time = $task_time;
     $model->assigned_to = $task_assigned_to;
     $model->status_id = $task_status_id;
     if($model->save()){
       Yii::$app->getSession()->setFlash('success', 'Task updated successfully.');
     }else{
       Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
     }
     return $this->redirect(['task/calendar']);
   }
    //------------------CALENDAR SECTION ENDS ----------------
}
