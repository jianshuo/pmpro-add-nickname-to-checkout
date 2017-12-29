<?php
/*
Plugin Name: Paid Memberships Pro - Add Nickname to Checkout Add On
Description: Adds nickname field to the user account section at checkout for Paid Memberships Pro.
Version: .3.1
Author: Jian Shuo Wang
Author URI: http://home.wangjianshuo.com
*/

//add the fields to the form
function pmproann2c_pmpro_checkout_after_password()
{
	if(!empty($_REQUEST['nickname']))
		$nickname = $_REQUEST['nickname'];
	elseif(!empty($_SESSION['nickname']))
		$nickname = $_SESSION['nickname'];
	elseif(is_user_logged_in()) {
		$nickname = $current_user->nickname;
	}
	else
		$nickname = "";

	?>
	<div>
	<label for="nickname">昵称</label>
	<input id="nickname" name="nickname" type="text" class="input pmpro_required" size="30" value="<?php echo $nickname; ?>" />
	</div>
	<?php
}
add_action('pmpro_checkout_after_password', 'pmproann2c_pmpro_checkout_after_password');

//require the fields
function pmproann2c_pmpro_registration_checks()
{
	global $pmpro_msg, $pmpro_msgt, $current_user;
	if(!empty($_REQUEST['nickname']))
		$nickname = $_REQUEST['nickname'];
	elseif(!empty($_SESSION['nickname']))
		$nickname = $_SESSION['nickname'];
	elseif(is_user_logged_in()) {
		$nickname = $current_user->nickname;
	}
	else
		$nickname = "";
	
	if($nickname || $current_user->ID)
	{
		//all good
		return true;
	}
	else
	{
		$pmpro_msg = "The nickname fields are required.";
		$pmpro_msgt = "pmpro_error";
		return false;
	}
}
add_filter("pmpro_registration_checks", "pmproann2c_pmpro_registration_checks");

//update the user after checkout
function pmproann2c_update_nickname_after_checkout($user_id)
{
	global $current_user;

	if(!empty($_REQUEST['nickname']))
		$nickname = $_REQUEST['nickname'];
	elseif(!empty($_SESSION['nickname']))
		$nickname = $_SESSION['nickname'];
	elseif(is_user_logged_in()) {
		$nickname = $current_user->nickname;
	}
	else
		$nickname = "";

	update_user_meta($user_id, "nickname", $nickname);
}
add_action('pmpro_after_checkout', 'pmproann2c_update_nickname_after_checkout');

function pmproann2c_pmpro_paypalexpress_session_vars()
{
	//save our added fields in session while the user goes off to PayPal
	$_SESSION['nickname'] = $_REQUEST['nickname'];
}
add_action("pmpro_paypalexpress_session_vars", "pmproann2c_pmpro_paypalexpress_session_vars");

/*
Function to add links to the plugin row meta
*/
function pmproann2c_plugin_row_meta($links, $file) {
	if(strpos($file, 'pmpro-add-nickname-to-checkout.php') !== false)
	{
		$new_links = array(
			'<a href="' . esc_url('http://paidmembershipspro.com/support/') . '" title="' . esc_attr( __( 'Visit Customer Support Forum', 'pmpro' ) ) . '">' . __( 'Support', 'pmpro' ) . '</a>',
		);
		$links = array_merge($links, $new_links);
	}
	return $links;
}
add_filter('plugin_row_meta', 'pmproann2c_plugin_row_meta', 10, 2);
