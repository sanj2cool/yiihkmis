<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $model */
use app\widgets\Alert;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;
$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid bg-body-tertiary dark__bg-gray-1200">
  <div class="bg-holder bg-auth-card-overlay" style="background-image:url(design/assets/img/bg/bg-38.png);mix-blend-mode: normal;">
  </div>
  <!--/.bg-holder-->

  <div class="row flex-center position-relative min-vh-100 g-0 py-5">
    <div class="col-11 col-sm-10 col-xl-8">
      <div class="card border border-translucent auth-card">
        <div class="card-body pe-md-0">
          <div class="row align-items-center gx-0 gy-7">
            <div class="col-auto bg-body-highlight dark__bg-gray-1100 rounded-3 position-relative overflow-hidden auth-title-box">
              <div class="bg-holder" style="background-image:url(design/assets/img/bg/38.png);">
              </div>
              <!--/.bg-holder-->

              <div class="position-relative px-4 px-lg-7 pt-7 pb-7 pb-sm-5 text-center text-md-start pb-lg-7 pb-md-7">
                <h3 class="mb-3 text-body-emphasis fs-7">Inventory Management</h3>
                <!-- <p class="text-body-tertiary">Give yourself some hassle-free development process with the uniqueness of Phoenix!</p> -->
                <ul class="list-unstyled mb-0 w-max-content w-md-auto">
                  <li class="d-flex align-items-center"><span class="uil uil-check-circle text-success me-2"></span><span class="text-body-tertiary fw-semibold">Fast</span></li>
                  <li class="d-flex align-items-center"><span class="uil uil-check-circle text-success me-2"></span><span class="text-body-tertiary fw-semibold">Simple</span></li>
                  <li class="d-flex align-items-center"><span class="uil uil-check-circle text-success me-2"></span><span class="text-body-tertiary fw-semibold">Reliable</span></li>
                </ul>
              </div>
              <div class="position-relative z-n1 mb-6 d-none d-md-block text-center mt-md-15">
                <!-- <img class="auth-title-box-img d-dark-none" src="design/assets/img/spot-illustrations/auth.png" alt="" />
                <img class="auth-title-box-img d-light-none" src="design/assets/img/spot-illustrations/auth-dark.png" alt="" /> -->
                <img class="auth-title-box-img" src="design/assets/img/spot-illustrations/16.png" alt="" />
              </div>
            </div>
            <div class="col mx-auto">
              <?= Alert::widget() ?>
              <div class="auth-form-box">
                <div class="text-center mb-2"><a class="d-flex flex-center text-decoration-none mb-4" href="#">
                    <div class="d-flex align-items-center fw-bolder fs-3 d-inline-block">
                      HK Trailer Parts
                    </div>
                  </a>
                  <h3 class="text-body-highlight">Sign In</h3>
                  <p class="text-body-tertiary">Get access to your account</p>
                </div>
                <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                    'fieldConfig' => [
                        'template' => "{label}\n{input}\n{error}",
                        'labelOptions' => ['class' => 'col-lg-12 col-form-label mr-lg-3'],
                        'inputOptions' => ['class' => 'col-lg-3 form-control'],
                        'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
                    ],
                ]); ?>
                <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
                <?= $form->field($model, 'password')->passwordInput() ?>


                <div class="form-group">
                    <div>
                        <?= Html::submitButton('Login', ['class' => 'btn btn-primary w-100', 'name' => 'login-button']) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>

                <div style="color:#999;">
                    <!-- You may login with <strong>admin/admin</strong> or <strong>demo/demo</strong>. -->
                    <!-- <br> -->
                    <!-- To modify the username/password, please check out the code <code>app\models\User::$users</code>. -->
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
