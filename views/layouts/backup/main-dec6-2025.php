<?php
ob_start();
/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;
use app\models\TblMenu;
use app\models\TblMenuAccess;
use app\models\TblUser;

use app\models\TblTask;
use app\models\TblTaskStatus;
use yii\db\Expression;
use yii\db\Query;
AppAsset::register($this);
$userName = Yii::$app->session->get('username');
$userID = Yii::$app->session->get('userId');
$getuser = TblUser::find()->where(['id'=>$userID])->one();
if(isset($getuser) && $getuser->alias != ""){
  $displayName = $getuser->alias;
}else{
  $displayName = $userName;
}
$user_company = Yii::$app->session->get('userCompany');
$web_search_item = Yii::$app->session->get('web_search_item');
$web_search_type = Yii::$app->session->get('web_search_type');

$getlocation = \app\models\TblOwnershipCompany::find()->where(['id'=>$user_company])->one();
if(isset($getlocation) && $getlocation->id != "")
{
  $location_name = $getlocation->location_name;
}else{
  $location_name = '';
}
$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/images/hktrailer-favicon.png')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
  <title><?= Html::encode($this->title) ?></title>
  <meta name="theme-color" content="#ffffff">
  <script src="design/vendors/simplebar/simplebar.min.js"></script>
  <script src="design/assets/js/config.js"></script>


  <!-- ===============================================-->
  <!--    Stylesheets-->
  <!-- ===============================================-->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
  <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap" rel="stylesheet">
  <link href="design/vendors/simplebar/simplebar.min.css" rel="stylesheet">
  <!-- <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css"> -->
  <link href="design/vendors/choices/choices.min.css" rel="stylesheet" />
  <link href="design/vendors/flatpickr/flatpickr.min.css" rel="stylesheet" />
  <link href="design/assets/css/theme-rtl.min.css" type="text/css" rel="stylesheet" id="style-rtl">
  <link href="design/assets/css/theme.min.css" type="text/css" rel="stylesheet" id="style-default">
  <link href="design/assets/css/user-rtl.min.css" type="text/css" rel="stylesheet" id="user-style-rtl">
  <link href="design/assets/css/user.min.css" type="text/css" rel="stylesheet" id="user-style-default">
  <link href="css/custom.css" type="text/css" rel="stylesheet" id="user-style-default">
  <script>
  var phoenixIsRTL = window.config.config.phoenixIsRTL;
  if (phoenixIsRTL) {
    var linkDefault = document.getElementById('style-default');
    var userLinkDefault = document.getElementById('user-style-default');
    linkDefault.setAttribute('disabled', true);
    userLinkDefault.setAttribute('disabled', true);
    document.querySelector('html').setAttribute('dir', 'rtl');
  } else {
    var linkRTL = document.getElementById('style-rtl');
    var userLinkRTL = document.getElementById('user-style-rtl');
    linkRTL.setAttribute('disabled', true);
    userLinkRTL.setAttribute('disabled', true);
  }
  </script>
  <link href="design/vendors/leaflet/leaflet.css" rel="stylesheet">
  <link href="design/vendors/leaflet.markercluster/MarkerCluster.css" rel="stylesheet">
  <link href="design/vendors/leaflet.markercluster/MarkerCluster.Default.css" rel="stylesheet">
  <link href="cropper/cropper.css" rel="stylesheet">

  <!-- <script src="https://unpkg.com/dropzone"></script> -->
  <script src="https://unpkg.com/cropperjs"></script>
  <script src="cropper/cropper.js"></script>

  <!-- <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css"> -->
  <?php $this->head() ?>
