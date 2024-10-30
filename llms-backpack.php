<?php
/**
* Plugin Name: LifterLMS BackPack Lite
* Plugin URI: https://learning-templates.com/product/lifterlms-back-pack/
* Description: Adds many new features to LifterLMS. Only six features function in the Settings page with this free version. Upgrade to the full version <b><a href="https://learning-templates.com/product/lifterlms-backpack/" target="blank">HERE</a></b>.
* Version: 1.0.1
* Author: Dennis Hall
* Author URI: https://learning-templates.com
* Requires at least: 3.8
* Tested up to: 4.9.7
*/

// Global Variable $llms_bkpk  for All Class instance 
global $llms_bkpk;
// config
include_once( dirname( __FILE__ ) . '/inc/config.php' );
// load class
include_once( dirname( __FILE__ ) . '/inc/boot.php' );

//include lib user forms field manager
include_once( dirname( __FILE__ ) . '/inc/modules/llms-custom-reg-fields/bkpk-fields-manager/bkpk-fields-manager.php');  

//define plugin root directory 
define( 'LLMS_BKPK_DIR', plugin_dir_path( __FILE__ ) );

//include some scripts and styles 
define( 'LLMS_BKPK_URL', plugins_url( '/', __FILE__  ) );

$boot      = '\llms_bkpk\Boot';
$llms_bkpk_class = new $boot;

defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

add_filter( 'plugin_action_links', 'lifterlms_bkpk_add_action_plugin', 10, 5 );
function lifterlms_bkpk_add_action_plugin( $actions, $plugin_file ) {
	static $plugin;
	if (!isset($plugin))
		$plugin = plugin_basename(__FILE__);
	if ($plugin == $plugin_file) {
			$site_link = array('buynow' => '<a href="https://learning-templates.com/product/lifterlms-backpack/" target="_blank"><b style="font-size:1.2em;">SUBSCRIBE NOW!</b></a>|');
			$settings = array('settings' => '<a href="admin.php?page=bkpk_settings">' . __('<b style="color:darkgreen;font-size:1.2em;">START HERE</b>', 'lifterlms') . '</a>|');
				$actions = array_merge($site_link, $actions);
    			$actions = array_merge($settings, $actions);
		}
	return $actions;
}

$bkpkpath = plugin_basename( __FILE__ );

add_action("after_plugin_row_{$bkpkpath}", function( $plugin_file, $plugin_data, $status ) {
	echo '<tr class="active"><td></td><td colspan="1" align="center"><img src="https://learning-templates.com/wp-content/uploads/2015/09/dennis.png" height="110" alt="Dennis Hall / Learning Templates"/></td><td colspan="8"><strong style="color:green; font-size:20px;">Looking for custom WordPress or LifterLMS development?</strong><br/><b>Areas of Expertise:</b> LMS Consulting, Web Development, Web Design, Instructional Design (Course Content Creation)<br/><b>Specialties:</b> Full Site Builds, Advanced Turnkey Solutions, Hosting, Custom Development, Custom Theme Development, Cross Platform Integration, SSO<br/>I develop custom solutions that make your LifterLMS site look and work the way you want it to. I can integrate your LifterLMS site with other websites, services and marketing systems.<br/>You can reach out to me for your project needs via my <a href="https://learning-templates.com/contact-us/" target="_blank"><b>Non Product Support Form</b></a></td></tr>';
}, 10, 3 );

