<?php
$text_align = 'left';
global $voynotif_template;
?>
<table class="td" cellspacing="0" cellpadding="6" style="border-left:none; border-right: none; border-color: #666666; width: 100%; font-size: 16px; color: #666666; font-family: Helvetica, Roboto, Arial, sans-serif;" border="1">
    <thead>
        <tr>
            <th class="td" scope="col" style="color:#fff;" bgcolor="<?php echo $voynotif_template->button_color; ?>"><?php esc_html_e('Product', 'woocommerce'); ?></th>
            <th class="td" scope="col" style="color:#fff;" bgcolor="<?php echo $voynotif_template->button_color; ?>"><?php esc_html_e('Quantity', 'woocommerce'); ?></th>
            <th class="td" scope="col" style="color:#fff;" bgcolor="<?php echo $voynotif_template->button_color; ?>"><?php esc_html_e('Price', 'woocommerce'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        echo wc_get_email_order_items($order, array(// WPCS: XSS ok.
            'show_sku' => true,
            'show_image' => true,
            'image_size' => array(32, 32),
            'plain_text' => false,
            'sent_to_admin' => false,
        ));
        ?>
    </tbody>
    <tfoot>
        <?php
        $totals = $order->get_order_item_totals();

        if ($totals) {
            $i = 0;
            foreach ($totals as $total) {
                $i++;
                ?>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:<?php echo esc_attr($text_align); ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo wp_kses_post($total['label']); ?></th>
                    <td class="td" style="text-align:<?php echo esc_attr($text_align); ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo wp_kses_post($total['value']); ?></td>
                </tr>
                <?php
            }
        }
        if ($order->get_customer_note()) {
            ?>
            <tr>
                <th class="td" scope="row" colspan="2" style="text-align:<?php echo esc_attr($text_align); ?>;"><?php esc_html_e('Note:', 'woocommerce'); ?></th>
                <td class="td" style="text-align:<?php echo esc_attr($text_align); ?>;"><?php echo wp_kses_post(wptexturize($order->get_customer_note())); ?></td>
            </tr>
            <?php
        }
        ?>
    </tfoot>
</table>