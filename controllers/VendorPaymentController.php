<?php

namespace app\controllers;
use Yii;
use app\models\TblVendorPayment;
use app\models\TblVendorPaymentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;
use yii\db\Expression;
use kartik\mpdf\Pdf;
use yii\helpers\Url;
use app\models\TblVendorPaymentFile;
use app\models\TblVendor;
use yii\web\Response;
use app\models\TblPreferredPaymentMethod;
use app\models\TblVendorAccountPayable;
/**
* VendorPaymentController implements the CRUD actions for TblVendorPayment model.
*/
class VendorPaymentController extends Controller
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
  * Lists all TblVendorPayment models.
  *
  * @return string
  */
  public function actionIndex()
  {
    $searchModel = new TblVendorPaymentSearch();
    $dataProvider = $searchModel->search($this->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
  * Displays a single TblVendorPayment model.
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
  * Creates a new TblVendorPayment model.
  * If creation is successful, the browser will be redirected to the 'view' page.
  * @return string|\yii\web\Response
  */
  function clean($string) {
    $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
    return preg_replace('/[^A-Za-z0-9.\-]/', '', $string); // Removes special chars.
  }
  public function actionCreate()
  {
    $session = Yii::$app->session;
    $user_company = $session['userCompany'];
    $model = new TblVendorPayment();
    $model->fk_location_id = $user_company;
    if ($this->request->isPost) {
      if ($model->load($this->request->post())) {
        //---create the driver invoice receivable entries as well --------
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
          return $this->render('create', [
            'model' => $model,
          ]);
        }else{
          if($model->save()){
            for($i=0;$i<count($invoice_id_arr);$i++){
              if($pending_amount_arr[$i] != "" && $pending_amount_arr[$i] != 0 && $pending_amount_arr[$i] != 0.00){
                // if($pending_amount_arr[$i] != "" && $pending_amount_arr[$i] != 0 && $pending_amount_arr[$i] != 0.00 && $pending_amount_arr[$i] >= 1){
                $modelAr = new \app\models\TblVendorAccountPayable();
                $modelAr->fk_vendor_payment_id = $model->id;
                $modelAr->fk_vendor_invoice_id = $invoice_id_arr[$i];
                $modelAr->amount_received = $pending_amount_arr[$i];
                $modelAr->ar_date = $model->ar_date;
                $modelAr->fk_payment_method_id = $model->fk_payment_method_id;
                $modelAr->notes = $model->notes;
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
            $target_dir = "../web/vendor-payment-files/";
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
                      $modelBefore = new TblVendorPaymentFile();
                      $modelBefore->fk_vendor_payment_id = $model->id;
                      $modelBefore->file_url = $file_name;
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
  * Updates an existing TblVendorPayment model.
  * If update is successful, the browser will be redirected to the 'view' page.
  * @param int $id ID
  * @return string|\yii\web\Response
  * @throws NotFoundHttpException if the model cannot be found
  */
  public function actionUpdate($id)
  {
    $model = $this->findModel($id);
    if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
      return $this->redirect(['view', 'id' => $model->id]);
    }
    return $this->render('update', [
      'model' => $model,
    ]);
  }

  /**
  * Deletes an existing TblVendorPayment model.
  * If deletion is successful, the browser will be redirected to the 'index' page.
  * @param int $id ID
  * @return \yii\web\Response
  * @throws NotFoundHttpException if the model cannot be found
  */
  public function actionDelete($id)
  {
    $model = TblVendorPayment::find()->where(['id'=>$id])->one();
    $model->status = 0;
    if($model->save()){
      \app\models\TblVendorAccountPayable::updateAll(array('status' => 0),'fk_vendor_payment_id='.$model->id);
      TblVendorPaymentFile::updateAll(array('status' => 0),'fk_vendor_payment_id='.$model->id);
      Yii::$app->getSession()->setFlash('success', 'Record deleted successfully');
    }else{
      Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
    }
    return $this->redirect(['index']);
  }

  /**
  * Finds the TblVendorPayment model based on its primary key value.
  * If the model is not found, a 404 HTTP exception will be thrown.
  * @param int $id ID
  * @return TblVendorPayment the loaded model
  * @throws NotFoundHttpException if the model cannot be found
  */
  protected function findModel($id)
  {
    if (($model = TblVendorPayment::findOne(['id' => $id])) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('The requested page does not exist.');
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
      $modelReceipt = TblVendorPayment::find()->where(['id'=>$id])->one();
      $modelReceipt->fk_payment_method_id = $pay_method_id;
      $modelReceipt->notes = $notes;
      if($modelReceipt->save()){
        \app\models\TblVendorAccountPayable::updateAll(array('fk_payment_method_id' => $pay_method_id,'notes'=>$notes),'fk_vendor_payment_id='.$id);
        $deletedIds = Yii::$app->request->post('deleted_image_ids', []);

        if (!empty($deletedIds)) {

          // If it comes as string → convert to array
          if (is_string($deletedIds)) {
            $deletedIds = explode(',', $deletedIds);
          }

          // Ensure array + clean integers
          $ids = array_filter(array_map('intval', (array)$deletedIds));

          if (!empty($ids)) {
            \app\models\TblVendorPaymentFile::updateAll(
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
        $target_dir = "../web/vendor-payment-files/";
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
                  $modelBefore = new TblVendorPaymentFile();
                  $modelBefore->fk_vendor_payment_id = $modelReceipt->id;
                  $modelBefore->file_url = $file_name;
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
  public function actionGetPendingInvoice(){
    $vendor_id = $_POST['vendor_id'];
    // Step 1: Fetch all invoices with related data in a single query
    $subQueryReceivables = (new Query())
    ->select(['fk_vendor_invoice_id', new Expression('SUM(amount_received) AS total_amount_received')])
    ->from('tbl_vendor_account_payable')
    ->where(['status' => 1])
    ->groupBy('fk_vendor_invoice_id');
    // Step 1: Fetch all invoices with related data in a single query
    $invoicesQuery = \app\models\TblVendorInvoice::find()
    ->alias('i')
    ->select([
      'i.id',
      'i.vendor_invoice_number',
      'i.purchase_invoice_no',
      'i.total_amount',
      'i.fk_vendor_id',
      'i.invoice_date',
      'ar.total_amount_received',
      'c.company_name AS client_name'
    ])
    ->leftJoin(['ar' => $subQueryReceivables], 'ar.fk_vendor_invoice_id = i.id')
    ->leftJoin(['c' => 'tbl_vendor'], 'c.id = i.fk_vendor_id')
    ->where(['!=', 'i.status', 0])
    ->andWhere(['i.fk_vendor_id' => $vendor_id])
    ->andWhere(['>', 'i.total_amount', 0])
    ->orderBy(['i.invoice_date' => SORT_ASC])
    ->asArray()
    ->all();
    // print_r($invoicesQuery);
    $return_str = '<div class="table-responsive">
    <table class="table table-bordered table-striped" id="invoices_table">
    <thead>
    <tr class="table-dark">
    <th> <input type="checkbox" id="check_all_invoices" /> All</th>
    <th>Sr. No.</th>
    <th>Vendor Invoice #</th>
    <th>Purchase Invoice #</th>
    <th>Invoice Date</th>
    <th>Total Amount</th>
    <th>Amount Paid</th>
    <th>Amount Pending</th>
    <th>Settle</th>
    </tr>
    </thead>
    <tbody>';
    $i = 1;
    foreach($invoicesQuery as $invoice) {
      $totalAmountReceived = $invoice['total_amount_received'] ?? 0.00;
      $amount_received = $totalAmountReceived;
      $tobe_paid_amount = $invoice['total_amount'];
      // $totalAmountPending = $invoice['total_amount']-$totalAmountReceived-$total_amount_credit;
      $totalAmountPending = bcsub($tobe_paid_amount, $amount_received, 2); // 10 decimal places
      $totalAmountReceived = number_format($totalAmountReceived,2,'.','');
      if($totalAmountPending > 0){
        $return_str .= '<tr>
        <td>
        <input type="checkbox" class="check_invoice" data-id="'.$i.'"/>
        </td>
        <td>'.$i.'</td>
        <td>'.$invoice['vendor_invoice_number'].' | <a href="'.Url::to(['vendor-invoice/view','id'=>$invoice['id']]).'" target="_blank"><i class="fa-solid fa-up-right-from-square"></i></a></td>
        <td>'.$invoice['purchase_invoice_no'].'</td>
        <td>'.$invoice['invoice_date'].'</td>
        <td>'.$tobe_paid_amount.'</td>
        <td>'.$totalAmountReceived.'</td>
        <td>'.number_format($totalAmountPending, 2, '.', '').'</td>
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
  }
  public function actionSendRemittance($id)
  {
    $payment = TblVendorPayment::findOne($id);

    if (!$payment) {
      throw new \yii\web\NotFoundHttpException('Vendor payment not found.');
    }

    // ---------- Vendor ----------
    $vendor = TblVendor::findOne($payment->fk_vendor_id);
    if (!$vendor || empty($vendor->email)) {
      Yii::$app->session->setFlash('error', 'Vendor email not found.');
      return $this->redirect(['view', 'id' => $payment->id]);
    }

    // ---------- Payment Method ----------
    $paymentMethod = TblPreferredPaymentMethod::findOne($payment->fk_payment_method_id);
    $paymentMethodName = $paymentMethod ? $paymentMethod->title : 'N/A';

    // ---------- Paid Invoices ----------
    $items = TblVendorAccountPayable::find()
    ->where([
      'fk_vendor_payment_id' => $payment->id,
      'status' => 1
    ])
    ->all();

    if (empty($items)) {
      Yii::$app->session->setFlash('error', 'No invoice data found for this payment.');
      return $this->redirect(['view', 'id' => $payment->id]);
    }


    // ---------- Total ----------
    $totalAmount = array_sum(array_column($items, 'amount_received'));
    $getcompany = \app\models\TblOwnershipCompany::find()->where(['id'=>$payment->fk_location_id])->one();
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
    $logoPath = Yii::getAlias('@webroot') . '/ownercompany-logo/' . $bill_logo;
    // ---------- EMAIL BODY ----------
    $emailBody = "
    <p>Greetings {$vendor->company_name},</p>

    <p>
    We are pleased to inform you that a payment has been processed against your account.
    Please find the remittance advice attached for your reference.
    </p>

    <table cellpadding='6'>
    <tr>
    <td><strong>Payment Reference:</strong></td>
    <td>{$payment->id}</td>
    </tr>
    <tr>
    <td><strong>Payment Date:</strong></td>
    <td>" . $payment->ar_date . "</td>
    </tr>
    <tr>
    <td><strong>Payment Method:</strong></td>
    <td>{$paymentMethodName}</td>
    </tr>
    <tr>
    <td><strong>Total Paid:</strong></td>
    <td><strong>$" . number_format($payment->amount_received, 2,'.',',') . "</strong></td>
    </tr>
    </table>

    <p>
    If you have any questions regarding this payment, please feel free to contact our accounts payable team.
    </p>

    <p>Regards,<br><strong>" . $bill_from_cname . "</strong></p>
   <img src='{$logoPath}' width='100px' /><br />
    <p>".$bill_from_address."<br />".$bill_from_city." ".$bill_from_province." ".$bill_from_postal_code."
    </p>
    ";

    // ---------- PDF GENERATION ----------
    $pdf = new Pdf([
      'mode' => Pdf::MODE_CORE,
      'format' => Pdf::FORMAT_A4,
      'destination' => Pdf::DEST_STRING,
      // 'destination' => Pdf::DEST_BROWSER,
      'content' => $this->renderPartial('remittance-pdf', [
        'payment' => $payment,
        'vendor' => $vendor,
        'items' => $items,
        'paymentMethod' => $paymentMethodName,
        'totalAmount' => $payment->amount_received,
      ]),
      'cssInline' => 'body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 11px;
        color: #333;
      }
      .header {
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
        margin-bottom: 15px;
      }
      .section-title {
        font-size: 12px;
        font-weight: bold;
        margin-bottom: 5px;
        text-transform: uppercase;
      }
      .table {
        width: 100%;
        border-collapse: collapse;
      }
      .table th {
        background: #f2f2f2;
        border: 1px solid #ccc;
        padding: 6px;
        font-weight: bold;
      }
      .table td {
        border: 1px solid #ccc;
        padding: 6px;
      }
      .right {
        text-align: right;
      }',
      'options' => [
        'title' => 'Remittance Advice',
      ],
      'methods' => [
        'SetHeader' => ['Remittance Statement ||Generated On: ' . date('Y-m-d')],
        'SetFooter' => ['|Page {PAGENO}|'],
      ]
    ]);
    // return $pdf->render();

    $pdfContent = $pdf->render();

    $from_email = "hktrailerpartsit@gmail.com";
    $from_name = "HK Trailer Parts";

    $attachmentBase64 = base64_encode($pdfContent);
    $emailPayload = [
    'sender' => [
        'name' => $bill_from_cname,
        'email' => $bill_email,
    ],
    'bcc' => [ // Add multiple CC recipients here
      [
          'name' => $bill_from_cname,
          'email' => $bill_email
      ]
    ],
    'to' => [],
    'subject' => 'Payment Remittance Advice – Payment #' . $payment->id.' | '.$bill_from_cname.' | '.date('d-M-Y h:ia'),
    'htmlContent' => $emailBody,
    'attachment' => [
        [
            'content' => $attachmentBase64,
            'name' => 'Remittance_Payment_' . $payment->id . '.pdf',
            'type' => 'application/pdf',
        ]
    ],
];
$emails = array_map('trim', explode(',', $vendor->email));
foreach ($emails as $email) {
  // Validate email
  if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $emailPayload['to'][] = [
      "email" => $email,
      "name" => $vendor->company_name
    ];
  }
}
//----make it live on production ----
// "cc" => [ // Add multiple CC recipients here
//   [
//       "email" => $bill_email,
//       "name" => $bill_from_cname
//   ]
// ],

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => 'https://api.brevo.com/v3/smtp/email',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'accept: application/json',
        'api-key: ' . Yii::$app->params['brevoApiKey'],
        'content-type: application/json',
    ],
    CURLOPT_POSTFIELDS => json_encode($emailPayload),
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

curl_close($ch);
if ($curlError) {
    Yii::error($curlError, 'brevo-email');
    Yii::$app->session->setFlash('error', 'Email sending failed.');
    return $this->redirect(['view', 'id' => $payment->id]);
}

$responseData = json_decode($response, true);

if ($httpCode >= 200 && $httpCode < 300) {
    $modelLog = new \app\models\TblVendorPaymentLog();
    $modelLog->fk_vendor_payment_id = $payment->id;
    $modelLog->message_id = $responseData['messageId'];
    $modelLog->save();

    Yii::$app->session->setFlash('success', 'Remittance email sent successfully.');

} else {
    Yii::error($responseData, 'brevo-email');
    Yii::$app->session->setFlash(
        'error',
        'Failed to send email: ' . ($responseData['message'] ?? 'Unknown error')
    );
}

    return $this->redirect(['view', 'id' => $payment->id]);
  }


}
