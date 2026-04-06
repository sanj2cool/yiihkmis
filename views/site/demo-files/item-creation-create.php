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
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Item No.</label>
           <input class="form-control" type="text" value="">
         </div>
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Item Name</label>
           <input class="form-control" type="text" value="">
         </div>
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Category</label>
           <select class="form-control" name="">
             <option value="">Select</option>
             <option value="">Cat1</option>
             <option value="">Cat2</option>
           </select>
         </div>
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Status</label>
           <select class="form-control" name="">
             <option value="">Select</option>
             <option value="">Active</option>
             <option value="">Inactive</option>
           </select>
         </div>
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Reorder Qty</label>
           <input class="form-control" type="text" value="">
         </div>
         <div class="col-md-9">
           <label class="form-label" for="inputEmail4">Description</label>
           <textarea name="name" rows="8" class="form-control"></textarea>
         </div>

       </div>
     </div>
   </div>


   <div class="card shadow-none border border-300 my-4" data-component-card="data-component-card">
     <div class="card-header p-4 border-bottom border-300 bg-soft">
       <div class="row g-3 justify-content-between align-items-center">
         <div class="col-12 col-md">
           <h4 class="text-900 mb-0" data-anchor="data-anchor">Cross Reference</h4>
         </div>
       </div>
     </div>
     <div class="card-body p-4">

       <div class="row">
         <div class="col-lg-4">
           <label for="">Item Name</label>
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-lg-4 mt-4">
           <button type="button" name="button" class="btn btn-primary btn-sm">
             <i class="fas fa-plus"></i>
           </button>
           <button type="button" name="button" class="btn btn-primary btn-sm">
             <i class="fas fa-minus"></i>
           </button>
         </div>
       </div>

       <div class="row mt-2">
         <div class="col-lg-4">
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-lg-4">
           <button type="button" name="button" class="btn btn-primary btn-sm">
             <i class="fas fa-plus"></i>
           </button>
           <button type="button" name="button" class="btn btn-primary btn-sm">
             <i class="fas fa-minus"></i>
           </button>
         </div>
       </div>
       <div class="row mt-2">
         <div class="col-lg-4">
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-lg-4">
           <button type="button" name="button" class="btn btn-primary btn-sm">
             <i class="fas fa-plus"></i>
           </button>
           <button type="button" name="button" class="btn btn-primary btn-sm">
             <i class="fas fa-minus"></i>
           </button>
         </div>
       </div>

     </div>
   </div><!-- Cross reference ended -->


   <div class="card shadow-none border border-300 my-4" data-component-card="data-component-card">
     <div class="card-header p-4 border-bottom border-300 bg-soft">
       <div class="row g-3 justify-content-between align-items-center">
         <div class="col-12 col-md">
           <h4 class="text-900 mb-0" data-anchor="data-anchor">Alternate Products</h4>
         </div>
       </div>
     </div>
     <div class="card-body p-4">
       <div class="row">
         <div class="col-lg-4">
           <label for="">Item Name</label>
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-lg-4 mt-4">
           <button type="button" name="button" class="btn btn-primary btn-sm">
             <i class="fas fa-plus"></i>
           </button>
           <button type="button" name="button" class="btn btn-primary btn-sm">
             <i class="fas fa-minus"></i>
           </button>
         </div>
       </div>

       <div class="row mt-2">
         <div class="col-lg-4">
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-lg-4">
           <button type="button" name="button" class="btn btn-primary btn-sm">
             <i class="fas fa-plus"></i>
           </button>
           <button type="button" name="button" class="btn btn-primary btn-sm">
             <i class="fas fa-minus"></i>
           </button>
         </div>
       </div>
       <div class="row mt-2">
         <div class="col-lg-4">
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-lg-4">
           <button type="button" name="button" class="btn btn-primary btn-sm">
             <i class="fas fa-plus"></i>
           </button>
           <button type="button" name="button" class="btn btn-primary btn-sm">
             <i class="fas fa-minus"></i>
           </button>
         </div>
       </div>
     </div>
   </div><!--- Alternate products ended -->


   <div class="card shadow-none border border-300 my-4" data-component-card="data-component-card">
     <div class="card-body p-2">
       <div class="row">
         <div class="col-12" align="center">
           <button class="btn btn-primary w-100" type="submit">Create Item</button>
         </div>
       </div>
     </div>
   </div>
 </form>
