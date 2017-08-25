<?php
/**
 * Email button part
 * 
 * @author Floflo
 * @since 1.2.0
 * @update 2017-01-30
 */
?>
<tr>                                
    <td>                                    
        <!-- BULLETPROOF BUTTON -->                                    
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="mobile-button-container">                                        
            <tr>                                            
                <td align="center" style="padding: 25px 0 0 0;" class="padding-copy">                                                
                    <table border="0" cellspacing="0" cellpadding="0" class="responsive-table">                                                    
                        <tr>                                                        
                            <td align="center">                                                          
                                <a href="<?php echo voynotif_email_button_url(); ?>" target="_blank" style="font-size: 16px; font-family: Helvetica, Arial, sans-serif; font-weight: normal; color: #ffffff; text-decoration: none; background-color: <?php echo voynotif_email_button_color(); ?>; border-top: 15px solid <?php echo voynotif_email_button_color(); ?>; border-bottom: 15px solid <?php echo voynotif_email_button_color(); ?>; border-left: 25px solid <?php echo voynotif_email_button_color(); ?>; border-right: 25px solid <?php echo voynotif_email_button_color(); ?>; border-radius: 3px; -webkit-border-radius: 3px; -moz-border-radius: 3px; display: inline-block;" class="mobile-button">
                                    <?php echo voynotif_email_button_text(); ?>                                                          
                                </a>                                                          
                                <br>
                                <br>
                                <br>                                                        
                            </td>                                                    
                        </tr>                                                
                    </table>                                            
                </td>                                        
            </tr>                                    
        </table>                                
    </td>                            
</tr>  

