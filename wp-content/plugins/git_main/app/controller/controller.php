<?php namespace te\app\cnt;
use te\app\thm_cnt\git_controller as run_git_controller;

class git_main_controller {

	public function __construct() {
	   $this->init();
	}	
	public function init() {

	    add_action( 'admin_menu', array($this, 'git_main_menu_page'));
		if(is_admin()) {
			include('git_controller.php');
		}
		// echo ABSPATH;
		$this->git_controller = new run_git_controller;
		
	}
	
	public function git_main_menu_page() {
		if(is_super_admin()){
			add_menu_page( __( 'GitHub', 'te-editor' ),'GitHub','manage_options', 'git_main_theme',array($this, 'add_git_page'),plugins_url( '/app/view/images/te.svg', MK_GIT_MAIN_FILE )); 
	
		}
	
	add_submenu_page( 'git_main_theme', __( 'List repos', 'git-main-repos' ), __( 'List repos', 'git-main-repos' ), 'manage_options', 'git_main_repos', array(&$this, 'git_main_repos_callback'));
	
	//Hidden..
	add_submenu_page( 'options-writing.php', __( 'Add account', 'git-add-account' ), __( 'Add account', 'git-add-account' ), 'manage_options', 'git_add_account', array(&$this, 'git_main_add_account_callback'));
	add_submenu_page( 'options-writing.php', __( 'Add repo', 'git-add-repo' ), __( 'Add repo', 'git-add-repo' ), 'manage_options', 'git_add_repo', array(&$this, 'git_main_add_repo_callback'));
	}	
	public function git_main_add_account_callback() {
		include(MK_GIT_MAIN_PATH.'app/view/add_account.php');	
	}
	public function git_main_add_repo_callback() {
		include(MK_GIT_MAIN_PATH.'app/view/add_repo.php');	
	}
	public function git_main_repos_callback() {
		include(MK_GIT_MAIN_PATH.'app/view/repos.php');	
	}
	public function add_git_page() {
		include(MK_GIT_MAIN_PATH.'app/view/account.php');
	}
	
	
	 public function load_custom_scripts_settings() {
		// $current_page = isset($_GET['page']) ? $_GET['page'] : ''; 
		//  if($current_page == 'theme_editor_settings') {
		//  }
		wp_enqueue_style( 'bootstrap-style','https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css' );
		wp_enqueue_style( 'custom-style', MK_GIT_MAIN_URL.'app/view/css/custom.css' );
		wp_enqueue_script('bootstrap-script','https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js');
		wp_enqueue_script( 'jquery-js', 'https://code.jquery.com/jquery-3.7.1.js' );
		wp_enqueue_script( 'custom-js', MK_GIT_MAIN_URL.'app/view/js/custom.js' );
		
		
		

	 }
	 public function sava_git_account($fields) {
		echo 'Saving Please wait...';
		 $save = $this->git_controller->__save( $fields );
		 
		 if($save) {
			$msg = $save['msg'];
			//echo $msg;die;
			$type = $save['type'];
			if($type=='error'){
				$this->redirect('admin.php?page=git_add_account&msg='.$msg.'&type='.$type);
			}else{
				$this->redirect('admin.php?page=git_main_theme&msg='.$msg.'&type='.$type);
			}
		 }
	}
	

	 public function success($msg) {
		_e( '<div class="updated settings-error notice is-dismissible" id="setting-error-settings_updated"> 
			<p><strong>'.$msg.'</strong></p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', 'te-editor');	
			}
			public function error($msg) {
					_e( '<div class="error settings-error notice is-dismissible" id="setting-error-settings_updated"> 
			<p><strong>'.$msg.'</strong></p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', 'te-editor');	
			}
			public function redirect($url) {
				?>
				<script>
				var get_last_Selecetd_tab =localStorage.getItem('theme_editor_selected_tab');
				if(get_last_Selecetd_tab==null){
					window.location.href='<?php echo $url; ?>';
				}else{
					window.history.pushState(null,null,'<?php echo $url; ?>'+get_last_Selecetd_tab);
					window.location.reload(true);
				}
				</script>
				<?php
			}
	 
}