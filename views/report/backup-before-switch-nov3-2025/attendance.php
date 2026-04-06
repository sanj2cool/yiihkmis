<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use app\models\TblAttendance;
use app\models\TblUser;
// use app\models\TblDailydispatch;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TblDailydispatchSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
// echo $startdate."::::::::::::::::".$enddate;
$this->title = 'Attendance Report';
$this->params['breadcrumbs'][] = $this->title;
$session = Yii::$app -> session;
$id = $session['id'];
// $user_details = TblUser::find()->where(['id'=>$id])->one();
// $fk_role_id = $user_details->fk_role_id;
?>

<div class="card">
	<div class="card-header bg-light">
		<div class="row align-items-center">
			<div class="col">
				<h5 class="mb-0"><?= Html::encode($this->title) ?></h5>
			</div>
		</div>
	</div>
	<div class="card-body border-top">
		<div class="tbl-dailydispatch-index">
			<?php // echo $this->render('_search', ['model' => $searchModel]); ?>
			<form name="frm_reservation" id="" action="<?= Yii::$app->urlManager->createUrl(['report/attendance']) ?>" method="post" autocomplete="off">
				<input type="hidden" name="_csrf-backend" value="<?=Yii::$app->request->getCsrfToken()?>" />
				<div class="row">
					<div class="col-md-2">
						<label>Start Date</label>
						<input type="text" class="form-control datetimepicker" placeholder="YYYY-MM-DD" name="startdate" value="<?php if(isset($startdate)){ echo $startdate; }?>" required="required" data-options='{"enableTime":false,"dateFormat":"Y-m-d","disableMobile":true}'/>
					</div>
					<div class="col-md-2">
						<label>End Date</label>
						<input type="text" class="form-control datetimepicker" placeholder="YYYY-MM-DD" name="enddate" value="<?php if(isset($enddate)){ echo $enddate; }?>"  required="required" data-options='{"enableTime":false,"dateFormat":"Y-m-d","disableMobile":true}'/>
					</div>
					<div class="col-md-3">
						<label>User</label>
						<select name="employee" id="employee" class="form-control" required="required">
							<option value="0" <?php if(isset($employee) && $employee == 0){ echo 'selected="selected"';} ?>>All</option>
							<?php
							$staff = TblUser::find()->where(['status'=>1])
							->orderBy(['username'=>SORT_ASC])
							->all();
							foreach($staff as $s){
								if(isset($employee) && $employee == $s->id){
									echo '<option value="'.$s->id.'" selected="selected">'.$s->username.'</option>';
								}else{
									echo '<option value="'.$s->id.'">'.$s->username.'</option>';
								}
							}
							?>
						</select>
					</div>
					<div class="col-md-2 mt-4">
						<button type="submit" class="btn btn-primary btn-md" name="" id="">Search</button>
					</div>
				</div>
			</form>
			<hr />
			<div class="table-responsive">
				<table id="example" class="table table-hover table-bordered display" data-page-length="20">
					<thead>
						<tr>
							<th>Sr.</th>
              <th>User</th>
							<th>Date</th>
							<th>Check-in Time</th>
							<th>Check-out Time</th>
              <th>Total Time Spent</th>
						</tr>
						<tr>
							<td class="filterhead">Sr.</td>
              <td class="filterhead">User</td>
							<td class="filterhead">Date</td>
							<td class="filterhead">In Time</td>
							<td class="filterhead">Out Time</td>
              <td class="filterhead">Total Time Spent</td>
						</tr>
					</thead>
					<tbody>
						<?php
						$counter = 1;
            if(isset($startdate) && isset($enddate) && isset($employee)){
              if($employee == 0){
                //-----all the employees --------
                $model = TblAttendance::find()
                        ->where(['status'=>1])
                        ->andWhere("DATE(date) BETWEEN '".$startdate."' AND '".$enddate."'")
                        ->orderBy(['id'=>SORT_DESC])->all();
              }else{
                //----specific employee ---------
                $model = TblAttendance::find()
                        ->where(['status'=>1])
                        ->andWhere(['fk_user_id'=>$employee])
                        ->andWhere("DATE(date) BETWEEN '".$startdate."' AND '".$enddate."'")
                        ->orderBy(['id'=>SORT_DESC])->all();
              }
            }else{
              //-----no vars are set -----
              $model = TblAttendance::find()->where(['status'=>1])->all();
            }
            $total_hours = 0;
            $total_mins = 0;
						foreach($model as $m){
							$sname = TblUser::find()->where(['id'=>$m->fk_user_id])->one()->username;
              if($m->out_time != ""){
                  // Combine the date and time into a single datetime string
                  $in_datetime_str = $m->date . ' ' . $m->in_time;
                  $out_datetime_str = $m->date . ' ' . $m->out_time;
                  // Create DateTime objects
                  $in_datetime = DateTime::createFromFormat('Y-m-d h:i a', $in_datetime_str);
                  $out_datetime = DateTime::createFromFormat('Y-m-d h:i a', $out_datetime_str);
                  // Calculate the difference
                  $interval = $in_datetime->diff($out_datetime);
                  // Get the difference in hours and minutes
                  $hours = $interval->h;
                  $minutes = $interval->i;
                  // If the out time is on the next day, add 24 hours to the calculation
                  if ($out_datetime < $in_datetime) {
                      $hours += 24;
                  }
                  $total_hours += $hours;
                  $total_mins += $minutes;
              }else{
                $hours = 0;
                $minutes = 0;
              }
							echo '<tr>';
							echo '
							<td>'.$counter.'</td>
              <td>'.$sname.'</td>
							<td>'.$m->date.'</td>
							<td>'.$m->in_time.'</td>
							<td>'.$m->out_time.'</td>
              <td>'.$hours.' Hrs. '.$minutes.' Mins.</td>
							</tr>';
							$counter++;
						}//-------for loop ended--------
            // Convert total minutes to hours if more than 60
            $total_hours += intdiv($total_mins, 60);
            $total_mins = $total_mins % 60;
						?>
					</tbody>
					<tfoot>
						<tr>
							<th>Sr.</th>
              <th>User</th>
							<th>Date</th>
              <th>Check-in Time</th>
							<th>Check-out Time</th>
              <th>Total Time Spent</th>
						</tr>
					</tfoot>
				</table>
			</div>
      <div class="row">
          <div class="col-12 text-center text-danger fw-bold fs-5">
            Total Time : <?=$total_hours?> Hrs. <?=$total_mins?> Mins.
          </div>
      </div>

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
	var table = $('#example').DataTable({
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
	$('#example thead .filterhead').each( function () {
		var title = $(this).text();
		$(this).html( '<input type=\"text\" class=\"column_search form-control\"/>' );
	} );
	// Apply the search
	$( '#example thead'  ).on( 'keyup', '.column_search',function () {
		table
		.column( $(this).parent().index() )
		.search( this.value )
		.draw();
	} );
	// Restore state
	var state = table.state.loaded();
	if ( state ) {

		$('#example thead .filterhead').each( function () {
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
