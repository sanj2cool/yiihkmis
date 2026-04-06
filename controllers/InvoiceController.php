<?php

namespace app\controllers;
use Yii;
use app\models\TblInvoice;
use app\models\TblInvoiceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\TblInvoiceItem;
use app\models\TblClient;
use app\models\TblProduct;
use app\models\TblTaxRate;
use app\models\TblInvoiceSentLog;
use app\models\TblProductReceiving;
use app\models\TblOwnershipCompany;
use app\models\TblMenuAccess;
use app\models\TblAccountReceivable;
use yii\db\Query;
use Mpdf\Mpdf;
use kartik\mpdf\Pdf;
ini_set('max_execution_time', 900); //300 seconds = 5 minutes
ini_set('memory_limit', '2056M'); // or you could use 1G
/**
 * InvoiceController implements the CRUD actions for TblInvoice model.
 */
class InvoiceController extends Controller
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
        $menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>22,'status'=>1])->one();
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
     * Lists all TblInvoice models.
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
        $searchModel = new TblInvoiceSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionResetFilters()
    {
        $searchModel = new TblInvoiceSearch();
        $searchModel->clearSearchState(); // Clear the session data
        return $this->redirect(['index']); // Redirect back to the index page
    }
    /**
     * Displays a single TblInvoice model.
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

    public function actionViewcopy($id)
    {
      $session = Yii::$app->session;
      if(!isset($session['username'])){
        Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
        return $this->redirect(['site/login']);
      }
        return $this->render('viewcopy', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TblInvoice model.
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
        $model = new TblInvoice();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
              $product = Yii::$app->request->post('product');
              $product_tier = Yii::$app->request->post('product_tier');
              $description = Yii::$app->request->post('desc');
              $qty = Yii::$app->request->post('qty');
              $rate = Yii::$app->request->post('rate');
              $amount = Yii::$app->request->post('amount');
              if(isset($qty) && count($qty) > 0){
                for($i = 0; $i < count($qty); $i++){
                  $modelItem[$i] = new TblInvoiceItem();
                  $modelItem[$i]->fk_invoice_id = $model->id;
                  $modelItem[$i]->fk_product_id = $product[$i];
                  $modelItem[$i]->tier_id = $product_tier[$i];
                  $modelItem[$i]->description = $description[$i];
                  $modelItem[$i]->quantity = $qty[$i];
                  $modelItem[$i]->unit_price = $rate[$i];
                  $modelItem[$i]->total_price = $amount[$i];
                  if($modelItem[$i]->save()){
                  }else{
                    print_r($modelItem[$i]->getErrors());
                  }
                }//----for loop ended-------
              }//-----items exists-------
              //---IF IT IS RELATED TO ORDER UPDATE THE ORDER TO HAVE THIS INVOICE ID ADDED AND UPDATE THE STATUS OF ORDER TO INVOICE CREATED =-----
              $order_id_received = $_POST['order_id_received'] ?? '';
              if($order_id_received != ""){
                $order = \app\models\TblOrder::find()->where(['id'=>$order_id_received])->one();
                if(isset($order) && $order->id != ""){
                  $order->fk_invoice_id = $model->id;
                  $order->order_status = 5;
                  $order->save();
                }
              }
                // return $this->redirect(['view', 'id' => $model->id]);
                if(isset($_POST['new_update'])){
                    return $this->redirect(['update', 'id' => $model->id]);
                }else if(isset($_POST['new_new'])){
                    // return $this->redirect(['create']);
                    return $this->redirect(['view', 'id' => $model->id]);
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
     * Updates an existing TblInvoice model.
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
          //---------invoice items entries start -------------
            TblInvoiceItem::updateAll(array('status' => 0),'fk_invoice_id='.$model->id);
            $product = Yii::$app->request->post('product');
            $product_tier = Yii::$app->request->post('product_tier');
            $description = Yii::$app->request->post('desc');
            $qty = Yii::$app->request->post('qty');
            $rate = Yii::$app->request->post('rate');
            $amount = Yii::$app->request->post('amount');
            $product_qty = Yii::$app->request->post('product_qty');
            $old_qty = Yii::$app->request->post('old_qty');
            if(isset($qty) && count($qty) > 0){
              for($i = 0; $i < count($qty); $i++){
                $modelItem[$i] = new TblInvoiceItem();
                $modelItem[$i]->fk_invoice_id = $model->id;
                $modelItem[$i]->fk_product_id = $product[$i];
                $modelItem[$i]->tier_id = $product_tier[$i];
                $modelItem[$i]->description = $description[$i];
                $modelItem[$i]->quantity = $qty[$i];
                $modelItem[$i]->unit_price = $rate[$i];
                $modelItem[$i]->total_price = $amount[$i];
                if($modelItem[$i]->save()){
                  //-------we need to check if qty has been changed ---------
                }else{
                  // print_r($modelItem[$i]->getErrors());
                }
              }//----for loop ended-------
            }//-----items exists-------
            //---------invoice items entries end -------------
            // return $this->redirect(['view', 'id' => $model->id]);
            if(isset($_POST['update'])){
                return $this->redirect(['update', 'id' => $model->id]);
            }else if(isset($_POST['new'])){
                // return $this->redirect(['create']);
                return $this->redirect(['view', 'id' => $model->id]);
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
     * Deletes an existing TblInvoice model.
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
        //--------CHECK IF INVOICE HAS EXISTING PAYMENT THEN CAN NOT ALLOW USER TO DELETE THE INVOICE ------
        $check = \app\models\TblAccountReceivable::find()->where(['fk_invoice_id'=>$id,'status'=>1])->count();
        if($check > 0){
          Yii::$app->getSession()->setFlash('error', 'There is a payment entry existing for this invoice. Please delete the payment entry and try again.');
        }else{
          $model = TblInvoice::find()->where(['id'=>$id])->one();
          $model->status = 0;
          if($model->save()){
            TblInvoiceItem::updateAll(array('status' => 0),'fk_invoice_id='.$id);
            //---IF THE ORDER IS CONNECTED TO THE INVOICE THEN REMOVE THE INVOICE_ID FROM THE ORDER AS WELL ----
            $check = \app\models\TblOrder::find()->where(['fk_invoice_id'=>$id])->one();
            if(isset($check) && $check->id != ""){
              $check->fk_invoice_id = null;
              $check->order_status = 3;
              $check->save();
            }
            Yii::$app->getSession()->setFlash('success', 'Record deleted successfully.');
          }else{
            Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
          }
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblInvoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return TblInvoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblInvoice::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }



    public function actionSendInvoiceOld($id){
      // $id = 2; //-----this is the id we will be getting from the browser ----
      $model = TblInvoice::find()->where(['id'=>$id])->andWhere('status != 0')->one();
      if(isset($model) && $model->id != ""){
        $getvendor = TblClient::find()->where(['id'=>$model->fk_client_id])->one();
        if(isset($getvendor) && $getvendor->id != ""){
            $vendor_name = $getvendor->company_name;
            $vendor_address = $getvendor->address.' '.$getvendor->city;
            $vendor_state_postal = $getvendor->state.' '.$getvendor->postal_code;
            $vendor_phone = $getvendor->phone;
            $vendor_email = $getvendor->email;
        }else{
            $vendor_name = "";
            $vendor_address = "";
            $vendor_state_postal = "";
            $vendor_phone = "";
            $vendor_email = "";
        }
        $gethst = TblTaxRate::find()->where(['id'=>$model->hst,'status'=>1])->one();
        if(isset($gethst) && $gethst->id != ""){
          $hst = $gethst->tax_rate.'%';
          $tax_desc = $gethst->description;
        }else{
          $hst = "0%";
          $tax_desc = "Tax";
        }

        $getcompany = TblOwnershipCompany::find()->where(['id'=>$model->fk_bill_from_id])->one();
        if(isset($getcompany) && $getcompany->id != ""){
          $bill_from_cname = $getcompany->company_name;
          $bill_from_address = $getcompany->address;
          $bill_from_city = $getcompany->city;
          $bill_from_province = $getcompany->state;
          $bill_from_postal_code = $getcompany->postal_code;
          $bill_from_phone = $getcompany->phone;
          $bill_logo = $getcompany->logo;
        }else{
          //---by default keep it hk trailer parts ---
          $bill_from_cname = "HK Trailer Parts";
          $bill_from_address = "120 Orenda Rd #1d";
          $bill_from_city = "Brampton";
          $bill_from_province = "ON";
          $bill_from_postal_code = "L6W 1W2";
          $bill_from_phone = "647-282-6031";
          $bill_logo = "hktrailerparts-logo.png";
        }

        $email_body = '<style>
        .container-cust {
            font-family: Arial, sans-serif;
            font-size:13px;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .container-cust {
            width: 90%;
            margin: auto;
            padding: 20px;
            max-width: 800px;
        }
        .header-cust, .footer-cust {
            text-align: center;
            margin-bottom: 20px;
        }
        .section-cust {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .section-cust div {
            width: 30%;
        }
        .addresses, .dates, .comments-totals {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .addresses div, .dates div, .comments-totals div {
            width: 48%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: auto;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .footer-cust {
            font-size: 0.8em;
            color: #555;
        }
        .align-text-right p{
            text-align:right;
        }
        .col-left-print {
          width: 50%;
        }

        .col-left-print img {
          width: 50%; /* Adjust logo size */
        }
        @media (max-width: 576px) {
            .section-cust {
                flex-direction: column;
            }
            .section-cust div {
                width: 100%;
                margin-bottom: 10px;
            }
            .comments-totals {
                flex-direction: column;
            }
            .comments-totals div {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
    <div class="container-cust" style="border:1px solid lightgrey;border-radius:5px;">
    <div class="comments-totals">
      <div>
        <p>
          <strong>'.$bill_from_cname.'</strong><br>
          '.$bill_from_address.', '.$bill_from_city.'<br />
          '.$bill_from_province.' '.$bill_from_postal_code.'<br>
          Cell: '.$bill_from_phone.'
        </p>
      </div>
      <div class="col-left-print" align="right">
        <img src="https://hkmis.ca/web/ownercompany-logo/'.$bill_logo.'">
      </div>
    </div>
        <div class="header-cust">
            <h1>Invoice</h1>
        </div>
        <div class="comments-totals">
            <div>
                <h4>Bill To:</h4>
                <p>
                    '.$vendor_name.'<br>
                    '.$vendor_address.'<br>
                    '.$vendor_state_postal.'<br>
                    '.$vendor_phone.'
                </p>
            </div>
            <div style="text-align:right;">
                <h4>Invoice Details</h4>
                <p>
                    <strong>Invoice #:</strong> <span>'.$model->invoice_number.'</span><br>
                    <strong>Invoice Date:</strong> <span>'.$model->invoice_date.'</span><br>
                    <strong>Due Date:</strong> <span>'.$model->due_date.'</span>
                </p>
            </div>
        </div>
        <div style="overflow-x: auto;">


        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Rate</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>';
                    $getitems = TblInvoiceItem::find()->where(['fk_invoice_id'=>$model->id,'status'=>1])->all();
                    if(isset($getitems) && count($getitems) > 0){
                        foreach($getitems as $gi){
                          // if($gi->type == 2){
                            //---its product---
                            $getprod = TblProduct::find()->where(['id'=>$gi->fk_product_id])->one();
                            if(isset($getprod) && $getprod->id != ""){
                              $item = $getprod->name.' ['.$getprod->internal_sku.']';
                            }else{
                              $item = "";
                            }
                          // }else{
                          //   //----its item ------
                          //   $item = $gi->item;
                          // }
                          $email_body .= ' <tr>
                              <td>'.$item.'</td>
                              <td>'.$gi->description.'</td>
                              <td>'.$gi->quantity.'</td>
                              <td>$'.$gi->unit_price.'</td>
                              <td>$'.$gi->total_price.'</td>
                          </tr>';
                        }
                    }
            $email_body .= '</tbody>
        </table>
        </div>

        <div class="comments-totals">
            <div>
                <h4>Comments</h4>
                <p>'.$model->comments.'</p>
            </div>
            <div class="align-text-right">
                <p><strong>Subtotal:</strong> $'.$model->subtotal.'</p>
                <p><strong>'.strtoupper($tax_desc).' ('.$hst.'):</strong> $'.$model->hst_amount.'</p>
                <p><strong>Total Amount:</strong> $'.$model->total_amount.'</p>
            </div>
        </div>

        <div class="footer-cust">
            <p>If you have any questions about this invoice, please contact us at invoice@hktrailerparts.com</p>
        </div>
    </div>';

      // $sender_name = "HK Trailer Parts";
      $sender_name = $bill_from_cname;
      // $sender_email = "hktrailerpartsit@gmail.com";
      $sender_email = "info@hktrailerparts.com";
      $subject = "Invoice #".$model->invoice_number." issued from HK Trailer Parts";
      // if (filter_var(trim($vendor_email), FILTER_VALIDATE_EMAIL)) {
        $send_arr = [
          "sender" => [
            "name" => $sender_name,
            "email" => $sender_email
          ],
          "to" => [],
          "cc" => [ // Add multiple CC recipients here
            [
                "email" => $sender_email,
                "name" => $sender_name
            ]
          ],
          "subject" => $subject,
          "htmlContent" => $email_body
        ];

        // "to" => [
        //   [
        //     "email" => $vendor_email,
        //     "name" => $vendor_name
        //   ]
        // ],
        //
        // echo json_encode($send_arr);
        // Split the $send_to string into an array
        $emails = array_map('trim', explode(',', $vendor_email));
        foreach ($emails as $email) {
          // Validate email
          if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $send_arr['to'][] = [
              "email" => $email,
              "name" => $vendor_name
            ];
          }
        }
        // Check if there are valid recipients
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
              'api-key: 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',
              'Accept: application/json',
              'Content-Type: application/json'
            ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $decode_resp = json_decode($response,true);
            if(isset($decode_resp['messageId']) && $decode_resp['messageId'] != ""){
              $modelSent = new TblInvoiceSentLog();
              $modelSent->fk_invoice_id = $model->id;
              $modelSent->message_id = $decode_resp['messageId'];
              $modelSent->save();
              Yii::$app->getSession()->setFlash('success', 'Email sent successfully.');
            }else{
                Yii::$app->getSession()->setFlash('error', 'Email-id not found. Please enter email-id and try again.');
            }
          }

          return $this->redirect(['view','id'=>$model->id]);
      }else{
        Yii::$app->getSession()->setFlash('error', 'Invoice not found.');
        return $this->redirect(['index']);
      }

    }
    public function actionGetproductdesc(){
      $session = Yii::$app->session;
      $user_company = $session['userCompany'];
      $product_id = $_POST['product_id'];
      //--------check for client --------

      $tier_id = ''; //---need to keep 1 as default ------
      $get_tier_markup = Yii::$app->params['defaultPricingTier']; //----this is defualt markup
      $getproduct = TblProduct::find()->where(['id'=>$product_id])->one();
      if(isset($getproduct) && $getproduct->id != ""){
        $description = $getproduct->website_description;
        if($getproduct->default_tier != "" && $getproduct->default_tier != null){
            $tier_id = $getproduct->default_tier;
        }

        if($tier_id == 1 && $getproduct->tier_1_markup != ""){
          $get_tier_markup = $getproduct->tier_1_markup;
        }else if($tier_id == 2 && $getproduct->tier_2_markup != ""){
          $get_tier_markup = $getproduct->tier_2_markup;
        }else if($tier_id == 3 && $getproduct->tier_3_markup != ""){
          $get_tier_markup = $getproduct->tier_3_markup;
        }else if($tier_id == 4 && $getproduct->tier_4_markup != ""){
          $get_tier_markup = $getproduct->tier_4_markup;
        }else if($tier_id == 5 && $getproduct->tier_5_markup != ""){
          $get_tier_markup = $getproduct->tier_5_markup;
        }else if($tier_id == 6 && $getproduct->tier_6_markup != ""){
          $get_tier_markup = $getproduct->tier_6_markup;
        }else if($tier_id == 7 && $getproduct->tier_7_markup != ""){
          $get_tier_markup = $getproduct->tier_7_markup;
        }else if($tier_id == 8 && $getproduct->tier_8_markup != ""){
          $get_tier_markup = $getproduct->tier_8_markup;
        }else if($tier_id == 9 && $getproduct->tier_9_markup != ""){
          $get_tier_markup = $getproduct->tier_9_markup;
        }else if($tier_id == 10 && $getproduct->tier_10_markup != ""){
          $get_tier_markup = $getproduct->tier_10_markup;
        }else if($tier_id == 11 && $getproduct->tier_11_markup != ""){
          $get_tier_markup = $getproduct->tier_11_markup;
        }
      }else{
        $description = "";
      }
      //-----get the recent most product pricing from TblProductReceiving----
      //----WE NEED TO GET THE PRICING AS PER THE SELECTED OWNERSHIP COMPANY NOT THE RECENT ENTRY HERE -----

      $getrecent = TblProductReceiving::find()
                  ->where(['fk_product_id'=>$product_id])
                  ->andWhere(['status'=>1,'fk_location_id'=>$user_company])
                  ->orderBy(['id'=>SORT_DESC])
                  ->one();
      $price = 0; //----default price ------
      if(isset($getrecent) && $getrecent->price_per_item != ""){
        $price_per_item = $getrecent->price_per_item;
        $price = $price_per_item+$price_per_item*($get_tier_markup/100);
        $price = number_format($price,2,'.','');
        // $price = round($price,2);
      }
      $array = array("description" => $description,'price' => $price,'tier'=>$tier_id);
      // return $description;
      return json_encode($array);
    }
    public function actionGetpdouctpricetier(){
      $session = Yii::$app->session;
      $user_company = $session['userCompany'];
      $product_id = $_POST['product_id'];
      $tier_id = $_POST['tier_id'];
      $get_tier_markup = Yii::$app->params['defaultPricingTier']; //----this is defualt markup
      $getproduct = TblProduct::find()->where(['id'=>$product_id])->one();
      if(isset($getproduct) && $getproduct->id != ""){

        // if($getproduct->default_tier != "" && $getproduct->default_tier != null){
        //     $tier_id = $getproduct->default_tier;
        // }

        if($tier_id == 1 && $getproduct->tier_1_markup != ""){
          $get_tier_markup = $getproduct->tier_1_markup;
        }else if($tier_id == 2 && $getproduct->tier_2_markup != ""){
          $get_tier_markup = $getproduct->tier_2_markup;
        }else if($tier_id == 3 && $getproduct->tier_3_markup != ""){
          $get_tier_markup = $getproduct->tier_3_markup;
        }else if($tier_id == 4 && $getproduct->tier_4_markup != ""){
          $get_tier_markup = $getproduct->tier_4_markup;
        }else if($tier_id == 5 && $getproduct->tier_5_markup != ""){
          $get_tier_markup = $getproduct->tier_5_markup;
        }else if($tier_id == 6 && $getproduct->tier_6_markup != ""){
          $get_tier_markup = $getproduct->tier_6_markup;
        }else if($tier_id == 7 && $getproduct->tier_7_markup != ""){
          $get_tier_markup = $getproduct->tier_7_markup;
        }else if($tier_id == 8 && $getproduct->tier_8_markup != ""){
          $get_tier_markup = $getproduct->tier_8_markup;
        }else if($tier_id == 9 && $getproduct->tier_9_markup != ""){
          $get_tier_markup = $getproduct->tier_9_markup;
        }else if($tier_id == 10 && $getproduct->tier_10_markup != ""){
          $get_tier_markup = $getproduct->tier_10_markup;
        }else if($tier_id == 11 && $getproduct->tier_11_markup != ""){
          $get_tier_markup = $getproduct->tier_11_markup;
        }
      }
      $getrecent = TblProductReceiving::find()
                  ->where(['fk_product_id'=>$product_id])
                  ->andWhere(['status'=>1,'fk_location_id'=>$user_company])
                  ->orderBy(['id'=>SORT_DESC])
                  ->one();
      $price = 0; //----default price ------
      if(isset($getrecent) && $getrecent->price_per_item != ""){
        $price_per_item = $getrecent->price_per_item;
        $price = $price_per_item+$price_per_item*($get_tier_markup/100);
        $price = number_format($price,2,'.','');
        // $price = round($price,2);
      }
      $array = array('price' => $price);
      // return $description;
      return json_encode($array);
    }
    public function actionGetTaxOwnership(){
      $bill_from = $_POST['bill_from'];
      $tax_id = 9;
      $getbfrom = TblOwnershipCompany::find()->where(['id'=>$bill_from])->one();
      if(isset($getbfrom) && $getbfrom->fk_tax_id != ""){
        $tax_id = $getbfrom->fk_tax_id;
      }
      return $tax_id;
    }
    //----NEW INVOICE SEND CODE ------
    public function actionSendInvoice($id){
      // $id = 2; //-----this is the id we will be getting from the browser ----
      $model = TblInvoice::find()->where(['id'=>$id])->andWhere('status != 0')->one();
      if(isset($model) && $model->id != ""){
        $getvendor = TblClient::find()->where(['id'=>$model->fk_client_id])->one();
        if(isset($getvendor) && $getvendor->id != ""){
            $vendor_name = $getvendor->company_name;
            $vendor_address = $getvendor->address.' '.$getvendor->city;
            $vendor_state_postal = $getvendor->state.' '.$getvendor->postal_code;
            $vendor_phone = $getvendor->phone;
            $vendor_email = $getvendor->email;
        }else{
            $vendor_name = "";
            $vendor_address = "";
            $vendor_state_postal = "";
            $vendor_phone = "";
            $vendor_email = "";
        }
        $gethst = TblTaxRate::find()->where(['id'=>$model->hst,'status'=>1])->one();
        if(isset($gethst) && $gethst->id != ""){
          $hst = $gethst->tax_rate.'%';
          $tax_desc = $gethst->description;
        }else{
          $hst = "0%";
          $tax_desc = "Tax";
        }

        $getcompany = TblOwnershipCompany::find()->where(['id'=>$model->fk_bill_from_id])->one();
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
        //-----get total received -------
        $totalRevenue = (new Query())
        ->select(['SUM(ROUND(amount_received, 2)) AS total_revenue'])
        ->from('tbl_account_receivable')
        ->where(['!=', 'status', 0])
        ->andWhere('fk_invoice_id = '.$model->id)
        ->scalar();
        // echo $totalRevenue;
        if($totalRevenue > 0 && $totalRevenue != ""){
          $amount_received = $totalRevenue;
        }else{
          $amount_received = 0;
        }
        if($amount_received > 0){
          $balance_due = $model->total_amount-$amount_received;
        }else{
          $balance_due = $model->total_amount;
        }
        $filename = $this->createpdf($id);
        $email_body = '<!DOCTYPE html>
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
        <h3>Greetings '.$vendor_name.',</h3><br /><br />
        <p>I hope this message finds you well.</p>';

          $email_body .= '
          <p>Please find the invoice attached to this email for the services rendered. The total amount for the invoice is <strong>$'.number_format($model->total_amount,2,'.',',').'</strong> out of which <strong>$'.number_format($balance_due,2,'.',',').'</strong> is due.</p>
          <p>Please review the attached invoice, and let us know if you have any questions or require any modifications.</p>
        ';
        $email_body .= '
        <br /><p>We appreciate your trust in '.$bill_from_cname.' and look forward to working with you.!</p><br /><br />
        <p><strong>Best Regards,</strong><br>'.$bill_from_cname.'</p>
          <img src="https://hkmis.ca/web/ownercompany-logo/'.$bill_logo.'" style="width:150px !important;">
        <p>
        '.$bill_from_address.'<br />'.$bill_from_city.' '.$bill_from_province.' '.$bill_from_postal_code.'
        </p>
        </td>
        </tr>
        </table>
        </body>
        </html>
        ';

      // $sender_name = "HK Trailer Parts";
      $sender_name = $bill_from_cname;
      $sender_email = $bill_email;
      // $sender_email = "info@hktrailerparts.com";
      $subject = "Invoice #".$model->invoice_number." issued from ".$bill_from_cname.' | '.date('Y-m-d h:ia');
      $filename = Yii::getAlias('@webroot/invoices/') . $file; // Path to your file
      $fileContent = file_get_contents($filePath); // Read the file content
      $base64File = base64_encode($fileContent); // Encode the file in Base64
      $filenamesend = "Invoice-".$model->invoice_number.".pdf";
      // if (filter_var(trim($vendor_email), FILTER_VALIDATE_EMAIL)) {
        $send_arr = [
          "sender" => [
            "name" => $sender_name,
            "email" => $sender_email
          ],
          "to" => [],
          "cc" => [
            [
                "email" => $sender_email,
                "name" => $sender_name
            ]
          ],
          "subject" => $subject,
          "htmlContent" => $email_body,
          "attachment" => [
                [
                    "content" => $base64File,
                    "name" => $filenamesend // Name of the file
                ]
            ]
        ];


        // echo json_encode($send_arr);
        // Split the $send_to string into an array
        $emails = array_map('trim', explode(',', $vendor_email));
        foreach ($emails as $email) {
          // Validate email
          if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $send_arr['to'][] = [
              "email" => $email,
              "name" => $vendor_name
            ];
          }
        }
        // Check if there are valid recipients
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
              'api-key: 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',
              'Accept: application/json',
              'Content-Type: application/json'
            ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $decode_resp = json_decode($response,true);
            if(isset($decode_resp['messageId']) && $decode_resp['messageId'] != ""){
              $modelSent = new TblInvoiceSentLog();
              $modelSent->fk_invoice_id = $model->id;
              $modelSent->message_id = $decode_resp['messageId'];
              $modelSent->save();
              Yii::$app->getSession()->setFlash('success', 'Email sent successfully.');
            }else{
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }
          }

          return $this->redirect(['view','id'=>$model->id]);
      }else{
        Yii::$app->getSession()->setFlash('error', 'Invoice not found.');
        return $this->redirect(['index']);
      }

    }
    public function createpdf($id){
      // $id = 23;
      // $id = 266;
      $file = "";
      $model = TblInvoice::find()->where(['id'=>$id])->one();
      if(isset($model) && $model->id != ""){
        $getvendor = TblClient::find()->where(['id'=>$model->fk_client_id])->one();
        if(isset($getvendor) && $getvendor->id != ""){
          $vendor_name = $getvendor->company_name;
          $vendor_address = $getvendor->address.' '.$getvendor->city;
          $vendor_state_postal = $getvendor->state.' '.$getvendor->postal_code;
          $vendor_phone = $getvendor->phone;
        }else{
          $vendor_name = "";
          $vendor_address = "";
          $vendor_state_postal = "";
          $vendor_phone = "";
        }
        $gethst = TblTaxRate::find()->where(['id'=>$model->hst,'status'=>1])->one();
        if(isset($gethst) && $gethst->id != ""){
          $tax_desc = $gethst->description;
          $hst = $gethst->tax_rate.'%';
        }else{
          $tax_desc = "Tax";
          $hst = "0%";
        }

        $getcompany = TblOwnershipCompany::find()->where(['id'=>$model->fk_bill_from_id])->one();
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
        //-----get total received -------
        $totalRevenue = (new Query())
        ->select(['SUM(ROUND(amount_received, 2)) AS total_revenue'])
        ->from('tbl_account_receivable')
        ->where(['!=', 'status', 0])
        ->andWhere('fk_invoice_id = '.$model->id)
        ->scalar();
        // echo $totalRevenue;
        if($totalRevenue > 0 && $totalRevenue != ""){
          $amount_received = $totalRevenue;
        }else{
          $amount_received = 0;
        }
        $balance_due = $model->total_amount-$amount_received;
        $email_body = '<div class="container-cust" style="border:1px solid lightgrey;border-radius:5px;padding:5px;">
          <div style="align-items: center;margin-bottom: 20px;width:100%">
            <div style="float: left; width: 50%;">
              <p>
                <strong>'.$bill_from_cname.'</strong><br>
                '.$bill_from_address.', '.$bill_from_city.'<br />
                '.$bill_from_province.' '.$bill_from_postal_code.'<br>
                Cell: '.$bill_from_phone.'<br />
                HST #: '.$bill_hst_no.'
              </p>
            </div>
            <div class="col-left-print" style="float: left; width: 50%;text-align:right">
              <img src="https://hkmis.ca/web/ownercompany-logo/'.$bill_logo.'">
            </div>
          </div>
          <div class="header-cust" style="text-align: center;margin-bottom: 20px;">
            <h1>Invoice</h1>
          </div>
          <div class="width: 100%; margin-bottom: 20px; overflow: hidden;">
            <div style="float: left; width: 50%; text-align: left;">
              <h4 style="font-weight:800;">Bill To:</h4>
              <p>
                '.$vendor_name.'<br>
                '.$vendor_address.'<br>
                '.$vendor_state_postal.'<br>
                '.$vendor_phone.'
              </p>
            </div>

            <div style="float: left; width: 50%; text-align: right;">
              <h4>Invoice Details</h4>
              <p>
                <strong>Invoice #:</strong> <span>'.$model->invoice_number.'</span><br>
                <strong>Invoice Date:</strong> <span>'.$model->invoice_date.'</span><br>
                <strong>Due Date:</strong> <span>'.$model->due_date.'</span><br />
                <strong>PO #:</strong> <span>'.strtoupper($model->po_number).'</span>
              </p>
            </div>
          </div>
          <div style="overflow-x: auto;margin-top:20px">


            <table style="width: 100%;border-collapse: collapse;margin-bottom: 20px;table-layout: auto;border: 1px solid #ddd;">
              <thead>
                <tr>
                  <th style="padding: 8px;text-align: left;background-color: #f2f2f2;border: 1px solid #ddd;">Item</th>
                  <th style="padding: 8px;text-align: left;background-color: #f2f2f2;border: 1px solid #ddd;">Description</th>
                  <th style="padding: 8px;text-align: left;background-color: #f2f2f2;border: 1px solid #ddd;">Quantity</th>
                  <th style="padding: 8px;text-align: left;background-color: #f2f2f2;border: 1px solid #ddd;">Rate</th>
                  <th style="padding: 8px;text-align: left;background-color: #f2f2f2;border: 1px solid #ddd;">Amount</th>
                </tr>
              </thead>
              <tbody>';
                $getitems = TblInvoiceItem::find()->where(['fk_invoice_id'=>$model->id,'status'=>1])->all();
                if(isset($getitems) && count($getitems) > 0){
                  foreach($getitems as $gi){
                    //---its product---
                    $getprod = TblProduct::find()->where(['id'=>$gi->fk_product_id])->one();
                    if(isset($getprod) && $getprod->id != ""){
                      $item = $getprod->name.' ['.$getprod->internal_sku.']';
                    }else{
                      $item = "";
                    }
                    $email_body .= ' <tr>
                    <td style="padding: 8px;text-align: left;border: 1px solid #ddd;">'.$item.'</td>
                    <td style="padding: 8px;text-align: left;border: 1px solid #ddd;">'.$gi->description.'</td>
                    <td style="padding: 8px;text-align: left;border: 1px solid #ddd;">'.$gi->quantity.'</td>
                    <td style="padding: 8px;text-align: left;border: 1px solid #ddd;">$'.$gi->unit_price.'</td>
                    <td style="padding: 8px;text-align: left;border: 1px solid #ddd;">$'.$gi->total_price.'</td>
                    </tr>';
                  }
                }
              $email_body .= '</tbody>
            </table>
          </div>

          <div style="align-items: center;margin-bottom: 20px;width:100%">
            <div style="float: left; width: 50%;">
              <h4>Comments</h4>
              <p>'.$model->comments.'</p>
            </div>
            <div class="col-left-print" style="float: left; width: 50%;text-align:right">
              <p style="margin-bottom:0px;"><strong>Subtotal:</strong> $'.$model->subtotal.'</p>';
              $new_subtotal = $model->subtotal;
              if($model->discount_type == 1 && $model->discount_value != ""){
                //----its discount value ----
                $dis_value = $model->discount_value;
                $new_subtotal = $new_subtotal-$dis_value;
                $email_body .= '<p style="margin-bottom:0px;"><strong>Discount:</strong> -$'.number_format($dis_value,2,'.',',').'</p>';
              }else if($model->discount_type == 2 && $model->discount_value != ""){
                $dis_value = $model->subtotal*($model->discount_value/100);
                $new_subtotal = $new_subtotal-$dis_value;
                $email_body .= '<p style="margin-bottom:0px;"><strong>Discount:</strong> -$'.number_format($dis_value,2,'.',',').'</p>';
              }
              if($model->hst == 11){
                //---its quebec location ----
                //---first get the 5%----
                $five_tax = $model->subtotal*(5/100);
                $quebec_tax = $model->hst_amount-$five_tax;

                $email_body .= '<p style="margin-bottom:0px;"><strong>GST (5%) on $'.number_format($new_subtotal,2,'.',',').':</strong> $'.number_format($five_tax,2,'.',',').'</p>
                <p style="margin-bottom:0px;"><strong>QST (9.975%) on $'.number_format($new_subtotal,2,'.',',').':</strong> $'.number_format($quebec_tax,2,'.',',').'</p>';
              }else{
                $email_body .= '<p style="margin-bottom:0px;"><strong>'.$tax_desc.' ('.$hst.') on $'.number_format($new_subtotal,2,'.',',').':</strong> $'.$model->hst_amount.'</p>';
              }
              $email_body .= '

              <p style="margin-bottom:0px;"><strong>Total Amount:</strong> $'.$model->total_amount.'</p>';
              $email_body .= '<p style="margin-bottom:0px;"><strong>Amount Recieved:</strong> $'.number_format($amount_received,2,'.',',').'</p>
              <p style="margin-bottom:0px;"><strong>Balance Due:</strong> $'.number_format($balance_due,2,'.',',').'</p>';
              $email_body .= '
            </div>
          </div>

          <div style="text-align: center;margin-bottom: 20px;width:100%">
            <p>If you have any questions about this invoice, please contact us at '.$bill_email.'</p>
          </div>
        </div>';
        $file = "invoice_".$id."-".date('Y-m-dh:iA').".pdf";
        $filename =  Yii::getAlias('@webroot/invoices/') . $file;
        if($balance_due == 0){
          //---paid ===
          $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'filename' => $filename,
            'destination' => Pdf::DEST_FILE,
            'content' => $email_body,
            'marginTop' => '5',
            'marginLeft' => '5',
            'marginRight' => '5',
            'marginBottom' => '5',
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'BW Invoice'],
            'methods' => [
              'SetHeader'=>[],
              'SetFooter'=>['{PAGENO}'],
              'SetWatermarkImage' => ['/var/www/vhosts/hkmis.ca/httpdocs/web/images/paid-invoice.png'],
            ]
          ]);
          // Customize watermark style
          // Access the mPDF instance
          $mpdf = $pdf->api; // Get the mPDF object
          $mpdf->showWatermarkImage = true; // Enable the watermark image
          $mpdf->watermarkImageAlpha = 0.2;
        }else{
          //---not paid ------
          $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'filename' => $filename,
            'destination' => Pdf::DEST_FILE,
            'content' => $email_body,
            'marginTop' => '5',
            'marginLeft' => '5',
            'marginRight' => '5',
            'marginBottom' => '5',
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'BW Invoice'],
            'methods' => [
              'SetHeader'=>[],
              'SetFooter'=>['{PAGENO}'],
            ]
          ]);
        }
        $pdf->render();
      }
      return $file;
    }
	
public function actionDownloadPdf($id)
{
    $file = $this->createpdf($id);

    if (!$file) {
        throw new NotFoundHttpException('PDF not generated');
    }

    $filePath = Yii::getAlias('@webroot/invoices/') . $file;

    $model = TblInvoice::findOne($id);
$invoiceNo = $model ? str_replace('#', '', $model->invoice_number) : $id;

$downloadName = 'invoice-' . $invoiceNo . '.pdf';

    return Yii::$app->response->sendFile($filePath, $downloadName);
}
}


