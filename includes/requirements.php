<?php

class Requirements {

	/**
	 * All about requirements checks
	 *
	 * @return bool
	 */
	public function check() {
		// Only the decision is taken here -- this runs while the plugin loads, before
		// init, and a __() that early makes WP 6.7 report the text domain as loaded too
		// soon on every request. The wording happens inside the notice.
		if(!class_exists('WPeMatico') || !function_exists('pll_current_language')) {
			$this->display_error('missing');
			return false;
		}

		// version_compare, not a string compare: '2.6' > '2.10' is true, because PHP
		// compares those character by character.
		if(version_compare(WPEMATICO_VERSION, WPEMATICO_POLYLANG_REQ_WPEMATICO, '<')) {
			$this->display_error('outdated');
			return false;
		};
		return true;
	}

	// Display message and handle errors
	public function display_error($reason) {
		add_action('admin_notices', function () use ($reason) {
			$message = ('outdated' === $reason)
					? sprintf(
							/* translators: %s: minimum WPeMatico version. */
							__('WPeMatico should be on version %s or above.', 'wpematico_polylang'),
							WPEMATICO_POLYLANG_REQ_WPEMATICO
					)
					: __('WPeMatico and Polylang are required plugins.', 'wpematico_polylang');
			printf('<div class="notice error is-dismissible"><p>%s</p></div>', esc_html($message));
		});

		// Deactive self
		add_action('admin_init', function () {
			deactivate_plugins(WPEMATICO_POLYLANG_MAIN_FILE_DIR);
			unset($_GET['activate']);
		});
	}

}
