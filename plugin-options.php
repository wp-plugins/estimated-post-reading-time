<?php
add_action('admin_init', 'plugin_options_init');
add_action('admin_menu', 'plugin_options_add_page');

/**
 * Init plugin options to white list our options
 */
function plugin_options_init() {
	register_setting('sample_options', 'plugin_options', 'plugin_options_validate');
}

/**
 * Load up the menu page
 */
function plugin_options_add_page() {
	add_menu_page(__('Estimated Time', 'estimated-post-reading-time-locale'), __('Estimated Time', 'estimated-post-reading-time-locale'), 'administrator', __FILE__, 'plugin_options_do_page', plugins_url('/images/clock.png', __FILE__));
}

/**
 * Create the options page
 */
function plugin_options_do_page() {

	if (!isset($_REQUEST['settings-updated']))
		$_REQUEST['settings-updated'] = false;
	?>
	<div class="wrap">
	<?php echo "<h2>" . __('Estimated Post Reading Time Settings', 'estimated-post-reading-time-locale') . "</h2>"; ?>

		<?php if (false !== $_REQUEST['settings-updated']) : ?>
			<div class="updated fade"><p><strong><?php _e('Options saved', 'estimated-post-reading-time-locale'); ?></strong></p></div>
		<?php endif; ?>

		<form method="post" action="options.php">
	<?php settings_fields('sample_options'); ?>
			<?php $options = get_option('plugin_options'); ?>

			<table class="form-table">
				<tr valign="top"><th scope="row"><?php _e('Words per minute', 'estimated-post-reading-time-locale'); ?></th>
					<td>
						<input id="plugin_options[wpm]" class="regular-text" type="text" name="plugin_options[wpm]" value="<?php esc_attr_e($options['wpm']); ?>" />
						<label class="description" for="plugin_options[wpm]"><?php _e('Type words per minute', 'estimated-post-reading-time-locale'); ?></label>
					</td>
				</tr>
				<tr valign="top"><th scope="row"><?php _e('Prefix', 'estimated-post-reading-time-locale'); ?></th>
					<td>
						<input id="plugin_options[prefix]" class="regular-text" type="text" name="plugin_options[prefix]" value="<?php esc_attr_e($options['prefix']); ?>" />
						<label class="description" for="plugin_options[prefix]"><?php _e('Type what will be shown before time', 'estimated-post-reading-time-locale'); ?></label>
					</td>
				</tr>
				<tr valign="top"><th scope="row"><?php _e('Paragraph', 'estimated-post-reading-time-locale'); ?></th>
					<td>
						<input name="plugin_options[paragraph]" type="checkbox" value="1" <?php checked('1', $options['paragraph']); ?> />
						<label class="description" for="plugin_options[paragraph]"><?php _e('Check this if you want to enclose estimated time in paragraph tags', 'estimated-post-reading-time-locale'); ?></label>
					</td>
				</tr>
			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Options', 'estimated-post-reading-time-locale'); ?>" />
			</p>
		</form>
	</div>
	<?php
}

/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 */
function plugin_options_validate($input) {
	global $select_options, $radio_options;

	// Consumer Key
	$input['wpm'] = wp_filter_nohtml_kses($input['wpm']);
	$input['prefix'] = wp_filter_nohtml_kses($input['prefix']);
	$input['paragraph'] = ( $input['paragraph'] == 1 ? 1 : 0 );

	return $input;
}