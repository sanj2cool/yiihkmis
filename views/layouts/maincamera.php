<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

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
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <meta name="csrf-token" content="<?= Yii::$app->request->csrfToken ?>">
    <link rel="manifest" href="design/assets/img/favicons/manifest.json">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
    <style>
    #scanner-camera {
           width: 100%;
           max-width: 600px;
           height: 300px; /* Fixed height to ensure it's not too large */
           position: relative;
           overflow: hidden;
           border: 2px solid black;
           background-color: #000; /* Black background for better contrast */
       }

       video {
           width: 100%;
           height: 100%;
           object-fit: contain; /* Keeps the video inside the container */
       }

       #scanner-result {
           margin-top: 20px;
           font-family: monospace;
           font-size: 18px;
           color: green;
           text-align: center;
       }

       #stop-scanner {
           margin-top: 10px;
           padding: 10px 20px;
           font-size: 16px;
           cursor: pointer;
           background-color: red;
           color: white;
           border: none;
           display: block;
           margin-left: auto;
           margin-right: auto;
       }
       #reset-button {
           margin-top: 10px;
           padding: 10px 20px;
           font-size: 16px;
           cursor: pointer;
           background-color: #3874ff;
           color: white;
           border: none;
           display: block;
           margin-left: auto;
           margin-right: auto;
       }
    </style>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

        <?= $content ?>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
