<?php
$this->title = "Item Allottment";
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
           <label class="form-label" for="inputEmail4">Item No.</label>
           <select class="form-control" name="">
             <option value="">Select</option>
             <option value="">Item 1</option>
             <option value="">Item 2</option>
           </select>
         </div>
         <div class="col-md-4">
           <label class="form-label" for="inputEmail4">Allotment Date</label>
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-md-4">
           <label class="form-label" for="inputEmail4">Customer</label>
           <select class="form-control" name="">
             <option value="">Select</option>
             <option value="">Customer1</option>
             <option value="">Customer2</option>
           </select>
         </div>

         <div class="col-md-4">
           <label class="form-label" for="inputEmail4">Qty Allotted</label>
           <input type="text" name="" class="form-control">
         </div>
         <div class="col-md-4">
           <label class="form-label" for="inputEmail4">Allotted By</label>
           <select class="form-control" name="">
             <option value="">Select</option>
             <option value="">User 1</option>
             <option value="">User 2</option>
             <option value="">User 3</option>
           </select>
         </div>
         <div class="col-md-4">
           <label class="form-label" for="inputEmail4">Status</label>
           <select class="form-control" name="">
             <option value="">Select</option>
             <option value="">In-transit</option>
             <option value="">Delivered</option>
           </select>
         </div>
         <div class="col-md-12">
           <label for="">Remarks/Comments</label>
           <textarea name="name" rows="8" class="form-control"></textarea>
         </div>


       </div>
     </div>
   </div>


   <div class="card shadow-none border border-300 my-4" data-component-card="data-component-card">
     <div class="card-body p-2">
       <div class="row">
         <div class="col-12" align="center">
           <button class="btn btn-primary w-100" type="submit">Create Allottment</button>
         </div>
       </div>
     </div>
   </div>
 </form>
