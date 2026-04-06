<?php
  $this->title = "Reports";
 ?>
 <div class="row">
   <div class="col-sm-12">
     <div class="card mb-3">
       <div class="card-header bg-light">
         <h5 class="mb-0">Items Creation Reports</h5>
       </div>
       <div class="card-body border-top p-0">
         <div class="row g-0 align-items-center border-bottom py-2 px-3">
           <div class="col-md mt-1 mt-md-0">
             <a class="unformat" href="javascript:void(0);">Item Master List</a> |
             <a class="unformat text-sm" href="javascript:void(0);" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
             <p>
               A comprehensive report listing all items in the system, including item numbers, categories, descriptions, status, and creation dates.
             </p>
           </div>
           <div class="col-md-auto">
             <p class="mb-0">
               <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

             </p>
           </div>
         </div>


         <div class="row g-0 align-items-center border-bottom py-2 px-3">
           <div class="col-md mt-1 mt-md-0">
             <a class="unformat" href="'.Url::to([$m->url]).'">New Items Report</a> |
             <a class="unformat text-sm" href="'.Url::to([$m->url]).'" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
             <p>
              A report showing all items created within a specific time frame, useful for tracking new additions to the inventory.
             </p>
           </div>
           <div class="col-md-auto">
             <p class="mb-0">
               <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

             </p>
           </div>
         </div>


       </div>
     </div>
   </div>
 </div><!-- item creation row ended -->


 <div class="row">
   <div class="col-sm-12">
     <div class="card mb-3">
       <div class="card-header bg-light">
         <h5 class="mb-0">Items Receiving Reports</h5>
       </div>
       <div class="card-body border-top p-0">
         <div class="row g-0 align-items-center border-bottom py-2 px-3">
           <div class="col-md mt-1 mt-md-0">
             <a class="unformat" href="javascript:void(0);">Received Items Summary</a> |
             <a class="unformat text-sm" href="javascript:void(0);" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
             <p>
               A report summarizing the total number of items received, categorized by item type, vendor, or date.
             </p>
           </div>
           <div class="col-md-auto">
             <p class="mb-0">
               <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

             </p>
           </div>
         </div>


         <div class="row g-0 align-items-center border-bottom py-2 px-3">
           <div class="col-md mt-1 mt-md-0">
             <a class="unformat" href="'.Url::to([$m->url]).'">Vendor Performance Report</a> |
             <a class="unformat text-sm" href="'.Url::to([$m->url]).'" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
             <p>
              Analyzes the timeliness and accuracy of deliveries by vendors based on received items.
             </p>
           </div>
           <div class="col-md-auto">
             <p class="mb-0">
               <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

             </p>
           </div>
         </div>

         <div class="row g-0 align-items-center border-bottom py-2 px-3">
           <div class="col-md mt-1 mt-md-0">
             <a class="unformat" href="'.Url::to([$m->url]).'">Outstanding Receipts</a> |
             <a class="unformat text-sm" href="'.Url::to([$m->url]).'" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
             <p>
               Lists items that have been ordered but not yet received, along with expected delivery dates.
             </p>
           </div>
           <div class="col-md-auto">
             <p class="mb-0">
               <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

             </p>
           </div>
         </div>


       </div>
     </div>
   </div>
 </div><!-- item creation row ended -->
 

 <div class="row">
   <div class="col-sm-12">
     <div class="card mb-3">
       <div class="card-header bg-light">
         <h5 class="mb-0">Purchase Order Reports</h5>
       </div>
       <div class="card-body border-top p-0">
         <div class="row g-0 align-items-center border-bottom py-2 px-3">
           <div class="col-md mt-1 mt-md-0">
             <a class="unformat" href="javascript:void(0);">Purchase Order Status Report</a> |
             <a class="unformat text-sm" href="javascript:void(0);" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
             <p>
               Tracks the status of all purchase orders (pending, partially received, fully received) with associated details.
             </p>
           </div>
           <div class="col-md-auto">
             <p class="mb-0">
               <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

             </p>
           </div>
         </div>


         <div class="row g-0 align-items-center border-bottom py-2 px-3">
           <div class="col-md mt-1 mt-md-0">
             <a class="unformat" href="'.Url::to([$m->url]).'">PO Summary by Vendor</a> |
             <a class="unformat text-sm" href="'.Url::to([$m->url]).'" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
             <p>
              Summarizes purchase orders by vendor, showing total amounts, quantities ordered, and amounts received.
             </p>
           </div>
           <div class="col-md-auto">
             <p class="mb-0">
               <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

             </p>
           </div>
         </div>

         <div class="row g-0 align-items-center border-bottom py-2 px-3">
           <div class="col-md mt-1 mt-md-0">
             <a class="unformat" href="'.Url::to([$m->url]).'">Open Purchase Orders</a> |
             <a class="unformat text-sm" href="'.Url::to([$m->url]).'" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
             <p>
               Lists all purchase orders that have not been fully received or paid, helping to manage outstanding commitments.
             </p>
           </div>
           <div class="col-md-auto">
             <p class="mb-0">
               <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

             </p>
           </div>
         </div>


       </div>
     </div>
   </div>
 </div><!-- item creation row ended -->


 <div class="row">
   <div class="col-sm-12">
     <div class="card mb-3">
       <div class="card-header bg-light">
         <h5 class="mb-0">Purchase Invoice Reports</h5>
       </div>
       <div class="card-body border-top p-0">
         <div class="row g-0 align-items-center border-bottom py-2 px-3">
           <div class="col-md mt-1 mt-md-0">
             <a class="unformat" href="javascript:void(0);">Invoice Payment Status</a> |
             <a class="unformat text-sm" href="javascript:void(0);" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
             <p>
               Tracks the payment status of purchase invoices (paid, partially paid, unpaid) to manage cash flow and outstanding liabilities.
             </p>
           </div>
           <div class="col-md-auto">
             <p class="mb-0">
               <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

             </p>
           </div>
         </div>


         <div class="row g-0 align-items-center border-bottom py-2 px-3">
           <div class="col-md mt-1 mt-md-0">
             <a class="unformat" href="'.Url::to([$m->url]).'">Vendor Billing Summary</a> |
             <a class="unformat text-sm" href="'.Url::to([$m->url]).'" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
             <p>
              Summarizes invoices received from each vendor, with total billed amounts, paid amounts, and outstanding amounts.
             </p>
           </div>
           <div class="col-md-auto">
             <p class="mb-0">
               <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

             </p>
           </div>
         </div>

         <div class="row g-0 align-items-center border-bottom py-2 px-3">
           <div class="col-md mt-1 mt-md-0">
             <a class="unformat" href="'.Url::to([$m->url]).'">Aging Report</a> |
             <a class="unformat text-sm" href="'.Url::to([$m->url]).'" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
             <p>
              Shows outstanding invoices categorized by the number of days past due, helping to prioritize payments.
             </p>
           </div>
           <div class="col-md-auto">
             <p class="mb-0">
               <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

             </p>
           </div>
         </div>


       </div>
     </div>
   </div>
 </div><!-- item creation row ended -->


  <div class="row">
    <div class="col-sm-12">
      <div class="card mb-3">
        <div class="card-header bg-light">
          <h5 class="mb-0">Accounts Payable Reports</h5>
        </div>
        <div class="card-body border-top p-0">
          <div class="row g-0 align-items-center border-bottom py-2 px-3">
            <div class="col-md mt-1 mt-md-0">
              <a class="unformat" href="javascript:void(0);">AP Aging Report</a> |
              <a class="unformat text-sm" href="javascript:void(0);" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
              <p>
                Details outstanding amounts owed to vendors, categorized by the number of days past due, aiding in cash flow management.
              </p>
            </div>
            <div class="col-md-auto">
              <p class="mb-0">
                <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

              </p>
            </div>
          </div>


          <div class="row g-0 align-items-center border-bottom py-2 px-3">
            <div class="col-md mt-1 mt-md-0">
              <a class="unformat" href="'.Url::to([$m->url]).'">Vendor Payment History</a> |
              <a class="unformat text-sm" href="'.Url::to([$m->url]).'" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
              <p>
                Tracks payments made to vendors over time, helping to monitor expenses and vendor relationships.
              </p>
            </div>
            <div class="col-md-auto">
              <p class="mb-0">
                <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

              </p>
            </div>
          </div>

          <div class="row g-0 align-items-center border-bottom py-2 px-3">
            <div class="col-md mt-1 mt-md-0">
              <a class="unformat" href="'.Url::to([$m->url]).'">Overdue Payables</a> |
              <a class="unformat text-sm" href="'.Url::to([$m->url]).'" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
              <p>
                Lists all overdue payables, with details on the amounts owed, vendors, and the length of time overdue.
              </p>
            </div>
            <div class="col-md-auto">
              <p class="mb-0">
                <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

              </p>
            </div>
          </div>


        </div>
      </div>
    </div>
  </div><!-- item creation row ended -->


   <div class="row">
     <div class="col-sm-12">
       <div class="card mb-3">
         <div class="card-header bg-light">
           <h5 class="mb-0">Vendor Reports</h5>
         </div>
         <div class="card-body border-top p-0">
           <div class="row g-0 align-items-center border-bottom py-2 px-3">
             <div class="col-md mt-1 mt-md-0">
               <a class="unformat" href="javascript:void(0);">Vendor Performance Report</a> |
               <a class="unformat text-sm" href="javascript:void(0);" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
               <p>
                 Analyzes vendors based on delivery timelines, accuracy, and quality of goods received, helping in vendor management.
               </p>
             </div>
             <div class="col-md-auto">
               <p class="mb-0">
                 <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

               </p>
             </div>
           </div>


           <div class="row g-0 align-items-center border-bottom py-2 px-3">
             <div class="col-md mt-1 mt-md-0">
               <a class="unformat" href="'.Url::to([$m->url]).'">Vendor Spend Analysis</a> |
               <a class="unformat text-sm" href="'.Url::to([$m->url]).'" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
               <p>
                Tracks total spending with each vendor over a period, useful for budgeting and negotiation.
               </p>
             </div>
             <div class="col-md-auto">
               <p class="mb-0">
                 <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

               </p>
             </div>
           </div>

           <div class="row g-0 align-items-center border-bottom py-2 px-3">
             <div class="col-md mt-1 mt-md-0">
               <a class="unformat" href="'.Url::to([$m->url]).'">Vendor Rating Report</a> |
               <a class="unformat text-sm" href="'.Url::to([$m->url]).'" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
               <p>
                Rates vendors based on predefined criteria like delivery performance, quality of goods, and price competitiveness.
               </p>
             </div>
             <div class="col-md-auto">
               <p class="mb-0">
                 <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

               </p>
             </div>
           </div>


         </div>
       </div>
     </div>
   </div><!-- item creation row ended -->


    <div class="row">
      <div class="col-sm-12">
        <div class="card mb-3">
          <div class="card-header bg-light">
            <h5 class="mb-0">General Reports (Cross-Module)</h5>
          </div>
          <div class="card-body border-top p-0">
            <div class="row g-0 align-items-center border-bottom py-2 px-3">
              <div class="col-md mt-1 mt-md-0">
                <a class="unformat" href="javascript:void(0);">Inventory Turnover Report</a> |
                <a class="unformat text-sm" href="javascript:void(0);" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
                <p>
                  Shows how quickly inventory is sold or used over a period, helping to optimize inventory levels.
                </p>
              </div>
              <div class="col-md-auto">
                <p class="mb-0">
                  <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

                </p>
              </div>
            </div>


            <div class="row g-0 align-items-center border-bottom py-2 px-3">
              <div class="col-md mt-1 mt-md-0">
                <a class="unformat" href="'.Url::to([$m->url]).'">Procurement Efficiency Report</a> |
                <a class="unformat text-sm" href="'.Url::to([$m->url]).'" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
                <p>
                  Analyzes the procurement process from order creation to receipt of goods, identifying bottlenecks or delays.
                </p>
              </div>
              <div class="col-md-auto">
                <p class="mb-0">
                  <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

                </p>
              </div>
            </div>

            <div class="row g-0 align-items-center border-bottom py-2 px-3">
              <div class="col-md mt-1 mt-md-0">
                <a class="unformat" href="'.Url::to([$m->url]).'">Cost Analysis Report</a> |
                <a class="unformat text-sm" href="'.Url::to([$m->url]).'" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
                <p>
                Tracks the cost of goods purchased, allocated, and paid for, helping in budgeting and cost control.
                </p>
              </div>
              <div class="col-md-auto">
                <p class="mb-0">
                  <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

                </p>
              </div>
            </div>

            <div class="row g-0 align-items-center border-bottom py-2 px-3">
              <div class="col-md mt-1 mt-md-0">
                <a class="unformat" href="'.Url::to([$m->url]).'">Financial Summary Report</a> |
                <a class="unformat text-sm" href="'.Url::to([$m->url]).'" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
                <p>
                Combines data from purchase invoices, accounts payable, and vendor payments to provide an overview of financial health.
                </p>
              </div>
              <div class="col-md-auto">
                <p class="mb-0">
                  <i class="far fa-star click_start" id="star_1">&nbsp;&nbsp;&nbsp;</i>

                </p>
              </div>
            </div>


          </div>
        </div>
      </div>
    </div><!-- item creation row ended -->
