/**
 * Safe bidding helpers — attach ke window jika belum ada.
 * Dipanggil dari app.js (Vue) dan blade (inline scripts).
 */
(function () {
    "use strict";

    if (typeof window.formatRp !== "function") {
        window.formatRp = function (n) {
            if (n === null || n === undefined) return "";
            const num = Number(n);
            if (isNaN(num)) return String(n);
            return String(num).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        };
    }

    /**
     * updateNominalDropdown
     * =====================
     * - Sinkron dengan elemen Blade berbasis data-role
     * - Support mobile & desktop (elemen aktif saja)
     * - Support data dari server (nominals) & fallback step
     * - Aman dipanggil dari Blade / Vue / Echo
     */
    if (typeof window.updateNominalDropdown !== "function") {
        window.updateNominalDropdown = function (
            highest,
            nominalsArray = null,
            stepOverride = null,
            stepDefault = 10000
        ) {
            const h = Number(highest);
            const defaultStep = Number(stepDefault) || 10000;

            /**
             * Ambil elemen aktif (tidak display:none)
             */
            function getActiveEl(role) {
                const els = document.querySelectorAll(`[data-role="${role}"]`);
                return (
                    Array.from(els).find((el) => el.offsetParent !== null) ||
                    null
                );
            }

            const select = getActiveEl("bid-select");

            // ===== DEBUG =====
            console.log("[updateNominalDropdown]", {
                highest: h,
                nominalsArray,
                stepOverride,
                stepDefault: defaultStep,
                selectFound: !!select,
            });

            // ===== GUARD =====
            if (!select) {
                console.warn(
                    "[updateNominalDropdown] ❌ bid-select not found (active)"
                );
                return;
            }

            if (isNaN(h)) {
                console.warn(
                    "[updateNominalDropdown] ❌ highest is NaN:",
                    highest
                );
                return;
            }

            // ===== RESET OPTIONS =====
            select.innerHTML = '<option value="">Pilih Nominal Bid</option>';

            /**
             * ===== CASE 1: Server kirim nominals (PRIORITAS)
             */
            if (Array.isArray(nominalsArray) && nominalsArray.length > 0) {
                console.log("[updateNominalDropdown] ✓ Using server nominals");

                nominalsArray.forEach((val) => {
                    const v = Number(val);
                    if (isNaN(v)) return;

                    const opt = document.createElement("option");
                    opt.value = v;
                    opt.textContent = "Rp " + window.formatRp(v);
                    select.appendChild(opt);
                });

                return; // STOP — jangan fallback
            }

            /**
             * ===== CASE 2: Fallback hitung dari step
             */
            const step = Number(stepOverride) || defaultStep;

            console.log(
                "[updateNominalDropdown] ✓ Fallback build by step:",
                step
            );

            for (let i = 1; i <= 5; i++) {
                const val = h + step * i;
                const opt = document.createElement("option");
                opt.value = val;
                opt.textContent = "Rp " + window.formatRp(val);
                select.appendChild(opt);
            }
        };
    }

    if (typeof window.refreshStateImmediate !== "function") {
        window.refreshStateImmediate = function () {
            // no-op fallback — bisa di-overwrite oleh app.js jika perlu
            console.info("[helpers] refreshStateImmediate noop");
        };
    }
})();
