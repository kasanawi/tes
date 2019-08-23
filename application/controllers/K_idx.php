<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class k_idx extends CI_Controller{
	function __construct()
	{	
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->library('session');
		$this->load->library('lib_tables');
		$this->load->library('lib_javidol');
		$this->load->library('table');
		$this->load->model('mod_operasiberkas');
		//$this->load->model('mod_javidol');
		//$this->load->model('modules');
		//$this->load->model('mod_capjay');
		//$this->load->model('mod_count');
		$this->load->helper('captcha');
		$this->load->helper('url');
		$this->load->library('calendar');
		$this->load->library('lib_editor');
		$this->page->set_base_url(site_url("/utama"));
		if ($this->form_validation->run() == FALSE)
		{
			//$this->load->view('myform');
		}
		else
		{
			//$this->load->view('formsuccess');
		}
	}
	function index($page = 1, $limit = 9, $item = "tahunterbit", $order = "desc", $pesan='')
	{	
		//$this->session->set_userdata('usrid','asas');
		//$this->load->model('modules');
		$this->page->menu("", "k_idx");
		$data['sistemgrant']=$pesan;
		if ($pesan=99) $data['sistemgrant']='Username dan password salah';
		$data['sistem']='cari';
		$data['halamanaktif']=1;
		$data['griyo']='active';
		$data['daftarjurnal']=$this->mod_operasiberkas->daftar(10,0);
		$data['daftarindek']=$this->mod_operasiberkas->daftar_indek();
		$data['max_pg']=$this->mod_operasiberkas->get_total_hal();
		
		$this->form_validation->set_rules('username', 'username', 'required');
		$this->form_validation->set_rules('password', 'password', 'required');
		//new style
		$hal=$this->kampung_halaman(1);
		$kartu=$this->kampung_kartu(1);
		
		
		$data_paper_slider=$this->mod_operasiberkas->daftar_galeri_slider_depan($hal[1],$hal[0]);	
		
		$this->load->library("grid");
		$grid_conf = array
		(
			"base_url"	=> $this->page->base_url("/index"),
			"act_url"	=> $this->page->base_url(""),
			//"items"		=> $this->mod_operasiberkas->items(),
			"num_rows"	=> $this->mod_operasiberkas->count_cerita(),
			"page"		=> $page,
			"limit"		=> $limit,
			"item"		=> $item,
			"order"		=> $order,
			"id_prodi"	=> 0,
			"warning"	=> "nama",
			"form_act"	=> $this->page->base_url("/set_pulang"),
			"checkbox"	=> FALSE,
		);
		$this->grid->init($grid_conf);
		$offset = $this->grid->offset();
		$data_paper=$this->mod_operasiberkas->daftar_paper_pages($limit,$offset,$item, $order);
		$this->grid->source($data_paper);
		$data = array(
			"action_cari"		=> $this->page->base_url("/nemu/$page/$limit/$item/$order"), 
			"page_link"		=> $this->grid->page_link(),
			);
		$data['daftar_paper']=$data_paper->result();
		$data['karusel_paper']=$data_paper_slider->result();
		$data['dino']=$this->mod_operasiberkas->dino_nasional();
		$data['bulan']=$this->mod_operasiberkas->bulan_nasional();
		//$this->load->view('ngajeng_pager', $data);$this->laporan->get_jadwal($tabel, $id_lab, $offset, $limit, $item, $order,$filter_prodi)
		$data["action"]	= $this->page->base_url("/baca_cerita");//
		
		
		
		$this->page->data($data);
		$this->page->content("default/ngajeng_pager3");
		$this->page->template("ngajeng_pager");
		$this->page->view();
	}
	
	function javidol()
	{
		$data['user']=$this->input->post('jav_name', TRUE);
		$data['pass']=$this->input->post('jav_pass', TRUE);
		//echo $data['user'];
		//echo $data['pass'];
		//echo $this->mod_javidol->jav_cek($data['user'], $data['pass']);
		if ($this->mod_javidol->jav_cek($data['user'], $data['pass'])==TRUE)
		{
			// nama ersebut ada kanjutkan ke pembuatan sesi
			$tongpotong=$this->mod_javidol->jav_artis($data['user'], $data['pass']);
			$this->session->set_userdata($tongpotong);
			//echo $this->session->userdata('member_id');
			$data['sistem']='okehokehwae';
            		$data['sistemgrant']='tamutakdiundangselamatdatangdisistemkami';
			// get informasi pelanggan
			//$this->periksa_sesi();
			redirect('/k_idx/anggota');
			//echo 'x';    
			
		}
		else
		{	
			$tongpotong=$this->mod_javidol->jav_bb17();
			$this->session->set_userdata($tongpotong);
			//echo "oh, tamu toh";
			$data['sistem']='okehokehwae';
            		$data['sistemgrant']='99';
			redirect('/k_idx/nuwun_sewu/'.$data['sistemgrant']);
   
		}
		
	}
	function topmenus($menus='1',$page='1',$kode='')
	{	//$this->session->set_userdata('usrid','asas');
	    	$this->load->library('lib_tables');
		$this->mod_count->increment(); // cache url
		$data['sistem']=$menus;
		$data['pemakai']="user";
		$data['pilihan']="0";
		$data['halamanaktif']=$page;
		$data['max_pg']=$this->mod_operasiberkas->get_total_hal();
		$data['lastque']='';
		$this->load->model('modules');
		$this->form_validation->set_rules('username', 'username', 'required');
		$this->form_validation->set_rules('password', 'password', 'required');
		switch($menus){
			case "arepmlebu";
				//$hal=$this->kampung_halaman($page);
				//$data['daftarjurnal']=$this->mod_operasiberkas->daftar($hal[1],$hal[0]);
				
				$this->load->view('template', $data);
			break;
			case "jurnal";
				$hal=$this->kampung_halaman($page);
				$data['kodepencari']='';
				if ($this->session->userdata('nama2')!='') $data['daftarjurnal']=$this->mod_operasiberkas->daftar_c($hal[1],$hal[0]);
				if ($this->session->userdata('nama2')=='') $data['daftarjurnal']=$this->mod_operasiberkas->daftar($hal[1],$hal[0]);	
				$data['lastque'] = $this->db->last_query();	
				$jav_artis['lastque'] = $this->db->last_query();
				$this->session->set_userdata($jav_artis);
				//$data['daftarjurnal']=$this->mod_operasiberkas->daftar($hal[1],$hal[0]);
				$this->load->view('template', $data);
			break;
			case "urutkan_golongan";
				$hal=$this->kampung_halaman($page);
				$data['max_pg']=$this->mod_operasiberkas->ayo_goleki_maxpage($hal[1],$hal[0],$kode);
				$data['kodepencari']=$kode;
				if ($this->session->userdata('nama2')!='') $data['daftarjurnal']=$this->mod_operasiberkas->daftar_cat($hal[1],$hal[0],$kode);
				if ($this->session->userdata('nama2')=='') $data['daftarjurnal']=$this->mod_operasiberkas->daftar_cat($hal[1],$hal[0],$kode);	
				//$data['daftarjurnal']=$this->mod_operasiberkas->daftar($hal[1],$hal[0]);
				$data['lastque'] = $this->db->last_query();
				$jav_artis['lastque'] = $this->db->last_query();
				$this->session->set_userdata($jav_artis);
				$this->load->view('template', $data);
			break;

			case "main";
				$hal=$this->kampung_halaman($page);
				$data['max_pg']=$this->mod_operasiberkas->ayo_goleki_maxpage($hal[1],$hal[0],$kode);
				$data['kodepencari']=$kode;
				if ($this->session->userdata('nama2')!='') $data['daftarjurnal']=$this->mod_operasiberkas->daftar_cat_group($hal[1],$hal[0],$kode);
				if ($this->session->userdata('nama2')=='') $data['daftarjurnal']=$this->mod_operasiberkas->daftar_cat_group($hal[1],$hal[0],$kode);	
				//$data['daftarjurnal']=$this->mod_operasiberkas->daftar($hal[1],$hal[0]);
				//echo $this->db->last_query();
				$data['max_pg']=$this->mod_operasiberkas->get_total_hal_main();

				$data['lastque'] = $this->db->last_query();
				$jav_artis['lastque'] = $this->db->last_query();
				$this->session->set_userdata($jav_artis);
				$this->load->view('template', $data);
			break;

			case "paketlist";
				//$data['sistem']='acc_list';
				//$data['pemakai']="user";
				//$data['pilihan']="0";
				//$data['halamanaktif']=$page;
				//$data['max_pg']=$this->mod_operasiberkas->get_total_hal();

				$hal=$this->kampung_halaman($page);
				$data['daftarjurnal']=$this->mod_operasiberkas->daftar_paket($hal[1],$hal[0]);
				$this->load->view('ngijini', $data);

			break;

			case "konpirmlist";
				//$data['sistem']='acc_list';
				//$data['pemakai']="user";
				//$data['pilihan']="0";
				//$data['halamanaktif']=$page;
				//$data['max_pg']=$this->mod_operasiberkas->get_total_hal();

				$hal=$this->kampung_halaman($page);
				$data['daftarjurnal']=$this->mod_operasiberkas->daftar_konpirm($hal[1],$hal[0]);
				$this->load->view('ngijini', $data);

			break;
			case "acclist";
				//$data['sistem']='acc_list';
				//$data['pemakai']="user";
				//$data['pilihan']="0";
				//$data['halamanaktif']=$page;
				//$data['max_pg']=$this->mod_operasiberkas->get_total_hal();

				$hal=$this->kampung_halaman($page);
				$data['daftarjurnal']=$this->mod_operasiberkas->daftar_acc($hal[1],$hal[0]);
				$this->load->view('ngijini', $data);

			break;

			case "buletin";
				$hal=$this->kampung_halaman($page);
				//$data['daftarjurnal']=$this->mod_operasiberkas->daftar($hal[1],$hal[0]);
				$this->load->view('template', $data);
			break;
			case "daftar";
				$hal=$this->kampung_halaman($page);
				//$data['daftarjurnal']=$this->mod_operasiberkas->daftar($hal[1],$hal[0]);
				$this->load->view('teken', $data);
			break;
			case "bayar";
				//$data['daftarjurnal']=$this->mod_operasiberkas->daftar($hal[1],$hal[0]);
				$data['daftarkategori']=$this->mod_operasiberkas->daftar_b(30,0);
				$this->load->view('template', $data);
			break;
			case "pengguna";
				$hal=$this->kampung_halaman($page);
				$data['daftarjurnal']=$this->mod_operasiberkas->daftar_anggauta(1000,$hal[0]);
				$data['pengwasa']=0;
				$this->load->view('pedamel', $data);
			break;
			case "penguasa";
				
				$hal=$this->kampung_halaman($page);
				$data['daftarjurnal']=$this->mod_operasiberkas->daftar_penguwasa($hal[1],$hal[0]);
				$data['pengwasa']=1;
				$this->load->view('pedamel', $data);

			break;


			case "unggahjurnal";
				$hal=$this->kampung_halaman($page);
				//$data['daftarjurnal']=$this->mod_operasiberkas->daftar($hal[1],$hal[0]);
				if ($this->session->userdata('nama2')!='') $data['daftarjurnal']=$this->mod_operasiberkas->daftar_c($hal[1],$hal[0]);
				if ($this->session->userdata('nama2')=='') $data['daftarjurnal']=$this->mod_operasiberkas->daftar($hal[1],$hal[0]);
			        $data['daftarkategori']=$this->mod_operasiberkas->daftar_b(130,0);
				$data['pilihankategori']=$this->input->post('id_kelas');
				if ($this->input->post('id_kelas')!='')
				{
					
				};
				$this->load->view('template', $data);
			break;
			case "editjurnal";
				$hal=$this->kampung_halaman($page);
				$data['daftarjurnal']=$this->mod_operasiberkas->daftar($hal[1],$hal[0]);
			       $data['daftarkategori']=$this->mod_operasiberkas->daftar_b(30,0);
				$data['pilihankategori']=$this->input->post('id_kelas');
				if ($this->input->post('id_kelas')!='')
				{
					
				};
				$this->load->view('template', $data);
			break;
			case "cari";
				$data['daftarjurnal']=$this->mod_operasiberkas->daftar(3,0);
				$jav_artis['lastque'] = $this->db->last_query();
				$this->session->set_userdata($jav_artis);
				$data['daftarindek']=$this->mod_operasiberkas->daftar_indek();
				$hal=$this->kampung_halaman($page);
				if ($hal[0]<0) $hal[0]=0;
				//$data['daftarjurnal']=$this->mod_operasiberkas->daftar($hal[1],$hal[0]);
				$this->load->view('template', $data);
			break;
			case "info";
				$data['daftarjurnal']=$this->mod_operasiberkas->daftar(3,0);
				$hal=$this->kampung_halaman($page);
				if ($hal[0]<0) $hal[0]=0;
				//$data['daftarjurnal']=$this->mod_operasiberkas->daftar($hal[1],$hal[0]);
				$this->load->view('template', $data);
			break;
			case "capcay";
				$this->capcay();
				echo "stop disini";
			break;
			case "recet_password";
				//$this->capcay();
				$this->load->view('template', $data);
			break;
			case "spc_ordered";
				$hal=$this->kampung_halaman($page);
				$data['max_pg']=$this->mod_operasiberkas->ayo_goleki_maxpage($hal[1],$hal[0],$kode);
				if ($hal[0]<0) $hal[0]=0;
				if (is_numeric($kode)) 
				{
				if ($this->session->userdata('nama2')!='') $data['daftarjurnal']=$this->mod_operasiberkas->urutan_tahun_c($hal[1],$hal[0],$kode);
				if ($this->session->userdata('nama2')=='') $data['daftarjurnal']=$this->mod_operasiberkas->urutan_tahun($hal[1],$hal[0],$kode);	
				//$data['daftarjurnal']=$this->mod_operasiberkas->urutan_tahun($hal[1],$hal[0],$kode);
				}else{
					$kode=$this->mod_operasiberkas->dots_elims($kode);
				if ($this->session->userdata('nama2')!='') $data['daftarjurnal']=$this->mod_operasiberkas->urutan_penulis_c($hal[1],$hal[0],$kode);
				if ($this->session->userdata('nama2')=='') $data['daftarjurnal']=$this->mod_operasiberkas->urutan_penulis($hal[1],$hal[0],$kode);	
				//$data['daftarjurnal']=$this->mod_operasiberkas->urutan_penulis($hal[1],$hal[0],$kode);
				}
				$jav_artis['lastque'] = $this->db->last_query();
				$this->session->set_userdata($jav_artis);
				$data['namapenulis']=$kode;
				$this->load->view('template', $data);
			break;
			case "1";
				$hal=$this->kampung_halaman($page);
				$data['daftarjurnal']=$this->mod_operasiberkas->daftar($hal[1],$hal[0]);
				$this->load->view('template', $data);
			break ;
			case "pesan";
				$this->load->view('template', $data);
			break ;
			case "export";
				if ($this->session->userdata('nama2')!='') $data['daftarjurnal']=$this->mod_operasiberkas->pra_export($this->session->userdata('lastque'));
				$this->load->view('fexprot', $data);
			break;
			
			}
		
		
	}
	function kampung_halaman($halaman)
	{	$halaman=$halaman-1;
		$hasil[0]=$halaman*10;;
		$hasil[1]=10;
		$data['halamanaktif']=$halaman;
		return $hasil;
	}
	
	function kampung_kartu($halaman)
	{	$halaman=$halaman-1;
		$hasil[0]=$halaman*15;;
		$hasil[1]=15;
		$data['halamanaktif']=$halaman;
		return $hasil;
	}
	
	function kampung_paper($halaman)
	{	$halaman=$halaman-1;
		$hasil[0]=$halaman*5;;
		$hasil[1]=5;
		$data['halamanaktif']=$halaman;
		return $hasil;
	}

	function uplot($edit=1)
	{	
		if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		//echo $edit;
		$data['sistem']="jurnal";
		$hal=$this->kampung_halaman(1);
		$data['daftarjurnal']=$this->mod_operasiberkas->daftar_c($hal[1],$hal[0]);
		if ($this->input->post('upload')) {
			$filename = $this->mod_operasiberkas->renameMD5($_FILES['userfile']['name']);	// $_FILES['userfile']['name'];
			$filename2 =$this->mod_operasiberkas->renameMD5($_FILES['userfile2']['name']);	 // $_FILES['userfile2']['name'];
			$data2['link']='';
			$data2['linkb']='';
			if ($filename2!='')	
			{$hasil=md5($filename2);
			$panjangnama=strlen($filename2);
			$filename2=substr($hasil,0,32).substr($filename2,$panjangnama-4,4);
			}
			$this->mod_operasiberkas->do_upload($filename2,$filename);
			$data2['judul']=$this->input->post('judul');
			$data2['judullama']=$this->input->post('judullama');
			$data2['penulis']=$this->input->post('penulis');
			$data2['files']=str_replace(' ','_',$filename);
			$data2['offlink']=$this->input->post('offlink');
			
			if ($this->input->post('linklama')=='')
			if ($_FILES['userfile']['name']!='') $data2['link']= base_url().'berkas/'.str_replace(' ','_',$filename);
			if ($this->input->post('linklama2')=='')
			if ($_FILES['userfile2']['name']!='') $data2['linkb']= base_url().'dnps5d/'.str_replace(' ','_',$filename2);
			
			if(!file_exists($_FILES['userfile']['tmp_name']) || !is_uploaded_file($_FILES['userfile']['tmp_name'])) {
   				 echo 'No upload for #1';
				 $data2['link']= $this->input->post('linklama');

			}
			if(!file_exists($_FILES['userfile2']['tmp_name']) || !is_uploaded_file($_FILES['userfile2']['tmp_name'])) {
   				 echo 'No upload for #2';
				 $data2['linkb']= $this->input->post('linklama2');
			}
			$data2['kategori']=$this->input->post('Kategori');
			$data2['deskripsi']=$this->input->post('Deskripsi');
			$data2['tahunterbit']=$this->input->post('tterbit');
			if ($edit==1)
				{
				$this->mod_operasiberkas->do_simpan($data2);
				}
			if ($edit==2)
				{
				$this->mod_operasiberkas->do_simpan_edit($data2);
				}
			//echo $_FILES['userfile2']['name'];
			//echo $_FILES['userfile']['name'];
			$data['daftarjurnal']=$this->mod_operasiberkas->daftar_last_rec($hal[1],$hal[0]);
			$data['halamanaktif']=1;
		}
		else{
			$data['pilihankategori']=$this->input->post('id_kelas');
			$data['sistem']="unggahjurnal";
			$data['daftarkategori']=$this->mod_operasiberkas->daftar_b(30,0);
			}
		 $data['daftarkategori']=$this->mod_operasiberkas->daftar_b(30,0);
		$data['pilihankategori']=$this->input->post('id_kelas');
		$data['sistem']="unggahjurnal";	
		$this->load->view('template', $data);
	}
	
	function bulletin()
	{	$data['sistem']="buletin";
		$hal=$this->kampung_halaman(1);
		$data['daftarjurnal']=$this->mod_operasiberkas->daftar_b($hal[1],$hal[0]);
		if ($this->input->post('upbul')) {
			//$this->mod_operasiberkas->do_upload();
			$data2['judul']=$this->input->post('judul');
			$data2['jhalaman']=$this->input->post('jhalaman');
			$data2['bulan']=$this->input->post('bulan');
			$data2['tahun']=$this->input->post('tahun');
			$data2['ISSN']=$this->input->post('ISSN');
			if ($this->input->post('upbul')=='Simpan')
			$this->mod_operasiberkas->do_simpan_b($data2);
			else 
			{
				$data2['kodebul']=$this->input->post('kodebul');
				$this->mod_operasiberkas->do_edit_b($data2);
			}
			//$this->input->post('upload')='';
		
		}
		else{
			}
		$this->form_validation->set_rules('filename', 'filename', 'required');
		$this->form_validation->set_rules('deskripsi', 'deskripsi', 'required');
		$this->load->view('template', $data);
	}
	function anggota($tanggal='',$page=1)
	{	
		$data['jam_server']="buletin";
		$data['sistem']="buletin";
		$hal=$this->kampung_paper($page);
		
		$data['halamanaktif']=$page;
		$data['tanggal']=site_url($this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->uri->segment(3));;
		if ($tanggal=='') 
			{ 	$tanggal=date("Y-m-d");
				$data['tanggal']=site_url($this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$tanggal);;

			}
		$data['dino']=$tanggal;

		//get data anggota
		$member_id=$this->session->userdata('member_id');
		$_langganan=$this->session->userdata('lagakmu'); 
		$_paket_aktiv=$this->mod_operasiberkas->paket($member_id,1);
		$_paket_pasif=$this->mod_operasiberkas->paket($member_id,0);
		$_paket_berlaku=$this->mod_operasiberkas->paket($member_id,0);
		$_terbaru=$this->mod_operasiberkas->daftar_paper($hal[1],$hal[0]);	
		$template = array(
        		'table_open'=> '<table class="table table-striped table-bordered table-hover" id="dataTables-payment"  border="0" cellpadding="4" cellspacing="0">'
		);
		$this->table->set_template($template);

		$this->table->set_heading('ID', 'Nama', 'Token','Jumlah','No Rekening','Nama Pemilik Rekening','Kode Paket','Durasi Paket','Tanggal Order','Status Konfirmasi','Tanggal Konfirmasi');
			$data['tabel_paket']=$this->table->generate($_paket_pasif);;
		$this->table->set_heading('ID', 'Nama', 'Token','Jumlah','No Rekening','Nama Pemilik Rekening','Kode Paket','Durasi Paket (hari)','Tanggal bayar','Terkonfirmasi','Tanggal Konfirmasi', 'Berlaku sampai dengan');
			$data['tabel_baca']=$this->table->generate($_paket_aktiv);;
		
		
		$data['tabel_paper']=$_terbaru;
		
		//new style
		$data_paper=$this->mod_operasiberkas->daftar_paper_day($hal[1],$hal[0],$tanggal);
		$data_paper_thmb=$this->mod_operasiberkas->daftar_paper_thmb($hal[1],$hal[0],$tanggal);
		$data_paper_slider=$this->mod_operasiberkas->daftar_paper_slider(7,0);
	
		//$data_paper=$this->mod_operasiberkas->daftar_paper_day($hal[1],$hal],$tanggal);
		//$data_paper_thmb=$this->mod_operasiberkas->daftar_paper_thmb($hal[1],$hal[0],$tanggal);
		//$data_paper_slider=$this->mod_operasiberkas->daftar_paper_slider($hal[1],$hal[0]);	
		

		$data['daftar_paper']=$data_paper->result();
		$data['daftar_paper_thmb']=$data_paper_thmb->result();
		$data['karusel_paper']=$data_paper_slider->result();
		$data['daftar_paper_slider']=$data_paper_slider->result();
		$data['daftar_paper_slider2']=$data_paper_slider;

		$data['menu_terbitan']=$this->lib_tables->slider_paper($data_paper->result_array());
		$this->lib_tables->slider_paper($data_paper->result_array());
		$template = array(
        		'table_open'=> '<table class="table table-striped table-bordered table-hover" id="dataTables-baca"  border="0" cellpadding="4" cellspacing="0">'
		);
	
		//$this->table->set_heading('No','Edisi','Jumlah Halaman','bulan','tahun','ISSN','reserved');
		$this->table->set_heading('No','Edisi','Halaman');

		$data['tabel_jurnal']=$this->table->generate($data_paper);;
		//get data terbitan
		//echo $this->session->userdata('embongan');
		if ($this->session->userdata('member_id')>0) { 
			if ($this->session->userdata('embongan')==0)
				{
					$this->load->view('keluarga', $data);
				} else
				{
					redirect('k_daftar/pilih/'.$this->session->userdata('embongan'));

				}
		 } else redirect('k_idx');

		//echo 'ilegal akses';
	}

	function anggota_baca($tanggal,$page=1)
	{
		$data['sistem']="buletin";
		$hal=$this->kampung_paper($page);
		
		$data['halamanaktif']=$page;
		$data['tanggal']=site_url($this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->uri->segment(3));;
		$data['dino']=$tanggal;
		//get data anggota
		$member_id=$this->session->userdata('member_id');
		$_langganan=$this->session->userdata('lagakmu'); 
		//$_paket_aktiv=$this->mod_operasiberkas->paket($member_id,1);
		//$_paket_pasif=$this->mod_operasiberkas->paket($member_id,0);
		//$_terbaru=$this->mod_operasiberkas->daftar_paper($hal[1],$hal[0]);	
	
		$data_paper=$this->mod_operasiberkas->daftar_paper_day($hal[1],$hal[0],$tanggal);
		$data_paper_thmb=$this->mod_operasiberkas->daftar_paper_thmb($hal[1],$hal[0],$tanggal);
		$data_paper_slider=$this->mod_operasiberkas->daftar_paper_slider_depan(40,0);	

		$data['daftar_paper']=$data_paper->result();
		$data['daftar_paper_thmb']=$data_paper_thmb->result();

		if ($this->session->userdata('embongan')==0)
				{
					$this->load->view('maos', $data);
				} else
				{
					redirect('k_daftar/pilih/'.$this->session->userdata('embongan'));

				}

	}
	function anggota_arsip($page_day=0,$_page=1,$tanggal='ALL')
	{
		//echo $tanggal;
		//echo $this->input->post('bday');
		if ($tanggal=='ALL') 
			{ 
			$tanggal=date("Y-m-d");
			$tanggal=$this->input->post('bday');
			}
		//echo $tanggal;
		$date = new DateTime($tanggal);
		$date->sub(new DateInterval('P'.$page_day.'D'));
		$tanggal= $date->format('Y-m-d') . "\n";//echo $tanggal;
		$data['sistem']="buletin";//echo "aa";
		$hal=$this->kampung_halaman($page_day);
		$_hal=$this->kampung_paper($_page);

		$data['halamanaktif']=$page_day;
		$data['_halamanaktif']=$_page;
		//$data['tanggal']=site_url($this->uri->segment(1).'/'.$this->uri->segment(2).'/');;
		$data['tanggal']=$tanggal;

		//get data anggota
		$member_id=$this->session->userdata('member_id');
		$_langganan=$this->session->userdata('lagakmu'); 
		//$_paket_aktiv=$this->mod_operasiberkas->paket($member_id,1);
		//$_paket_pasif=$this->mod_operasiberkas->paket($member_id,0);
		//$_terbaru=$this->mod_operasiberkas->daftar_paper($hal[1],$hal[0]);	
	
		$data_paper=$this->mod_operasiberkas->daftar_paper_day(40,0,$tanggal);
		$data_paper_thmb=$this->mod_operasiberkas->daftar_paper_thmb(40,0,$tanggal);

			// print_r($data_paper->result());

		$data['daftar_paper']=$data_paper->result();
		$data['daftar_paper_thmb']=$data_paper_thmb->result();

		if ($this->session->userdata('embongan')==0)
				{
					$this->load->view('arsip', $data);
				} else
				{
					redirect('k_daftar/pilih/'.$this->session->userdata('embongan'));

				}

	}
	function urutkan_penulis($kode,$page='1')
	{	$data['sistem']="spc_ordered";
		$hal=$this->kampung_halaman($page);
		$data['max_pg']=$this->mod_operasiberkas->ayo_goleki_maxpage($hal[1],$hal[0],$kode);
		//$data['daftarjurnal']=$this->mod_operasiberkas->urutan_penulis($hal[1],$hal[0],$kode);					
		if ($this->session->userdata('nama2')!='') $data['daftarjurnal']=$this->mod_operasiberkas->urutan_penulis_c($hal[1],$hal[0],$kode);
		if ($this->session->userdata('nama2')=='') $data['daftarjurnal']=$this->mod_operasiberkas->urutan_penulis($hal[1],$hal[0],$kode);
		$data['title']='JurnalEdit';
		$data['otentifik']='user';
		$data['jenis']='penulis';
		$data['halamanaktif']=1;
		//$data['menukiri']=$this->mod_leftmenu->daftar(6,0);	
		$this->load->view('template', $data);
	}
	function urutkan_tahun($kode,$page='1')
	{
		$data['namapenulis']=$kode;
		$data['sistem']="spc_ordered";
		$hal=$this->kampung_halaman($page);
		$data['max_pg']=$this->mod_operasiberkas->ayo_goleki_maxpage($hal[1],$hal[0],$kode);
		//$data['daftarjurnal']=$this->mod_operasiberkas->urutan_penulis($hal[1],$hal[0],$kode);		
		//$data['daftarjurnal']=$this->mod_operasiberkas->urutan_tahun(16,0,$kode);
		if ($this->session->userdata('nama2')!='') $data['daftarjurnal']=$this->mod_operasiberkas->urutan_tahun_c($hal[1],$hal[0],$kode);
		if ($this->session->userdata('nama2')=='') $data['daftarjurnal']=$this->mod_operasiberkas->urutan_tahun($hal[1],$hal[0],$kode);
		$data['title']='JurnalEdit';
		$data['otentifik']='user';
		$data['jenis']='tahun';
		$data['halamanaktif']=1;
		//$data['menukiri']=$this->mod_leftmenu->daftar(6,0);
		
		$this->load->view('template', $data);
	}
	
	function urutkan_golongan($menus='1',$kode='',$page='1')
	{
		$data['namapenulis']=$kode;
		$data['sistem']="urutkangolongan";
		$hal=$this->kampung_halaman($page);
		$data['max_pg']=$this->mod_operasiberkas->ayo_goleki_maxpage($hal[1],$hal[0],$kode);
		//$data['daftarjurnal']=$this->mod_operasiberkas->urutan_penulis($hal[1],$hal[0],$kode);		
		//$data['daftarjurnal']=$this->mod_operasiberkas->urutan_tahun(16,0,$kode);
		if ($this->session->userdata('nama2')!='') $data['daftarjurnal']=$this->mod_operasiberkas->daftar_cat($hal[1],$hal[0],$kode);
		if ($this->session->userdata('nama2')=='') $data['daftarjurnal']=$this->mod_operasiberkas->daftar_cat($hal[1],$hal[0],$kode);
		$data['title']='JurnalEdit';
		$data['otentifik']='user';
		$data['jenis']='tahun';
		$data['halamanaktif']=1;
		//$data['menukiri']=$this->mod_leftmenu->daftar(6,0);
		
		$this->load->view('template', $data);
	}
	function busekae($kode,$page='1')
	{
		$this->load->model('mod_operasiberkas');
		$data['sistem']="pesan";
		$data['halamanaktif']=1;
		$hal=$this->kampung_halaman($page);
		$data['daftarjurnal']=$this->mod_operasiberkas->urutan_penulis($hal[1],$hal[0],$kode);					
		$data['title']='JurnalEdit';
		$data['otentifik']='user';
		$data['hapusjurnal']=$this->mod_operasiberkas->disetip($kode);
		$data['jenis']='Jurnal';
		$data['pesankesan']='Jurnal Telah dihapus';
		$data['curlink']=base_url().'index.php/k_idx/';
		$this->form_validation->set_rules('username', 'username', 'required');
		$this->form_validation->set_rules('password', 'password', 'required');
		$this->load->view('template', $data);
	}
	function siswa()
	{   $data['sistem']='unggahjurnal';//$this->input->post('id_kelas');
	    $data['daftarkategori']=$this->mod_operasiberkas->daftar_b(30,0);
		$data['pilihan']=$this->input->post('id_kelas');
		$id = $this->input->post('id_kelas');
		//$data['siswa'] = $this->mod_operasiberkas->daftar_b($id,0);
        $this->load->view('siswa',$data);
	}
	
	function get_md5_32b($string)
	{
		$hasil=md5($string);
		$hasil=substr($hasil,0,32);
		return $hasil;
	}
	
	function capcay($token='DCB8FE4F77042D40D5C9394F4ABF5F73')
	{	echo "<pre>";
		
		
		$masukan=$this->input->post();
		echo $masukan['password'];
		
		if ($masukan['password']!=$masukan['password2']) 
		{
			$redirect = base_url().'index.php/k_idx/teken/password_salah';
			redirect($redirect);
		}
		
		if ($masukan['email']!=$masukan['email2']) 
		{
			$redirect = base_url().'index.php/k_idx/teken/email_salah';
			redirect($redirect);
		}
		//cek apakah sudah punya akun atau nama sama
		$cek=$this->mod_operasiberkas->periksa_akun($masukan['username'],$masukan['email']);
		if($cek===false)
		{
			$redirect = base_url().'index.php/k_idx/teken/akun_terdaftar';
			redirect($redirect);
		}
		if($this->session->userdata('martabak')!=md5($this->input->post('martabak', TRUE)))
														{
															$data['sistem']=$this->session->userdata('martabak').'='.$this->input->post('martabak', TRUE);
															echo "proses regitrasi gagal'";
															$this->load->view('blank', $data);
														}
		else
		{
			
			$mdx_user=array(	
					'username' => $masukan['username'],
					'password' => md5($masukan['password']),
					//'password2' => 123
					'email' =>  $masukan['email2'],
					'kd_user_group'	=> 'okb',
					'unmasked'=> $masukan['password'],
					'id_biodata' => '',
					);
			$mdx_pembaca=array(
					'id' => $masukan['id'], 
					'No_RM' => '',
					'Nik' =>  $masukan['Nik'] , 
					'Nama_Pembaca' => $masukan['Nama_Pembaca'], 
					'Alamat' => $masukan['Alamat'] , 
					'rt' => $masukan['rt'] , 
					'rw' => $masukan['rw'], 
					'kelurahan' => $masukan['kelurahan'], 
					'provinsi' => '', 
					'kota' => '', 
					'kecamatan' =>  $masukan['kecamatan'], 
					'Status_nikah' => $masukan['Status_nikah'] ,
					'Gol_darah' => '-', 
					'NoTLP' => $masukan['NoTLP'], 
					'Tgl_lahir' => $masukan['Tgl_lahir'],
					'Tempat_Lahir' => $masukan['Tempat_Lahir'], 
					'Tgl_daftar' => $masukan['Tgl_daftar'],
					'Agama' => 0, 
					'Ket_Pasien' => $masukan['Ket_Pembaca'] ,
					'Jenis_pasien'	=> 'pembaca',
					'id_pendidikan' => $masukan['id_pendidikan'], 
					'id_pekerjaan' => $masukan['id_pekerjaan'], 
					'jenis_kelamin' => $masukan['jenis_kelamin'],
				);			
					
			print_r($mdx_pembaca);print_r($mdx_user);
			$data['sistem']=$this->input->post('martabak', TRUE);
			
			$data['halamanaktif']= $this->mod_operasiberkas->registrasi_member($mdx_user,$mdx_pembaca);
		}
		//echo $this->input->post('capcay', TRUE);
		
		$data['sistem']='Registrasi berhasil';
		$data['pesankesan']="Selamat anda telah berhasil melakukan registrasi di Paper Malang Post<p> Silakan <a href ='".base_url()."/index.php/k_idx/topmenus/arepmlebu'>LOGIN</a> dengan ID anda";
		$this->load->view('blank', $data);
	}
	function nemu($page = 1, $limit = 9, $item = "tahunterbit", $order = "desc", $pesan='')
	{
		//echo $_
		$urut = $this->input->post('urutan');
		$cari = $this->input->post('cari');
		
		switch ($urut) {
			case 101:
				$item = "tahunterbit";$order = "desc";
				break;
			case 101:
				$item = "tahunterbit";$order = "asc";
				break;
			case 102:
				$item = "pelihat";$order = "desc";
				break;
			case 120:
				$item = "pelihat";$order = "asc";
				break;
			case 103:
				$item = "judul";$order = "asc";
				break;
			case 130:
				$item = "judul";$order = "desc";
				break;
			default:
				$item = "tahunterbit";$order = "desc";
		}
		$this->goleki($page, $limit, $item, $order, $cari);
		//$this->page->base_url("/goleki/$page/$limit/$item/$order");
	}		
	
	function goleki($page = 1, $limit = 9, $item = "tahunterbit", $order = "desc", $pesan='')
	{	
		//$this->session->set_userdata('usrid','asas');
		//$this->load->model('modules');
		$this->page->menu("", "k_idx");
		
		$data['sistem']='cari';
		
		$hal=$this->kampung_halaman(1);
		$kartu=$this->kampung_kartu(1);
		
		
		$data_paper_slider=$this->mod_operasiberkas->daftar_galeri_slider_depan($hal[1],$hal[0]);	
		
		$this->load->library("grid");
		$grid_conf = array
		(
			"base_url"	=> $this->page->base_url("/goleki"),
			"act_url"	=> $this->page->base_url(""),
			//"items"		=> $this->mod_operasiberkas->items(),
			"num_rows"	=> $this->mod_operasiberkas->count_cerita(),
			"page"		=> $page,
			"limit"		=> $limit,
			"item"		=> $item,
			"order"		=> $order,
			"id_prodi"	=> 0,
			"warning"	=> "nama",
			"form_act"	=> $this->page->base_url("/set_pulang"),
			"checkbox"	=> FALSE,
		);
		$this->grid->init($grid_conf);
		$offset = $this->grid->offset();
		$data_paper=$this->mod_operasiberkas->daftar_paper_pages($limit,$offset,$item, $order);
		$this->grid->source($data_paper);
		$data = array(
			"action_cari"		=> $this->page->base_url("/nemu/$page/$limit/$item/$order"), 
			"page_link"		=> $this->grid->page_link(),
			);
		$data['daftar_paper']=$data_paper->result();
		$data['karusel_paper']=$data_paper_slider->result();
		$data['dino']=$this->mod_operasiberkas->dino_nasional();
		$data['bulan']=$this->mod_operasiberkas->bulan_nasional();
		//$this->load->view('ngajeng_pager', $data);$this->laporan->get_jadwal($tabel, $id_lab, $offset, $limit, $item, $order,$filter_prodi)
		$data["action"]	= $this->page->base_url("/baca_cerita");//
		
		
		
		$this->page->data($data);
		$this->page->content("default/ngajeng_pager3");
		$this->page->template("ngajeng_pager");
		$this->page->view();
	
	}
	
	function goleki_kunci($kunci,$page='1')
	{	//echo "AAA".$this->db->last_query();
		$data['halamanaktif']=$page;
		$katakunci=$kunci;
		$data['sistem']="spc_ordered";
		$hal=$this->kampung_halaman($page);
		$data['max_pg']=$this->mod_operasiberkas->get_total_hal();
				if ($hal[0]<0) $hal[0]=0;
				//$data['daftarjurnal']=$this->mod_operasiberkas->daftar($hal[1],$hal[0]);
		if ($this->session->userdata('nama2')!='') $data['daftarjurnal']=$this->mod_operasiberkas->ayo_goleki_c($hal[1],$hal[0],$katakunci);
		if ($this->session->userdata('nama2')=='') $data['daftarjurnal']=$this->mod_operasiberkas->ayo_goleki($hal[1],$hal[0],$katakunci);
		//$data['daftarjurnal']=$this->mod_operasiberkas->ayo_goleki($hal[1],$hal[0],$katakunci);
		$data['title']='JurnalEdit';
		$data['otentifik']='user';
		$data['jenis']='Jurnal';
		$data['namapenulis']=$katakunci;
		$data['max_pg']=$this->mod_operasiberkas->ayo_goleki_maxpage($hal[1],$hal[0],$katakunci);
		//$data['menukiri']=$this->mod_leftmenu->daftar(6,0);
		
		$this->load->view('template', $data);
		
	}
    function edit($kode,$page='1')
	{	
		$data['sistem']="editjurnal";
		$data['pemakai']="user";
		$data['halamanaktif']=$page;
		$hal=$this->kampung_halaman($page);
		$data['daftarjurnal']=$this->mod_operasiberkas->daftar($hal[1],$hal[0]);
		$data['editjurnal']=$this->mod_operasiberkas->get_judul($kode,0);
		$data['daftarkategori']=$this->mod_operasiberkas->daftar_b(300,0);
		$this->load->view('template', $data);
	}

	function nuwun_sewu($status = "")
	{
		
		$data = array
		(
			"status" => $status,
			"action" => $this->page->base_url("/check"),
			"kueri"	 => $this->db->last_query(),
			//"masa_aktif" => $this->periode->get_opt_feeder(),
			//"periode" 	=> $this->periode->get_opt(),
			//"aktif"		=> $this->periode->get_aktif()->row(),
			//"semester"	=> $this->semester->get_opt(),
			
		);
		
		
		$this->page->data($data);
		$this->page->template("ngajeng_pager");
		$this->page->content("default/kulonuwun");
		$this->page->view();
		
		
	}
	function logout()
	{
		//$data['user']='oemoem';
		//$tongpotong=$this->mod_javidol->jav_artis($data['user'], $data['pass']);
		//$this->session->unset_userdata($tongpotong);
		$this->session->unset_userdata();
		$this->session->sess_destroy();
		$this->load->model('modules');
		$data['sistemgrant']='tamutakdiundang';
		$data['sistem']='arepmlebu';
		$data['halamanaktif']=1;
		$data['daftarjurnal']=$this->mod_operasiberkas->daftar(10,0);
		$this->form_validation->set_rules('username', 'username', 'required');
		$this->form_validation->set_rules('password', 'password', 'required');
		redirect('/k_idx');

		$this->load->view('wangsul');
	}
	function fjbkaskus()
	{	
		echo $this->input->post('tanggal');
		$data['validasi']=$this->input->post('validasi');
		$data['bayar']= $this->input->post('bayar');
		$data['nodong']= $this->input->post('nodong');
		$data['noduit']=$this->input->post('noduit');
		$data['pilihan']= $this->input->post('Kategori');
		$data['nama']= $this->session->userdata('nama');
		$data['iduser']= $this->session->userdata('nama2');
		$data['tanggal']=$this->input->post('tanggal');
		$data['pesankesan']=$this->mod_operasiberkas->konpirm_bayar($data);
		$data['sistemgrant']='tamutakdiundang';
		$data['sistem']='pesan';
		
		$data['halamanaktif']=1;
		$data['daftarjurnal']=$this->mod_operasiberkas->daftar(10,0);
		$this->load->view('template', $data);
	}
	
	function accbayar($kode,$page='1')
	{	
		$data['sistem']="konpirmlist";
		$data['pemakai']="user";
		$data['halamanaktif']=$page;
		$member_id=$this->session->userdata('member_id');
		$this->mod_operasiberkas->acc_bayar($kode,$member_id);
		$hal=$this->kampung_halaman($page);
		$data['daftarjurnal']=$this->mod_operasiberkas->daftar_konpirm($hal[1],$hal[0]);
		$this->load->view('ngijini', $data);
	}
	function tolakbayar($kode,$page='1')
	{	
		$data['sistem']="konpirmlist";
		$data['pemakai']="user";
		$data['halamanaktif']=$page;
		$this->mod_operasiberkas->tolak_bayar($kode);
		$hal=$this->kampung_halaman($page);
		$data['daftarjurnal']=$this->mod_operasiberkas->daftar_konpirm($hal[1],$hal[0]);
		$this->load->view('ngijini', $data);
	}

	public function baca_cerita($id_cerita,$status="")
	{
		$this->page->menu("diari", "");
		$this->load->model("cerita_model", "cerita");
		$data=array(
			
			'id_pembaca'	=> $this->session->userdata('id_biodata'),
			"aksi_benci"	=> $this->page->base_url("celoteh/benci/$id_cerita"),
			"aksi_suka"		=> $this->page->base_url("celoteh/suka/$id_cerita"),
			"add"			=> $this->page->base_url("/add"),
			"redirect"		=> $this->agent->referrer(),
			"multi_del"		=> $this->page->base_url("/multi_del"),
			"action"		=> $this->page->base_url("celoteh/simpan"),
			"import"		=> $this->page->base_url("/import"),
			//"excel"		=> $this->page->base_url("/xls/$page/$limit/$id_prodi"),
			"pdf"			=> $this->page->base_url("/pdf"),
			"mode"			=> 'read',
			"cerita"		=> $this->cerita->get_cerita($id_cerita),
			"celoteh"		=>	$this->cerita->get_celoteh($id_cerita),
			"cendolbata"	=>  $this->cerita->get_cendolbata($id_cerita),
			"pelihat"		=>  $this->cerita->get_pelihat($id_cerita),
			//"komen"		=>  $this->cerita->get_komen($id_cerita),
		);
		
		$this->page->data($data);
		$this->page->content("default/baca_cerita");
		$this->page->template("ngajeng_pager");
		$this->page->view();
	}
	
	

	function perkoro()
	{
		
		$data['panduan']=$this->mod_operasiberkas->get_panduan();
		$width = '100%';
        	$height = '500px';
        	$this->editor($width,$height); //plugin ckeditor di defenisikan pada halaman index
 		$this->load->view('paket/perkoro', $data);

	}
	
	function panduan()
	{
		
		$data['panduan']=$this->mod_operasiberkas->get_panduan();
		$width = '100%';
        	$height = '500px';
        	$this->editor($width,$height); //plugin ckeditor di defenisikan pada halaman index
 		$this->load->view('bimbingan', $data);

	}
	function kontak()
	{
		
		$data['panduan']=$this->mod_operasiberkas->get_panduan();
		$width = '100%';
        	$height = '500px';
        	$this->editor($width,$height); //plugin ckeditor di defenisikan pada halaman index
 		$this->load->view('sesambetan', $data);

	}

	public function download($pillch='Tidak_ada_data')
	{	
		if (isset($pillch))
		if ($pillch!='Tidak_ada_data') 
			{	
				//$pillch=str_replace('http://paper.poltekkes-malang.ac.id/berkas/','',$pillch);
				$n="http://docs.google.com/viewer?url=paper.poltekkes-malang.ac.id/berkas/".$pillch;
				//echo 
				//'<iframe src="https://docs.google.com/viewer?url=paper.poltekkes-malang.ac.id/berkas/'.$pillch.'&embedded=true" width="1200" height="780" style="border: none;"></iframe>';	
				//echo '"https://docs.google.com/viewer?url='.$pillch.'&embedded=true" width="1200" height="780" style="border: none;"';
				//echo $n; 
				//header('Location:'.base_url().'index.php/berkas/'.$pillch);
				//header('Location: http://docs.google.com/viewer?url=http://paper.poltekkes-malang.ac.id/berkas/'.$pillch);
				//header('Location: https://docs.google.com/viewer?embedded=true&url=http%3A%2F%2Fhomepages.inf.ed.ac.uk%2Fneilb%2FTestWordDoc.doc');
				header('Location: https://docs.google.com/viewer?embedded=true&url=http%3A%2F%2Fpaper.poltekkes-malang.ac.id%2Fberkas%2F'.$pillch);

				// http://docs.google.com/viewer?url=http%3A%2F%2Fpaper.poltekkes-malang.ac.id%2Fberkas%2F20110301.pdf
			}
			else
			{	
				$this->load->view('ftemp');
			}
		
	}

	public function unduh($pillch='Tidak_ada_data',$terbitan,$files)
	{	
		echo $pillch;
		if (isset($pillch))
		if ($pillch!='Tidak_ada_data') 
			{
			$n="http://docs.google.com/viewer?url=paper.poltekkes-malang.ac.id/".$pillch.'/'.$terbitan.'/'.$files;
			header('Location: https://docs.google.com/viewer?embedded=true&url='.base_url().$pillch.'/'.$terbitan.'/'.$files);
			}
			else
			{	
				$this->load->view('ftemp');
			}


	} //end unduh
	function editbuletin($kode,$judul)
	{	$data['sistem']="editbuletin";
		$data['pemakai']="user";
		//$data['editbuletin']=$this->mod_operasiberkas->get_judul_b($kode,0);
		$data['daftarkategori']=$this->mod_operasiberkas->daftar_b(30,0);
		$data['judul']=str_replace('%20',' ',$judul);
		$data['kode']=$kode;
		$this->load->view('template', $data);
	}
	function hapusbuletin($kode,$page='1')
	{
		$this->load->model('mod_operasiberkas');
		$data['sistem']="pesan";
		$data['halamanaktif']=1;
		$hal=$this->kampung_halaman($page);
		$data['title']='JurnalEdit';
		$data['otentifik']='user';
		$this->mod_operasiberkas->disetipbuletin($kode);
		$data['jenis']='Jurnal';
		$data['pesankesan']='Buletin Telah dihapus';
		$data['curlink']=base_url().'index.php/k_idx/topmenus/buletin/1';
		$this->form_validation->set_rules('username', 'username', 'required');
		$this->form_validation->set_rules('password', 'password', 'required');
		$this->load->view('template', $data);
	}
	function export($lastper)
	{			
	}
	function gantisandi()
	{	$data['sistem']="gantisandi";
		$data['pemakai']="user";
		//$data['editbuletin']=$this->mod_operasiberkas->get_judul_b($kode,0);
		$this->load->view('template', $data);
	}
	function gantipass_acc()
	{	$nama=$this->session->userdata('nama');
		$id=$this->session->userdata('nama2');
		$tuwir=$this->input->post('tuwir');
		$perawan=$this->input->post('perawan');
		$janda=$this->input->post('janda');
		$this->mod_javidol->jav_gachinco($id,$nama,$tuwir,$perawan,$janda);
		$data['sistem']="pesan";
		$data['pemakai']="user";
		//$data['editbuletin']=$this->mod_operasiberkas->get_judul_b($kode,0);
		$this->load->view('template', $data);
	}
	function recet_password_8192()
	{
		$janda=$this->input->post('tuwir');
		$dataemail=$this->mod_javidol->jav_tokyohot($janda);
		foreach($dataemail->result() as $row){
			//echo $row->email;
			$mail=$row->email;
			$nama=$row->Nama;
			$dongkijelek=$row->Password;
		}
		$newpassword=substr(md5($dongkijelek),0,8);
		$this->mod_javidol->jav_enternity($mail,$newpassword,$dongkijelek);
		
		$this->load->library('email');
		$this->email->from('adminjurnal@surel.poltekkes-malang.ac.id', 'Admin');
		$this->email->to($mail);
		$this->email->cc($mail);
		$this->email->bcc($mail);
		$this->email->subject('Password reset dari jurnal online Poltekkes Malang');
		$this->email->message('Berikut ini adalah user name dan password anda
							  	user     : '.$nama.'
								paswword : '.$newpassword.'
								
							  ');
		$this->email->send();
		//echo $this->email->print_debugger();
		$data['sistem']="pesan";
		$data['pesankesan']="Cek email anda untuk mengetahui password baru";
		$this->load->view('template', $data);
	}
	
	function recet_password($usermail)
	{
		
		$this->load->library('email');
		$this->email->from('adminjurnal@surel.poltekkes-malang.ac.id', 'Admin');
		$this->email->to('kasanawi@gmail.com');
		$this->email->cc($usermail);
		$this->email->bcc($usermail);
		$this->email->subject('Email Test Kampret jaren njaluk password reset');
		$this->email->message('Testing the email class. iki tak wenwhi ojo bola bali lali ae');
		$this->email->send();
		echo $this->email->print_debugger();
	}

	function berlangganan()
	{
		$page=1;
		$this->session->set_userdata('usrid','asas');
	    	$this->load->library('lib_tables');
		$this->mod_count->increment(); // cache url
		$data['sistem']='berlangganan';
		$data['pemakai']="user";
		$data['pilihan']="0";
		$data['halamanaktif']=$page;
		$data['max_pg']=$this->mod_operasiberkas->get_total_hal();
		$data['lastque']='';
		$data['paket']=$this->lib_tables->tabel_paket();
		$this->load->model('modules');
				
				$hal=$this->kampung_halaman($page);
				//$data['daftarjurnal']=$this->mod_operasiberkas->daftar($hal[1],$hal[0]);
				$this->load->view('langganan', $data);

	}
	
	function pelihat($url='')
	{	
		$data['url']=$url;
		$this->load->view('pewaos', $data);

	}
	
	function maos($url='')
	{
		$data['url']=$url;
		$this->load->view('langganan/maos', $data);
	}
	
	function periksa_sesi()
	{
		echo "<pre>";
		//$this->session->set_userdata('paket','asas');
		print_r($this->session);
		echo "</pre>";
	}

	function update_status_paket($member)
	{
		echo $member."has active day = ";
		echo $this->mod_javidol->jav_bukkake($member);

	}
	function editor($width,$height) {
    		//configure base path of ckeditor folder
    		$this->lib_editor->basePath = base_url().'jogorogo/plugins/ckeditor/';
    		$this->lib_editor->config['toolbar'] = 'Full';
    		$this->lib_editor->config['language'] = 'en';
    		$this->lib_editor->config['width'] = $width;
    		$this->lib_editor->config['height'] = $height;
  	}
	function simpan_panduan()
	{
	}

	function tmpl()
	{
	}
	
	function acc_list($page=1)
	{						
		$data['sistem']='acc_list';
		$data['pemakai']="user";
		$data['pilihan']="0";
		$data['halamanaktif']=$page;
		$data['max_pg']=$this->mod_operasiberkas->get_total_hal();

				$hal=$this->kampung_halaman($page);
				$data['daftarjurnal']=$this->mod_operasiberkas->daftar_konpirm($hal[1],$hal[0]);
				$this->load->view('ngijini', $data);

	}	

	function teken($mode='baru')
	{
		$data=array(
			
			'id_peserta'	=> $this->session->userdata('id_biodata'),
			"add"			=> $this->page->base_url("/add"),
		
			"multi_del"		=> $this->page->base_url("/multi_del"),

			"import"		=> $this->page->base_url("/import"),
			//"excel"		=> $this->page->base_url("/xls/$page/$limit/$id_prodi"),
			"pdf"			=> $this->page->base_url("/pdf"),
			"mode"			=> $mode,
			"opt_wil"		=> $this->mod_operasiberkas->get_opt_wil_ar(),
			"martabak"		=> $this->mod_operasiberkas->martabak(),
		);
		
		$this->page->data($data);
		$this->page->content("forms/daftar");
		$this->page->template("ngajeng_pager");
		$this->page->view();
				//$hal=$this->kampung_halaman($page);
				//$data['daftarjurnal']=$this->mod_operasiberkas->daftar($hal[1],$hal[0]);
				
	}
	function intipan($lokasi,$folder,$file)
	{
		$data = array(
				'message'	=> 'Image Uploaded Successfully',
				'file' 		=> $file,
				'folder' 		=> $folder,
				'lokasi' 		=> $lokasi,

				);

		$this->load->view('langganan/intipan', $data);
		

	} // end intipan
	
	function prepiew($source, $target)
	{
		//$source = realpath($source);
		$target = dirname($source).DIRECTORY_SEPARATOR.$target;
		$im     = new Imagick($source."[0]"); // 0-first page, 1-second page
		$im->setImageColorspace(255); // prevent image colors from inverting
		$im->setimageformat("jpeg");
		$im->thumbnailimage(160, 120); // width and height
		$im->writeimage($target);
		$im->clear();
		$im->destroy();
	} // end pf prepiew
	
	function gosok()
	{
		//$this->session->set_userdata('usrid','asas');
		//$this->load->model('modules');
		$this->page->menu("", "k_idx");
		$data['sistemgrant']='KKK';
		if ($pesan=99) $data['sistemgrant']='Username dan password salah';
		$data['sistem']='cari';
		$data['halamanaktif']=1;
		$data['griyo']='active';
		$data['daftarjurnal']=$this->mod_operasiberkas->daftar(10,0);
		$data['daftarindek']=$this->mod_operasiberkas->daftar_indek();
		$data['max_pg']=$this->mod_operasiberkas->get_total_hal();
		
		$this->form_validation->set_rules('username', 'username', 'required');
		$this->form_validation->set_rules('password', 'password', 'required');
		//new style
		$hal=$this->kampung_halaman(1);
		
		$data_paper=$this->mod_operasiberkas->daftar_paper_pages($hal[1],$hal[0]);
		$data_paper_slider=$this->mod_operasiberkas->daftar_paper_slider_depan($hal[1],$hal[0]);	

		$data['daftar_paper']=$data_paper->result();
		$data['karusel_paper']=$data_paper_slider->result();
		$data['dino']=$this->mod_operasiberkas->dino_nasional();
		$data['bulan']=$this->mod_operasiberkas->bulan_nasional();
		//$this->load->view('ngajeng_pager', $data);
		$data["action"]	= $this->page->base_url("/baca_cerita");//site_url
		
		$this->page->data($data);
		$this->page->content("default/ngajeng_pager2");
		$this->page->template("general");
		$this->page->view();
	}
	
	

}// update v.1.3
