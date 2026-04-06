<?php
$this->title = "Item Category Create";
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
           <label for="">PO Number</label>
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-md-4">
           <label for="">PO Date</label>
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-md-4">
           <label class="form-label" for="inputEmail4">Vendor</label>
           <select class="form-control" name="">
             <option value="">Select</option>
             <option value="">Vendor1</option>
             <option value="">Vendor2</option>
           </select>
         </div>

         <div class="col-md-4">
           <label class="form-label" for="inputEmail4">Status</label>
           <select class="form-control" name="">
             <option value="">Select</option>
             <option value="">Pending</option>
             <option value="">Partially Received</option>
             <option value="">Received</option>
           </select>
         </div>
         <div class="col-md-4">
           <label class="form-label" for="inputEmail4">Payment Status</label>
           <select class="form-control" name="">
             <option value="">Select</option>
             <option value="">Unpaid</option>
             <option value="">Partially Paid</option>
             <option value="">Paid</option>
           </select>
         </div>
         <div class="col-md-4">
           <label class="form-label" for="inputEmail4">Approved By</label>
           <select class="form-control" name="">
             <option value="">Select</option>
             <option value="">User1</option>
             <option value="">User2</option>
             <option value="">User3</option>
           </select>
         </div>
         <div class="col-md-12">
           <label for="">Remarks</label>
           <textarea name="name" rows="4" class="form-control"></textarea>
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
         <div class="col-md-2">
           <label for="">Item</label>
           <select class="form-control" name="">
             <option value="">Select</option>
             <option value="">Item 1</option>
             <option value="">Item 2</option>
           </select>
         </div>
         <div class="col-md-2">
           <label for="">Qty Ordered</label>
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
         <div class="col-md-2">
           <label for="">Received Qty</label>
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-md-2 mt-4">
           <button type="button" name="button" class="btn btn-primary btn-sm">
             <i class="fas fa-plus"></i>
           </button>
         </div>
       </div><!-- Item row ended -->
       <div class="row mt-2">
         <div class="col-md-2">
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
         <div class="col-md-2">
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
         <div class="col-md-2">
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
     <div class="card-body p-2">
       <div class="row">
         <div class="col-12" align="center">
           <button class="btn btn-primary w-100" type="submit">Create Purchase Order</button>
         </div>
       </div>
     </div>
   </div>
 </form>