if ( ! defined( 'LLMS_BKPK_PLUGIN_FILE' ) ) {
	define( 'LLMS_BKPK_PLUGIN_FILE', __FILE__ );
}
$llms_bkpk_license_key = add_option('llms_bkpk_license_key', 'free');

	// enqueue the scripts
	function llms_bkpk_admin_init_method($hook) {
		if($hook =='post.php')
			return;
		
		// include admin styles
		wp_register_style( 'llms-bkpk-form-styles' , plugin_dir_url( __FILE__ ) . '/css/admin-styles.css' );
		wp_enqueue_style( 'llms-bkpk-form-styles' );
		wp_register_style( 'llms-bkpk-admin-styles' , plugin_dir_url( __FILE__ ) . '../lifterlms/assets/css/admin.min.css' );
		wp_enqueue_style( 'llms-bkpk-admin-styles' );
		$ver = 1;
		wp_register_script( 'llms-bkpk-script' , plugin_dir_url( __FILE__ ) . 'js/script.js', array(), $ver );
		wp_enqueue_script( 'llms-bkpk-script' );
		wp_register_script( 'llms-bkpkunzip' , plugin_dir_url( __FILE__ ) . 'js/jszip.js', array(), $ver );

		wp_enqueue_script( 'customizer-range-value-control', plugin_dir_url( __FILE__ ) . 'inc/interfaces/customizer/js/customizer-range-value-control.js', array( 'jquery' ), rand(), true );
		wp_register_style( 'customizer-range-value-control', plugin_dir_url( __FILE__ ) . 'inc/interfaces/customizer/css/customizer-range-value-control.css' );
		wp_enqueue_style( 'customizer-range-value-control' );

		//Admin load Google fonts
		$charset = get_option( 'charset_setting', 'latin' );
		$fonts = array(
			"Abel:400:{$charset}",
			"Amatic+SC:400:{$charset}",
			"Arimo:400:{$charset}",
			"Arvo:400:{$charset}",
			"Bevan:400:{$charset}",
			"Bitter:400:{$charset}",
			"Black+Ops+One:400:{$charset}",
			"Boogaloo:%4003A{$charset}",
			"Bree+Serif:400:{$charset}",
			"Calligraffitti:400:{$charset}",
			"Cantata+One:400:{$charset}",
			"Cardo:400:{$charset}",
			"Changa+One:400:{$charset}",
			"Cherry+Cream+Soda:400:{$charset}",
			"Chewy:400:{$charset}",
			"Comfortaa:400:{$charset}",
			"Coming+Soon:%4003A{$charset}",
			"Covered+By+Your+Grace:400:{$charset}",
			"Crafty+Girls:400:{$charset}",
			"Crete+Round:400:{$charset}",
			"Crimson+Text:400:{$charset}",
			"Cuprum:400:{$charset}",
			"Dancing+Script:400:{$charset}",
			"Dosis:400:{$charset}",
			"Droid+Sans:400:{$charset}",
			"Droid+Serif:400:{$charset}",
			"Francois+One:400:{$charset}",
			"Fredoka+One:400:{$charset}",
			"The+Girl+Next+Door:400:{$charset}",
			"Gloria+Hallelujah:400:{$charset}",
			"Happy+Monkey:400:{$charset}",
			"Indie+Flower:400:{$charset}",
			"Josefin+Slab:400:{$charset}",
			"Judson:400:{$charset}",
			"Kreon:400:{$charset}",
			"Lato:400:{$charset}",
			"Lato+Light:400:{$charset}",
			"Leckerli+One:400:{$charset}",
			"Lobster:400:{$charset}",
			"Lobster+Two:400:{$charset}",
			"Lora:400:{$charset}",
			"Luckiest+Guy:400:{$charset}",
			"Merriweather:400:{$charset}",
			"Metamorphous:400:{$charset}",
			"Montserrat:400:{$charset}",
			"Noticia+Text:400:{$charset}",
			"Nova+Square:400:{$charset}",
			"Nunito:400:{$charset}",
			"Old+Standard+TT:400:{$charset}",
			"Open+Sans:400:{$charset}",
			"Open+Sans+Condensed:300:{$charset}",
			"Open+Sans+Light:400:{$charset}",
			"Oswald:400:{$charset}",
			"Pacifico:400:{$charset}",
			"Passion+One:400:{$charset}",
			"Patrick+Hand:400:{$charset}",
			"Permanent+Marker:400:{$charset}",
			"Play:400:{$charset}",
			"Playfair+Display:400:{$charset}",
			"Poiret+One:400:{$charset}",
			"PT+Sans:400:{$charset}",
			"PT+Sans+Narrow:400:{$charset}",
			"PT+Serif:400:{$charset}",
			"Raleway:400:{$charset}",
			"Raleway+Dots:400:{$charset}",
			"Reenie+Beanie:400:{$charset}",
			"Righteous:400:{$charset}",
			"Roboto:400:{$charset}",
			"Roboto+Condensed:400:{$charset}",
			"Roboto+Light:400:{$charset}",
			"Rock+Salt:400:{$charset}",
			"Rokkitt:400:{$charset}",
			"Sanchez:400:{$charset}",
			"Satisfy:400:{$charset}",
			"Schoolbell:400:{$charset}",
			"Shadows+Into+Light:400:{$charset}",
			"Shadows+Into+Light+Two:400:{$charset}",
			"Source+Sans+Pro:400:{$charset}",
			"Special+Elite:400:{$charset}",
			"Squada+One:400:{$charset}",
			"Tangerine:400:{$charset}",
			"Ubuntu:400:{$charset}",
			"Unkempt:400:{$charset}",
			"Vollkorn:400:{$charset}",
			"Walter+Turncoat:400:{$charset}",
			"Yanone+Kaffeesatz:400:{$charset}",
		);
		$fonts     = implode( '%7C', $fonts);
		$protocol  = is_ssl() ? 'https' : 'http';
		$fonts_url = esc_url( "{$protocol}://fonts.googleapis.com/css?family={$fonts}" );
		wp_enqueue_style( 'atd-google-fonts', $fonts_url, array(), null );
}
add_action( 'admin_enqueue_scripts' , 'llms_bkpk_admin_init_method' );

