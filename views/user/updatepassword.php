<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\TblUser;
/* @var $this yii\web\View */
/* @var $model app\models\TblIssue */

$this->title = 'Change Password';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-user-change-password">
  <div class="card shadow rounded mt-2">
      <div class="card-body p-3">
      <div class="row">
        <div class="col-lg-6">
          <h3><?= Html::encode($this->title) ?></h3>
        </div>
        <div class="col-lg-6 text-end">
              <?= Html::a('Back to List', ['index'], ['class' => 'btn btn-primary btn-sm']) ?>
        </div>
      </div>
    </div>
    </div>

  <div class="card mt-3">
    <div class="card-body">
      <div id="msg">

        <?php
        if(Yii::$app -> session -> getFlash('success')!=null){
          ?>

          <div class="alert alert-outline-success d-flex align-items-center" role="alert">
            <span class="fas fa-check-circle text-success fs-5 me-3"></span>
            <p class="mb-0 flex-1"><?= Yii::$app -> session -> getFlash('success'); ?></p>

            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          <?php
        }else if(Yii::$app -> session -> getFlash('error')!=null){
          ?>
          <div class="alert alert-outline-danger d-flex align-items-center" role="alert">
            <span class="fas fa-times-circle text-danger fs-5 me-3"></span>
            <p class="mb-0 flex-1"><?= Yii::$app -> session -> getFlash('error'); ?></p>
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>

          <?php
        }
        ?>
      </div>

      <div class="row justify-content-center">
        <div class="col-4">
          <form method="post" action="<?= Yii::$app -> urlManager -> createUrl('user/updatepassword') ?>" id="form_update">
            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
            <div class="mb-3">
              <label class="form-label" for="old-password">Select User</label>
              <select class="form-select" name="user_select" required>
                <option value="">Select</option>
                <?php
                //---if logged in user is 1 or 9 show all else show their own ---
                $session = Yii::$app->session;
                $fk_user_id = $session['userId'];
                if($fk_user_id == 1 || $fk_user_id == 9 || $fk_user_id == 8){
                  $getallusers = TblUser::find()->where(['status'=>1])->orderBy(['username'=>SORT_ASC])->all();
                }else{
                  $getallusers = TblUser::find()->where(['status'=>1,'id'=>$fk_user_id])->orderBy(['username'=>SORT_ASC])->all();
                }

                  if(isset($getallusers) && count($getallusers) > 0){
                    foreach($getallusers as $gu){
                      if($gu->id == $fk_user_id){
                          echo '<option value="'.$gu->id.'" selected="selected">'.$gu->username.' ['.$gu->alias.']</option>';
                      }else{
                          echo '<option value="'.$gu->id.'">'.$gu->username.' ['.$gu->alias.']</option>';
                      }

                    }
                  }
                 ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label" for="new-password">New Password</label>
              <input class="form-control passval" id="new_password" name="new_password" type="password" required />
              <div id="new_password_error" style="color:#f00;">

              </div>
            </div>
            <div class="mb-3">
              <label class="form-label" for="confirm-password">Confirm Password</label>
              <input class="form-control passval" id="confirm_password" name="confirm_password" type="password" required />
              <div id="confirm_password_error" style="color:#f00;">

              </div>
            </div>
            <button class="btn btn-primary d-block w-100" type="submit">Update Password </button>
          </form>
        </div>
        <div class="col-4">
          <b>Password must:</b>
          <br><span class="passval invalid" id="lengthval">Be atleast 8 Characters!</span>
          <br><span class="passval invalid" id="digitval">Contain atleast 1 number!</span>
          <br><span class="passval invalid" id="uppercaseval">Contain atleast 1 Capital Case!</span>
          <br><span class="passval invalid" id="lowercaseval">Contain atleast 1 Letter in Small Case!</span>
          <br><span class="passval invalid" id="specialval">Contain atleast 1 Special Character!<span>
          </div>
        </div>

      </div>
    </div>

  </div>
  <style>
  .red{
    border:1px solid #f00;
  }
  .green{
    border:1px solid #0f0;
  }
</style>
<?php
//form id - form_update
$js = <<<JS
$("#form_update").submit(function(e){
  e.preventDefault();
  // alert("form submitted");
  var new_password = $('#new_password').val();
  var confirm_password = $("#confirm_password").val();
  $("#new_password_error").html("");
  $("#confirm_password_error").html("");
  if(new_password == ""){
    //check if sub task is selected or not
    $("#new_password_error").html('Password cannot be blank');
    return false;
  }else if(confirm_password == ""){
    $("#confirm_password_error").html('Confirm Password cannot be blank');
    return false;
  }else if(new_password != confirm_password){
    $("#confirm_password_error").html("Password and confirm password should match");
    return false;
  }else{
    $("#pageloader").removeClass("hidden");
    // return true;
    e.currentTarget.submit();
  }
});
JS;
$this->registerJs($js);
$this->registerJsFile('web/js/password_validation.js',['depends' => [yii\web\JqueryAsset::className()]]);
$this->registerJs('
$("#confirm_password").blur(function(){
  console.log("confirm password");
  var new_password = $("#new_password").val();
  var confirm_password = $("#confirm_password").val();
  if(new_password === confirm_password){
    if($("#confirm_password").hasClass("red")){
      $("#confirm_password").removeClass("red");
    }
    $("#confirm_password").addClass("green");
  }else{
    $("#confirm_password").addClass("red");
    if($("#confirm_password").hasClass("green")){
      $("#confirm_password").removeClass("green");
    }
  }
});
');

?>
