$(document).ready(function () {
    $(document).on("click", ".save-data", function (e) {
        e.preventDefault();
        var routeURL = $(this).attr("data-route");
        var formId = 'ajaxForm';
        $.ajax({
            type: "POST",
            url: routeURL,
            data: new FormData($('form#'+formId)[0]),
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
            'success': function (data) {
                if(data.status == 'success'){
                    if(data.pageReload === false) {
                        $('.main-list-div').html(data.templateData);
                        if($('.datatable').length > 0) {
                            $('.datatable').DataTable({
                                "bFilter": false,
                            });
                        }
                        toastr.success(data.message, "Success");
                        $("#add_modal.custom-modal").modal('hide');
                    }
                    else {
                        location.reload();
                        return true;
                    }
                } else {
                    $("#add_modal").html( data );
                    initChosen();
                    updateMultiSelectCheckbox();
                    $("#add_modal.custom-modal").modal('show');
                }
            },
            'error': function (data) {

            }
        });

        return false;
    });

    $(document).on("click",".delete-record",function(){
        var routeURL = $(this).attr("data-route");
        $.confirm({
            title: 'Confirmation',
            content: 'Are you sure you want delete this record?',
            buttons: {
                ok: {
                    text: "Yes",
                    btnClass: 'btn btn-danger',
                    keys: ['enter'],
                    action: function(){
                        window.location.href = routeURL;
                    }
                },
                cancel: function(){

                }
            }
        });
    });

    $(document).on('click', '.open-modal', function () {
        var routeURL = $(this).attr('data-route');
        // var labelName = $(this).attr('data-label');
        $.ajax({
            'method': 'get',
            'url': routeURL,
            'success': function (data) {
                $("#add_modal").html(data);
                initChosen();
                updateMultiSelectCheckbox();
                $("#add_modal.custom-modal").modal('show');
            },
            'error': function (data) {

            }
        });
    });

    $(document).on('click', '.delete-modal, .common-modal', function () {
        var dataRoute = $(this).attr("data-route");
        $('.continue-btn').attr('href', dataRoute);
    });

});

function initChosen() {

    $('.chosen-select').chosen({search_contains: true, width: '100%'});
    $('.chosen-select').each(function (index) {
        this.setAttribute('style', 'display:visible; position:absolute; clip:rect(0,0,0,0)');
    });

    $(".hidden-chosen-select").chosen({width: '100% !important', search_contains: true});
}

export {initChosen};