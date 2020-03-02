<?php
add_action( 'wp_ajax_fc_delete_contact', 'fc_delete_contact' );
add_action( 'wp_ajax_nopriv_fc_delete_contact', 'fc_delete_contact' );

function fc_delete_contact(){
    if(wp_delete_post( $_POST['id'], true)) echo true;
    wp_die();
}