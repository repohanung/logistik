<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class Muser extends CI_Model {

	
	public function __construct() 
	{
		parent::__construct();
	}

	function get_user() 
	{
		$this->db->select('id, username, nama, role, picture');
		$this->db->order_by('id','desc');
		$q = $this->db->get('users');
		return $q->result();

	}

	function get_user_id($id)
	{
		$this->db->where('id',$id);
		$q = $this->db->get('users');

		return $q->row();
	}
	     
    function get_user_login($nama,$pass) 
	{
		$where = array ('username'=> $nama, 'password'=>$pass);
		$this->db->where($where);
		$q = $this->db->get('users');
		$result = $q->row();
		if(isset($result)){
			return $result;
		}
		else
			return FALSE;
	}

	function get_total_user()
	{
		$q = $this->db->get('users','id');
    	return $q->num_rows();
	}

	function id_incr()
	{
		$this->db->order_by('id','desc');
		$this->db->limit(1);
		$q = $this->db->get('users','id');
		$old_id = $q->row()->id;

		$new_id = $old_id + 1;
		return $new_id;
	}  

	function insert($data)
    {
    	$data['id'] = $this->id_incr();
    	$q = $this->db->insert('users', $data);
    	return $q;

    }

    function edit($data, $id)
    {
    	$this->db->where('id', $id);
    	$q = $this->db->update('users', $data);
    	return $q;
    }

    function delete($id)
    {
    	$this->db->where('id', $id);
    	$q = $this->db->delete('user');
    	if($q) {
    		return TRUE;
    	}
    } 
}
?>