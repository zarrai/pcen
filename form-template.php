<?php
// Shortcode to display the form
add_shortcode('creer_post', 'pcen_creer_post_shortcode');
function pcen_creer_post_shortcode() {
    ob_start();
    ?>
    <form method="post" id="creer_post_form">
        <label for="titre">Titre :</label><br/>
        <input type="text" id="titre" name="titre" required>
        
        <label for="contenu">Texte :</label><br/>
        <textarea id="contenu" name="contenu" required></textarea>
        <div id="pcen-error-message" style="color:red;"></div></br>
        
        <input type="submit" value="Créer Post">
    </form>

    <script>
    jQuery(document).ready(function($) {
        $('#creer_post_form').submit(function(event) {
            event.preventDefault();

            var titre = $('#titre').val();

            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'pcen_check_post_title',
                    titre: titre
                },
                success: function(response) {
                    if (response.success) {
                        $('#pcen-error-message').html('').hide();
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

</script>
    <?php
    return ob_get_clean();
}

