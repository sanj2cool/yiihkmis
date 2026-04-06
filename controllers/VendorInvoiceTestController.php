<?php

namespace app\controllers;
use Yii;
use app\models\TblVendorInvoice;
use app\models\TblVendorInvoiceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\TblVendor;
use app\models\TblTerms;
use app\models\TblProduct;
use app\models\TblClient;
use app\models\TblProductReceiving;
use app\models\TblVendorInvoiceItem;
use app\models\TblVendorAccountPayable;
use app\models\TblMenuAccess;
use yii\web\Response;
/**

 * VendorInvoiceController implements the CRUD actions for TblVendorInvoice model.
 */
class VendorInvoiceTestController extends BaseController
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
    // public function beforeAction($action) {
    //     $session = Yii::$app->session;
    //     if(!isset($session['username'])){
    //       Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
    //       return $this->redirect(['site/login']);
    //     }
    //     $fk_user_id = $session['userId'];
    //     $menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>12,'status'=>1])->one();
    //     if($this->action->id == "index"){
    //       //check if user has permission to access the page
    //       if(isset($menuaccess) && $menuaccess->view_crud == 1){
    //
    //       }else{
    //         return $this->redirect(['site/index']);
    //       }
    //     }else if($this->action->id == "create"){
    //       if(isset($menuaccess) && $menuaccess->create_crud == 1){
    //
    //       }else{
    //         return $this->redirect(['site/index']);
    //       }
    //     }else if($this->action->id == "update"){
    //
    //       if(isset($menuaccess) && $menuaccess->edit_crud == 1){
    //
    //       }else{
    //         return $this->redirect(['site/index']);
    //       }
    //     }else if($this->action->id == "delete"){
    //
    //       if(isset($menuaccess) && $menuaccess->delete_crud == 1){
    //
    //       }else{
    //         return $this->redirect(['site/index']);
    //       }
    //     }
    //     $this->enableCsrfValidation = false;
    //     return parent::beforeAction($action);
    //   }
    /**
     * Lists all TblVendorInvoice models.
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

        $searchModel = new TblVendorInvoiceSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionResetFilters()
    {
        $searchModel = new TblVendorInvoiceSearch();
        $searchModel->clearSearchState(); // Clear the session data
        return $this->redirect(['index']); // Redirect back to the index page
    }

    /**
     * Displays a single TblVendorInvoice model.
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
     * Creates a new TblVendorInvoice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */

     public function getLatestLocationProduct($product_id){
       // $product_id = 2117;
       $latest_location = "";
       $checklatest = \app\models\TblProductReceiving::find()
                         ->where(['status'=>1,'fk_product_id'=>$product_id])
                         ->orderBy(['id'=>SORT_DESC])->one();
       if(isset($checklatest) && $checklatest->id != ""){
         $latest_location = $checklatest->bin_location;
       }
       return $latest_location;
     }
    public function actionCreate()
    {
        $session = Yii::$app->session;
        if(!isset($session['username'])){
          Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
          return $this->redirect(['site/login']);
        }
        $model = new TblVendorInvoice();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
              //---------invoice items entries start -------------
                // $type = Yii::$app->request->post('type');
                // $title = Yii::$app->request->post('title');
                $product = Yii::$app->request->post('product');
                $description = Yii::$app->request->post('desc');
                $qty = Yii::$app->request->post('qty');
                $rate = Yii::$app->request->post('rate');
                $amount = Yii::$app->request->post('amount');
                // $bin_location = Yii::$app->request->post('bin_location');
                // $receiving_remarks = Yii::$app->request->post('receiving_/remarks');
                if(isset($product) && count($product) > 0){
                  for($i = 0; $i < count($product); $i++){
                    $modelItem[$i] = new TblVendorInvoiceItem();
                    $modelItem[$i]->fk_vendor_invoice_id = $model->id;
                    // $modelItem[$i]->type = $type[$i];
                    // $modelItem[$i]->item = $title[$i];
                    $modelItem[$i]->fk_product_id = $product[$i];
                    $modelItem[$i]->description = $description[$i];
                    $modelItem[$i]->quantity = $qty[$i];
                    $modelItem[$i]->unit_price = $rate[$i];
                    $modelItem[$i]->total_price = $amount[$i];
                    if($modelItem[$i]->save()){
                      if(isset($_POST['product_receiving_'.$i])){

                        // $bin_location = $this->getLatestLocationProduct($product[$i]) ?? '';
                        $bin_location = $_POST['bin_location_'.$i] ?? '';
                        $receiving_remarks = $_POST['receiving_remarks_'.$i] ?? '';
                        $modelProduct = new \app\models\TblProductReceiving();
                        $modelProduct->fk_product_id = $product[$i];
                        $modelProduct->fk_vendor_invoice_id = $model->id;
                        $modelProduct->fk_vendor_id = $model->fk_vendor_id;
                        $modelProduct->fk_vendor_invoice_item_id = $modelItem[$i]->id;
                        $modelProduct->no_of_items = $qty[$i];
                        $modelProduct->price_per_item = $rate[$i];
                        $modelProduct->bin_location = $bin_location;
                        $modelProduct->remarks = $receiving_remarks;
                        $modelProduct->fk_location_id = $model->fk_location_id;
                        if($modelProduct->save()){
                        }
                      }//----if for checking checkbox ended -----
                    }else{
                      print_r($modelItem[$i]->getErrors());
                    }
                  }//----for loop ended-------
                }//-----items exists-------
                //---------invoice items entries end -------------

                $msg_doc = "";
                $target_dir = "../web/vendor-invoices/";
                if(isset($_FILES["vendor_invoice"]) && $_FILES["vendor_invoice"]["name"] != ""){
                  $mc=md5(microtime());
                  $f_name = $mc.basename($_FILES["vendor_invoice"]["name"]);
                  $basename = basename($_FILES["vendor_invoice"]["name"]);
                  $file_name = $this->clean($f_name);
                  $target_file = $target_dir.$file_name;
                  $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                  if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                        && $imageFileType != "gif" && $imageFileType != "webp" && $imageFileType != "pdf") {
                      $msg_doc = "Sorry, only Image and PDF files are allowed.";
                  }else{
                    if(move_uploaded_file($_FILES["vendor_invoice"]["tmp_name"], $target_file)){
                      $modelPic = TblVendorInvoice::find()->where(['id'=>$model->id])->one();
                      $modelPic->invoice_file = $file_name;
                      $modelPic->save();
                    }
                  }//-----else for checking file type ended ----
                }
                if($msg_doc != ""){
                  Yii::$app->getSession()->setFlash('error', $msg_doc);
                  return $this->redirect(['update', 'id' => $model->id]);
                }else{
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


                // return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TblVendorInvoice model.
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
              //====WE CAN'T DELETE THE ENTRIES AND RE-ADD THEM AS Receiving IS CONNECTED NOW ---
              //---WE NEED TO UPDATE THE EXISTING ENTRIES AND CREATE NEW ONES WITH RECIEVING ----
              $item_id = Yii::$app->request->post('item_id');
              $product = Yii::$app->request->post('product');
              $description = Yii::$app->request->post('desc');
              $qty = Yii::$app->request->post('qty');
              $rate = Yii::$app->request->post('rate');
              $amount = Yii::$app->request->post('amount');
              $product_qty = Yii::$app->request->post('product_qty');
              $old_qty = Yii::$app->request->post('old_qty');
              // $bin_location = Yii::$app->request->post('bin_location');
              // $receiving_remarks = Yii::$app->request->post('receiving_remarks');

              //---------MAKE THAT ENTRY STATUS = 0 AS IT WOULD BE CONSIDERED DELETED --------
              if(isset($item_id) && count($item_id) > 0){
                //-------there are repair entries existing ------
                $getallitems = TblVendorInvoiceItem::find()->where(['fk_vendor_invoice_id'=>$model->id,'status'=>1,'type'=>1])->all();
                if(isset($getallitems) && count($getallitems) > 0){
                  foreach($getallitems as $ga){
                    if(!in_array($ga->id,$item_id)){
                      //----this entry has been deleted by the user -------
                      $modelDel = TblVendorInvoiceItem::find()->where(['id'=>$ga->id])->one();
                      $modelDel->status = 0;
                      $modelDel->save();
                      //---ALSO DELETE THE RELATED RECEIVING ENTRY ----
                      $getrec = TblProductReceiving::find()->where(['fk_vendor_invoice_item_id'=>$ga->id,'status'=>1])->one();
                      if(isset($getrec) && $getrec->id != ""){
                        $getrec->status = 0;
                        $getrec->save();
                      }
                    }//----if for checking entry in array ended --------------
                  }//-----for loop ended --------
                }//-----if isset ended --------
              }//--------if count ended ---------

              if(isset($product) && count($product) > 0){
                for($i = 0; $i < count($product); $i++){
                  if(isset($item_id[$i]) && $item_id[$i] != ""){
                    $modelItem[$i] = TblVendorInvoiceItem::find()->where(['id'=>$item_id[$i]])->one();
                    $modelItem[$i]->fk_vendor_invoice_id = $model->id;
                  }else{
                    $modelItem[$i] = new TblVendorInvoiceItem();
                    $modelItem[$i]->fk_vendor_invoice_id = $model->id;
                  }
                  $modelItem[$i]->fk_product_id = $product[$i];
                  $modelItem[$i]->description = $description[$i];
                  $modelItem[$i]->quantity = $qty[$i];
                  $modelItem[$i]->unit_price = $rate[$i];
                  $modelItem[$i]->total_price = $amount[$i];
                  if($modelItem[$i]->save()){
                    //----------CHECK IF CHECKBOX IS CHECKED THEN CREATE THE PRODUCT RECEIVING ENTRY AS WELL ------
                    if(isset($_POST['product_receiving_'.$i])){
                      // $bin_location = $this->getLatestLocationProduct($product[$i]) ?? '';
                      // $bin_location = $bin_location[$i] ?? '';
                      $bin_location = $_POST['bin_location_'.$i] ?? '';
                      $receiving_remarks = $_POST['receiving_remarks_'.$i] ?? '';

                      $modelProduct = new TblProductReceiving();
                      $modelProduct->fk_product_id = $product[$i];
                      $modelProduct->fk_vendor_invoice_id = $model->id;
                      $modelProduct->fk_vendor_id = $model->fk_vendor_id;
                      $modelProduct->fk_vendor_invoice_item_id = $modelItem[$i]->id;
                      $modelProduct->no_of_items = $qty[$i];
                      $modelProduct->price_per_item = $rate[$i];
                      $modelProduct->bin_location = $bin_location;
                      $modelProduct->fk_location_id = $model->fk_location_id;
                      $modelProduct->remarks = $receiving_remarks;
                      // $modelProduct->fk_bin_id = 0;
                      if($modelProduct->save()){
                      }
                    }//----if for checking checkbox ended -----

                    if(isset($item_id[$i]) && $item_id[$i] != ""){
                      //----THIS IS UPDATE ENTRY AND WE NEED TO MAKE SURE THE QTY AND RATE ARE SAME AT BOTH THE PLACES ----
                      $getreceiving = TblProductReceiving::find()->where(['fk_vendor_invoice_item_id'=>$item_id[$i],'status'=>1])->one();
                      if(isset($getreceiving) && $getreceiving->id != ""){
                        $getreceiving->fk_product_id = $product[$i];
                        $getreceiving->no_of_items = $qty[$i];
                        $getreceiving->price_per_item = $rate[$i];
                        $getreceiving->fk_vendor_id = $model->fk_vendor_id;
                        $getreceiving->save();
                      }
                    }
                  }else{
                    // print_r($modelItem[$i]->getErrors());
                  }
                }//----for loop ended-------
              }//-----items exists-------
                //---------invoice items entries end -------------

                $msg_doc = "";
                $target_dir = "../web/vendor-invoices/";
                if(isset($_FILES["vendor_invoice"]) && $_FILES["vendor_invoice"]["name"] != ""){
                  $mc=md5(microtime());
                  $f_name = $mc.basename($_FILES["vendor_invoice"]["name"]);
                  $basename = basename($_FILES["vendor_invoice"]["name"]);
                  $file_name = $this->clean($f_name);
                  $target_file = $target_dir.$file_name;
                  $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                  if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                        && $imageFileType != "gif" && $imageFileType != "webp" && $imageFileType != "pdf") {
                      $msg_doc = "Sorry, only Image and PDF files are allowed.";
                  }else{
                    if(move_uploaded_file($_FILES["vendor_invoice"]["tmp_name"], $target_file)){
                      $modelPic = TblVendorInvoice::find()->where(['id'=>$model->id])->one();
                      $modelPic->invoice_file = $file_name;
                      $modelPic->save();
                    }
                  }//-----else for checking file type ended ----
                }
                if($msg_doc != ""){
                  Yii::$app->getSession()->setFlash('error', $msg_doc);
                  return $this->redirect(['update', 'id' => $model->id]);
                }else{
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
            // return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TblVendorInvoice model.
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
        //-----CHECK IF PAYMENT EXISTS FOR THE INVOICE THEN CAN;T DELETE THE ENTRY ----

        $model = TblVendorInvoice::find()->where(['id'=>$id])->one();
        $model->status = 0;
        if($model->save()){
          $ip = Yii::$app -> getRequest() -> getUserIp();
          $mod_by = $session -> get('userId');
          $mod_time = date('Y-m-d H:i:s');
          $updatearr = [
            'ip' => $ip,
            'mod_by' => $mod_by,
            'mod_time' => $mod_time,
            'status' => 0
          ];
          TblVendorInvoiceItem::updateAll($updatearr,'fk_vendor_invoice_id='.$id);
          \app\models\TblProductReceiving::updateAll($updatearr,'fk_vendor_invoice_id='.$id);
          //---DELETE THE RECIEVING ENTRIES RELATED TO THIS INVOICE AS WELL -----

          Yii::$app->getSession()->setFlash('success', 'Record deleted successfully.');
        }else{
          Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblVendorInvoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return TblVendorInvoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblVendorInvoice::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    //-----------custom functions -----------------
    function clean($string) {
      $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
      return preg_replace('/[^A-Za-z0-9.\-]/', '', $string); // Removes special chars.
    }
     public function actionGetvendor(){
      Yii::$app->response->format = Response::FORMAT_JSON;
      $fk_vendor_id = $_POST['fk_vendor_id'];
      $data = [];
      //-------get the terms for the client ------
      $terms = 0;
      $purchase_orders = [];
      $getclient = TblVendor::find()->where(['id'=>$fk_vendor_id])->andWhere('status != 0')->one();
      if(isset($getclient) && $getclient->fk_terms_id != ""){
        $terms = $getclient->fk_terms_id;
      }
      $session = Yii::$app -> session;
      $fk_location_id = $session['userCompany'];
      $allporders = \app\models\TblPurchaseOrder::find()->where(['status'=>1])->andWhere(['fk_location_id'=>$fk_location_id,'fk_vendor_id'=>$fk_vendor_id])->orderBy(['crt_by'=>SORT_DESC])->all();
      foreach ($allporders as $row) {
          $data[] = [
              'value' => $row['id'],
              'label' => $row['po_number']
          ];
      }
      return ['terms'=>$terms,'purchase'=>$data];
    }
    public function actionGettermdays(){
      $term_id = $_POST['term_val'];
      $invoice_date = $_POST['invoice_date'];
      $getdays = TblTerms::find()->where(['id'=>$term_id])->andWhere(['status'=>1])->one();
      $due_date = "";
      if(isset($getdays) && $getdays->id != ""){
        $no_of_days = $getdays->days;
        if($no_of_days > 0){
          $due_date = date('Y-m-d', strtotime($invoice_date. ' + '.$no_of_days.' days'));
        }else{
          $due_date = $invoice_date;
        }
      }else{
        $due_date = $invoice_date;
      }
      return $due_date;
    }
    public function actionGetproductdesc(){
      $client_id = $_POST['client_id'];
      $product_id = $_POST['product_id'];
      //--------check for client --------
      $getclient = TblClient::find()->where(['id'=>$client_id])->one();
      if(isset($getclient) && $getclient->tier_id != ""){
        $tier_id = $getclient->tier_id;
      }else{
        $tier_id = 1; //--by default keep it one
      }
      $get_tier_markup = 15; //----this is defualt markup
      $getproduct = TblProduct::find()->where(['id'=>$product_id])->one();
      if(isset($getproduct) && $getproduct->id != ""){
        $description = $getproduct->description;
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
        }
      }else{
        $description = "";
      }
      //-----get the recent most product pricing from TblProductReceiving----
      $getrecent = TblProductReceiving::find()
                  ->where(['fk_product_id'=>$product_id])
                  ->andWhere(['status'=>1])
                  ->orderBy(['id'=>SORT_DESC])
                  ->one();
      $price = 0; //----default price ------
      if(isset($getrecent) && $getrecent->price_per_item != ""){
        $price_per_item = $getrecent->price_per_item;
        $price = $price_per_item+$price_per_item*($get_tier_markup/100);
        $price = number_format($price,2,'.','');
        // $price = round($price,2);
      }
      $array = array("description" => $description,'price' => $price);
      // return $description;
      return json_encode($array);
    }
}
