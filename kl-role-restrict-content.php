<?php
/*
Plugin Name: KL Role Restrict Content Shortcode
Plugin URI: https://github.com/educate-sysadmin/kl-role-restrict-content
Description: Wordpress plugin to for shortcode access controls by role
Version: 0.1
Author: b.cunningham@ucl.ac.uk
Author URI: https://educate.london
License: GPL2
*/

$klrrc_config = array(
    'divup' => true, // surround content with divs with role classes
);

function klrrc_shortcode( $atts, $content = null ) {
    global $klrrc_config;

	$output = '';
	$class = ' klrrc '; // to populate in case needed for divup
    // parse options
	$options = shortcode_atts( array( 'roles' => '' ), $atts );
	$show_content = false;

	if ( $content !== null) {
        if (!isset($options['roles']) || $options['roles'] === null || $options['roles'] === "") {
            $show_content = true;            
        } else {
            $show_content = false;
            $user_roles = KLUtils::get_user_roles();
            $roles_allowed = explode(",", $options['roles']);
            foreach ($roles_allowed as $role) {            
                $class .= 'klrrc-'.$role.' ';
            }
            foreach ($roles_allowed as $role) {
                if (in_array($role, $user_roles)) {
                    $show_content = true;
                    break;
                }
            }
        }

		if ( $show_content ) {
			remove_shortcode( 'kl_role_restrict' );
			$content = do_shortcode( $content );
			add_shortcode( 'kl_role_restrict', 'klrrc_shortcode' );
            if ($klrrc_config['divup']) { $output .= '<div class = "'.$class.'">'; }
			$output .= $content;
            if ($klrrc_config['divup']) { $output .= '</div>'; }
		}
	}
	return $output;
}

add_shortcode( 'kl_role_restrict', 'klrrc_shortcode' );
