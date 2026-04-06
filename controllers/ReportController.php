<?php

namespace app\controllers;
use Yii;
use app\models\TblBin;
use app\models\TblBinSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\TblReportFavorite;
use app\models\TblReport;
use yii\helpers\Url;
use app\models\TblMenuAccess;
use yii\db\Query;
/**
* BinController implements the CRUD actions for TblBin model.
*/
class ReportController extends ReportBaseController
{
  /**
  * @inheritDoc
  */
  // public function behaviors()
  // {
  //     return array_merge(
  //         parent::behaviors(),
  //         [
  //             'verbs' => [
  //                 'class' => VerbFilter::className(),
  //                 'actions' => [
  //                     'delete' => ['POST'],
  //                 ],
  //             ],
  //         ]
  //     );
  // }
  public function actions()
  {
    return [
      'error' => [
        'class' => 'yii\web\ErrorAction',
      ],
    ];
  }
  public function beforeAction($action) {
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    $fk_user_id = $session['userId'];
    $menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>4,'status'=>1])->one();
    if($this->action->id == "index"){
      //check if user has permission to access the page
      if(isset($menuaccess) && $menuaccess->view_crud == 1){

      }else{
        return $this->redirect(['site/index']);
      }
    }
    $this->enableCsrfValidation = false;
    return parent::beforeAction($action);
  }
  /**
  * Lists all TblBin models.
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
    return $this->render('index');
  }

  public function actionIndexcopy()
  {
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      // Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    return $this->render('indexcopy');
  }
  public function actionSetfav(){
    $session = Yii::$app->session;
    $id = $_POST['id'];
    $fav = $_POST['fav'];
    // $model = TblReport::find()->where(['id'=>$id])->one();
    if($fav == 1){
      //add favorite
      $model = new TblReportFavorite();
      $model->fk_report_id = $id;
      $model->fk_user_id = $session->get('userId');
      $model->save();
    }else{
      //remove favorite
      $model = TblReportFavorite::find()->where(['fk_report_id'=>$id])->andWhere(['fk_user_id'=>$session->get('userId')])->one();
      $model->status = 0;
      $model->save();
    }

    $str = "";
    $favs = TblReport::find()->where(['status'=>1])->andWhere('id in (select fk_report_id from tbl_report_favorite where status = 1 and fk_user_id = '.$session->get('userId').')')->all();
    foreach($favs as $f){
      $str .= '<div class="row g-0 align-items-center border-bottom py-2 px-3" id="fav_'.$f->id.'">
      <div class="col-md mt-1 mt-md-0">
      <a class="unformat" href="'.Url::to([$f->url]).'">'.$f->title.'</a> |
      <a class="unformat text-sm" href="'.Url::to([$f->url]).'" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
      <p>'.$f->description.'</p>
      </div>
      <div class="col-md-auto">
      <p class="mb-0">';
      $str .= '<i class="fas fa-star" id="fav_star_'.$f->id.'">&nbsp;&nbsp;&nbsp;</i>';
      $str .= '</p>
      </div>
      </div>';
    }
    return $str;
  }


  public function actionInventoryCountReport(){
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      // Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    if(Yii::$app->request->post('item_category') != ""){
      $item_category = Yii::$app->request->post('item_category');
      return $this->render('inventory-count-report',[
        'item_category' => $item_category
      ]);
    }else{
      return $this->render('inventory-count-report');
      // return $this->render('inspection-overdue-trucks');
    }
  }


  public function actionLowInventoryCountReport(){
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      // Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    if(Yii::$app->request->post('item_category') != ""){
      $item_category = Yii::$app->request->post('item_category');
      return $this->render('low-inventory-count-report',[
        'item_category' => $item_category
      ]);
    }else{
      return $this->render('low-inventory-count-report');
      // return $this->render('inspection-overdue-trucks');
    }
  }


  public function actionCriticalLowInventoryCountReport(){
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      // Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    if(Yii::$app->request->post('item_category') != ""){
      $item_category = Yii::$app->request->post('item_category');
      return $this->render('critical-low-inventory-count-report',[
        'item_category' => $item_category
      ]);
    }else{
      return $this->render('critical-low-inventory-count-report');
      // return $this->render('inspection-overdue-trucks');
    }
  }
  public function actionCustomerAccountStatements(){
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    if(Yii::$app->request->post('from_date') != "" && Yii::$app->request->post('to_date') != "" && Yii::$app->request->post('customer') != ""){
      $from_date = Yii::$app->request->post('from_date');
      $to_date = Yii::$app->request->post('to_date');
      $customer = Yii::$app->request->post('customer');
      // $bill_company = Yii::$app->request->post('bill_company');
      return $this->render('customer-account-statements', [
        'from_date' => $from_date,
        'to_date' => $to_date,
        'customer' => $customer
      ]);
    }else{
      return $this->render('customer-account-statements');
    }

  }
  public function actionCustomerList(){
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }

    if(Yii::$app->request->post('from_date') != "" && Yii::$app->request->post('to_date') != "" && Yii::$app->request->post('terms') != "" && Yii::$app->request->post('crt_by') != ""){
      $from_date = Yii::$app->request->post('from_date');
      $to_date = Yii::$app->request->post('to_date');
      $terms = Yii::$app->request->post('terms');
      $crt_by = Yii::$app->request->post('crt_by');

      return $this->render('customer-list', [
        'from_date' => $from_date,
        'to_date' => $to_date,
        'terms' => $terms,
        'crt_by' => $crt_by
      ]);
    }else{
      return $this->render('customer-list');
    }
  }
  public function actionCustomerAgingReport(){
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    if(Yii::$app->request->post('from_date') != "" && Yii::$app->request->post('to_date') != "" && Yii::$app->request->post('customer') != ""){
      $from_date = Yii::$app->request->post('from_date');
      $to_date = Yii::$app->request->post('to_date');
      $customer = Yii::$app->request->post('customer');
      // $bill_company = Yii::$app->request->post('bill_company');
      return $this->render('customer-aging-report', [
        'from_date' => $from_date,
        'to_date' => $to_date,
        'customer' => $customer
      ]);
    }else{
      return $this->render('customer-aging-report');
    }
  }
  public function actionAttendance(){
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }

    if(Yii::$app->request->post('startdate') != "" && Yii::$app->request->post('enddate') != "" && Yii::$app->request->post('employee') != ""){
      $startdate = Yii::$app->request->post('startdate');
      $enddate = Yii::$app->request->post('enddate');
      $employee = Yii::$app->request->post('employee');

      return $this->render('attendance', [
        'startdate' => $startdate,
        'enddate' => $enddate,
        'employee' => $employee
      ]);
    }else{
      return $this->render('attendance');
    }

  }
  public function actionInvoiceSummary(){
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    if(Yii::$app->request->post('from_date') != "" && Yii::$app->request->post('to_date') != "" && Yii::$app->request->post('customer') != "" && Yii::$app->request->post('crt_by') != ""){
      $from_date = Yii::$app->request->post('from_date');
      $to_date = Yii::$app->request->post('to_date');
      $customer = Yii::$app->request->post('customer');
      $crt_by = Yii::$app->request->post('crt_by');

      return $this->render('invoice-summary', [
        'from_date' => $from_date,
        'to_date' => $to_date,
        'customer' => $customer,
        'crt_by' => $crt_by
      ]);
    }else{
      return $this->render('invoice-summary');
    }
  }
  public function actionArAgingReport(){
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    if(Yii::$app->request->post('from_date') != "" && Yii::$app->request->post('to_date') != "" && Yii::$app->request->post('customer') != ""){
      $from_date = Yii::$app->request->post('from_date');
      $to_date = Yii::$app->request->post('to_date');
      $customer = Yii::$app->request->post('customer');

      return $this->render('ar-aging-report', [
        'from_date' => $from_date,
        'to_date' => $to_date,
        'customer' => $customer
      ]);
    }else{
      return $this->render('ar-aging-report');
    }

  }
  public function actionArAgingReportDetails(){
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    return $this->render('ar-aging-report-details');
  }
  public function actionDetailedSalesInvoice(){
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    if(Yii::$app->request->post('from_date') != "" && Yii::$app->request->post('to_date') != "" && Yii::$app->request->post('customer') != "" && Yii::$app->request->post('crt_by') != ""){
      $from_date = Yii::$app->request->post('from_date');
      $to_date = Yii::$app->request->post('to_date');
      $customer = Yii::$app->request->post('customer');
      $crt_by = Yii::$app->request->post('crt_by');

      return $this->render('detailed-sales-invoice', [
        'from_date' => $from_date,
        'to_date' => $to_date,
        'customer' => $customer,
        'crt_by' => $crt_by
      ]);
    }else{
      return $this->render('detailed-sales-invoice');
    }
  }
  public function actionUnpaidInvoice(){
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    if(Yii::$app->request->post('from_date') != "" && Yii::$app->request->post('to_date') != "" && Yii::$app->request->post('customer') != "" && Yii::$app->request->post('crt_by') != ""){
      $from_date = Yii::$app->request->post('from_date');
      $to_date = Yii::$app->request->post('to_date');
      $customer = Yii::$app->request->post('customer');
      $crt_by = Yii::$app->request->post('crt_by');

      return $this->render('unpaid-invoice', [
        'from_date' => $from_date,
        'to_date' => $to_date,
        'customer' => $customer,
        'crt_by' => $crt_by
      ]);
    }else{
      return $this->render('unpaid-invoice');
    }
  }
  public function actionVendorList(){
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }

    if(Yii::$app->request->post('from_date') != "" && Yii::$app->request->post('to_date') != "" && Yii::$app->request->post('terms') != "" && Yii::$app->request->post('crt_by') != ""){
      $from_date = Yii::$app->request->post('from_date');
      $to_date = Yii::$app->request->post('to_date');
      $terms = Yii::$app->request->post('terms');
      $crt_by = Yii::$app->request->post('crt_by');

      return $this->render('vendor-list', [
        'from_date' => $from_date,
        'to_date' => $to_date,
        'terms' => $terms,
        'crt_by' => $crt_by
      ]);
    }else{
      return $this->render('vendor-list');
    }
  }
  public function actionVendorPaymentHistory(){
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    if(Yii::$app->request->post('from_date') != "" && Yii::$app->request->post('to_date') != "" && Yii::$app->request->post('vendor') != ""){
      $from_date = Yii::$app->request->post('from_date');
      $to_date = Yii::$app->request->post('to_date');
      $vendor = Yii::$app->request->post('vendor');

      return $this->render('vendor-payment-history', [
        'from_date' => $from_date,
        'to_date' => $to_date,
        'vendor' => $vendor
      ]);
    }else{
      return $this->render('vendor-payment-history');
    }
  }
  public function actionVendorAgingReport(){
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    if(Yii::$app->request->post('from_date') != "" && Yii::$app->request->post('to_date') != "" && Yii::$app->request->post('customer') != ""){
      $from_date = Yii::$app->request->post('from_date');
      $to_date = Yii::$app->request->post('to_date');
      $customer = Yii::$app->request->post('customer');
      // $bill_company = Yii::$app->request->post('bill_company');
      return $this->render('vendor-aging-report', [
        'from_date' => $from_date,
        'to_date' => $to_date,
        'customer' => $customer
      ]);
    }else{
      return $this->render('vendor-aging-report');
    }
  }
  public function actionVendorAccountStatements(){
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    if(Yii::$app->request->post('from_date') != "" && Yii::$app->request->post('to_date') != "" && Yii::$app->request->post('customer') != ""){
      $from_date = Yii::$app->request->post('from_date');
      $to_date = Yii::$app->request->post('to_date');
      $customer = Yii::$app->request->post('customer');
      // $bill_company = Yii::$app->request->post('bill_company');
      return $this->render('vendor-account-statements', [
        'from_date' => $from_date,
        'to_date' => $to_date,
        'customer' => $customer
      ]);
    }else{
      return $this->render('vendor-account-statements');
    }

  }
  public function actionPurchaseInvoiceSummary(){
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    if(Yii::$app->request->post('from_date') != ""
    && Yii::$app->request->post('to_date') != ""
    && Yii::$app->request->post('vendor') != ""
    && Yii::$app->request->post('crt_by') != ""){
      $from_date = Yii::$app->request->post('from_date');
      $to_date = Yii::$app->request->post('to_date');
      $vendor = Yii::$app->request->post('vendor');
      $crt_by = Yii::$app->request->post('crt_by');

      return $this->render('purchase-invoice-summary', [
        'from_date' => $from_date,
        'to_date' => $to_date,
        'vendor' => $vendor,
        'crt_by' => $crt_by
      ]);
    }else{
      return $this->render('purchase-invoice-summary');
    }
  }
  public function actionApAgingReport(){
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    if(Yii::$app->request->post('from_date') != "" && Yii::$app->request->post('to_date') != "" && Yii::$app->request->post('vendor') != ""){
      $from_date = Yii::$app->request->post('from_date');
      $to_date = Yii::$app->request->post('to_date');
      $vendor = Yii::$app->request->post('vendor');

      return $this->render('ap-aging-report', [
        'from_date' => $from_date,
        'to_date' => $to_date,
        'vendor' => $vendor
      ]);
    }else{
      return $this->render('ap-aging-report');
    }
  }
  public function actionApAgingReportDetails(){
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    return $this->render('ap-aging-report-details');
  }
  public function actionDetailedPurchaseInvoice(){
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    if(Yii::$app->request->post('from_date') != "" && Yii::$app->request->post('to_date') != "" && Yii::$app->request->post('vendor') != ""){
      $from_date = Yii::$app->request->post('from_date');
      $to_date = Yii::$app->request->post('to_date');
      $vendor = Yii::$app->request->post('vendor');
      // $crt_by = Yii::$app->request->post('crt_by');

      return $this->render('detailed-purchase-invoice', [
        'from_date' => $from_date,
        'to_date' => $to_date,
        'vendor' => $vendor
      ]);
    }else{
      return $this->render('detailed-purchase-invoice');
    }
  }
  public function actionApPaymentSchedule(){
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    if(Yii::$app->request->post('from_date') != ""
    && Yii::$app->request->post('to_date') != ""
    && Yii::$app->request->post('vendor') != ""){
      $from_date = Yii::$app->request->post('from_date');
      $to_date = Yii::$app->request->post('to_date');
      $vendor = Yii::$app->request->post('vendor');

      return $this->render('ap-payment-schedule', [
        'from_date' => $from_date,
        'to_date' => $to_date,
        'vendor' => $vendor
      ]);
    }else{
      return $this->render('ap-payment-schedule');
    }
  }
  public function actionUnpaidVendorInvoice(){
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    if(Yii::$app->request->post('from_date') != "" && Yii::$app->request->post('to_date') != "" && Yii::$app->request->post('vendor') != "" && Yii::$app->request->post('crt_by') != ""){
        $from_date = Yii::$app->request->post('from_date');
        $to_date = Yii::$app->request->post('to_date');
        $vendor = Yii::$app->request->post('vendor');
        $crt_by = Yii::$app->request->post('crt_by');

        return $this->render('unpaid-vendor-invoice', [
            'from_date' => $from_date,
            'to_date' => $to_date,
            'vendor' => $vendor,
            'crt_by' => $crt_by
        ]);
    }else{
        return $this->render('unpaid-vendor-invoice');
    }
}
public function actionProfitAndLoss($from = null, $to = null)
    {
      $session = Yii::$app->session;
      $user_company = $session['userCompany'];
        if(Yii::$app->request->post('from_date') != "" && Yii::$app->request->post('to_date') != ""){
          $from = Yii::$app->request->post('from_date'); // first day of current month
          $to = Yii::$app->request->post('to_date');      // last day of current month
        }else{
          $from = $from ?: date('Y-m-01'); // first day of current month
          $to = $to ?: date('Y-m-t');      // last day of current month
        }
        $connection = Yii::$app->db;

        // Total Sales
        $totalSales = (new Query())
            ->from('tbl_invoice')
            ->where(['between', 'invoice_date', $from, $to])
            ->andWhere(['status'=>1])
            ->andWhere(['fk_bill_from_id' => $user_company])
            ->sum('total_amount') ?? 0;

        // Total Customer Payments
        $totalReceived = (new Query())
            ->from('tbl_account_receivable ir')
            ->leftJoin('tbl_invoice i', 'i.id = ir.fk_invoice_id and i.fk_bill_from_id = '.$user_company)
            ->where(['between', 'i.invoice_date', $from, $to])
            ->andWhere(['i.status'=>1])
            ->sum('ir.amount_received') ?? 0;

        // Total Vendor Purchases
        $totalPurchases = (new Query())
            ->from('tbl_vendor_invoice')
            ->where(['between', 'invoice_date', $from, $to])
            ->andWhere(['status'=>1])
            ->andWhere(['fk_location_id'=>$user_company])
            ->sum('total_amount') ?? 0;

        // Total Vendor Payments
        $totalPaid = (new Query())
            ->from('tbl_vendor_account_payable vp')
            ->leftJoin('tbl_vendor_invoice vi', 'vi.id = vp.fk_vendor_invoice_id and vi.fk_location_id = '.$user_company)
            ->where(['between', 'vi.invoice_date', $from, $to])
            ->andWhere(['vi.status'=>1])
            ->sum('vp.amount_received') ?? 0;

        // Total COGS based on product receiving
        $totalCOGS = (new Query())
            ->from('tbl_product_receiving pr')
            ->leftJoin('tbl_vendor_invoice vi', 'vi.id = pr.fk_vendor_invoice_id and vi.fk_location_id = '.$user_company)
            ->where(['between', 'vi.invoice_date', $from, $to])
            ->andWhere(['pr.status'=>1])
            ->andWhere(['pr.fk_location_id'=>$user_company])
            ->sum(new \yii\db\Expression('pr.no_of_items * pr.price_per_item')) ?? 0;

        // Profit Calculation
        $grossProfit = $totalSales - $totalCOGS;
        $netProfit = $grossProfit; // You can subtract other expenses later if needed

        // Pass data to view
        return $this->render('profit-and-loss', [
            'from' => $from,
            'to' => $to,
            'totalSales' => $totalSales,
            'totalReceived' => $totalReceived,
            'totalPurchases' => $totalPurchases,
            'totalPaid' => $totalPaid,
            'totalCOGS' => $totalCOGS,
            'grossProfit' => $grossProfit,
            'netProfit' => $netProfit,
        ]);
    }
    public function actionExpenses(){
        $session = Yii::$app->session;
        if(!isset($session['username'])){
          Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
          return $this->redirect(['site/login']);
        }

        if(Yii::$app->request->post('from_date') != "" && Yii::$app->request->post('to_date') != "" && Yii::$app->request->post('category_selected') != "" && Yii::$app->request->post('vendor_selected') != ""){
            $from_date = Yii::$app->request->post('from_date');
            $to_date = Yii::$app->request->post('to_date');
            $category_selected = Yii::$app->request->post('category_selected');
            $vendor_selected = Yii::$app->request->post('vendor_selected');
            return $this->render('expenses', [
                'from_date' => $from_date,
                'to_date' => $to_date,
                'category_selected' => $category_selected,
                'vendor_selected' => $vendor_selected
            ]);
        }else{
            return $this->render('expenses');
        }
    }

}
