/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "../css/app.scss";

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
import "bootstrap/dist/js/bootstrap";
import "./components/stickyHeader";

import SmoothScroll from "smooth-scroll"

let scroll = new SmoothScroll('a[href*="#"]', {
    speed: 500
});

$(".alert").on("click", function () {
    $(this).hide(200);
})


