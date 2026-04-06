<?php
$this->title = "Purchase Invoice";
use yii\helpers\Url;
 ?>
 <div class="card shadow-none border border-300 my-4" data-component-card="data-component-card">
   <div class="card-header p-4 border-bottom border-300 bg-soft">
     <div class="row g-3 justify-content-between align-items-center">
       <div class="col-12 col-md">
         <h4 class="text-900 mb-0" data-anchor="data-anchor">Purchase Invoices</h4>
       </div>
       <div class="col col-md-auto">
         <a href="<?=Url::to(['site/purchase-invoice-create'])?>">
       <button type="button" name="button" class="btn btn-primary">Create New Purchase Invoice
       </button>
       </a>
       </div>

     </div>
   </div>
   <div class="card-body p-4">


     <div id="tableExample3" data-list="{&quot;valueNames&quot;:[&quot;name&quot;,&quot;email&quot;,&quot;age&quot;],&quot;page&quot;:10,&quot;pagination&quot;:true}">
       <div class="search-box mb-3 mx-auto">
         <form class="position-relative" data-bs-toggle="search" data-bs-display="static">
           <input class="form-control search-input search form-control-sm" type="search" placeholder="Search" aria-label="Search">
           <svg class="svg-inline--fa fa-magnifying-glass search-box-icon" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="magnifying-glass" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M500.3 443.7l-119.7-119.7c27.22-40.41 40.65-90.9 33.46-144.7C401.8 87.79 326.8 13.32 235.2 1.723C99.01-15.51-15.51 99.01 1.724 235.2c11.6 91.64 86.08 166.7 177.6 178.9c53.8 7.189 104.3-6.236 144.7-33.46l119.7 119.7c15.62 15.62 40.95 15.62 56.57 0C515.9 484.7 515.9 459.3 500.3 443.7zM79.1 208c0-70.58 57.42-128 128-128s128 57.42 128 128c0 70.58-57.42 128-128 128S79.1 278.6 79.1 208z"></path></svg><!-- <span class="fas fa-search search-box-icon"></span> Font Awesome fontawesome.com -->

         </form>
       </div>
       <div class="table-responsive">
         <table class="table table-striped table-sm fs--1 mb-0">
           <thead>
             <tr>
               <th class="sort border-top" data-sort="status">Act.</th>
               <th class="sort border-top ps-3" data-sort="id">Invoice #</th>
               <th class="sort border-top ps-3" data-sort="id">Invoice Date</th>
               <th class="sort border-top ps-3" data-sort="id">PO #</th>
               <th class="sort border-top ps-3" data-sort="id">Vendor</th>
               <th class="sort border-top" data-sort="date">Total Amount</th>
               <th class="sort border-top" data-sort="status">Due Date</th>
               <th class="sort border-top" data-sort="status">Approved By</th>
               <th class="sort border-top" data-sort="status">Remarks</th>

             </tr>
           </thead>
           <tbody class="list">
             <tr>
     <td class="align-middle status">
       <i class="far fa-edit text-primary"></i>
       <i class="fas fa-trash text-danger"></i>
     </td>
     <td class="align-middle">INV001</td>
     <td class="align-middle date">2024-08-05</td>
     <td class="align-middle">PO001</td>
     <td class="align-middle">ABC Supplies</td>
     <td class="align-middle ps-3 id">$1,200.00</td>
     <td class="align-middle title">2024-09-05</td>
     <td class="align-middle">John Doe</td>
     <td class="align-middle">Early payment discount</td>
   </tr>
   <tr>
     <td class="align-middle status">
       <i class="far fa-edit text-primary"></i>
       <i class="fas fa-trash text-danger"></i>
     </td>
     <td class="align-middle">INV002</td>
     <td class="align-middle date">2024-08-07</td>
     <td class="align-middle">PO002</td>
     <td class="align-middle">XYZ Industries</td>
     <td class="align-middle ps-3 id">$2,500.00</td>
     <td class="align-middle title">2024-09-07</td>
     <td class="align-middle">Jane Smith</td>
     <td class="align-middle">Pending approval</td>
   </tr>
   <tr>
     <td class="align-middle status">
       <i class="far fa-edit text-primary"></i>
       <i class="fas fa-trash text-danger"></i>
     </td>
     <td class="align-middle">INV003</td>
     <td class="align-middle date">2024-08-09</td>
     <td class="align-middle">PO003</td>
     <td class="align-middle">Global Parts</td>
     <td class="align-middle ps-3 id">$750.00</td>
     <td class="align-middle title">2024-09-09</td>
     <td class="align-middle">Michael Brown</td>
     <td class="align-middle">Goods received in full</td>
   </tr>
   <tr>
     <td class="align-middle status">
       <i class="far fa-edit text-primary"></i>
       <i class="fas fa-trash text-danger"></i>
     </td>
     <td class="align-middle">INV004</td>
     <td class="align-middle date">2024-08-11</td>
     <td class="align-middle">PO004</td>
     <td class="align-middle">Prime Distributors</td>
     <td class="align-middle ps-3 id">$1,800.00</td>
     <td class="align-middle title">2024-09-11</td>
     <td class="align-middle">Emma Wilson</td>
     <td class="align-middle">Awaiting delivery confirmation</td>
   </tr>
   <tr>
     <td class="align-middle status">
       <i class="far fa-edit text-primary"></i>
       <i class="fas fa-trash text-danger"></i>
     </td>
     <td class="align-middle">INV005</td>
     <td class="align-middle date">2024-08-13</td>
     <td class="align-middle">PO005</td>
     <td class="align-middle">Supply Chain Inc.</td>
     <td class="align-middle ps-3 id">$3,000.00</td>
     <td class="align-middle title">2024-09-13</td>
     <td class="align-middle">Olivia Davis</td>
     <td class="align-middle">Partial payment made</td>
   </tr>
   <tr>
     <td class="align-middle status">
       <i class="far fa-edit text-primary"></i>
       <i class="fas fa-trash text-danger"></i>
     </td>
     <td class="align-middle">INV006</td>
     <td class="align-middle date">2024-08-15</td>
     <td class="align-middle">PO006</td>
     <td class="align-middle">Northwest Parts</td>
     <td class="align-middle ps-3 id">$900.00</td>
     <td class="align-middle title">2024-09-15</td>
     <td class="align-middle">Liam Johnson</td>
     <td class="align-middle">Urgent shipment</td>
   </tr>
   <tr>
     <td class="align-middle status">
       <i class="far fa-edit text-primary"></i>
       <i class="fas fa-trash text-danger"></i>
     </td>
     <td class="align-middle">INV007</td>
     <td class="align-middle date">2024-08-17</td>
     <td class="align-middle">PO007</td>
     <td class="align-middle">West Coast Supplies</td>
     <td class="align-middle ps-3 id">$2,200.00</td>
     <td class="align-middle title">2024-09-17</td>
     <td class="align-middle">Sophia Miller</td>
     <td class="align-middle">Discount applied</td>
   </tr>
   <tr>
     <td class="align-middle status">
       <i class="far fa-edit text-primary"></i>
       <i class="fas fa-trash text-danger"></i>
     </td>
     <td class="align-middle">INV008</td>
     <td class="align-middle date">2024-08-19</td>
     <td class="align-middle">PO008</td>
     <td class="align-middle">Eastern Logistics</td>
     <td class="align-middle ps-3 id">$1,500.00</td>
     <td class="align-middle title">2024-09-19</td>
     <td class="align-middle">James Anderson</td>
     <td class="align-middle">Awaiting payment</td>
   </tr>
   <tr>
     <td class="align-middle status">
       <i class="far fa-edit text-primary"></i>
       <i class="fas fa-trash text-danger"></i>
     </td>
     <td class="align-middle">INV009</td>
     <td class="align-middle date">2024-08-21</td>
     <td class="align-middle">PO009</td>
     <td class="align-middle">Southern Parts Co.</td>
     <td class="align-middle ps-3 id">$2,800.00</td>
     <td class="align-middle title">2024-09-21</td>
     <td class="align-middle">Charlotte Taylor</td>
     <td class="align-middle">Backordered items pending</td>
   </tr>
   <tr>
     <td class="align-middle status">
       <i class="far fa-edit text-primary"></i>
       <i class="fas fa-trash text-danger"></i>
     </td>
     <td class="align-middle">INV010</td>
     <td class="align-middle date">2024-08-23</td>
     <td class="align-middle">PO010</td>
     <td class="align-middle">Midwest Traders</td>
     <td class="align-middle ps-3 id">$1,000.00</td>
     <td class="align-middle title">2024-09-23</td>
     <td class="align-middle">Amelia Thomas</td>
     <td class="align-middle">Review for discrepancies</td>
   </tr>

         </tbody>
         </table>
       </div>
       <div class="d-flex justify-content-between mt-3"><span class="d-none d-sm-inline-block" data-list-info="data-list-info">1 to 5 <span class="text-600"> Items of </span>43</span>
         <div class="d-flex">
           <button class="page-link disabled" data-list-pagination="prev" disabled=""><svg class="svg-inline--fa fa-chevron-left" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-left" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg=""><path fill="currentColor" d="M224 480c-8.188 0-16.38-3.125-22.62-9.375l-192-192c-12.5-12.5-12.5-32.75 0-45.25l192-192c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25L77.25 256l169.4 169.4c12.5 12.5 12.5 32.75 0 45.25C240.4 476.9 232.2 480 224 480z"></path></svg><!-- <span class="fas fa-chevron-left"></span> Font Awesome fontawesome.com --></button>
           <ul class="mb-0 pagination"><li class="active"><button class="page" type="button" data-i="1" data-page="5">1</button></li><li><button class="page" type="button" data-i="2" data-page="5">2</button></li><li><button class="page" type="button" data-i="3" data-page="5">3</button></li><li class="disabled"><button class="page" type="button">...</button></li></ul>
           <button class="page-link pe-0" data-list-pagination="next"><svg class="svg-inline--fa fa-chevron-right" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg=""><path fill="currentColor" d="M96 480c-8.188 0-16.38-3.125-22.62-9.375c-12.5-12.5-12.5-32.75 0-45.25L242.8 256L73.38 86.63c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0l192 192c12.5 12.5 12.5 32.75 0 45.25l-192 192C112.4 476.9 104.2 480 96 480z"></path></svg><!-- <span class="fas fa-chevron-right"></span> Font Awesome fontawesome.com --></button>
         </div>
       </div>
     </div>

   </div>
 </div>
