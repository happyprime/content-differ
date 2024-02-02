<?php
/**
 * Commands for manipulating content into HTML.
 *
 * @package content-differ
 */

namespace ContentDiffer;

use stdClass;
use WP_CLI;
use WP_Error;
use WP_Query;

/**
 * Commands for manipulating content into HTML.
 */
class HTML {

	/**
	 * Generate HTML for a specified post type's permalinks.
	 *
	 * @subcommand generate-html
	 *
	 * @param array $args The arguments passed to the command.
	 * @param array $assoc_args The associative arguments passed to the command.
	 */
	public function generate_html( array $args, $assoc_args ): void {
		$directory = isset( $args[0] ) ? $args[0] : false;

		$post_type = isset( $assoc_args['post_type'] ) ? $assoc_args['post_type'] : 'page';
		$post_id   = isset( $assoc_args['post_id'] ) ? $assoc_args['post_id'] : false;

		if ( ! $directory ) {
			WP_CLI::error( 'No directory specified.' );
			return;
		}

		if ( $post_id ) {
			$post = get_post( $post_id );

			if ( ! $post ) {
				WP_CLI::error( 'No post found with ID ' . $post_id );
				return;
			}

			$posts = (object) array(
				'posts'       => array(
					$post,
				),
				'found_posts' => 1,
			);
		} else {
			$posts = new WP_Query(
				array(
					'post_type'      => $post_type,
					'posts_per_page' => -1,
					'post_status'    => 'publish',
				)
			);
		}

		$progress = \WP_CLI\Utils\make_progress_bar( 'Generating HTML', $posts->found_posts );

		foreach ( $posts->posts as $post ) {

			$filename = $post_type . '-' . $post->ID . '-' . $post->post_name . '.html';
			$html     = $this->get_post_html( $post->ID );

			if ( '' === $html ) {
				WP_CLI::warning( 'No HTML generated for ' . $post->post_name );
				$progress->tick();
				continue;
			}

			file_put_contents( $directory . '/' . $filename, $html ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
			$progress->tick();
		}

		$progress->finish();
	}

	/**
	 * Retrieve the HTML for a post.
	 *
	 * @param int $post_id The ID of the post to retrieve the HTML for.
	 * @return string The HTML for the post.
	 */
	private function get_post_html( int $post_id ): string {
		$permalink = get_permalink( $post_id );
		$response  = wp_remote_get( $permalink );

		if ( $response instanceof WP_Error ) {
			WP_CLI::warning( 'Error retrieving ' . $permalink . ': ' . $response->get_error_message() );
			return '';
		}

		$html = wp_remote_retrieve_body( $response );

		return $html;
	}
}
