<?php
/* Siakad Controler Version 3.2 */
class Cari extends CI_Controller {

	var $menu = "Data Mahasiswa";

	function __construct()
	{
		parent::__construct();
		$this->page->set_base_url(site_url("/Cari"));
	}
	
	function search()
	{
		$keyword = $this->input->post("keyword");
		$sess_data = array
				(
					
					"kd_user_group"	=> 'cri',
					
					"status"		=> "debug ver 2.1",
				);
		$this->session->set_userdata($sess_data);	
		redirect($this->page->base_url("/index/$keyword"));
	}
	
	function index($keyword = "", $page = 1, $limit = 20, $item = "nim", $order = "asc")
	{
		$this->load->library("grid");
		$this->load->model("Cari_model", "cari");
		
		if ($keyword == "")
		{
			redirect($this->page->base_url());
		}
		
		$id_prodi = $this->session->userdata("id_prodi");
		
		$grid_conf = array
		(
			"base_url"	=> $this->page->base_url("/index/$keyword"),
			"act_url"	=> $this->page->base_url(),
			"items"		=> $this->cari->items(),
			"num_rows"	=> $this->cari->count_results($id_prodi, $keyword),
			"page"		=> $page,
			"limit"		=> $limit,
			"item"		=> $item,
			"order"		=> $order,
			"id_prodi"	=> $id_prodi,
			"warning"	=> "nama",
		);
		
		$this->grid->init($grid_conf);
		$offset = $this->grid->offset();
		$this->grid->source($this->cari->get_results($keyword, $offset, $limit, $item, $order, $id_prodi));
		
		$actions = array
		(
			"print_single" 	=> FALSE, //array("show" => TRUE, "title" => "Print", "icon" => "printer.gif"),
			"edit"		=> array("show" => FALSE),
			"delete"	=> array("show" => FALSE),
		);
		
		$this->grid->add_actions($actions);
		
		$data = array
		(
			"keyword"	=> strtoupper($keyword),
			"add"		=> $this->page->base_url("/add"),
			"multi_del"	=> $this->page->base_url("/multi_del"),
			"grid"		=> $this->grid->draw(),
			"page_link"	=> $this->grid->page_link(),
			"import"	=> $this->page->base_url("/import"),
			"excel"		=> $this->page->base_url("/xls"),
			"pdf"		=> $this->page->base_url("/pdf"),
		);
		//echo $this->db->last_query();
		$this->page->css(array("grid"));
		$this->page->javascript(array("grid"));
		$this->page->data($data);
		$this->page->content("results/cari");
		$this->page->view();
	}
	
	function profil($id = "")
	{
		$this->load->model("mahasiswa_model", "mhs");
		
			
		if ($id == 0)
		{
			$id = $this->_id_mhs;
			$nim=$this->mhs->get_nim($id);
		}
		//$databayar=$this->keuangan->get_bank($nim,$id);
		$data = array
		(
			"id"		=> $id,
			"goto"		=> $this->page->base_url("/show/$id"),
			"cek"		=> site_url("data/mahasiswa/cek_feeder/$id"),
			
			"profil"	=> $this->mhs->get_profile($id),
		
		);
	  
		$this->page->data($data);
		$this->page->content("profiles/mahasiswa_min");
		$this->page->view();
	}
}

/* End of file cari.php ver 3.0 */ 
/* Location: ./application/controllers/cari.php */