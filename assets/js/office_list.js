import {initChosen} from './common';

$(document).ready(function () {
    initChosen();

    let searchFlag = $('#searchFlag').attr('data-id');
    if (searchFlag == 0)
        $('.contact-search-box').hide();

    $('.search-button').click(function () {

        $('.contact-search-box').toggle();

    });
});