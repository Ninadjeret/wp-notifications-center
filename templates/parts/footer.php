<?php
/**
 * Email Footer
 * 
 * @author Floflo
 * @since 1.2.0
 * @update 2017-01-30
 */
?>
         
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table> 

        <!-- FOOTER -->
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td bgcolor="<?php echo voynotif_email_background_color(); ?>" align="center">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                        <tr>
                            <td style="padding: 20px 0px 20px 0px;">
                                <!-- UNSUBSCRIBE COPY -->
                                <table width="500" border="0" cellspacing="0" cellpadding="0" align="center" class="responsive-table">
                                    <tr>
                                        <td align="center" valign="middle" style="font-size: 12px; line-height: 18px; font-family: Helvetica, Arial, sans-serif; color:#666666;">
                                            <span class="appleFooter" style="color:#666666;"><?php echo voynotif_email_footer_text(); ?></span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <?php echo voynotif_email_footer(); ?>
    </body>
</html> 