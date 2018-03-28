<?php

class VOYNOTIF_logs {
    
    const TABLE_NAME_BASE = 'voynotif_sent_logs';
    const POSTS_PER_PAGE = 20;
    
    
    /**
     * 
     */
    function __construct() {
        //Nothing at the moment
        add_action('template_redirect', array('VOYNOTIF_logs', 'update_notification_status') );
        add_action('voynotif/template/footer', array('VOYNOTIF_logs', 'add_pixel') );
    }
    
    
    /**
     * 
     * @global type $voynotif_log
     * @return type
     */
    public static function add_pixel() {
        global $voynotif_log;
        if( !isset($voynotif_log) || !isset($voynotif_log->id) ) return;
        echo '<img src="https://dev.floflo.fr/wp/?voynotif_log_id='.$voynotif_log->id.'&voynotif_log_token='.$voynotif_log->token.'" />';
    }
    
    
    /**
     * 
     * @return type
     */
    public static function update_notification_status() {
        
        //Check if $_get needed params are here
        if( !isset( $_GET['voynotif_log_id'] ) || !isset( $_GET['voynotif_log_token'] ) ) return;
        
        //Get basic info
        $log_id = absint($_GET['voynotif_log_id']);
        $log = VOYNOTIF_logs::get_log($log_id);  
        
        //Check if token is correct
        if( $log->token != $_GET['voynotif_log_token'] ) {
            return;
        }
        
        //Updade log status to opened & add new opened date
        $args['id'] = $log_id;
        $args['status'] = 2;
        $log->opens[] = date('Y-m-d H:i:s');
        $args['opens'] = serialize($log->opens);        
        VOYNOTIF_logs::update_log($args);
        
        //Return 1px x 1px png image
        $im = imagecreatefrompng(VOYNOTIF_URL . "/assets/img/pixel.png");
        ob_clean();
        header('Content-Type: image/png');
        header("Content-Disposition: inline; filename=\"pixel.png\"");
        imagepng($im);
        imagedestroy($im);
        die();      
    }
    
    public static function load_png($imgname) {
        /* Tente d'ouvrir l'image */
        $im = @imagecreatefrompng($imgname);

        /* Traitement en cas d'échec */
        if (!$im) {
            /* Création d'une image vide */
            $im = imagecreatetruecolor(150, 30);
            $bgc = imagecolorallocate($im, 255, 255, 255);
            $tc = imagecolorallocate($im, 0, 0, 0);

            imagefilledrectangle($im, 0, 0, 150, 30, $bgc);

            /* On y affiche un message d'erreur */
            imagestring($im, 1, 5, 5, 'Erreur de chargement ' . $imgname, $tc);
        }

        return $im;
    }

