<?php namespace te\app\thm_cnt;
class git_controller {
	   
		/*
		* construct
		*/
		public function __construct() {
			global $wpdb;
			//echo $this->encrypt_decrypt('decrypt','QTA5cTlPdWUyU2hic3pyclE4bGhVVVpueDdTdUY5WFpiMVlrbGZiTGRQdXNiUFFZU0h1eSt5YlRqWFVEazZpQg');
			
		}
		/*
		* Theme Data
		*/

		function datatables_assets() {
			wp_enqueue_style( 'datatable_style', 'https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css' );
			wp_enqueue_script( 'datatables', 'https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js', array( 'jquery' ) );
		}
		
		private function encrypt_decrypt($action, $string) {
       
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
	

		function verifyGitCreds($data)
		{
			$username = $data['username'];
			$accessToken = $data['personal_access_token'];

			$apiUrl = "https://api.github.com/users/{$username}";
			$headers = [
				'Authorization: Bearer ' . $accessToken,
				'User-Agent: github', // Replace with your application name
			];
			
			$ch = curl_init($apiUrl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$response = curl_exec($ch);
			$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
			curl_close($ch);
			return $statusCode === 200;
		}

		
		public function get_accounts() {
			global $wpdb;
			$current_user = get_current_user_id(); 
			$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}git_accounts WHERE user_id = {$current_user}", OBJECT );
			//$this->debug($results);
			return $results;

		}
		function getGitbranches()
		{
			$account = $this->get_current_account();

			
			$accessToken = $this->encrypt_decrypt('decrypt',$account->personal_access_token);
			$username = $account->username;
			$repo = $this->get_current_repo();
			if($repo){
				$apiUrl = "https://api.github.com/repos/$username/$repo/branches";

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $apiUrl);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, [
					'Authorization: Bearer ' . $accessToken,
					'User-Agent: github' 
				]);
				$response = curl_exec($ch);
				curl_close($ch);

				if ($response === false) {
					$branches = null;
				}else{
					$branches = json_decode($response, true);
				}
			}else{
				$branches = null;
			}
			//$this->debug($response);
			if ($branches === null) {
				return array();
			}
			return $branches;
		}
		function getGitRepos()
		{
			global $wpdb;
			$current_user = get_current_user_id(); 
			$account = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}git_accounts WHERE is_active = 1 AND user_id = {$current_user}", OBJECT );
			//$this->debug($account);
			$username = $account->username;
			$accessToken = $this->encrypt_decrypt('decrypt',$account->personal_access_token);

			//$apiUrl = "https://api.github.com/users/{$username}/repos";
			$apiUrl = "https://api.github.com/user/repos";

			$headers = [
				'Authorization: Bearer ' . $accessToken,
				'User-Agent: github', 
			];

			$ch = curl_init($apiUrl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$response = curl_exec($ch);
			$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			curl_close($ch);
			//$this->debug(json_decode($response, true));
			if ($statusCode === 200) {
				return json_decode($response, true);
			} else {
				return false;
			}
		}
		
		public function get_current_account() {
			global $wpdb;
			$current_user = get_current_user_id(); 
			$account = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}git_accounts WHERE is_active = 1 AND user_id = {$current_user}", OBJECT );
			return $account;
		}
		public function get_current_repo() {
			global $wpdb;
			$current_account = $this->get_current_account()->id;
			$current_repo = $wpdb->get_row( "SELECT repo_name FROM {$wpdb->prefix}current_linked_repo WHERE account_id = {$current_account}", OBJECT );
			
			return isset($current_repo->repo_name)?$current_repo->repo_name:"";
		}
		public function get_current_branch() {
			global $wpdb;
			$current_account = $this->get_current_account()->id;
			$current_branch = $wpdb->get_row( "SELECT branch_name FROM {$wpdb->prefix}current_linked_repo WHERE account_id = {$current_account}", OBJECT );
			
			return isset($current_branch->branch_name)?$current_branch->branch_name:"";
		}
		public function push_website($post){
			$account = $this->get_current_account();
			$username = $account->username;
			$accessToken = $this->encrypt_decrypt('decrypt',$account->personal_access_token);
			$current_repo = $this->get_current_repo();
			//$repositoryPath = ABSPATH.$current_repo; // Replace with the actual path to your Git repository
			
			$repositoryPath = ABSPATH;

			$remoteRepository = 'https://'.$username.':'.$accessToken.'@github.com/'.$username.'/'.$current_repo.'.git';
			$commitMessage = $post['commit_msg'];
			
			chdir(ABSPATH);
			exec('git init');
			exec('git add .');
			exec("git commit -m '{$commitMessage}'");
			exec("git remote add origin {$remoteRepository}");
			$outputPush = exec('git push origin main',$output,$statuscode);
			
			if ($statuscode === 0) {
				$type = 'success';
				$msg =  "Branch main pushed to the remote repository.";
			}else{
				$type = 'error';
				$msg =  "Error pushing branch to remote repository. Command failed with return code: {$pushReturnCode}";
			}

			$this->redirect('admin.php?page=git_main_repos&msg='.$msg.'&type='.$type);

			echo $statuscode;
			print_r($outputPush);die;

		}
		public function create_branch($post){
			$account = $this->get_current_account();
			$username = $account->username;
			$accessToken = $this->encrypt_decrypt('decrypt',$account->personal_access_token);
			$current_repo = $this->get_current_repo();
		//	$repositoryPath = ABSPATH.$current_repo; // Replace with the actual path to your Git repository
			$repositoryPath = ABSPATH; 
			$git_url = 'https://'.$username.':'.$accessToken.'@github.com/'.$username.'/'.$current_repo.'.git';
			$newBranchName = $post['branch_name'];

			chdir($repositoryPath);
			if (is_dir('.git')) {
				$command = "git checkout -b {$newBranchName}";
				exec($command, $output, $returnCode);
				exec('git branch');
				if ($returnCode === 0) {
					exec('git checkout {$newBranchName}');
					exec('git fetch origin');
					$pushCommand = "git push origin {$newBranchName}";
					exec($pushCommand, $pushOutput, $pushReturnCode);
					if ($pushReturnCode === 0) {
						$type = 'success';
						$msg =  "Branch '{$newBranchName}' pushed to the remote repository.";
					}else{
						$type = 'error';
						$msg =  "Error pushing branch to remote repository. Command failed with return code: {$pushReturnCode}";
					}
				} else {
					$type = 'error';
					$msg =  "Error creating branch. Command failed with return code: {$returnCode}";
				}
			}else{
				$type = 'error';
				$msg =  "Git repository not found at {$repositoryPath}";
			}
			$this->redirect('admin.php?page=git_main_repos&msg='.$msg.'&type='.$type);
		}
		public function create_repo($post){
			
			$account = $this->get_current_account();
			$accessToken = $this->encrypt_decrypt('decrypt',$account->personal_access_token);
			$username = $account->username;
			$repoName = $post['repo_name'];
			$private = false;
			if(isset($post['is_private'])){
				$private = true;
			}
			$apiUrl = "https://api.github.com/user/repos";
			
			$data = [
				'name' => $repoName,
				'description' => $repoName.' repository created in '.$username,
				'auto_init' => true, 
				'private' =>$private,
			];
			//$this->debug($data);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $apiUrl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Authorization: Bearer ' . $accessToken,
				'User-Agent: github', 
				'Content-Type: application/json',
			]);

			$response = curl_exec($ch);
			curl_close($ch);
			
			if ($response === false) {
				$type = 'error';
				$msg = 'Error: Could not create repository!';
				$this->redirect('admin.php?page=git_main_repos&msg='.$msg.'&type='.$type);
			}

			$repoInfo = json_decode($response, true);

			if ($repoInfo === null) {
				$type = 'error';
				$msg = 'Error: Something went wrong!';
				$this->redirect('admin.php?page=git_main_repos&msg='.$msg.'&type='.$type);
			}
			$result = json_decode($response, true);
			//$this->debug($result);

			if(isset($result['errors'])){
				$message = isset($result['errors'][0]['message'])?$result['errors'][0]['message']:'Something went wrong!';
				$type = 'error';
				$msg = 'Success: '.$message;
			}else{
				$type = 'success';
				$msg = 'Success: Repository created successfully!';
			}
			
			$this->redirect('admin.php?page=git_main_repos&msg='.$msg.'&type='.$type);
			

		}
		public function activate_branch($branch_name) {
			global $wpdb;
			$current_account = $this->get_current_account()->id;
			$tablename=$wpdb->prefix.'current_linked_repo';

			$current_repo = $this->get_current_repo();
			if($current_repo){
				$data_update = array('branch_name'=>$branch_name);
				$data_where = array('account_id'=>$current_account);

				$wpdb->update($tablename, $data_update, $data_where);
			}

			//$repoPath = ABSPATH.$current_repo; //mysen
			$repoPath = ABSPATH; 

			if (is_dir($repoPath)) {
				chdir($repoPath);
				exec("git add .");
				exec("git commit -m 'Comiting changes before switching branch'");
				exec("git checkout {$branch_name}",$output,$returnCode);
			}

			$this->redirect('admin.php?page=git_main_repos');
		}
		public function activate_repo($repo_name) {
			global $wpdb;
			$current_account = $this->get_current_account()->id;
			$tablename=$wpdb->prefix.'current_linked_repo';

			$current_repo = $this->get_current_repo();
			//echo $current_repo;die;
			if($current_repo){
				$data_update = array('repo_name'=>$repo_name,'branch_name'=>null);
				$data_where = array('account_id'=>$current_account);
				$wpdb->update($tablename, $data_update, $data_where);
			}else{
				$data['repo_name']=$repo_name;
				$data['account_id']=$current_account;
				$saveRepo =  $wpdb->insert( $tablename, $data);
			}

			$repoPath = ABSPATH.$repo_name; 

			// if (!is_dir($repoPath)) {
			// 	if (mkdir($repoPath, 0755, true)) {
			// 		chmod($repoPath, 0755);

			// 		$account = $this->get_current_account();
			// 		$username = $account->username;
			// 		$accessToken = $this->encrypt_decrypt('decrypt',$account->personal_access_token);
			// 		$git_url = 'https://'.$username.':'.$accessToken.'@github.com/'.$username.'/'.$current_repo.'.git';
			

			// 		exec("git init {$repoPath}", $output, $returnCode);
			// 		chdir($repoPath);
					
			// 		exec("git remote add origin {$git_url}");
			// 		// Fetch all remote branches and data
			// 		exec('git fetch origin');

			// 		if ($returnCode === 0) {
			// 			$type  = 'success';
			// 		} else {
			// 			$type  = 'error';
			// 			$msg =  "Error initializing Git repository. Command failed with return code: {$returnCode}";
			// 		}
			// 		$this->redirect('admin.php?page=git_main_repos&msg='.$msg.'&type='.$type);
			// 	} 
			// } 
			$this->redirect('admin.php?page=git_main_repos');
			
		}

		public function activate_account($id) {
			global $wpdb;
			$tablename=$wpdb->prefix.'git_accounts';

			$data_update = array('is_active'=>0);
			$data_where = array('is_active'=>1);
			$wpdb->update($tablename, $data_update, $data_where);
			
			$data_update = array('is_active'=>1);
			$data_where = array('id'=>$id);
			$wpdb->update($tablename, $data_update, $data_where);

			$this->redirect('admin.php?page=git_main_theme');
		}
		
		public function __save($fields) {
			//echo '1';die;
			global $wpdb;
			$tablename=$wpdb->prefix.'git_accounts';

			$data = array();
			$needToUnset = array('save_account','git_nonce_field','_wp_http_referer');
			foreach($needToUnset as $noneed):
				unset($fields[$noneed]);
			endforeach;
			foreach($fields as $key => $val):
				$data[$key] = $val;
			endforeach;
			
			$username = $data['username'];
			$gitUser = $wpdb->get_row( "SELECT * FROM {$tablename} WHERE username = '{$username}'", OBJECT );
			if($gitUser){
				$res['type'] = 'error';
				$res['msg'] = 'Error: Setting already exists!';
				return $res;
			}
	
			$authenticated = $this->verifyGitCreds($data);
			if($authenticated){
				$data['created']= strtotime('now');

				$current_user = get_current_user_id();
				$user = $wpdb->get_row( "SELECT * FROM {$tablename} WHERE user_id = {$current_user}", OBJECT );
				if(!$user){
					$data['is_active']= 1;
				}
				$data['personal_access_token'] = $this->encrypt_decrypt('encrypt',$data['personal_access_token']);
				$saveSettings =  $wpdb->insert( $tablename, $data);
				if($saveSettings){
					$res['type'] = 'success';
					$res['msg'] = 'Success: Settings Saved!';
				}
				else {
					$res['type'] = 'error';
					$res['msg'] = 'Error: Settings Not Saved!';
				}
			}else{
				$res['type'] = 'error';
				$res['msg'] = 'Error: Invalid credentials!';
			}
			return $res;
			
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

		



		 function debug($data){
			echo '<pre>';
			print_r($data);
			echo '</pre>';
			die;
		 }

	
}