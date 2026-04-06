<?php
$this->title = "Create user";
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
           <label class="form-label" for="inputEmail4">Username</label>
           <input class="form-control" type="text" value="">
         </div>
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Password</label>
           <input class="form-control" type="text" value="">
         </div>
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Name/Alias</label>
           <input class="form-control" type="text" value="">
         </div>
         <div class="col-md-3">
           <label class="form-label" for="inputEmail4">Role</label>
           <select class="form-control" name="">
             <option value="">Select</option>
             <option value="">Admin</option>
             <option value="">Supervisor</option>
             <option value="">Employee</option>
           </select>
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
     <div class="card-header p-4 border-bottom border-300 bg-soft">
       <div class="row g-3 justify-content-between align-items-center">
         <div class="col-12 col-md">
           <h4 class="text-900 mb-0" data-anchor="data-anchor">Module Access Details</h4>
         </div>
       </div>
     </div>
     <div class="card-body p-4 table-responsive">
       <table class="table table-bordered table-striped">
         <thead>
           <tr>
             <th>Module Name</th>
             <th>View</th>
             <th>Create</th>
             <th>Update</th>
             <th>Delete</th>
           </tr>
         </thead>
         <tbody>
           <tr class="table-primary">
             <td>Statistics</td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
           </tr>
           <tr>
             <td>Dashboard</td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
           </tr>
           <tr>
             <td>Reports</td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
           </tr>

           <tr class="table-primary">
             <td>Inventory Management</td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
           </tr>
           <tr>
             <td>Item Category</td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
           </tr>
           <tr>
             <td>Item creation</td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
           </tr>
           <tr>
             <td>Item receiving</td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
           </tr>
           <tr>
             <td>Allotment/Shipping/Sales</td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
           </tr>
           <tr class="table-primary">
             <td>Purchase Order</td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
           </tr>
           <tr>
             <td>List</td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
           </tr>
           <tr>
             <td>Purchase Invoice</td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
           </tr>
           <tr>
             <td>Accounts Payable</td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
           </tr>
           <tr class="table-primary">
             <td>Vendors</td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
           </tr>
           <tr>
             <td>List</td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
           </tr>
           <tr>
             <td>Create</td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
           </tr>

           <tr class="table-primary">
             <td>Customers</td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
           </tr>
           <tr>
             <td>List</td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
           </tr>
           <tr>
             <td>Invoice</td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
           </tr>
           <tr>
             <td>Accounts Receivables</td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
           </tr>


           <tr class="table-primary">
             <td>User Access</td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
           </tr>
           <tr>
             <td>List</td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
           </tr>
           <tr>
             <td>Manage</td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
           </tr>
           <tr>
             <td>Password Update</td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
             <td>
               <input type="checkbox" name="" value="">
             </td>
           </tr>
         </tbody>
       </table>
     </div>
   </div>
   <div class="card shadow-none border border-300 my-4" data-component-card="data-component-card">
     <div class="card-body p-2">
       <div class="row">
         <div class="col-12" align="center">
           <button class="btn btn-primary w-100" type="submit">Create User</button>
         </div>
       </div>
     </div>
   </div>
 </form>
