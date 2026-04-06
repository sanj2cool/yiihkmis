<?php

namespace app\controllers;
use Yii;
use app\models\TblClientReceipt;
use app\models\TblClientReceiptSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\TblMenuAccess;
/**
 * ClientReceiptController implements the CRUD actions for TblClientReceipt model.
 */
class ClientReceiptController extends BaseController
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
        $menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>64,'status'=>1])->one();
        if($this->action->id == "index"){
          //check if user has permission to access the page
          if(isset($menuaccess) && $menuaccess->view_crud == 1){

          }else{
            return $this->redirect(['site/index']);
          }
        }else if($this->action->id == "create" || $this->action->id == "create-multiple"){
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
     * Lists all TblClientReceipt models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new TblClientReceiptSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TblClientReceipt model.
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
     * Creates a new TblClientReceipt model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new TblClientReceipt();

        if ($this->request->isPost) {
          if ($model->load($this->request->post())) {
            if(isset($_POST['invoice_id'])){
              $invoice_id_arr = $_POST['invoice_id'];
            }else{
              $invoice_id_arr = [];
            }
            if(isset($_POST['pending_amount'])){
              $pending_amount_arr = $_POST['pending_amount'];
            }else{
              $pending_amount_arr = [];
            }
            if(count($invoice_id_arr) == 0){
              //no invoices were settled
              Yii::$app->getSession()->setFlash('error', 'Please select customer with pending invoice amount(s).');
              return $this->render('create', [
                'model' => $model,
              ]);
            }else{
              if($model->save()){
                for($i=0;$i<count($invoice_id_arr);$i++){
                  if($pending_amount_arr[$i] != "" && $pending_amount_arr[$i] != 0 && $pending_amount_arr[$i] != 0.00){
                    // if($pending_amount_arr[$i] != "" && $pending_amount_arr[$i] != 0 && $pending_amount_arr[$i] != 0.00 && $pending_amount_arr[$i] >= 1){
                    $modelAr = new \app\models\TblAccountReceivable();
                    $modelAr->fk_client_receipt_id = $model->id;
                    $modelAr->fk_invoice_id = $invoice_id_arr[$i];
                    $modelAr->amount_received = $pending_amount_arr[$i];
                    $modelAr->ar_date = $model->ar_date;
                    $modelAr->fk_payment_method_id = $model->fk_payment_method_id;
                    $modelAr->notes = $model->notes;
                    $modelAr->transaction_reference_number = $model->transaction_reference_number;
                    $modelAr->fk_bill_from_id = $model->fk_bill_from_id;
                    if($modelAr->save()){

                    }else{
                      // print_r($modelAr->getErrors());
                      // die();
                    }
                  }
                }//-------for loop ended-------
                //---CHECK IF ANY FILE(S) HAVE BEEN UPLOADED----
                // vendor-payment-files
                $msg_doc = "";
                $target_dir = "../web/receipt-doc/";
                if (isset($_FILES['doc_file']['name'])) {
                  // Process images for this description
                  foreach ($_FILES['doc_file']['name'] as $imgIndex => $fileName) {
                    if ($_FILES['doc_file']['error'][$imgIndex] === UPLOAD_ERR_OK) {

                      $mc=md5(microtime());
                      $f_name = $mc.basename($_FILES["doc_file"]["name"][$imgIndex]);
                      $basename = basename($_FILES["doc_file"]["name"][$imgIndex]);
                      $file_name = $this->clean($f_name);
                      $target_file = $target_dir.$file_name;
                      $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                      if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "webp" && $imageFileType != "heic" && $imageFileType != "HEIC" && $imageFileType != "pdf") {
                        $msg_doc = "Sorry, only Images and PDF files are allowed.";
                      }else{
                        if(move_uploaded_file($_FILES["doc_file"]["tmp_name"][$imgIndex], $target_file)){
                          $modelBefore = new \app\models\TblClientReceiptImage();
                          $modelBefore->fk_receipt_id = $model->id;
                          $modelBefore->file_name = $file_name;
                          $modelBefore->save();
                        }
                      }//-----else for checking file type ended ----

                    }
                  }
                }//---if isset ended-----
                Yii::$app->getSession()->setFlash('success', 'Record created successfully.');
                return $this->redirect(['index']);
              }else{
                Yii::$app->getSession()->setFlash('error', 'Creation Error! Please try again.');
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
     * Updates an existing TblClientReceipt model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
      return $this->redirect(['index']);
        // $model = $this->findModel($id);
        //
        // if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
        //     return $this->redirect(['view', 'id' => $model->id]);
        // }
        //
        // return $this->render('update', [
        //     'model' => $model,
        // ]);
    }

    /**
     * Deletes an existing TblClientReceipt model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        // $this->findModel($id)->delete();
        $session = Yii::$app->session;
        if(!isset($session['username'])){
          Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
          return $this->redirect(['site/login']);
        }
          // $this->findModel($id)->delete();
          $model = TblClientReceipt::find()->where(['id'=>$id])->one();
          $model->status = 0;
          if($model->save()){
            \app\models\TblAccountReceivable::updateAll(array('status' => 0),'fk_client_receipt_id='.$model->id);
            \app\models\TblClientReceiptImage::updateAll(array('status' => 0),'fk_receipt_id='.$model->id);
            Yii::$app->getSession()->setFlash('success', 'Record deleted successfully');
          }else{
            Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
          }

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblClientReceipt model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return TblClientReceipt the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblClientReceipt::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    function clean($string) {
      $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
      return preg_replace('/[^A-Za-z0-9.\-]/', '', $string); // Removes special chars.
    }
    public function actionEditTransaction($id){
      $session = Yii::$app->session;
      if(!isset($session['username'])){
        // Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
        return $this->redirect(['site/login']);
      }
      if(isset($_POST['payment_method'])){
        $pay_method_id = $_POST['payment_method'];
        $notes = $_POST['notes'];
        $transaction_reference_number = $_POST['transaction_reference_number'] ?? '';
        $modelReceipt = TblClientReceipt::find()->where(['id'=>$id])->one();
        $modelReceipt->fk_payment_method_id = $pay_method_id;
        $modelReceipt->notes = $notes;
        $modelReceipt->transaction_reference_number = $transaction_reference_number;
        if($modelReceipt->save()){
          \app\models\TblAccountReceivable::updateAll(array('fk_payment_method_id' => $pay_method_id,'notes'=>$notes,'transaction_reference_number'=>$transaction_reference_number),'fk_client_receipt_id='.$id);
          $deletedIds = Yii::$app->request->post('deleted_image_ids', []);

          if (!empty($deletedIds)) {

            // If it comes as string → convert to array
            if (is_string($deletedIds)) {
              $deletedIds = explode(',', $deletedIds);
            }

            // Ensure array + clean integers
            $ids = array_filter(array_map('intval', (array)$deletedIds));

            if (!empty($ids)) {
              \app\models\TblClientReceiptImage::updateAll(
                [
                  'status' => 0,
                  'mod_by' => $session['userId'] ?? null,
                  'mod_time' => date('Y-m-d H:i:s'),
                ],
                ['id' => $ids]
              );
            }
          }
          $msg_doc = "";
          $target_dir = "../web/receipt-doc/";
          if (isset($_FILES['file_url']['name'])) {
            // Process images for this description
            foreach ($_FILES['file_url']['name'] as $imgIndex => $fileName) {
              if ($_FILES['file_url']['error'][$imgIndex] === UPLOAD_ERR_OK) {

                $mc=md5(microtime());
                $f_name = $mc.basename($_FILES["file_url"]["name"][$imgIndex]);
                $basename = basename($_FILES["file_url"]["name"][$imgIndex]);
                $file_name = $this->clean($f_name);
                $target_file = $target_dir.$file_name;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "webp" && $imageFileType != "heic" && $imageFileType != "HEIC" && $imageFileType != "pdf") {
                  $msg_doc = "Sorry, only Images and PDF files are allowed.";
                }else{
                  if(move_uploaded_file($_FILES["file_url"]["tmp_name"][$imgIndex], $target_file)){
                    $modelBefore = new \app\models\TblClientReceiptImage();
                    $modelBefore->fk_receipt_id = $modelReceipt->id;
                    $modelBefore->file_name = $file_name;
                    $modelBefore->save();
                  }
                }//-----else for checking file type ended ----

              }
            }
          }//---if isset ended-----
          Yii::$app->getSession()->setFlash('success', 'Record updated successfully.');
        }else{
          Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
        }
      }else{
        //---need bank account selected -----
        Yii::$app->getSession()->setFlash('error', 'Please select payment method.');
      }
      return $this->redirect(['view', 'id' => $id]);
    }
    //---WE NEED TO ADD CUSTOMER ID FOR ALL THE STATUS = 1 TRANSACTIONS ------
    // public function actionMigrate(){
    //   $getallactive = TblClientReceipt::find()->where(['status'=>1])->all();
    //   if(isset($getallactive) && count($getallactive) > 0){
    //     foreach($getallactive as $gaa){
    //       $getinvoice = \app\models\TblInvoice::find()->where(['id'=>$gaa->fk_invoice_id])->one();
    //       if(isset($getinvoice) && $getinvoice->id != ""){
    //         $customer_id = $getinvoice->fk_client_id;
    //         echo "Customer found::".$customer_id;
    //         $gaa->fk_client_id = $customer_id;
    //         $gaa->save();
    //       }else {
    //         echo "Customer not found:::".$gaa->id;
    //       }
    //       echo "<hr />";
    //     }//---for loop ended -----
    //   }
    // }//---function ended -------
}
