<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\TblBin;
use app\models\TblBinArea;
use app\models\TblBinBay;
use app\models\TblBinLevel;
use app\models\TblBinPosition;
use app\models\TblBinRow;
use yii\helpers\ArrayHelper;
//-----get bins --------
$allbins = TblBin::find()->where(['status'=>1])->all();
$binarr = ArrayHelper::map($allbins, 'id', function($model) {
  //-------get area ------
  $getarea = TblBinArea::find()->where(['status'=>1,'id'=>$model->fk_area_id])->one();
  if(isset($getarea) && $getarea->title != ""){
    $area = $getarea->title;
  }else{
    $area = "";
  }
  //---------get row ------
  $getrow = TblBinRow::find()->where(['status'=>1,'id'=>$model->fk_row_id])->one();
  if(isset($getrow) && $getrow->title != ""){
    $row = $getrow->title;
  }else{
    $row = "";
  }
  //---------get bay -------
  $getbay = TblBinBay::find()->where(['status'=>1,'id'=>$model->fk_bay_id])->one();
  if(isset($getbay) && $getbay->title != ""){
    $bay = $getbay->title;
  }else{
    $bay = "";
  }
  //---------get level -------
  $getlevel = TblBinLevel::find()->where(['status'=>1,'id'=>$model->fk_level_id])->one();
  if(isset($getlevel) && $getlevel->title != ""){
    $level = $getlevel->title;
  }else{
    $level = "";
  }
  //---------get position -------
  $getposition = TblBinPosition::find()->where(['status'=>1,'id'=>$model->fk_position_id])->one();
  if(isset($getposition) && $getposition->title != ""){
    $position = $getposition->title;
  }else{
    $position = "";
  }
  return $area.' - '.$row.' - '.$bay.' - '.$level.' - '.$position;
});
// print_r($binarr);
$this->title = "Print Labels";
?>
    <style>
    .label-container {
         display: grid;
         grid-template-columns: repeat(3, 1fr); /* 3 labels per row */
         gap: 30px; /* increased space between labels */
         margin: 20px;
     }
     .label {
         border: 1px solid #000;
         padding: 20px;
         text-align: center;
         font-size: 18px;
         height: 100px;
         display: flex;
         justify-content: center;
         align-items: center;
         background-color: #007BFF; /* blue background */
         color: #fff; /* white text */
         font-weight: bold; /* bold text */
         /* white-space: nowrap; /* Prevent text wrapping */ */
     }
     @media print {
         body * {
             visibility: hidden;
         }
         #labelContainer, #labelContainer * {
             visibility: visible;
         }
         #labelContainer {
             position: absolute;
             left: 0;
             top: 0;
             width: 100%;
         }
     }
       </style>

         <div class="card shadow rounded mt-2">
             <div class="card-body p-3">
             <div class="row">
               <div class="col-lg-6">
                 <h3>Generate Printable Labels</h3>
               </div>
               <div class="col-lg-6 text-end">
                     <button id="printButton" class="btn btn-primary btn-sm mt-3">Print Labels</button>
               </div>
             </div>
           </div>
           </div>

       <div class="card shadow rounded mt-2">
         <div class="card-body p-3">

       <div class="container">
               <form id="labelForm" class="mb-4">
                   <div class="mb-3">
                       <label for="labelSelect" class="form-label">Select up to 12 options:</label>
                       <select id="labelSelect" class="form-select" multiple required>
                         <?php
                          foreach($binarr as $key => $value){
                            echo '<option value="'.$key.'">'.$value.'</option>';
                          }
                          ?>
                           <!-- <option value="1">A-B-C-D-E</option>
                           <option value="2">F-G-H-I-J</option>
                           <option value="3">K-L-M-N-O</option>
                           <option value="4">P-Q-R-S-T</option>
                           <option value="5">U-V-W-X-Y</option>
                           <option value="6">Z-A-B-C-D</option>
                           <option value="7">E-F-G-H-I</option>
                           <option value="8">J-K-L-M-N</option>
                           <option value="9">O-P-Q-R-S</option>
                           <option value="10">T-U-V-W-X</option>
                           <option value="11">Y-Z-A-B-C</option>
                           <option value="12">D-E-F-G-H</option> -->
                       </select>
                   </div>
                   <button type="submit" class="btn btn-primary btn-sm">Create Labels</button>
               </form>

               <div id="labelContainer" class="label-container"></div>
           </div>
         </div>
       </div>


           <script>
       document.addEventListener('DOMContentLoaded', function() {
           const labelSelect = new Choices('#labelSelect', {
             removeItemButton: true,        // Allow items to be removed
             maxItemCount: 12,              // Limit the number of selected items to 12
             searchEnabled: true,           // Enable searching within the select list
             searchResultLimit: -1,         // Show all search results without limiting
             renderChoiceLimit: -1,         // Render all available choices
             shouldSort: false              // Keep the original order of options
           });

           document.getElementById('labelForm').addEventListener('submit', function(event) {
               event.preventDefault();
               const labelContainer = document.getElementById('labelContainer');
               labelContainer.innerHTML = ''; // Clear previous labels
               const selectedOptions = labelSelect.getValue(); // Get selected options
               selectedOptions.forEach(option => {
                   const labelDiv = document.createElement('div');
                   labelDiv.classList.add('label');
                   labelDiv.textContent = option.label; // Use the option text for the label
                   labelContainer.appendChild(labelDiv);
               });
           });
           document.getElementById('printButton').addEventListener('click', function() {
              window.print(); // This will print only the labelContainer
          });
       });
   </script>
