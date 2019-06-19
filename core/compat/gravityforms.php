<?php
/**
 * Compatibility woth Duplicate Post, to allow users to send Notifications when a post is duplicated
 */
class VOYNOTIF_compat_gravityforms extends VOYNOTIF_compat {
    
    function __construct() {
        $this->dependencies = array(
            'Duplicate Post' => array(
                'file'      => 'gravityforms/gravityforms.php',
                'version'   => '2.2.5'
            ),
        );  
        parent::__construct();
    }
    
    function init() {
        add_filter( 'customize_register', array( $this, 'add_customizer' ), 60 ); 
        add_filter('gform_pre_send_email', array( $this, 'add_html_template' ), 10, 4 );
    }
    
    public function add_customizer() {
        global $wp_customize;
        $wp_customize->add_setting( VOYNOTIF_FIELD_PREFIXE . 'email_gf_table_bg', array( 
            'type' => 'option', // Attention ! 
            'default' => '#0073aa', 
            'transport' => 'refresh', ) 
        );
        $wp_customize->add_setting( VOYNOTIF_FIELD_PREFIXE . 'email_gf_table_color', array( 
            'type' => 'option', // Attention ! 
            'default' => '#fff', 
            'transport' => 'refresh', ) 
        );
        $wp_customize->add_control(
            new WP_Customize_Color_Control(
                $wp_customize,
                VOYNOTIF_FIELD_PREFIXE . 'email_gf_table_bg',
                array(
                    'label'       => __( 'GravityForms table background', 'notifications-center' ),
                    'description' => __( 'Choose the background of GravityForms tables', 'notifications-center' ),
                    'section'     => 'voynotif_body',
                    'settings'    => VOYNOTIF_FIELD_PREFIXE . 'email_gf_table_bg',
                )
            )
        ); 
        $wp_customize->add_control(
            new WP_Customize_Color_Control(
                $wp_customize,
                VOYNOTIF_FIELD_PREFIXE . 'email_gf_table_color',
                array(
                    'label'       => __( 'GravityForms table color', 'notifications-center' ),
                    'description' => __( 'Choose the color of GravityForms tables', 'notifications-center' ),
                    'section'     => 'voynotif_body',
                    'settings'    => VOYNOTIF_FIELD_PREFIXE . 'email_gf_table_color',
                )
            )
        );
    }
    
    public function add_html_template( $email, $message_format, $notification, $entry ) {        
        
        $template = new VOYNOTIF_email_template();
        
        $content = str_replace('<tr bgcolor="#FFFFFF">', '<tr bgcolor="'.$template->backgroundcontent_color.'">', $email['message'] );
        $content = str_replace('<tr bgcolor="#EAF2FA">', '<tr style="color:'.$template->gf_table_color.'" bgcolor="'.$template->gf_table_bg.'">', $content );
        
        $template->set_title($email['subject']);
        $template->set_content($content);
        $html = $template->get_html();
        
        $email['message'] = $html;
        return $email;
    }
    
}

new VOYNOTIF_compat_gravityforms();

