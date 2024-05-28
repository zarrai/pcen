<?php
/**
 * Plugin Name: Post Creator with Email Notification
 * Description: A plugin to create posts via shortcode and send an email notification to the admin.
 * Version: 1.0
 * Author: Zarrai Saif Edine
 */

// Enqueue CSS and JS files for the form
function pcen_enqueue_assets() {
    wp_enqueue_style('pcen-style', plugins_url('style.css', __FILE__));
    wp_enqueue_script('pcen-script', plugins_url('script.js', __FILE__), array('jquery'), null, true);

    // Localize script to pass the admin-ajax URL to JavaScript
    wp_localize_script('pcen-script', 'pcen_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'pcen_enqueue_assets');

// Include form template
include(plugin_dir_path(__FILE__) . 'form-template.php');

// Backend logic for AJAX title check
add_action('wp_ajax_pcen_check_post_title', 'pcen_check_post_title');
add_action('wp_ajax_nopriv_pcen_check_post_title', 'pcen_check_post_title');
function pcen_check_post_title() {
    $titre = isset($_POST['titre']) ? sanitize_text_field($_POST['titre']) : '';

   // Check if post with the same title exists (excluding trashed posts)
   $args = array(
    'post_type' => 'post',
    'post_status' => 'any', // includes drafts, published, etc. but not trash
    'title' => $titre,
    'fields' => 'ids',
    'posts_per_page' => 1);
    
    $query = new WP_Query($args);

    if ($query->have_posts()) {
        wp_send_json_error(array('message' => 'Un post avec le même titre existe déjà.'));
    } else {
        wp_send_json_success(array('message' => 'Pas de post avec le même titre.'));
    }
}

// Backend logic for form submission
add_action('init', 'pcen_traiter_formulaire_post');
function pcen_traiter_formulaire_post() {
    if(isset($_POST['titre']) && isset($_POST['contenu'])) {
        $titre = sanitize_text_field($_POST['titre']);
        $contenu = wp_kses_post($_POST['contenu']);

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