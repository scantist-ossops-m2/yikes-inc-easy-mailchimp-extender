<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.yikesinc.com/
 * @since      1.0.0
 *
 * @package    Yikes_Inc_Easy_Mailchimp_Extender
 * @subpackage Yikes_Inc_Easy_Mailchimp_Extender/includes
 */
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Yikes_Inc_Easy_Mailchimp_Extender
 * @subpackage Yikes_Inc_Easy_Mailchimp_Extender/includes
 * @author     YIKES Inc. <info@yikesinc.com>
 */
class Yikes_Inc_Easy_Mailchimp_Error_Logging {
	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->yikes_inc_easy_mailchimp_extender = 'yikes-inc-easy-mailchimp-extender';			
	}
	
	
	// this will be used to write errors to our log
	// do_action( 'yikes_easy_mailchimp_write_to_error_log' , $error );
	/*
	*	Parameters:
	*	@returned_error 
	*	@error_type - what was running when the error occured ie (new user subscription, remove user etc)
	*/
	public function yikes_easy_mailchimp_write_to_error_log( $returned_error , $error_type , $page='' ) {
		
		// if we pass in a custom page, don't set things up
		if( empty( $page ) ) {
			// get the current page, admin or front end?
			if( is_admin() ) {
				$page = 'Admin';
			} else {
				$page = 'Front End';
			}
		}
		
		ob_start();
		?>
			<tr>
				<td class="row-title">
					<label for="tablecell">
						<em><?php echo ucwords( stripslashes( $returned_error ) ); ?></em>
					</label>
				</td>
				<td>
					<?php _e( 'Page:' , $this->yikes_inc_easy_mailchimp_extender ); echo ' ' . $page; ?> || <?php _e( 'Time:' , $this->yikes_inc_easy_mailchimp_extender ); echo ' ' . date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) , current_time('timestamp') ); ?> || <?php _e( 'Type:' , $this->yikes_inc_easy_mailchimp_extender ); echo ' ' . $error_type; ?>
				</td>
			</tr>
		<?php
		$new_contents = ob_get_clean();
		
		// file put contents $returned error + other data
		if( file_exists( YIKES_MC_PATH . 'includes/error_log/yikes-easy-mailchimp-error-log.php' ) ) {
			echo file_put_contents( 
				YIKES_MC_PATH . 'includes/error_log/yikes-easy-mailchimp-error-log.php',
				$new_contents,
				FILE_APPEND
			);
		}
	}
	
	/*
	*  ytks_mc_generate_error_log_table()
	*  generate our erorr log table on the options settings page
	*
	*  @since 5.6
	*/	
	public function yikes_easy_mailchimp_generate_error_log_table() {		
		// ensure file_get_contents exists
		if( function_exists( 'file_get_contents' ) ) {	
			// confirm that our file exists
			if( file_exists( YIKES_MC_PATH . 'includes/error_log/yikes-easy-mailchimp-error-log.php' ) ) {
				$error_log_contents = file_get_contents( YIKES_MC_PATH . 'includes/error_log/yikes-easy-mailchimp-error-log.php' , true );							
				if( $error_log_contents === FALSE ) {
					return _e( 'File get contents not available' , $this->yikes_inc_easy_mailchimp_extender );
				}
				if ( $error_log_contents != '' ) {
					// return $error_log_contents;
					print_r( $error_log_contents );
				} else {
					?>
						<!-- table body -->
						<tr>
							<td style="display:table;margin-bottom:1em;margin-top:.5em;" class="row-title colspanchange" colspan="2">
								<strong><span class='dashicons dashicons-no-alt'></span> <?php _e( 'No errors logged.', $this->yikes_inc_easy_mailchimp_extender ); ?></strong>
								<?php if( get_option( 'yikes-mailchimp-debug-status' , '' ) == '' ) { ?>
									<br />
									<p style="margin:10px 0;"><em><?php _e( "To start logging errors toggle on the 'Enable Debugging' option above.", $this->yikes_inc_easy_mailchimp_extender ); ?></em></p>
								<?php } ?>
							</td>
						</tr>
					<?php
				}
			}
		} else { // if file_get_contents is disabled server side
			?>
				<!-- table body -->
				<tr>
					<td class="row-title colspanchange" colspan="2">
						<strong><?php esc_attr_e( 'It looks like the function file_get_contents() is disabled on your server. We cannot retrieve the contents of the error log.', $this->yikes_inc_easy_mailchimp_extender ); ?></strong>
					</td>
				</tr>
			<?php
		}
	}
		
}