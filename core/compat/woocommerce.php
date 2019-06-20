<?php
/**
 * Compatibility woth Duplicate Post, to allow users to send Notifications when a post is duplicated
 */
class VOYNOTIF_compat_woocommerce extends VOYNOTIF_compat {
    
    function __construct() {
        $this->dependencies = array(
            'Woocommerce' => array(
                'file'      => 'woocommerce/woocommerce.php',
                'version'   => '3.3.0'
            ),
        );  
        parent::__construct();
    }
    
    function init() {       
        add_filter( 'voynotif/notifications/tags', array( $this, 'add_tag' ) ); 
        add_filter( 'voynotif/masks/tags', array( $this, 'add_maks_tags' ) );  
        add_filter( 'voynotif/notification/content', array( $this, 'perform_masks' ), 20, 2 );
        
        add_filter( 'voynotif/logs/context/type=wc_new_order', array( $this, 'log_context' ), 10, 2 );
        include_once( VOYNOTIF_DIR .  '/notifications/woocommerce/new-order.php');
    }
    
    public function log_context( $return, $context ) {
        if( isset( $context['order_id'] ) ) {
            $order = wc_get_order($context['order_id']);
            $order_html = '<a href="'.get_edit_post_link($context['order_id'], false).'">'.$order->get_order_number().'</a>';
            return sprintf( __('About order n°%s', 'notifications-center'), $order_html );            
        }
        return $return;
    }
    
    public function add_tag( $tags ) {
        $tags['woocommerce'] = __('Woocommerce', 'notifications-center');
        return $tags;
    }
    
    public function add_maks_tags($tags) {
        $tags['woocommerce_order'] = __('Order', 'notifications-center');
        $tags['woocommerce_order_customer'] = __('Order - customer', 'notifications-center');
        $tags['woocommerce_order_payment'] = __('Order - payment', 'notifications-center');
        $tags['woocommerce_order_shipping'] = __('Order - shipping', 'notifications-center');
        return $tags;
    }
    
    public function perform_masks( $content, $context ) {
        
        if( !isset( $context['order_id'] ) || empty( $context['order_id'] ) ) return $content;
        
        $order = wc_get_order( $context['order_id'] );
        $gateway = wc_get_payment_gateway_by_order($order);
        $gateway_instructions = ( isset( $gateway->instructions ) ) ? wpautop( wptexturize( $gateway->instructions ) ) : '' ;
        $date_paid = ( $order->get_date_paid() ) ? $order->get_date_paid()->format( get_option('date_format') ) : '' ;
        
        $content = str_replace( '{wc_order_num}', $order->get_order_number(), $content );
        $content = str_replace( '{wc_order_date}', $order->get_date_created()->format( get_option('date_format') ), $content );
        
        $content = str_replace( '{wc_billing_firstname}', $order->get_billing_first_name(), $content );
        $content = str_replace( '{wc_billing_address}', $order->get_formatted_billing_address(), $content );
        
        $content = str_replace( '{wc_shipping_method}', $order->get_shipping_method(), $content );
        $content = str_replace( '{wc_shipping_method2}', $order->get_shipping_to_display(), $content );  
   
        $content = str_replace( '{wc_payment_method}', $order->get_payment_method_title(), $content );
        $content = str_replace( '{wc_payment_instructions}', $gateway_instructions, $content );
        $content = str_replace( '{wc_payment_date}', $date_paid, $content );
        
        ob_start();
        include( voynotif_email_template_path() . 'parts/woocommerce/order-details.php' );
        $order_details = ob_get_clean();
        $content = str_replace( '{wc_order_details}', $order_details, $content );
        
        error_log('----- $order->get_total() -----');
        error_log( print_r( $order->get_total(), true ) );
        
        return $content;
    }
    
