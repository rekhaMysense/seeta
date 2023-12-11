<?php
/*
Plugin Name: Git Main
Description: GitHub integration
Author: mndpsingh287
Version: 1.0.0
Author URI: https://profiles.wordpress.org/mndpsingh287
*/

define( 'MK_GIT_MAIN_PATH', WP_PLUGIN_DIR . '/' . basename( dirname( __FILE__ ) ) . '/' );
define( 'MK_GIT_MAIN_URL', plugin_dir_url( MK_GIT_MAIN_PATH ) . basename( dirname( __FILE__ ) ) . '/' );
define( 'MK_GIT_MAIN_FILE', __FILE__);
if (!defined("MK_GIT_MAIN_DIRNAME")) define("MK_GIT_MAIN_DIRNAME", plugin_basename(dirname(__FILE__)));

define('ENCRYPTION_SECRET_KEY', 'A2S4Q6J6RC4R3K8BF92VFK86M9LF3VG453VT433K5N5VC5P8YCYIPO' );
define('ENCRYPTION_SECRET_IV', 'A2S4H9J6RC4K3K8BL32VFK86T9LF3VG453VT433K5N5VK5M8VRS49H' );

function encrypt_decrypt($action, $string) {
       
	$output = false;
	
	$encrypt_method = "AES-256-CBC";
	$key = hash('sha256', ENCRYPTION_SECRET_KEY);
	$iv = substr(hash('sha256', ENCRYPTION_SECRET_IV), 0, 16);

	if ( $action == 'encrypt' ) {
		$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
		$output = base64_encode($output);
	} else if( $action == 'decrypt' ) {
		$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
	}
	return $output;
}

add_action('wp_ajax_get_status', 'get_status_callback');
do_action( "wp_ajax_nopriv_get_status",  'get_status_callback' );


function get_status_callback() {
	if ( !is_user_logged_in() ) {
		echo 'Please login first.';die;
	}
	global $wpdb; 
	$current_user = get_current_user_id(); 
	$account = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}git_accounts WHERE is_active = 1 AND user_id = {$current_user}", OBJECT );
	if($account){
		$linked = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}current_linked_repo WHERE account_id = {$account->id}", OBJECT );
		if($linked){
			$repo = $linked->repo_name;
			$branch = $linked->branch_name;

			//$repositoryPath = ABSPATH.$repo;
			$repositoryPath = ABSPATH;
			chdir($repositoryPath);

			$Command = "git status";
			exec($Command, $Output, $ReturnCode);
			// <button type="button" class="btn-close close-btn" aria-label="Close"></button>
			$status =  '<div data-bs-theme="dark" class="error-div">
			';

			foreach($Output as $text){
				$status .= '<p>'.$text.'</p>';
			}
			$status .= '</div>';
			echo $status;

			exit(); // this is required to return a proper result & exit is faster than die();
		}
		
	}
	echo "Error occured while fetching status for this branch..";die;
	
}
add_action('wp_ajax_git_pull', 'git_pull_callback');
do_action( "wp_ajax_nopriv_git_pull",  'git_pull_callback' );

function git_pull_callback() {
	if ( !is_user_logged_in() ) {
		echo 'Please login first.'; exit();
	}
	$nonce = $_REQUEST['_wpnonce'];
	if ( ! wp_verify_nonce( $nonce, 'git_pull_nonce' ) ) {
		echo 'This nonce is not valid.'; exit();
	}
	global $wpdb; 
	$current_user = get_current_user_id(); 
	$account = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}git_accounts WHERE is_active = 1 AND user_id = {$current_user}", OBJECT );
	if($account){
		$linked = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}current_linked_repo WHERE account_id = {$account->id}", OBJECT );
		if($linked){
			$repo = $linked->repo_name;
			$branch = $linked->branch_name;

			//$repositoryPath = ABSPATH.$repo;
			$repositoryPath = ABSPATH;
			chdir($repositoryPath);
			$Command = "git pull origin {$branch}";
			exec($Command, $Output, $ReturnCode);
			
				// <button type="button" class="btn-close close-btn" aria-label="Close"></button>
				$res =  '<div data-bs-theme="dark" class="error-div">';

				foreach($Output as $text){
					$res .= '<p>'.$text.'</p>';
				}
				$res .= '</div>';
				echo $res;
			
			exit();
		}
	}
	echo "Error occured while taking pull."; exit();
}

add_action('wp_ajax_git_push', 'git_push_callback');
do_action( "wp_ajax_nopriv_git_push",  'git_push_callback' );
function git_push_callback() {
	if ( !is_user_logged_in() ) {
		echo 'Please login first.'; exit();
	}
	$nonce = $_REQUEST['_wpnonce'];
	if ( ! wp_verify_nonce( $nonce, 'git_push_nonce' ) ) {
		echo 'This nonce is not valid.'; exit();
	}
	global $wpdb; 
	$current_user = get_current_user_id(); 
	$account = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}git_accounts WHERE is_active = 1 AND user_id = {$current_user}", OBJECT );
	if($account){
		$linked = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}current_linked_repo WHERE account_id = {$account->id}", OBJECT );
		
		if($linked){
			$username = $account->username;
			$accessToken = encrypt_decrypt('decrypt',$account->personal_access_token);
			$repo = $linked->repo_name;
			$branch = $linked->branch_name;
			$remoteRepository = 'https://'.$username.':'.$accessToken.'@github.com/'.$username.'/'.$repo.'.git';

			//$repositoryPath = ABSPATH.$repo;
			$repositoryPath = ABSPATH;
			chdir($repositoryPath);
			//test comment
			$commitMessage = $_REQUEST['commit_msg'];

			if (is_dir('.git')) {
				exec("git add .");
				exec("git commit -m '{$commitMessage}'");
				$Command = "git push origin {$branch}  2>&1";
				exec($Command, $Output, $ReturnCode);
					
			}
			$res =  '<div data-bs-theme="dark" class="error-div">';
			foreach($Output as $text){
				$res .= '<p>'.$text.'</p>';
			}
			
			$res .= '</div>';
			echo $res;
			exit();
		}
	}
	echo "Error occured while making pushing."; exit();
}

include('app/app.php');

use te\pa\git_main_app as run_git_main_app;
new run_git_main_app;