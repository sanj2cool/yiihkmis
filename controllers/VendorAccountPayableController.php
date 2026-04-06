<?php

namespace app\controllers;
use Yii;
use app\models\TblVendorAccountPayable;
use app\models\TblVendorAccountPayableSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\TblMenuAccess;
use app\models\TblVendorPayment;
/**
 * VendorAccountPayableController implements the CRUD actions for TblVendorAccountPayable model.
 */
class VendorAccountPayableController extends Controller
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
        $menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>63,'status'=>1])->one();
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
     * Lists all TblVendorAccountPayable models.
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
      return $this->redirect(['vendor-payment/index']);
        $searchModel = new TblVendorAccountPayableSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionResetFilters()
    {
        $searchModel = new TblVendorAccountPayableSearch();
        $searchModel->clearSearchState(); // Clear the session data
        return $this->redirect(['index']); // Redirect back to the index page
    }
    /**
     * Displays a single TblVendorAccountPayable model.
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
      return $this->redirect(['vendor-payment/index']);
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TblVendorAccountPayable model.
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
      return $this->redirect(['vendor-payment/index']);
        $model = new TblVendorAccountPayable();

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
     * Updates an existing TblVendorAccountPayable model.
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
      return $this->redirect(['vendor-payment/index']);
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
     * Deletes an existing TblVendorAccountPayable model.
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
      return $this->redirect(['vendor-payment/index']);
        // $this->findModel($id)->delete();
        $model = TblVendorAccountPayable::find()->where(['id'=>$id])->one();
        $model->status = 0;
        if($model->save()){
          Yii::$app->getSession()->setFlash('success', 'Record deleted successfully');
        }else{
          Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblVendorAccountPayable model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return TblVendorAccountPayable the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblVendorAccountPayable::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionCreateMultiple()
    {
      $session = Yii::$app->session;
      if(!isset($session['username'])){
        // Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
        return $this->redirect(['site/login']);
      }
      return $this->redirect(['vendor-payment/index']);
      if(Yii::$app->request->post('client') && Yii::$app->request->post('receivable_date') && Yii::$app->request->post('total_amount') && Yii::$app->request->post('mode_of_payment')){
        $receivable_date = Yii::$app->request->post('receivable_date');
        $mode_payment = Yii::$app->request->post('mode_of_payment');
        $notes = Yii::$app->request->post('notes');
        $vendor_id = Yii::$app->request->post('client');
        $total_amount_received = Yii::$app->request->post('total_amount');

        $session = Yii::$app -> session;
        $fk_location_id = $session['userCompany'];

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
          Yii::$app->getSession()->setFlash('error', 'Please select vendor with pending invoice amount(s).');
          return $this->render('create-multiple');
        }else{
          // $modelPayment = new \app\models\TblVendorPayment();
          // $modelPayment->fk_vendor_id = $vendor_id;
          // $modelPayment->amount_received = $total_amount_received;
          // $modelPayment->ar_date = $receivable_date;
          // $modelPayment->fk_payment_method_id = $mode_payment;
          // $modelPayment->fk_location_id = $fk_location_id;
          // $modelPayment->notes = $notes;
          // if($modelPayment->save()){
            //--------create AR Entries----------
            //----FIRST CREATE THE VENDOR PAYMENT ENTRY HERE AND THEN CREATE THE VENDOR ACCOUNT PAYABLE ENTRIES
            for($i=0;$i<count($invoice_id_arr);$i++){
              if($pending_amount_arr[$i] != "" && $pending_amount_arr[$i] != 0 && $pending_amount_arr[$i] != 0.00 && $pending_amount_arr[$i] >= 1){
                $modelAr = new TblVendorAccountPayable();
                // $modelAr->fk_vendor_payment_id = $modelPayment->id;
                $modelAr->fk_vendor_invoice_id = $invoice_id_arr[$i];
                $modelAr->amount_received = $pending_amount_arr[$i];
                $modelAr->ar_date = $receivable_date;
                $modelAr->fk_payment_method_id = $mode_payment;
                $modelAr->notes = $notes;
                // $modelAr->fk_bill_from_id = $fk_location_id;
                if($modelAr->save()){

                }else{
                  // print_r($modelAr->getErrors());
                  // die();
                }
              }
            }//-------for loop ended-------
            //-----redirect to index -------
            Yii::$app->getSession()->setFlash('success', 'Invoices settled successfully.');
          // }else{
          //   //-----ERROR SAVING THE DATA ----
          //   //-----redirect to index -------
          //   Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
          // }



          return $this->redirect(['index']);
        }
      }else{
          return $this->render('create-multiple');
      }

    }

    public function actionGetPendingInvoice(){
      // $client_id = 121;
      $client_id = $_POST['client_id'];
      // $bill_from_company = $_POST['bill_from_company'];
      $session = Yii::$app->session;
      $bill_from_company = $session['userCompany'];
      // Step 1: Fetch all invoices with related data in a single query
      $invoicesQuery = \app\models\TblVendorInvoice::find()
      ->alias('i')
      ->select(['i.id', 'i.vendor_invoice_number', 'i.total_amount', 'i.fk_vendor_id','i.fk_location_id'])
      ->leftJoin(['ir' => 'tbl_vendor_account_payable'], 'ir.fk_vendor_invoice_id = i.id AND ir.status != 0')
      ->leftJoin(['c' => 'tbl_vendor'], 'c.id = i.fk_vendor_id')
      ->addSelect(['SUM(ir.amount_received) AS total_amount_received', 'c.company_name AS client_name'])
      ->where('i.status != 0')
      ->andWhere('fk_vendor_id = '.$client_id)
      ->andWhere(['i.fk_location_id'=>$bill_from_company])
      ->andWhere('i.total_amount > 0')
      ->groupBy(['i.id'])
      ->asArray()
      ->all();
      // print_r($invoicesQuery);
      $return_str = '<div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead>
          <tr class="table-dark">
            <th>Sr. No.</th>
            <th>Invoice #</th>
            <th>Bill From</th>
            <th>Total Amount</th>
            <th>Amount Received</th>
            <th>Amount Pending</th>
            <th>Settle</th>
          </tr>
        </thead>
        <tbody>';
        $i = 1;
        foreach($invoicesQuery as $invoice) {
          $totalAmountReceived = $invoice['total_amount_received'] ?? 0.00;
          $totalAmountPending = $invoice['total_amount']-$totalAmountReceived;
          if($totalAmountPending > 0){
            $getbillfrom = \app\models\TblOwnershipCompany::find()->where(['id'=>$invoice['fk_location_id'],'status'=>1])->one();
            if(isset($getbillfrom) && $getbillfrom->id != ""){
              $bill_from = $getbillfrom->location_name;
            }else{
              $bill_from = '(not set)';
            }
            $return_str .= '<tr>
            <td>'.$i.'</td>
            <td>'.$invoice['vendor_invoice_number'].'</td>
            <td>'.$bill_from.'</td>
            <td>'.$invoice['total_amount'].'</td>
            <td>'.$totalAmountReceived.'</td>
            <td>'.$totalAmountPending.'</td>
            <td>
            <input type="hidden" name="invoice_id[]" value="'.$invoice['id'].'" />
            <input type="hidden" name="invoice_pending_amount[]" value="'.number_format($totalAmountPending, 2, '.', '').'" id="invoice_pending_amount_'.$i.'" />
            <input type="text" class="form-control floatNumberField" onkeyup="checkDec(this);" name="pending_amount[]" id="settle_amount_'.$i.'" />
            </td>
            </tr>';
            $i++;
          }

        }//------for loop ended------
        $return_str .= '</tbody></table></div>';
        return $return_str;
    }//-------function ended----------
}