//Site load Google fonts
add_action( 'wp_enqueue_scripts' , 'bkpk_front_end_google_fonts' );
function bkpk_front_end_google_fonts(){
		$charset = get_option( 'charset_setting', 'latin' );
		$fonts = array(
			"Abel:400:{$charset}",
			"Amatic+SC:400:{$charset}",
			"Arimo:400:{$charset}",
			"Arvo:400:{$charset}",
			"Bevan:400:{$charset}",
			"Bitter:400:{$charset}",
			"Black+Ops+One:400:{$charset}",
			"Boogaloo:%4003A{$charset}",
			"Bree+Serif:400:{$charset}",
			"Calligraffitti:400:{$charset}",
			"Cantata+One:400:{$charset}",
			"Cardo:400:{$charset}",
			"Changa+One:400:{$charset}",
			"Cherry+Cream+Soda:400:{$charset}",
			"Chewy:400:{$charset}",
			"Comfortaa:400:{$charset}",
			"Coming+Soon:%4003A{$charset}",
			"Covered+By+Your+Grace:400:{$charset}",
			"Crafty+Girls:400:{$charset}",
			"Crete+Round:400:{$charset}",
			"Crimson+Text:400:{$charset}",
			"Cuprum:400:{$charset}",
			"Dancing+Script:400:{$charset}",
			"Dosis:400:{$charset}",
			"Droid+Sans:400:{$charset}",
			"Droid+Serif:400:{$charset}",
			"Francois+One:400:{$charset}",
			"Fredoka+One:400:{$charset}",
			"The+Girl+Next+Door:400:{$charset}",
			"Gloria+Hallelujah:400:{$charset}",
			"Happy+Monkey:400:{$charset}",
			"Indie+Flower:400:{$charset}",
			"Josefin+Slab:400:{$charset}",
			"Judson:400:{$charset}",
			"Kreon:400:{$charset}",
			"Lato:400:{$charset}",
			"Lato+Light:400:{$charset}",
			"Leckerli+One:400:{$charset}",
			"Lobster:400:{$charset}",
			"Lobster+Two:400:{$charset}",
			"Lora:400:{$charset}",
			"Luckiest+Guy:400:{$charset}",
			"Merriweather:400:{$charset}",
			"Metamorphous:400:{$charset}",
			"Montserrat:400:{$charset}",
			"Noticia+Text:400:{$charset}",
			"Nova+Square:400:{$charset}",
			"Nunito:400:{$charset}",
			"Old+Standard+TT:400:{$charset}",
			"Open+Sans:400:{$charset}",
			"Open+Sans+Condensed:300:{$charset}",
			"Open+Sans+Light:400:{$charset}",
			"Oswald:400:{$charset}",
			"Pacifico:400:{$charset}",
			"Passion+One:400:{$charset}",
			"Patrick+Hand:400:{$charset}",
			"Permanent+Marker:400:{$charset}",
			"Play:400:{$charset}",
			"Playfair+Display:400:{$charset}",
			"Poiret+One:400:{$charset}",
			"PT+Sans:400:{$charset}",
			"PT+Sans+Narrow:400:{$charset}",
			"PT+Serif:400:{$charset}",
			"Raleway:400:{$charset}",
			"Raleway+Dots:400:{$charset}",
			"Reenie+Beanie:400:{$charset}",
			"Righteous:400:{$charset}",
			"Roboto:400:{$charset}",
			"Roboto+Condensed:400:{$charset}",
			"Roboto+Light:400:{$charset}",
			"Rock+Salt:400:{$charset}",
			"Rokkitt:400:{$charset}",
			"Sanchez:400:{$charset}",
			"Satisfy:400:{$charset}",
			"Schoolbell:400:{$charset}",
			"Shadows+Into+Light:400:{$charset}",
			"Shadows+Into+Light+Two:400:{$charset}",
			"Source+Sans+Pro:400:{$charset}",
			"Special+Elite:400:{$charset}",
			"Squada+One:400:{$charset}",
			"Tangerine:400:{$charset}",
			"Ubuntu:400:{$charset}",
			"Unkempt:400:{$charset}",
			"Vollkorn:400:{$charset}",
			"Walter+Turncoat:400:{$charset}",
			"Yanone+Kaffeesatz:400:{$charset}",
		);
		$fonts     = implode( '%7C', $fonts);
		$protocol  = is_ssl() ? 'https' : 'http';
		$fonts_url = esc_url( "{$protocol}://fonts.googleapis.com/css?family={$fonts}" );
		wp_enqueue_style( 'atd-google-fonts', $fonts_url, array(), null );
}

