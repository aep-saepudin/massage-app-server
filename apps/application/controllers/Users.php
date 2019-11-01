<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
		$this->load->library('params');

	}

  public function detail($id)
	{
		/**
		 * @return [
		 * 		type: 'u',
		 * 		id  : 1,
		 * ]
		 */
		
		$extract =  $this->params->extractUserParam($id);
		$data = $this->user_model->getDetail($extract['id']);
		print_r($data);
		return $data;
	}
	
	public function index()
	{
    echo 'dsdsa';
		return 'wow';
	}

	public function view(){
		$data = $this->detail('u1');
		print_r($data);
	}

}
