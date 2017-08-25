<?php
/*
 * -------------------------------------------------------------------------
 * COMMON API
 * ------------------------------------------------------------------------- 
 */

/**
 * Get excerpt by post ID
 */
if( !function_exists('voynotif_get_excerpt_by_id') ) {
    function voynotif_get_excerpt_by_id( $post_id = 0, $length = 150 ) {
        global $post;
        $save_post = $post;
        $post = get_post( $post_id );
        setup_postdata( $post );
        $excerpt = substr(get_the_excerpt(), 0,$length);
        $post = $save_post;
        wp_reset_postdata( $post );
        return $excerpt;
    }
}


/*
 * -------------------------------------------------------------------------
 * TEMPLATE API
 * ------------------------------------------------------------------------- 
 */


/**
 * FUNCTION get_current_email_template
 * Renvoie le template choisi par l'utilisateur
 * 
 * @deprecated since version 1.2.0
 * @author Floflo
 * @since 0.9
 * @update 2016-04-22
 * 
 * @return string template name
 **/
if( !function_exists('voynotif_get_current_email_template') ) {
    function voynotif_get_current_email_template() {
        return 'plugin';
    }
}


/**
 * Renvoi un array de tous les templates enregistrés
 * 
 * @deprecated since version 1.2.0
 * @author Floflo
 * @since 0.9
 * @return array Array of all registered templates
 */
if( !function_exists('voynotif_get_templates') ) {
    function voynotif_get_templates() {
        $templates = apply_filters( 'voynotif/get_templates', '' );
        return $templates;
    }
}


/**
 * Get template data by tempalte name/id
 * 
 * @deprecated since version 1.2.0
 * @author Floflo
 * @since 0.9
 * 
 * @global type $voy_notifs_global_registered_templates
 * @param type $template_id Template_id
 * @return mixed Array of template data or false
 */
if( !function_exists('voynotif_get_template') ) {
    function voynotif_get_template($template_id) {
        $templates = voynotif_get_templates(); 

        //If template exists
        if( array_key_exists( $template_id, $templates ) ) {
            return $templates[$template_id];
        }

        //Return False if template ID doesn't exist
        return false;
    }
}

    
/*
 * -------------------------------------------------------------------------
 * NOTIFICATIONS API
 * ------------------------------------------------------------------------- 
 */


/**
 * Get notifications types.
 * 
 * @author Floflo
 * @since 0.9
 * @since 1.1 New $tag parameter added to get types regarding to a specific tag
 * @update 2016/12-27
 * 
 * @param string $tag Tag ID
 * @return array array of notification types, like array( slug => label )
 */
if( !function_exists('voynotif_get_notifications_types') ) {
    function voynotif_get_notifications_types( $tag = null ) {   
        $types = apply_filters( 'voynotif/notifications/types', array() );  
        
        if( ! empty( $tag ) ) {
            $matching_types = array();
            foreach( $types as $id => $data ) {
                if( $data['tags'][0] != $tag ) continue;
                $matching_types[$id] = $data;
            }
            return $matching_types;
        }
        
        return $types;
    }
}


/**
 * Get allowed notifications tags. Used for displaying notification choice
 * 
 * @author Floflo
 * @since 0.9
 * @update 2016-07-28
 * 
 * @return array array of notification tags, like array( slug => label )
 */
if( !function_exists('voynotif_get_notifications_tags') ) {
    function voynotif_get_notifications_tags() {   
        $tags = apply_filters( 'voynotif/notifications/tags', array(
            'system' => __( 'System', 'notifications-center' ),
            'content' => __( 'Content', 'notifications-center' ),
            'comment' => __( 'Comment', 'notifications-center' ),
            'user' => __( 'User', 'notifications-center' ),
            'summary' => __( 'Summary', 'notifications-center' ),
            'multisite' => __( 'Multisite', 'notifications-center' ),
        ) );
        return $tags;
    }
}


/**
 * Get notification regarding to a specific notification type
 * 
 * @author Floflo
 * @since 0.9
 * @update 2016-07-28
 * 
 * @param string $notification_type Slug of the notification type
 * @return array Array of notifications as objects like array ( 0 => NOTIF_notification, etc). Return empty array if no matching notifications
 */
if( !function_exists('voynotif_get_notifications') ) {
    function voynotif_get_notifications( $notification_type ) {

        $matching_notifications = array();

        //Build and send query to get notifications
        $notifications = get_posts( array(
            'post_type' => 'voy_notification', 
            'meta_key' => VOYNOTIF_FIELD_PREFIXE . 'type', 
            'meta_value' => $notification_type, 
            'posts_per_page' => -1
        ) );

        //if there is no matching notification
        if( empty( $notifications ) ) {
            return $matching_notifications;  
        }

        //Build notifications objects
        foreach( $notifications as $notification ) {
            $matching_notifications[] = new VOYNOTIF_notification($notification->ID);
        }

        //Return only one or all matching notifications, regarding to $only_one param
        return $matching_notifications;

    }
}


/*
 * -------------------------------------------------------------------------
 * SETTINGS API
 * ------------------------------------------------------------------------- 
 */


/**
 * 
 */
if( !function_exists('voynotif_get_settings_screens') ) {
    function voynotif_get_settings_screens() {
        return apply_filters( 'voynotif/settings/screens', array() );
    }
}


/**
 * 
 */
if( !function_exists('voynotif_get_settings_fields') ) {
    function voynotif_get_settings_fields( $screen_id = null ) {
        $fields = apply_filters( 'voynotif/settings/fields', array() );;
        if( empty( $screen_id ) ) {
            return $fields;
        }
        
        foreach( $fields as $field_id => $field_data ) {
            if( $field_data['screen'] != $screen_id ) {
                unset( $fields[$field_id] );
            }
        }      
        return $fields;
    }
}

/**
 * 
 */
if( !function_exists('voynotif_get_notification') ) {
    function voynotif_get_notification( $notification_id ) {
        if( get_post( $notification_id ) ) {
            return new VOYNOTIF_notification( $notification_id );
        }
        return null;
    }
}

?>