<?php
$this->title = "Update Password";
 ?>
 <form>
   <div class="card shadow-none border border-300 my-4" data-component-card="data-component-card">
     <div class="card-header p-4 border-bottom border-300 bg-soft">
       <div class="row g-3 justify-content-between align-items-center">
         <div class="col-12 col-md">
           <h4 class="text-900 mb-0" data-anchor="data-anchor">Password Update</h4>
         </div>
       </div>
     </div>
     <div class="card-body p-4">
       <div class="row">
         <div class="col-md-4">
           <label class="form-label" for="inputEmail4">Select User</label>
           <input class="form-control" type="text" value="">
         </div>
         <div class="col-md-4">
           <label class="form-label" for="inputEmail4">Password</label>
           <input class="form-control" type="text" value="">
         </div>
         <div class="col-md-4">
           <label class="form-label" for="inputEmail4">Confirm Password</label>
           <input class="form-control" type="text" value="">
         </div>

       </div>
     </div>
   </div>
   <div class="card shadow-none border border-300 my-4" data-component-card="data-component-card">
     <div class="card-body p-2">
       <div class="row">
         <div class="col-12" align="center">
           <button class="btn btn-primary w-100" type="submit">Update Password</button>
         </div>
       </div>
     </div>
   </div>
 </form>
