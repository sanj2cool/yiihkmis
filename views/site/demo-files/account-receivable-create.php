<?php
  $this->title = "Create Accounts Receivable";
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
         <label for="">Invoice</label>
         <select class="form-control" name="">
           <option value="">Select</option>
         </select>
       </div>
       <div class="col-md-4">
         <label for="">Amount Received</label>
         <input type="text" class="form-control" value="">
       </div>
       <div class="col-md-4">
         <label for="">Receivable Date</label>
         <input type="text" class="form-control" value="">
       </div>
     </div>
     <div class="row mt-3">
       <div class="col-md-4">
         <label for="">Payment Method</label>
         <select class="form-control" name="">
           <option value="">Select</option>
         </select>
       </div>
       <div class="col-md-4 mt-4">
         <input type="checkbox" name="" value="">
         <label for="">Mark as Bad Debt</label>
       </div>

     </div>


   </div>
 </div>
 <div class="card shadow-none border border-300 my-4" data-component-card="data-component-card">

   <div class="card-body p-4">
     <div class="row">
       <div class="col-lg-12">
         <label for="">Notes</label>
         <textarea name="name" rows="4" class="form-control"></textarea>
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
