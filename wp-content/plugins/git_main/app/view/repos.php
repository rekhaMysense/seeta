<?php if ( ! defined( 'ABSPATH' ) ) exit; 
   $msg = isset($_GET['msg']) ? $_GET['msg'] : '';
   $type = isset($_GET['type']) ? $_GET['type'] : '';
   $page       = 'git_main_repos';
   
      $this->load_custom_scripts_settings();
      $this->git_controller->datatables_assets();  
      $repos = $this->git_controller->getGitRepos();
      $branches = $this->git_controller->getGitbranches();
      $current_account = $this->git_controller->get_current_account();
      $current_repo = $this->git_controller->get_current_repo();
      $current_branch = $this->git_controller->get_current_branch();
      
      $current_user = wp_get_current_user(); 
      
      $activate_repo = isset($_GET['active_repo']) ? $_GET['active_repo'] : '';
      
      if(!empty($activate_repo)){
          $this->git_controller->activate_repo($activate_repo);
      }
      
      $activate_branch = isset($_GET['active_branch']) ? $_GET['active_branch'] : '';
      
      if(!empty($activate_branch)){
          $this->git_controller->activate_branch($activate_branch);
      }
   
      if(isset($_POST['new_repo']) && wp_verify_nonce( $_POST['repo_nonce_field'], 'save_git_repo' )):
       $this->git_controller->create_repo($_POST);
      endif; 


      if(isset($_POST['new_branch']) && wp_verify_nonce( $_POST['branch_nonce_field'], 'save_git_branch' )):
         $this->git_controller->create_branch($_POST);
        endif; 

      if(isset($_POST['push_website']) && wp_verify_nonce( $_POST['push_website_nonce_field'], 'push_full_website' )):
      $this->git_controller->push_website($_POST);
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
<h3>Active account - <span style="color:green;"><?php echo $current_account->username; ?></span> </h3>
<div class="table-wrap div-center">
   <div class="card card-100">
      <div class="card-header">
         New repository
      </div>
      <div class="card-body" style="max-width: 45%">
         <form action="" method="post">
            <?php  wp_nonce_field( 'save_git_repo', 'repo_nonce_field' ); ?>
            <div class="align-items-center">
               <div class="mb-3">
                  <input type="text" id="" name="repo_name" class="form-control" placeholder="Enter repository name" required>
               </div>
               <div class="mb-3">
                  <div class="form-check">
                     <label class="form-check-label" for="flexCheckDefault">
                     <input class="form-check-input" name="is_private" type="checkbox" value="1" id="flexCheckDefault" style="margin-top:4px;">
                     Make Private
                     </label>
                  </div>
               </div>
               <div class="mb-3">
                  <button type="submit" name="new_repo" value="new_repo" class="btn btn-primary">Create</button>
               </div>
            </div>
         </form>
      </div>
   </div>
   <div class="card card-100">
      <div class="card-header">
         Existing repositories
      </div>
      <div class="card-body">
         <div class="row">
            <?php if($repos == false): ?>
            <div class="col-md-12">No branch available</div>
            <?php else:?>
            <div class="col-md-7">
               <?php  if(!$current_repo){ ?>
               <div id="branchlHelp" class="form-text">Please select one repository</div>
               <?php 
                  } ?>
               <div class="dropdown">
                  <button class="btn btn-secondary dropdown-toggle repo-drpdown" type="button" id="dropdownRepo" data-bs-toggle="dropdown" aria-expanded="false">
                  <?php echo $current_repo?$current_repo:'Select repository'; ?>
                  </button>
                  <ul class="dropdown-menu repo-drpdown" aria-labelledby="dropdownRepo">
                     <?php    
                        foreach($repos as $repo){ 
                        
                         $active_repo = $repo['name'];
                         $active_repo_url = add_query_arg(compact('page','active_repo'), admin_url('admin.php'));
                        
                         $class='';
                         if($repo['name']==$current_repo){
                           $class='active';
                         }
                         ?> 
                     <li class="list-group-item <?php echo $class; ?>"><a href="<?= $active_repo_url ?>" class="repo-anchr"><?php echo $repo['name']; ?></a></li>
                     <?php    } ?>
                  </ul>
               </div>
            </div>
            <div class="col-md-5" style="text-align:center">
               <h1><?php echo $current_repo?'Current repo - '.$current_repo:'No active repository' ?> </h1>
            </div>
            <?php  endif; ?>
         </div>
      </div>
   </div>
   <div class="card card-100">
      <div class="card-header">
         Current Repository branches
      </div>
      <div class="card-body">
         <div class="row">
            <?php if(empty($branches) && !$current_repo): ?>
               <div class="col-md-3">No branch available</div>
            <?php elseif(empty($branches) && $current_repo):?>

               <div class="push-website">
                  <h1>Push complete website</h1>
                  <form action="" method="post">
                     <?php  wp_nonce_field( 'push_full_website', 'push_website_nonce_field' ); ?>
                     <div class="align-items-center">
                     </div>
                           <div class="mb-3">
                              <label for="Textarea1" class="form-label">Commit Message</label>
                              <textarea class="form-control" name="commit_msg" id="Textarea1" rows="3"></textarea>
                           </div>
                        <div class="mb-3">
                           <button type="submit" name="push_website" value="push_website" class="btn btn-primary">Create</button>
                        </div>
                     </div>
                  </form>
               </div>

            <?php else:?>
            <div class="col-md-3">
               <?php  if(!$current_branch){ ?>
               <div id="repolHelp" class="form-text">Please select one branch</div>
               <?php 
                  }
                  ?>
               <div class="dropdown">
                  <button class="btn btn-secondary dropdown-toggle repo-drpdown" type="button" id="dropdownBranch" data-bs-toggle="dropdown" aria-expanded="false">
                  <?php echo $current_branch?$current_branch:'Select branch'; ?>
                  </button>
                  <ul class="dropdown-menu repo-drpdown" aria-labelledby="dropdownBranch">
                     <?php    
                        foreach($branches as $branch){ 
                        
                         $active_branch = $branch['name'];
                         $active_branch_url = add_query_arg(compact('page','active_branch'), admin_url('admin.php'));
                        
                         $class='';
                         if($branch['name']==$current_branch){
                           $class='active';
                         }
                         ?> 
                     <li class="dropdown-item <?php echo $class; ?>"><a href="<?= $active_branch_url ?>" class="repo-anchr"><?php echo $branch['name']; ?></a></li>
                     <?php    } ?>
                  </ul>
               </div>
            </div>
           
            <div class="col-md-4">
               <?php  if($current_repo):?>
               <div class="new-branch">
                  <h1>New branch</h1>
                  <form action="" method="post">
                     <?php  wp_nonce_field( 'save_git_branch', 'branch_nonce_field' ); ?>
                     <div class="align-items-center">
                        <div class="mb-3">
                           <input type="text" id="" name="branch_name" class="form-control" placeholder="Enter branch name" required>
                        </div>
                        <div class="mb-3">
                           <button type="submit" name="new_branch" value="new_branch" class="btn btn-primary">Create</button>
                        </div>
                     </div>
                  </form>
               </div>
              
            </div>

            <div class="col-md-5" style="text-align:center;">
               <?php if($current_branch): ?>
               <div>
                  <h1>Current branch - <?php echo $current_branch;?> </h1>
                  <div id="repolHelp" class="form-text">Perform your required action to the selected branch</div>
                  <div>
                     <div class="div-center">

                        <?php $pullNonce = wp_create_nonce( 'git_pull_nonce' ); ?>
                        <?php $pushNonce = wp_create_nonce( 'git_push_nonce' ); ?>
                        <input type="hidden" id="wp_pull_nonce" name="wp_pull_nonce" value="<?php echo $pullNonce; ?>">
                        <input type="hidden" id="wp_push_nonce" name="wp_push_nonce" value="<?php echo $pushNonce; ?>">

                        <button type="button" class="btn btn-primary btn-lg git-pull-request">Pull changes</button>
                        <button type="button" class="btn btn-primary btn-lg check-git-status">Check status</button>
                        <button type="button" class="btn btn-primary btn-lg git-push-request">Push changes</button>
                     </div>
                     <div class="div-center results-div"></div>
                  </div>
                  <?php else: ?>
                  <h1>No active branch</h1>
                  <div id="repolHelp" class="form-text">Please select a branch or create a new one....</div>
                  <?php endif; ?>
               </div>
               <?php endif; ?>
            </div>
            <?php  endif; ?>
         </div>
      </div>
   </div>
</div>
<script></script>