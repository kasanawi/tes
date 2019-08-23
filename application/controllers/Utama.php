<?php
/* Siakad Controler Version 3.2 */
class Utama extends CI_Controller {

	var $siakad_b;

	function __construct()
	{
		parent::__construct();
		$this->page->set_base_url(site_url("/utama"));
		//$this->siakad_b=$this->load->database('siakad_b', TRUE);
		error_reporting(E_ALL); 
		ini_set("display_errors", 1); 
	}
	
	function index($status = "")
	{
		login_session();
		//$this->load->model("periode_model", "periode");
		$this->load->model("semester_model", "semester");
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
		//echo "Silakan menunggu....";
		$this->page->data($data);
		//echo $this->sub_dom();
		$this->page->template("login");
		
		$this->page->view();
	}
	
	function check()
	{	//echo 'cek';
		$this->load->model("user_model", "user");
		$this->load->model("periode_model", "periode");
		
		$this->page->set_db_data(array("username", "password"));
		$source	= $this->user->check($this->page->db_data());
		
		$data_aktif_admin=$_POST;
		//print_r($data_aktif_admin);
		if ($source === FALSE)
		{	
			
			$source2	= $this->user->check_pembaca($this->page->db_data());
			if ($source2 === FALSE)
			{	
				//print_r($source2);
				//echo 'sssssssssss';
				//redirect($this->page->base_url("/index/err"));
			}
			else
			{
				$this->proses_login_user($source2);
			}				
		}
		else
		{
			//echo "Silakan menunggu....";
			$this->load->model("periode_model", "periode");
			//print_r($data_aktif_admin); 
			$periode = 1;; //$this->periode->get_aktif_admin($data_aktif_admin['id_periode'],$data_aktif_admin['semester'])->row();
			$row = $source->row();
			//print_r($row);
			//echo $row->kd_user_group;
			//kriteria session umum
			
			echo "..";
			$date = new DateTime();
			$date->sub(new DateInterval('P1D'));
			//echo $date->format('Y-m-d') . "\n";
			
			$sess_data = array
			(
				//"token"			=> $this->WS->mintatoken(),
				"id_biodata"		=> $row->id_biodata,
				"nama"				=> $row->nama,
				"username"			=> $this->page->db_data("username"),
				"kd_user_group"		=> $row->kd_user_group,
				"id_periode"		=> 1,
				"thn_akademik"		=> 1,
				"semester"			=> 1,
				"mfs_log"			=> 1,			
				"status_program"		=> "debug ver 0.2.1",			
				"f_tindakan"		=> '',
				"f_angkatan"	=> '',
				"filter"		=> $date->format('Y-m-d'),
				"filter_r"		=> date('Y-m-d'),
				"hal_aktif"		=> '',
				"f_status"		=> '',
				"f_langkah"		=> '',
				"f_mk"			=> '',
				"sebegun"		=> '',	
				"catatan"		=> 'login sukses',
			);
					//$this->session->set_userdata('filter',$filter);
					//$this->session->set_userdata('filter_r',$filter_r);
					
			switch ($row->kd_user_group)
			{	
				
				case "adm" :
				{
					
					$sess_data["base"] = "A";
					$sess_data["id_prodi"] = 0;
					$sess_data["id_sms"] = '';
					$sess_data["base_name"]='Administrasi'; 
					break;
				}
				
				case "keu" :
				{
					//$id_prodi = $this->user->get_id_prodi($row->kd_user_group, $row->id_biodata);
					$sess_data["base"] = "K";
					$sess_data["id_prodi"] = 0;
					$sess_data["id_sms"] = '';
					$sess_data["base_name"]='Administrasi Keuangan'; 
					break;
				}
				
				case "tpp" :
				{
					
					$sess_data["base"] = "P";
					$sess_data["id_prodi"] = 0;
					$sess_data["id_sms"] = '';
					$sess_data["base_name"]='Administrasi Penerimaan Pasien'; 
					$sess_data["id_pasien"] = 0;
					$sess_data["id_tindakan"] = 0;
					$sess_data['id_detail_tindakan'] = 0;
					$sess_data['id_farmasi'] = 0;
					break;
				}
				
				case "ari" :
				{
					
					$sess_data["base"] = "I";
					$sess_data["id_prodi"] = 0;
					$sess_data["id_sms"] = '';
					$sess_data["base_name"]='Administrasi Rawat Inap'; 
					$sess_data["id_pasien"] = 0;
					$sess_data["id_tindakan"] = 0;
					$sess_data['id_detail_tindakan'] = 0;
					$sess_data['id_farmasi'] = 0;
					break;
				}

				case "arj" :
				{
					$sess_data["base"] = "J";
					$sess_data["id_prodi"] = 0;
					$sess_data["id_sms"] = '';
					$sess_data["base_name"]='Administrasi Rawat Jalan'; 
					$sess_data["id_pasien"] = 0;
					$sess_data["id_tindakan"] = 0;
					$sess_data['id_detail_tindakan'] = 0;
					$sess_data['id_farmasi'] = 0;
					break;
				}
				
				case "alb" :
				{
					$sess_data["base"] = "L";
					$sess_data["id_prodi"] = 0;
					$sess_data["id_sms"] = '';
					$sess_data["base_name"]='Administrasi Laboratorium'; 
					break;
				}

				case "igd" :
				{
					$sess_data["base"] = "G";
					$sess_data["id_prodi"] = 0;
					$sess_data["id_sms"] = '';
					$sess_data["base_name"]='Administrasi IGD'; 
					break;
				}

				case "giz" :
				{
					$sess_data["base"] = "Z";
					$sess_data["id_prodi"] = 0;
					$sess_data["id_sms"] = '';
					$sess_data["base_name"]='Administrasi Gizi'; 
					break;
				}
				
				case "frm" :
				{
					$sess_data["base"] = "F";
					$sess_data["id_prodi"] = 0;
					$sess_data["id_sms"] = '';
					$sess_data["base_name"]='Administrasi Farmasi'; 
					break;
				}
				case "dok" :
				{
					$sess_data["base"] = "D";
					$sess_data["id_prodi"] = 0;
					$sess_data["id_sms"] = '';
					$sess_data["id_dok"] = '';
					$sess_data["base_name"]='Pengguna'; 
					break;
				}
				
				case "okb" :
				{
					$sess_data["base"] = "O";
					$sess_data["id_prodi"] = 0;
					$sess_data["id_sms"] = '';
					$sess_data["id_dok"] = '';
					$sess_data["base_name"]='Pengguna'; 
					break;
				}
			}
			
			$this->session->set_userdata($sess_data);
			$this->session->mark_as_flash('catatan');
			switch ($source->row()->kd_user_group)
			{
				case "adm" : case "adp" : $redirect = site_url("/beranda/dasbor"); break;
				case "keu" : $redirect = site_url("/keuangan/dasbor"); break;
				case "dbg" : $redirect = site_url("/beranda"); break;
				case "dos" : $redirect = site_url("/dosen"); break;
				case "tpp" : $redirect = site_url("/registrasi/dasbor/index/tpp"); break;
				case "ari" : $redirect = site_url("/ari/dasbor"); break;
				case "arj" : $redirect = site_url("/arj/dasbor"); break;
				case "igd" : $redirect = site_url("/igd/dasbor"); break;
				case "alb" : $redirect = site_url("/alb/dasbor"); break;
				case "giz" : $redirect = site_url("/giz/dasbor"); break;
				case "frm" : $redirect = site_url("/frm/dasbor"); break;
				case "okb" : $redirect = site_url("/diari/dasbor/index/okb"); break;
				case "mhs" : $redirect = site_url("/mahasiswa/dasbor"); break;

				default	: $redirect = base_url().'index.php/keluar';
			}
			
			if ($source->row()->kd_user_group == "dos")
			{
					
				$this->load->model("dosen_model", "dosen");
				$this->dosen->insrt_act("login");
			}
			//echo $row->kd_user_group;
			$this->Userlog_model->insert('login', 'login','');
			redirect($redirect);
		}
	}
	
