<?php
/*
Plugin Name: Notifications Center
Plugin URI: http://www.notificationscenter.com/
Description: Personnalized notifications for your Wordpress website with beautiful, responsive and personnalised emails.
Version: 1.5.0
Author: Florian Chaillou
Author URI: http://www.notificationscenter.com 
Text Domain: notifications-center
Domain Path: /languages
*/

if( !class_exists( 'VOYNOTIF_plugin' ) ) {
    class VOYNOTIF_plugin {

        
        /**
         * @var object(VOYNOTIF_updater) 
         */
        var $updater;

        /**
         * Plugin init
         * 
         * @author Floflo
         * @since 0.9
         * @update 2017-01-17
         */
        function __construct() {

            //------------------------------------------------------------//
            // 1. Constants
            //------------------------------------------------------------// 
            define('VOYNOTIF_DIR', WP_PLUGIN_DIR . '/' . basename( dirname( __FILE__ ) ) );
            define('VOYNOTIF_URL', plugins_url() . '/' . basename( dirname( __FILE__ ) ) );
            define('VOYNOTIF_VERSION', '1.5.0');
            define('VOYNOTIF_FIELD_PREFIXE', 'voynotif_');
            define('VOYNOTIF_PREMIUM_URL', 'http://www.notificationscenter.com');
            define('VOYNOTIF_EXPORT_VERSION', '1.1.0');

            //------------------------------------------------------------//
            // 2. Plugin activation hook
            //------------------------------------------------------------// 
            register_activation_hook( __FILE__, array( $this, 'install' ) );

            //------------------------------------------------------------//
            // 3. Hook setup
            //------------------------------------------------------------// 
            add_action( 'init', array( $this, 'post_type_register' ), 0 );
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_loadscripts' ) );
            add_action( 'plugins_loaded', array( $this, 'load_text_domain' ) );
            add_action( 'plugins_loaded', array( $this, 'init' ), 20 );
            add_action( 'template_redirect', array( $this, 'template_preview' ) );
            add_action( 'admin_notices', array( $this, 'admin_notices' ) );                
            
            /**
             * @since 1.1.0
             */
            if( get_option( VOYNOTIF_FIELD_PREFIXE . 'extend_sender' ) == 'wordpress' ) {
                add_action( 'wp_mail_from_name', array( $this, 'extend_sender_name'), 100, 1 );
                add_action( 'wp_mail_from', array( $this, 'extend_sender_email' ), 100, 1 );    
            }
            
            //------------------------------------------------------------//
            // 4. Includes
            //------------------------------------------------------------//
            include_once('updater.php');
            
            include_once('core/framework/class.notification_type.php');
            include_once('core/framework/class.compat.php');
            include_once('core/framework/class.addon.php');
            include_once('core/framework/class.settings_screen.php');
            
            include_once('admin/notifications.php');
            include_once('admin/notification.php');   
            include_once('admin/template-customizer.php');
            include_once('admin/settings.php');
            include_once('admin/settings-import.php');
            include_once('admin/settings-logs.php');
            //include_once('admin/help.php');

            include_once('core/class.notification.php');
            include_once('core/class.email_template.php');           
            include_once('core/class.field.php');
            include_once('core/functions.php');
            include_once('core/class.masks.php');
            include_once('core/class.logs.php');
            include_once('core/template.php');
           
                     
            
            
            //------------------------------------------------------------//
            // 5. CLASS Init
            //------------------------------------------------------------//
            $this->updater = new VOYNOTIF_updater();
            $this->admin_notices = array();

        }
        
        public function answer_expiration_event() {
            error_log('answer_expiration_event');
        }
        

        /**
         * Install plugin with dummy data (current template, colors, etc)
         * 
         * @author Floflo
         * @since 0.9
         * @update 2016-07-19
         */
        function install() {
            
            //Multisite install
            if( is_multisite() ) {
                $sites = wp_get_sites();
                foreach ( $sites as $i => $site ) {
                    switch_to_blog( $site[ 'blog_id' ] );
                    $this->updater->init();
                    restore_current_blog();
                }

            //Single site install    
            } else {
                $this->updater->init();
            }

        }


        /**
         * Post type register
         * 
         * @author Floflo
         * @since 0.9
         * @update 2016-06-02
         */    
        function post_type_register() {

            $labels = array(
                'name'                => __( 'Notifications', 'Post Type General Name', 'notifications-center' ),
                'singular_name'       => __( 'Notification', 'Post Type Singular Name', 'notifications-center' ),
                'menu_name'           => __( 'Notifications', 'notifications-center' ),
                'all_items'           => __( 'All notifications', 'notifications-center' ),
                'view_item'           => __( 'See notification', 'notifications-center' ),
                'add_new_item'        => __( 'New notification', 'notifications-center' ),
                'add_new'             => __( 'New notification', 'notifications-center' ),
                'edit_item'           => __( 'Modify', 'notifications-center' ),
                'update_item'         => __( 'Update', 'notifications-center' ),
                'search_items'        => __( 'Search', 'notifications-center' ),
                'not_found'           => __( 'No notification found', 'notifications-center' ),
                'not_found_in_trash'  => __( 'Not found in Trash', 'notifications-center' ),
            );
            $args = apply_filters( 'voy/notif/install/register_cpt', 
                array(
                    'label'               => __( 'voy_notification', 'notifications-center' ),
                    'description'         => __( 'voy_notification', 'notifications-center' ),
                    'labels'              => $labels,
                    'supports'            => array( 'title', 'editor', 'author', 'revisions', ),
                    'hierarchical'        => false,
                    'public'              => false,
                    'show_ui'             => true,
                    'show_in_menu'        => false,
                    'show_in_nav_menus'   => false,
                    'show_in_admin_bar'   => false,
                    'menu_position'       => 39,
                    'menu_icon'           => 'dashicons-email',
                    'can_export'          => true,
                    'has_archive'         => false,
                    'exclude_from_search' => false,
                    'publicly_queryable'  => false,
                    'capability_type'     => 'post',
                ) 
            );
            register_post_type( 'voy_notification', $args );  

        }


        /**
         * Load admin scripts
         * 
         * @author Floflo
         * @since 0.9
         * @update 2016-06-02
         */
        function admin_loadscripts() {
            wp_register_style( 'voynotif_admin_css', VOYNOTIF_URL . '/assets/css/admin.css', false, VOYNOTIF_VERSION );
            wp_enqueue_style( 'voynotif_admin_css' );        
        }


        /**
         * Load text domain
         * 
         * @author Floflo
         * @since 0.9
         * @update 2016-06-02
         */
        function load_text_domain() {
            load_plugin_textdomain( 'notifications-center', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
        }


        /**
         * Init plugin with loading notifications & templates
         * 
         * @author Floflo
         * @since 0.9
         * @update 2016-07-13
         */
        function init() {

            //Add compat files with other plugins
            include_once('core/compat/duplicate-post.php'); 
            include_once('core/compat/gravityforms.php'); 
            include_once('core/compat/woocommerce.php'); 
            
            //Load Notifications
            include_once('notifications/comment_new.php');
            include_once('notifications/comment_reply.php');
            include_once('notifications/comment_moderate.php');
            include_once('notifications/content_draft.php');
            include_once('notifications/content_future.php');
            include_once('notifications/content_pending.php');
            include_once('notifications/content_publish.php');
            include_once('notifications/content_trash.php');
            include_once('notifications/core_update.php');            
            include_once('notifications/user_login.php');
            include_once('notifications/user_password_changed.php');
            include_once('notifications/user_password_reset.php');
            include_once('notifications/user_register.php');
            
            //Check for udpate
            if( $this->updater->current_version !== $this->updater->new_version ) {
                $this->updater->update();
            }
            
            /**
             * ACTION voynotif/loaded
             * Triggered when Notifications Center is fully loaded (both function & notifications)
             * 
             * @author Floflo
             * @since 1.3.0
             * @update 2017-08-26
             */
            do_action('voynotif/loaded');

        }


        /**
         * Generate template preview
         * 
         * @author Floflo
         * @since 0.9
         * @update 2016-07-13
         */
        function template_preview() {
            if( is_singular( 'voy_notification' ) ) {
                if( is_user_logged_in() ) {
                    $notification = new VOYNOTIF_notification( get_the_ID() );
                    echo $notification->get_html();              
                } else {
                    _e( 'You must be logged in to preview this notification', 'notifications-center' );
                }
                exit();
            }
        }
        
        
        /**
         * Overrides Wordpress default sender name
         * 
         * @author Floflo
         * @since 1.1.0
         * @update 2016-12-11
         * 
         * @param string $name Wordpress default sender name
         * @return string Notifications Center sender name
         */
        function extend_sender_name( $name ) {
            
            $sender_name = get_option( VOYNOTIF_FIELD_PREFIXE . 'sender_name' );
            
            if( !empty( $sender_name ) ) {
                return $sender_name;
            } 
            
            return $name;
            
        }
        
        
        /**
         * Overrides Wordpress default sender email
         * 
         * @author Floflo
         * @since 1.1.0
         * @update 2016-12-11
         * 
         * @param string $email Wordpress default sender email
         * @return string Notifications Center sender email
         */
        function extend_sender_email( $email ) {
            
            $sender_email = get_option( VOYNOTIF_FIELD_PREFIXE . 'sender_email' );
            
            if( is_email( $sender_email ) ) {
                return $sender_email;
            } 
            
            return $email;
            
        }
        
        /**
         * 
         * @return type
         */
        function admin_notices() {
            
            if( empty( $this->admin_notices ) ) {
                return;
            }
            
            foreach ($this->admin_notices as $notice) {
            ?>
            <div class="notice notice-<?php echo $notice['class']; ?> is-dismissible">
                <p><?php echo $notice['message']; ?></p>
            </div>
            <?php
            }
        }
        
        public function cron_activation() {
            if( !wp_next_scheduled( 'notifications_center_logs_deletion' ) ) {
                error_log('activation');
                wp_schedule_event( time(), 'daily', 'notifications_center_logs_deletion' );
            }          
        }
        
        public function cron_deactivation() {
            wp_clear_scheduled_hook('notifications_center_logs_deletion');
        }
        
        
        
    }
}
$notifications_center = new VOYNOTIF_plugin();

register_activation_hook( __FILE__, array($notifications_center, 'cron_activation') );
register_deactivation_hook(__FILE__, array($notifications_center, 'cron_deactivation') );






