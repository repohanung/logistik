<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class Auth extends CI_Controller {

	
	public function __construct() 
	{
		parent::__construct();
		$this->load->model('muser');
	}

	public function index()
	{
		$data['judul'] = "Aplikasi Manajemen Logistik Bantuan Bencana";
		$this->load->view('login', $data);
	}
     
    function cek_login() 
	{

		if($this->session->userdata("logged_in") === TRUE) 
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function login()
	{
		$username = $this->input->post('username');
		$pass = trim($this->input->post('password'));
		$user = $this->muser->get_user_login($username, $pass);
		if(isset($user) && $user != FALSE) {
			$sess = array(
				"username" => $user->username,
				"nama" => $user->nama,
				"picture" => $user->picture,
				"role" => $user->role,
				"logged_in" => TRUE
				);
			$this->session->set_userdata($sess);
        	redirect("dashboard");
		}
		else {
			$this->session->set_flashdata("msg", "Username atau password salah !!");
			$this->session->sess_destroy();
			$this->load->view('login');
		}
		
	}
        
   function logout()
	{
		$this->session->sess_destroy();
        redirect("auth");
	}
}
?>