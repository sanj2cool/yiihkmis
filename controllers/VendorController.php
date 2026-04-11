<?php

namespace app\controllers;
use Yii;
use app\models\TblVendor;
use app\models\TblVendorSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\TblMenuAccess;
use kartik\mpdf\Pdf;
use yii\helpers\FileHelper;
use yii\web\Response;

/**
 * VendorController implements the CRUD actions for TblVendor model.
 */
class VendorController extends Controller
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
        $menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>9,'status'=>1])->one();
        if($this->action->id == "index"){
          //check if user has permission to access the page
          if(isset($menuaccess) && $menuaccess->view_crud == 1){

          }else{
            return $this->redirect(['site/index']);
          }
        }else if($this->action->id == "create"){
          $menuaccess_c = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>10,'status'=>1])->one();
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
        }
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
      }
    /**
     * Lists all TblVendor models.
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
        $searchModel = new TblVendorSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionResetFilters()
    {
        $searchModel = new TblVendorSearch();
        $searchModel->clearSearchState(); // Clear the session data
        return $this->redirect(['index']); // Redirect back to the index page
    }
    /**
     * Displays a single TblVendor model.
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
     * Creates a new TblVendor model.
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
        $model = new TblVendor();

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
     * Updates an existing TblVendor model.
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
     * Deletes an existing TblVendor model.
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
        $model = TblVendor::find()->where(['id'=>$id])->one();
        $model->status = 0;
        if($model->save()){
          Yii::$app->getSession()->setFlash('success', 'Record deleted successfully');
        }else{
          Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblVendor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return TblVendor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblVendor::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionStatementForm($vendor_id)
  {
      return $this->renderAjax('_statement_form', [
          'vendor_id' => $vendor_id,
      ]);
  }
  public function actionGenerateStatement()
{
    $request = Yii::$app->request;

    $vendor_id    = $request->post('vendor_id');
    $from_date    = $request->post('from_date');
    $to_date      = $request->post('to_date');
    $user_company = Yii::$app->session->get('userCompany');

    if (!$vendor_id || !$from_date || !$to_date) {
        return '<div class="alert alert-danger">Invalid request.</div>';
    }

    $today = date('Y-m-d');

    /**
     * ----------------------------------------------------------
     * STEP 1: FETCH ONLY PENDING INVOICES (NET OF PAYMENTS)
     * ----------------------------------------------------------
     */
    $pendingInvoices = (new \yii\db\Query())
        ->select([
            'invoice_id'     => 'vi.id',
            'invoice_number' => 'vi.vendor_invoice_number',
            'purchase_invoice_no' => 'vi.purchase_invoice_no',
            'invoice_date'   => 'vi.invoice_date',
            'due_date'       => 'vi.due_date',
            'invoice_total'  => 'vi.total_amount',
            'paid_amount'    => 'IFNULL(SUM(vap.amount_received),0)',
            'pending_amount' => 'vi.total_amount - IFNULL(SUM(vap.amount_received),0)',
            'age_days'       => "DATEDIFF('{$today}', vi.due_date)",
            'location_id' => 'vi.fk_location_id'
        ])
        ->from('tbl_vendor_invoice vi')
        ->leftJoin(
            'tbl_vendor_account_payable vap',
            'vap.fk_vendor_invoice_id = vi.id AND vap.status = 1'
        )
        ->where([
            'vi.status' => 1,
            'vi.fk_vendor_id' => $vendor_id,
            'vi.fk_location_id' => $user_company
        ])
        ->andWhere(['between', 'vi.invoice_date', $from_date, $to_date])
        ->groupBy('vi.id')
        ->having('pending_amount > 0')
        ->orderBy(['vi.invoice_date' => SORT_ASC])
        ->all();

    /**
     * ----------------------------------------------------------
     * STEP 2: AGING BUCKET CALCULATION
     * ----------------------------------------------------------
     */
    $aging = [
        'current' => 0,
        '1_30'    => 0,
        '31_60'   => 0,
        '61_90'   => 0,
        '90_plus' => 0,
    ];

    $totalOutstanding = 0;

    foreach ($pendingInvoices as $inv) {
        $pending = (float) $inv['pending_amount'];
        $days    = (int) $inv['age_days'];

        $totalOutstanding += $pending;

        if ($days <= 0) {
            $aging['current'] += $pending;
        } elseif ($days <= 30) {
            $aging['1_30'] += $pending;
        } elseif ($days <= 60) {
            $aging['31_60'] += $pending;
        } elseif ($days <= 90) {
            $aging['61_90'] += $pending;
        } else {
            $aging['90_plus'] += $pending;
        }
    }

    /**
     * ----------------------------------------------------------
     * STEP 3: RENDER STATEMENT INSIDE MODAL
     * ----------------------------------------------------------
     */
     $vendor = TblVendor::findOne($vendor_id);
    return $this->renderAjax('_statement_result', [
        'pendingInvoices'  => $pendingInvoices,
        'aging'            => $aging,
        'totalOutstanding' => $totalOutstanding,
        'from_date'        => $from_date,
        'to_date'          => $to_date,
        'vendor'          => $vendor,
    ]);
}
public function actionEmailStatement()
{
    Yii::$app->response->format = Response::FORMAT_JSON;

    $request = Yii::$app->request;

    $vendor_id    = $request->post('vendor_id');
    $from_date    = $request->post('from_date');
    $to_date      = $request->post('to_date');
    $user_company = Yii::$app->session->get('userCompany');

    if (!$vendor_id || !$from_date || !$to_date) {
        return ['success' => false, 'message' => 'Invalid request'];
    }

    /* -------------------------------------------------
     * FETCH VENDOR
     * ------------------------------------------------- */
    $vendor = TblVendor::findOne($vendor_id);
    if (!$vendor || empty($vendor->email)) {
        return ['success' => false, 'message' => 'Vendor email not found'];
    }

    /* -------------------------------------------------
     * COMPANY INFO (reuse your existing vars)
     * ------------------------------------------------- */
     $getcompany = \app\models\TblOwnershipCompany::find()->where(['id'=>$vendor->fk_location_id])->one();
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
        'logo'    => Yii::getAlias('@webroot/ownercompany-logo/' . ($bill_logo ?? ''))
    ];

    /* -------------------------------------------------
     * STATEMENT DATA (PENDING ONLY)
     * ------------------------------------------------- */
    $today = date('Y-m-d');

    $pendingInvoices = (new \yii\db\Query())
        ->select([
            'invoice_number' => 'vi.vendor_invoice_number',
            'purchase_invoice_no' => 'vi.purchase_invoice_no',
            'invoice_date'   => 'vi.invoice_date',
            'due_date'       => 'vi.due_date',
            'pending_amount' => 'vi.total_amount - IFNULL(SUM(vap.amount_received),0)',
            'age_days'       => "DATEDIFF('{$today}', vi.due_date)"
        ])
        ->from('tbl_vendor_invoice vi')
        ->leftJoin(
            'tbl_vendor_account_payable vap',
            'vap.fk_vendor_invoice_id = vi.id AND vap.status = 1'
        )
        ->where([
            'vi.status' => 1,
            'vi.fk_vendor_id' => $vendor_id,
            'vi.fk_location_id' => $user_company
        ])
        ->andWhere(['between', 'vi.invoice_date', $from_date, $to_date])
        ->groupBy('vi.id')
        ->having('pending_amount > 0')
        ->all();

    if (empty($pendingInvoices)) {
        return ['success' => false, 'message' => 'No outstanding invoices to email'];
    }

    /* -------------------------------------------------
     * AGING + TOTAL
     * ------------------------------------------------- */
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

    /* -------------------------------------------------
     * RENDER HTML (SAME VIEW AS MODAL / PRINT)
     * ------------------------------------------------- */
    $html = $this->renderPartial('_statement_result', [
        'pendingInvoices'  => $pendingInvoices,
        'aging'            => $aging,
        'totalOutstanding' => $totalOutstanding,
        'from_date'        => $from_date,
        'to_date'          => $to_date,
        'vendor'           => $vendor,
        'company'          => $company,
        'mode'             => 'pdf' // 👈 THIS IS THE KEY
    ]);

    /* -------------------------------------------------
     * GENERATE PDF (kartik-v/mpdf)
     * ------------------------------------------------- */
    // $dir = Yii::getAlias('@web/vendor-statements');
    $dir = '/var/www/vhost/hkmis.ca/httpdocs/web/vendor-statements';
    // FileHelper::createDirectory($dir);

    $fileName = 'Vendor_Statement_' . $vendor_id . '_' . time() . '.pdf';
    $filePath = $dir . '/' . $fileName;

    $pdf = new Pdf([
        'mode'        => Pdf::MODE_UTF8,
        'format'      => Pdf::FORMAT_A4,
        'orientation' => Pdf::ORIENT_PORTRAIT,
        'destination' => Pdf::DEST_FILE,
        'filename'    => $filePath,
        'content'     => $html,
        'cssInline'   => 'body {font-family: Arial, sans-serif;font-size: 12px;color: #000;margin: 20px;}table {width: 100%;border-collapse: collapse;}table th, td {padding: 6px;vertical-align: top;}.statement-header table,.statement-vendor table {border: none;}.statement-header td,.statement-vendor td {border: none;padding: 2px 0;}.statement-invoices thead th {background-color: #e9ecef !important;border-bottom: 2px solid #000;font-weight: bold;}.statement-invoices tbody td {border-bottom: 1px solid #ccc;}.statement-invoices tfoot th,.statement-invoices tfoot td {background-color: #f8f9fa !important;border-top: 2px solid #000;font-weight: bold;}.statement-aging thead th {background-color: #f1f3f5 !important;border-bottom: 1px solid #000;font-weight: bold;}.statement-aging tbody td {border-top: 1px solid #ccc;}.text-end { text-align: right; }.fw-bold { font-weight: bold; }.text-danger { color: #000; }',
        'options'     => ['title' => 'Vendor Statement'],
    ]);
    $pdf->render();

    /* -------------------------------------------------
     * SEND EMAIL VIA BREVO
     * ------------------------------------------------- */
    $brevoResponse = $this->sendViaBrevo(
        $vendor->email,
        $vendor->company_name,
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

    if (!$brevoResponse['success']) {
        return ['success' => false, 'message' => 'Email sending failed'];
    }

    /* -------------------------------------------------
     * SAVE EMAIL LOG
     * ------------------------------------------------- */
       $session = Yii::$app -> session;

    Yii::$app->db->createCommand()->insert(
        'tbl_vendor_statement_log',
        [
            'fk_vendor_id' => $vendor_id,
            'message_id'   => $brevoResponse['message_id'],
            'from_date'    => $from_date,
            'to_date'      => $to_date,
            'file_name'    => $fileName,
            'crt_time'   => date('Y-m-d H:i:s'),
            'status'       => 1,
            'crt_by' => $session -> get('userId'),
            'ip' => Yii::$app -> getRequest() -> getUserIp()
        ]
    )->execute();

    return [
        'success' => true,
        'message' => 'Statement emailed successfully'
    ];
}
private function sendViaBrevo($toEmail, $toName, $pdfPath, $fromDate, $toDate,$bill_from_cname,$bill_email,$bill_logo,$bill_from_address,$bill_from_city,$bill_from_province,$bill_from_postal_code)
{
    $apiKey = Yii::$app->params['brevoApiKey'];
    //---NEED TO SET "TO" EMPTY AS IT WILL BE SET LATER DEPENDING UPON IF THE VENDOR HAS MULITPLE EMAILS IN THE EMAIL VARIABLE ----

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
        'subject' => "Vendor Statement ({$fromDate} to {$toDate}) | ".$bill_from_cname.' | '.date('d-M-Y h:ia'),
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

public function actionToggleUsa()
{
    $id = Yii::$app->request->post('id');
    $value = Yii::$app->request->post('value');

    $model = TblVendor::findOne($id);
    if ($model) {
        $model->is_usa = $value;
        $model->save(false);
    }

    return json_encode(['success' => true]);
}


}
