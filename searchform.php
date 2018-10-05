<?php
/**
 * Search form template
 *
 * @package Fixed
 * @since 1.0
 */
?>
<form action="<?php echo home_url( '/' ); ?>" class="search-form clearfix">
	<fieldset>
		<input type="text" class="search-form-input text" name="s" onfocus="if (this.value == '<?php _e('Search...','fixed'); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('Search...','fixed'); ?>';}" value="<?php _e('Search...','fixed'); ?>"/>
		<input type="submit" value="Search" class="submit search-button" />
	</fieldset>
</form>