import Alpine from "alpinejs";
import axios from "axios";
import { Html5Qrcode } from "html5-qrcode";

window.Alpine = Alpine;
window.axios = axios;
window.Html5Qrcode = Html5Qrcode;

// Set CSRF token
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Try to load sheet-manager (safe wrapper)
try {
    import("./sheet-manager").then((module) => {
        if (module.default) {
            window.SheetManager = module.default;
        }
    });
} catch (e) {
    console.log('SheetManager not loaded (this is OK)');
}

Alpine.start();