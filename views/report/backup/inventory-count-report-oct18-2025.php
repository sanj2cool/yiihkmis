<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use app\models\TblProduct;
use app\models\TblProductAllotment;
use app\models\TblProductReceiving;
use app\models\TblBin;
use app\models\TblBinArea;
use app\models\TblBinBay;
use app\models\TblBinLevel;
use app\models\TblBinPosition;
use app\models\TblBinRow;
use app\models\TblProductCategory;
use app\models\TblBinLocations;
use yii\db\Expression;
use yii\db\Query;
/** @var yii\web\View $this */
/** @var app\models\TruckInventoriesSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Inventory Count Report';
$this->params['breadcrumbs'][] = $this->title;

$statusarr = [1=>"Active",2=>"Inactive"];
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
    <form name="frm_reservation" id="" action="<?= Yii::$app->urlManager->createUrl(['report/inventory-count-report']) ?>" method="post" autocomplete="off">
      <input type="hidden" name="_csrf-backend" value="<?=Yii::$app->request->getCsrfToken()?>" />
      <div class="row justify-content-center">
        <div class="col-lg-3">
          <label for="">Item Category</label>
          <select class="form-control" name="item_category" id="item_category" required>
            <option value="">Select</option>
            <?php
            $getcats = TblProductCategory::find()->where(['status'=>1])->orderBy(['title'=>SORT_ASC])->all();
            foreach($getcats as $gc){
              if(isset($item_category) && $item_category == $gc->id){
                  echo '<option value="'.$gc->id.'" selected="selected">'.$gc->title.'</option>';
              }else{
                  echo '<option value="'.$gc->id.'">'.$gc->title.'</option>';
              }
            }
            ?>
          </select>
        </div>
        <div class="col-md-2 mt-4">
          <button type="submit" class="btn btn-primary btn-sm" name="" id="">Search</button>
          <a href="<?=Url::to(['report/inventory-count-report'])?>"><button type="button" class="btn btn-danger btn-sm" name="" id="">Reset</button></a>
        </div>
      </div>
    </form>

    <hr />

    <div class="truck-inventories-index table-responsive">
      <table id="example_inv" class="table table-striped table-bordered display table-hover">
        <thead>
          <tr>
            <th>Part Name</th>
            <th>Description</th>
            <th>Category</th>
            <th>SKU</th>
            <th>Current Qty</th>
            <th>Bin Location</th>
          </tr>
          <tr>
            <td class="filterhead">Part Name</td>
            <td class="filterhead">Description</td>
            <td class="filterhead">Category</td>
            <td class="filterhead">SKU</td>
            <td class="filterhead">Current Qty</td>
            <td class="filterhead">Bin Location</td>
          </tr>
        </thead>
        <tbody>
          <?php
          $query_str = "";

          if(isset($item_category) && $item_category != ""){
            $query_str = 'fk_category_id = '.$item_category;
            $query = (new Query())
            ->select([
              'p.name AS product_name',
              'p.id AS product_id',
              'p.description AS description',
              'p.sku AS product_sku',
              'pc.title AS product_category',
              '(IFNULL(SUM(pr.no_of_items), 0) - IFNULL(SUM(pa.no_of_items), 0)) AS current_qty',
              'GROUP_CONCAT(DISTINCT pr.bin_location SEPARATOR ", ") AS product_location'
            ])
            ->from('tbl_product p')
            ->leftJoin('tbl_product_category pc', 'p.fk_category_id = pc.id')
            ->leftJoin('tbl_product_receiving pr', 'p.id = pr.fk_product_id AND pr.status != 0')
            ->leftJoin('tbl_product_allotment pa', 'p.id = pa.fk_product_id AND pa.status != 0')
            ->where('p.status != 0')
            ->andWhere($query_str)
            ->groupBy('p.id')
            ->orderBy(['current_qty' => SORT_DESC]);
          }else{
            $query = (new Query())
            ->select([
              'p.name AS product_name',
              'p.id AS product_id',
              'p.description AS description',
              'p.sku AS product_sku',
              'pc.title AS product_category',
              '(IFNULL(SUM(pr.no_of_items), 0) - IFNULL(SUM(pa.no_of_items), 0)) AS current_qty',
              'GROUP_CONCAT(DISTINCT pr.bin_location SEPARATOR ", ") AS product_location'
            ])
            ->from('tbl_product p')
            ->leftJoin('tbl_product_category pc', 'p.fk_category_id = pc.id')
            ->leftJoin('tbl_product_receiving pr', 'p.id = pr.fk_product_id AND pr.status != 0')
            ->leftJoin('tbl_product_allotment pa', 'p.id = pa.fk_product_id AND pa.status != 0')
            ->where('p.status != 0')
            ->groupBy('p.id')
            ->orderBy(['current_qty' => SORT_DESC]);
          }




          // Execute the query and get the result
          $products = $query->all();
          // print_r($products);
          if(isset($products) && count($products) > 0){
            foreach($products as $p){
              // echo $p['product_name'];
              // echo $p['product_sku'];
              // $bin_locations = $p['product_location'];
              //----check if comma separated ---
              $bin_locs = $p['product_location'];
              $locations = $p['product_location'];

              echo '<tr>
              <td>'.$p['product_name'].' <a class="unformat text-sm" href="'.Url::to(['product/update','id'=>$p['product_name']]).'" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a></td>
              <td>'.$p['description'].'</td>
              <td>'.$p['product_category'].'</td>
              <td>'.$p['product_sku'].'</td>
              <td>'.$p['current_qty'].'</td>
              <td>'.$bin_locs.'</td>
              </tr>';
            }
          }//-----if isset ended ------


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
  var table = $('#example_inv').DataTable({
    dom: 'Bfrtip',
    buttons: [
      'copy', 'csv', 'excel', 'pdf', 'print'
    ],
    columnDefs: [{
      targets: \"_all\",
      orderable: true
    }],
    pageLength: 20,
    orderCellsTop: true,
    stateSave: true
  });
  $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1 btn-sm');
  $('#example_inv thead .filterhead').each( function () {
    var title = $(this).text();
    $(this).html( '<input type=\"text\" class=\"column_search form-control\"/>' );
  } );
  // Apply the search
  $( '#example_inv thead'  ).on( 'keyup', '.column_search',function () {
    table
    .column( $(this).parent().index() )
    .search( this.value )
    .draw();
  } );
  // Restore state
  var state = table.state.loaded();
  if ( state ) {

    $('#example_inv thead .filterhead').each( function () {
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
<?php
$this->registerJs('
  // Initialize Choices.js
  //--------------create client code starts -----------------------
  const element = document.getElementById("item_category");
  const choices = new Choices(element, {
    removeItemButton: true,
    placeholder: true
  });
');
 ?>
