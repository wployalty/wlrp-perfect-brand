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
                (response.success) ? showToast('success', response.data.message) : showToast('invalid', response.data.message);
            },
            error: function (xhr, status, error) {
                showToast('error', xhr)
            }
        });
    });
})(wlrp_jquery);