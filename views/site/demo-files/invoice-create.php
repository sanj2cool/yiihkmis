<?php
$this->title = "Create Invoice";
 ?>
 <div class="card shadow-none border border-300 my-4" data-component-card="data-component-card">
   <div class="card-header p-4 border-bottom border-300 bg-soft">
     <div class="row g-3 justify-content-between align-items-center">
       <div class="col-12 col-md">
         <h4 class="text-900 mb-0" data-anchor="data-anchor">Basic Details</h4>
       </div>
     </div>
   </div>
   <div class="card-body p-4">
     <div class="row">
       <div class="col-md-4">
         <label for="">Customer</label>
         <select class="form-control" name="">
           <option value="">Select</option>
         </select>
       </div>
       <div class="col-md-4">
         <label for="">Customer Email</label>
         <input type="text" class="form-control" value="">
       </div>
       <div class="col-md-4">
         <label for="">Invoice #</label>
         <input type="text" class="form-control" value="">
       </div>
     </div>
     <div class="row mt-3">
       <div class="col-md-4">
         <label for="">Terms</label>
         <select class="form-control" name="">
           <option value="">Select</option>
         </select>
       </div>
       <div class="col-md-4">
         <label for="">Invoice Date</label>
         <input type="text" class="form-control" value="">
       </div>
       <div class="col-md-4">
         <label for="">Due Date</label>
         <input type="text" class="form-control" value="">
       </div>
     </div>


   </div>
 </div>
 <div class="card shadow-none border border-300 my-4" data-component-card="data-component-card">
   <div class="card-header p-4 border-bottom border-300 bg-soft">
     <div class="row g-3 justify-content-between align-items-center">
       <div class="col-12 col-md">
         <h4 class="text-900 mb-0" data-anchor="data-anchor">Add Items</h4>
       </div>
     </div>
   </div>
   <div class="card-body p-4">
     <div class="row">
       <div class="col-lg-2">
         <label for="">Type</label>
         <select class="form-control" name="">
           <option value="">Custom Item</option>
           <option value="">Parts</option>
         </select>
       </div>
       <div class="col-lg-2">
         <label for="">Item</label>
         <input class="form-control" type="text" value="">
       </div>
       <div class="col-lg-3">
         <label for="">Description</label>
         <input class="form-control" type="text" value="">
       </div>
       <div class="col-lg-1">
         <label for="">Qty</label>
         <input class="form-control" type="text" value="">
       </div>
       <div class="col-lg-1">
         <label for="">Rate</label>
         <input class="form-control" type="text" value="">
       </div>
       <div class="col-lg-1">
         <label for="">Amount</label>
         <input class="form-control" type="text" value="">
       </div>
       <div class="col-lg-1">
         <label for="">Act.</label><br />
         <button type="button" name="button" class="btn btn-primary btn-sm">
           <i class="fas fa-plus"></i>
         </button>
       </div>
     </div>
     </div>
     </div>
   <div class="card shadow-none border border-300 my-4" data-component-card="data-component-card">
     <div class="card-body p-4">
       <div class="row">
         <div class="col-md-4">
           <label for="">Message on Invoice</label>
           <textarea class="form-control" rows="4"></textarea>
         </div>
         <div class="col-md-4">
           <label for="">Terms</label>
           <textarea class="form-control" rows="4"></textarea>
         </div>
           <div class="col-4 mt-2 text-end">
       <div class="row g-3" style="font-weight:600;">
         <div class="col-8 mt-3">
           Subtotal
         </div>
         <div class="col-4 mt-3" id="subtotal_calc">$10297.56</div>
       </div>
       <div class="row g-3">
         <div class="col-6 mt-4 pe-1">
           <div class="form-group field-tblinvoice-discount_type">

             <select id="tblinvoice-discount_type" class="form-select form-select-sm" name="TblInvoice[discount_type]">
             <option value="1">Discount Value</option>
             <option value="2">Discount Percentage</option>
             </select>

             <div class="help-block"></div>
             </div>            </div>
                         <div class="col-2 mt-4 ps-1">
                           <div class="form-group field-tblinvoice-discount_value">

             <input type="text" id="tblinvoice-discount_value" class="form-control form-control-sm floatNumberField" name="TblInvoice[discount_value]" value="0.00" onkeyup="checkDec(this);">

             <div class="help-block"></div>
             </div>
           </div>
         <div class="col-4 mt-4" id="discount_calc">-$0.00</div>
       </div>
       <div class="row g-3" id="hst_13">
                         <div class="col-8 mt-4" id="hst_val_13">HST @ 13% on 10297.56<br></div>
             <div class="col-4 mt-4" id="hst_calc_13">1338.68<br></div>

       </div>
       <div class="row g-3" style="font-weight:600;">
         <div class="col-8 mt-3">
           Total
         </div>
         <div class="col-4 mt-3" id="total_calc">$11636.24</div>
       </div>
       <input type="hidden" id="amount_paid" value="0.00">
                 <div class="row g-3" style="font-weight:600;">
         <div class="col-8 mt-3">
           Balance Due
         </div>
         <div class="col-4 mt-3" id="balance_due">$11636.24</div>
       </div>
     </div>
       </div>
       </div>
       </div>
       <div class="card shadow-none border border-300 my-4" data-component-card="data-component-card">
         <div class="card-header p-4 border-bottom border-300 bg-soft">
           <div class="row g-3 justify-content-between align-items-center">
             <div class="col-12 col-md">
               <h4 class="text-900 mb-0" data-anchor="data-anchor">Attachments</h4>
             </div>
           </div>
         </div>
         <div class="card-body p-4">
           <div class="row">
             <div class="col-md-3">
               <label for="">Upload Attachments</label>
               <input type="file" class="form-control" value="">
             </div>
           </div>
           </div>
           </div>



           <div class="card shadow rounded p-3 mt-4">
               <div class="row text-center">
                         <div class="d-grid gap-2 col-4 mx-auto pe-1">
                     <input type="submit" name="new_update" value="Create &amp; Edit" class="btn btn-info btn-sm">
                   </div>
                   <div class="d-grid gap-2 col-4 mx-auto pe-1 ps-1">
                     <input type="submit" name="new_new" value="Create &amp; View" class="btn btn-primary btn-sm">
                   </div>
                   <div class="d-grid gap-2 col-4 mx-auto ps-1">
                     <input type="submit" name="new_exit" value="Create &amp; Exit" class="btn btn-secondary btn-sm">
                   </div>

               </div>
             </div>
