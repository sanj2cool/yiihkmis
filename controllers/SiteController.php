<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\TblUser;
use app\models\TblAttendance;
use app\models\TblProduct;
use app\models\TblProductReceiving;
use app\models\TblProductAllotment;
use app\models\TblPrintedLabels;
use app\models\TblBinLocations;
use app\models\TblPrintedBarcodes;
use app\models\TblProductCrossRef;
use yii\db\Query;
use yii\helpers\Url;
use yii\db\Expression;
use yii\helpers\Html;
use yii\log\Logger;
class SiteController extends Controller
{
  /**
  * {@inheritdoc}
  */
  public function behaviors()
  {
    return [
      // 'access' => [
      //     'class' => AccessControl::class,
      //     'only' => ['logout','index'], // Add 'index' to the access control
      //     'rules' => [
      //         [
      //             'actions' => ['logout','index'], // Add 'index' to the access control
      //             'allow' => true,
      //             'roles' => ['@'], // Require users to be logged in
      //         ],
      //     ],
      // ],
      // 'verbs' => [
      //     'class' => VerbFilter::class,
      //     'actions' => [
      //         'logout' => ['post'],
      //     ],
      // ],
    ];
  }

  /**
  * {@inheritdoc}
  */
  public function actions()
  {
    return [
      'error' => [
        'class' => 'yii\web\ErrorAction',
      ],
      'captcha' => [
        'class' => 'yii\captcha\CaptchaAction',
        'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
      ],
    ];
  }
  public function beforeAction($action) {
    $this->enableCsrfValidation = false;
    return parent::beforeAction($action);
  }
  /**
  * Displays homepage.
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
  public function actionIndex2()
  {
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      // Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    $this->layout = "main2";
    return $this->render('index2');

  }

  /**
  * Login action.
  *
  * @return Response|string
  */
  public function actionLogin()
  {
    $this->layout = "mainlogin";
    if (!Yii::$app->user->isGuest) {
      return $this->goHome();
    }
    $model = new TblUser();
    if ($model->load(Yii::$app->request->post())) {
      $password = md5(hash('sha256',$model->password));
      $checkUser=TblUser::find()->where(['username'=>$model->username])->andWhere(['password'=>$password])->andWhere(['status'=>'1'])->one();
      if(isset($checkUser) && $checkUser->id != ""){
        $session = Yii::$app->session;
        Yii::$app->session->set('tmp_userId', $checkUser->id);
        Yii::$app->session->set('tmp_username', $checkUser->username);
        return $this->redirect(['company-selection']);
      }else{
        Yii::$app->getSession()->setFlash('error', 'Username or password doesnot match. Please try again!! You have been traced!! Your IP is '.Yii::$app->getRequest()->getUserIP());
        return $this->render('login', ['model' => $model]);
      }
    }else{
      return $this->render('login', [
        'model' => $model,
      ]);
    }
    // $model = new LoginForm();
    // if ($model->load(Yii::$app->request->post()) && $model->login()) {
    //     return $this->goBack();
    // }
    //
    // $model->password = '';
    // return $this->render('login', [
    //     'model' => $model,
    // ]);
  }
  public function actionCompanySelection(){
    $this->layout = "mainlogin";
    if(Yii::$app->request->post('company_selection') != ""){
      //----add the selected company in session ----
      //---move the sessions variables from tmp to actual and redirect to index ----
      $session = Yii::$app->session;
      $selected_company = Yii::$app->request->post('company_selection') ;
      $tmp_username = $session->get('tmp_username');

      $tmp_id = $session->get('tmp_userId');

      $session->set('username', $tmp_username);
      $session->set('userId', $tmp_id);
      $session->set('userCompany', $selected_company);
      $session->remove('tmp_username');
      $session->remove('tmp_userId');
      return $this->redirect(['index']);
    }

    return $this->render('company-selection');
  }
  public function actionSwitchCompany(){
    $this->layout = "mainlogin";
    if(Yii::$app->request->post('company_selection') != ""){
      //----add the selected company in session ----
      //---move the sessions variables from tmp to actual and redirect to index ----
      $session = Yii::$app->session;
      $selected_company = Yii::$app->request->post('company_selection');
      $session->set('userCompany', $selected_company);
      return $this->redirect(['index']);
    }

    return $this->render('switch-company');
  }

  /**
  * Logout action.
  *
  * @return Response
  */
  public function actionLogout()
  {
    // Yii::$app->user->logout();
    // return $this->goHome();
    Yii::$app->session->remove('userId');
    Yii::$app->session->remove('username');
    Yii::$app->session->remove('userCompany');
    Yii::$app->session->remove('web_search_item');
    Yii::$app->session->remove('web_search_type');
    return $this->redirect(['site/login']);
  }