    /**
     * 
     * @global type $wpdb
     * @return type
     */
    public static function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . self::TABLE_NAME_BASE;
    }
    
    
    /**
     * 
     * @param type $type
     * @param type $recipient
     * @param type $object
     * @param type $result
     * @param type $args
     * @return boolean
     */
    public static function add_log( $args = array() ) {

        $defaults = array(
            'date'              => date('Y-m-d H:i:s'),
            'notification_id'   => '',
            'type'              => __('Unknown', 'notifications-center'),
            'recipient'         => '',
            'subject'           => '',
            'title'             => '',
            'status'            => '',
            'context'           => array(),   
        );
        
        $args = wp_parse_args($args, $defaults);
        
        if( empty( $args['recipient'] ) ) {
            return false;
        }
        
        global $wpdb;
        $result = $wpdb->insert( 
            self::get_table_name(), 
            array( 
                'id'                => '',
                'notification_id'   => $args['notification_id'],
                'date'              => $args['date'], 
                'type'              => $args['type'],
                'recipient'         => $args['recipient'],
                'subject'           => $args['subject'],
                'title'             => $args['title'],
                'status'            => $args['status'],
                'context'           => serialize($args['context']), 
                'token'             => wp_generate_password(40, false),
                'opens'             => serialize(array()),
            )            
        );
        
        if( $result === 1 ) {
            return VOYNOTIF_logs::get_log($wpdb->insert_id);
        }
        
        return false;
    }
    
    /**
     * 
     * @param array $args
     * @return int
     */
    public static function get_logs_count( $args ) {
        
        unset($args['paged']);
        $args['per_page'] = -1;  
        $logs = VOYNOTIF_logs::get_logs( $args );
        
        if( empty( $logs ) ) {
            return 0;
        }
        
        return count($logs);
    }
    
    /**
     * 
     * @global type $wpdb
     * @param type $args
     * @return type
     */
    public static function get_logs( $args = array() ) {
        
        $defaults = array(
            'orderby'       => 'date',
            'order'         => 'DESC',
            'date_begin'    => '',
            'date_end'      => '',
            'paged'         => 1,
            'per_page'      => self::POSTS_PER_PAGE
        );
        $args = wp_parse_args($args, $defaults);

        $per_page = $args['per_page'];
        if( $per_page === -1 ) $per_page = 1000000000;
        $offset = 0;
        if( $args['paged'] > 1 ) {
            $offset = ($args['paged'] - 1) * $per_page; 
        }
        
        global $wpdb;
        $table = self::get_table_name();
        $fivesdrafts = $wpdb->get_results( 
            "
            SELECT id, notification_id, type, recipient, subject, title, status, context, date, token, opens  
            FROM $table
                ORDER BY ".$args['orderby']." ".$args['order']."
                LIMIT $per_page OFFSET $offset
            "
        );
        return $fivesdrafts; 
        
    }
    
    
    /**
     * 
     */
    public static function update_log( $args ) {
        
        if( !is_array($args) || empty($args) || !isset( $args['id'] ) || empty($args['id']) ) {
            return false;
        }
        
        $log_id = $args['id'];
        unset( $args['id'] );
        
        global $wpdb;
        $result = $wpdb->update( 
                self::get_table_name(), 
                $args, 
                array( 'id' => $log_id ), 
                null,
                array( '%d' ) 
        ); 
        
    }
    
    public static function get_log( $log_id ) {
        
        if( empty( $log_id ) ) {
            return false;
        }
        global $wpdb;
        $table = self::get_table_name();
        $logs = $wpdb->get_results( 
            "
            SELECT id, notification_id, type, recipient, subject, title, status, context, date, token, opens  
            FROM $table
                WHERE id = '".$log_id."'
            "
        );
        if( !empty($logs) ) {
            $log = $logs[0];
            $log->context = unserialize($log->context);
            $log->opens = unserialize($log->opens);
            return $log;
        }
        return false;        
    }
    
    
    /**
     * 
     * @param type $status_id
     * @return type
     */
    public static function get_status_title( $status_id ) {
        
        $statuses = apply_filters( 'voynotif/logs/status', array(
            0 => __('Not sent', 'notifications-center'),
            1 => __('Sent', 'notifications-center'),
            2 => __('Opened', 'notifications-center'),
        ) );
        
        if( array_key_exists( $status_id, $statuses ) ) {
            return $statuses[$status_id];
        }
        
        return __('Unknown', 'notifications-center');
        
    }
    
    
    /**
     * 
     * @param type $param
     * @return type
     */
    public static function get_order_url( $param ) {
        $orderby = ( isset( $_GET['orderby'] ) && !empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : false;
        $order = ( isset( $_GET['order'] ) && $_GET['order'] == 'ASC' ) ? 'DESC' : 'ASC';       
        $url = add_query_arg( 'orderby', $param );
        $url = add_query_arg( 'order', $order, $url ); 
        $url = add_query_arg( 'paged', 1, $url ); 
        
        return $url;
    }
    
    public static function get_context_html( $context, $type ) {
        
        if( empty( $context ) ) return false;
        
        $specific_return = apply_filters('voynotif/logs/context/type='.$type, false, $context);
        
        if( $specific_return ) {
            return $specific_return;
        }
        
        $user_id = ( array_key_exists('user_id', $context) ) ? array_key_exists('user_id', $context) : false;
        $post_id = ( array_key_exists('post_id', $context) ) ? array_key_exists('post_id', $context) : false;
        $comment_id = ( array_key_exists('comment_id', $context) ) ? array_key_exists('comment_id', $context) : false;
        
        if( $user_id && !$post_id && !$comment_id ) {
            $user_html = '<a href="'.get_edit_user_link($context['user_id']).'">'.get_user_by('id',$context['user_id'])->user_nicename.'</a>';
            return sprintf( __('About user %s', 'notifications-center'), $user_html );            
        }
        
        elseif( !$user_id && $post_id && $comment_id ) {
            $post_html = '<a href="'.get_permalink($context['post_id']).'">'.get_the_title( $context['post_id'] ).'</a>';
            $comment_html = '<a href="'.get_comment_link($context['comment_id']).'">#'.$context['comment_id'].'</a>';
            return sprintf( __('About comment %s on %s', 'notifications-center'), $comment_html, $post_html );                        
        }
        
        elseif( !$user_id && $post_id && !$comment_id ) {
            $post_html = '<a href="'.get_permalink($context['post_id']).'">'.get_the_title( $context['post_id'] ).'</a>';
            return sprintf( __('About content %s', 'notifications-center'), $post_html );                        
        }
        
        return false;
        
    }
    
}

new VOYNOTIF_logs();