    public static function get_masks() {
        return array(
            
            //Billing
            'wc_order_num' => array(
                'title' => __('Order number', 'notificaitons-center'),
                'tag' => 'woocommerce_order'
            ),
            'wc_order_date' => array(
                'title' => __('Order date', 'notificaitons-center'),
                'tag' => 'woocommerce_order'
            ),
            'wc_order_num' => array(
                'title' => __('Order number', 'notificaitons-center'),
                'tag' => 'woocommerce_order'
            ),
            'wc_order_details' => array(
                'title' => __('Order details', 'notificaitons-center'),
                'tag' => 'woocommerce_order'
            ),
            
            //Billing
            'wc_billing_firstname' => array(
                'title' => __('Billing - Firstname', 'notificaitons-center'),
                'tag' => 'woocommerce_order_customer'
            ),
            'wc_billing_lastname' => array(
                'title' => __('Billing - Lastname', 'notificaitons-center'),
                'tag' => 'woocommerce_order_customer'
            ),
            'wc_billing_email' => array(
                'title' => __('Billing - Phone', 'notificaitons-center'),
                'tag' => 'woocommerce_order_customer'
            ),
            'wc_billing_phone' => array(
                'title' => __('Billing - Phone', 'notificaitons-center'),
                'tag' => 'woocommerce_order_customer'
            ),
            'wc_billing_company' => array(
                'title' => __('Billing - Company', 'notificaitons-center'),
                'tag' => 'woocommerce_order_customer'
            ),
            'wc_billing_address1' => array(
                'title' => __('Billing - Address 1', 'notificaitons-center'),
                'tag' => 'woocommerce_order_customer'
            ),
            'wc_billing_address2' => array(
                'title' => __('Billing - Address 2', 'notificaitons-center'),
                'tag' => 'woocommerce_order_customer'
            ),
            'wc_billing_state' => array(
                'title' => __('Billing - State', 'notificaitons-center'),
                'tag' => 'woocommerce_order_customer'
            ),
            'wc_billing_postcode' => array(
                'title' => __('Billing - Postcode', 'notificaitons-center'),
                'tag' => 'woocommerce_order_customer'
            ),
            'wc_billing_country' => array(
                'title' => __('Billing - Country', 'notificaitons-center'),
                'tag' => 'woocommerce_order_customer'
            ),
            'wc_billing_address' => array(
                'title' => __('Billing - Full address', 'notificaitons-center'),
                'tag' => 'woocommerce_order_customer'
            ),
            
            //Shipping
            'wc_shipping_firstname' => array(
                'title' => __('Shipping - Firstname', 'notificaitons-center'),
                'tag' => 'woocommerce_order_customer'
            ),
            'wc_shipping_lastname' => array(
                'title' => __('Shipping - Lastname', 'notificaitons-center'),
                'tag' => 'woocommerce_order_customer'
            ),
            'wc_shipping_email' => array(
                'title' => __('Shipping - Phone', 'notificaitons-center'),
                'tag' => 'woocommerce_order_customer'
            ),
            'wc_shipping_phone' => array(
                'title' => __('Shipping - Phone', 'notificaitons-center'),
                'tag' => 'woocommerce_order_customer'
            ),
            'wc_shipping_company' => array(
                'title' => __('Shipping - Company', 'notificaitons-center'),
                'tag' => 'woocommerce_order_customer'
            ),
            'wc_shipping_address1' => array(
                'title' => __('Shipping - Address 1', 'notificaitons-center'),
                'tag' => 'woocommerce_order_customer'
            ),
            'wc_shipping_address2' => array(
                'title' => __('Shipping - Address 2', 'notificaitons-center'),
                'tag' => 'woocommerce_order_customer'
            ),
            'wc_shipping_state' => array(
                'title' => __('Shipping - State', 'notificaitons-center'),
                'tag' => 'woocommerce_order_customer'
            ),
            'wc_shipping_postcode' => array(
                'title' => __('Shipping - Postcode', 'notificaitons-center'),
                'tag' => 'woocommerce_order_customer'
            ),
            'wc_shipping_country' => array(
                'title' => __('Shipping - Country', 'notificaitons-center'),
                'tag' => 'woocommerce_order_customer'
            ),
            'wc_shipping_address' => array(
                'title' => __('Shipping - Full address', 'notificaitons-center'),
                'tag' => 'woocommerce_order_customer'
            ),
            
            //Shipping
            'wc_shipping_method' => array(
                'title' => __('Shipping method', 'notificaitons-center'),
                'tag' => 'woocommerce_order_shipping'
            ),
            'wc_shipping_methods' => array(
                'title' => __('Shipping methods', 'notificaitons-center'),
                'tag' => 'woocommerce_order_shipping'
            ),
            'wc_shipping_method2' => array(
                'title' => __('Shipping method display', 'notificaitons-center'),
                'tag' => 'woocommerce_order_shipping'
            ),
            
            //Paiment
            'wc_payment_method' => array(
                'title' => __('Payment method', 'notificaitons-center'),
                'tag' => 'woocommerce_order_payment'
            ),
            'wc_payment_instructions' => array(
                'title' => __('Payment instructions', 'notificaitons-center'),
                'tag' => 'woocommerce_order_payment'
            ),
            'wc_payment_date' => array(
                'title' => __('Payment date', 'notificaitons-center'),
                'tag' => 'woocommerce_order_payment'
            ),
        );
    }
    
}

new VOYNOTIF_compat_woocommerce();

