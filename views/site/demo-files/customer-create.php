<?php
  $this->title = "Create Customer";
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
           <label class="form-label" for="inputEmail4">Client ID</label>
           <input class="form-control" type="text" value="">
         </div>
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Company Name</label>
           <input class="form-control" type="text" value="">
         </div>
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Contact Number</label>
           <input class="form-control" type="text" value="">
         </div>
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Email ID</label>
           <input class="form-control" type="text" value="">
         </div>
       </div>
       <div class="row mt-3">
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Contact Person</label>
           <input class="form-control" type="text" value="">
         </div>
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Street Address</label>
           <input class="form-control" type="text" value="">
         </div>
         <div class="col-md-2">
           <label class="form-label" for="inputEmail4">City</label>
           <input class="form-control" type="text" value="">
         </div>
         <div class="col-md-2">
           <label class="form-label" for="inputEmail4">Province</label>
           <input class="form-control" type="text" value="">
         </div>
         <div class="col-md-2">
           <label class="form-label" for="inputEmail4">Postal Code</label>
           <input class="form-control" type="text" value="">
         </div>
       </div>
       <div class="row mt-3">
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Website</label>
           <input class="form-control" type="text" value="">
         </div>
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Client Manager</label>
           <input class="form-control" type="text" value="">
         </div>
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Sales Manager</label>
           <input class="form-control" type="text" value="">
         </div>
       </div>
     </div>
   </div>
   <div class="card shadow-none border border-300 my-4" data-component-card="data-component-card">
     <div class="card-header p-4 border-bottom border-300 bg-soft">
       <div class="row g-3 justify-content-between align-items-center">
         <div class="col-12 col-md">
           <h4 class="text-900 mb-0" data-anchor="data-anchor">Payment Details</h4>
         </div>
       </div>
     </div>
     <div class="card-body p-4">
       <div class="row">
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Preferred Payment Method</label>
           <select class="form-control">
             <option value="">Select</option>
           </select>
         </div>
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Terms</label>
           <select class="form-control">
             <option value="">Select</option>
           </select>
         </div>
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Pay Cycle</label>
           <select class="form-control">
             <option value="">Select</option>
           </select>
         </div>
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Credit Limit</label>
           <select class="form-control">
             <option value="">Select</option>
           </select>
         </div>
       </div>
     </div>
     </div>
     <div class="card shadow-none border border-300 my-4" data-component-card="data-component-card">
       <div class="card-header p-4 border-bottom border-300 bg-soft">
         <div class="row g-3 justify-content-between align-items-center">
           <div class="col-12 col-md">
             <h4 class="text-900 mb-0" data-anchor="data-anchor">Notes</h4>
           </div>
         </div>
       </div>
       <div class="card-body p-4">
         <div class="row">
           <div class="col-12">
             <textarea name="name" rows="8" cols="80" class="form-control"></textarea>
           </div>
         </div>
         </div>
         </div>
   <div class="card shadow-none border border-300 my-4" data-component-card="data-component-card">
     <div class="card-body p-2">
       <div class="row">
         <div class="col-12" align="center">
           <button class="btn btn-primary w-100" type="submit">Create Customer</button>
         </div>
       </div>
     </div>
   </div>
 </form>
