<?php

/*
  Plugin Name: Estimated Post Reading Time
  Plugin URI: http://wordpress.org/extend/plugins/estimated-post-reading-time/
  Description: Calculates an average required time to complete reading a post.
  Version: 1.2
  Author: Konstantinos Kouratoras
  Author URI: http://www.kouratoras.gr
  Author Email: kouratoras@gmail.com
  License: GPL v2

  Copyright 2012 Konstantinos Kouratoras (kouratoras@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

class EstimatedPostReadingTime {

	/* -------------------------------------------------- */
	/* Constructor
	  /*-------------------------------------------------- */

	public function __construct() {

		load_plugin_textdomain('estimated-post-reading-time-locale', false, plugin_dir_path(__FILE__) . '/lang/');

		//Shortcode
		add_action('init', array(&$this, 'register_estimate_time_shortcode'));
		
		//Options Page
		add_action('admin_menu', array(&$this, 'plugin_add_options'));
	}

	function estimate_time() {
		
		$wpm = get_option('eprt_words_per_minute', 250);
		
		global $post;
		$content = strip_tags($post->post_content);		
		$content_words = str_word_count($content);
		$estimated_minutes = floor($content_words / $wpm);
		
		$result = '';

		if ($estimated_minutes < 1)
			$result .= " ".__('Less than a minute', 'estimated-post-reading-time-locale');
		else if ($estimated_minutes > 60) {
			if ($estimated_minutes > 1440)
				$result .= " ".__('More than a day', 'estimated-post-reading-time-locale');
			else
				$result .= floor($estimated_minutes / 60)." ". __('hours', 'estimated-post-reading-time-locale');
		}
		else if ($estimated_minutes == 1)
			$result .= $estimated_minutes." ". __('minute', 'estimated-post-reading-time-locale');
		else
			$result .= $estimated_minutes." ". __('minutes', 'estimated-post-reading-time-locale');

		return $result;
	}

	function estimate_time_shortcode() {
		return $this->estimate_time();
	}

	function register_estimate_time_shortcode() {
		add_shortcode('est_time', array(&$this, 'estimate_time_shortcode'));
	}
	
	public function plugin_add_options() {
		add_options_page('Post Reading Time', 'Post Reading Time', 'manage_options', 'eprtoptions', array(&$this, 'plugin_options_page'));
	}

	function plugin_options_page() {

		$opt_name = array(
		    'eprt_words_per_minute' => 'eprt_words_per_minute',
		);
		$hidden_field_name = 'eprt_submit_hidden';

		$opt_val = array(
		    'eprt_words_per_minute' => get_option($opt_name['eprt_words_per_minute']),
		);

		if (isset($_POST[$hidden_field_name]) && $_POST[$hidden_field_name] == 'Y') {
			$opt_val = array(
			    'eprt_words_per_minute' => stripslashes(esc_html(esc_attr(($_POST[$opt_name['eprt_words_per_minute']])))),
			);
			update_option($opt_name['eprt_words_per_minute'], $opt_val['eprt_words_per_minute']);
			?>
			<div id="message" class="updated fade">
				<p><strong>
						<?php _e('Options saved.', 'estimated-post-reading-time-locale'); ?>
					</strong></p>
			</div>
			<?php
		}
		?>

		<div class="wrap">
			<h2><?php _e('Estimated Post Reading Time Options', 'att_trans_domain'); ?></h2>
			<form name="att_img_options" method="post" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>">
				<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

				<p><label for=""><?php _e('Words Per Minute', 'estimated-post-reading-time-locale');?>:</label>
					<input type="text" name="<?php echo $opt_name['eprt_words_per_minute']; ?>" id="<?php echo $opt_name['eprt_words_per_minute']; ?>" value="<?php echo $opt_val['eprt_words_per_minute']; ?>"/>
				</p>

				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes', 'estimated-post-reading-time-locale'); ?>"></p>
			</form>

			<?php
		}

}

new EstimatedPostReadingTime();