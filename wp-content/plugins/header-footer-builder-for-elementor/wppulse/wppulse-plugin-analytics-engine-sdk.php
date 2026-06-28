<?php
/**
 * SDK: WPPulse – Plugin Analytics Engine
 * Description: Lightweight plugin analytics SDK with optional deactivation feedback modal.
 * Author: Turbo Addons
 * @package WPPulse
 */

if ( ! defined( 'ABSPATH' ) ) exit;
// phpcs:disable WordPress.WP.I18n.NonSingularStringLiteralDomain, WordPress.WP.I18n.TextDomainMismatch
/**
 * WPPulse SDK – works like Appsero SDK but fully open-source.
 */
class WPPulse_SDK {

	private static $cfg = [];
	private static $file = '';

	/**
	 * Initialize SDK.
	 *
	 * @param string $file Main plugin file.
	 * @param array  $args Configuration (name, slug, version, endpoint).
	 */
	public static function init( $file, $args ) {
		self::$file = $file;
		self::$cfg  = wp_parse_args( $args, [
			'name'     => '',
			'slug'     => '',
			'version'  => '',
			'endpoint' => '',
		]);

		// Track main events.
		register_activation_hook( self::$file, [ __CLASS__, 'activated' ] );
		register_deactivation_hook( self::$file, [ __CLASS__, 'noop' ] ); // Modal handles real deactivation.
		add_action( 'upgrader_process_complete', [ __CLASS__, 'updated' ], 10, 2 );
		add_action( 'deleted_plugin', [ __CLASS__, 'uninstalled' ], 10, 2 );

		// Modal + AJAX for deactivation feedback.
		add_filter( 'plugin_action_links_' . plugin_basename( self::$file ), [ __CLASS__, 'filter_links' ] );
		add_action( 'admin_footer', [ __CLASS__, 'print_modal' ] );
		add_action( 'wp_ajax_wppulse_reason_submit', [ __CLASS__, 'ajax_reason_submit' ] );
		add_action( 'wp_ajax_wppulse_reason_skip', [ __CLASS__, 'ajax_reason_skip' ] );
	}

	/** ✅ Plugin Activated */
	public static function activated() {
		self::send( 'activated' );
	}

	/** Placeholder for deactivate hook (real handled by modal) */
	public static function noop() {}

	/** ✅ Plugin Updated */
	public static function updated( $upgrader, $options ) {
		if ( 'plugin' === $options['type'] && 'update' === $options['action'] ) {
			if ( in_array( plugin_basename( self::$file ), $options['plugins'] ?? [], true ) ) {
				self::send( 'updated' );
			}
		}
	}

	/** ✅ Plugin Uninstalled */
	public static function uninstalled( $plugin ) {
		if ( $plugin === plugin_basename( self::$file ) ) {
			self::send( 'uninstalled' );
		}
	}

	/** 🔹 Modify deactivate link to open modal instead */
	public static function filter_links( $links ) {
		if ( isset( $links['deactivate'] ) ) {
			$links['deactivate'] = str_replace( '<a ', '<a class="wppulse-deactivate-link" ', $links['deactivate'] );
		}
		return $links;
	}

	/**
	 * 🔹 Feedback Modal HTML + JS
	 */

