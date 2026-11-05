
<?php
/**
 * Enqueue Meridian Builders frontend assets.
 *
 * @package Meridian_Builders
 */

/**
 * Load compiled Vite assets using the production manifest.
 */
function meridian_builders_enqueue_assets() {

	$dist_path     = get_template_directory() . '/assets/dist';
	$dist_uri      = get_template_directory_uri() . '/assets/dist';
	$manifest_path = $dist_path . '/.vite/manifest.json';

	// Stop if the Vite manifest is unavailable.
	if ( ! is_readable( $manifest_path ) ) {
		return;
	}

	$manifest = json_decode( file_get_contents( $manifest_path ), true );

	if ( ! is_array( $manifest ) ) {
		return;
	}

	// Retrieve the CSS entry.
	$css_entry = $manifest['src/css/main.css'] ?? null;

	if (
		is_array( $css_entry ) &&
		! empty( $css_entry['file'] ) &&
		is_string( $css_entry['file'] )
	) {
		$css_file = $dist_path . '/' . $css_entry['file'];

		if ( is_readable( $css_file ) ) {
			wp_enqueue_style(
				'meridian-builders-main',
				$dist_uri . '/' . $css_entry['file'],
				array(),
				(string) filemtime( $css_file )
			);
		}
	}

	// Retrieve the JavaScript entry.
	$js_entry = $manifest['src/js/main.js'] ?? null;

	if (
		is_array( $js_entry ) &&
		! empty( $js_entry['file'] ) &&
		is_string( $js_entry['file'] )
	) {
		$js_file = $dist_path . '/' . $js_entry['file'];

		if ( is_readable( $js_file ) ) {
			wp_enqueue_script(
				'meridian-builders-main',
				$dist_uri . '/' . $js_entry['file'],
				array(),
				(string) filemtime( $js_file ),
				true
			);
		}
	}

	// Preserve WordPress comment-reply functionality when required.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'meridian_builders_enqueue_assets' );

/**
 * Load the main JavaScript file as an ES module.
 *
 * @param string $tag    Script HTML tag.
 * @param string $handle Registered script handle.
 * @param string $src    Script source URL.
 * @return string
 */
function meridian_builders_script_module_tag( $tag, $handle, $src ) {

	if ( 'meridian-builders-main' !== $handle ) {
		return $tag;
	}

	return preg_replace(
		'/<script\b/',
		'<script type="module"',
		$tag,
		1
	);
}
add_filter( 'script_loader_tag', 'meridian_builders_script_module_tag', 10, 3 );
