<?php
use app\models\TblOwnershipCompany;
$ownership_companies = TblOwnershipCompany::find()->where(['status'=>1])->orderBy(['company_name'=>SORT_ASC])->all();
 ?>
 <?php
 use yii\helpers\Html;
 use yii\helpers\Url;
  ?>
<form id="client-statement-form">
    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />

    <input type="hidden" name="client_id" value="<?= $client_id ?>">

    <div class="row g-3">
        <div class="col-md-4">
            <label>From Date</label>
            <input type="text" name="from_date" id="client-from-date" class="form-control" required>
        </div>

        <div class="col-md-4">
            <label>To Date</label>
            <input type="text" name="to_date" id="client-to-date" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label for="">Select Company</label>
          <select class="form-select" name="ownership_company" required>
            <option value="">Select</option>
            <?php
              if(isset($ownership_companies) && count($ownership_companies) > 0){
                foreach($ownership_companies as $oc){
                  echo '<option value="'.$oc->id.'">'.$oc->location_name.'</option>';
                }
              }
             ?>
          </select>
        </div>

    </div>

    <div class="mt-3 text-end">
        <button class="btn btn-primary">Generate</button>
    </div>
</form>
<?php
    $this->registerJs('
    $("#client-from-date").flatpickr({
      disableMobile: "true",
      dateFormat: "Y-m-d"
    });
    $("#client-to-date").flatpickr({
      disableMobile: "true",
      dateFormat: "Y-m-d"
    });

    ');
 ?>
<script>
$('#client-statement-form').on('submit', function (e) {
    e.preventDefault();

    $('#customerStatementModalContent').html('Loading...');

    $.post(
        'index.php?r=client/generate-statement',
        $(this).serialize(),
        function (html) {
            $('#customerStatementModalContent').html(html);
        }
    );
});
</script>
