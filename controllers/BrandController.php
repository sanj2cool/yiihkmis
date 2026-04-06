<?php

namespace app\controllers;
use Yii;
use app\models\TblBrand;
use app\models\TblBrandSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\TblMenuAccess;
use app\models\TblProduct;
use app\models\TblProductBrand;
use app\models\TblProductCategory;
use yii\helpers\Url;
/**
 * BrandController implements the CRUD actions for TblBrand model.
 */
class BrandController extends Controller
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
        $menuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$fk_user_id,'fk_menu_id'=>74,'status'=>1])->one();
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
     * Lists all TblBrand models.
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
        $searchModel = new TblBrandSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionResetFilters()
    {
        $searchModel = new TblBrandSearch();
        $searchModel->clearSearchState(); // Clear the session data
        return $this->redirect(['index']); // Redirect back to the index page
    }
    /**
     * Displays a single TblBrand model.
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
     * Creates a new TblBrand model.
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
        $model = new TblBrand();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {

              //-----CHECK FOR FILE UPLOADED ----
              $msg_doc = "";
              $target_dir = "../web/brand-images/";
              if(isset($_FILES["cat_img"]) && $_FILES["cat_img"]["name"] != ""){
                $mc=md5(microtime());
                $f_name = $mc.basename($_FILES["cat_img"]["name"]);
                $basename = basename($_FILES["cat_img"]["name"]);
                $file_name = $this->clean($f_name);
                $target_file = $target_dir.$file_name;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                      && $imageFileType != "gif" && $imageFileType != "webp") {
                    $msg_doc = "Sorry, only Image files are allowed.";
                }else{
                  if(move_uploaded_file($_FILES["cat_img"]["tmp_name"], $target_file)){
                    $modelPic = TblBrand::find()->where(['id'=>$model->id])->one();
                    $modelPic->image_url = $file_name;
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

            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TblBrand model.
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
          //-----CHECK FOR FILE UPLOADED ----
          $msg_doc = "";
          $target_dir = "../web/brand-images/";
          if(isset($_FILES["cat_img"]) && $_FILES["cat_img"]["name"] != ""){
            $mc=md5(microtime());
            $f_name = $mc.basename($_FILES["cat_img"]["name"]);
            $basename = basename($_FILES["cat_img"]["name"]);
            $file_name = $this->clean($f_name);
            $target_file = $target_dir.$file_name;
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                  && $imageFileType != "gif" && $imageFileType != "webp") {
                $msg_doc = "Sorry, only Image files are allowed.";
            }else{
              if(move_uploaded_file($_FILES["cat_img"]["tmp_name"], $target_file)){
                $modelPic = TblBrand::find()->where(['id'=>$model->id])->one();
                $modelPic->image_url = $file_name;
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

        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TblBrand model.
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
        $check = $this->CheckBeforeDelete($id);
        if($check == ""){
          //---no non-deleted product is related to this brand delete it ----
          $model = TblBrand::find()->where(['id'=>$id])->one();
          $model->status = 0;
          if($model->save()){
            Yii::$app->getSession()->setFlash('success', 'Record deleted successfully');
          }else{
            Yii::$app->getSession()->setFlash('error', 'Something went wrong. Please try again.');
          }
          return $this->redirect(['index']);
        }else{
          $message_str = '
          <div class="card">
          <div class="card-body p-3" style="max-height:500px;overflow:scroll;">

          <table class="table table-bordered table-striped mt-2 border-danger">
            <thead>
              <tr class="table-danger" align="center">
                <th colspan="6" align="center">
                  There are product(s) related to this brand. Please remove the brand from product(s) and try again.
                </th>
              </tr>
              <tr>
              <th>Sr. No.</th>
              <th>Product Name</th>
              <th>Brand(s)</th>
              <th>Category</th>
              <th>Status</th>
              <th>Act.</th>
              </tr>
              </thead>
              <tbody>'.$check.'</tbody></table>
                        </div>
                        </div>';
          Yii::$app->getSession()->setFlash('error', $message_str);
          return $this->redirect(['view','id'=>$id]);
        }

    }
    private function CheckBeforeDelete($id){
      //--- find products which have brands ----
      // $return_str = '<table border="1" style="border-collapse:collapse;">';
      $return_str = '';
      $check = TblProduct::find()
              ->where('status != 0')
              ->andWhere('id in (select fk_product_id from tbl_product_brand where status = 1 and fk_brand_id = '.$id.')')
              ->all();
      if(isset($check) && count($check) > 0){
        $i =1;
        foreach($check as $c){
          if($c->status == 1){
            $status = "Active";
          }else if($c->status == 2){
            $status = "Inactive";
          }else{
            $status = "<i>(not set)</i>";
          }
          $getselectedbrands = TblProductBrand::find()->where(['fk_product_id'=>$c->id,'status'=>1])->all();
          $brands_str = "";
          if(isset($getselectedbrands) && count($getselectedbrands) > 0){
            foreach($getselectedbrands as $gs){
              $getbtitle = TblBrand::find()->where(['id'=>$gs->fk_brand_id])->one();
              if(isset($getbtitle) && $getbtitle->title != ""){
                if($brands_str == ""){
                  $brands_str .= $getbtitle->title;
                }else{
                  $brands_str .= ",<br />".$getbtitle->title;
                }
              }
            }//---for loop ended----
          }//---if isset ended-----
          // $getcat = TblProductCategory::find()->where(['id'=>$c->fk_category_id,'status'=>1])->one();
          // if(isset($getcat) && $getcat->title != ""){
          //   $category = $getcat->title;
          // }else{
          //   $category = "(not set)";
          // }
          $getselectedcats = \app\models\TblProductAssignedCategory::find()->where(['fk_product_id'=>$c->id,'status'=>1])->all();
          $cats_str = "";
          if(isset($getselectedcats) && count($getselectedcats) > 0){
            foreach($getselectedcats as $gs){
              $getbtitle = TblProductCategory::find()->where(['id'=>$gs->fk_category_id,'status'=>1])->one();
              if(isset($getbtitle) && $getbtitle->title != ""){
                if($cats_str == ""){
                  $cats_str .= $getbtitle->title;
                }else{
                  $cats_str .= ", ".$getbtitle->title;
                }
              }
            }//---for loop ended----
          }//---if isset ended-----
          $category = $cats_str;
          $return_str .= '<tr>
          <td>'.$i.'</td>
          <td>'.$c->name.'</td>
          <td>'.$brands_str.'</td>
          <td>'.$category.'</td>
          <td>'.$status.'</td>
          <td><a target="_blank" href="'.Url::to(['product/update','id'=>$c->id]).'">Update</a></td>
          </tr>';
          $i++;
        }//---for loop ended ----
      }//---if isset ended-------
      // $return_str .= '</table>';
      return $return_str;
    }

    /**
     * Finds the TblBrand model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return TblBrand the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblBrand::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    function clean($string) {
      $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
      return preg_replace('/[^A-Za-z0-9.\-]/', '', $string); // Removes special chars.
    }
}
