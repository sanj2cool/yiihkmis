<?php
$this->title = "Customers";
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\TblTerms;
use app\models\TblClient;
use app\models\TblUser;
$session = Yii::$app->session;
$user_company = $session['userCompany'];
?>
<div class="card mb-4">
  <div class="card-header bg-light">
    <div class="row align-items-center">
      <div class="col">
        <h5 class="mb-0"><?= Html::encode($this->title) ?></h5>
      </div>
    </div>
  </div>
  <div class="card-body border-top">
    <form name="frm_reservation" id="" action="<?= Yii::$app->urlManager->createUrl(['report/customer-list']) ?>" method="post" autocomplete="off">
      <input type="hidden" name="_csrf-backend" value="<?=Yii::$app->request->getCsrfToken()?>" />
      <div class="row">
        <div class="col-lg-2">
          <label for="">From Date<span class="text-danger">*</span></label>
          <input type="text" class="form-control datetimepicker" required="required" value="<?php if(isset($from_date)){ echo $from_date;} ?>" name="from_date" data-options='{"enableTime":false,"dateFormat":"Y-m-d","disableMobile":true}'>
          </div>
          <div class="col-lg-2">
            <label for="">To Date<span class="text-danger">*</span></label>
            <input type="text" class="form-control datetimepicker" required="required" value="<?php if(isset($to_date)){ echo $to_date;} ?>" name="to_date" data-options='{"enableTime":false,"dateFormat":"Y-m-d","disableMobile":true}'>
          </div>
          <div class="col-md-3">
            <label>Terms<span class="text-danger">*</span></label>
            <select class="form-select" id="terms" name="terms" required="required">
              <option value="">Select</option>
              <option value="0" <?php if(isset($terms)){ if($terms == 0){ echo 'selected="selected"';}}else{ echo 'selected="selected"';}?>>All</option>
              <?php
              $allterms = TblTerms::find()->where(['status'=>1])->all();
              if(isset($allterms) && count($allterms) > 0){
                foreach($allterms as $at){
                  if(isset($terms) && $terms == $at->id){
                    echo '<option value="'.$at->id.'" selected="selected">'.$at->title.'</option>';
                  }else{
                    echo '<option value="'.$at->id.'">'.$at->title.'</option>';
                  }
                }
              }
              ?>
            </select>
          </div>
          <div class="col-md-3">
            <label>Created By<span class="text-danger">*</span></label>
            <select class="form-select" id="crt_by" name="crt_by" required="required">
              <option value="">Select</option>
              <option value="0" <?php if(isset($crt_by)){ if($crt_by == 0){ echo 'selected="selected"';}}else{ echo 'selected="selected"';}?>>All</option>
              <?php
              $allusers = TblUser::find()->where(['status'=>1])->orderBy(['username'=>SORT_ASC])->all();
              if(isset($allusers) && count($allusers) > 0){
                foreach($allusers as $at){
                  if($at->alias != ""){
                    $name = $at->alias;
                  }else{
                    $name = $at->username;
                  }
                  if(isset($crt_by) && $crt_by == $at->id){
                    echo '<option value="'.$at->id.'" selected="selected">'.$name.'</option>';
                  }else{
                    echo '<option value="'.$at->id.'">'.$name.'</option>';
                  }
                }
              }
              ?>
            </select>
          </div>
          <div class="col-md-2 pt-4">
            <button type="submit" class="btn btn-primary">Search</button>
          </div>
        </div>
      </form>
      <hr />
    <div class="table-responsive">
      <table id="customer-list" class="display table table-hover table-bordered" data-page-length="20">
        <thead>
          <tr>
            <th>Act.</th>
            <th>Company Name</th>
            <th>Contact Name</th>
            <th>Phone Number</th>
            <th>Email</th>
            <th>Address</th>
            <th>Terms</th>
            <th>Created By</th>
            <th>Created Time</th>
          </tr>
          <tr>
            <td class="filterhead"></td>
            <td class="filterhead">Company Name</td>
            <td class="filterhead">Contact Name</td>
            <td class="filterhead">Phone Number</td>
            <td class="filterhead">Email</td>
            <td class="filterhead">Address</td>
            <td class="filterhead">Terms</td>
            <td class="filterhead">Created By</td>
            <td class="filterhead">Created Time</td>
          </tr>
        </thead>
        <tbody>
          <?php
          //------get all customers -----
          if(isset($from_date) && isset($to_date) && isset($terms) && isset($crt_by)){
            if($terms == 0 && $crt_by == 0){
              //------all the terms and crt by
              $model = TblClient::find()
              ->where('status != 0')
              ->andWhere("DATE(crt_time) BETWEEN '".$from_date."' AND '".$to_date."'")
              ->andWhere(['fk_location_id'=>$user_company])
              ->orderBy(['id'=>SORT_DESC])->all();
            }else if($terms == 0 && $crt_by != 0){
              $model = TblClient::find()
              ->where('status != 0')
              ->andWhere(['crt_by'=>$crt_by])
              ->andWhere("DATE(crt_time) BETWEEN '".$from_date."' AND '".$to_date."'")
              ->andWhere(['fk_location_id'=>$user_company])
              ->orderBy(['id'=>SORT_DESC])->all();
            }else if($crt_by == 0 && $terms != 0){
              $model = TblClient::find()
              ->where('status != 0')
              ->andWhere(['fk_terms_id'=>$terms])
              ->andWhere("DATE(crt_time) BETWEEN '".$from_date."' AND '".$to_date."'")
              ->andWhere(['fk_location_id'=>$user_company])
              ->orderBy(['id'=>SORT_DESC])->all();
            }else{
              $model = TblClient::find()
              ->where('status != 0')
              ->andWhere(['crt_by'=>$crt_by])
              ->andWhere(['fk_terms_id'=>$terms])
              ->andWhere("DATE(crt_time) BETWEEN '".$from_date."' AND '".$to_date."'")
              ->andWhere(['fk_location_id'=>$user_company])
              ->orderBy(['id'=>SORT_DESC])->all();
            }

          }else{
            $model = TblClient::find()
                    ->where('status != 0')
                    ->andWhere(['fk_location_id'=>$user_company])
                    ->orderBy(['id'=>SORT_DESC])
                    ->all();
          }

          foreach($model as $m){
            $getcrtby = TblUser::find()->where(['id'=>$m->crt_by])->andWhere('status != 0')->one();
            if(isset($getcrtby) && $getcrtby->id != ""){
              if($getcrtby->alias != ""){
                $crt_by = $getcrtby->alias;
              }else{
                $crt_by = $getcrtby->username;
              }
            }else{
              $crt_by = "(not set)";
            }
            $getterms = TblTerms::find()->where(['id'=>$m->fk_terms_id,'status'=>1])->one();
            if(isset($getterms) && $getterms->id != ""){
              $terms = $getterms->title;
            }else{
              $terms = "(not set)";
            }
            echo '<tr>
            <td><a href="'.Url::to(['client/update','id'=>$m->id]).'" title="View" target="_blank">View</a></td>
            <td>'.$m->company_name.'</td>
            <td>'.$m->contact_name.'</td>
            <td>'.$m->phone.'</td>
            <td>'.$m->email.'</td>
            <td>'.$m->address.'</td>
            <td>'.$terms.'</td>
            <td>'.$crt_by.'</td>
            <td>'.date('d-F-Y h:ia',strtotime($m->crt_time)).'</td>
            </tr>';
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php
$this->registerCssFile('https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css');
$this->registerJsFile('https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js',['depends' => [yii\web\JqueryAsset::className()]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js',['depends' => [yii\web\JqueryAsset::className()]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js',['depends' => [yii\web\JqueryAsset::className()]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js',['depends' => [yii\web\JqueryAsset::className()]]);
$this->registerJs("
$(document).ready(function() {
  // DataTable
  var table = $('#customer-list').DataTable({
    dom: 'Bfrtip',
    buttons: [
      'copy', 'csv', 'excel', 'pdf', 'print'
    ],
    columnDefs: [{
      targets: \"_all\",
      orderable: true
    }],
    orderCellsTop: true,
    stateSave: true
  });
  $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1 btn-sm');
  $('#customer-list thead .filterhead').each( function () {
    var title = $(this).text();
    $(this).html( '<input type=\"text\" class=\"column_search form-control\"/>' );
  } );
  // Apply the search
  $( '#customer-list thead'  ).on( 'keyup', '.column_search',function () {
    table
    .column( $(this).parent().index() )
    .search( this.value )
    .draw();
  } );
  // Restore state
  var state = table.state.loaded();
  if ( state ) {

    $('#customer-list thead .filterhead').each( function () {
      var colSearch = state.columns[$(this).index()].search;

      if ( colSearch.search ) {
        $( 'input',this ).val( colSearch.search );
      }
    } );
    table.draw();
  }
});
");
?>
