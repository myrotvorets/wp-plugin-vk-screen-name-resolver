<?php defined( 'ABSPATH' ) || die(); ?>
<label for="vk-screen-name"><?php _e( 'VK ID:', 'vksnr' ); ?></label><br/>
<input type="text" id="vk-screen-name" value="" autocomplete="off" class="text"/>
<input type="button" class="button button-secondary" value="<?php esc_attr_e( 'Check', 'vksnr' ); ?>" id="vk-screen-name-resolve"/>
<br/><br/>
<input id="vk-screen-name-result" type="text" readonly="readonly" class="text" placeholder="<?php esc_attr_e( 'Result', 'vksnr' ); ?>"/>
