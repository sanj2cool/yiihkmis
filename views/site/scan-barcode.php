
<div class="container">
  <div class="d-flex justify-content-center row">
    <div class="col-lg-4 col-md-6 col-sm-12">

      <div class="card">
        <div class="card-body">

          <div id="barcode-scanner">
              <!-- Video feed where camera input will be shown -->
              <div id="scanner-camera"></div>

              <!-- Element to display the decoded barcode result -->
              <div id="scanner-result">Waiting for barcode scan...</div>

              <!-- Button to stop the barcode scanner manually -->

              <button id="reset-button" style="display:none;">Scan Next Product</button>
              <button id="stop-scanner">Stop Scanner</button>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
    // Function to start the barcode scanner
    function startBarcodeScanner() {
        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: document.querySelector('#scanner-camera'), // The div where the camera feed will be shown
                constraints: {
                    facingMode: "environment" // Use the rear camera for barcode scanning
                }
            },
            decoder: {
                readers: ["code_128_reader"] // Use Code 128 reader for barcode decoding
            },
            locate: true // Optional: helps Quagga locate the barcode in the frame
        }, function(err) {
            if (err) {
                console.error("Error initializing Quagga: ", err);
                return;
            }
            console.log("Barcode scanner initialized successfully");
            Quagga.start();
        });

        // Listen for the barcode detection event
        Quagga.onDetected(function(result) {
            if (result && result.codeResult && result.codeResult.code) {
                const internalSKU = result.codeResult.code; // The decoded internal SKU

                // Display the result in the scanner-result div
                document.getElementById('scanner-result').innerHTML = `SKU: ${internalSKU}`;

                // Stop the scanner after a successful detection
                Quagga.stop();
                //---------NOW GET THE REQUIRED INFORMATION ABOUT THE PRODUCT HERE -------
                 lookupProduct(internalSKU);
            }
        });
    }

    // Function to stop the barcode scanner manually
    function stopBarcodeScanner() {
        Quagga.stop();
        document.getElementById('scanner-result').innerHTML = 'Scanner stopped.';
    }
    function resetScanner() {
    // Clear previous results
      document.getElementById('scanner-result').innerHTML = ''; // Clear the display

      // Restart the scanner
      startBarcodeScanner();

      // Hide the reset button
      document.getElementById('reset-button').style.display = 'none';
  }

    // Start the scanner when the page loads
    startBarcodeScanner();
    // Add event listener to reset button
    document.getElementById('reset-button').addEventListener('click', function() {
        resetScanner();
    });

    // Add event listener to the stop button
    document.getElementById('stop-scanner').addEventListener('click', function() {
        stopBarcodeScanner();
    });
    function lookupProduct(internalSKU) {
      // Get CSRF token from meta tag
   var csrfToken = $('meta[name="csrf-token"]').attr('content');
        // Send an AJAX request to search for the product
        $.ajax({
            url: 'index.php?r=product/search',  // Adjust to your search endpoint
            method: 'POST',
            data: {
              internal_sku: internalSKU,
              _csrf: csrfToken
            },
            success: function(data) {
                // Handle the response (display product details, etc.)
                console.log("Product details:", data);
                document.getElementById('scanner-result').innerHTML = data;
                // Show the reset button after successful lookup
                document.getElementById('reset-button').style.display = 'block';
            },
            error: function(err) {
                console.error("Error fetching product:", err);
            }
        });
    }
</script>
