/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
// import '../styles/app.css';
import 'toastr/build/toastr.min.css';
import 'jquery-confirm/css/jquery-confirm.css';
import '../css/custom.css';
import '../css/style.css';

import 'bootstrap';
import './jquery-ui-1.10.3.custom.min';
import 'jquery-confirm';
import 'bootstrap-datepicker';
import 'jquery-blockui';
import './mindmup-editabletable';
import toastr from 'toastr';
import './html5shiv.min';

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

    if($('#infoMessages').attr('data-id')){
        infoMessages = JSON.parse($('#infoMessages').attr('data-id'));
        $.each(infoMessages, function (i, flashMessage){
            toastr.info(flashMessage, 'Information');
        });
    }
    if($('#successMessages').attr('data-id')){
        successMessages = JSON.parse($('#successMessages').attr('data-id'));
        $.each(successMessages, function (i, flashMessage){
            toastr.success(flashMessage, 'Success');
        });
    }
    if($('#errorMessages').attr('data-id')){
        errorMessages = JSON.parse($('#errorMessages').attr('data-id'));
        $.each(errorMessages, function (i, flashMessage){
            toastr.error(flashMessage, 'Error');
        });
    }
});

$('[data-toggle="tooltip"]').tooltip();
$(".preloader").fadeOut();
// ==============================================================
// Login and Recover Password
// ==============================================================
$('#to-recover').on("click", function() {
    $("#loginform").slideUp();
    $("#recoverform").fadeIn();
});