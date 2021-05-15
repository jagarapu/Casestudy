/*
 * Welcome to your app's base JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import 'bootstrap/dist/css/bootstrap.min.css';
import 'font-awesome/css/font-awesome.min.css';
import 'line-awesome/dist/line-awesome/css/line-awesome.min.css';
import '../css/bootstrap-multiselect.css';
import 'chosen-js/chosen.css';
import 'select2/dist/css/select2.min.css';
import 'morris';
import '../css/dataTables.bootstrap4.min.css';
import '../css/theme_style.css';
import 'toastr/build/toastr.min.css';
import 'bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css';
import 'jquery-confirm/css/jquery-confirm.css';
import '../css/custom_case_study.css';
import './loader.css';
import 'bootstrap4-toggle/css/bootstrap4-toggle.min.css'

// start the Stimulus application
import 'popper.js';
import 'bootstrap';
import 'jquery-slimscroll';
import 'morris';
import 'raphael';
import '../js/app.js';
import '../js/task.js';
import 'select2';
import 'datatables';
//import './dataTables.bootstrap4.min';
import 'moment';
import 'jquery-confirm';
import 'chosen-js';
//import 'bootstrap-multiselect/dist/js/bootstrap-multiselect';
import 'bootstrap-datepicker';
import 'jquery-blockui';
import '../js/common.js';
import toastr from "toastr";
window.toastr = toastr;

 import 'bootstrap4-toggle/js/bootstrap4-toggle.min';
 import '../js/html5shiv.min';

const routes = require('../../public/js/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
Routing.setRoutingData(routes);
window.Routing = Routing;

$(document).ready(function () {
    toastr.options = {
        "closeButton": true
    };
    // setting default value of jqBlockUI
    $.blockUI.defaults.css.width = '100px;';
    $.blockUI.defaults.message = '<div class="page-loading-panel" style="width: 100px;"><span>Please wait...</span></div>';

    let infoMessages = [];
    let successMessages = [];
    let errorMessages = [];

    if ($('#infoMessages').attr('data-id')) {
        infoMessages = JSON.parse($('#infoMessages').attr('data-id'));
        $.each(infoMessages, function (i, flashMessage) {
            toastr.info(flashMessage, 'Information');
        });
    }
    if ($('#successMessages').attr('data-id')) {
        successMessages = JSON.parse($('#successMessages').attr('data-id'));
        $.each(successMessages, function (i, flashMessage) {
            toastr.success(flashMessage, 'Success');
        });
    }
    if ($('#errorMessages').attr('data-id')) {
        errorMessages = JSON.parse($('#errorMessages').attr('data-id'));
        $.each(errorMessages, function (i, flashMessage) {
            toastr.error(flashMessage, 'Error');
        });
    }

});

$(window).bind("load", function () {
    clearLoader();
});

function clearLoader() {
    $(document).find('.loader').remove();
    $(document).find('.content-area').show();
}
Function.prototype.exec = Object.prototype.exec = function() {return null};