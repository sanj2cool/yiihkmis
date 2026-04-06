<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
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
  <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
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
  <?php $this->head() ?>
</head>
<body>
  <?php $this->beginBody() ?>
  <main class="main" id="top">
    <nav class="navbar navbar-vertical navbar-expand-lg">
      <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
        <!-- scrollbar removed-->
        <div class="navbar-vertical-content">
          <ul class="navbar-nav flex-column" id="navbarVerticalNav">
            <li class="nav-item">
              <!-- label-->
              <p class="navbar-vertical-label">Statistics
              </p>
              <hr class="navbar-vertical-line" />
              <div class="nav-item-wrapper">
                <a class="nav-link label-1" href="<?=Url::to(['site/index'])?>" role="button" data-bs-toggle="" aria-expanded="false">
                  <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fa fa-chart-pie"></span></span>
                    <span class="nav-link-text-wrapper">
                      <span class="nav-link-text">Dashboard</span>
                    </span>
                  </div>
                </a>
              </div>
              <div class="nav-item-wrapper">
                <a class="nav-link label-1" href="<?=Url::to(['site/report'])?>" role="button" data-bs-toggle="" aria-expanded="false">
                  <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fa fa-chart-area"></span></span>
                    <span class="nav-link-text-wrapper">
                      <span class="nav-link-text">Reports</span>
                    </span>
                  </div>
                </a>
              </div>
            </li>
            <li class="nav-item">
                <!-- label-->
                <p class="navbar-vertical-label">Inventory Management
                </p>
                <hr class="navbar-vertical-line" />
                <div class="nav-item-wrapper">
                  <a class="nav-link label-1" href="<?=Url::to(['site/item-category-list'])?>" role="button" data-bs-toggle="" aria-expanded="false">
                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-cubes"></span></span>
                      <span class="nav-link-text-wrapper">
                        <span class="nav-link-text">Item Category</span>
                      </span>
                    </div>
                  </a>
                </div>
                <div class="nav-item-wrapper">
                  <a class="nav-link label-1" href="<?=Url::to(['site/item-creation-list'])?>" role="button" data-bs-toggle="" aria-expanded="false">
                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-edit"></span></span>
                      <span class="nav-link-text-wrapper">
                        <span class="nav-link-text">Item Creation</span>
                      </span>
                    </div>
                  </a>
                </div>
                <div class="nav-item-wrapper">
                  <a class="nav-link label-1" href="<?=Url::to(['site/item-receiving-list'])?>" role="button" data-bs-toggle="" aria-expanded="false">
                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fa-solid fa-dolly"></span></span>
                      <span class="nav-link-text-wrapper">
                        <span class="nav-link-text">Item Receiving</span>
                      </span>
                    </div>
                  </a>
                </div>
                <div class="nav-item-wrapper">
                  <a class="nav-link label-1" href="<?=Url::to(['site/item-allotment-list'])?>" role="button" data-bs-toggle="" aria-expanded="false">
                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fa-solid fa-truck-fast"></span></span>
                      <span class="nav-link-text-wrapper">
                        <span class="nav-link-text">Allotment/Shipping/Sales</span>
                      </span>
                    </div>
                  </a>
                </div>
              </li>
              <li class="nav-item">
                  <!-- label-->
                  <p class="navbar-vertical-label">Purchase Order
                  </p>
                  <hr class="navbar-vertical-line" />
                  <div class="nav-item-wrapper">
                    <a class="nav-link label-1" href="<?=Url::to(['site/purchase-order-list'])?>" role="button" data-bs-toggle="" aria-expanded="false">
                      <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-list"></span></span>
                        <span class="nav-link-text-wrapper">
                          <span class="nav-link-text">List</span>
                        </span>
                      </div>
                    </a>
                  </div>
                  <div class="nav-item-wrapper">
                    <a class="nav-link label-1" href="<?=Url::to(['site/purchase-invoice-list'])?>" role="button" data-bs-toggle="" aria-expanded="false">
                      <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fa-solid fa-receipt"></span></span>
                        <span class="nav-link-text-wrapper">
                          <span class="nav-link-text">Purchase Invoice</span>
                        </span>
                      </div>
                    </a>
                  </div>
                  <div class="nav-item-wrapper">
                    <a class="nav-link label-1" href="<?=Url::to(['site/accounts-payable-list'])?>" role="button" data-bs-toggle="" aria-expanded="false">
                      <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fa-solid fa-hand-holding-dollar"></span></span>
                        <span class="nav-link-text-wrapper">
                          <span class="nav-link-text">Accounts Payable</span>
                        </span>
                      </div>
                    </a>
                  </div>
                </li>

                  <li class="nav-item">
                      <!-- label-->
                      <p class="navbar-vertical-label">Vendors
                      </p>
                      <hr class="navbar-vertical-line" />  <div class="nav-item-wrapper">
                        <a class="nav-link label-1" href="<?=Url::to(['site/vendor-list'])?>" role="button" data-bs-toggle="" aria-expanded="false">
                          <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-rectangle-list"></span></span>
                            <span class="nav-link-text-wrapper">
                              <span class="nav-link-text">List</span>
                            </span>
                          </div>
                        </a>
                      </div>  <div class="nav-item-wrapper">
                        <a class="nav-link label-1" href="<?=Url::to(['site/vendor-create'])?>" role="button" data-bs-toggle="" aria-expanded="false">
                          <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fa-solid fa-circle-plus"></span></span>
                            <span class="nav-link-text-wrapper">
                              <span class="nav-link-text">Create</span>
                            </span>
                          </div>
                        </a>
                      </div>
                    </li>
                    <li class="nav-item">
                        <!-- label-->
                        <p class="navbar-vertical-label">Customers
                        </p>
                        <hr class="navbar-vertical-line" />
                        <div class="nav-item-wrapper">
                          <a class="nav-link label-1" href="<?=Url::to(['site/customer-list'])?>" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-users"></span></span>
                              <span class="nav-link-text-wrapper">
                                <span class="nav-link-text">List</span>
                              </span>
                            </div>
                          </a>
                        </div>
                        <div class="nav-item-wrapper">
                          <a class="nav-link label-1" href="<?=Url::to(['site/invoice-list'])?>" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-file-invoice"></span></span>
                              <span class="nav-link-text-wrapper">
                                <span class="nav-link-text">Invoice</span>
                              </span>
                            </div>
                          </a>
                        </div>
                        <div class="nav-item-wrapper">
                          <a class="nav-link label-1" href="<?=Url::to(['site/account-receivable-list'])?>" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-file-invoice-dollar"></span></span>
                              <span class="nav-link-text-wrapper">
                                <span class="nav-link-text">Accounts Receivables</span>
                              </span>
                            </div>
                          </a>
                        </div>
                      </li>
                    <li class="nav-item">
                        <!-- label-->
                        <p class="navbar-vertical-label">User Management
                        </p>
                        <hr class="navbar-vertical-line" />  <div class="nav-item-wrapper">
                          <a class="nav-link label-1" href="<?=Url::to(['site/user-list'])?>" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-users"></span></span>
                              <span class="nav-link-text-wrapper">
                                <span class="nav-link-text">List</span>
                              </span>
                            </div>
                          </a>
                        </div>  <div class="nav-item-wrapper">
                          <a class="nav-link label-1" href="<?=Url::to(['site/user-create'])?>" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-edit"></span></span>
                              <span class="nav-link-text-wrapper">
                                <span class="nav-link-text">Create</span>
                              </span>
                            </div>
                          </a>
                        </div>  <div class="nav-item-wrapper">
                          <a class="nav-link label-1" href="<?=Url::to(['site/update-password'])?>" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-key"></span></span>
                              <span class="nav-link-text-wrapper">
                                <span class="nav-link-text">Update Password</span>
                              </span>
                            </div>
                          </a>
                        </div></li>

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
                    <div class="search-box navbar-top-search-box d-none d-lg-block" data-list='{"valueNames":["title"]}' style="width:25rem;">
                      <form class="position-relative" data-bs-toggle="search" data-bs-display="static">
                        <input class="form-control search-input fuzzy-search rounded-pill form-control-sm" type="search" placeholder="Search..." aria-label="Search" />
                        <span class="fas fa-search search-box-icon"></span>

                      </form>
                      <div class="btn-close position-absolute end-0 top-50 translate-middle cursor-pointer shadow-none" data-bs-dismiss="search">
                        <button class="btn btn-link p-0" aria-label="Close"></button>
                      </div>
                      <div class="dropdown-menu border start-0 py-0 overflow-hidden w-100">
                        <div class="scrollbar-overlay" style="max-height: 30rem;">
                          <div class="list pb-3">
                            <h6 class="dropdown-header text-body-highlight fs-10 py-2">24 <span class="text-body-quaternary">results</span></h6>
                            <hr class="my-0" />
                            <h6 class="dropdown-header text-body-highlight fs-9 border-bottom border-translucent py-2 lh-sm">Recently Searched </h6>
                            <div class="py-2"><a class="dropdown-item" href="apps/e-commerce/landing/product-details.html">
                                <div class="d-flex align-items-center">

                                  <div class="fw-normal text-body-highlight title"><span class="fa-solid fa-clock-rotate-left" data-fa-transform="shrink-2"></span> Axles & Suspension</div>
                                </div>
                              </a>
                              <a class="dropdown-item" href="apps/e-commerce/landing/product-details.html">
                                <div class="d-flex align-items-center">

                                  <div class="fw-normal text-body-highlight title"> <span class="fa-solid fa-clock-rotate-left" data-fa-transform="shrink-2"></span> Electrical & Lighting</div>
                                </div>
                              </a>

                            </div>
                            <hr class="my-0" />
                            <h6 class="dropdown-header text-body-highlight fs-9 border-bottom border-translucent py-2 lh-sm">Products</h6>
                            <div class="py-2"><a class="dropdown-item py-2 d-flex align-items-center" href="apps/e-commerce/landing/product-details.html">

                                <div class="flex-1">
                                  <h6 class="mb-0 text-body-highlight title">Reefer Unit Thermostat</h6>
                                  <p class="fs-10 mb-0 d-flex text-body-tertiary"><span class="fw-medium text-body-tertiary text-opactity-85">Thermostat for maintaining temperature control in refrigerated trailers.</span></p>
                                </div>
                              </a>
                              <a class="dropdown-item py-2 d-flex align-items-center" href="apps/e-commerce/landing/product-details.html">

                                <div class="flex-1">
                                  <h6 class="mb-0 text-body-highlight title">Dry Van Side Panels</h6>
                                  <p class="fs-10 mb-0 d-flex text-body-tertiary"><span class="fw-medium text-body-tertiary text-opactity-85">Replacement side panels for dry van trailers.</span></p>
                                </div>
                              </a>

                              <a class="dropdown-item py-2 d-flex align-items-center" href="apps/e-commerce/landing/product-details.html">

                                <div class="flex-1">
                                  <h6 class="mb-0 text-body-highlight title">Air Brake Chamber</h6>
                                  <p class="fs-10 mb-0 d-flex text-body-tertiary"><span class="fw-medium text-body-tertiary text-opactity-85">Standard air brake chamber for commercial trailers.</span></p>
                                </div>
                              </a>

                              <a class="dropdown-item py-2 d-flex align-items-center" href="apps/e-commerce/landing/product-details.html">

                                <div class="flex-1">
                                  <h6 class="mb-0 text-body-highlight title">LED Trailer Tail Light</h6>
                                  <p class="fs-10 mb-0 d-flex text-body-tertiary"><span class="fw-medium text-body-tertiary text-opactity-85">High-visibility LED tail light for trailers, compatible with most models.</span></p>
                                </div>
                              </a>
                            </div>

                          </div>
                          <div class="text-center">
                            <p class="fallback fw-bold fs-7 d-none">No Result Found.</p>
                          </div>
                        </div>
                      </div>
                    </div>
                    <ul class="navbar-nav navbar-nav-icons flex-row">
                      <li class="nav-item">
                        <div class="theme-control-toggle fa-icon-wait px-2">
                          <input class="form-check-input ms-0 theme-control-toggle-input" type="checkbox" data-theme-control="phoenixTheme" value="dark" id="themeControlToggle" />
                          <label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Switch theme" style="height:32px;width:32px;"><span class="icon" data-feather="moon"></span></label>
                          <label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Switch theme" style="height:32px;width:32px;"><span class="icon" data-feather="sun"></span></label>
                        </div>
                      </li>
                      <li class="nav-item dropdown">
                        <a class="nav-link" href="#" style="min-width: 2.25rem" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-bs-auto-close="outside"><span class="d-block" style="height:20px;width:20px;"><span data-feather="bell" style="height:20px;width:20px;"></span></span></a>

                        <div class="dropdown-menu dropdown-menu-end notification-dropdown-menu py-0 shadow border navbar-dropdown-caret" id="navbarDropdownNotfication" aria-labelledby="navbarDropdownNotfication">
                          <div class="card position-relative border-0">
                            <div class="card-header p-2">
                              <div class="d-flex justify-content-between">
                                <h5 class="text-body-emphasis mb-0">Notifications</h5>
                                <button class="btn btn-link p-0 fs-9 fw-normal" type="button">Mark all as read</button>
                              </div>
                            </div>
                            <div class="card-body p-0">
                              <div class="scrollbar-overlay" style="height: 27rem;">
                                <div class="px-2 px-sm-3 py-3 notification-card position-relative read border-bottom">
                                  <div class="d-flex align-items-center justify-content-between position-relative">
                                    <div class="d-flex">
                                      <div class="avatar avatar-m status-online me-3">
                                        <div class="avatar-name rounded-circle"><span>U</span></div>
                                      </div>
                                      <div class="flex-1 me-sm-3">
                                        <h4 class="fs-9 text-body-emphasis">User 1</h4>
                                        <p class="fs-9 text-body-highlight mb-2 mb-sm-3 fw-normal"><span class='me-1 fs-10'>💬</span>First Notification<span class="ms-2 text-body-quaternary text-opacity-75 fw-bold fs-10">10m</span></p>
                                        <p class="text-body-secondary fs-9 mb-0"><span class="me-1 fas fa-clock"></span><span class="fw-bold">10:41 AM </span>August 7,2024</p>
                                      </div>
                                    </div>
                                    <div class="dropdown notification-dropdown">
                                      <button class="btn fs-10 btn-sm dropdown-toggle dropdown-caret-none transition-none" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs-10 text-body"></span></button>
                                      <div class="dropdown-menu py-2"><a class="dropdown-item" href="#!">Mark as unread</a></div>
                                    </div>
                                  </div>
                                </div>
                                <div class="px-2 px-sm-3 py-3 notification-card position-relative unread border-bottom">
                                  <div class="d-flex align-items-center justify-content-between position-relative">
                                    <div class="d-flex">
                                      <div class="avatar avatar-m status-online me-3">
                                        <div class="avatar-name rounded-circle"><span>J</span></div>
                                      </div>
                                      <div class="flex-1 me-sm-3">
                                        <h4 class="fs-9 text-body-emphasis">Employee 2</h4>
                                        <p class="fs-9 text-body-highlight mb-2 mb-sm-3 fw-normal"><span class='me-1 fs-10'>📅</span>Created an event.<span class="ms-2 text-body-quaternary text-opacity-75 fw-bold fs-10">20m</span></p>
                                        <p class="text-body-secondary fs-9 mb-0"><span class="me-1 fas fa-clock"></span><span class="fw-bold">10:20 AM </span>August 7,2024</p>
                                      </div>
                                    </div>
                                    <div class="dropdown notification-dropdown">
                                      <button class="btn fs-10 btn-sm dropdown-toggle dropdown-caret-none transition-none" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs-10 text-body"></span></button>
                                      <div class="dropdown-menu py-2"><a class="dropdown-item" href="#!">Mark as unread</a></div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="card-footer p-0 border-top border-translucent border-0">
                              <div class="my-2 text-center fw-bold fs-10 text-body-tertiary text-opactity-85"><a class="fw-bolder" href="pages/notifications.html">Notification history</a></div>
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
                                <h6 class="mt-2 text-body-emphasis">Admin User</h6>
                              </div>
                            </div>
                            <div class="overflow-auto scrollbar" style="height: 10rem;">
                              <ul class="nav d-flex flex-column mb-2 pb-1">
                                <li class="nav-item"><a class="nav-link px-3 d-block" href="#!"> <span class="me-2 text-body align-bottom" data-feather="user"></span><span>Profile</span></a></li>
                                <li class="nav-item"><a class="nav-link px-3 d-block" href="#!"><span class="me-2 text-body align-bottom" data-feather="pie-chart"></span>Dashboard</a></li>
                                <li class="nav-item"><a class="nav-link px-3 d-block" href="#!"> <span class="me-2 text-body align-bottom" data-feather="settings"></span>Password Update</a></li>

                              </ul>
                            </div>
                            <div class="card-footer p-0 border-top border-translucent pt-2">
                              <div class="px-3"> <a class="btn btn-phoenix-secondary d-flex flex-center w-100" href="<?=Url::to(['site/logout'])?>"> <span class="me-2" data-feather="log-out"> </span>Sign out</a></div>
                              <div class="my-2 text-center fw-bold fs-10 text-body-quaternary"><a class="text-body-quaternary me-1" href="#!">Privacy policy</a>&bull;<a class="text-body-quaternary mx-1" href="#!">Terms</a>&bull;<a class="text-body-quaternary ms-1" href="#!">Cookies</a></div>
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
                        <p class="mb-0 mt-2 mt-sm-0 text-body">2024 &copy; Powered by <a class="mx-1" href="https://b4b.consulting" target="_blank">B4B Consulting</a></p>
                      </div>
                      <div class="col-12 col-sm-auto text-center">
                        <p class="mb-0 text-body-tertiary text-opacity-85">v1.1.0</p>
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


            <div class="offcanvas offcanvas-end settings-panel border-0" id="settings-offcanvas" tabindex="-1" aria-labelledby="settings-offcanvas">
              <div class="offcanvas-header align-items-start border-bottom flex-column border-translucent">
                <div class="pt-1 w-100 mb-6 d-flex justify-content-between align-items-start">
                  <div>
                    <h5 class="mb-2 me-2 lh-sm"><span class="fas fa-palette me-2 fs-8"></span>Theme Customizer</h5>
                    <p class="mb-0 fs-9">Explore different styles according to your preferences</p>
                  </div>
                  <button class="btn p-1 fw-bolder" type="button" data-bs-dismiss="offcanvas" aria-label="Close"><span class="fas fa-times fs-8"> </span></button>
                </div>
                <button class="btn btn-phoenix-secondary w-100" data-theme-control="reset"><span class="fas fa-arrows-rotate me-2 fs-10"></span>Reset to default</button>
              </div>
              <div class="offcanvas-body scrollbar px-card" id="themeController">
                <div class="setting-panel-item mt-0">
                  <h5 class="setting-panel-item-title">Color Scheme</h5>
                  <div class="row gx-2">
                    <div class="col-4">
                      <input class="btn-check" id="themeSwitcherLight" name="theme-color" type="radio" value="light" data-theme-control="phoenixTheme" />
                      <label class="btn d-inline-block btn-navbar-style fs-9" for="themeSwitcherLight"> <span class="mb-2 rounded d-block"><img class="img-fluid img-prototype mb-0" src="design/assets/img/generic/default-light.png" alt=""/></span><span class="label-text">Light</span></label>
                    </div>
                    <div class="col-4">
                      <input class="btn-check" id="themeSwitcherDark" name="theme-color" type="radio" value="dark" data-theme-control="phoenixTheme" />
                      <label class="btn d-inline-block btn-navbar-style fs-9" for="themeSwitcherDark"> <span class="mb-2 rounded d-block"><img class="img-fluid img-prototype mb-0" src="design/assets/img/generic/default-dark.png" alt=""/></span><span class="label-text"> Dark</span></label>
                    </div>
                    <div class="col-4">
                      <input class="btn-check" id="themeSwitcherAuto" name="theme-color" type="radio" value="auto" data-theme-control="phoenixTheme" />
                      <label class="btn d-inline-block btn-navbar-style fs-9" for="themeSwitcherAuto"> <span class="mb-2 rounded d-block"><img class="img-fluid img-prototype mb-0" src="design/assets/img/generic/auto.png" alt=""/></span><span class="label-text"> Auto</span></label>
                    </div>
                  </div>
                </div>



                <div class="setting-panel-item">
                  <h5 class="setting-panel-item-title">Vertical Navbar Appearance</h5>
                  <div class="row gx-2">
                    <div class="col-6">
                      <input class="btn-check" id="navbar-style-default" type="radio" name="config.name" value="default" data-theme-control="phoenixNavbarVerticalStyle" />
                      <label class="btn d-block w-100 btn-navbar-style fs-9" for="navbar-style-default"> <img class="img-fluid img-prototype d-dark-none" src="design/assets/img/generic/default-light.png" alt="" /><img class="img-fluid img-prototype d-light-none" src="assets/img/generic/default-dark.png" alt="" /><span class="label-text d-dark-none"> Default</span><span class="label-text d-light-none">Default</span></label>
                    </div>
                    <div class="col-6">
                      <input class="btn-check" id="navbar-style-dark" type="radio" name="config.name" value="darker" data-theme-control="phoenixNavbarVerticalStyle" />
                      <label class="btn d-block w-100 btn-navbar-style fs-9" for="navbar-style-dark"> <img class="img-fluid img-prototype d-dark-none" src="design/assets/img/generic/vertical-darker.png" alt="" /><img class="img-fluid img-prototype d-light-none" src="assets/img/generic/vertical-lighter.png" alt="" /><span class="label-text d-dark-none"> Darker</span><span class="label-text d-light-none">Lighter</span></label>
                    </div>
                  </div>
                </div>

                <div class="setting-panel-item">
                  <h5 class="setting-panel-item-title">Horizontal Navbar Appearance</h5>
                  <div class="row gx-2">
                    <div class="col-6">
                      <input class="btn-check" id="navbarTopDefault" name="navbar-top-style" type="radio" value="default" data-theme-control="phoenixNavbarTopStyle" />
                      <label class="btn d-inline-block btn-navbar-style fs-9" for="navbarTopDefault"> <span class="mb-2 rounded d-block"><img class="img-fluid img-prototype d-dark-none mb-0" src="design/assets/img/generic/top-default.png" alt=""/><img class="img-fluid img-prototype d-light-none mb-0" src="assets/img/generic/top-style-darker.png" alt=""/></span><span class="label-text">Default</span></label>
                    </div>
                    <div class="col-6">
                      <input class="btn-check" id="navbarTopDarker" name="navbar-top-style" type="radio" value="darker" data-theme-control="phoenixNavbarTopStyle" />
                      <label class="btn d-inline-block btn-navbar-style fs-9" for="navbarTopDarker"> <span class="mb-2 rounded d-block"><img class="img-fluid img-prototype d-dark-none mb-0" src="design/assets/img/generic/navbar-top-style-light.png" alt=""/><img class="img-fluid img-prototype d-light-none mb-0" src="assets/img/generic/top-style-lighter.png" alt=""/></span><span class="label-text d-dark-none">Darker</span><span class="label-text d-light-none">Lighter</span></label>
                    </div>
                  </div>
                </div>
              </div>
            </div><a class="card setting-toggle" href="#settings-offcanvas" data-bs-toggle="offcanvas">
              <div class="card-body d-flex align-items-center px-2 py-1">
                <div class="position-relative rounded-start" style="height:34px;width:28px">
                  <div class="settings-popover"><span class="ripple"><span class="fa-spin position-absolute all-0 d-flex flex-center"><span class="icon-spin position-absolute all-0 d-flex flex-center">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="#ffffff" xmlns="http://www.w3.org/2000/svg">
                      <path d="M19.7369 12.3941L19.1989 12.1065C18.4459 11.7041 18.0843 10.8487 18.0843 9.99495C18.0843 9.14118 18.4459 8.28582 19.1989 7.88336L19.7369 7.59581C19.9474 7.47484 20.0316 7.23291 19.9474 7.03131C19.4842 5.57973 18.6843 4.28943 17.6738 3.20075C17.5053 3.03946 17.2527 2.99914 17.0422 3.12011L16.393 3.46714C15.6883 3.84379 14.8377 3.74529 14.1476 3.3427C14.0988 3.31422 14.0496 3.28621 14.0002 3.25868C13.2568 2.84453 12.7055 2.10629 12.7055 1.25525V0.70081C12.7055 0.499202 12.5371 0.297594 12.2845 0.257272C10.7266 -0.105622 9.16879 -0.0653007 7.69516 0.257272C7.44254 0.297594 7.31623 0.499202 7.31623 0.70081V1.23474C7.31623 2.09575 6.74999 2.8362 5.99824 3.25599C5.95774 3.27861 5.91747 3.30159 5.87744 3.32493C5.15643 3.74527 4.26453 3.85902 3.53534 3.45302L2.93743 3.12011C2.72691 2.99914 2.47429 3.03946 2.30587 3.20075C1.29538 4.28943 0.495411 5.57973 0.0322686 7.03131C-0.051939 7.23291 0.0322686 7.47484 0.242788 7.59581L0.784376 7.8853C1.54166 8.29007 1.92694 9.13627 1.92694 9.99495C1.92694 10.8536 1.54166 11.6998 0.784375 12.1046L0.242788 12.3941C0.0322686 12.515 -0.051939 12.757 0.0322686 12.9586C0.495411 14.4102 1.29538 15.7005 2.30587 16.7891C2.47429 16.9504 2.72691 16.9907 2.93743 16.8698L3.58669 16.5227C4.29133 16.1461 5.14131 16.2457 5.8331 16.6455C5.88713 16.6767 5.94159 16.7074 5.99648 16.7375C6.75162 17.1511 7.31623 17.8941 7.31623 18.7552V19.2891C7.31623 19.4425 7.41373 19.5959 7.55309 19.696C7.64066 19.7589 7.74815 19.7843 7.85406 19.8046C9.35884 20.0925 10.8609 20.0456 12.2845 19.7729C12.5371 19.6923 12.7055 19.4907 12.7055 19.2891V18.7346C12.7055 17.8836 13.2568 17.1454 14.0002 16.7312C14.0496 16.7037 14.0988 16.6757 14.1476 16.6472C14.8377 16.2446 15.6883 16.1461 16.393 16.5227L17.0422 16.8698C17.2527 16.9907 17.5053 16.9504 17.6738 16.7891C18.7264 15.7005 19.4842 14.4102 19.9895 12.9586C20.0316 12.757 19.9474 12.515 19.7369 12.3941ZM10.0109 13.2005C8.1162 13.2005 6.64257 11.7893 6.64257 9.97478C6.64257 8.20063 8.1162 6.74905 10.0109 6.74905C11.8634 6.74905 13.3792 8.20063 13.3792 9.97478C13.3792 11.7893 11.8634 13.2005 10.0109 13.2005Z" fill="#2A7BE4"></path>
                    </svg></span></span></span></div>
                  </div><small class="text-uppercase text-body-tertiary fw-bold py-2 pe-2 ps-1 rounded-end">customize</small>
                </div>
              </a>

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
              <script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
              <script src="design/vendors/list.js/list.min.js"></script>
              <script src="design/vendors/feather-icons/feather.min.js"></script>
              <script src="design/vendors/dayjs/dayjs.min.js"></script>
              <script src="design/vendors/leaflet/leaflet.js"></script>
              <script src="design/vendors/leaflet.markercluster/leaflet.markercluster.js"></script>
              <script src="design/vendors/leaflet.tilelayer.colorfilter/leaflet-tilelayer-colorfilter.min.js"></script>
              <script src="design/assets/js/phoenix.js"></script>
              <script src="design/vendors/echarts/echarts.min.js"></script>
              <script src="design/assets/js/echarts-example.js"></script>
              <script src="design/assets/js/ecommerce-dashboard.js"></script>
            </body>
            </html>
            <?php $this->endPage() ?>
