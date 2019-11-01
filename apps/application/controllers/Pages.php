<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_user');
	}

	
	public function index()
	{
    echo 'dsdsa';
		return 'wow';
	}

	public function view($slug = NULL)
	{
		$data = $this->user->get_news($slug);
		print_r($data);
	}

}
