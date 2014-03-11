<?php

/*
  Plugin Name: Estimated Post Reading Time
  Plugin URI: http://wordpress.org/extend/plugins/estimated-post-reading-time/
  Description: Calculates an average required time to complete reading a post.
  Version: 1.1
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
	/* Private variables
	  /*-------------------------------------------------- */

	private $wpm;

	/* -------------------------------------------------- */
	/* Constructor
	  /*-------------------------------------------------- */

	public function __construct() {

		$options = get_option('plugin_options');
		$this->wpm = isset($options['wpm']) ? $options['wpm'] : 250;

		load_plugin_textdomain('estimated-post-reading-time-locale', false, plugin_dir_path(__FILE__) . '/lang/');

		//Admin options
		require_once( plugin_dir_path(__FILE__) . 'plugin-options.php' );

		//Shortcode
		add_action('init', array(&$this, 'register_estimate_time_shortcode'));
	}

	function estimate_time() {
		
		$options = get_option('plugin_options');

		$content = strip_tags(get_the_content());
		$content_words = str_word_count($content);	
		$estimated_minutes = floor($content_words / $this->wpm);
		
		$result = '';

		if ($estimated_minutes < 1)
			$result .= " ".__('Less than a minute', 'estimated-post-reading-time-locale');
		else if ($estimated_minutes > 60) {
			if ($estimated_minutes > 1440)
				$result .= " ".__('More than a day', 'estimated-post-reading-time-locale');
			else
				$result .= floor($estimated_minutes / 60)." ". __('hours', 'estimated-post-reading-time-locale');
		}
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

}

new EstimatedPostReadingTime();