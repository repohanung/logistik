<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model("muser");

	}

	function index()
	{
		if($this->cek_login()) {
			$data = array();
			$data['daftar'] = $this->muser->get_user();
			$data['content'] = "users/list";
			$data['judul'] = "Users";
			$data['active'] = "users";
			$data['js_add'] = null;
			$this->load->view("container", $data);
		}
		else
			redirect('auth');
	}

	function upload_pic() 
	{
        //echo base_url('assets/images/users/').$_FILES['file']['name'];

        //Mulai upload
		$path = 'assets/images/users/';

		$config['upload_path'] = $path;
		$config['allowed_types'] = 'jpg|png|jpeg';
		$config['max_filename'] = '255';
		$config['encrypt_name'] = FALSE;
		//$config['overwrite'] = TRUE;
		$config['remove_spaces'] = TRUE;
		$config['max_size'] = '5000'; //5 MB

        if (isset($_FILES['file']['name'])) {
            if (0 < $_FILES['file']['error']) {
                echo json_encode(array('val' => 'Error during file upload' . $_FILES['file']['error'])); 
                die();
            } else {
                if ($this->url_exists(base_url('assets/images/users/').$_FILES['file']['name'])) {
                    echo json_encode(array('val' => 'file_exist'));
                } else {
                	 
                	$this->upload->initialize($config);	
                    if (!$this->upload->do_upload('file')) {
                        echo json_encode(array('val' => 'error', 'err_val'=>$this->upload->display_errors()));
                    } else {
                        echo json_encode(array('val'=>'sukses', 'nm_file'=>$_FILES['file']['name']));
                    }
                }
            }
        } else {
            echo json_encode(array('val'=>'nofile'));
        }
    }

    function url_exists($url) {
	    $ch = @curl_init($url);
	    @curl_setopt($ch, CURLOPT_HEADER, TRUE);
	    @curl_setopt($ch, CURLOPT_NOBODY, TRUE);
	    @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
	    @curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    $status = array();
	    preg_match('/HTTP\/.* ([0-9]+) .*/', @curl_exec($ch) , $status);
	    if($status[1] == 200) {
	    	return TRUE;
	    }
	    else
	    	return FALSE;
	}

	function tambah() 
	{
		if($this->cek_login()) {
			$data = array();
			$data['unor'] = $this->muser->get_user();
			$data['content'] = "users/tambah";
			$data['judul'] = "Users <small>Tambah Data</small>";
			$data['active'] = "unor";
			$data['js_add'] = null;
			$this->load->view("container", $data);
		}
		else
			redirect('auth');
	}

	function add() 
	{
		$config = array(
			array(
				"field" => "username",
				"label" => "Username",
				"rules" => "required"
				),
			array(
				"field" => "nama",
				"label" => "Nama",
				"rules" => "required"
				),
			array(
				"field" => "pass",
				"label" => "Password",
				"rules" => "required"
				),
			array(
				"field" => "pass_conf",
				"label" => "Konfirmasi Password",
				"rules" => "required|matches[pass]"
				),
			array(
				"field" => "role",
				"label" => "Role",
				"rules" => "required"
				),
		);

		$this->form_validation->set_message("required", "{field} harus diisi.");
		$this->form_validation->set_message("matches", "{field} tidak sama.");
		$this->form_validation->set_rules($config);

		if($this->form_validation->run()==FALSE) {
			$this->tambah();
		}
		else {
			$dt = array();
			$dt['username'] = $this->input->post("username");
			$dt['password'] = md5($this->input->post("pass"));
			$dt['nama'] = $this->input->post("nama");
			$dt['role'] = $this->input->post("role");
			$dt['picture'] = $this->input->post("picture");
			$dt['date_created'] = date('Y-m-d');
			$dt['time_created'] = date('H:i:s');
			$dt['date_modified'] = date('Y-m-d');
			$dt['time_modified'] = date('H:i:s');

			$insert = $this->muser->insert($dt);
			if($insert) {
				$this->session->set_flashdata('msg_success', 'Data berhasil disimpan');
				redirect('users');
			}
		}
	}

	function ubah($id) 
	{
		if($this->cek_login()) {
			$data = array();
			$data['id'] = $id;
			$data['row'] = $this->muser->get_user_id($id);
			$data['content'] = "users/ubah";
			$data['judul'] = "Users <small>Ubah Data</small>";
			$data['active'] = "users";
			$data['js_add'] = null;
			$this->load->view("container", $data);
		}
		else
			redirect('auth');
	}

	function edit($id) 
	{
		$dt = array();
		$dt['username'] = $this->input->post("username");
		$dt['nama'] = $this->input->post("nama");
		$dt['role'] = $this->input->post("role");
		$dt['picture'] = $this->input->post("picture");
		$dt['date_modified'] = date('Y-m-d');
		$dt['time_modified'] = date('H:i:s');

		$edit = $this->muser->edit($dt, $id);
		if($edit) {
			$this->session->set_flashdata('msg_success', 'Data berhasil diubah');
			if($this->session->userdata("username") == $dt['username']) {
				$this->session->set_userdata('picture',$dt['picture']);
			}
			redirect('users');
		}
	}

	function hapus($id)
	{
		$q = $this->munor->delete($id);
		if($q) {
			$this->session->set_flashdata('msg_success','Data berhasil dihapus');
			echo json_encode(array("status" => TRUE));
		}
	}

	function cek_login() 
	{
		if($this->session->userdata('logged_in') == TRUE && $this->session->userdata('role') !=0 ) 
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
}
?>