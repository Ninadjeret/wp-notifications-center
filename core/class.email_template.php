<?php
/**
 * CLASS VOYNOTIF_email_template
 * 
 * @author Floflo
 * @since 0.9
 */
if( !class_exists( 'VOYNOTIF_email_template' ) ) {
    class VOYNOTIF_email_template {

        var $type,
            $logo_url,
            $logo_width,
            $logo_height,
            $button_color,
            $background_color,
            $backgroundcontent_color,
            $title_color,
            $footer;


        /**
         *  Constructor
         * 
         * @author Floflo
         * @since 0.9
         * 
         * @param string $title Titre du mail
         * @param string $content Contenu du mail
         * @param string $context Contexte dans lequel le template est sollicité
         **/
        function __construct() {

            //Type
            if( get_option( VOYNOTIF_FIELD_PREFIXE . 'template_path' == 'theme' )  ) {
                $this->type = 'theme';
            } else {
                $this->type = 'plugin';
            }

            //logo
            $this->logo_url = get_option( VOYNOTIF_FIELD_PREFIXE . 'email_logo' );

            //Couleurs
            $this->button_color = get_option( VOYNOTIF_FIELD_PREFIXE . 'email_button_color' );
            $this->background_color = get_option( VOYNOTIF_FIELD_PREFIXE . 'email_background_color' );
            $this->backgroundcontent_color = get_option( VOYNOTIF_FIELD_PREFIXE . 'email_backgroundcontent_color' );
            $this->title_color = get_option( VOYNOTIF_FIELD_PREFIXE . 'email_title_color' );

            //Footer
            $this->footer = get_option( VOYNOTIF_FIELD_PREFIXE . 'email_footer_message' );

        }

    }
}

?>