<?php
$this->title = "Purchase Invoice Create";
 ?>
 <form>
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
           <label for="">Invoice Number</label>
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-md-4">
           <label for="">Invoice Date</label>
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-md-4">
           <label for="">Due Date</label>
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Purchase Order</label>
           <select class="form-control" name="">
             <option value="">Select</option>
             <option value="">Vendor1</option>
             <option value="">Vendor2</option>
           </select>
         </div>

         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Vendor</label>
           <select class="form-control" name="">
             <option value="">Select</option>
             <option value="">Vendor1</option>
             <option value="">Vendor2</option>
           </select>
         </div>

         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Status</label>
           <select class="form-control" name="">
             <option value="">Select</option>
             <option value="">BIN #1234</option>
             <option value="">BIN #2345</option>
             <option value="">BIN #5566</option>
           </select>
         </div>
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Approved By</label>
           <select class="form-control" name="">
             <option value="">Select</option>
             <option value="">BIN #1234</option>
             <option value="">BIN #2345</option>
             <option value="">BIN #5566</option>
           </select>
         </div>


       </div>
     </div>
   </div>

   <div class="card shadow-none border border-300 my-4" data-component-card="data-component-card">
     <div class="card-header p-4 border-bottom border-300 bg-soft">
       <div class="row g-3 justify-content-between align-items-center">
         <div class="col-12 col-md">
           <h4 class="text-900 mb-0" data-anchor="data-anchor">Item Details</h4>
         </div>
       </div>
     </div>
     <div class="card-body p-4">
       <div class="row">
         <div class="col-md-4">
           <label for="">Item</label>
           <select class="form-control" name="">
             <option value="">Select</option>
             <option value="">Item 1</option>
             <option value="">Item 2</option>
           </select>
         </div>
         <div class="col-md-2">
           <label for="">Qty</label>
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-md-2">
           <label for="">Price per item</label>
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-md-2">
           <label for="">Total Amount</label>
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-md-2 mt-4">
           <button type="button" name="button" class="btn btn-primary btn-sm">
             <i class="fas fa-plus"></i>
           </button>
         </div>
       </div><!-- Item row ended -->
       <div class="row mt-2">
         <div class="col-md-4">
           <select class="form-control" name="">
             <option value="">Select</option>
             <option value="">Item 1</option>
             <option value="">Item 2</option>
           </select>
         </div>
         <div class="col-md-2">
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-md-2">
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-md-2">
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-md-2">
           <button type="button" name="button" class="btn btn-primary btn-sm">
             <i class="fas fa-plus"></i>
           </button>
           <button type="button" name="button" class="btn btn-primary btn-sm">
             <i class="fas fa-minus"></i>
           </button>
         </div>
       </div><!-- Item row ended -->
       <div class="row mt-2">
         <div class="col-md-4">
           <select class="form-control" name="">
             <option value="">Select</option>
             <option value="">Item 1</option>
             <option value="">Item 2</option>
           </select>
         </div>
         <div class="col-md-2">
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-md-2">
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-md-2">
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-md-2">
           <button type="button" name="button" class="btn btn-primary btn-sm">
             <i class="fas fa-plus"></i>
           </button>
           <button type="button" name="button" class="btn btn-primary btn-sm">
             <i class="fas fa-minus"></i>
           </button>
         </div>
       </div><!-- Item row ended -->
       <div class="row mt-2">
         <div class="col-md-4">
           <select class="form-control" name="">
             <option value="">Select</option>
             <option value="">Item 1</option>
             <option value="">Item 2</option>
           </select>
         </div>
         <div class="col-md-2">
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-md-2">
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-md-2">
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-md-2">
           <button type="button" name="button" class="btn btn-primary btn-sm">
             <i class="fas fa-plus"></i>
           </button>
           <button type="button" name="button" class="btn btn-primary btn-sm">
             <i class="fas fa-minus"></i>
           </button>
         </div>
       </div><!-- Item row ended -->

     </div>
   </div>

   <div class="card shadow-none border border-300 my-4" data-component-card="data-component-card">
     <div class="card-body p-4">
       <div class="row">
         <div class="col-md-8">
           <label for="">Remarks</label>
           <textarea name="name" rows="6" class="form-control"></textarea>
         </div>
           <div class="col-md-4 mt-2 text-end">
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
     <div class="card-body p-2">
       <div class="row">
         <div class="col-12" align="center">
           <button class="btn btn-primary w-100" type="submit">Create Purchase Invoice</button>
         </div>
       </div>
     </div>
   </div>
 </form>
