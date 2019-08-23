<?php
/* Siakad Controler Version 3.2 */
class Keluar extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		no_cache();
	}
	
	function index()
	{
		$this->load->library("session");
		
		$sess_data = array
		(
			"id_biodata"	=> NULL,
			"nama"			=> NULL,
			"username"		=> NULL,
			"kd_user_group"	=> NULL,
			"id_periode"	=> NULL,
			"thn_akademik"	=> NULL,
			"semester"		=> NULL,
			"mfs_log"		=> NULL,
		);
		$this->Userlog_model->insert('logout', 'logout','');
		
		if ($this->session->userdata("base"))
		{
			switch ($this->session->userdata("base"))
			{
				case "P" : $sess_data["id_prodi"] = NULL; break;
				case "J" : $sess_data["id_jurusan"] = NULL; break;
			}
		}
		
		$this->session->unset_userdata($sess_data);
		$this->session->sess_destroy();
		redirect(base_url());
	}
	
}

/* End of file keluar.php */
/* Location: ./application/controllers/keluar.php */