if (typeof (wlrp_jquery) == "undefined") {
    wlrp_jquery = jQuery.noConflict();
}
(function ($) {
    $(document).on('submit', '#wlrp-save-settings', function (event) {
        event.preventDefault();
        let formData = $(this).serialize();
        $.ajax({
            url: wlrp_localize_data.ajax_url,
            type: 'POST',
            dataType: 'JSON',
            data: formData + '&action=wlrp_save_settings&wlrp_nonce=' + wlrp_localize_data.save_nonce,
            success: function (response) {
                alertify.set('notifier', 'position', 'top-right');
                if (response.success) {
                    alertify.success(response.data.message);
                } else {
                    alertify.error(response.data.message);
                }
            },
            error: function (xhr, status, error) {
                alertify.error(error);
            }
        });
    });
})(wlrp_jquery);