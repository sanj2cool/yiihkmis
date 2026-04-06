<?php
  $this->title = "Create Vendor";
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
           <label class="form-label" for="inputEmail4">Company Name</label>
           <input class="form-control" type="text" value="">
         </div>
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Email</label>
           <input class="form-control" type="text" value="">
         </div>
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Phone</label>
         <input class="form-control" type="text" value="">
         </div>
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Contact Name</label>
           <input class="form-control" type="text" value="">
         </div>
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Contact Title</label>
           <input class="form-control" type="text" value="">
         </div>
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Address</label>
           <input class="form-control" type="text" value="">
         </div>
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">City</label>
           <input class="form-control" type="text" value="">
         </div>
         <!-- <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Sites</label>
           <select class="form-control" name="">
             <option value="">Select</option>
             <option value="">Site 1</option>
             <option value="">Site 2</option>
           </select>
         </div> -->
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Status</label>
           <select class="form-control" name="">
             <option value="">Select</option>
             <option value="">Active</option>
             <option value="">Inactive</option>
           </select>
         </div>
       </div>
     </div>
   </div>


   <div class="card shadow-none border border-300 my-4" data-component-card="data-component-card">
     <div class="card-body p-2">
       <div class="row">
         <div class="col-12" align="center">
           <button class="btn btn-primary w-100" type="submit">Create Vendor</button>
         </div>
       </div>
     </div>
   </div>
 </form>
