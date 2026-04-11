<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons/css/flag-icons.min.css">

<div class="currency-converter js-currency">
    
    <div class="cc-body">
        <span>1 USD <span class="fi fi-us"></span> = </span>
        <input type="number" step="0.00001" class="cc-rate" value="<?= $defaultRate ?>">
        <span> CAD <span class="fi fi-ca"></span></span>

        <!-- ✅ UPDATED: wrapped with cc-result and hidden by default -->
        <div class="cc-result" style="margin-top:5px; display:none;">
            <strong>Total (CAD):</strong> <span class="cc-total"></span>
        </div>
    </div>

    <span class="cc-usd" style="display:none;"><?= $amount ?></span>
</div>

<script>
(function () {
    const wrapper = document.currentScript.previousElementSibling;

    const rateInput = wrapper.querySelector('.cc-rate');
    const usdEl = wrapper.querySelector('.cc-usd');
    const cadEl = wrapper.querySelector('.cc-total');
    const resultBox = wrapper.querySelector('.cc-result');

    const STORAGE_KEY = 'currency_rate';

    // ✅ Load saved rate (if exists)
    let savedRate = localStorage.getItem(STORAGE_KEY);
    if (savedRate) {
        rateInput.value = savedRate;
    }

    function convert() {
        let usd = parseFloat(usdEl.textContent) || 0;
        let rate = parseFloat(rateInput.value) || 0;
        let cad = usd * rate;

        if (rate > 0 && cad > 0) {
            cadEl.textContent = new Intl.NumberFormat('en-CA', {
                style: 'currency',
                currency: 'CAD'
            }).format(cad);

            resultBox.style.display = 'block';
        } else {
            resultBox.style.display = 'none';
        }
    }

    // ✅ Save on change
    rateInput.addEventListener('input', function () {
        localStorage.setItem(STORAGE_KEY, this.value);
        convert();
    });

    // initial run
    convert();
})();
</script>
