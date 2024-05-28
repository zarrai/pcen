<?php
/**
 * Plugin Name: Post Creator with Email Notification
 * Description: A plugin to create posts via shortcode and send an email notification to the admin.
 * Version: 1.0
 * Author: Zarrai Saif Edine
 */

// Enqueue CSS file for the form
add_action('wp_enqueue_scripts', 'pcen_enqueue_styles');
function pcen_enqueue_styles() {
    wp_enqueue_style('pcen-style', plugins_url('style.css', __FILE__));
}

// Include form template
include(plugin_dir_path(__FILE__) . 'form-template.php');

add_action('init', 'pcen_traiter_formulaire_post');
function pcen_traiter_formulaire_post() {
    if(isset($_POST['titre']) && isset($_POST['contenu'])) {
        $titre = sanitize_text_field($_POST['titre']);
        $contenu = wp_kses_post($_POST['contenu']);

        // Vérifier si le titre existe déjà
        $existing_post = get_page_by_title($titre, OBJECT, 'post');
        if($existing_post) {
            wp_die('Un post avec le même titre existe déjà.');
        }

        // Créer le post non publié
        $post_data = array(
            'post_title'    => $titre,
            'post_content'  => $contenu,
            'post_status'   => 'draft',
            'post_author'   => get_current_user_id(),
            'post_type'     => 'post'
        );
        $post_id = wp_insert_post($post_data);

        // Envoyer un email à l'administrateur
        $admin_email = get_option('admin_email');
        $subject = 'Nouveau post créé';
        $message = "Un nouveau post a été créé avec le titre : $titre\n\nContenu :\n$contenu";
        wp_mail($admin_email, $subject, $message);

        // Rediriger l'utilisateur
        wp_redirect(home_url());
        exit;
    }
}
