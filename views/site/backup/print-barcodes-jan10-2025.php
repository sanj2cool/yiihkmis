<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\TblProduct;
use app\models\TblPrintedBarcodes;
use yii\helpers\ArrayHelper;
//-----get products --------
$allbins = TblProduct::find()->where(['status'=>1])->andWhere('id not in (select fk_product_id from tbl_printed_barcodes WHERE status = 1)')->all();
// $truckarr = ArrayHelper::map($alltrucks,'id','unit_no');
$binarr = ArrayHelper::map($allbins, 'id', function($model) {
  // return $model->unit_no . ' | Plate No. ' . $model->plate;
  return $model->name.' - '.$model->sku.' - '.$model->internal_sku;
});
// print_r($binarr);
$this->title = "Print Barcodes";

use yii\helpers\Url;
use app\models\TblMenu;
//=======get the mennu items with parent_id = 18 =======
//====order by priority =======
$getmenuitems = TblMenu::find()->where(['parent_id'=>18,'status'=>1])->orderBy(['priority'=>SORT_ASC])->all();
//======current menu id is 19=======
if(isset($getmenuitems) && count($getmenuitems) > 0){
  echo '<nav aria-label="breadcrumb mb-2">
  <ol class="breadcrumb mb-0">';
  foreach($getmenuitems as $gm){
    if($gm->id == 70){
      echo ' <li class="breadcrumb-item active" aria-current="page">'.$gm->title.'</li>';
    }else{
      echo '<li class="breadcrumb-item"><a href="'.Url::to([$gm->url]).'">'.$gm->title.'</a></li>';
    }
  }
  echo '  </ol>
</nav>';
}//======if isset ended ========
?>
    <style>
    @page {
            size: 21.6cm 27.9cm;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
        }

        .label-sheet {
            width: 21.6cm;
            height: 27.9cm;
            margin: 0;
            padding-top: 1.2cm;
            padding-left: 0.4cm;
            box-sizing: border-box;
            display: flex;
            flex-wrap: wrap;
            align-content: flex-start;
        }

        .label {
            width: 10.1cm;
            height: 2.55cm;
            border: 1px solid grey;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #000;
            box-sizing: border-box;
            margin: 0;
            font-size:35px;
            font-weight:900;
        }

        .label:nth-child(2n) {
            margin-left: 0.4cm; /* Space between columns */
        }

        .no-print {
            margin: 20px;
            text-align: center;
        }

        @media print {
            body * {
                visibility: hidden;
            }
            .label-sheet, .label-sheet * {
                visibility: visible;
            }
            .label-sheet {
                position: absolute;
                left: 0;
                top: 0;
                width: 21.6cm;
                height: 27.9cm;
            }
            .no-print {
                display: none;
            }
        }
       </style>
       <div id="msg">


           <div class="alert alert-outline-success d-flex align-items-center hidden" role="alert" id="success_msg">
             <span class="fas fa-check-circle text-success fs-5 me-3"></span>
             <p class="mb-0 flex-1">Printed barcodes removed successfully.</p>

             <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
           </div>

           <div class="alert alert-outline-danger d-flex align-items-center hidden" role="alert" id="error_msg">
             <span class="fas fa-times-circle text-danger fs-5 me-3"></span>
             <p class="mb-0 flex-1">Something went wrong. Please try again.</p>
             <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
           </div>


       </div>
       <div class="card shadow rounded mt-2">
      <div class="card-body p-3">
          <div class="row">
              <div class="col-lg-6">
                  <h3>Generate Printable Barcodes</h3>
              </div>
              <div class="col-lg-6 text-end">
                    <button type="button" class="btn btn-danger btn-sm hidden" id="btn_remove">Remove the Printed Barcodes from the List</button>

                  <button id="printButton" class="btn btn-primary btn-sm">Print Barcodes</button>
              </div>
          </div>
      </div>
  </div>

  <div class="card shadow rounded mt-2 mb-2">
      <div class="card-body p-3">
          <div class="container">
              <form id="labelForm" class="mb-4">
                  <div class="mb-3">
                      <label for="labelSelect" class="form-label">Select 20 options:</label>
                      <select id="labelSelect" class="form-select" multiple required>
                          <?php
                          foreach($binarr as $key => $value){
                              echo '<option value="'.$key.'">'.$value.'</option>';
                          }
                          ?>
                      </select>
                  </div>
                  <button type="submit" class="btn btn-primary btn-sm">Create Labels</button>
              </form>

              <div class="row">
                <div class="col-12">
                  <p class="text-danger text-center fw-bold">
                    Please Select Paper Size - A4, Margin Settings - None and Scale - Default when printing the labels.
                    <!-- Check out this <a target="_blank" href="img/printer-settings-label.png">image</a> for reference. -->
                  </p>
                </div>
              </div>

          </div>
      </div>
  </div>
  <div id="print_content_container">
      <div id="labelSheetContainer" class="label-sheet"></div>
  </div>


  <script>

