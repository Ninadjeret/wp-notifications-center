<?php
/**
 * 
 */
?>

<?php
global $voynotif_notification;
$post_id = $voynotif_notification->$context_info['post_id'];
if (has_post_thumbnail($post_id)) {
    $thumb_id = get_post_thumbnail_id($post_id);
    $thumb_url_array = wp_get_attachment_image_src($thumb_id, 'thumbnail', true);
    $thumb_url = $thumb_url_array[0];
} else {
    $thumb_url = '';
}
$post = get_post($post_id);
setup_postdata($post);
?>  

<tr>                                
<table>                                    
    <tr>                                        
        <td valign="top" style="padding: 40px 0 0 0;" class="mobile-hide">
            <a href="'.get_site_url().'/wp-admin/post.php?post='.$post_id.'&action=edit" target="_blank">
                <img src="'.$thumb_url.'" alt="Litmus" width="105" height="105" border="0" style="display: block; font-family: Arial; color: #666666; font-size: 14px; width: 105px; height: 105px;"></a></td>                                        
        <td style="padding: 40px 0 0 0;" class="no-padding">                                            
            <!-- ARTICLE -->                                            
            <table border="0" cellspacing="0" cellpadding="0" width="100%">                                                
                <tr>                                                    
                    <td align="left" style="padding: 0 0 5px 25px; font-size: 13px; font-family: Helvetica, Arial, sans-serif; font-weight: normal; color: #aaaaaa;" class="padding-meta">Le '.get_the_date('d/m/Y', $post_id).', par '.get_the_author().'</td>                                                
                </tr>                                                
                <tr>                                                    
                    <td align="left" style="padding: 0 0 5px 25px; font-size: 22px; font-family: Helvetica, Arial, sans-serif; font-weight: normal; color: #333333;" class="padding-copy">'.get_the_title($post_id).'</td>                                                
                </tr>                                                
                <tr>                                                     
                    <td align="left" style="padding: 10px 0 15px 25px; font-size: 16px; line-height: 24px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding-copy">'.get_excerpt_by_id($post_id, 100).'</td>                                                
                </tr>                                            
            </table>                                        
        </td>                                    
    </tr>                                                               
</table>                            
</tr>

<?php wp_reset_postdata(); ?>

