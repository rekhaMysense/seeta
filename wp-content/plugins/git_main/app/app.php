<?php namespace te\pa;
use te\app\cnt\git_main_controller as run_git_main_controller;
use te\app\mdl\git_main_model as run_git_main_model;
class git_main_app {
	var $controller;
	var $model;
	public function __construct() {
	    $this->controller();
		//$this->model();
	}
	public function controller() {
		if(is_admin()) {	
		  include('controller/controller.php');
		 $controller = new run_git_main_controller;	
		} 
	}
	// public function model() {
	// 	if(is_admin()) {
	//        include('model/model.php');
	//       $model = new run_git_main_model;	
	// 	}
	// }
	
}