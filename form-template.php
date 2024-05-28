<?php
// Shortcode to display the form
add_shortcode('creer_post', 'pcen_creer_post_shortcode');
function pcen_creer_post_shortcode() {
    ob_start();
    ?>
    <form method="post" id="creer_post_form">
        <label for="titre">Titre :</label><br/>
        <input type="text" id="titre" name="titre" required><br/><br/>
        
        <label for="contenu">Texte :</label><br/>
        <textarea id="contenu" name="contenu" required></textarea><br/><br/>
        
        <input type="submit" value="Créer Post">
    </form>
    <?php
    return ob_get_clean();
}