	// style sdk files//
	public static function print_modal() {
		global $pagenow;
		if ( 'plugins.php' !== $pagenow ) return;
		$nonce = wp_create_nonce( 'wppulse_reason_nonce' );
		?>
		<style>
		#wppulse-modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:999999;font-family:"Inter",-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;}
		.wppulse-modal-wrap{background:#fff;width:992px;margin:6% auto;border-radius:8px;box-shadow:0 10px 30px rgba(0,0,0,0.15);overflow:hidden;}
		.wppulse-header{padding:24px 32px 10px;}
		.wppulse-header h2{margin:0;font-size:18px;color:#111827;font-weight:600;}
		.wppulse-body{padding:0 32px 22px;}
		.wppulse-reason-grid{display:flex;flex-wrap:wrap;gap:10px;margin-top:20px;}
		.wppulse-reason-item{flex:1 1 10%;border:1px solid #E5E7EB;border-radius:8px;padding:18px 12px 10px; text-align:center!important; background:#fff;cursor:pointer;transition:all .2s; display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:100px;}
		.wppulse-reason-item i{display:block;font-size:28px;color:#2563EB;margin-bottom:8px;}
		.wppulse-reason-item span{display:block;font-size:13px;color:#111827;font-weight:500;}
		.wppulse-reason-item.active{background:#2563EB;border-color:#2563EB;}
		.wppulse-reason-item.active i,.wppulse-reason-item.active span{color:#fff;}
		#wppulse-desc{width:100%;height:90px;border:1px solid #D1D5DB;border-radius:6px;padding:10px;margin-top:18px;font-size:14px;color:#111827; display: none;}
		.wppulse-footer{border-top:1px solid #E5E7EB;padding:16px 28px;display:flex;justify-content:space-between;align-items:center;}
		.wppulse-footer .button{border-radius:4px;padding:6px 14px;font-size:14px;}
		.wppulse-footer .button-primary{background:#2563EB;border-color:#2563EB;}
		</style>

		<div id="wppulse-modal">
			<div class="wppulse-modal-wrap">
				<div class="wppulse-header">

					<h2><?php esc_html_e( 'Before deactivation, could you tell us why?', self::$cfg['slug'] ); ?></h2>
				</div>
				<div class="wppulse-body">
					<div class="wppulse-reason-grid" id="wppulse-reason-grid">
						<?php
						$reasons = [
							'couldnt-understand'  => __( "Couldn't understand", self::$cfg['slug'] ),
							'found-better-plugin' => __( 'Found a better plugin', self::$cfg['slug'] ),
							'missing-feature'     => __( 'Missing a feature', self::$cfg['slug'] ),
							'not-working'         => __( 'Not working', self::$cfg['slug'] ),
							'not-what-looking'    => __( 'Not what I was looking for', self::$cfg['slug'] ),
							'didnt-work-expected' => __( "Didn't work as expected", self::$cfg['slug'] ),
							'others'              => __( 'Others', self::$cfg['slug'] ),
						];
						foreach ( $reasons as $slug => $label ) {

							// 🔹 Assign unique icon for each reason
							switch ( $slug ) {
								case 'couldnt-understand':
									$icon = 'editor-help'; // question mark icon
									break;
								case 'found-better-plugin':
									$icon = 'awards'; // trophy icon
									break;
								case 'missing-feature':
									$icon = 'admin-tools'; // wrench/tools icon
									break;
								case 'not-working':
									$icon = 'dismiss'; // cross/error icon
									break;
								case 'not-what-looking':
									$icon = 'search'; // magnifying glass icon
									break;
								case 'didnt-work-expected':
									$icon = 'warning'; // warning triangle icon
									break;
								case 'others':
								default:
									$icon = 'ellipsis'; // three dots for “other”
									break;
							}

							// 🔹 Output each item with its unique icon
							printf(
								'<div class="wppulse-reason-item" data-value="%1$s"><i class="dashicons dashicons-%3$s"></i><span>%2$s</span></div>',
								esc_attr( $slug ),
								esc_html( $label ),
								esc_attr( $icon )
							);
						}

						?>
					</div>
					<textarea id="wppulse-desc" placeholder="<?php esc_attr_e( 'Optional: tell us more...', self::$cfg['slug'] ); ?>"></textarea>
					<p style="margin-top: 18px!important;">We share your data with <a href="https://wp-turbo.com/">WP-TURBO</a> to troubleshoot problems & make product improvements. Learn more about how Appsero handles your data.</p>
				</div>
				<div class="wppulse-footer">
					<button type="button" id="wppulse-skip" class="button"><?php esc_html_e( 'Skip & Deactivate', self::$cfg['slug'] ); ?></button>
					<div>
						<button type="button" id="wppulse-cancel" class="button"><?php esc_html_e( 'Cancel', self::$cfg['slug'] ); ?></button>
						<button type="button" id="wppulse-submit" class="button button-primary"><?php esc_html_e( 'Submit & Deactivate', self::$cfg['slug'] ); ?></button>
					</div>
				</div>
			</div>
		</div>

		<!-- //script// -->

		<script>
		jQuery(function($){
			var modal = $('#wppulse-modal');
			var deactivateLink = '';
			var selectedReason = '';
			var descBox = $('#wppulse-desc');

			// Open modal
			$('#the-list').on('click', 'a.wppulse-deactivate-link', function(e){
				e.preventDefault();
				deactivateLink = $(this).attr('href');
				modal.fadeIn(200);
			});

			// Handle reason selection
			$('#wppulse-reason-grid').on('click', '.wppulse-reason-item', function(){
				$('.wppulse-reason-item').removeClass('active');
				$(this).addClass('active');
				selectedReason = $(this).data('value');

				// ✅ Show textarea only when any reason is selected
				if(selectedReason){
					descBox.slideDown(200);
				}
			});

			// Cancel button → close modal
			$('#wppulse-cancel').on('click', function(){
				modal.fadeOut(200);
			});

			// Skip button → send skip event
			$('#wppulse-skip').on('click', function(){
				$.post(ajaxurl, { action:'wppulse_reason_skip', nonce:'<?php echo esc_js( $nonce ); ?>' });
				window.location.href = deactivateLink;
			});

			// Submit feedback → send reason + desc
			$('#wppulse-submit').on('click', function(){
				$.post(ajaxurl, {
					action:'wppulse_reason_submit',
					nonce:'<?php echo esc_js( $nonce ); ?>',
					reason_id:selectedReason,
					reason_text:$('#wppulse-desc').val()
				});
				window.location.href = deactivateLink;
			});
		});
		</script>

		<?php
	}

	/** 🔹 AJAX: Skip button */
	public static function ajax_reason_skip() {
		check_ajax_referer( 'wppulse_reason_nonce', 'nonce' );
		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( __( 'Permission denied.', self::$cfg['slug'] ) );
		}
		self::send( 'deactivated' );
		wp_send_json_success();
	}

	/** 🔹 AJAX: Submit feedback */
	public static function ajax_reason_submit() {
		check_ajax_referer( 'wppulse_reason_nonce', 'nonce' );
		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( __( 'Permission denied.', self::$cfg['slug'] ) );
		}
		$reason = isset( $_POST['reason_id'] ) ? sanitize_text_field( wp_unslash( $_POST['reason_id'] ) ) : '';
		$text   = isset( $_POST['reason_text'] ) ? sanitize_textarea_field( wp_unslash( $_POST['reason_text'] ) ) : '';
		self::send( 'deactivated', [ 'reason_id' => $reason, 'reason_text' => $text ] );
		wp_send_json_success();
	}

	/**
	 * 🔹 Send telemetry data to WPPulse server
	 */
	private static function send( $status, $extra = [] ) {
		if ( empty( self::$cfg['endpoint'] ) ) return;

		$body = array_merge( [
			'domain'  => esc_url_raw( home_url() ),
			'email'   => sanitize_email( get_bloginfo( 'admin_email' ) ),
			'plugin'  => sanitize_text_field( self::$cfg['name'] ),
			'version' => sanitize_text_field( self::$cfg['version'] ),
			'status'  => sanitize_text_field( $status ),
		], $extra );

		wp_remote_post( self::$cfg['endpoint'], [
			'headers' => [ 'Content-Type' => 'application/json' ],
			'body'    => wp_json_encode( $body ),
			'timeout' => 10,
		] );
	}
}
// phpcs:enable WordPress.WP.I18n.NonSingularStringLiteralDomain, WordPress.WP.I18n.TextDomainMismatch