</script>
<?php
  $this->registerJs("

      const labelSelect = new Choices('#labelSelect', {
          removeItemButton: true,
          maxItemCount: 20,
          searchEnabled: true,
          searchResultLimit: -1,
          renderChoiceLimit: -1,
          shouldSort: false
      });
      document.getElementById('labelForm').addEventListener('submit', function(event) {
             event.preventDefault();
             $('#btn_remove').removeClass('hidden');
             const labelSheetContainer = document.getElementById('labelSheetContainer');
             labelSheetContainer.innerHTML = ''; // Clear previous labels

             // Log selected options to understand their structure
             console.log('Selected Options:', labelSelect.getValue());

             const selectedOptions = labelSelect.getValue();
             selectedOptions.forEach(option => {
                 console.log('Option:', option);
                 // Handle different formats
                 const labelText = option.label || option.value || option; // Try different properties or default to option itself
                 let stringToArray = labelText.split('-');
                 let arrayLength = stringToArray.length;
                 let position = parseInt(arrayLength)-parseInt(1);
                 let internal_sku = stringToArray[position].trim();

                 const labelDiv = document.createElement('div');

                 labelDiv.classList.add('label');

                 // Create a div to wrap the barcode image and the text
                const barcodeContainer = document.createElement('div');
                barcodeContainer.style.textAlign = 'center'; // Optional: center align the content



                // Create an image element for the barcode
                const barcodeImage = document.createElement('img');
                barcodeImage.src = 'product-barcodes/'+internal_sku+'.png'; // Construct the image URL based on the internal_sku
                barcodeImage.alt = 'Barcode';
                barcodeImage.style.display = 'block'; // Ensure the image is displayed as a block element

                // Create a div for the human-readable text below the barcode
                const textDiv = document.createElement('div');
                textDiv.style.fontFamily = 'monospace';
                textDiv.style.fontSize = '16px';
                textDiv.style.marginTop = '5px';
                textDiv.textContent = internal_sku; // Set the internal SKU as the human-readable text

                // Append the barcode image and the text to the label div
                barcodeContainer.appendChild(barcodeImage);
                barcodeContainer.appendChild(textDiv);


                // Append the barcode container to the labelDiv
                labelDiv.appendChild(barcodeContainer);

                // Append the labelDiv to the labelSheetContainer or wherever you're placing it
                labelSheetContainer.appendChild(labelDiv);





             });
             // Add background color and text alignment
             const labelSheetOuter = document.getElementById('print_content_container');
             labelSheetOuter.style.backgroundColor = '#fff';
          // labelSheetContainer.style.textAlign = 'center';
         });
      document.getElementById('printButton').addEventListener('click', function() {
          window.print(); // This will print only the labelSheetContainer
      });
      // Function to remove selected choices and reinitialize
    function removeSelectedChoices() {
      // Remove selected choices
         labelSelect.removeActiveItems();

         // Make an AJAX call to get refreshed data from the server
         fetch('index.php?r=site/get-barcodes')  // Replace with your actual endpoint
             .then(response => response.json())
             .then(data => {
                 // Assuming `data` is an array of objects like [{ value: '1', label: 'Option 1' }, { value: '2', label: 'Option 2' }]


                 console.log(data);
                 let obj = JSON.parse(data);
                 // Clear the current choices
                 labelSelect.clearChoices();

                 // Add new choices from the PHP response
                 labelSelect.setChoices(obj, 'value', 'label', false);

             })
             .catch(error => {
                 console.error('Error fetching data:', error);
             });
    }

    $('#btn_remove').click(function(e){
      e.preventDefault();
       if (confirm('Are you sure you want to remove the printed labels?') == true) {
         let selected_labels = $('#labelSelect').val();
         console.log(selected_labels);
         $.post('index.php?r=site/remove-barcodes-from-list',{labels:selected_labels},function(r){
           // console.log(r);
           if(r == 'success'){
             // alert('Printed labels removed successfully');
             removeSelectedChoices();
             $('#btn_remove').addClass('hidden');
             $('#success_msg').removeClass('hidden');
           }else{
             // alert('Something went wrong. Please try again.');
             $('#error_msg').removeClass('hidden');
           }
         });
       }

    });
  ");
 ?>
