<?php

namespace app\controllers;
use Yii;
use app\models\TblAccountReceivable;
use app\models\TblAccountReceivableSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\TblInvoice;
use yii\db\Query;
use app\models\TblMenuAccess;
use app\models\TblOwnershipCompany;
use app\models\TblAccountReceivableUp;
/**
 * AccountReceivableController implements the CRUD actions for TblAccountReceivable model.
 */
class AccountReceivableController extends Controller
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
          return $this->redirect(['client-receipt/index']);
          //check if user has permission to access the page
          // if(isset($menuaccess) && $menuaccess->view_crud == 1){
          //
          // }else{
          //   return $this->redirect(['site/index']);
          // }
        }else if($this->action->id == "create" || $this->action->id == "create-multiple"){
          return $this->redirect(['client-receipt/index']);
          if(isset($menuaccess) && $menuaccess->create_crud == 1){

          }else{
            return $this->redirect(['site/index']);
          }
        }else if($this->action->id == "update"){
          return $this->redirect(['client-receipt/index']);

          if(isset($menuaccess) && $menuaccess->edit_crud == 1){

          }else{
            return $this->redirect(['site/index']);
          }
        }else if($this->action->id == "delete"){
          return $this->redirect(['client-receipt/index']);

          if(isset($menuaccess) && $menuaccess->delete_crud == 1){

          }else{
            return $this->redirect(['site/index']);
          }
        }
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
      }
    /**
     * Lists all TblAccountReceivable models.
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
        $searchModel = new TblAccountReceivableSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionResetFilters()
    {
        $searchModel = new TblAccountReceivableSearch();
        $searchModel->clearSearchState(); // Clear the session data
        return $this->redirect(['index']); // Redirect back to the index page
    }
    /**
     * Displays a single TblAccountReceivable model.
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
      return $this->redirect(['client-receipt/index']);
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TblAccountReceivable model.
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
        $model = new TblAccountReceivable();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                // return $this->redirect(['view', 'id' => $model->id]);
                $this->updateBill($model->fk_invoice_id,$model->id);
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
     * Updates an existing TblAccountReceivable model.
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
            // return $this->redirect(['view', 'id' => $model->id]);
            $this->updateBill($model->fk_invoice_id,$model->id);
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
    private function updateBill($fk_invoice_id,$ar_id){
      $model = TblInvoice::find()->where(['id'=>$fk_invoice_id])->one();
      if(isset($model) && $model->id != ""){
        // $model->fk_bill_from_id;
        $modelUp = TblAccountReceivable::find()->where(['id'=>$ar_id])->one();
        $modelUp->fk_bill_from_id = $model->fk_bill_from_id;
        if($modelUp->save()){
          // echo "Updated";
        }else{
          // echo "not updated:::".$ga->id;
        }
      }
    }
    /**
     * Deletes an existing TblAccountReceivable model.
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
        $model = TblAccountReceivable::find()->where(['id'=>$id])->one();
        $model->status = 0;
        if($model->save()){
          Yii::$app->getSession()->setFlash('success', 'Record deleted successfully');
        }else{
          Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblAccountReceivable model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return TblAccountReceivable the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblAccountReceivable::findOne(['id' => $id])) !== null) {
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
      if(Yii::$app->request->post('client') && Yii::$app->request->post('receivable_date') && Yii::$app->request->post('total_amount') && Yii::$app->request->post('mode_of_payment')){
        $receivable_date = Yii::$app->request->post('receivable_date');
        $mode_payment = Yii::$app->request->post('mode_of_payment');
        $notes = Yii::$app->request->post('notes');
        $bill_from_company = Yii::$app->request->post('bill_from_company');
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
          Yii::$app->getSession()->setFlash('error', 'Please select client with pending invoice amount(s).');
          return $this->render('create-multiple');
        }else{
          //--------create AR Entries----------
          for($i=0;$i<count($invoice_id_arr);$i++){
            if($pending_amount_arr[$i] != "" && $pending_amount_arr[$i] != 0 && $pending_amount_arr[$i] != 0.00 && $pending_amount_arr[$i] >= 1){
              $modelAr = new TblAccountReceivable();
              $modelAr->fk_invoice_id = $invoice_id_arr[$i];
              $modelAr->amount_received = $pending_amount_arr[$i];
              $modelAr->ar_date = $receivable_date;
              $modelAr->fk_payment_method_id = $mode_payment;
              $modelAr->notes = $notes;
              $modelAr->fk_bill_from_id = $bill_from_company;
              if($modelAr->save()){

              }else{
                // print_r($modelAr->getErrors());
                // die();
              }
            }
          }//-------for loop ended-------

          //-----redirect to index -------
          Yii::$app->getSession()->setFlash('success', 'Invoices settled successfully.');
          return $this->redirect(['index']);
        }
      }else{
          return $this->render('create-multiple');
      }

    }

    public function actionGetPendingInvoice(){
      // $client_id = 33;
      $client_id = $_POST['client_id'];
      // $bill_from_company = $_POST['bill_from_company'];
      $session = Yii::$app->session;
      $bill_from_company = $session['userCompany'];
      // Step 1: Fetch all invoices with related data in a single query
      $invoicesQuery = TblInvoice::find()
      ->alias('i')
      ->select(['i.id', 'i.invoice_number', 'i.total_amount', 'i.fk_client_id','i.fk_bill_from_id'])
      ->leftJoin(['ir' => 'tbl_account_receivable'], 'ir.fk_invoice_id = i.id AND ir.status != 0')
      ->leftJoin(['c' => 'tbl_client'], 'c.id = i.fk_client_id')
      ->addSelect(['SUM(ir.amount_received) AS total_amount_received', 'c.company_name AS client_name'])
      ->where('i.status != 0')
      ->andWhere('fk_client_id = '.$client_id)
      ->andWhere(['i.fk_bill_from_id'=>$bill_from_company])
      ->andWhere('i.total_amount > 0')
      ->groupBy(['i.id'])
      ->asArray()
      ->all();
      // print_r($invoicesQuery);
      $return_str = '<div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead>
          <tr class="table-dark">
          <th> <input type="checkbox" id="check_all_invoices" /> All</th>
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
            $getbillfrom = TblOwnershipCompany::find()->where(['id'=>$invoice['fk_bill_from_id'],'status'=>1])->one();
            if(isset($getbillfrom) && $getbillfrom->id != ""){
              $bill_from = $getbillfrom->location_name;
            }else{
              $bill_from = '(not set)';
            }
            $return_str .= '<tr>
            <td>
            <input type="checkbox" class="check_invoice" data-id="'.$i.'"/>
            </td>
            <td>'.$i.'</td>
            <td>'.$invoice['invoice_number'].'</td>
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
    public function actionAddBillCompany(){
      $getallars = TblAccountReceivable::find()->where(['status'=>1])->orderBy(['id'=>SORT_ASC])->all();
      // echo count($getallars);
      foreach($getallars as $ga){
        echo $ga->id."::::".$ga->fk_invoice_id."::::::::";
        $getinvoice = TblInvoice::find()->where(['id'=>$ga->fk_invoice_id])->one();
        if(isset($getinvoice) && $getinvoice->id != ""){
          echo $getinvoice->fk_bill_from_id;
          $modelUp = TblAccountReceivableUp::find()->where(['id'=>$ga->id])->one();
          $modelUp->fk_bill_from_id = $getinvoice->fk_bill_from_id;
          if($modelUp->save()){
            echo "Updated";
          }else{
            echo "not updated:::".$ga->id;
          }
        }
        echo "<br />";
      }//--for loop ended ---
    }
}
