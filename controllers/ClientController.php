<?php

namespace app\controllers;
use Yii;
use app\models\TblClient;
use app\models\TblClientSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\TblClientLogin;
use app\models\TblInvoice;
use app\models\TblAccountReceivable;
use app\models\TblClientStatementLog;
use app\models\TblMenuAccess;
use app\models\TblOwnershipCompany;
use yii\db\Query;
use yii\db\Expression;

use kartik\mpdf\Pdf;
use yii\helpers\FileHelper;
use yii\web\Response;


/**
 * ClientController implements the CRUD actions for TblClient model.
 */
class ClientController extends Controller
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
        $menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>13,'status'=>1])->one();
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
     * Lists all TblClient models.
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
        $searchModel = new TblClientSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionResetFilters()
    {
        $searchModel = new TblClientSearch();
        $searchModel->clearSearchState(); // Clear the session data
        return $this->redirect(['index']); // Redirect back to the index page
    }
    /**
     * Displays a single TblClient model.
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
     * Creates a new TblClient model.
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
        $model = new TblClient();

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
     * Updates an existing TblClient model.
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
     * Deletes an existing TblClient model.
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
        $model = TblClient::find()->where(['id'=>$id])->one();
        $model->status = 0;
        if($model->save()){
          $checkLogin = TblClientLogin::find()->where(['fk_client_id'=>$model->id,'status'=>1])->one();
          if(isset($checkLogin) && $checkLogin->id != ""){
            $checkLogin->status = 0;
            $checkLogin->save();
          }
          Yii::$app->getSession()->setFlash('success', 'Record deleted successfully');
        }else{
          Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblClient model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return TblClient the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblClient::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionGetclient(){
      $fk_client_id = $_POST['fk_client_id'];
      //-------get the terms for the client ------
      $terms = 0;
      $getclient = TblClient::find()->where(['id'=>$fk_client_id])->andWhere('status != 0')->one();
      if(isset($getclient) && $getclient->fk_terms_id != ""){
        $terms = $getclient->fk_terms_id;
      }
      return $terms;
    }
    public function createCredentials($id,$email_client_login,$send_notify){
      //---send notification if it is one -----
      $model = TblClient::find()->where(['id'=>$client_id,'status'=>1])->one();
      //---get the location_id of the client ---
      $fk_location_id = $model->fk_location_id;
      //---FIRST CHECK IF EMAIL ID DOES NOT EXISTS FOR ANY USER ----
      $check = TblClientLogin::find()->where(['username'=>$email_client_login,'status'=>1,'fk_location_id'=>$fk_location_id])->all();
      if(isset($check) && count($check) > 0){
        //---email id already exists for someone else abort ----
      }else{
        $comb = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
         $pass = array();
         $combLen = strlen($comb) - 1;
         for ($i = 0; $i < 8; $i++) {
             $n = rand(0, $combLen);
             $pass[] = $comb[$n];
         }
        $password = implode($pass);
        $password_sha = md5(hash('sha256',$password));
        $client_id = $id;
        $model = TblClient::find()->where(['id'=>$client_id,'status'=>1])->one();
        if(isset($model) && $model->id != ""){
          $modelLogin = new TblClientLogin();
          $modelLogin->fk_client_id = $model->id;
          $modelLogin->username = $email_client_login;
          $modelLogin->password = $password_sha;
          $modelLogin->fk_location_id = $fk_location_id;
          if($modelLogin->save()){
            if($send_notify == 1){
              //----SEND THE CREDENTIALS EMAIL FOR THE CLIENT -----

            }
          }//----if for creating credentials ended =----
        }//----if for getting model ended ------



      }

    }
    public function actionSendCredentials(){
      $session = Yii::$app->session;
      if(!isset($session['username'])){
        Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
        return $this->redirect(['site/login']);
      }
      $id = $_POST['client_id'];
      $user_email = trim(strtolower($_POST['user_email']));
      $comb = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
       $pass = array();
       $combLen = strlen($comb) - 1;
       for ($i = 0; $i < 8; $i++) {
           $n = rand(0, $combLen);
           $pass[] = $comb[$n];
       }
      $password = implode($pass);
      $password_sha = md5(hash('sha256',$password));
      $client_id = $id;
      $model = TblClient::find()->where(['id'=>$client_id])->andWhere(['<>','status',0])->one();
      if(isset($model) && $model->id != ""){
        $model->status = 1;
        $model->save();
        //--------check if entry already exists for the client ---------
        $fk_location_id = $model->fk_location_id;
        $checkOther = TblClientLogin::find()->where(['username'=>$user_email,'status'=>1,'fk_location_id'=>$fk_location_id])->andWhere('fk_client_id != '.$model->id)->all();
        if(isset($check) && count($check) > 0){
          Yii::$app->getSession()->setFlash('error', 'Username already exists in the system for other user. Please try again with different username.');
        }else {
          $checkLogin = TblClientLogin::find()->where(['fk_client_id'=>$model->id,'status'=>1,'fk_location_id'=>$fk_location_id])->one();
          if(isset($checkLogin) && $checkLogin->id != ""){
            //----------entry exists update the password and send the email --------

            $checkLogin->password = $password_sha;
            if($checkLogin->save()){
              $username = $checkLogin->username;
              $company_name = $model->company_name;
              $message = '
              <!DOCTYPE html>
              <html lang="en">
              <head>
              <meta charset="UTF-8">
              <meta name="viewport" content="width=device-width, initial-scale=1.0">
              <title>Invoice Email</title>
              <style>
              /* General styles for responsiveness */
              body {
                margin: 0;
                padding: 0;
                font-family: Arial, sans-serif;
                background-color: #f9f9f9;
                color: #333333;
              }
              table {
                border-spacing: 0;
                width: 100%;
                max-width: 600px;
                margin: 20px auto;
                background-color: #ffffff;
                border: 1px solid #dddddd;
              }
              td {
                padding: 20px;
              }
              h1 {
                color: #555555;
                font-size: 24px;
                margin: 0 0 10px;
              }
              p {
                font-size: 16px;
                line-height: 1.5;
                margin: 0 0 15px;
              }
              a {
                color: #0066cc;
                text-decoration: none;
              }
              .footer {
                font-size: 14px;
                color: #999999;
                text-align: center;
                padding: 15px;
              }
              /* Responsive styles */
              @media screen and (max-width: 600px) {
                table {
                  width: 100%;
                }
                td {
                  padding: 15px;
                }
                h1 {
                  font-size: 20px;
                }
                p {
                  font-size: 14px;
                }
              }
              </style>
              </head>
              <body>
              <table>
              <tr>
              <td>
              <h3>
              Hello '.$company_name.'
              </h3><br /><br />
              <p>
              Thank you for associating with HK Trailer Parts for all your trailer part needs. Please check the details below for your web portal to place and track your orders conveniently.
              </p>
              <p>
              <strong>Access Link:</strong> https://hktrailerparts.com/login<br />
              <strong>Username:</strong> '.$username.'<br />
              <strong>Password:</strong> '.$password.'<br />
              </p><br />
              <p>
              Please feel free to contact us at info@hktrailerparts.com if you have any questions regarding the usage of our online portal.
              </p>
              <br /><br />
              <p>
              Best Regards!<br />
              </p>
              <img src="https://hkmis.ca/web/images/hk-trailer-parts.png" width="150px" />
              </td>
              </tr>
              </table>
              </body>
              </html>';
              $to = $username;
              $from_email = "hktrailerpartsit@gmail.com";
              $from_name = "HK Trailer Parts";
              $subject = "Web Portal Details | HK Trailer Parts ";
              if (filter_var(trim($to), FILTER_VALIDATE_EMAIL)) {
                $to = trim($to);
                try{
                  $layout = Yii::$app->mailer->htmlLayout = "layouts/html";
                  if(Yii::$app->mailer->compose($layout, ['content' => $message])
                  ->setFrom([$from_email=>$from_name])
                  ->setTo($to)
                  ->setSubject($subject)
                  ->send()){
                  }
                }catch(\Swift_TransportException $e){
                  // echo "inside catch";
                  // print_r($e);
                }
              }
              Yii::$app->getSession()->setFlash('success', 'Email sent successfully.');
            }else{
              Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
            }
          }else{
            //----------create new entry -------
            //---FIRST CHECK IF USERNAME ALREADY EXISTS ONLY CREATE THE NEW ONES -----
            $check = TblClientLogin::find()->where(['username'=>$user_email,'status'=>1,'fk_location_id'=>$fk_location_id])->all();
            if(isset($check) && count($check) > 0){
              Yii::$app->getSession()->setFlash('error', 'Username already exists in the system. Please try again with different username.');
            }else {
              // code...
              $modelLogin = new TblClientLogin();
              $modelLogin->fk_client_id = $model->id;
              $modelLogin->username = $user_email;
              $modelLogin->password = $password_sha;
              if($modelLogin->save()){
                $username = $modelLogin->username;
                $company_name = $model->company_name;
                $message = '
                <!DOCTYPE html>
                <html lang="en">
                <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Invoice Email</title>
                <style>
                /* General styles for responsiveness */
                body {
                  margin: 0;
                  padding: 0;
                  font-family: Arial, sans-serif;
                  background-color: #f9f9f9;
                  color: #333333;
                }
                table {
                  border-spacing: 0;
                  width: 100%;
                  max-width: 600px;
                  margin: 20px auto;
                  background-color: #ffffff;
                  border: 1px solid #dddddd;
                }
                td {
                  padding: 20px;
                }
                h1 {
                  color: #555555;
                  font-size: 24px;
                  margin: 0 0 10px;
                }
                p {
                  font-size: 16px;
                  line-height: 1.5;
                  margin: 0 0 15px;
                }
                a {
                  color: #0066cc;
                  text-decoration: none;
                }
                .footer {
                  font-size: 14px;
                  color: #999999;
                  text-align: center;
                  padding: 15px;
                }
                /* Responsive styles */
                @media screen and (max-width: 600px) {
                  table {
                    width: 100%;
                  }
                  td {
                    padding: 15px;
                  }
                  h1 {
                    font-size: 20px;
                  }
                  p {
                    font-size: 14px;
                  }
                }
                </style>
                </head>
                <body>
                <table>
                <tr>
                <td>
                <h3>
                Hello '.$company_name.'
                </h3><br /><br />
                <p>
                Thank you for associating with HK Trailer Parts for all your trailer part needs. Please check the details below for your web portal to place and track your orders conveniently.
                </p>
                <p>
                <strong>Access Link:</strong> https://hktrailerparts.com/login<br />
                <strong>Username:</strong> '.$username.'<br />
                <strong>Password:</strong> '.$password.'<br />
                </p><br />
                <p>
                Please feel free to contact us at info@hktrailerparts.com if you have any questions regarding the usage of our online portal.
                </p>
                <br /><br />
                <p>
                Best Regards!<br />
                </p>
                <img src="https://hkmis.ca/web/images/hk-trailer-parts.png" width="150px" />
                </td>
                </tr>
                </table>
                </body>
                </html>';
                $to = $username;
                $from_email = "info@hktrailerparts.com";
                $from_name = "HK Trailer Parts";
                $subject = "Web Portal Details | HK Trailer Parts ";
                if (filter_var(trim($to), FILTER_VALIDATE_EMAIL)) {
                  $to = trim($to);
                  try{
                    $layout = Yii::$app->mailer->htmlLayout = "layouts/html";
                    if(Yii::$app->mailer->compose($layout, ['content' => $message])
                    ->setFrom([$from_email=>$from_name])
                    ->setTo($to)
                    ->setSubject($subject)
                    ->send()){
                    }
                  }catch(\Swift_TransportException $e){
                    // echo "inside catch";
                    // print_r($e);
                  }
                }
                Yii::$app->getSession()->setFlash('success', 'Email sent successfully.');
              }else{
                Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
              }
            }

          }//----else of creating new entry ended ------
        }


      }else{
        Yii::$app->getSession()->setFlash('error', 'Client not found. Please try again.');
      }
      return $this->redirect(['view','id'=>$id]);

    }
    public function actionSendStatement($id){
      $client_id = $id;
      $model = TblClient::find()->where(['id'=>$id])->one();
      $subject = "Summary of Transactions - HK Trailer Parts";
      $return_str = '<p>
      Greetings '.$model->company_name.'
      </p><br />
      <p>
      Please find the summary of transactions statement below:
      </p>
      <table style="max-width:650px;border-collapse:collapse;" border="1" cellpadding="5">
        <thead class="table-dark">
          <tr>
            <th>Transaction Date</th>
            <th>Transaction Type</th>
            <th>Transaction Number</th>
            <th>Total</th>
            <th>Paid Status</th>
            <th>Balance</th>

          </tr>
        </thead>
        <tbody>';
          $getinvoices = TblInvoice::find()
          ->where('status != 0')
          ->andWhere(['fk_client_id' => $model->id])
          ->orderBy(['crt_time' => SORT_ASC])
          ->all();

          $invoice_arr = [];
          $ar_arr = [];

          if (isset($getinvoices) && count($getinvoices) > 0) {
            foreach ($getinvoices as $gi) {
              // Calculate total payments received for this invoice
              $totalPayments = TblAccountReceivable::find()
              ->where(['fk_invoice_id' => $gi->id])
              ->sum('amount_received');

              // Determine the paid status based on total amount and payments
              if ($totalPayments >= $gi->total_amount || $gi->total_amount == 0 || $gi->total_amount == 0.00 ) {
                $paid_status = "Paid";
              } elseif ($totalPayments > 0) {
                $paid_status = "Partially Paid";
              } else {
                $paid_status = "Unpaid";
              }
              $invoice_arr[] = [
                'date' => date('Y-m-d', strtotime($gi->crt_time)),
                'type' => "Invoice",
                'number' => $gi->invoice_number,
                'amount' => $gi->total_amount,
                'paid_status' => $paid_status
              ];


            }
          }

          $getar = TblAccountReceivable::find()
          ->where('status != 0')
          ->andWhere('fk_invoice_id in (select id from tbl_invoice where fk_client_id = ' . $model->id . ' and status != 0)')
          ->orderBy(['crt_time' => SORT_ASC])
          ->all();

          if (isset($getar) && count($getar) > 0) {
            foreach ($getar as $gi) {
              $getinvoice = TblInvoice::find()->where(['id' => $gi->fk_invoice_id])->one();
              $invoice_no = isset($getinvoice) && $getinvoice->id != "" ? " (Invoice #" . $getinvoice->invoice_number . ")" : " (Invoice #.)";

              $ar_arr[] = [
                'date' => date('Y-m-d', strtotime($gi->crt_time)),
                'type' => "Amount Received",
                'number' => $gi->id . $invoice_no,
                'amount' => $gi->amount_received
              ];
            }
          }

          // Merge and sort arrays
          $mergedArray = array_merge($invoice_arr, $ar_arr);
          usort($mergedArray, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
          });

          // Output
          if (count($mergedArray) > 0) {
            if ($mergedArray[0]['type'] == "Invoice") {
              $starting_balance = $mergedArray[0]['amount'];
            } elseif ($mergedArray[0]['type'] == "Amount Received" || $mergedArray[0]['type'] == "Bad Debt.") {
              $starting_balance = -$mergedArray[0]['amount'];
            }
            $i = 0;
            foreach ($mergedArray as $v) {
              if ($i > 0) {
                if ($v['type'] == "Invoice") {
                  $starting_balance += $v['amount'];
                } else {
                  $starting_balance -= $v['amount'];
                }
              }
              $return_str .= '<tr>
              <td>' . $v['date'] . '</td>
              <td>' . $v['type'] . '</td>
              <td>' . $v['number'] . '</td>
              <td>' . $v['amount'] . '</td>';
              if ($v['type'] == "Invoice") {
                $return_str .= '<td>' . $v['paid_status'] . '</td>';
              } else {
                $return_str .= '<td>-</td>';
              }
              $return_str .= '<td>' . $starting_balance . '</td>
              </tr>';
              $i++;
            }
          }
        $return_str .= '</tbody>
      </table><br />
      <p>
      Please feel free to contact us at info@hktrailerparts.ca if you have any queries regarding the summary of transactions.
      </p>
      <br /><br />
      Best Regards!<br /><br />
      <img src="https://hkmis.ca/web/images/hk-trailer-parts.png" width="100px" /><br />
      <p>
      120 Orenda Rd #1d<br />Brampton, ON<br />L6W 1W2
      </p>
    ';

      // return $return_str;
      $send_to = $model->email;
      $send_to_name = $model->company_name;
      // $from_email = "hktrailerpartsit@gmail.com";
      $from_email = "info@hktrailerparts.com";
      $from_name = "HK Trailer Parts";
      // if (filter_var(trim($to), FILTER_VALIDATE_EMAIL)) {
      //   $to = trim($to);
      //   try{
      //     $layout = Yii::$app->mailer->htmlLayout = "layouts/html";
      //     if(Yii::$app->mailer->compose($layout, ['content' => $return_str])
      //     ->setFrom([$from_email=>$from_name])
      //     ->setTo($to)
      //     ->setSubject($subject)
      //     ->send()){
      //       Yii::$app->getSession()->setFlash('success', 'Email sent successfully.');
      //     }
      //   }catch(\Swift_TransportException $e){
      //     // echo "inside catch";
      //     // print_r($e);
      //     Yii::$app->getSession()->setFlash('error', 'Client not found. Please try again.');
      //   }
      // }else{
      //   Yii::$app->getSession()->setFlash('error', 'Email-id not found. Please enter email-id and try again.');
      // }

            $send_arr = [
              "sender" => [
                "name" => $from_name,
                "email" => $from_email
              ],
              "to" => [
                [
                  "email" => $send_to,
                  "name" => $send_to_name
                ]
              ],
              "subject" => $subject,
              "htmlContent" => $return_str
            ];
            // echo json_encode($send_arr);
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.brevo.com/v3/smtp/email',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>json_encode($send_arr),
            CURLOPT_HTTPHEADER => array(
              'api-key: sendkey',
              'Accept: application/json',
              'Content-Type: application/json'
            ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $decode_resp = json_decode($response,true);
            if(isset($decode_resp['messageId']) && $decode_resp['messageId'] != ""){
              Yii::$app->getSession()->setFlash('success', 'Email sent successfully.');
            }else{
                Yii::$app->getSession()->setFlash('error', 'Email-id not found. Please enter email-id and try again.');
            }

      return $this->redirect(['update','id'=>$id]);
    }
    public function actionSendStatementView($id){
      $client_id = $id;
      $model = TblClient::find()->where(['id'=>$id])->one();
      $subject = "Summary of Transactions - HK Trailer Parts";
      $return_str = '<p>
      Greetings '.$model->company_name.'
      </p><br />
      <p>
      Please find the summary of transactions statement below:
      </p>
      <table style="max-width:650px;border-collapse:collapse;" border="1" cellpadding="5">
        <thead class="table-dark">
          <tr>
            <th>Transaction Date</th>
            <th>Transaction Type</th>
            <th>Transaction Number</th>
            <th>Total</th>
            <th>Paid Status</th>
            <th>Balance</th>

          </tr>
        </thead>
        <tbody>';
          $getinvoices = TblInvoice::find()
          ->where('status != 0')
          ->andWhere(['fk_client_id' => $model->id])
          ->orderBy(['crt_time' => SORT_ASC])
          ->all();

          $invoice_arr = [];
          $ar_arr = [];

          if (isset($getinvoices) && count($getinvoices) > 0) {
            foreach ($getinvoices as $gi) {
              // Calculate total payments received for this invoice
              $totalPayments = TblAccountReceivable::find()
              ->where(['fk_invoice_id' => $gi->id])
              ->sum('amount_received');

              // Determine the paid status based on total amount and payments
              if ($totalPayments >= $gi->total_amount || $gi->total_amount == 0 || $gi->total_amount == 0.00 ) {
                $paid_status = "Paid";
              } elseif ($totalPayments > 0) {
                $paid_status = "Partially Paid";
              } else {
                $paid_status = "Unpaid";
              }
              $invoice_arr[] = [
                'date' => date('Y-m-d', strtotime($gi->crt_time)),
                'type' => "Invoice",
                'number' => $gi->invoice_number,
                'amount' => $gi->total_amount,
                'paid_status' => $paid_status
              ];

            }
          }

          $getar = TblAccountReceivable::find()
          ->where('status != 0')
          ->andWhere('fk_invoice_id in (select id from tbl_invoice where fk_client_id = ' . $model->id . ' and status != 0)')
          ->orderBy(['crt_time' => SORT_ASC])
          ->all();

          if (isset($getar) && count($getar) > 0) {
            foreach ($getar as $gi) {
              $getinvoice = TblInvoice::find()->where(['id' => $gi->fk_invoice_id])->one();
              $invoice_no = isset($getinvoice) && $getinvoice->id != "" ? " (Invoice #" . $getinvoice->invoice_number . ")" : " (Invoice #.)";

              $ar_arr[] = [
                'date' => date('Y-m-d', strtotime($gi->crt_time)),
                'type' => "Amount Received",
                'number' => $gi->id . $invoice_no,
                'amount' => $gi->amount_received
              ];
            }
          }

          // Merge and sort arrays
          $mergedArray = array_merge($invoice_arr, $ar_arr);
          usort($mergedArray, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
          });

          // Output
          if (count($mergedArray) > 0) {
            if ($mergedArray[0]['type'] == "Invoice") {
              $starting_balance = $mergedArray[0]['amount'];
            } elseif ($mergedArray[0]['type'] == "Amount Received" || $mergedArray[0]['type'] == "Bad Debt.") {
              $starting_balance = -$mergedArray[0]['amount'];
            }
            $i = 0;
            foreach ($mergedArray as $v) {
              if ($i > 0) {
                // if ($v['type'] == "Invoice") {
                //   $starting_balance += $v['amount'];
                // } else {
                //   $starting_balance -= $v['amount'];
                // }
                if ($v['type'] == "Invoice") {
                  $starting_balance = bcadd($starting_balance, $v['amount'], 2);
                } else {
                  $starting_balance = bcsub($starting_balance, $v['amount'],2);
                }
              }
              $return_str .= '<tr>
              <td>' . $v['date'] . '</td>
              <td>' . $v['type'] . '</td>
              <td>' . $v['number'] . '</td>
              <td>' . $v['amount'] . '</td>';
              if ($v['type'] == "Invoice") {
                $return_str .= '<td>' . $v['paid_status'] . '</td>';
              } else {
                $return_str .= '<td>-</td>';
              }
              $return_str .= '<td>' . $starting_balance . '</td>
              </tr>';
              $i++;
            }
          }
        $return_str .= '</tbody>
      </table><br />
      <p>
      Please feel free to contact us at info@hktrailerparts.ca if you have any queries regarding the summary of transactions.
      </p>
      <br /><br />
      Best Regards!<br /><br />
      <img src="https://hkmis.ca/web/images/hk-trailer-parts.png" width="100px" /><br />
      <p>
      120 Orenda Rd #1d<br />Brampton, ON<br />L6W 1W2
      </p>
    ';

      // return $return_str;
      $send_to = $model->email;
      $send_to_name = $model->company_name;
      $from_email = "info@hktrailerparts.com";
      $from_name = "HK Trailer Parts";
      // if (filter_var(trim($to), FILTER_VALIDATE_EMAIL)) {
      //   $to = trim($to);
      //   try{
      //     $layout = Yii::$app->mailer->htmlLayout = "layouts/html";
      //     if(Yii::$app->mailer->compose($layout, ['content' => $return_str])
      //     ->setFrom([$from_email=>$from_name])
      //     ->setTo($to)
      //     ->setSubject($subject)
      //     ->send()){
      //       Yii::$app->getSession()->setFlash('success', 'Email sent successfully.');
      //     }
      //   }catch(\Swift_TransportException $e){
      //     // echo "inside catch";
      //     // print_r($e);
      //     Yii::$app->getSession()->setFlash('error', 'Client not found. Please try again.');
      //   }
      // }else{
      //   Yii::$app->getSession()->setFlash('error', 'Email-id not found. Please enter email-id and try again.');
      // }

            $send_arr = [
              "sender" => [
                "name" => $from_name,
                "email" => $from_email
              ],
              "to" => [],
              "cc" => [ // Add multiple CC recipients here
                [
                    "email" => $from_email,
                    "name" => $from_name
                ]
              ],
              "subject" => $subject,
              "htmlContent" => $return_str
            ];

            // "to" => [
            //   [
            //     "email" => $send_to,
            //     "name" => $send_to_name
            //   ]
            // ],
            $emails = array_map('trim', explode(',', $send_to));
            foreach ($emails as $email) {
              // Validate email
              if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $send_arr['to'][] = [
                  "email" => $email,
                  "name" => $send_to_name
                ];
              }
            }
            // echo json_encode($send_arr);
            if (empty($send_arr['to'])) {
              Yii::$app->getSession()->setFlash('error', 'No valid Email-id not found. Please enter valid email-id and try again.');
            }else{

            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.brevo.com/v3/smtp/email',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>json_encode($send_arr),
            CURLOPT_HTTPHEADER => array(
              'api-key: sendkey',
              'Accept: application/json',
              'Content-Type: application/json'
            ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $decode_resp = json_decode($response,true);
            if(isset($decode_resp['messageId']) && $decode_resp['messageId'] != ""){
              $modelLog = new TblClientStatementLog();
              $modelLog->fk_client_id = $client_id;
              $modelLog->message_id = $decode_resp['messageId'];
              $modelLog->save();
              Yii::$app->getSession()->setFlash('success', 'Email sent successfully.');
            }else{
                Yii::$app->getSession()->setFlash('error', 'Email-id not found. Please enter email-id and try again.');
            }
          }

      return $this->redirect(['view','id'=>$id]);
    }

    public function actionSendStatementCompanySpecific($id){
      $client_id = $id;
      $ownership_company = $_POST['ownership_company'];
      $getcompany = TblOwnershipCompany::find()->where(['id'=>$ownership_company])->one();
      if(isset($getcompany) && $getcompany->id != ""){
        $bill_from_cname = $getcompany->company_name;
        $bill_from_address = $getcompany->address;
        $bill_from_city = $getcompany->city;
        $bill_from_province = $getcompany->state;
        $bill_from_postal_code = $getcompany->postal_code;
        $bill_from_phone = $getcompany->phone;
        $bill_logo = $getcompany->logo;
        $bill_email = $getcompany->email;
      }else{
        //---by default keep it hk trailer parts ---
        $bill_from_cname = "HK Trailer Parts";
        $bill_from_address = "120 Orenda Rd #1d";
        $bill_from_city = "Brampton";
        $bill_from_province = "ON";
        $bill_from_postal_code = "L6W 1W2";
        $bill_from_phone = "647-282-6031";
        $bill_logo = "hktrailerparts-logo.png";
        $bill_email = "info@hktrailerparts.com";
      }
      $model = TblClient::find()->where(['id'=>$id])->one();
      $subject = "Summary of Transactions - ".$bill_from_cname;
      $return_str = '<p>
      Greetings '.$model->company_name.'
      </p><br />
      <p>
      Please find the summary of transactions statement below:
      </p>
      <table style="max-width:650px;border-collapse:collapse;" border="1" cellpadding="5">
        <thead class="table-dark">
          <tr>
            <th>Transaction Date</th>
            <th>Transaction Type</th>
            <th>Transaction Number</th>
            <th>Total</th>
            <th>Paid Status</th>
            <th>Balance</th>

          </tr>
        </thead>
        <tbody>';
          $getinvoices = TblInvoice::find()
          ->where('status != 0')
          ->andWhere(['fk_client_id' => $model->id])
          ->andWhere(['fk_bill_from_id'=>$ownership_company])
          ->orderBy(['crt_time' => SORT_ASC])
          ->all();

          $invoice_arr = [];
          $ar_arr = [];

          if (isset($getinvoices) && count($getinvoices) > 0) {
            foreach ($getinvoices as $gi) {
              // Calculate total payments received for this invoice
              $totalPayments = TblAccountReceivable::find()
              ->where(['fk_invoice_id' => $gi->id])
              ->sum('amount_received');

              // Determine the paid status based on total amount and payments
              if ($totalPayments >= $gi->total_amount || $gi->total_amount == 0 || $gi->total_amount == 0.00 ) {
                $paid_status = "Paid";
              } elseif ($totalPayments > 0) {
                $paid_status = "Partially Paid";
              } else {
                $paid_status = "Unpaid";
              }
              $invoice_arr[] = [
                'date' => date('Y-m-d', strtotime($gi->crt_time)),
                'type' => "Invoice",
                'number' => $gi->invoice_number,
                'amount' => $gi->total_amount,
                'paid_status' => $paid_status
              ];

            }
          }

          $getar = TblAccountReceivable::find()
          ->where('status != 0')
          ->andWhere('fk_invoice_id in (select id from tbl_invoice where fk_client_id = ' . $model->id . ' and status != 0 and fk_bill_from_id = '.$ownership_company.')')
          ->orderBy(['crt_time' => SORT_ASC])
          ->all();

          if (isset($getar) && count($getar) > 0) {
            foreach ($getar as $gi) {
              $getinvoice = TblInvoice::find()->where(['id' => $gi->fk_invoice_id])->one();
              $invoice_no = isset($getinvoice) && $getinvoice->id != "" ? " (Invoice #" . $getinvoice->invoice_number . ")" : " (Invoice #.)";

              $ar_arr[] = [
                'date' => date('Y-m-d', strtotime($gi->crt_time)),
                'type' => "Amount Received",
                'number' => $gi->id . $invoice_no,
                'amount' => $gi->amount_received
              ];
            }
          }

          // Merge and sort arrays
          $mergedArray = array_merge($invoice_arr, $ar_arr);
          usort($mergedArray, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
          });

          // Output
          if (count($mergedArray) > 0) {
            if ($mergedArray[0]['type'] == "Invoice") {
              $starting_balance = $mergedArray[0]['amount'];
            } elseif ($mergedArray[0]['type'] == "Amount Received" || $mergedArray[0]['type'] == "Bad Debt.") {
              $starting_balance = -$mergedArray[0]['amount'];
            }
            $i = 0;
            foreach ($mergedArray as $v) {
              if ($i > 0) {
                // if ($v['type'] == "Invoice") {
                //   $starting_balance += $v['amount'];
                // } else {
                //   $starting_balance -= $v['amount'];
                // }
                if ($v['type'] == "Invoice") {
                  $starting_balance = bcadd($starting_balance, $v['amount'], 2);
                } else {
                  $starting_balance = bcsub($starting_balance, $v['amount'],2);
                }
              }
              $return_str .= '<tr>
              <td>' . $v['date'] . '</td>
              <td>' . $v['type'] . '</td>
              <td>' . $v['number'] . '</td>
              <td>' . $v['amount'] . '</td>';
              if ($v['type'] == "Invoice") {
                $return_str .= '<td>' . $v['paid_status'] . '</td>';
              } else {
                $return_str .= '<td>-</td>';
              }
              $return_str .= '<td>' . $starting_balance . '</td>
              </tr>';
              $i++;
            }
          }
        $return_str .= '</tbody>
      </table><br />
      <p>
      Please feel free to contact us at '.$bill_email.' if you have any queries regarding the summary of transactions.
      </p>
      <br /><br />
      Best Regards!<br /><br />
      <img src="https://hkmis.ca/web/ownercompany-logo/'.$bill_logo.'" width="100px" /><br />
      <p>
      '.$bill_from_address.'<br />'.$bill_from_city.' '.$bill_from_province.' '.$bill_from_postal_code.'
      </p>
    ';

      // return $return_str;
      $send_to = $model->email;
      $send_to_name = $model->company_name;
      $from_email = $bill_email;
      $from_name = $bill_from_cname;

            $send_arr = [
              "sender" => [
                "name" => $from_name,
                "email" => $from_email
              ],
              "to" => [],
              "cc" => [ // Add multiple CC recipients here
                [
                    "email" => $from_email,
                    "name" => $from_name
                ]
              ],
              "subject" => $subject,
              "htmlContent" => $return_str
            ];

            // "to" => [
            //   [
            //     "email" => $send_to,
            //     "name" => $send_to_name
            //   ]
            // ],
            $emails = array_map('trim', explode(',', $send_to));
            foreach ($emails as $email) {
              // Validate email
              if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $send_arr['to'][] = [
                  "email" => $email,
                  "name" => $send_to_name
                ];
              }
            }
            // echo json_encode($send_arr);
            if (empty($send_arr['to'])) {
              Yii::$app->getSession()->setFlash('error', 'No valid Email-id not found. Please enter valid email-id and try again.');
            }else{

            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.brevo.com/v3/smtp/email',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>json_encode($send_arr),
            CURLOPT_HTTPHEADER => array(
              'api-key: sendkey',
              'Accept: application/json',
              'Content-Type: application/json'
            ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $decode_resp = json_decode($response,true);
            if(isset($decode_resp['messageId']) && $decode_resp['messageId'] != ""){
              $modelLog = new TblClientStatementLog();
              $modelLog->fk_client_id = $client_id;
              $modelLog->message_id = $decode_resp['messageId'];
              $modelLog->fk_ownership_id = $ownership_company;
              $modelLog->save();
              Yii::$app->getSession()->setFlash('success', 'Email sent successfully.');
            }else{
                Yii::$app->getSession()->setFlash('error', 'Email-id not found. Please enter email-id and try again.');
            }
          }

      return $this->redirect(['view','id'=>$id]);
    }
    //----CHANGE THE CLIENT LOCATION AS PER THE INVOICE ISSUED TO HIM -----
    public function actionCheckLocation(){
      // Subquery to get distinct client-biller pairs
$subQuery = (new Query())
    ->select(['fk_client_id', 'fk_bill_from_id'])
    ->distinct()
    ->from('tbl_invoice')
    ->where(['status' => 1]);

// Main query to get clients with exactly one billing company
$query = (new Query())
    ->select([
        't.fk_client_id',
        'c.company_name AS client_name',
        'client_location' => 'c.fk_location_id',
        'bill_from_list' => new Expression('GROUP_CONCAT(DISTINCT t.fk_bill_from_id ORDER BY t.fk_bill_from_id)'),
        'biller_count' => new Expression('COUNT(DISTINCT t.fk_bill_from_id)')
    ])
    ->from(['t' => $subQuery])
    ->leftJoin(['c' => 'tbl_client'], 'c.id = t.fk_client_id')
    ->groupBy(['t.fk_client_id', 'c.company_name', 'c.fk_location_id'])
    ->having(new Expression('biller_count = 1'));

$clientsWithSingleBiller = $query->all();

// Build the HTML table for verification
$return_str = '<table border="1" style="border-collapse:collapse;">
<tr>
    <th>Client Id</th>
    <th>Client Name</th>
    <th>Current Location</th>
    <th>Bill Location</th>
    <th>Match</th>
</tr>';

foreach ($clientsWithSingleBiller as $cs) {
    $client_location = (int) $cs['client_location'];
    $bill_from = (int) $cs['bill_from_list']; // safe cast since only one biller exists

    $match = ($bill_from === $client_location) ? 'Yes' : '<span style="color:#f00;">No Match</span>';
    if($bill_from != $client_location){
      $update = TblClient::find()->where(['id'=>$cs['fk_client_id']])->one();
      $update->fk_location_id = $bill_from;
      $update->save();
    }

    $return_str .= '<tr>
        <td>'.$cs['fk_client_id'].'</td>
        <td>'.$cs['client_name'].'</td>
        <td>'.$client_location.'</td>
        <td>'.$bill_from.'</td>
        <td>'.$match.'</td>
    </tr>';
}

$return_str .= '</table>';

return $return_str;
    }
    public function actionStatementForm($client_id)
    {
        return $this->renderAjax('_statement_form', [
            'client_id' => $client_id,
        ]);
    }

    public function actionGenerateStatement()
    {
        $client_id    = Yii::$app->request->post('client_id');
        $from_date    = Yii::$app->request->post('from_date');
        $to_date      = Yii::$app->request->post('to_date');
        $user_company = Yii::$app->request->post('ownership_company');

        $today = date('Y-m-d');

        /* -------------------------------
         * FETCH PENDING INVOICES (AR)
         * ------------------------------- */
        $pendingInvoices = (new \yii\db\Query())
            ->select([
                'invoice_number' => 'i.invoice_number',
                'invoice_date'   => 'i.invoice_date',
                'due_date'       => 'i.due_date',
                'total_amount'       => 'i.total_amount',
                'pending_amount' => 'i.total_amount - IFNULL(SUM(ar.amount_received),0)',
                'age_days'       => "DATEDIFF('{$today}', i.due_date)"
            ])
            ->from('tbl_invoice i')
            ->leftJoin(
                'tbl_account_receivable ar',
                'ar.fk_invoice_id = i.id AND ar.status = 1'
            )
            ->where([
                'i.status' => 1,
                'i.fk_client_id' => $client_id,
                'i.fk_bill_from_id' => $user_company
            ])
            ->andWhere(['between', 'i.invoice_date', $from_date, $to_date])
            ->groupBy('i.id')
            ->having('pending_amount > 0')
            ->orderBy(['i.invoice_date' => SORT_ASC])
            ->all();

        /* -------------------------------
         * AGING
         * ------------------------------- */
        $aging = ['current'=>0,'1_30'=>0,'31_60'=>0,'61_90'=>0,'90_plus'=>0];
        $totalOutstanding = 0;

        foreach ($pendingInvoices as $inv) {
            $amt  = (float)$inv['pending_amount'];
            $days = (int)$inv['age_days'];

            $totalOutstanding += $amt;

            if ($days <= 0)      $aging['current'] += $amt;
            elseif ($days <=30)  $aging['1_30'] += $amt;
            elseif ($days <=60)  $aging['31_60'] += $amt;
            elseif ($days <=90)  $aging['61_90'] += $amt;
            else                 $aging['90_plus'] += $amt;
        }

        $getcompany = \app\models\TblOwnershipCompany::find()->where(['id'=>$user_company])->one();
        if(isset($getcompany) && $getcompany->id != ""){
          $bill_from_cname = $getcompany->company_name;
          $bill_from_address = $getcompany->address;
          $bill_from_city = $getcompany->city;
          $bill_from_province = $getcompany->state;
          $bill_from_postal_code = $getcompany->postal_code;
          $bill_from_phone = $getcompany->phone;
          $bill_logo = $getcompany->logo;
          $bill_hst_no = $getcompany->hst_number;
          $bill_email = $getcompany->email;
        }else{
          //---by default keep it hk trailer parts ---
          $bill_from_cname = "HK Trailer Parts";
          $bill_from_address = "120 Orenda Rd #1d";
          $bill_from_city = "Brampton";
          $bill_from_province = "ON";
          $bill_from_postal_code = "L6W 1W2";
          $bill_from_phone = "647-282-6031";
          $bill_logo = "hktrailerparts-logo.png";
          $bill_hst_no = "787330810RT0001";
          $bill_email = "info@hktrailerparts.com";
        }

       $company = [
           'name'    => $bill_from_cname,
           'email'   => $bill_email,
           'phone'   => $bill_from_phone,
           'address' => $bill_from_address.', '.$bill_from_city.' '.$bill_from_province.' '.$bill_from_postal_code,
           'logo'    => $bill_logo ?? ''
       ];

        return $this->renderAjax('_statement_result', [
            'pendingInvoices'  => $pendingInvoices,
            'aging'            => $aging,
            'totalOutstanding' => $totalOutstanding,
            'from_date'        => $from_date,
            'to_date'          => $to_date,
            'client'           => TblClient::findOne($client_id),
            'company'          => $company,
            'mode'             => 'modal',
            'user_company'     => $user_company
        ]);


    }
    public function actionEmailStatement()
{
    Yii::$app->response->format = Response::FORMAT_JSON;

    $client_id    = Yii::$app->request->post('client_id');
    $from_date    = Yii::$app->request->post('from_date');
    $to_date      = Yii::$app->request->post('to_date');
    $user_company = Yii::$app->request->post('user_company');

    if (!$client_id || !$from_date || !$to_date) {
        return ['success' => false, 'message' => 'Invalid request'];
    }

    $client = TblClient::findOne($client_id);
    if (!$client || empty($client->email)) {
        return ['success' => false, 'message' => 'Customer email not found'];
    }
    /* -------------------------------------------------
     * COMPANY INFO (reuse your existing vars)
     * ------------------------------------------------- */
     $getcompany = \app\models\TblOwnershipCompany::find()->where(['id'=>$user_company])->one();
     if(isset($getcompany) && $getcompany->id != ""){
       $bill_from_cname = $getcompany->company_name;
       $bill_from_address = $getcompany->address;
       $bill_from_city = $getcompany->city;
       $bill_from_province = $getcompany->state;
       $bill_from_postal_code = $getcompany->postal_code;
       $bill_from_phone = $getcompany->phone;
       $bill_logo = $getcompany->logo;
       $bill_hst_no = $getcompany->hst_number;
       $bill_email = $getcompany->email;
     }else{
       //---by default keep it hk trailer parts ---
       $bill_from_cname = "HK Trailer Parts";
       $bill_from_address = "120 Orenda Rd #1d";
       $bill_from_city = "Brampton";
       $bill_from_province = "ON";
       $bill_from_postal_code = "L6W 1W2";
       $bill_from_phone = "647-282-6031";
       $bill_logo = "hktrailerparts-logo.png";
       $bill_hst_no = "787330810RT0001";
       $bill_email = "info@hktrailerparts.com";
     }

    $company = [
        'name'    => $bill_from_cname,
        'email'   => $bill_email,
        'phone'   => $bill_from_phone,
        'address' => $bill_from_address.', '.$bill_from_city.' '.$bill_from_province.' '.$bill_from_postal_code,
        'logo'    => $bill_logo ?? ''
    ];


    /* ----------------- STATEMENT DATA ----------------- */
    $today = date('Y-m-d');

    $pendingInvoices = (new \yii\db\Query())
        ->select([
            'invoice_number' => 'i.invoice_number',
            'invoice_date'   => 'i.invoice_date',
            'due_date'       => 'i.due_date',
            'total_amount'       => 'i.total_amount',
            'pending_amount' => 'i.total_amount - IFNULL(SUM(ar.amount_received),0)',
            'age_days'       => "DATEDIFF('{$today}', i.due_date)"
        ])
        ->from('tbl_invoice i')
        ->leftJoin(
            'tbl_account_receivable ar',
            'ar.fk_invoice_id = i.id AND ar.status = 1'
        )
        ->where([
            'i.status' => 1,
            'i.fk_client_id' => $client_id,
            'i.fk_bill_from_id' => $user_company
        ])
        ->andWhere(['between', 'i.invoice_date', $from_date, $to_date])
        ->groupBy('i.id')
        ->having('pending_amount > 0')
        ->all();

    if (empty($pendingInvoices)) {
        return ['success' => false, 'message' => 'No outstanding invoices'];
    }

    /* ----------------- AGING ----------------- */
    $aging = ['current'=>0,'1_30'=>0,'31_60'=>0,'61_90'=>0,'90_plus'=>0];
    $totalOutstanding = 0;

    foreach ($pendingInvoices as $inv) {
        $amt  = (float)$inv['pending_amount'];
        $days = (int)$inv['age_days'];
        $totalOutstanding += $amt;

        if ($days <= 0)      $aging['current'] += $amt;
        elseif ($days <=30)  $aging['1_30'] += $amt;
        elseif ($days <=60)  $aging['31_60'] += $amt;
        elseif ($days <=90)  $aging['61_90'] += $amt;
        else                 $aging['90_plus'] += $amt;
    }

    /* ----------------- PDF ----------------- */

    $html = $this->renderPartial('_statement_result', [
        'pendingInvoices'  => $pendingInvoices,
        'aging'            => $aging,
        'totalOutstanding' => $totalOutstanding,
        'from_date'        => $from_date,
        'to_date'          => $to_date,
        'client'           => $client,
        'company'          => $company,
        'mode'             => 'pdf',
        'user_company'     => $user_company
    ]);

    // $dir = Yii::getAlias('@runtime/client_statements');
      $dir = '/var/www/html/web/client-statements';
    // FileHelper::createDirectory($dir);

    $fileName = 'Client_Statement_' . $client_id . '_' . time() . '.pdf';
    $filePath = $dir . '/' . $fileName;

    $pdf = new Pdf([
        'mode' => Pdf::MODE_UTF8,
        'format' => Pdf::FORMAT_A4,
        'destination' => Pdf::DEST_FILE,
        'filename' => $filePath,
        'content' => $html,
        'cssInline'   => 'body {font-family: Arial, sans-serif;font-size: 12px;color: #000;margin: 20px;}table {width: 100%;border-collapse: collapse;}table th, td {padding: 6px;vertical-align: top;}.statement-header table,.statement-vendor table {border: none;}.statement-header td,.statement-vendor td {border: none;padding: 2px 0;}.statement-invoices thead th {background-color: #e9ecef !important;border-bottom: 2px solid #000;font-weight: bold;}.statement-invoices tbody td {border-bottom: 1px solid #ccc;}.statement-invoices tfoot th,.statement-invoices tfoot td {background-color: #f8f9fa !important;border-top: 2px solid #000;font-weight: bold;}.statement-aging thead th {background-color: #f1f3f5 !important;border-bottom: 1px solid #000;font-weight: bold;}.statement-aging tbody td {border-top: 1px solid #ccc;}.text-end { text-align: right; }.fw-bold { font-weight: bold; }.text-danger { color: #000; }',
        'options'     => ['title' => 'Client Statement'],
    ]);
    $pdf->render();

    /* ----------------- EMAIL ----------------- */
    $brevo = $this->sendViaBrevo(
        $client->email,
        $client->company_name,
        $filePath,
        $from_date,
        $to_date,
        $bill_from_cname,
        $bill_email,
        $bill_logo,
        $bill_from_address,
        $bill_from_city,
        $bill_from_province,
        $bill_from_postal_code
    );

    if (!$brevo['success']) {
        return ['success' => false, 'message' => 'Email failed'];
    }

    /* ----------------- LOG ----------------- */
    $session = Yii::$app -> session;
    Yii::$app->db->createCommand()->insert('tbl_client_statement_email_log', [
        'fk_client_id' => $client_id,
        'from_date'    => $from_date,
        'to_date'      => $to_date,
        'fk_ownership_id'=>$user_company,
        'message_id'   => $brevo['message_id'],
        'file_name'     => $fileName,
        'crt_time'   => date('Y-m-d H:i:s'),
        'status'       => 1,
        'crt_by' => $session -> get('userId'),
        'ip' => Yii::$app -> getRequest() -> getUserIp()
    ])->execute();

    return ['success' => true, 'message' => 'Customer statement emailed successfully'];
}
  private function sendViaBrevo($toEmail, $toName, $pdfPath, $fromDate, $toDate,$bill_from_cname,$bill_email,$bill_logo,$bill_from_address,$bill_from_city,$bill_from_province,$bill_from_postal_code)
  {
      $apiKey = Yii::$app->params['brevoApiKey'];
      // 'to' => [
      //     [
      //         'email' => $toEmail,
      //         'name'  => $toName
      //     ]
      // ],
      $payload = [
          'sender' => [
              'name'  => $bill_from_cname,
              'email' => $bill_email
          ],
          'to' => [],
          'bcc' => [ // Add multiple CC recipients here
            [
                'name' => $bill_from_cname,
                'email' => $bill_email
            ]
          ],
          'subject' => "Customer Statement ({$fromDate} to {$toDate}) | ".$bill_from_cname.' | '.date('d-M-Y h:ia'),
          'htmlContent' => "
              <p>Greetings {$toName},</p>
              <p>Please find the attached statement for the period
              <strong>{$fromDate}</strong> to <strong>{$toDate}</strong>.</p>
              <p>If you have any questions, please contact us.</p>
              <p>Regards,<br><strong>" . $bill_from_cname . "</strong></p>
              <img src='https://hkmis.ca/web/ownercompany-logo/".$bill_logo."' width='100px' /><br />
              <p>
              ".$bill_from_address."<br />".$bill_from_city." ".$bill_from_province." ".$bill_from_postal_code."
              </p>
          ",
          'attachment' => [
              [
                  'content' => base64_encode(file_get_contents($pdfPath)),
                  'name'    => basename($pdfPath)
              ]
          ]
      ];

      $emails = array_map('trim', explode(',', $toEmail));
      foreach ($emails as $email) {
        // Validate email
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $payload['to'][] = [
            "email" => $email,
            "name" => $toName
          ];
        }
      }

      $ch = curl_init('https://api.brevo.com/v3/smtp/email');
      curl_setopt_array($ch, [
          CURLOPT_HTTPHEADER => [
              'api-key: ' . $apiKey,
              'Content-Type: application/json',
              'accept: application/json'
          ],
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_POST => true,
          CURLOPT_POSTFIELDS => json_encode($payload)
      ]);

      $response = curl_exec($ch);
      $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);

      $data = json_decode($response, true);

      return [
          'success'    => $status >= 200 && $status < 300,
          'message_id' => $data['messageId'] ?? null
      ];
  }
}
