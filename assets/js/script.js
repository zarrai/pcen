jQuery(document).ready(function($) {
    $('#creer_post_form').submit(function(event) {
        event.preventDefault();

        var titre = $('#titre').val();

        $.ajax({
            url: pcen_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'pcen_check_post_title',
                titre: titre
            },
            success: function(response) {
                if (response.success) {
                    $('#pcen-error-message').css('color', 'green');
                    $('#pcen-error-message').html(response.data.message).show();
                    $('#creer_post_form').unbind('submit').submit();
                } else {
                    $('#pcen-error-message').html(response.data.message).show();
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                console.log(xhr.responseText);
            }
        });
    });
});