</head>
<body>
  <?php $this->beginBody() ?>
  <main class="main" id="top">
    <nav class="navbar navbar-vertical navbar-expand-lg sidebar-bg text-white" data-navbar-appearence="darker">
      <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
        <!-- scrollbar removed-->
        <div class="navbar-vertical-content" id="sidebar">
          <ul class="navbar-nav flex-column" id="navbarVerticalNav">
            <?php
            $getmenuitems = TblMenu::find()->where(['status'=>1,'parent_id'=>1])->orderBy(['priority'=>SORT_ASC])->all();
            if(isset($getmenuitems) && count($getmenuitems) > 0){
              foreach($getmenuitems as $gm){
                //-------check if menu access entry exists
                $check = TblMenuAccess::find()->where(['status'=>1,'fk_menu_id'=>$gm->id,'fk_user_id'=>$userID])->one();
                if(isset($check) && $check->view_crud == 1){
                  echo ' <li class="nav-item">
                  <!-- label-->
                  <p class="navbar-vertical-label text-white">'.$gm->title.'
                  </p>
                  <hr class="navbar-vertical-line" />';
                  //-------get the child elements -------
                  $getchildren = TblMenu::find()->where(['parent_id'=>$gm->id,'status'=>1])->orderBy(['priority'=>SORT_ASC])->all();
                  if(isset($getchildren) && count($getchildren) > 0){
                    foreach($getchildren as $gc){
                      //------check for menu access ----
                      $check_child = TblMenuAccess::find()->where(['status'=>1,'fk_menu_id'=>$gc->id,'fk_user_id'=>$userID])->one();
                      if(isset($check_child) && $check_child->view_crud == 1){
                        echo '  <div class="nav-item-wrapper">
                        <a class="nav-link label-1  text-white" href="'.Url::to([$gc->url]).'" role="button" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="'.$gc->class.'"></span></span>
                        <span class="nav-link-text-wrapper">
                        <span class="nav-link-text">'.$gc->title.'</span>
                        </span>
                        </div>
                        </a>
                        </div>';
                      }

                    }
                  }

                  echo '</li>';
                }

              }//-------for loop ended -----
            }//------if isset ended ------
            ?>



          </ul>
        </div>
      </div>
      <div class="navbar-vertical-footer">
        <button class="btn navbar-vertical-toggle border-0 fw-semibold w-100 white-space-nowrap d-flex align-items-center"><span class="uil uil-left-arrow-to-left fs-8"></span><span class="uil uil-arrow-from-right fs-8"></span><span class="navbar-vertical-footer-text ms-2">Collapsed View</span></button>
      </div>
    </nav>
    <nav class="navbar navbar-top fixed-top navbar-expand" id="navbarDefault">
      <div class="collapse navbar-collapse justify-content-between">
        <div class="navbar-logo">

          <button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
          <a class="navbar-brand me-1 me-sm-3" href="index.html">
            <div class="d-flex align-items-center">
              <div class="d-flex align-items-center">
                <!-- <img src="assets/img/icons/logo.png" alt="phoenix" width="27" /> -->
                <h5 class="logo-text ms-2 d-none d-sm-block">HK Trailer Parts</h5>
              </div>
            </div>
          </a>
        </div>

          <div class="row w-50">
            <div class="col-3 pe-0 px-0">
              <div class=" d-none d-lg-block d-sm-block w-100">
                <select class="form-control form-control-sm" name="select-search-opt" id="select-search-opt">
                  <?php
                    if(isset($web_search_type) && $web_search_type != ""){
                      ?>
                      <option value="1" <?php if($web_search_type == 1){ echo 'selected="selected"';} ?>>Item Name</option>
                      <option value="2" <?php if($web_search_type == 2){ echo 'selected="selected"';} ?>>Cross Reference</option>
                      <option value="3" <?php if($web_search_type == 3){ echo 'selected="selected"';} ?>>Description</option>
                      <?php
                    }else{
                      ?>
                      <option value="1" selected="selected">Item Name</option>
                      <option value="2">Cross Reference</option>
                      <option value="3">Description</option>
                      <?php
                    }
                   ?>

                  <!-- <option value="4">Brands</option> -->
                </select>
              </div>
            </div>
            <div class="col-9 pe-0 px-1">
              <div class="search-box navbar-top-search-box d-none d-lg-block d-sm-block w-lg-100 w-100 me-2" data-list=''>
              <form class="position-relative" data-bs-toggle="search" data-bs-display="static">

                  <div class="input-group">
                      <input class="form-control search-input fuzzy-search form-control-sm rounded" id="search_by_part" type="search" placeholder="Search by item name..." aria-label="Search" value="<?php if(isset($web_search_item) && $web_search_item != ""){ echo $web_search_item;}?>" />
                     <a href="https://hkmis.ca/scan-barcode.html" target="_blank" class="btn position-absolute top-50 end-0 translate-middle-y pe-3" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Scan Item Barcode">
                       <i class="fas fa-barcode"></i>
                     </a>
                   </div>
                  <span class="fas fa-search search-box-icon"></span>
              </form>


              <div class="btn-close position-absolute end-0 top-50 translate-middle cursor-pointer shadow-none" data-bs-dismiss="search">
                <button class="btn btn-link p-0" aria-label="Close"></button>
              </div>
              <div class="dropdown-menu border start-0 py-0 overflow-hidden w-100">
                <div class="scrollbar-overlay" style="max-height: 30rem;">
                    <h6 class="dropdown-header text-body-highlight fs-10 py-2"><span id="search_result_count">0</span> <span class="text-body-quaternary">results</span></h6>
                    <hr class="my-0" />
                    <h6 class="dropdown-header text-body-highlight fs-9 border-bottom border-translucent py-2 lh-sm">Items</h6>
                    <div class="py-2" id="searchbar_content_div">
                    </div>
                </div>
              </div>
            </div>
          </div>



        </div>
        <ul class="navbar-nav navbar-nav-icons flex-row">
          <li class="nav-item">
            <h6><?=$location_name?></h6>
          </li>
          <li class="nav-item">
            <div class="theme-control-toggle fa-icon-wait px-2">
              <input class="form-check-input ms-0 theme-control-toggle-input" type="checkbox" data-theme-control="phoenixTheme" value="dark" id="themeControlToggle" />
              <label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Switch theme" style="height:32px;width:32px;"><span class="icon" data-feather="moon"></span></label>
              <label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Switch theme" style="height:32px;width:32px;"><span class="icon" data-feather="sun"></span></label>
            </div>
          </li>
          <?php
            $task = TblTask::find()
            ->where('status != 0 and status_id != 3')
            ->andWhere('assigned_to = '.$userID.' or crt_by = '.$userID)
            ->andWhere('due_date <= "'.date('Y-m-d').'"')->all();
            if(isset($task) && count($task) > 0){
              $task_count = count($task);
            }else{
              $task_count = 0;
            }
           ?>
           <li class="nav-item dropdown" style="align-items:stretch;">
             <a class="nav-link text-success" href="#" style="min-width: 2.5rem;font-size:1rem;display:flex;align-items:center;padding:.75rem .75rem;" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-bs-auto-close="outside">
               <span class="fa-solid fa-bell text-success" style="height:20px;width:20px;"></span>
               <sup>
                 &nbsp;<badge class="badge bg-success text-white"><?=$task_count?></badge>
               </sup>
             </a>

             <div class="dropdown-menu dropdown-menu-end notification-dropdown-menu py-0 shadow border border-300 navbar-dropdown-caret" id="navbarDropdownNotfication" aria-labelledby="navbarDropdownNotfication">
               <div class="card position-relative border-0">
                 <div class="card-header p-2">
                   <div class="d-flex justify-content-between">
                     <h5 class="text-black mb-0">Overdue Tasks</h5>
                   </div>
                 </div>
                 <div class="card-body p-0">
                   <div class="scrollbar-overlay" style="height: 27rem;">
                     <?php

                     if(isset($task) && count($task) > 0){
                       foreach($task as $m){
                         $gettaskstatus = TblTaskStatus::find()->where(['id'=>$m->status_id])->andWhere('status != 0')->one();
                         if(isset($gettaskstatus) && $gettaskstatus->status_name != ""){
                           $task_status = '<span class="badge '.$gettaskstatus->icon.'">'.$gettaskstatus->status_name.'</span>';
                         }else{
                           $task_status = "(not set)";
                         }
                         $getemployee = TblUser::find()->where(['id'=>$m->assigned_to])->andWhere('status != 0')->one();
                         if(isset($getemployee) && $getemployee->username != ""){
                           $ass_to = $getemployee->username;
                         }else{
                           $ass_to = "(not set)";
                         }
                         echo '<div class="border-300">
                         <div class="px-2 px-sm-3 py-3 border-300 notification-card position-relative read border-bottom">
                         <div class="d-flex align-items-center justify-content-between position-relative">
                         <div class="d-flex">
                         <div class="flex-1 me-sm-3">
                         <h4 class="fs--1 text-black"><a href="'.Url::to(['task/update','id'=>$m->id]).'">'.$m->title.'</a></h4>
                         <p class="fs--1 text-1000 fw-normal">Assigned To -'.$ass_to.'</p>
                         <p class="fs--1 text-1000 mb-sm-3 fw-normal">'.$m->description.'</p>

                         <p class="text-800 fs--1 mb-0"><span class="me-1 fas fa-clock"></span>'.$m->due_date.' '.$m->time.' '.$task_status.'</p>
                         </div>
                         </div>
                         </div>
                         </div>
                         </div>';
                       }//======for loop ended =======
                     }//=====if isset ended ==========
                     ?>


                   </div>
                 </div>
                 <div class="card-footer p-0 border-top border-0">
                   <div class="my-2 text-center fw-bold fs--2 text-600"><a class="fw-bolder" href="<?=Url::to(['task/index'])?>">View All</a></div>
                 </div>
               </div>
             </div>
           </li>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#" style="min-width: 2.25rem" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-bs-auto-close="outside"><span class="d-block" style="height:30px;width:30px;"><span class="fas fa-bullhorn text-danger" style="height:30px;width:30px;" class="text-warning"></span></span></a>

            <div class="dropdown-menu dropdown-menu-end notification-dropdown-menu py-0 shadow border navbar-dropdown-caret" id="navbarDropdownNotfication" aria-labelledby="navbarDropdownNotfication">
              <div class="card position-relative border-0">
                <div class="card-header p-2">
                  <div class="d-flex justify-content-between">
                    <h5 class="text-body-emphasis mb-0">Critical Reorder Level</h5>
                    <!-- <button class="btn btn-link p-0 fs-9 fw-normal" type="button">Item with critical re-order level</button> -->
                  </div>
                </div>
                <div class="card-body p-0">
                  <div class="scrollbar-overlay" style="height: 27rem;">
                    <?php
                    //-------get 5 items here -------------
                    $query = (new Query())
                    ->select([
                      'p.name AS product_name',
                      'p.id AS product_id',
                      'p.description AS description',
                      'p.sku AS product_sku',
                      'pc.title AS product_category',
                      '(IFNULL(SUM(pr.no_of_items), 0) - IFNULL(SUM(pa.no_of_items), 0)) AS current_qty',
                      'GROUP_CONCAT(DISTINCT pr.fk_bin_id SEPARATOR ", ") AS product_location',
                      'p.quantity_in_stock as reorder_qty'
                    ])
                    ->from('tbl_product p')
                    ->leftJoin('tbl_product_category pc', 'p.fk_category_id = pc.id')
                    ->leftJoin('tbl_product_receiving pr', 'p.id = pr.fk_product_id AND pr.status != 0')
                    ->leftJoin('tbl_product_allotment pa', 'p.id = pa.fk_product_id AND pa.status != 0')
                    ->where('p.status != 0')
                    ->groupBy('p.id')
                    // ->having('current_qty < p.quantity_in_stock')  // Show products where current_qty is below reorder_qty
                    ->having('current_qty < (p.quantity_in_stock * 0.80)')
                    ->limit(10)
                    ->orderBy(['current_qty' => SORT_DESC]);
                    // Execute the query and get the result
                    $critical_products = $query->all();
                    if(isset($critical_products) && count($critical_products) > 0){
                      foreach($critical_products as $p){
                        echo '<div class="px-2 px-sm-3 py-3 notification-card position-relative read border-bottom">
                        <div class="d-flex align-items-center justify-content-between position-relative">
                        <div class="d-flex">

                        <div class="flex-1 me-sm-3">
                        <h4 class="fs-9 text-body-emphasis">'.$p['product_name'].'</h4>
                        <p class="fs-9 text-body-highlight mb-2 mb-sm-3 fw-normal">Current Qty in Stock - '.$p['current_qty'].' | Reorder Qty Level - '.$p['reorder_qty'].'</p>

                        </div>
                        </div>

                        </div>
                        </div>';
                      }
                    }//-----if isset ended -------
                    ?>
                  </div>
                </div>
                <div class="card-footer p-0 border-top border-translucent border-0">
                  <div class="my-2 text-center fw-bold fs-10 text-body-tertiary text-opactity-85"><a class="fw-bolder" href="<?=Url::to(['report/critical-low-inventory-count-report'])?>">View All</a></div>
                </div>
              </div>
            </div>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link" href="#" style="min-width: 2.25rem" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-bs-auto-close="outside"><span class="d-block" style="height:30px;width:30px;"><span class="fas fa-bell text-warning" style="height:30px;width:30px;" class="text-warning"></span></span></a>

            <div class="dropdown-menu dropdown-menu-end notification-dropdown-menu py-0 shadow border navbar-dropdown-caret" id="navbarDropdownNotfication" aria-labelledby="navbarDropdownNotfication">
              <div class="card position-relative border-0">
                <div class="card-header p-2">
                  <div class="d-flex justify-content-between">
                    <h5 class="text-body-emphasis mb-0">Under Reorder Level</h5>
                    <button class="btn btn-link p-0 fs-9 fw-normal" type="button">Items under re-order level</button>
                  </div>
                </div>
                <div class="card-body p-0">
                  <div class="scrollbar-overlay" style="height: 27rem;">
                    <?php
                    $query1 = (new Query())
                    ->select([
                      'p.name AS product_name',
                      'p.id AS product_id',
                      'p.description AS description',
                      'p.sku AS product_sku',
                      'pc.title AS product_category',
                      '(IFNULL(SUM(pr.no_of_items), 0) - IFNULL(SUM(pa.no_of_items), 0)) AS current_qty',
                      'GROUP_CONCAT(DISTINCT pr.fk_bin_id SEPARATOR ", ") AS product_location',
                      'p.quantity_in_stock as reorder_qty'
                    ])
                    ->from('tbl_product p')
                    ->leftJoin('tbl_product_category pc', 'p.fk_category_id = pc.id')
                    ->leftJoin('tbl_product_receiving pr', 'p.id = pr.fk_product_id AND pr.status != 0')
                    ->leftJoin('tbl_product_allotment pa', 'p.id = pa.fk_product_id AND pa.status != 0')
                    ->where('p.status != 0')
                    ->groupBy('p.id')
                    ->having('current_qty < p.quantity_in_stock')  // Show products where current_qty is below reorder_qty
                    ->limit(10)
                    ->orderBy(['current_qty' => SORT_DESC]);
                    $low_products = $query1->all();
                    if(isset($low_products) && count($low_products) > 0){
                      foreach($low_products as $p){
                        echo '<div class="px-2 px-sm-3 py-3 notification-card position-relative read border-bottom">
                        <div class="d-flex align-items-center justify-content-between position-relative">
                        <div class="d-flex">

                        <div class="flex-1 me-sm-3">
                        <h4 class="fs-9 text-body-emphasis">'.$p['product_name'].'</h4>
                        <p class="fs-9 text-body-highlight mb-2 mb-sm-3 fw-normal">Current Qty in Stock - '.$p['current_qty'].' | Reorder Qty Level - '.$p['reorder_qty'].'</p>

                        </div>
                        </div>

                        </div>
                        </div>';
                      }
                    }
                    ?>







                  </div><!--sccollbar div ended -->
                </div><!-- card body ended -->
                <div class="card-footer p-0 border-top border-translucent border-0">
                  <div class="my-2 text-center fw-bold fs-10 text-body-tertiary text-opactity-85"><a class="fw-bolder" href="<?=Url::to(['report/low-inventory-count-report'])?>">View All</a></div>
                </div>
              </div>
            </div>
          </li>

          <li class="nav-item dropdown"><a class="nav-link lh-1 pe-0" id="navbarDropdownUser" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
            <div class="avatar avatar-l ">
              <img class="rounded-circle " src="design/assets/img/team/72x72/avatar.webp" alt="" />

            </div>
          </a>
          <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border" aria-labelledby="navbarDropdownUser">
            <div class="card position-relative border-0">
              <div class="card-body p-0">
                <div class="text-center pt-4 pb-3">
                  <div class="avatar avatar-xl ">
                    <img class="rounded-circle" src="design/assets/img/team/72x72/avatar.webp" alt="" />

                  </div>
                  <h6 class="mt-2 text-body-emphasis"><?=$displayName?></h6>
                </div>
              </div>
              <div class="overflow-auto scrollbar" style="height: 10rem;">
                <ul class="nav d-flex flex-column mb-2 pb-1">
                  <li class="nav-item"><a class="nav-link px-3 d-block" href="<?=Url::to(['site/index'])?>"><span class="me-2 text-body align-bottom" data-feather="pie-chart"></span>Dashboard</a></li>
                  <li class="nav-item"><a class="nav-link px-3 d-block" href="<?=Url::to(['user/updatepassword'])?>"> <i class="me-2 text-body  align-bottom fas fa-key"></i>Password Update</a></li>
                  <li class="nav-item"><a class="nav-link px-3 d-block" href="<?=Url::to(['site/switch-company'])?>"> <span class="me-2 text-body align-bottom" data-feather="settings"></span>Switch Company</a></li>

                </ul>
              </div>
              <div class="card-footer p-0 border-top border-translucent pt-2">
                <div class="px-3 pb-2"> <a class="btn btn-phoenix-secondary d-flex flex-center w-100" href="<?=Url::to(['site/logout'])?>"> <span class="me-2" data-feather="log-out"> </span>Log out</a></div>

              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </nav>
  <div class="content">
    <?=$content?>
    <footer class="footer position-absolute">
      <div class="row g-0 justify-content-between align-items-center h-100">
        <div class="col-12 col-sm-auto text-center">
          <p class="mb-0 mt-2 mt-sm-0 text-body">2024 - <?=date('Y')?> &copy; Powered by <a class="mx-1" href="https://b4b.consulting" target="_blank">B4B Consulting</a></p>
        </div>
        <div class="col-12 col-sm-auto text-center">
          <p class="mb-0 text-body-tertiary text-opacity-85">v1.2.0</p>
        </div>
      </div>
    </footer>
  </div>

  <script>
  var navbarTopStyle = window.config.config.phoenixNavbarTopStyle;
  var navbarTop = document.querySelector('.navbar-top');
  if (navbarTopStyle === 'darker') {
    navbarTop.setAttribute('data-navbar-appearance', 'darker');
  }

  var navbarVerticalStyle = window.config.config.phoenixNavbarVerticalStyle;
  var navbarVertical = document.querySelector('.navbar-vertical');
  if (navbarVertical && navbarVerticalStyle === 'darker') {
    navbarVertical.setAttribute('data-navbar-appearance', 'darker');
  }
