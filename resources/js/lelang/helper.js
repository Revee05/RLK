/**
 * Safe bidding helpers — attach ke window jika belum ada.
 * Dipanggil dari app.js (Vue) dan blade (inline scripts).
 */
(function () {
    'use strict';

    if (typeof window.formatRp !== 'function') {
        window.formatRp = function (n) {
            if (n === null || n === undefined) return '';
            const num = Number(n);
            if (isNaN(num)) return String(n);
            return String(num).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        };
    }

    if (typeof window.updateNominalDropdown !== 'function') {
        window.updateNominalDropdown = function (highest, nominalsArray, stepOverride, stepDefault) {
            const defaultStep = Number(stepDefault) || 10000;
            const h = Number(highest);
            const select = document.getElementById('bidSelect');
            
            // DEBUG LOG
            console.log('[updateNominalDropdown] Called with:', {
                highest: h,
                nominalsArray: nominalsArray,
                stepOverride: stepOverride,
                stepDefault: stepDefault,
                defaultStep: defaultStep
            });
            
            if (!select || isNaN(h)) {
                console.warn('[updateNominalDropdown] Select not found or highest is NaN');
                return;
            }

            select.innerHTML = '<option value="">Pilih Nominal Bid</option>';

            // Jika nominalsArray ada dan valid, gunakan itu (dari server)
            if (Array.isArray(nominalsArray) && nominalsArray.length) {
                console.log('[updateNominalDropdown] Using nominals from server:', nominalsArray);
                nominalsArray.forEach(val => {
                    const v = Number(val);
                    if (isNaN(v)) return;
                    const opt = document.createElement('option');
                    opt.value = v;
                    opt.textContent = 'Rp ' + window.formatRp(v);
                    select.appendChild(opt);
                });
                return;
            }

            // Fallback: hitung dari step
            const step = Number(stepOverride) || defaultStep;
            console.log('[updateNominalDropdown] Fallback: building from step:', step);
            for (let i = 1; i <= 5; i++) {
                const val = h + (step * i);
                const opt = document.createElement('option');
                opt.value = val;
                opt.textContent = 'Rp ' + window.formatRp(val);
                select.appendChild(opt);
            }
        };
    }

    if (typeof window.refreshStateImmediate !== 'function') {
        window.refreshStateImmediate = function () {
            // no-op fallback — bisa di-overwrite oleh app.js jika perlu
            console.info('[helpers] refreshStateImmediate noop');
        };
    }
})();