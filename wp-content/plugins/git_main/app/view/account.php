<?php if ( ! defined( 'ABSPATH' ) ) exit; 
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$active_id = isset($_GET['active_id']) ? $_GET['active_id'] : '';



if(!empty($active_id)){
    $this->git_controller->activate_account($active_id);
}
$this->git_controller->datatables_assets(); 
$this->load_custom_scripts_settings();
 
$accounts = $this->git_controller->get_accounts();

$current_user = wp_get_current_user(); 
$ls_nonce = wp_create_nonce( 'list-account' ); 
$page       = 'git_add_account';
$add_setting_url = add_query_arg(compact('page'), admin_url('admin.php'));

if(!empty($msg) && !empty($type)):
    if($type=='success'){
        $this->success($msg);
    }else{
        $this->error($msg);
    }
endif; 


?>
<div class="main-wrapper">
<h1>List accounts</h1>
<div class="btn-wrap"><a  href="<?php echo $add_setting_url ?>" class="add-setting-btn btn btn-primary float-end" type="button">link account</a></div>
<div class="table-wrap div-center">   
<table id="settings-table" class="display" cellspacing="0" style="width:100%">
    <thead>
        <tr>
            <th>Username</th>
            <th>Personal access token</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody> 
   <?php foreach($accounts  as $account): ?>
        <tr>
            <td><?= $account->username ?></td>
            <td class="short-txt"><?= $account->personal_access_token ?></td>
            <td><?php
            if($account->is_active==1):
                echo '<p class="active-account">Activated</p>';
            else:
                $page       = 'git_main_theme';
                $active_id = $account->id;
                $setting_url = add_query_arg(compact('page','active_id'), admin_url('admin.php'));
                echo '<a href="'.$setting_url.'"  class="anchr">Set active</a>';
            endif;
            ?></td>
        </tr>
    <?php endforeach; ?>  
    </tbody>
</table>

</div>
</div>
<script>
    jQuery(document).ready(function ($) {

    $('#settings-table').DataTable();

    });
</script>
