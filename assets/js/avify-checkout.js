import Checkout from "./components/checkout";

(function () {
    window.addEventListener("load", (event) => {
        $ = jQuery;
        new Checkout();
    });
})();