	function cek_aktif($id_mhs,$id_periode,$semester)
	{
		$this->load->model("konfirm_model", "konfirm");
		$this->load->model("mahasiswa_model", "mahasiswa");
		// cek apakah periode / semester ini mahasiswa sudah bayar
		$konfirm=$this->konfirm->cek($id_mhs,$id_periode,$semester);
		//echo $konfirm;
		if ($konfirm==1) 
			{
				//echo 'sudag bhayr';
				//redirect(site_url("/mahasiswa/$controller/profil/$id_mhs"));
			}
		if ($konfirm==0) 
		{
			//echo 'belum bayar';
			redirect(site_url("/mahasiswa/konfirmasi/add/$id_mhs"));
		}
		
	}
	
	
	function cek_bayar($nim,$id_mhs,$id_periode,$semester)
	{
		$this->load->model("konfirm_model", "konfirm");
		$this->load->model("mahasiswa_model", "mahasiswa");
		// cek apakah periode / semester ini mahasiswa sudah bayar
		$konfirm=$this->konfirm->cek_bayar($nim,$id_periode,$semester);
		//echo $konfirm;
		if ($konfirm==1) 
			{
				//echo 'sudag bhayr';
				//redirect(site_url("/mahasiswa/$controller/profil/$id_mhs"));
			}
		if ($konfirm==0) 
		{
			//echo 'belum bayar';
			redirect(site_url("/mahasiswa/konfirmasi/add_bayar/$id_mhs"));
		}
		
	}	
	