</script>

</main>
<!-- ===============================================-->
<!--    End of Main Content-->
<!-- ===============================================-->




  <?php $this->endBody() ?>
  <!-- ===============================================-->
  <!--    JavaScripts-->
  <!-- ===============================================-->
  <script src="design/vendors/popper/popper.min.js"></script>
  <script src="design/vendors/bootstrap/bootstrap.min.js"></script>
  <script src="design/vendors/anchorjs/anchor.min.js"></script>
  <script src="design/vendors/is/is.min.js"></script>
  <script src="design/vendors/fontawesome/all.min.js"></script>
  <script src="design/vendors/lodash/lodash.min.js"></script>
  <!-- <script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script> -->
  <!-- <script src="design/vendors/list.js/list.min.js"></script> -->
  <script src="design/vendors/feather-icons/feather.min.js"></script>
  <script src="design/vendors/dayjs/dayjs.min.js"></script>
  <script src="design/vendors/leaflet/leaflet.js"></script>
  <script src="design/vendors/leaflet.markercluster/leaflet.markercluster.js"></script>
  <script src="design/vendors/leaflet.tilelayer.colorfilter/leaflet-tilelayer-colorfilter.min.js"></script>
  <script src="design/assets/js/phoenix.js"></script>
  <script src="design/vendors/echarts/echarts.min.js"></script>
  <script src="design/assets/js/echarts-example.js"></script>
  <!-- <script src="design/assets/js/ecommerce-dashboard.js"></script> -->
  <script src="design/vendors/choices/choices.min.js"></script>
  <script src="design/vendors/flatpickr/flatpickr.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js" integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script>
  function printImg(url) {
    var win = window.open('');
    win.document.write('<img src="' + url + '" onload="window.print();window.close()" width="97%"/>');
    win.focus();
  }
  function sleep(milliseconds) {
    var start = new Date().getTime();
    for (var i = 0; i < 1e7; i++) {
      if ((new Date().getTime() - start) > milliseconds){
        break;
      }
    }
  }
  function checkNum(el){
    var ex = /^[0-9]*$/;
    if(ex.test(el.value)==false){
      el.value = el.value.substring(0,el.value.length - 1);
    }
  }
  function checkDec(el){
    var ex = /^[0-9]+\.?[0-9]*$/;
    if(ex.test(el.value)==false){
      el.value = el.value.substring(0,el.value.length - 1);
    }
  }
  $(document).ready(function(){
    $(document).on("change",".floatNumberField",function(){
      if($(this).val() != ""){
        $(this).val(parseFloat($(this).val()).toFixed(2));
      }
    });

    //-------search -------------
    $("#search_by_part").keyup(function(){
      // console.log("key up called");
      let search = $("#search_by_part").val();
      let search_type = $("#select-search-opt").val();
      if(search != ""){
        $.post("index.php?r=site/search-by-part-copy",{search:search,search_type:search_type},function(r){
          console.log(r);
          let obj = JSON.parse(r);
          let return_count = obj.return_count;
          let return_str = obj.return_str;
          $("#search_result_count").html(return_count);
          $("#searchbar_content_div").html(return_str);
        });
      }else{
        $("#search_result_count").html(0);
        $("#searchbar_content_div").html("");
      }

    });


  });

  </script>
  <script>
  // Save the sidebar scroll position and set active class before navigation
  document.querySelectorAll('#sidebar .nav-link').forEach(function(link) {
      link.addEventListener('click', function() {
          // Save scroll position
          const sidebar = document.getElementById('sidebar');
          localStorage.setItem('sidebarScrollPosition', sidebar.scrollTop);

          // Remove 'active' class from all links
          document.querySelectorAll('#sidebar .nav-link').forEach(function(nav) {
              nav.classList.remove('active');
          });

          // Add 'active' class to the clicked link
          link.classList.add('active');

          // Optionally, you can save the active link href to localStorage to persist the active state after reload
          localStorage.setItem('activeLink', link.getAttribute('href'));
      });
  });

  // Restore the sidebar scroll position and active class after page load
  window.addEventListener('load', function() {
      const sidebar = document.getElementById('sidebar');
      const savedPosition = localStorage.getItem('sidebarScrollPosition');
      if (savedPosition) {
          sidebar.scrollTop = savedPosition;
      }

      // Restore the active class on the link from localStorage
      const activeLink = localStorage.getItem('activeLink');
      if (activeLink) {
          document.querySelectorAll('.nav-link').forEach(function(link) {
              if (link.getAttribute('href') === activeLink) {
                  link.classList.add('active');
              }
          });
      }
  });
  </script>
  <!-- CODE FOR RETURNING BACK TO SAME POSITION WHEN REDIRECTED TO NEXT PAGE -->
  <script>
  document.addEventListener("DOMContentLoaded", function () {
      // Restore scroll position ONLY on back/forward
      window.addEventListener("pageshow", function (event) {
          const scrollPos = sessionStorage.getItem("scrollPos");
          if (event.persisted || performance.getEntriesByType("navigation")[0].type === "back_forward") {
              if (scrollPos !== null) {
                  window.scrollTo(0, parseInt(scrollPos));
                  sessionStorage.removeItem("scrollPos");
              }
          }
      });

      // Save scroll position ONLY for normal links (not pagination or tab links)
      document.querySelectorAll("a").forEach(function(link) {
          link.addEventListener("click", function (e) {
              // Skip if link has data-page (e.g., Yii2 pagination)
              if (link.closest(".pagination") || link.closest(".sort-link")) return;

              // Only save if it's the same origin
              const isSameOrigin = link.hostname === window.location.hostname;
              if (!isSameOrigin) return;

              sessionStorage.setItem("scrollPos", window.scrollY);
          });
      });
  });
  </script>
  <?php
  // $this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css');
  ?>
</body>
</html>
<?php $this->endPage() ?>
