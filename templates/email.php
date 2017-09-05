<?php
/**
 * Email template
 * 
 * @author Floflo
 * @since 1.2.0
 * @update 2017-01-30
 */
voynotif_email_template_part( 'header' );
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <!-- COPY -->
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="center" style="font-size: 25px; font-family: Helvetica, Arial, sans-serif; color: <?php echo voynotif_email_title_color(); ?>;" class="padding-copy">
                        <?php echo voynotif_email_title(); ?>
                    </td>
                </tr>
                <tr>
                    <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding-copy">
                        <?php echo voynotif_email_content(); ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <?php 
    if( voynotif_email_button_url() ) { 
        voynotif_email_template_part( 'button' );
    }
    ?>
</table>
<?php
voynotif_email_template_part( 'footer' );
?>