	function get_info_kamar()
	{
	}
	
	function testrim()	
	{
		$data=" add di sini   ";
		echo trim($data);
	}
	
	function tampil_sesi($log=1234)
	{
		if($log==1234)
		{
			print_r($this->session->userdata);
		}
		echo $this->page->base_url("/index/err");
	}
	
	function up_wil()
	{
		$this->load->model("kota_model", "kota");
		$this->kota->update_ref();
	}
	
	
	
	function test_core()
	{
		
		$data=array();
		$this->page->data($data);
		
		
		$this->page->template("belajar");
		
		$this->page->view();
	}
	
	function proses_login_user($source2)
	{
			$this->load->model("periode_model", "periode");
			//print_r($source2); 
			$periode = 1;; //$this->periode->get_aktif_admin($data_aktif_admin['id_periode'],$data_aktif_admin['semester'])->row();
			$row = $source2->row();
			//print_r($row);
			//echo $row->kd_user_group;
			//kriteria session umum
			
			echo "..";
			$date = new DateTime();
			$date->sub(new DateInterval('P1D'));
			//echo $date->format('Y-m-d') . "\n";
			
			$sess_data = array
			(
				//"token"			=> $this->WS->mintatoken(),
				"id_biodata"		=> $row->id_biodata,
				"nama"				=> $row->Nama_pembaca,
				"username"			=> $this->page->db_data("username"),
				"kd_user_group"		=> $row->kd_user_group,
				"id_periode"		=> 1,
				"thn_akademik"		=> 1,
				"semester"			=> 1,
				"mfs_log"			=> 1,			
				"status_program"		=> "debug ver 0.2.1",			
				"f_tindakan"		=> '',
				"f_angkatan"	=> '',
				"filter"		=> $date->format('Y-m-d'),
				"filter_r"		=> date('Y-m-d'),
				"hal_aktif"		=> '',
				"f_status"		=> '',
				"f_langkah"		=> '',
				"f_mk"			=> '',
				"sebegun"		=> '',	
				"catatan"		=> 'login sukses',
			);
					//$this->session->set_userdata('filter',$filter);
					//$this->session->set_userdata('filter_r',$filter_r);
					
			switch ($row->kd_user_group)
			{	
				
				case "okb" :
				{
					$sess_data["base"] = "O";
					$sess_data["id_prodi"] = 0;
					$sess_data["id_sms"] = '';
					$sess_data["id_dok"] = '';
					$sess_data["base_name"]='Pengguna'; 
					break;
				}
			}
			
			$this->session->set_userdata($sess_data);
			$this->session->mark_as_flash('catatan');
			switch ($source2->row()->kd_user_group)
			{
				
				case "okb" : $redirect = site_url("/diari/dasbor/index/okb"); break;
				

				default	: $redirect = base_url().'index.php/keluar';
			}
			
			if ($source2->row()->kd_user_group == "dos")
			{
					
				$this->load->model("dosen_model", "dosen");
				$this->dosen->insrt_act("login");
			}
			echo $row->kd_user_group;
			$this->Userlog_model->insert('login_pembaca', 'login_pembaca','');
			redirect($redirect);
	}

}

/* End of file utama.php */
/* Location: ./application/controllers/utama.php */