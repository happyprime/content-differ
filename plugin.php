<?php
/**
 * Plugin Name:     Content Differ
 * Plugin URI:      https://github.com/happyprime/content-differ/
 * Description:     Identify differences in content.
 * Author:          Happy Prime
 * Author URI:      https://happyprime.co
 * Text Domain:     content-differ
 * Domain Path:     /languages
 * Version:         0.0.1
 * Requires PHP:    7.4
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @package yawp
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( defined( 'WP_CLI' ) ) {
	require_once __DIR__ . '/includes/class-html.php';

	\WP_CLI::add_command( 'differ-html', 'ContentDiffer\HTML' );
}
