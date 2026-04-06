<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception$exception */

use yii\helpers\Html;

// $this->title = $name;


/** @var yii\web\View $this */
/** @var yii\base\Exception $exception */

$this->title = $exception->statusCode . ' - ' . Html::encode($exception->getMessage());
?>
<div class="site-error">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= nl2br(Html::encode($exception->getMessage())) ?>
    </p>
    <p>
        If you think this is a server error, please contact support.
    </p>
</div>
<!-- <div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        The above error occurred while the Web server was processing your request.
    </p>
    <p>
        Please contact us if you think this is a server error. Thank you.
    </p>

</div> -->
