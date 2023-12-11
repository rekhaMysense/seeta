<?php if ( ! defined( 'ABSPATH' ) ) exit;
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';

$this->load_custom_scripts_settings();
if(isset($_POST['save_account']) && wp_verify_nonce( $_POST['git_nonce_field'], 'save_git_acnt' )):
    $this->sava_git_account($_POST);
   endif; 



if(!empty($msg) && !empty($type)):
    if($type=='success'){
        $this->success($msg);
    }else{
        $this->error($msg);
    }
endif; 

?>

<div class="main-wrapper">
<h1>Link account</h1>
<div class="form-wrap"> 
<form action="" method="post">
<?php  wp_nonce_field( 'save_git_acnt', 'git_nonce_field' ); ?>
  <input type="hidden" name="user_id" class="form-control" value="<?php echo get_current_user_id(); ?>" >
 
  <div class="mb-3">
    <label for="username" class="form-label">Username</label>
    <input type="text" name="username" class="form-control" id="username" required >
  </div>
 
  <div class="mb-3">
    <label for="pac" class="form-label">Personal access token</label>
    <input type="text" name="personal_access_token" class="form-control" id="pac" aria-describedby="paclHelp" required>
    <div id="emailHelp" class="form-text">If you do not have one, <a href="https://github.com/settings/apps" target="_blank">click here</a> to generate a Personal Access Token for Git</div>
  </div>
 
  <button type="submit" name="save_account" value="save_account" class="btn btn-primary">Save</button>
</form>
</div>
</div>

