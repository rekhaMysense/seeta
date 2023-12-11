

<?php if ( ! defined( 'ABSPATH' ) ) exit; 
$this->load_custom_scripts_settings();
$current_user = wp_get_current_user(); 

if(isset($_POST['save_repo']) && wp_verify_nonce( $_POST['repo_nonce_field'], 'save_git_repo' )):
    $this->sava_git_repo($_POST);
   endif; 

?>
<div class="main-wrapper">
<h1>Add repository</h1>
<div class="form-wrap"> 
<form action="" method="post">
<?php  wp_nonce_field( 'save_git_repo', 'repo_nonce_field' ); ?>
<input type="hidden" name="user_id" class="form-control" value="<?php echo get_current_user_id(); ?>" >
<div class="mb-3">
    <label for="repo" class="form-label">Repo name</label>
    <input type="text" name="repo_name" class="form-control" id="repo" >
  </div>
  <div class="mb-3">
    <label for="branch" class="form-label">Branch name</label>
    <input type="text" name="branch_name"  class="form-control" id="branch" >
  </div>
 
  <div class="mb-3">
  <label for="account" class="form-label">Account</label>
      <select id="account" name="account_id"  class="form-select select-inpts" aria-label="Default select example">
      <option value="" selected>Open this select menu</option>
      <?php echo $this->git_controller->get_acc_options();  ?>
        </select>
    </div>
 
  <button type="submit" name="save_repo" value="save_repo" class="btn btn-primary">Save</button>
</form>
</div>
</div>



