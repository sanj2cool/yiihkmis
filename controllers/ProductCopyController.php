<?php

namespace app\controllers;
use Yii;
use app\models\TblProduct;
use app\models\TblProductSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\TblProductCrossRef;
use app\models\TblProductAlternate;
use app\models\TblProductImage;
use app\models\TblProductCategory;
use app\models\TblProductRelated;
use app\models\TblProductCompetition;
use app\models\TblProductBrand;
use app\models\TblProductAssignedCategory;
use yii\helpers\Url;
use Picqer\Barcode\BarcodeGeneratorPNG;
use yii\db\Query;
use app\models\TblMenuAccess;
/**
 * ProductController implements the CRUD actions for TblProduct model.
 */
class ProductCopyController extends Controller
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
        $menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>19,'status'=>1])->one();
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
     * Lists all TblProduct models.
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
        $searchModel = new TblProductSearch();
        $params = Yii::$app->request->queryParams;

         // If category_id is passed, ensure it's set in the search model
         // if (isset($params['categories'])) {
         //     $searchModel->categories = $params['categories'];
         // }

        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionResetFilters()
    {
        $searchModel = new TblProductSearch();
        $searchModel->clearSearchState(); // Clear the session data
        return $this->redirect(['index']); // Redirect back to the index page
    }

    /**
     * Displays a single TblProduct model.
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
     * Creates a new TblProduct model.
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
        $model = new TblProduct();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
              //------first check if same name exists --------
            $part_name = $model->name;
            //======check the name against the part name in the products table =======
            $checkProduct = TblProduct::find()
                            ->where('status != 0')
                            ->andWhere("LOWER(REPLACE(REPLACE(REPLACE(REPLACE(name, ' ', ''), '-', ''), '.', ''), '_', '')) = LOWER(REPLACE(REPLACE(REPLACE(REPLACE('".$part_name."' , ' ', ''), '-', ''), '.', ''), '_', ''))")
                            ->all();
            $return_str_product = "";
            $return_status = 0; //=========no part name matched ==============
            if(isset($checkProduct) && count($checkProduct) > 0){
              $return_status++;
              $return_str_product .= "<h5>Products with same name:</h5><div class='row'><div class='col-md-12 table-responsive table-sm'><table class='table table-bordered'><tr><th>Sr. No.</th><th>Item Name</th><th>Link to the item</th></tr>";
              $i = 1;
              foreach($checkProduct as $cp){
                $return_str_product .= "<tr><td>".$i."</td><td>".$cp->name."</td><td><a href='".Url::to(['product/update','id'=>$cp->id])."' target='_blank'>View Product</a></td></tr>";
                $i++;
              }
              $return_str_product .= "</table></div></div>";
            }//====if isset for products exists ended ===
            if($return_status >= 1){
              Yii::$app->getSession()->setFlash('error_item', 'Item(s) with same name already exists.<br />'.$return_str_product);
              return $this->render('create', [
                  'model' => $model,
                  'return_str_product' => $return_str_product
              ]);
            }else{
              //-----no product found
              if($model->save()){
                //-------save and create the unique internal SKU and barcode-----
                $internal_sku = 'HK' . str_pad($model->id, 7, '0', STR_PAD_LEFT);
                $modelUpdate = TblProduct::find()->where(['id'=>$model->id])->one();
                $modelUpdate->internal_sku = $internal_sku;
                if($modelUpdate->save()){
                  $this->generateBarcodeSingle($internal_sku);
                }else{
                  // echo "not saved:::".$ga->id."<br />";
                }
                //--------now need to save all the cross refs if any -----
                if(isset($_POST['cross_ref'])){
                  $cross_ref = $_POST['cross_ref'];
                }else{
                  $cross_ref = [];
                }
                if(count($cross_ref) > 0){
                  for($i = 0; $i < count($cross_ref); $i++){
                    //------first need to check if same product exists in cross ref for this product --
                    //------sometimes user can add one product twice ------
                    if(isset($cross_ref[$i]) && $cross_ref[$i] != ""){
                          //---create new entry ----
                          $modelRef = new TblProductCrossRef();
                          $modelRef->fk_product_id = $model->id;
                          $modelRef->cross_ref = $cross_ref[$i];
                          $modelRef->save();
                    }//-----if for checking particular item exists ----

                  }//--------for loop ended -----------
                }//----if for checking count ended ------
                //-----------cross ref ended ----------------


                //--------now need to save all the alt products if any -----
                if(isset($_POST['prod_alt'])){
                  $prod_alt = $_POST['prod_alt'];
                }else{
                  $prod_alt = [];
                }
                if(count($prod_alt) > 0){
                  for($i = 0; $i < count($prod_alt); $i++){
                    //------first need to check if same product exists in cross ref for this product --
                    //------sometimes user can add one product twice ------
                    if(isset($prod_alt[$i]) && $prod_alt[$i] != ""){
                        $check = TblProductAlternate::find()->where(['status'=>1,'fk_product_id'=>$model->id,'fk_alt_product_id'=>$prod_alt[$i]])->one();
                        if(isset($check) && $check->id != ""){
                          //-----item already exists --
                          //----skip this one ----
                        }else {
                          //---create new entry ----
                          $modelRef = new TblProductAlternate();
                          $modelRef->fk_product_id = $model->id;
                          $modelRef->fk_alt_product_id = $prod_alt[$i];
                          $modelRef->save();
                        }
                    }//-----if for checking particular item exists ----

                  }//--------for loop ended -----------
                }//----if for checking count ended ------
                //-----------alt products ended ----------------
                //=========related products logic started =========
                if(isset($_POST['prod_related'])){
                  $prod_related = $_POST['prod_related'];
                }else{
                  $prod_related = [];
                }
                if(count($prod_related) > 0){
                  for($i = 0; $i < count($prod_related); $i++){
                    //------first need to check if same product exists in cross ref for this product --
                    //------sometimes user can add one product twice ------
                    if(isset($prod_related[$i]) && $prod_related[$i] != ""){
                        $check = TblProductRelated::find()->where(['status'=>1,'fk_product_id'=>$model->id,'fk_related_product_id'=>$prod_related[$i]])->one();
                        if(isset($check) && $check->id != ""){
                          //-----item already exists --
                          //----skip this one ----
                        }else {
                          //---create new entry ----
                          $modelRef = new TblProductRelated();
                          $modelRef->fk_product_id = $model->id;
                          $modelRef->fk_related_product_id = $prod_related[$i];
                          $modelRef->save();
                        }
                    }//-----if for checking particular item exists ----

                  }//--------for loop ended -----------
                }//----if for checking count ended ------
                //=========related products logic ended ===========
                //--------now need to save all seller competitors if any -----
                if(isset($_POST['seller_name'])){
                  $seller_name = $_POST['seller_name'];
                }else{
                  $seller_name = [];
                }
                if(isset($_POST['seller_price'])){
                  $seller_price = $_POST['seller_price'];
                }else{
                  $seller_price = [];
                }
                if(count($seller_name) > 0){
                  for($i = 0; $i < count($seller_name); $i++){
                    //------first need to check if same product exists in cross ref for this product --
                    //------sometimes user can add one product twice ------
                    if(isset($seller_name[$i]) && $seller_name[$i] != ""){
                          //---create new entry ----
                          $modelComp = new TblProductCompetition();
                          $modelComp->fk_product_id = $model->id;
                          $modelComp->seller_name = $seller_name[$i];
                          $modelComp->selling_price = $seller_price[$i];
                          $modelComp->save();
                    }//-----if for checking particular item exists ----

                  }//--------for loop ended -----------
                }//----if for checking count ended ------
                //-----------seller competitors ended ----------------

                //--------product images started ------------
                $msg_doc = "";
                $target_dir = "../web/product-images/";
                if(isset($_FILES["file_url"]) && count($_FILES["file_url"]) > 0){
                  for($j = 0; $j < count($_FILES["file_url"]); $j++){
                    if(isset($_FILES["file_url"]["name"][$j]) && $_FILES["file_url"]["name"][$j] != ""){
                      $mc=md5(microtime());
                      $f_name = $mc.basename($_FILES["file_url"]["name"][$j]);
                      $basename = basename($_FILES["file_url"]["name"][$j]);
                      $file_name = $this->clean($f_name);
                      $target_file = $target_dir.$file_name;
                      $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                      if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                            && $imageFileType != "gif" && $imageFileType != "webp") {
                          $msg_doc = "Sorry, only Image files are allowed.";
                      }else{
                        if(move_uploaded_file($_FILES["file_url"]["tmp_name"][$j], $target_file)){
                          $modelPic = new TblProductImage();
                          $modelPic->fk_product_id = $model->id;
                          $modelPic->url = $file_name;
                          $modelPic->save();
                        }
                      }//-----else for checking file type ended ----
                    }//-------if for checking file uplaoded ended
                  }//-----for loop ended --------
                }//-------if isset ended ---------
                //--------product images ended --------------
                //---------check for brands started --------------
                if(isset($_POST['brand']) && count($_POST['brand']) > 0){
                  $brand_arr = $_POST['brand'];
                  for($i = 0; $i < count($brand_arr); $i++){
                    $modelBrand = new TblProductBrand();
                    $modelBrand->fk_product_id = $model->id;
                    $modelBrand->fk_brand_id = $brand_arr[$i];
                    $modelBrand->save();
                  }
                }
                //---------check for brand ended -----------------
                //---------check for categories started --------------
                if(isset($_POST['categories']) && count($_POST['categories']) > 0){
                  $categories_arr = $_POST['categories'];
                  for($c = 0; $c < count($categories_arr); $c++){
                    $modelAssignedCat = new TblProductAssignedCategory();
                    $modelAssignedCat->fk_product_id = $model->id;
                    $modelAssignedCat->fk_category_id = $categories_arr[$c];
                    $modelAssignedCat->save();
                  }
                }
                //---------check for categories ended -----------------
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
                }//------else of no error ended ----
              }else{
                return $this->render('create', [
                    'model' => $model,
                ]);
              }//-------else ended ----------
            }//-------else ended ----------
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
     * Updates an existing TblProduct model.
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

        if ($this->request->isPost && $model->load($this->request->post())) {
          //------first check if same name exists --------
        $part_name = $model->name;
        //======check the name against the part name in the products table =======
        $checkProduct = TblProduct::find()
                        ->where('status != 0')
                        ->andWhere('id != '.$model->id)
                        ->andWhere("LOWER(REPLACE(REPLACE(REPLACE(REPLACE(name, ' ', ''), '-', ''), '.', ''), '_', '')) = LOWER(REPLACE(REPLACE(REPLACE(REPLACE('".$part_name."' , ' ', ''), '-', ''), '.', ''), '_', ''))")
                        ->all();
        $return_str_product = "";
        $return_status = 0; //=========no part name matched ==============
        if(isset($checkProduct) && count($checkProduct) > 0){
          $return_status++;
          $return_str_product .= "<h5 class='alert-heading fw-semibold'>Products with same name:</h5><div class='row'><div class='col-md-12 table-responsive table-sm'><table class='table table-bordered'><tr><th>Sr. No.</th><th>Item Name</th><th>Link to the item</th></tr>";
          $i = 1;
          foreach($checkProduct as $cp){
            $return_str_product .= "<tr><td>".$i."</td><td>".$cp->name."</td><td><a href='".Url::to(['product/update','id'=>$cp->id])."' target='_blank'>View Product</a></td></tr>";
            $i++;
          }
          $return_str_product .= "</table></div></div>";
        }//====if isset for products exists ended ===
        if($return_status >= 1){
          Yii::$app->getSession()->setFlash('error_item', $return_str_product);
          return $this->render('update', [
              'model' => $model
          ]);
        }else{

        if($model->save()){
          //--------now need to save all the cross refs if any -----
          if(isset($_POST['cross_ref'])){
            $cross_ref = $_POST['cross_ref'];
          }else{
            $cross_ref = [];
          }
          TblProductCrossRef::updateAll(array('status' => 0),'fk_product_id='.$model->id);
          if(count($cross_ref) > 0){
            for($i = 0; $i < count($cross_ref); $i++){
              //------first need to check if same product exists in cross ref for this product --
              //------sometimes user can add one product twice ------
              if(isset($cross_ref[$i]) && $cross_ref[$i] != ""){
                    //---create new entry ----
                    $modelRef = new TblProductCrossRef();
                    $modelRef->fk_product_id = $model->id;
                    $modelRef->cross_ref = $cross_ref[$i];
                    $modelRef->save();
              }//-----if for checking particular item exists ----

            }//--------for loop ended -----------
          }//----if for checking count ended ------
          //-----------cross ref ended ----------------

            //--------now need to save all the alt products if any -----
            TblProductAlternate::updateAll(array('status' => 0),'fk_product_id='.$model->id);
            if(isset($_POST['prod_alt'])){
              $prod_alt = $_POST['prod_alt'];
            }else{
              $prod_alt = [];
            }
            if(count($prod_alt) > 0){
              for($i = 0; $i < count($prod_alt); $i++){
                //------first need to check if same product exists in cross ref for this product --
                //------sometimes user can add one product twice ------
                if(isset($prod_alt[$i]) && $prod_alt[$i] != ""){
                    $check = TblProductAlternate::find()->where(['status'=>1,'fk_product_id'=>$model->id,'fk_alt_product_id'=>$prod_alt[$i]])->one();
                    if(isset($check) && $check->id != ""){
                      //-----item already exists --
                      //----skip this one ----
                    }else {
                      //---create new entry ----
                      $modelRef = new TblProductAlternate();
                      $modelRef->fk_product_id = $model->id;
                      $modelRef->fk_alt_product_id = $prod_alt[$i];
                      $modelRef->save();
                    }
                }//-----if for checking particular item exists ----

              }//--------for loop ended -----------
            }//----if for checking count ended ------
            //-----------alt products ended ----------------
            //=========related products logic started =========
            TblProductRelated::updateAll(array('status' => 0),'fk_product_id='.$model->id);
            if(isset($_POST['prod_related'])){
              $prod_related = $_POST['prod_related'];
            }else{
              $prod_related = [];
            }
            if(count($prod_related) > 0){
              for($i = 0; $i < count($prod_related); $i++){
                //------first need to check if same product exists in cross ref for this product --
                //------sometimes user can add one product twice ------
                if(isset($prod_related[$i]) && $prod_related[$i] != ""){
                    $check = TblProductRelated::find()->where(['status'=>1,'fk_product_id'=>$model->id,'fk_related_product_id'=>$prod_related[$i]])->one();
                    if(isset($check) && $check->id != ""){
                      //-----item already exists --
                      //----skip this one ----
                    }else {
                      //---create new entry ----
                      $modelRef = new TblProductRelated();
                      $modelRef->fk_product_id = $model->id;
                      $modelRef->fk_related_product_id = $prod_related[$i];
                      $modelRef->save();
                    }
                }//-----if for checking particular item exists ----

              }//--------for loop ended -----------
            }//----if for checking count ended ------
            //=========related products logic ended ===========
            //--------now need to save all seller competitors if any -----
            if(isset($_POST['seller_name'])){
              $seller_name = $_POST['seller_name'];
            }else{
              $seller_name = [];
            }
            if(isset($_POST['seller_price'])){
              $seller_price = $_POST['seller_price'];
            }else{
              $seller_price = [];
            }
            TblProductCompetition::updateAll(array('status' => 0),'fk_product_id='.$model->id);
            if(count($seller_name) > 0){
              for($i = 0; $i < count($seller_name); $i++){
                //------first need to check if same product exists in cross ref for this product --
                //------sometimes user can add one product twice ------
                if(isset($seller_name[$i]) && $seller_name[$i] != ""){
                      //---create new entry ----
                      $modelComp = new TblProductCompetition();
                      $modelComp->fk_product_id = $model->id;
                      $modelComp->seller_name = $seller_name[$i];
                      $modelComp->selling_price = $seller_price[$i];
                      $modelComp->save();
                }//-----if for checking particular item exists ----

              }//--------for loop ended -----------
            }//----if for checking count ended ------
            //-----------seller competitors ended ----------------
          //--------product images started ------------
          $msg_doc = "";
          $target_dir = "../web/product-images/";
          if(isset($_FILES["file_url"]) && count($_FILES["file_url"]) > 0){
            for($j = 0; $j < count($_FILES["file_url"]); $j++){
              if(isset($_FILES["file_url"]["name"][$j]) && $_FILES["file_url"]["name"][$j] != ""){
                $mc=md5(microtime());
                $f_name = $mc.basename($_FILES["file_url"]["name"][$j]);
                $basename = basename($_FILES["file_url"]["name"][$j]);
                $file_name = $this->clean($f_name);
                $target_file = $target_dir.$file_name;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                      && $imageFileType != "gif" && $imageFileType != "webp") {
                    $msg_doc = "Sorry, only Image files are allowed.";
                }else{
                  if(move_uploaded_file($_FILES["file_url"]["tmp_name"][$j], $target_file)){
                    $modelPic = new TblProductImage();
                    $modelPic->fk_product_id = $model->id;
                    $modelPic->url = $file_name;
                    $modelPic->save();
                  }
                }//-----else for checking file type ended ----
              }//-------if for checking file uplaoded ended
            }//-----for loop ended --------
          }//-------if isset ended ---------
          //--------product images ended --------------
          //---------check for brands started --------------
          TblProductBrand::updateAll(array('status' => 0),'fk_product_id='.$model->id);
          if(isset($_POST['brand']) && count($_POST['brand']) > 0){
            $brand_arr = $_POST['brand'];
            for($i = 0; $i < count($brand_arr); $i++){
              $modelBrand = new TblProductBrand();
              $modelBrand->fk_product_id = $model->id;
              $modelBrand->fk_brand_id = $brand_arr[$i];
              $modelBrand->save();
            }
          }
          //---------check for brand ended -----------------
          //---------check for categories started --------------
          TblProductAssignedCategory::updateAll(array('status' => 0),'fk_product_id='.$model->id);
          if(isset($_POST['categories']) && count($_POST['categories']) > 0){
            $categories_arr = $_POST['categories'];
            for($c = 0; $c < count($categories_arr); $c++){
              $modelAssignedCat = new TblProductAssignedCategory();
              $modelAssignedCat->fk_product_id = $model->id;
              $modelAssignedCat->fk_category_id = $categories_arr[$c];
              $modelAssignedCat->save();
            }
          }
          //---------check for categories ended -----------------
            // return $this->redirect(['view', 'id' => $model->id]);
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
        }else{
          Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
          return $this->redirect(['update', 'id' => $model->id]);
        }//------else of model not saved ended ----------
      }//------else of no duplicate found ended


        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TblProduct model.
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
        $model = TblProduct::find()->where(['id'=>$id])->one();
        $model->status = 0;
        if($model->save()){
          TblProductCrossRef::updateAll(array('status' => 0),'fk_product_id='.$id);
          TblProductAlternate::updateAll(array('status' => 0),'fk_product_id='.$id);
          TblProductImage::updateAll(array('status' => 0),'fk_product_id='.$id);
          TblProductRelated::updateAll(array('status' => 0),'fk_product_id='.$id);
          TblProductCompetition::updateAll(array('status' => 0),'fk_product_id='.$id);
          TblProductBrand::updateAll(array('status' => 0),'fk_product_id='.$id);
          TblProductAssignedCategory::updateAll(array('status' => 0),'fk_product_id='.$id);
          Yii::$app->getSession()->setFlash('success', 'Record deleted successfully');
        }else{
          Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the TblProduct model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return TblProduct the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblProduct::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    function clean($string) {
      $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
      return preg_replace('/[^A-Za-z0-9.\-]/', '', $string); // Removes special chars.
    }
    public function actionImport()
  {
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    return $this->render('import');
  }
  public function actionUploadcsv(){
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    $target_dir = "../web/csv/";
    if(isset($_FILES['my-image']['name']) && $_FILES['my-image']['name']!=""){
        $mc=md5(microtime());
        $f_name = $mc.basename($_FILES["my-image"]["name"]);
        $f_name = preg_replace("/\s+/", "", $f_name);
        $file_name = $this->clean($f_name);
        $target_file = $target_dir.$file_name;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        if($imageFileType != "csv") {
          $msg_doc = "Sorry, only CSV files are allowed.";
          //send back to import
          Yii::$app->getSession()->setFlash('error', $msg_doc);
          return $this->redirect(['import']);
        }else{
          if(move_uploaded_file($_FILES["my-image"]["tmp_name"], $target_file)){
            //now file is uploaded
            $csvFile = '../web/csv/'.$file_name; // Replace with the path to your CSV file
            if (($handle = fopen($csvFile, 'r')) !== false) {
              $header = fgetcsv($handle); // Read the first row as column headers
              while (($data = fgetcsv($handle)) !== false) {
                  $row = array_combine($header, $data); // Combine column headers and data
                  // Now you can access data by column name
                  if(isset($row['sku'])){
                    $sku = $row['sku'];
                  }else{
                    $sku = "";
                  }

                  if(isset($row['name'])){
                    $name = $row['name'];
                  }else{
                    $name = "";
                  }
                  if(isset($row['category'])){
                    $category = $row['category'];
                  }else{
                    $category = "";
                  }
                  if(isset($row['description'])){
                    $description = $row['description'];
                  }else{
                    $description = "";
                  }
                  if(isset($row['reorder_qty'])){
                    $reorder_qty = $row['reorder_qty'];
                  }else{
                    $reorder_qty = "";
                  }
                  //--------get category ------------------
                  $lowercasedcat = strtolower(str_replace(' ', '', $category));
                  $getcatid = TblProductCategory::find()->where('LOWER(REPLACE(title, " ", "")) = "'.$lowercasedcat.'"')->andWhere(['status'=>1])->one();
                  if(isset($getcatid) && $getcatid->id != ""){
                    $cat_id = $getcatid->id;
                  }else{
                    $cat_id = 0;
                  }
                  //------need to check if item existing using sku -----
                  $check = TblProduct::find()->where(['status'=>1,'sku'=>$sku])->one();
                  if(isset($check) && $check->id != ""){
                    //------update existing item  ----
                    $check->name = $name;
                    $check->description = $description;
                    $check->fk_category_id = $cat_id;
                    $check->reorder_qty = $reorder_qty;
                    $check->save();
                  }else{
                    //------create new record ----
                    $model = new TblProduct();
                    $model->sku = $sku;
                    $model->name = $name;
                    $model->description = $description;
                    $model->fk_category_id = $cat_id;
                    $model->reorder_qty = $reorder_qty;
                    if($model->save()){
                      $this->generateInternalSkuBarcode($model->id);
                    }
                  }
              }//---while loop ended ---------
              fclose($handle);
              Yii::$app->getSession()->setFlash('success', 'Data imported successfully.');
              return $this->redirect(['index']);
            }else{
              //soemthignwent wrong file not uploaded send back to import
              Yii::$app->getSession()->setFlash('error', 'Error opening the CSV file.');
              return $this->redirect(['import']);
            }//----file not opening------
          }else{
            //soemthignwent wrong file not uploaded send back to import
            Yii::$app->getSession()->setFlash('error', 'Error opening the CSV file.');
            return $this->redirect(['import']);

          }//-----file not uploaded else ended ---
        }//-----else of file found ----
    }else{
      //no file found send back to import
      Yii::$app->getSession()->setFlash('error', 'No file found. Please upload a file.');
      return $this->redirect(['import']);
    }
  }//----------function ended ------------
  public function actionDelpics(){
      $del_id = $_POST['del_id'];
      $model = TblProductImage::find()->where(['id'=>$del_id])->one();
      $model->status = 0;
      if($model->save()){
        return "yes";
      }else{
        return "no";
      }
    }
    public function actionCheckpartname(){
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    // Get raw JSON POST data
    $data = json_decode(Yii::$app->request->getRawBody(), true);

    // Check if 'name' key exists in the decoded data
    if (isset($data['name'])) {
      $part_name = $data['name'];
      // Extract only the numeric part by removing everything else
      $numeric_part = preg_replace('/[^0-9]/', '', $part_name);
      //=====3 levels of checking ========
      $return_str_product = "";
      $return_str_cross_ref = "";
      $return_str_similar = "";
      $return_status = 0; //=========no part name matched ==============
      //======check the name against the part name in the products table =======
      $checkProduct = TblProduct::find()
                      ->where('status != 0')
                      ->andWhere("LOWER(REPLACE(REPLACE(REPLACE(REPLACE(name, ' ', ''), '-', ''), '.', ''), '_', '')) = LOWER(REPLACE(REPLACE(REPLACE(REPLACE('".$part_name."' , ' ', ''), '-', ''), '.', ''), '_', ''))")
                      ->all();
      if(isset($checkProduct) && count($checkProduct) > 0){
        $return_status++;
        $return_str_product .= "<h5>Items with same name:</h5><div class='row'><div class='col-md-12 table-responsive'><table class='table table-bordered'><tr><th>Sr. No.</th><th>Item Name</th><th>Link to the item</th></tr>";
        $i = 1;
        foreach($checkProduct as $cp){
          $return_str_product .= "<tr><td>".$i."</td><td>".$cp->name."</td><td><a href='".Url::to(['product/update','id'=>$cp->id])."' target='_blank'>View Product</a></td></tr>";
          $i++;
        }
        $return_str_product .= "</table></div></div>";
      }//====if isset for products exists ended ===
      //=======check the name against the name in cross references table =======
      $checkProductCross = TblProductCrossRef::find()
                      ->where('status != 0')
                      ->andWhere("LOWER(REPLACE(REPLACE(REPLACE(REPLACE(cross_ref, ' ', ''), '-', ''), '.', ''), '_', '')) = LOWER(REPLACE(REPLACE(REPLACE(REPLACE('".$part_name."' , ' ', ''), '-', ''), '.', ''), '_', ''))")
                      ->orWhere(['like', "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(cross_ref, ' ', ''), '-', ''), '.', ''), '_', ''))", strtolower(str_replace([' ', '-', '.', '_'], '', $part_name))])
                      ->groupBy(['fk_product_id'])
                      ->all();
      if(isset($checkProductCross) && count($checkProductCross) > 0){
        $return_status++;
        $return_str_cross_ref .= "<h5>Items with same/similar Cross Reference:</h5><div class='row'><div class='col-md-12 table-responsive'><table class='table table-bordered'><tr><th>Sr. No.</th><th>Cross Reference Name</th><th>Link to the item</th></tr>";
        $i = 1;
        foreach($checkProductCross as $cp){
          $return_str_cross_ref .= "<tr><td>".$i."</td><td>".$cp->cross_ref."</td><td><a href='".Url::to(['product/update','id'=>$cp->fk_product_id])."' target='_blank'>View Product</a></td></tr>";
          $i++;
        }
        $return_str_cross_ref .= "</table></div></div>";
      }//====if isset for products exists ended ===
      //=======check the numeric part of name against the numeric name of name in products table ======
      //====only check if same name does not exists ======
      if(isset($checkProduct) && count($checkProduct) == 0 && $numeric_part != ""){
        $checkProductSimilar = TblProduct::find()
                        ->where('status != 0')
                        ->andWhere("name LIKE '%".$numeric_part."%'")
                        ->all();
        if(isset($checkProductSimilar) && count($checkProductSimilar) > 0){
          $return_status++;
          $return_str_similar .= "<h5>Items with similar name:</h5><div class='row'><div class='col-md-12 table-responsive'><table class='table table-bordered'><tr><th>Sr. No.</th><th>Item Number</th><th>Link to the item</th></tr>";
          $i = 1;
          foreach($checkProductSimilar as $cp){
            $return_str_similar .= "<tr>
            <td>".$i."</td>
            <td>".$cp->name."</td>
            <td><a href='".Url::to(['product/update','id'=>$cp->id])."' target='_blank'>View Product</a></td>
            </tr>";
            $i++;
          }
          $return_str_similar .= "</table>
            </div>
            </div>";
        }//====if isset for products exists ended ===
      }

      $array = array(
        "product" => $return_str_product,
        "crossref" => $return_str_cross_ref,
        "similar" => $return_str_similar,
        "return_status" => $return_status
      );
      return json_encode($array);
    }else{
      $array = array(
        "product" => "",
        "crossref" => "",
        "similar" => "",
        "return_status" => 0
      );
      return json_encode($array);
    }




    }//=====function ended =====
    public function generateInternalSkuBarcode($product_id){
      $internal_sku = 'HK' . str_pad($product_id, 7, '0', STR_PAD_LEFT);
      $modelUpdate = TblProduct::find()->where(['id'=>$product_id])->one();
      $modelUpdate->internal_sku = $internal_sku;
      if($modelUpdate->save()){
        $this->generateBarcodeSingle($internal_sku);
      }else{
      }
    }
    public function generateBarcodeSingle($productCode){
        // Create a new instance of the Barcode Generator
        $generator = new BarcodeGeneratorPNG();
        // Generate the barcode as a PNG image
        $barcode = $generator->getBarcode($productCode, $generator::TYPE_CODE_128);
        // Save the barcode to a file
        $filePath = Yii::getAlias('@webroot/product-barcodes/' . $productCode . '.png');
        file_put_contents($filePath, $barcode);
    }//----function ended

    public function actionGenerateBarcode(){
      // Example product data
      $productCode = '123456789012'; // This should be your product code or SKU
      $internal_sku = 'HK' . str_pad(23, 7, '0', STR_PAD_LEFT);
      // echo $internal_sku;
      // Create a new instance of the Barcode Generator
      $generator = new BarcodeGeneratorPNG();

      // Generate the barcode as a PNG image
      $barcode = $generator->getBarcode($productCode, $generator::TYPE_CODE_128);

      // Save the barcode to a file
      $filePath = Yii::getAlias('@webroot/product-barcodes/' . $productCode . '.png');
      file_put_contents($filePath, $barcode);

      // Alternatively, you can output the barcode directly to the browser:
      // Set the correct content-type for PNG image
        // header('Content-Type: image/png');
        // echo $barcode;
        // exit; // Prevent Yii from sending additional outpu
    }
    public function actionGenerateInternalsku(){
      //-----generate internal sku for the existing products --------
      $getactiveproducts = TblProduct::find()->where(['status'=>1])->orderBy(['id'=>SORT_ASC])->all();
      // echo count($getactiveproducts);
      // foreach($getactiveproducts as $ga){
        // echo $ga->id.'<br />';
          // $internal_sku = 'HK' . str_pad($ga->id, 7, '0', STR_PAD_LEFT);
          // $modelUpdate = TblProduct::find()->where(['id'=>$ga->id])->one();
          // $modelUpdate->internal_sku = $internal_sku;
          // if($modelUpdate->save()){
          //   echo "internal sku saved"."<br />";
          // }else{
          //   echo "not saved:::".$ga->id."<br />";
          // }
      // }
    }
    public function actionGenerateBarcodeForProducts(){
      // Example product data
      $getactiveproducts = TblProduct::find()->where(['status'=>1])->orderBy(['id'=>SORT_ASC])->all();
      // echo count($getactiveproducts);
      foreach($getactiveproducts as $ga){
        $productCode = $ga->internal_sku;
        // Create a new instance of the Barcode Generator
        $generator = new BarcodeGeneratorPNG();
        // Generate the barcode as a PNG image
        $barcode = $generator->getBarcode($productCode, $generator::TYPE_CODE_128);
        // Save the barcode to a file
        $filePath = Yii::getAlias('@webroot/product-barcodes/' . $productCode . '.png');
        if(file_put_contents($filePath, $barcode)){
          echo "barcode geneated<br />";
        }else{
          echo "not generated<br />";
        }
      }//------for loop ended -------
    }//----function ended
    public function actionSearch(){
      $internal_sku = $_POST['internal_sku'];

      $getproduct = TblProduct::find()->where(['internal_sku'=>trim($internal_sku)])->one();
      if(isset($getproduct) && $getproduct->id != ""){
        if($getproduct->status == 1){
          $return_str = "SKU: ".$internal_sku;
          $return_str .= "<br />".$getproduct->name;
          $getcategory = TblProductCategory::find()->where(['id'=>$getproduct->fk_category_id,'status'=>1])->one();
          if(isset($getcategory) && $getcategory->id != ""){
            $return_str .= "<br />".$getcategory->title;
          }
          $query = (new Query())
            ->select([
                'product_id' => 'p.id',
                'current_qty' => 'IFNULL(SUM(pr.no_of_items), 0) - IFNULL(SUM(pa.no_of_items), 0)'
            ])
            ->from('tbl_product p')
            ->leftJoin('tbl_product_receiving pr', 'p.id = pr.fk_product_id')
            ->leftJoin('tbl_product_allotment pa', 'p.id = pa.fk_product_id')
            ->where(['p.id' => $getproduct->id])
            ->groupBy('p.id')
            ->one();
            // print_r($query);
            if(isset($query['current_qty']) && $query['current_qty'] != ""){
              $current_qty = $query['current_qty'];
            }else{
              $current_qty = 0;
            }
            $return_str .= "<br />Qty in Stock: ".$current_qty;
          return $return_str;
        }else{
          return "Item not active";
        }
      }else{
        return "Item not found";
      }
    }

}
