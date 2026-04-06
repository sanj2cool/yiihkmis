<?php
$this->title = "Item Category";
use yii\helpers\Url;
 ?>
 <div class="card shadow-none border border-300 my-4" data-component-card="data-component-card">
   <div class="card-header p-4 border-bottom border-300 bg-soft">
     <div class="row g-3 justify-content-between align-items-center">
       <div class="col-12 col-md">
         <h4 class="text-900 mb-0" data-anchor="data-anchor">Item Categories</h4>
       </div>
       <div class="col col-md-auto">
         <a href="<?=Url::to(['site/item-category-create'])?>">
       <button type="button" name="button" class="btn btn-primary">Create New Item Category
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
               <th class="sort border-top ps-3" data-sort="id">Title</th>
               <th class="sort border-top" data-sort="date">Description</th>
               <th class="sort border-top" data-sort="status">Status</th>

             </tr>
           </thead>
           <tbody class="list"><tr>
             <td class="align-middle status">
               <i class="far fa-edit text-primary"></i>
             </td>
             <td class="align-middle date">Refrigerated Trailer Parts</td>
             <td class="align-middle ps-3 id">Parts specifically designed for refrigerated trailers, including cooling units and insulation.</td>
             <td class="align-middle title">Active</td>


           </tr>
           <tr>
             <td class="align-middle status">
               <i class="far fa-edit text-primary"></i>
             </td>
             <td class="align-middle date">Dry Van Trailer Parts</td>
             <td class="align-middle ps-3 id">Components and accessories for dry van trailers, focused on secure and durable cargo transport.</td>
             <td class="align-middle title">Active</td>


           </tr>
           <tr>
             <td class="align-middle status">
               <i class="far fa-edit text-primary"></i>
             </td>
             <td class="align-middle date">Axles & Suspension</td>
             <td class="align-middle ps-3 id">Axles, suspension systems, and related components for commercial trailers of all types.</td>
             <td class="align-middle title">Active</td>


           </tr>
           <tr>
             <td class="align-middle status">
               <i class="far fa-edit text-primary"></i>
             </td>
             <td class="align-middle date">Brakes & Wheel Ends</td>
             <td class="align-middle ps-3 id">Brake systems, wheel ends, and related parts for ensuring safe and reliable trailer operation.</td>
             <td class="align-middle title">Active</td>


           </tr>
           <tr>
             <td class="align-middle status">
               <i class="far fa-edit text-primary"></i>
             </td>
             <td class="align-middle date">Electrical & Lighting</td>
             <td class="align-middle ps-3 id">Electrical components, lighting systems, and wiring for trailer operation and safety compliance.</td>
             <td class="align-middle title">Active</td>
           </tr>




           <tr>
             <td class="align-middle status">
               <i class="far fa-edit text-primary"></i>
             </td>
             <td class="align-middle date">Doors & Hardware</td>
             <td class="align-middle ps-3 id">Trailer door mechanisms, locks, and other hardware necessary for secure loading and unloading.</td>
             <td class="align-middle title">Active</td>


           </tr>
           <tr>
             <td class="align-middle status">
               <i class="far fa-edit text-primary"></i>
             </td>
             <td class="align-middle date">Flooring & Panels</td>
             <td class="align-middle ps-3 id">Flooring materials and wall panels designed for durability and load-bearing capacity.</td>
             <td class="align-middle title">Active</td>


           </tr>
           <tr>
             <td class="align-middle status">
               <i class="far fa-edit text-primary"></i>
             </td>
             <td class="align-middle date">Tires & Wheels</td>
             <td class="align-middle ps-3 id">Tires, wheels, and related accessories for various types of commercial trailers.</td>
             <td class="align-middle title">Active</td>


           </tr>
           <tr>
             <td class="align-middle status">
               <i class="far fa-edit text-primary"></i>
             </td>
             <td class="align-middle date">Trailer Coupling & Hitches</td>
             <td class="align-middle ps-3 id">Hitches, couplings, and related parts for secure trailer attachment to trucks.</td>
             <td class="align-middle title">Active</td>


           </tr>
           <tr>
             <td class="align-middle status">
               <i class="far fa-edit text-primary"></i>
             </td>
             <td class="align-middle date">Roofs & Tarp Systems</td>
             <td class="align-middle ps-3 id">Roof systems and tarps designed to protect cargo from weather conditions and other external factors.</td>
             <td class="align-middle title">Active</td>
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
