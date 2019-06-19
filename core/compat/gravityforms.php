<?php
/**
 * Compatibility with GravityForms
 */
class VOYNOTIF_compat_gravityforms extends VOYNOTIF_compat {
    
    /**
     * COnstructor
     */
    function __construct() {
        $this->dependencies = array(
            'Duplicate Post' => array(
                'file'      => 'gravityforms/gravityforms.php',
                'version'   => '2.2.5'
            ),
        );  
        parent::__construct();
    }
    
    /**
     * 
     */
    function init() {
        add_filter( 'customize_register', array( $this, 'add_customizer' ), 60 ); 
        add_filter( 'gform_pre_send_email', array( $this, 'add_html_template' ), 10, 4 );
        add_filter( 'voynotif/settings/fields', array($this, 'add_settings') );
    }
    
    public function add_settings( $fields ) {
        $fields['gf_active'] = array(
            'id' => 'gf_active',                
            'label' => __( 'Gravity Forms', 'notifications-center' ),
            'type' => 'boolean',
                'params'    => array(
                    'title' => __( 'Use Notifications Center template on GravityForms emails', 'notifications-center' ),
                ),
            'screen' => 'general',
            'fieldgroup' => 'template'
        );
        return $fields;
    }
    
    /**
     * Add customizer settings if GF is active
     * @global type $wp_customize
     */
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
    
    /**
     * 
     * @param array $email
     * @param type $message_format
     * @param type $notification
     * @param type $entry
     * @return type
     */
    public function add_html_template( $email, $message_format, $notification, $entry ) { 
        
        $auth = get_option( VOYNOTIF_FIELD_PREFIXE . 'gf_active' );
        if( empty( $auth ) ) return $email;
        
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

