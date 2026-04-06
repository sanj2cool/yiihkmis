<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons/css/flag-icons.min.css">
<div class="currency-converter">
    <span>1 USD <span class="fi fi-us"></span> = </span>
    <input type="number" step="0.00001" class="cc-rate" value="<?= $defaultRate ?>">
    <span> CAD <span class="fi fi-ca"></span></span>

    <div style="margin-top:5px;">
       <strong>Total (CAD):</strong> <span class="cc-total"></span>
    </div>

    <span class="cc-usd" style="display:none;"><?= $amount ?></span>
</div>

<script>
(function () {
    const wrapper = document.currentScript.previousElementSibling;
    const rateInput = wrapper.querySelector('.cc-rate');
    const usdEl = wrapper.querySelector('.cc-usd');
    const cadEl = wrapper.querySelector('.cc-total');

    function convert() {
        let usd = parseFloat(usdEl.textContent) || 0;
        let rate = parseFloat(rateInput.value) || 0;
        let cad = usd * rate;

        cadEl.textContent = rate
            ? new Intl.NumberFormat('en-CA', { style: 'currency', currency: 'CAD' }).format(cad)
            : '';
    }

    rateInput.addEventListener('input', convert);
    convert();
})();
</script>
