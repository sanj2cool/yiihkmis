<?php
use yii\helpers\Html;
use yii\helpers\Url;
 ?>
<form id="vendor-statement-form" method="post">
    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />

    <input type="hidden" name="vendor_id" value="<?= $vendor_id ?>">

    <div class="row g-3">
        <div class="col-md-6">
            <label>From Date</label>
            <input type="text" name="from_date" id="statement-from-date" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label>To Date</label>
            <input type="text" name="to_date" id="statement-to-date" class="form-control" required>
        </div>
    </div>

    <div class="mt-3 text-end">
        <button class="btn btn-primary">
            Generate
        </button>
    </div>
</form>
<?php
    $this->registerJs('
    $("#statement-from-date").flatpickr({
      disableMobile: "true",
      dateFormat: "Y-m-d"
    });
    $("#statement-to-date").flatpickr({
      disableMobile: "true",
      dateFormat: "Y-m-d"
    });

    ');
 ?>

<script>
$('#vendor-statement-form').on('submit', function (e) {
    e.preventDefault();

    let form = $(this);

    $('#vendorStatementModalContent').html(
        '<div class="text-center p-4">Generating statement...</div>'
    );

    $.post(
        '<?= Yii::$app->urlManager->createUrl("vendor/generate-statement") ?>',
        form.serialize(),
        function (response) {
            $('#vendorStatementModalContent').html(response);
        }
    );
});
</script>