  //--------------custom functions start here -------------------------
  public function actionSigninattendance(){
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    $fk_user_id = $session->get('userId');
    // Current date and time
    $datetime = date("Y-m-d h:i A");
    // Convert datetime to Unix timestamp
    $timestamp = strtotime($datetime);
    // Subtract time from datetime
    $time = $timestamp;
    $date = date('Y-m-d',$time);
    // Date and time after subtraction
    $datetime = date("Y-m-d h:i A", $time);
    $time = date('h:i A',strtotime($datetime));
    $attendance = new TblAttendance();
    $attendance->in_time = $time;
    $attendance->date = $date;
    $attendance->fk_user_id = $fk_user_id;
    if($attendance->save()){
      return $this->redirect(['index']);
    }else{
      return $this->redirect(['index']);
    }
  }
  public function actionSignoutattendance(){
    $session = Yii::$app->session;
    if(!isset($session['username'])){
      Yii::$app->getSession()->setFlash('error', 'Please login first!! Your IP is '.Yii::$app->getRequest()->getUserIP());
      return $this->redirect(['site/login']);
    }
    $datetime = date("Y-m-d h:i A");
    // Convert datetime to Unix timestamp
    $timestamp = strtotime($datetime);
    // Subtract time from datetime
    // $time = $timestamp - (4 * 60 * 60);
    $time = $timestamp;
    // $time = $timestamp;
    // Date and time after subtraction
    $datetime = date("Y-m-d h:i A", $time);
    $time = date('h:i A',strtotime($datetime));
    $att_id = $_POST['att_id'];
    $attendance = TblAttendance::find()->where(['id'=>$att_id])->one();
    $attendance->out_time = $time;
    if($attendance->save()){
      return $this->redirect(['index']);
    }else{
      print_r($attendance->getErrors());
      // return $this->redirect('index.php?r=site/dashboard');
    }
  }
  /**
  * Displays contact page.
  *
  * @return Response|string
  */
  public function actionContact()
  {
    $model = new ContactForm();
    if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
      Yii::$app->session->setFlash('contactFormSubmitted');

      return $this->refresh();
    }
    return $this->render('contact', [
      'model' => $model,
    ]);
  }

  public function actionSearchByPart(){
    $part_name = $_POST['search'];
    // Extract only the numeric part by removing everything else
    $numeric_part = preg_replace('/[^0-9]/', '', $part_name);
    //=====3 levels of checking ========
    $return_str_product = "";
    $return_str_similar = "";
    $return_status = 0; //=========no part name matched ==============
    $return_count = 0;
    //======check the name against the part name in the products table =======
    $checkProduct = TblProduct::find()
                    ->where('status != 0')
                    ->andWhere("LOWER(REPLACE(REPLACE(REPLACE(REPLACE(name, ' ', ''), '-', ''), '.', ''), '_', '')) = LOWER(REPLACE(REPLACE(REPLACE(REPLACE('".$part_name."' , ' ', ''), '-', ''), '.', ''), '_', ''))")
                    ->all();
    if(isset($checkProduct) && count($checkProduct) > 0){
      $return_status++;
      $return_count = count($checkProduct);
      $i = 1;
      foreach($checkProduct as $cp){
        $query = (new Query())
            ->select([
                'product_id' => 'p.id',
                '(IFNULL((SELECT SUM(pr.no_of_items)
                          FROM tbl_product_receiving pr
                          WHERE pr.fk_product_id = p.id AND pr.status = 1), 0)
                 - IFNULL((SELECT SUM(it.quantity)
                           FROM tbl_invoice_item it
                           WHERE it.fk_product_id = p.id AND it.status = 1), 0)) as current_qty'
            ])
            ->from('tbl_product p')
            // ->leftJoin('tbl_product_receiving pr', 'p.id = pr.fk_product_id')
            // ->leftJoin('tbl_product_allotment pa', 'p.id = pa.fk_product_id')
            ->where(['p.id' => $cp->id])
            ->groupBy('p.id')
            ->one();
          $current_qty = isset($query['current_qty']) && $query['current_qty'] !== "" ? $query['current_qty'] : 0;
        $return_str_product .= '<a class="dropdown-item py-2 d-flex align-items-center" href="'.Url::to(['product/view','id'=>$cp->id]).'">
          <div class="flex-1">
            <h6 class="mb-0 text-body-highlight title">'.$cp->name.' | Qty in Stock: '.$current_qty.'</h6>
            <p class="fs-10 mb-0 d-flex text-body-tertiary">
            <span class="fw-medium text-body-tertiary text-opactity-85">'.$cp->description.'</span>
            </p>
          </div>
        </a>';
        $i++;
      }
    }//====if isset for products exists ended ===
    $checkProductSimilarFull = TblProduct::find()
                    ->where('status != 0')
                    ->andWhere("name LIKE '%".$part_name."%' or description LIKE '%".$part_name."%'")
                    ->all();
    if(isset($checkProductSimilarFull) && count($checkProductSimilarFull) > 0){
      $return_status++;
      $i = 1;
      $return_count += count($checkProductSimilarFull);
      foreach($checkProductSimilarFull as $cp){
        $query = (new Query())
            ->select([
                'product_id' => 'p.id',
                '(IFNULL((SELECT SUM(pr.no_of_items)
                          FROM tbl_product_receiving pr
                          WHERE pr.fk_product_id = p.id AND pr.status = 1), 0)
                 - IFNULL((SELECT SUM(it.quantity)
                           FROM tbl_invoice_item it
                           WHERE it.fk_product_id = p.id AND it.status = 1), 0)) as current_qty'
            ])
            ->from('tbl_product p')
            // ->leftJoin('tbl_product_receiving pr', 'p.id = pr.fk_product_id')
            // ->leftJoin('tbl_product_allotment pa', 'p.id = pa.fk_product_id')
            ->where(['p.id' => $cp->id])
            ->groupBy('p.id')
            ->one();
          $current_qty = isset($query['current_qty']) && $query['current_qty'] !== "" ? $query['current_qty'] : 0;

        $return_str_product .= '<a class="dropdown-item py-2 d-flex align-items-center" href="'.Url::to(['product/view','id'=>$cp->id]).'">
          <div class="flex-1">
            <h6 class="mb-0 text-body-highlight title">'.$cp->name.' | Qty in Stock: '.$current_qty.'</h6>
            <p class="fs-10 mb-0 d-flex text-body-tertiary"><span class="fw-medium text-body-tertiary text-opactity-85">'.$cp->description.'</span></p>
          </div>
        </a>';
        $i++;
      }
    }//====if isset for products exists ended ===
    //========check for cross ref started ========
    $checkCrossRef = TblProductCrossRef::find()->where('status != 0')
                      ->andWhere("cross_ref LIKE '%".$part_name."%'")
                      ->all();
    if(isset($checkCrossRef) && count($checkCrossRef) > 0){
      //----exists in cross ref -----
      foreach($checkCrossRef as $cc){
        //----product id -----
        $product_id = $cc->fk_product_id;
        $query = (new Query())
            ->select([
                'product_id' => 'p.id',
                '(IFNULL((SELECT SUM(pr.no_of_items)
                          FROM tbl_product_receiving pr
                          WHERE pr.fk_product_id = p.id AND pr.status = 1), 0)
                 - IFNULL((SELECT SUM(it.quantity)
                           FROM tbl_invoice_item it
                           WHERE it.fk_product_id = p.id AND it.status = 1), 0)) as current_qty'
            ])
            ->from('tbl_product p')
            // ->leftJoin('tbl_product_receiving pr', 'p.id = pr.fk_product_id')
            // ->leftJoin('tbl_product_allotment pa', 'p.id = pa.fk_product_id')
            ->where(['p.id' => $product_id])
            ->groupBy('p.id')
            ->one();
          $current_qty = isset($query['current_qty']) && $query['current_qty'] !== "" ? $query['current_qty'] : 0;
          $getproduct = TblProduct::find()->where(['id'=>$cc->fk_product_id,'status'=>1])->one();
          if(isset($getproduct) && $getproduct->id != ""){
            $return_str_product .= '<a class="dropdown-item py-2 d-flex align-items-center" href="'.Url::to(['product/view','id'=>$cc->fk_product_id]).'">
              <div class="flex-1">
                <h6 class="mb-0 text-body-highlight title">'.$getproduct->name.' | Qty in Stock: '.$current_qty.'</h6>
                <p class="fs-10 mb-0 d-flex text-body-tertiary"><span class="fw-medium text-body-tertiary text-opactity-85">'.$getproduct->description.'</span></p>
                <p class="fs-10 mb-0 d-flex text-body-tertiary"><span class="fw-medium text-body-tertiary text-opactity-85">'.$cc->cross_ref.'</span></p>
              </div>
            </a>';
          }

      }//-------for loop ended ----------
    }//-------if isset ended ---------------
    //========check for cross ref ended ========
    if($numeric_part != ""){
      $checkProductSimilar = TblProduct::find()
                      ->where('status != 0')
                      ->andWhere("name LIKE '%".$numeric_part."%'")
                      ->all();
      if(isset($checkProductSimilar) && count($checkProductSimilar) > 0){
        $return_status++;
        $i = 1;
        $return_count += count($checkProductSimilar);
        foreach($checkProductSimilar as $cp){
          $query = (new Query())
              ->select([
                  'product_id' => 'p.id',
                  '(IFNULL((SELECT SUM(pr.no_of_items)
                            FROM tbl_product_receiving pr
                            WHERE pr.fk_product_id = p.id AND pr.status = 1), 0)
                   - IFNULL((SELECT SUM(it.quantity)
                             FROM tbl_invoice_item it
                             WHERE it.fk_product_id = p.id AND it.status = 1), 0)) as current_qty'
              ])
              ->from('tbl_product p')
              // ->leftJoin('tbl_product_receiving pr', 'p.id = pr.fk_product_id')
              // ->leftJoin('tbl_product_allotment pa', 'p.id = pa.fk_product_id')
              ->where(['p.id' => $cp->id])
              ->groupBy('p.id')
              ->one();
            $current_qty = isset($query['current_qty']) && $query['current_qty'] !== "" ? $query['current_qty'] : 0;

          $return_str_product .= '<a class="dropdown-item py-2 d-flex align-items-center" href="'.Url::to(['product/view','id'=>$cp->id]).'">
            <div class="flex-1">
              <h6 class="mb-0 text-body-highlight title">'.$cp->name.' | Qty in Stock: '.$current_qty.'</h6>
              <p class="fs-10 mb-0 d-flex text-body-tertiary"><span class="fw-medium text-body-tertiary text-opactity-85">'.$cp->description.'</span></p>
            </div>
          </a>';
          $i++;
        }
      }//====if isset for products exists ended ===
    }

    $array = array("return_count"=>$return_count,"return_str"=>$return_str_product);
    // return $return_str_product;
    return json_encode($array);


  }//------public function ended --------------

  public function actionSearchByPartCopy(){
    $part_name = $_POST['search'];
    $search_type = $_POST['search_type'];

    $session = Yii::$app->session;
    Yii::$app->session->set('web_search_item', $part_name);
    Yii::$app->session->set('web_search_type', $search_type);
    // Extract only the numeric part by removing everything else
    $numeric_part = preg_replace('/[^0-9]/', '', $part_name);
    $return_str_product = "";
    $return_count = 0;
    if($search_type == 1){
      //---by part name -----
      $return_status = 0; //=========no part name matched ==============
      //======check the name against the part name in the products table =======
      $checkProduct = TblProduct::find()
                      ->where('status != 0')
                      ->andWhere("LOWER(REPLACE(REPLACE(REPLACE(REPLACE(name, ' ', ''), '-', ''), '.', ''), '_', '')) = LOWER(REPLACE(REPLACE(REPLACE(REPLACE('".$part_name."' , ' ', ''), '-', ''), '.', ''), '_', ''))")
                      ->all();
      if(isset($checkProduct) && count($checkProduct) > 0){
        $return_status++;
        $return_count = count($checkProduct);
        $i = 1;
        foreach($checkProduct as $cp){
          $query = (new Query())
              ->select([
                  'product_id' => 'p.id',
                  '(IFNULL((SELECT SUM(pr.no_of_items)
                            FROM tbl_product_receiving pr
                            WHERE pr.fk_product_id = p.id AND pr.status = 1), 0)
                   - IFNULL((SELECT SUM(it.quantity)
                             FROM tbl_invoice_item it
                             WHERE it.fk_product_id = p.id AND it.status = 1), 0)) as current_qty'
              ])
              ->from('tbl_product p')
              // ->leftJoin('tbl_product_receiving pr', 'p.id = pr.fk_product_id')
              // ->leftJoin('tbl_product_allotment pa', 'p.id = pa.fk_product_id')
              ->where(['p.id' => $cp->id])
              ->groupBy('p.id')
              ->one();
            $current_qty = isset($query['current_qty']) && $query['current_qty'] !== "" ? $query['current_qty'] : 0;
          $return_str_product .= '<a class="dropdown-item py-2 d-flex align-items-center" href="'.Url::to(['product/view','id'=>$cp->id]).'">
            <div class="flex-1">
              <h6 class="mb-0 text-body-highlight title">'.$cp->name.' | Qty in Stock: '.$current_qty.'</h6>
              <p class="fs-10 mb-0 d-flex text-body-tertiary">
              <span class="fw-medium text-body-tertiary text-opactity-85">'.$cp->description.'</span>
              </p>
            </div>
          </a>';
          $i++;
        }
      }//====if isset for products exists ended ===
      if($return_status == 0){
        //---no product matched -----
        $checkProductSimilarFull = TblProduct::find()
                        ->where('status != 0')
                        ->andWhere("name LIKE '%".$part_name."%'")
                        ->all();
        if(isset($checkProductSimilarFull) && count($checkProductSimilarFull) > 0){
          $return_status++;
          $i = 1;
          $return_count += count($checkProductSimilarFull);
          foreach($checkProductSimilarFull as $cp){
            $query = (new Query())
                ->select([
                    'product_id' => 'p.id',
                    '(IFNULL((SELECT SUM(pr.no_of_items)
                              FROM tbl_product_receiving pr
                              WHERE pr.fk_product_id = p.id AND pr.status = 1), 0)
                     - IFNULL((SELECT SUM(it.quantity)
                               FROM tbl_invoice_item it
                               WHERE it.fk_product_id = p.id AND it.status = 1), 0)) as current_qty'
                ])
                ->from('tbl_product p')
                // ->leftJoin('tbl_product_receiving pr', 'p.id = pr.fk_product_id')
                // ->leftJoin('tbl_product_allotment pa', 'p.id = pa.fk_product_id')
                ->where(['p.id' => $cp->id])
                ->groupBy('p.id')
                ->one();
              $current_qty = isset($query['current_qty']) && $query['current_qty'] !== "" ? $query['current_qty'] : 0;

            $return_str_product .= '<a class="dropdown-item py-2 d-flex align-items-center" href="'.Url::to(['product/view','id'=>$cp->id]).'">
              <div class="flex-1">
                <h6 class="mb-0 text-body-highlight title">'.$cp->name.' | Qty in Stock: '.$current_qty.'</h6>
                <p class="fs-10 mb-0 d-flex text-body-tertiary"><span class="fw-medium text-body-tertiary text-opactity-85">'.$cp->description.'</span></p>
              </div>
            </a>';
            $i++;
          }
        }//====if isset for products exists ended ===
      }

      if($numeric_part != "" && $return_status == 0){
        $checkProductSimilar = TblProduct::find()
                        ->where('status != 0')
                        ->andWhere("name LIKE '%".$numeric_part."%'")
                        ->all();
        if(isset($checkProductSimilar) && count($checkProductSimilar) > 0){
          $return_status++;
          $i = 1;
          $return_count += count($checkProductSimilar);
          foreach($checkProductSimilar as $cp){
            $query = (new Query())
                ->select([
                    'product_id' => 'p.id',
                    '(IFNULL((SELECT SUM(pr.no_of_items)
                              FROM tbl_product_receiving pr
                              WHERE pr.fk_product_id = p.id AND pr.status = 1), 0)
                     - IFNULL((SELECT SUM(it.quantity)
                               FROM tbl_invoice_item it
                               WHERE it.fk_product_id = p.id AND it.status = 1), 0)) as current_qty'
                ])
                ->from('tbl_product p')
                // ->leftJoin('tbl_product_receiving pr', 'p.id = pr.fk_product_id')
                // ->leftJoin('tbl_product_allotment pa', 'p.id = pa.fk_product_id')
                ->where(['p.id' => $cp->id])
                ->groupBy('p.id')
                ->one();
              $current_qty = isset($query['current_qty']) && $query['current_qty'] !== "" ? $query['current_qty'] : 0;

            $return_str_product .= '<a class="dropdown-item py-2 d-flex align-items-center" href="'.Url::to(['product/view','id'=>$cp->id]).'">
              <div class="flex-1">
                <h6 class="mb-0 text-body-highlight title">'.$cp->name.' | Qty in Stock: '.$current_qty.'</h6>
                <p class="fs-10 mb-0 d-flex text-body-tertiary"><span class="fw-medium text-body-tertiary text-opactity-85">'.$cp->description.'</span></p>
              </div>
            </a>';
            $i++;
          }
        }//====if isset for products exists ended ===
      }
    }else if($search_type == 2){
        //----cross ref ----

          //========check for cross ref started ========
          $checkCrossRef = TblProductCrossRef::find()->where('status != 0')
                            ->andWhere("cross_ref LIKE '%".$part_name."%'")
                            ->all();
          if(isset($checkCrossRef) && count($checkCrossRef) > 0){
            //----exists in cross ref -----
            $return_count += count($checkCrossRef);
            foreach($checkCrossRef as $cc){
              //----product id -----
              $product_id = $cc->fk_product_id;
              $query = (new Query())
                  ->select([
                      'product_id' => 'p.id',
                      '(IFNULL((SELECT SUM(pr.no_of_items)
                                FROM tbl_product_receiving pr
                                WHERE pr.fk_product_id = p.id AND pr.status = 1), 0)
                       - IFNULL((SELECT SUM(it.quantity)
                                 FROM tbl_invoice_item it
                                 WHERE it.fk_product_id = p.id AND it.status = 1), 0)) as current_qty'
                  ])
                  ->from('tbl_product p')
                  // ->leftJoin('tbl_product_receiving pr', 'p.id = pr.fk_product_id')
                  // ->leftJoin('tbl_product_allotment pa', 'p.id = pa.fk_product_id')
                  ->where(['p.id' => $product_id])
                  ->groupBy('p.id')
                  ->one();
                $current_qty = isset($query['current_qty']) && $query['current_qty'] !== "" ? $query['current_qty'] : 0;
                $getproduct = TblProduct::find()->where(['id'=>$cc->fk_product_id,'status'=>1])->one();
                if(isset($getproduct) && $getproduct->id != ""){
                  $return_str_product .= '<a class="dropdown-item py-2 d-flex align-items-center" href="'.Url::to(['product/view','id'=>$cc->fk_product_id]).'">
                    <div class="flex-1">
                      <h6 class="mb-0 text-body-highlight title">'.$getproduct->name.' | Qty in Stock: '.$current_qty.'</h6>
                      <p class="fs-10 mb-0 d-flex text-body-tertiary"><span class="fw-medium text-body-tertiary text-opactity-85">'.$getproduct->description.'</span></p>
                      <p class="fs-10 mb-0 d-flex text-body-tertiary"><span class="fw-medium text-body-tertiary text-opactity-85">'.$cc->cross_ref.'</span></p>
                    </div>
                  </a>';
                }

            }//-------for loop ended ----------
          }//-------if isset ended ---------------
          //========check for cross ref ended ========

    }else if($search_type == 3){
        //----description ----
        $checkProductSimilarFull = TblProduct::find()
                        ->where('status != 0')
                        ->andWhere("description LIKE '%".$part_name."%'")
                        ->all();
        if(isset($checkProductSimilarFull) && count($checkProductSimilarFull) > 0){

          $i = 1;
          $return_count += count($checkProductSimilarFull);
          foreach($checkProductSimilarFull as $cp){
            $query = (new Query())
                ->select([
                    'product_id' => 'p.id',
                    '(IFNULL((SELECT SUM(pr.no_of_items)
                              FROM tbl_product_receiving pr
                              WHERE pr.fk_product_id = p.id AND pr.status = 1), 0)
                     - IFNULL((SELECT SUM(it.quantity)
                               FROM tbl_invoice_item it
                               WHERE it.fk_product_id = p.id AND it.status = 1), 0)) as current_qty'
                ])
                ->from('tbl_product p')
                // ->leftJoin('tbl_product_receiving pr', 'p.id = pr.fk_product_id')
                // ->leftJoin('tbl_product_allotment pa', 'p.id = pa.fk_product_id')
                ->where(['p.id' => $cp->id])
                ->groupBy('p.id')
                ->one();
              $current_qty = isset($query['current_qty']) && $query['current_qty'] !== "" ? $query['current_qty'] : 0;

            $return_str_product .= '<a class="dropdown-item py-2 d-flex align-items-center" href="'.Url::to(['product/view','id'=>$cp->id]).'">
              <div class="flex-1">
                <h6 class="mb-0 text-body-highlight title">'.$cp->name.' | Qty in Stock: '.$current_qty.'</h6>
                <p class="fs-10 mb-0 d-flex text-body-tertiary"><span class="fw-medium text-body-tertiary text-opactity-85">'.$cp->description.'</span></p>
              </div>
            </a>';
            $i++;
          }
        }//====if isset for products exists ended ===
    }else{
      //---by name only ------
      $return_status = 0; //=========no part name matched ==============
      //======check the name against the part name in the products table =======
      $checkProduct = TblProduct::find()
                      ->where('status != 0')
                      ->andWhere("LOWER(REPLACE(REPLACE(REPLACE(REPLACE(name, ' ', ''), '-', ''), '.', ''), '_', '')) = LOWER(REPLACE(REPLACE(REPLACE(REPLACE('".$part_name."' , ' ', ''), '-', ''), '.', ''), '_', ''))")
                      ->all();
      if(isset($checkProduct) && count($checkProduct) > 0){
        $return_status++;
        $return_count = count($checkProduct);
        $i = 1;
        foreach($checkProduct as $cp){
          $query = (new Query())
              ->select([
                  'product_id' => 'p.id',
                  '(IFNULL((SELECT SUM(pr.no_of_items)
                            FROM tbl_product_receiving pr
                            WHERE pr.fk_product_id = p.id AND pr.status = 1), 0)
                   - IFNULL((SELECT SUM(it.quantity)
                             FROM tbl_invoice_item it
                             WHERE it.fk_product_id = p.id AND it.status = 1), 0)) as current_qty'
              ])
              ->from('tbl_product p')
              // ->leftJoin('tbl_product_receiving pr', 'p.id = pr.fk_product_id')
              // ->leftJoin('tbl_product_allotment pa', 'p.id = pa.fk_product_id')
              ->where(['p.id' => $cp->id])
              ->groupBy('p.id')
              ->one();
            $current_qty = isset($query['current_qty']) && $query['current_qty'] !== "" ? $query['current_qty'] : 0;
          $return_str_product .= '<a class="dropdown-item py-2 d-flex align-items-center" href="'.Url::to(['product/view','id'=>$cp->id]).'">
            <div class="flex-1">
              <h6 class="mb-0 text-body-highlight title">'.$cp->name.' | Qty in Stock: '.$current_qty.'</h6>
              <p class="fs-10 mb-0 d-flex text-body-tertiary">
              <span class="fw-medium text-body-tertiary text-opactity-85">'.$cp->description.'</span>
              </p>
            </div>
          </a>';
          $i++;
        }
      }//====if isset for products exists ended ===
      $checkProductSimilarFull = TblProduct::find()
                      ->where('status != 0')
                      ->andWhere("name LIKE '%".$part_name."%'")
                      ->all();
      if(isset($checkProductSimilarFull) && count($checkProductSimilarFull) > 0){
        $return_status++;
        $i = 1;
        $return_count += count($checkProductSimilarFull);
        foreach($checkProductSimilarFull as $cp){
          $query = (new Query())
              ->select([
                  'product_id' => 'p.id',
                  '(IFNULL((SELECT SUM(pr.no_of_items)
                            FROM tbl_product_receiving pr
                            WHERE pr.fk_product_id = p.id AND pr.status = 1), 0)
                   - IFNULL((SELECT SUM(it.quantity)
                             FROM tbl_invoice_item it
                             WHERE it.fk_product_id = p.id AND it.status = 1), 0)) as current_qty'
              ])
              ->from('tbl_product p')
              // ->leftJoin('tbl_product_receiving pr', 'p.id = pr.fk_product_id')
              // ->leftJoin('tbl_product_allotment pa', 'p.id = pa.fk_product_id')
              ->where(['p.id' => $cp->id])
              ->groupBy('p.id')
              ->one();
            $current_qty = isset($query['current_qty']) && $query['current_qty'] !== "" ? $query['current_qty'] : 0;

          $return_str_product .= '<a class="dropdown-item py-2 d-flex align-items-center" href="'.Url::to(['product/view','id'=>$cp->id]).'">
            <div class="flex-1">
              <h6 class="mb-0 text-body-highlight title">'.$cp->name.' | Qty in Stock: '.$current_qty.'</h6>
              <p class="fs-10 mb-0 d-flex text-body-tertiary"><span class="fw-medium text-body-tertiary text-opactity-85">'.$cp->description.'</span></p>
            </div>
          </a>';
          $i++;
        }
      }//====if isset for products exists ended ===
      if($numeric_part != ""){
        $checkProductSimilar = TblProduct::find()
                        ->where('status != 0')
                        ->andWhere("name LIKE '%".$numeric_part."%'")
                        ->all();
        if(isset($checkProductSimilar) && count($checkProductSimilar) > 0){
          $return_status++;
          $i = 1;
          $return_count += count($checkProductSimilar);
          foreach($checkProductSimilar as $cp){
            $query = (new Query())
                ->select([
                    'product_id' => 'p.id',
                    '(IFNULL((SELECT SUM(pr.no_of_items)
                              FROM tbl_product_receiving pr
                              WHERE pr.fk_product_id = p.id AND pr.status = 1), 0)
                     - IFNULL((SELECT SUM(it.quantity)
                               FROM tbl_invoice_item it
                               WHERE it.fk_product_id = p.id AND it.status = 1), 0)) as current_qty'
                ])
                ->from('tbl_product p')
                // ->leftJoin('tbl_product_receiving pr', 'p.id = pr.fk_product_id')
                // ->leftJoin('tbl_product_allotment pa', 'p.id = pa.fk_product_id')
                ->where(['p.id' => $cp->id])
                ->groupBy('p.id')
                ->one();
              $current_qty = isset($query['current_qty']) && $query['current_qty'] !== "" ? $query['current_qty'] : 0;

            $return_str_product .= '<a class="dropdown-item py-2 d-flex align-items-center" href="'.Url::to(['product/view','id'=>$cp->id]).'">
              <div class="flex-1">
                <h6 class="mb-0 text-body-highlight title">'.$cp->name.' | Qty in Stock: '.$current_qty.'</h6>
                <p class="fs-10 mb-0 d-flex text-body-tertiary"><span class="fw-medium text-body-tertiary text-opactity-85">'.$cp->description.'</span></p>
              </div>
            </a>';
            $i++;
          }
        }//====if isset for products exists ended ===
      }

    }//----else ended ------


    $array = array("return_count"=>$return_count,"return_str"=>$return_str_product);
    // return $return_str_product;
    return json_encode($array);


  }//------public function ended --------------

  public function actionSearchByPartNew()
{
  Yii::$app->response->format = Response::FORMAT_JSON;

      $request     = Yii::$app->request;
      $part_name   = trim((string)$request->post('search', ''));
      $search_type = (int)$request->post('search_type', 1);

      if (!in_array($search_type, [1, 2, 3], true)) {
          $search_type = 1;
      }

      // Store last input
      Yii::$app->session->set('web_search_item', $part_name);
      Yii::$app->session->set('web_search_type', $search_type);



      $numeric_part = preg_replace('/[^0-9]/', '', $part_name);

      $return_str_product = '';
      $addedProductIds = [];

      // ---------------------------------------------------------------------
      // HELPERS
      // ---------------------------------------------------------------------

      // Get current qty
      $getCurrentQty = function ($productId) {
      $user_company = Yii::$app->session->get('userCompany');

      $row = (new Query())
          ->select([
              'current_qty' => new Expression("
                  IFNULL(
                      (
                          SELECT SUM(pr.no_of_items)
                          FROM tbl_product_receiving pr
                          WHERE pr.fk_product_id = p.id
                          AND pr.status = 1
                          AND pr.fk_location_id = :company
                      ),
                      0
                  )
                  -
                  IFNULL(
                      (
                          SELECT SUM(it.quantity)
                          FROM tbl_invoice_item it
                          WHERE it.fk_product_id = p.id
                          AND it.status = 1
                          AND it.fk_invoice_id IN (
                              SELECT id FROM tbl_invoice
                              WHERE status = 1
                              AND fk_bill_from_id = :company
                          )
                      ),
                      0
                  )
              ")
          ])
          ->from('tbl_product p')
          ->where(['p.id' => $productId])
          ->addParams([':company' => $user_company])
          ->one();

      return isset($row['current_qty']) ? $row['current_qty'] : 0;
  };


      // Highlight matched text
      $highlight = function ($text, $search) {
          if (!$search || trim($search) === '') return Html::encode($text);

          return preg_replace(
              '/' . preg_quote($search, '/') . '/i',
              '<span class="text-warning fw-bold">$0</span>',
              Html::encode($text)
          );
      };

      // Build HTML for product
      $buildProductHtml = function ($product, $currentQty, $highlightFn, $searchTerm, $extra = null) {
          $name        = $highlightFn($product->name, $searchTerm);
          $description = $highlightFn($product->description, $searchTerm);
          $qtyLabel    = "Qty in Stock: " . $currentQty;
          $url         = Url::to(['product/view', 'id' => $product->id]);

          $html  = '<a class="dropdown-item py-2 d-flex align-items-center" href="'.$url.'">';
          $html .= '  <div class="flex-1">';
          $html .= '    <h6 class="mb-0 text-body-highlight title">'.$name.' | '.$qtyLabel.'</h6>';
          $html .= '    <p class="fs-10 mb-0 d-flex text-body-tertiary"><span>'.$description.'</span></p>';

          if ($extra) {
              $html .= ' <p class="fs-10 mb-0 d-flex text-body-tertiary"><span>'.$highlightFn($extra, $searchTerm).'</span></p>';
          }

          $html .= '  </div>';
          $html .= '</a>';

          return $html;
      };

      // ---------------------------------------------------------------------
      // CASE 1 — ITEM NAME SEARCH (Hybrid, sorted, highlighted)
      // ---------------------------------------------------------------------
      if ($search_type === 1) {

          // 1) EXACT-NORMALIZED MATCH
          $exactProducts = TblProduct::find()
              ->where(['!=', 'status', 0])
              ->andWhere(
                  "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(name,' ',''),'-',''),'.',''),'_',''))
                   =
                   LOWER(REPLACE(REPLACE(REPLACE(REPLACE(:p,' ',''),'-',''),'.',''),'_',''))",
                  [':p' => $part_name]
              )
              ->limit(50)
              ->all();

          // 2) LIKE full term
          $likeProducts = TblProduct::find()
              ->where(['!=', 'status', 0])
              ->andWhere(['like', 'name', $part_name])
              ->limit(50)
              ->all();

          // 3) LIKE numeric part
          $numericProducts = [];
          if ($numeric_part !== '' && $numeric_part !== $part_name) {
              $numericProducts = TblProduct::find()
                  ->where(['!=', 'status', 0])
                  ->andWhere(['like', 'name', $numeric_part])
                  ->limit(50)
                  ->all();
          }

          // Sorting priority:
          // 1 = exact match
          // 2 = LIKE on full term
          // 3 = LIKE on numeric term
          $sortedList = [];

          foreach ($exactProducts as $p)   $sortedList[] = ['p' => $p, 'rank' => 1];
          foreach ($likeProducts as $p)    $sortedList[] = ['p' => $p, 'rank' => 2];
          foreach ($numericProducts as $p) $sortedList[] = ['p' => $p, 'rank' => 3];

          // Use product ID + rank to ensure stable ordered results
          usort($sortedList, function ($a, $b) {
              if ($a['rank'] === $b['rank']) {
                  return $a['p']->id <=> $b['p']->id; // secondary
              }
              return $a['rank'] <=> $b['rank'];
          });

          // Build HTML
          foreach ($sortedList as $row) {
              $product = $row['p'];
              if (in_array($product->id, $addedProductIds, true)) continue;

              $currentQty = $getCurrentQty($product->id);
              $return_str_product .= $buildProductHtml($product, $currentQty, $highlight, $part_name);

              $addedProductIds[] = $product->id;
          }
      }

      // ---------------------------------------------------------------------
      // CASE 2 — CROSS REF SEARCH
      // ---------------------------------------------------------------------
      elseif ($search_type === 2) {

          $crosses = TblProductCrossRef::find()
              ->where(['!=', 'status', 0])
              ->andWhere(['like', 'cross_ref', $part_name])
              ->limit(50)
              ->all();

          foreach ($crosses as $cr) {
              $product = TblProduct::findOne(['id' => $cr->fk_product_id, 'status' => 1]);
              if (!$product) continue;

              if (in_array($product->id, $addedProductIds, true)) continue;

              $currentQty = $getCurrentQty($product->id);

              $return_str_product .= $buildProductHtml(
                  $product,
                  $currentQty,
                  $highlight,
                  $part_name,
                  $cr->cross_ref
              );

              $addedProductIds[] = $product->id;
          }
      }

      // ---------------------------------------------------------------------
      // CASE 3 — DESCRIPTION SEARCH
      // ---------------------------------------------------------------------
      elseif ($search_type === 3) {

          $products = TblProduct::find()
              ->where(['!=', 'status', 0])
              ->andWhere(['like', 'description', $part_name])
              ->limit(50)
              ->all();

          foreach ($products as $p) {
              if (in_array($p->id, $addedProductIds, true)) continue;

              $currentQty = $getCurrentQty($p->id);

              $return_str_product .= $buildProductHtml($p, $currentQty, $highlight, $part_name);

              $addedProductIds[] = $p->id;
          }
      }

      // FINAL RESPONSE
      return [
          'return_count' => count($addedProductIds),
          'return_str'   => $return_str_product
      ];
}
  /**
  * Displays about page.
  *
  * @return string
  */
  public function actionAbout()
  {
    return $this->render('about');
  }
  public function actionDashboard(){
    return $this->render('dashboard');
  }
  public function actionReport(){
    return $this->render('report');
  }
  //---------item category----------
  public function actionItemCategoryList(){
    return $this->render('item-category-list');
  }
  public function actionItemCategoryCreate(){
    return $this->render('item-category-create');
  }
  //---------item creation----------
  public function actionItemCreationList(){
    return $this->render('item-creation-list');
  }
  public function actionItemCreationCreate(){
    return $this->render('item-creation-create');
  }
  //---------item receiving----------
  public function actionItemReceivingList(){
    return $this->render('item-receiving-list');
  }
  public function actionItemReceivingCreate(){
    return $this->render('item-receiving-create');
  }
  //---------item allotment/shipping----------
  public function actionItemAllotmentList(){
    return $this->render('item-allotment-list');
  }
  public function actionItemAllotmentCreate(){
    return $this->render('item-allotment-create');
  }
  //---------vendors----------
  public function actionVendorList(){
    return $this->render('vendor-list');
  }
  public function actionVendorCreate(){
    return $this->render('vendor-create');
  }
  //---------customers----------
  public function actionCustomerList(){
    return $this->render('customer-list');
  }
  public function actionCustomerCreate(){
    return $this->render('customer-create');
  }
  //---------customer invoicing----------
  public function actionInvoiceList(){
    return $this->render('invoice-list');
  }
  public function actionInvoiceCreate(){
    return $this->render('invoice-create');
  }
  //---------AR----------
  public function actionAccountReceivableList(){
    return $this->render('account-receivable-list');
  }
  public function actionAccountReceivableCreate(){
    return $this->render('account-receivable-create');
  }
  //---------purchase order----------
  public function actionPurchaseOrderList(){
    return $this->render('purchase-order-list');
  }
  public function actionPurchaseOrderCreate(){
    return $this->render('purchase-order-create');
  }
  //---------purchase invoice----------
  public function actionPurchaseInvoiceList(){
    return $this->render('purchase-invoice-list');
  }
  public function actionPurchaseInvoiceCreate(){
    return $this->render('purchase-invoice-create');
  }
  //---------accounts payable ----------
  public function actionAccountsPayableList(){
    return $this->render('accounts-payable-list');
  }
  public function actionAccountsPayableCreate(){
    return $this->render('accounts-payable-create');
  }
  //----------user management-------------
  public function actionUserList(){
    return $this->render('user-list');
  }
  public function actionUserCreate(){
    return $this->render('user-create');
  }
  public function actionUpdatePassword(){
    return $this->render('update-password');
  }
  public function actionPrintLabel()
  {
    return $this->render('print-label');
  }
  public function actionPrintBarcodes()
  {
    return $this->render('print-barcodes');
  }
  public function actionScanBarcode()
  {
    $this->layout = "maincamera";
    return $this->render('scan-barcode');
  }
  public function actionPrintLabelV2()
  {
    return $this->render('print-label-v2');
  }
  public function actionRemoveLabelsFromList(){
    $labels = $_POST['labels'];
    for($i=0;$i<count($labels);$i++){
      $check = TblPrintedLabels::find()->where(['fk_bin_locations_id'=>$labels[$i],'status'=>1])->all();
      if(isset($check) && count($check) > 0){

      }else{
        $modelCreate = new TblPrintedLabels();
        $modelCreate->fk_bin_locations_id = $labels[$i];
        $modelCreate->save();
      }

    }
    // return json_encode($labels);
    return "success";
  }
  public function actionRemoveBarcodesFromList(){
    $labels = $_POST['labels'];
    for($i=0;$i<count($labels);$i++){
      $check = TblPrintedBarcodes::find()->where(['fk_product_id'=>$labels[$i],'status'=>1])->all();
      if(isset($check) && count($check) > 0){

      }else{
        $modelCreate = new TblPrintedBarcodes();
        $modelCreate->fk_product_id = $labels[$i];
        $modelCreate->save();
      }

    }
    // return json_encode($labels);
    return "success";

  }
  public function actionGetBarcodes(){
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    //-----get products --------
    $allbins = TblProduct::find()->where(['status'=>1])->andWhere('id not in (select fk_product_id from tbl_printed_barcodes WHERE status = 1)')->all();

    $units = [];
    if(isset($allbins) && count($allbins) > 0){
      foreach($allbins as $gu){
        $units[] = ['id'=>$gu->id,'name'=>$gu->name.' - '.$gu->sku.' - '.$gu->internal_sku];
      }//--for loop ended----
    }//----if isset ended------
    // Convert to JSON format with 'value' and 'label'
    $formattedBrands = array_map(function($brand) {
        return ['value' => $brand['id'], 'label' => $brand['name']];
    }, $units);

    // Return JSON response
    // return $formattedBrands;
    return json_encode($formattedBrands);
  }//-----func ended -------
  public function actionGetLabels(){
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    //-----get products --------
    $allbins = TblBinLocations::find()->where(['status'=>1])->andWhere('id not in (select fk_bin_locations_id from tbl_printed_labels WHERE status = 1)')->all();

    $units = [];
    if(isset($allbins) && count($allbins) > 0){
      foreach($allbins as $gu){
        $units[] = ['id'=>$gu->id,'name'=>$gu->area.' - '.$gu->row.' - '.$gu->bay.' - '.$gu->level.' - '.$gu->position];
      }//--for loop ended----
    }//----if isset ended------
    // Convert to JSON format with 'value' and 'label'
    $formattedBrands = array_map(function($brand) {
        return ['value' => $brand['id'], 'label' => $brand['name']];
    }, $units);

    // Return JSON response
    // return $formattedBrands;
    return json_encode($formattedBrands);
  }//-----func ended -------
  public function actionGetCsrfToken()
  {
      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      return [
          'csrfToken' => Yii::$app->request->getCsrfToken(),
      ];
  }
  public function actionSendemail(){
    $emailBody = "Test email";

    $sender_name = "HK Trailer Parts";
    $sender_email = "hktrailerpartsit@gmail.com";
    $rec_name = "B4B Consulting";
    $rec_email = "service@b4b.consulting";
    // $rec_name = Yii::$app->params['rec_name'];
    // $rec_email = Yii::$app->params['rec_email'];
    $subject = "Invoice Issued from HK Trailer Parts";
    $layout = Yii::$app->mailer->htmlLayout = "layouts/html";
    try{
      if(Yii::$app->mailer->compose($layout, ['content' => $emailBody])
      ->setFrom($sender_email)
      ->setTo($rec_email)
      ->setSubject($subject)
      ->send()){
        // Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
        echo "email sent";
      }else{
        // Yii::$app->session->setFlash('error', 'There was an error sending your message.');
        echo "error sending email";
      }
    }catch(\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e){
      // Yii::$app->session->setFlash('error', 'There was an error sending your message.');
      print_r($e);
    }
  }
  public function actionGetChartData()
  {
      $user_company = Yii::$app->session->get('userCompany');
      // Get data for the past 12 months
      $currentMonth = (int) date('m');
      $currentYear = (int) date('Y');
      $months = [];
      for ($i = 11; $i >= 0; $i--) {
          $timestamp = mktime(0, 0, 0, $currentMonth - $i, 1, $currentYear);
          $months[] = date('F', $timestamp);
      }

      // Get invoice amounts for the past 12 months
      $invoiceAmounts = (new Query())
          ->select(['SUM(total_amount) AS total'])
          ->from('tbl_invoice')
          ->where(['>=', 'invoice_date', new Expression('DATE_SUB(CURDATE(), INTERVAL 12 MONTH)')])
          ->andWhere('status != 0')
          ->andWhere(['fk_bill_from_id'=>$user_company])
          ->groupBy(new Expression('YEAR(invoice_date), MONTH(invoice_date)'))
          ->orderBy(new Expression('YEAR(invoice_date), MONTH(invoice_date)'))
          ->column();

      // Get account receivables for the past 12 months
      $accountReceivables = (new Query())
          ->select(['SUM(amount_received) AS total'])
          ->from('tbl_account_receivable')
          ->where(['>=', 'ar_date', new Expression('DATE_SUB(CURDATE(), INTERVAL 12 MONTH)')])
          ->andWhere('status != 0')
          ->andWhere(['fk_bill_from_id'=>$user_company])
          ->groupBy(new Expression('YEAR(ar_date), MONTH(ar_date)'))
          ->orderBy(new Expression('YEAR(ar_date), MONTH(ar_date)'))
          ->column();

      // return $this->asJson([
      //     'months' => $months,
      //     'invoiceAmounts' => $invoiceAmounts,
      //     'accountReceivables' => $accountReceivables
      // ]);
      Yii::$app->response->format = Response::FORMAT_JSON;
      return [
          'months' => $months,
          'invoiceAmounts' => $invoiceAmounts,
          'arAmounts' => $accountReceivables,
      ];
  }
  public function actionTestError(){
    throw new \yii\web\HttpException(500, 'Testing error logging in production mode.');
  }
}