add_action('admin_menu', 'llms_bkpk_license_menu',11);
function llms_bkpk_license_menu(){
	$hook = add_submenu_page('lifterlms',
		'LifterLMS BackPack',
		'LifterLMS BackPack',
		'manage_options',
		'bkpk_settings',
		'llms_bkpk');
	}
	function llms_bkpk() {
		?>
		<?php
		echo '<div class="wrap">';
		/* License activate button was clicked */
		if (isset($_REQUEST['activate_license'])) {
			$license_key = $_REQUEST['llms_bkpk_license_key'];

			// API query parameters
			$api_params = array(
				'slm_action' => 'slm_activate',
				'secret_key' => LLMS_BKPK_SECRET_KEY,
				'license_key' => $license_key,
				'registered_domain' => $_SERVER['SERVER_NAME'],
				'item_reference' => urlencode(LLMS_BKPK_ITEM_REFERENCE),
			);

			// Send query to the license manager server
			$query = esc_url_raw(add_query_arg($api_params, LLMS_BKPK_LICENSE_SERVER_URL));
			$response = wp_remote_get($query, array('timeout' => 20, 'sslverify' => false));
			// Check for error in the response
			if (is_wp_error($response)){
				echo '<p class="license-error">Unexpected Error! The query returned with an error.</p>';
			}

			// License data.
			$license_data = json_decode(wp_remote_retrieve_body($response));
			if($license_data->result == 'success'){//Success was returned for the license activation
				echo '<p class="license-ok">'.$license_data->message.'</p>';

				//Save the license key in the options table
				update_option('llms_bkpk_license_key', $license_key); 

				//refresh page 
				?>
				<meta http-equiv="refresh" content="1">
					<?php 
			}else{
				echo '<p class="license-error">'.$license_data->message.'</p>';
			}
		} // End of license activation
    
		/*** License activate button was clicked ***/
		if (isset($_REQUEST['deactivate_license'])) {
			$license_key = $_REQUEST['llms_bkpk_license_key'];

			// API query parameters
			$api_params = array(
				'slm_action' => 'slm_deactivate',
				'secret_key' => LLMS_BKPK_SECRET_KEY,
				'license_key' => $license_key,
				'registered_domain' => $_SERVER['SERVER_NAME'],
				'item_reference' => urlencode(LLMS_BKPK_ITEM_REFERENCE),
			);

			// Send query to the license manager server
			$query = esc_url_raw(add_query_arg($api_params, LLMS_BKPK_LICENSE_SERVER_URL));
			$response = wp_remote_get($query, array('timeout' => 20, 'sslverify' => false));
			// Check for error in the response
			if (is_wp_error($response)){
				echo '<p class="license-error">Unexpected Error! The query returned with an error.</p>';
			}

			// License data.
			$license_data = json_decode(wp_remote_retrieve_body($response));
			if($license_data->result == 'success'){//Success was returned for the license activation
				echo '<p class="license-ok">'.$license_data->message.'</p>';

				//Remove the licensse key from the options table. It will need to be activated again.
				update_option('llms_bkpk_license_key', '');

				//refresh page 
				?>
				<meta http-equiv="refresh" content="1">
				<?php 
			}else{

				//Show error to the user. Probably entered incorrect license key.
				echo '<p class="license-error">'.$license_data->message.'</p>';
			}
		} // End of sample license deactivation
		$llms_bkpk_license_key = get_option('llms_bkpk_license_key');
		
		//include 'css/admin-styles.css';
		?>
		<div class="key-page">
        	<div class="lt-logo" style="width:20%; float:left; padding-bottom:10px;"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/LT_Logo.png" width="80%"/></div>
			<div style="width:80%; float:left; text-align:left; vertical-align:middle;">
				Thanks for using LifterLMS Back-Pack!<br/>
				This plugin adds a number of features to your LifterLMS site. <b>We regularly add new features to the subscription version of this plugin! Subscribe now to access the full features and get the new features as we release them!</b> To learn more about these features, please visit the product page at <a href="https://learning-templates.com/product/lifterlms-backpack/" target="_blank">Learning Templates</a>.
			</div>
			<div class="clear"></div>
			<hr/>
			<div class="lt-logo" style="width:20%; text-align:center; float:left;"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/dennis.png" width="45%"/></div>
			<div style="width:80%; float:left; text-align:left; vertical-align:middle;"><strong style="color:green; font-size:20px;">
				Looking for custom WordPress or LifterLMS development?</strong><br/>
				<b>Areas of Expertise:</b> LMS Consulting, Web Development, Web Design, Instructional Design (Course Content Creation)<br/>
				<b>Specialties:</b> Full Site Builds, Advanced Turnkey Solutions, Hosting, Custom Development, Custom Theme Development, Cross Platform Integration, SSO<br/>
				I develop custom solutions that make your LMS site look and work the way you want it to. I can integrate your LifterLMS site with other websites, services and marketing systems.<br/>
				You can reach out to me for your project needs via my <a href="https://learning-templates.com/contact-us/" target="_blank"><b>Non-Product-Support Contact Form</b></a>
			</div>
			<div class="clear"></div>
			<hr/>
			<div style="width:100%; text-align:center;">
                <b style="color:red; font-size:1.4em;">SUPPORT FOR THIS PLUGIN IS AVAILABLE IN THE <a href="https://wordpress.org/support/plugin/llms-backpack-lite" target="_blank">WORDPRESS SUPPORT FORUM</a></b>
			</div>
			<div class="tab blue">
			</div>
			<?php
			// Enable all content when license is activated
			$llms_bkpk_options = get_option('llms_bkpk_options');
			if(!empty($llms_bkpk_license_key)){
				$lt_custom_classes['path'] = \llms_bkpk\AdminMenu::check_for_other_lt_plugin_classes( 'custom' );
				$lt_custom_classes['namespace'] = 'llms_bkpk';
				$lt_classes = '';
				$classes_available = \llms_bkpk\AdminMenu::get_available_classes( array( $lt_custom_classes, $lt_classes ) );
				// Get options  
				$active_classes = \llms_bkpk\Config::get_active_classes();
				?>
				<div class="module_activated" id="module_activated">
					<i class="fa fa-check-circle" aria-hidden="true"></i><span></span>
				</div>
				<div class="module_deactivated" id="module_deactivated">
					<i class="fa fa-times-circle" aria-hidden="true"></i><span></span>
				</div>
				<div class="lt_feature_container">
					<?php \llms_bkpk\AdminMenu::create_features( $classes_available, $active_classes ); ?>
				</div>
		        <div class="tab blue">
					<input id="tab-11" type="radio" name="tabs2">
					<label for="tab-11" class="tab">Contact Support</label>
					<div class="tab-content tab11">
					<?php
					$from_submitted = isset($_REQUEST['support-subject']) ? $_REQUEST['support-subject'] : '' ;
					if($from_submitted){
					?>
						<p id="support-response"><h1>THANKS FOR ATTEMPTING TO REACH OUT TO US FOR SUPPORT!</p><p>HAD YOU PURCHASED THIS PRODUCT, WE WOULD HAVE RECEIVED IT</h1></p>
						<p><h2><center>INFORMATION YOU HAVE PROVIDED IN THIS FORM SUBMISSION WENT NO-WHERE, BUT IT WOULD BE GUARDED CAREFULLY AND WOULD NOT BE SHARED WITH ANYONE OR IN ANY FORM IF IT DID GO SOMEWHERE</center></h2></p>
						<p style="text-align:center;">If you were a paid subscriber, you would get priority support, however, as a free WordPress user, we will get back to you within a week. As a paid subscriber, an email would have also been sent to you with the information you have submitted.</p>
				<?php }else{ ?>
						<p style="text-align:center;"><h1>LIFTERLMS BACKPACK SUPPORT</h1></p>
						<p><h2><center>THIS FORM IS ONLY ACTIVE FOR PAID SUBSCRIBERS</center></h2></p>
						<p style="text-align:center;">As a paid subscriber, when you submit a support request, we automatically collect your Site URL, First and Last name, email address, license key and WordPress system report.</p>
						<p style="text-align:center;">ALL INFORMATION PROVIDED IN THIS FORM WOULD NOT BE SHARED WITH ANYONE OR IN ANY FORM.<br/>AS A PAID SUBSCRIBER, YOU WOULD RECEIVE A COPY OF THIS EMAIL TO THE EMAIL ADDRESS YOU ARE CURRENTLY LOGGED-IN AS.</p>
						<p>
							<form id="llms-backpack-support" action="#" method="post" name="LifterLMS BackPack Support Form">
								<p> 
								<?php 
								$user_id = get_current_user_id();
								$user_data = get_userdata($user_id);
								?>
									<input id="support-first-name" type="hidden" name="support-first-name" value="<?php echo $user_data->first_name; ?>"/>
									<input id="support-last-name" type="hidden" name="support-last-name" value="<?php echo $user_data->last_name; ?>"/>
									<input id="support-email" type="hidden" name="support-email" value="<?php echo $user_data->user_email; ?>"/>
									<input id="support-url" type="hidden" name="support-url" value="<?php echo home_url(); ?>"/>
									<textarea id="support-report" style="display:none;" type="hidden" name="support-report"/></textarea>
									<table class="form-table">
										<tbody>
											<tr>
												<th><label class="support-label" for="support-subject">Support Subject:</label></th>
											</tr>
											<tr>
												<td><input id="support-subject" type="text" name="support-subject" class="support-full-width"/></td>
											</tr>
											<tr>
												<th><label class="support-label" for="support-message">Describe the problem (Please enter the problem description and steps to reproduce the problem):</label></th>
											</tr>
											<tr>
												<td><textarea rows="10" id="support-message" name="support-message" class="support-full-width"></textarea></td>
											</tr>
											<tr>
												<th><label class="support-label" for="support-login">Support Login (please provide a support user email address to login to your platform as system a administrator):</label></th>
											</tr>
											<tr>
												<td><input id="support-login" type="text" name="support-login" class="support-full-width" /></td>
											</tr>
											<tr>
												<th><label class="support-label" for="support-password">Support Password (please provide the password for the support user - alternately, you can create a new administrative user with the email address dhall@videotron.ca):</label></th>
											</tr>
											<tr>
												<td><input id="support-password" type="text" name="support-password" class="support-full-width" /></td>
											</tr>
											<tr>
												<td><center><button type="submit" class="llms-button-primary">SUBMIT TO SUPPORT</button></center></td>
											</tr>
										</tbody>
									</table>
								</p>
							</form>
							<div style='display:none'>
							<?php 
							//include report 
							include_once(WP_PLUGIN_DIR.'/lifterlms/includes/admin/class.llms.admin.system-report.php');
							LLMS_Admin_System_Report::output();
							?>
								<script>
									jQuery( document ).ready( function( $ ) {
										var $textarea = $( '#support-report' );
										$( '.llms-widget.settings-box' ).each( function( index, element ) {
											var title = $( this ).find( '.llms-label' ).text();
											title = title + '\n' + '-------------------------------------------';
											var val = $( this ).find( 'li' ).text().replace(/  /g, '').replace(/\t/g, '').replace(/\n\n/g, '\n');
											$textarea.val( $textarea.val() + title + '\n' + val + '\n\n' );
										} );
										$( '#copy-for-support' ).on( 'click', function() {
											$textarea.show().select();
											try {
												if ( ! document.execCommand( 'copy' ) ) {
													throw 'Not allowed.';
												}
											} catch( e ) {
												alert( 'copy the text below' );
											}
										} );
										$textarea.on( 'click', function() {
											$( this ).select();
										} );
									});
								</script>
							</div>
						</p>
						<?php } //end else  ?>
						<?php
						}
						echo '<div class="clear"></div>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
		}