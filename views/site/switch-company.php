<?php

 /** @var yii\web\View $this */
 /** @var yii\bootstrap5\ActiveForm $form */

 /** @var app\models\LoginForm $model */
 use app\widgets\Alert;
 use yii\bootstrap5\ActiveForm;
 use yii\bootstrap5\Html;
 use yii\helpers\Url;
 $this->title = 'Switch Company';
 $this->params['breadcrumbs'][] = $this->title;
 $session = Yii::$app->session;
 $tmp_id = $session->get('userId');
 if($tmp_id == ""){
   //---send the user back to login as we do not want this page to be opened by anyone outside the process of login ----
   header("Location:/bwmis/web/");
   die();
 }
 $getlocs = \app\models\TblUserLocation::find()->where(['fk_user_id'=>$tmp_id])->andWhere(['status'=>1])->all();
 $loc_arr = array();
 if(isset($getlocs) && count($getlocs) > 0){
   foreach($getlocs as $l){
       array_push($loc_arr,$l->fk_location_id);
   }
 }
 $getlocs_all = \app\models\TblOwnershipCompany::find()->where(['status'=>1])->andWhere(['in','id',$loc_arr])->all();
 ?>
 <div class="container-fluid bg-body-tertiary dark__bg-gray-1200">
   <div class="bg-holder bg-auth-card-overlay" style="background-image:url(design/assets/img/bg/bg-24.png);mix-blend-mode: normal;">
   </div>
   <!--/.bg-holder-->

   <div class="row flex-center position-relative min-vh-100 g-0 py-5">
     <div class="col-11 col-sm-10 col-xl-8">
       <div class="card border border-translucent auth-card">
         <div class="card-body pe-md-0">
           <div class="row align-items-center gx-0 gy-7">

             <div class="col-12 mx-auto">
               <?= Alert::widget() ?>
               <div class="auth-form-box">
                 <div class="text-center mb-4">
                   <h3 class="text-body-highlight">Select Company to Continue</h3>

                 </div>
                 <?= Alert::widget() ?>
                 <form class="verification-form" data-2fa-form="data-2fa-form" method="POST" action="<?=Url::to(['site/switch-company'])?>">
                   <!-- ALL THE COMPANIES/LOCATIONS OF THE USER -->
                   <?php if(isset($getlocs_all) && count($getlocs_all) > 0): ?>
                     <div class="company-selection d-flex flex-wrap gap-3">
                       <?php foreach($getlocs_all as $gc): ?>
                         <label class="company-card">
                           <input type="radio" name="company_selection" value="<?= $gc->id ?>" required />
                           <div class="card-content">
                             <h6 class="mb-1"><?= htmlspecialchars($gc->location_name) ?></h6>
                             
                           </div>
                         </label>
                       <?php endforeach; ?>
                     </div>
                   <?php endif; ?>

                   <Button class="btn btn-primary w-100 mb-5 mt-3" type="submit">Select</Button>
                 </form>

               </div>
             </div>
           </div>
         </div>
       </div>
     </div>
   </div>
 </div>
<style>
.company-card {
  display: block;
  cursor: pointer;
  border: 2px solid #ddd;
  border-radius: 10px;
  padding: 1rem;
  min-width: 200px;
  transition: all 0.2s ease;
  background: #fff;
  flex: 1 1 200px;
  position: relative; /* needed for checkmark positioning */
}

.company-card input[type="radio"] {
  display: none; /* hide default radio */
}

.company-card .card-content {
  text-align: center;
}

.company-card:hover {
  border-color: #0d6efd;
  box-shadow: 0 0 6px rgba(13, 110, 253, 0.3);
}

/* Highlight selected card */
.company-card input[type="radio"]:checked + .card-content {
  border-radius: 8px;
  background: #e9f3ff;
  border: 2px solid #0d6efd;
  padding: 0.75rem;
}

/* Checkmark when selected */
.company-card input[type="radio"]:checked + .card-content::after {
  content: "✔";
  position: absolute;
  top: 8px;
  right: 12px;
  font-size: 1.2rem;
  color: #0d6efd;
  font-weight: bold;
}


</style>
