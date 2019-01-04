<?php 
class k_lgn extends CI_Controller{
	var $user="";
	function __construct()
	{	
		parent::__construct();
		$this->load->library('session');
		$this->load->library('lib_tables');
		$this->load->library('lib_javidol');
		$this->load->model('mod_operasiberkas');
		$this->load->model('mod_javidol');
		$this->load->model('modules');
		$this->load->model('mod_capjay');
		$this->load->helper('captcha');
		$this->load->helper('url');
		$this->load->library('calendar');
		//session_start();
	}
	function index(){
		if ($this->cekuser()=="yes"){
			$this->artikelnojudul();}
		else{
			$this->logout();
			//redirect('.');
		}
	}
	function logout(){
		
	}
	function cekuser(){
		
	
	}
	
	function download_page($namafile)
	{	
		$permalink='Location:'.base_url().'dnps5d/'.$namafile;
		//header("Location: http://s2.ci.com/dnps5d/".$namafile);
		//echo $permalink;
		header($permalink);
	}
	
	function recet_password()
	{
	}
	
	
}    //http://s2.ci.com/dpns5d/f3f0e339142bbe8bdca3c8df15ed0394.pdf

	?>