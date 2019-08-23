<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class k_adm extends CI_Controller{
	function __construct()
	{	
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->library('session');
		$this->load->library('lib_tables');
		$this->load->library('lib_javidol');
		$this->load->library('table');
		$this->load->model('mod_operasiberkas');
		$this->load->model('mod_javidol');
		$this->load->model('modules');
		$this->load->model('mod_capjay');
		$this->load->model('mod_count');
		$this->load->helper('captcha');
		$this->load->helper('url');
		$this->load->library('calendar');
		$this->load->library('lib_editor');
		if ($this->form_validation->run() == FALSE)
		{
			//$this->load->view('myform');
		}
		else
		{
			//$this->load->view('formsuccess');
		}
	}
	function index()
	{	
		$this->session->set_userdata('usrid','asas');
		$this->load->model('modules');
		$data['sistemgrant']='tamutakdiundang';
		$data['sistem']='upload';
		$data['halamanaktif']=1;
		$data['griyo']='adm/pasang';
		$tanggal=date('d/m/Y');
		$data['daftarjurnal']=$this->mod_operasiberkas->daftar_paper_baru(30,0,$tanggal);
		$data['daftarindek']=$this->mod_operasiberkas->daftar_indek();
		$data['max_pg']=$this->mod_operasiberkas->get_total_hal();
		$data['paket']=$this->lib_tables->tabel_paket();
		$this->form_validation->set_rules('username', 'username', 'required');
		$this->form_validation->set_rules('password', 'password', 'required');
		$this->load->view('ngajeng_adm', $data);
	}
	function pasang()
	{	
		$this->session->set_userdata('usrid','asas');
		$this->load->model('modules');
		$data['sistemgrant']='tamutakdiundang';
		$data['sistem']='cari';
		$data['halamanaktif']=1;
		$data['griyo']='active';
		$data['daftarjurnal']=$this->mod_operasiberkas->daftar(10,0);
		$data['daftarindek']=$this->mod_operasiberkas->daftar_indek();
		$data['max_pg']=$this->mod_operasiberkas->get_total_hal();
		$data['adm_page']='pasang';
		$data['paket']=$this->lib_tables->tabel_paket();
		$this->form_validation->set_rules('username', 'username', 'required');
		$this->form_validation->set_rules('password', 'password', 'required');
		$this->load->view('ngajeng_adm', $data);
	}
	function unggah()
	{
		//$structure='2301';
		//print_r($_FILES['file']['type']);
		
		if(isset($_FILES['file']['name'])){
			$_prefixname=$_FILES['file']['name'];
			$_foldername=substr($_prefixname,0,8);
			$tanggalterbit=substr($_prefixname,2,2);
			$bulanterbit=substr($_prefixname,4,2);
			$tahunterbit=substr($_prefixname,6,2);
			$prefiktanggal = $tanggalterbit.'-'.$bulanterbit.'-20'.$tahunterbit;
			$_tahunterbit = DateTime::createFromFormat('d-m-Y', $prefiktanggal);
			$newDateString = $_tahunterbit->format('Y-m-d H:i');
			//echo $_tahunterbit;
			$structure = $_foldername;
			$structure_md5 = md5($structure);
			$data2['judul']='Malang POST Paper '.$prefiktanggal;
			$data2['penulis']='Paper Malangpost';
			$data2['kategori']='90';
			$data2['deskripsi']='Malang POST Paper '.$newDateString;
			$old_umask = umask(0);	
			if (file_exists('berkas/'.$structure)) {
				$data = array(
					'error'	=> 'Direktori ada',
				);
				echo json_encode($data);
			}
			else
			{
				if (!mkdir('berkas/'.$structure, 0777, true)) 
				{
						// die('Failed to create folders...');
					$data = array(
						'error'	=> 'Direktori ada, gagal buat',
					);
					$sejarah = array(
						 0 => array(
								'sejarah' => 'Direktori ada, gagal buat',
								
							),
					);
					echo json_encode($data);
				}
			}
			if (file_exists('dnps5d/'.$structure_md5)) {
				$data = array(
					'error'	=> 'Direktori ada',
				);
				echo json_encode($data);
				$sejarah = array(
					 1 => array(
							'sejarah' => $data,							
						),
				);
			}
			else
				{
				if (!mkdir('dnps5d/'.$structure_md5, 0777, true)) 
				{
						// die('Failed to create folders...');
					$data = array(
						'error'	=> 'Direktori ada, gagal buat',
					);
					echo json_encode($data);
					$sejarah = array(
						 2 => array(
								'sejarah' => $data,	
							),
					);
				}
			}
			umask($old_umask);
		} // end if filename

		if(isset($_FILES['file']['type']))
		{
			$validextensions = array('jpeg', 'jpg', 'png', 'pdf');
			$allowedExts = array("pdf");
			$temp = explode(".", $_FILES['file']['name']);
			$extension = end($temp);

			
			$temporary = explode('.', $_FILES['file']['name']);
			$file_extension = strtolower(end($temporary));
			if($extension=='pdf' or $extension=='jpg')  {
			    echo 'Upload Done :';
				$sejarah = array(
				1 => array(
						'sejarah' => 'Done',
						'error'	  => ''
					),
				);
				if (file_exists('berkas/' . $_FILES['file']['name'])) {
							$data = array(
								'error' => $_FILES['file']['name'] . ' already exists' 
							);
							http_response_code(500);
							echo json_encode($data);
							$sejarah = array(
							 5 => array(
									'sejarah' => $data,									
									),
							);
				}else{
					if($extension=='pdf') 
					{
						if ($_FILES['file']['type'] == 'application/pdf') {
							$filename = $_FILES['file']['name'];
							$filename_md5 = md5($filename).'.pdf'; 
							$filename_md5_pv = md5($filename).'.jpg';	

							$sourcePath = $_FILES['file']['tmp_name']; // Storing source path of the file in a variable
							$targetPath = 'berkas/'.$structure.'/'.$filename; // Target path where file is to be stored
							$targetPath_md5 = 'dnps5d/'.$structure_md5.'/'.$filename_md5; // Target path where fi
							//move_uploaded_file($sourcePath,$targetPath) ; // Moving Uploaded file
							//move_uploaded_file($sourcePath,$targetPath) ; // Moving Uploaded file normal
							move_uploaded_file($sourcePath,$targetPath_md5) ; // Moving Uploaded file md5
							$data2['files']=$filename;
							
							$this->mod_operasiberkas->genPdfThumbnail($targetPath_md5,$filename_md5_pv); // generates /uploads/my.jpg
							
							$data2['files_md5']=$filename_md5;
							$data2['offlink']='berkas/'.$structure.'/'.$filename;
							$data2['linkb']='dnps5d/'.$structure_md5.'/'.$filename_md5;
							$data2['link3']='dnps5d/'.$structure_md5.'/'.$filename_md5_pv;
							$data2['tahunterbit']=$newDateString; 
							$data = array(
								'message'	=> 'Image Uploaded Successfully',
								'file' 		=> $targetPath,
								'db'		=> $this->mod_operasiberkas->save_paper($data2)	
								//'db2'		=> $this->mod_operasiberkas->save_paper_jpg($data3)	
							);
							//echo json_encode($data);	
							$sejarah = array(
								 6 => array(
										'sejarah' => $data,									
										),
								);								
						}; // end  if ($_FILES['file']['type'] == 'application/pdf')
					} //end if if($extension=='pdf') 
					if($extension=='jpg') 	
					{	
						$filename = $_FILES['file']['name'];
						$kunci_nama=explode('.', $filename);
						$nama_baru=$kunci_nama[0];
						$filename_md5 = md5($nama_baru.'.pdf').'.jpg'; 
						

						$sourcePath = $_FILES['file']['tmp_name']; // Storing source path of the file in a variable
						$targetPath = 'berkas/'.$structure.'/'.$filename; // Target path where file is to be stored
						$targetPath_md5 = 'dnps5d/'.$structure_md5.'/'.$filename_md5; // Target path where fi
						//move_uploaded_file($sourcePath,$targetPath) ; // Moving Uploaded file
						//move_uploaded_file($sourcePath,$targetPath) ; // Moving Uploaded file normal
						move_uploaded_file($sourcePath,$targetPath_md5) ; // Moving Uploaded file md5
						//$data2['files']=$filename;
						
						//$this->mod_operasiberkas->genPdfThumbnail($targetPath_md5,$filename_md5_pv); // generates /uploads/my.jpg
						
						//$data2['files_md5']=$filename_md5;
						//$data2['offlink']='berkas/'.$structure.'/'.$filename;
						//$data2['linkb']='dnps5d/'.$structure_md5.'/'.$filename_md5;
						$data2['link3']='dnps5d/'.$structure_md5.'/'.$filename_md5;
						//$data2['tahunterbit']=$newDateString; 
						$data = array(
								'message'	=> 'Image Uploaded jpg Successfully',
								'file' 		=> $targetPath,
								'db'		=> $this->mod_operasiberkas->save_gambar($nama_baru.'.pdf',$data2)	
								//'db2'		=> $this->mod_operasiberkas->save_paper_jpg($data3)	
							);
							$sejarah = array(
							 7 => array(
									'sejarah' => $data,									
									),
							);		
					};
				}; // end if (file_exists('berkas/' . $_FILES['file']['name']))

			}
			else
			{	
				$data = array(
					'error'	=> 'Invalid type data'.'error :',
					);
				http_response_code(500);
				echo json_encode($data);

				$sejarah = array(
				2 => array(
						'sejarah' => $data,'error'	  => 'salah'
					),
				);

			}	; // end of $_FILES['file']['type']
			if(($_FILES['file']['size'] > 3200000)){
				$data = array(
							'error'	=> 'Max file size is 32Mb You size is'.$_FILES['file']['size'],
						);
				http_response_code(500);
				echo json_encode($data);
				$sejarah = array(
				1 => array(
						'sejarah' => $data,
						'error'	  => 'salah'
					),
				);
			} else {
				
			}; //end if(($_FILES['file']['size']
			
			
		}
		else
		{	
			
		} // end if if(isset($_FILES['file']['type']))
		
		$data = array(
					'error'	=> 'General Error',
					);
				http_response_code(500);
				//echo json_encode($data);
		echo json_encode($sejarah);

	} // end function unggah
	
	function intipan()
	{
		$data = array(
				'message'	=> 'Image Uploaded Successfully',
				'file' 		=> '',
				);
		$this->load->view('langganan/intipan', $data);
	} // end intipan
	function adm()
	{
		$this->load->view('admin/admin');
	} // end of adm frontpage
	function gosok($tanggal_terbitan)
	{
		$this->mod_operasiberkas->gosok_paper($tanggal_terbitan);
	}
	function editpaket($kode_paket)
	{	
		$data['kode_paket']=$kode_paket;
		$data['paket']=$this->mod_operasiberkas->get_paket($kode_paket);;
		$data['status_paket']=1;
		//echo $this->lib_editor->editor('panduan','');
		$this->load->view('paket_admin', $data);
	}
	function tambahpaket($kode_paket)
	{	
		$data['kode_paket']=$kode_paket;
		$data['paket']=$this->mod_operasiberkas->get_paket(0);;
		$data['status_paket']=2;
		//echo $this->lib_editor->editor('panduan','');
		$this->load->view('paket_admin', $data);
	}
	function aksi_paket($kode_aksi,$kode_paket='')
	{	//$data['user']=$this->input->post('jav_name', TRUE);
		//echo $kode_aksi;
		$data = array(
			'id' => $this->input->post('id_paket', TRUE),
               		'nama' => $this->input->post('nama', TRUE),
               		'harga' => $this->input->post('harga', TRUE),
               		'masa_aktif' => $this->input->post('aktif', TRUE),
			'keterangan' => $this->input->post('keterangan', TRUE),
            		);
		if($kode_aksi==1)
		{
			$this->db->where('id', $data['id']);
			$this->db->update('mdx_paket', $data); 
			$this->editpaket($data['id']);
		} //end edit
		if($kode_aksi==2)
		{
			unset($data['id']);
			//$data['id']=''; // auto inkremen
			$this->db->insert('mdx_paket', $data); 		
			$this->tambahpaket(0);
		} //end tambah
		if($kode_aksi==3)
		{
			$this->db->where('id', $kode_paket);
			$this->db->delete('mdx_paket'); 
			redirect('k_idx/berlangganan');
			//echo $this->db->last_query();	
		} //end edit
	} // end aksi paket
	function editpengwasa($kode_paket)
	{	
		$data['kode_paket']=$kode_paket;
		$data['daftarjurnal']=$this->mod_operasiberkas->detail_pengwasa($kode_paket);;
		$data['status_paket']=1;
		//echo $this->lib_editor->editor('panduan','');
		$this->load->view('pengwasa_admin', $data);
	}
	function tambahpengwasa($kode_paket)
	{	
		$data['kode_paket']=$kode_paket;
		$data['daftarjurnal']=$this->mod_operasiberkas->detail_pengwasa(0);;
		$data['status_paket']=2;
		//echo $this->lib_editor->editor('panduan','');
		$this->load->view('pengwasa_admin', $data);
	}
	function aksi_pengwasa($kode_aksi,$kode_paket='')
	{	//$data['user']=$this->input->post('jav_name', TRUE);
		//echo $kode_aksi;
		$data = array(
			'id' => $this->input->post('id', TRUE),
			'jenengmusapa' => $this->input->post('userid', TRUE),
			'kuncimuendi1' =>$this->input->post('userpass2', TRUE),
			'kuncimuendi'=> $this->input->post('userpass', TRUE),
			'email'=> $this->input->post('email', TRUE),
			'email2' => $this->input->post('email2', TRUE),
			'lagakmu' => $this->input->post('jenis', TRUE),
			);
		$data2 = array(
			'id' => '',
			'nama' => $this->input->post('nama', TRUE),
			'alamat' => $this->input->post('alamat', TRUE),
			'kota' => $this->input->post('kota', TRUE),
			'provinsi' => $this->input->post('provinsi', TRUE),
			'kodepos' => $this->input->post('kodepos', TRUE),
			'instansi' => $this->input->post('instansi', TRUE),
        		);
			$lempar[1]=$this->input->post('userid', TRUE);
			$lempar[2]=$this->input->post('userpass2', TRUE);
			$lempar[3]= $this->input->post('userpass', TRUE);
			$lempar[4]= $this->input->post('email', TRUE);
			$lempar[5]= $this->input->post('email2', TRUE);
			$lempar[6]= $this->input->post('nama', TRUE);
			$lempar[7]= $this->input->post('alamat', TRUE);
			$lempar[8]= $this->input->post('kota', TRUE);
			$lempar[9]= $this->input->post('provinsi', TRUE);
			$lempar[10]= $this->input->post('kodepos', TRUE);
			$lempar[11]= $this->input->post('instansi', TRUE);
			$lempar[12]= $this->input->post('jenis', TRUE);
		if($kode_aksi==1)
		{
			if ($data['kuncimuendi1']=='') { 
				unset($data['kuncimuendi']);unset($data['kuncimuendi1']); 
				}
				else  if ($data['kuncimuendi']==$data['kuncimuendi1'])
					{ 
					$data['kuncimuendi']=MD5($data['kuncimuendi']);
					unset($data['kuncimuendi1']); 
					}
			if ($data['email2']=='')
				 {
				 unset($data['email']);
				 unset($data['email2']);
				 }
			//if ($data['password1']='') { $lempar[2]=$lempar[3]; }
			//if ($data['email2']='') { $lempar[5]=$lempar[4]; }
			//$this->mod_operasiberkas->registrasi_admin($lempar);
			$this->db->where('id', $data['id']);
			$this->db->update('mdx_kolpakz', $data); 
			$this->db->where('id', $data2['id']);
			$this->db->update('mdx_anggota', $data2); 
			$this->editpengwasa($data['id']);
		} //end edit
		if($kode_aksi==2)
		{
			//print_r($lempar);
			$this->mod_operasiberkas->registrasi_admin($lempar);
			//$this->db->insert('mdx_paket', $data); 		
			//$this->tambahpaket(0);
			$this->tambahpengwasa(0);
		} //end tambah
		if($kode_aksi==3)
		{
			$this->db->where('id', $kode_paket);
			$this->db->delete('mdx_kolpakz'); 
			$this->db->where('id', $kode_paket);
			$this->db->delete('mdx_anggota'); 
			redirect('k_idx/topmenus/penguasa');
			//echo $this->db->last_query();	
		} //end edit
	} // end aksi pengwasa
	function pasang_dewe()
	{
		$this->session->set_userdata('usrid','asas');
		$this->load->model('modules');
		$data['sistemgrant']='tamutakdiundang';
		$data['sistem']='cari';
		$data['halamanaktif']=1;
		$data['griyo']="adm/pasang_dewe";
		$tanggal=date('d/m/Y');
		$dir  = 'unggah';
		$data['files1'] = scandir($dir);
		$data['files2'] = scandir($dir, 1);
		$data['daftarjurnal']=$this->mod_operasiberkas->daftar_paper_baru(30,0,$tanggal);
		$data['daftarindek']=$this->mod_operasiberkas->daftar_indek();
		$data['max_pg']=$this->mod_operasiberkas->get_total_hal();
		$data['paket']=$this->lib_tables->tabel_paket();
		$this->form_validation->set_rules('username', 'username', 'required');
		$this->form_validation->set_rules('password', 'password', 'required');
		$this->load->view('ngajeng_adm', $data);
	}
	function unggah_manual()
	{
		// bagiab upload dari hasil upload ftp
		$dir    = 'unggah';
		$files1 = scandir($dir);
		$files2 = scandir($dir, 1);
		//print_r($files1);
		//print_r($files2);
		foreach($files1 as $row){
			$filename=$row;//echo substr($filename, 0,2);
			if (substr($filename, 0,2)=='MP')	
			{
			$this->proses('unggah',$filename);
			//echo $filename;
			}
		}
	$this->pasang_dewe();
	} //end unggah manual
	function proses($sumber,$filename)
	{
		//echo $filename;
		$t=time();
		//echo($t . "<br>");
		//echo 'mulai'.(date("Y-m-d",$t));
		if(isset($filename)){
			$_prefixname=$filename;
			$_foldername=substr($_prefixname,0,8);
			$tanggalterbit=substr($_prefixname,2,2);
			$bulanterbit=substr($_prefixname,4,2);
			$tahunterbit=substr($_prefixname,6,2);
			$prefiktanggal = $tanggalterbit.'-'.$bulanterbit.'-20'.$tahunterbit;
			$_tahunterbit = DateTime::createFromFormat('d-m-Y', $prefiktanggal);
			$newDateString = $_tahunterbit->format('Y-m-d H:i');
			//echo $_tahunterbit;
		}
		$structure = $_foldername;
		$structure_md5 = md5($structure);
		$data2['judul']='Malang POST Paper '.$prefiktanggal;
		$data2['penulis']='Paper Malangpost';
		$data2['kategori']='90';
		$data2['deskripsi']='Malang POST Paper '.$newDateString;
		$old_umask = umask(0);	
		//echo '<hr>make directory'.$structure_md5;
		//echo '<hr>make directory'.$structure;
		if (file_exists('berkas/'.$structure)) {
			$data = array(
				'error'	=> 'Direktori ada',
			);
			//echo json_encode($data);
		}
 		else
		{
 		if (!mkdir('berkas/'.$structure, 0777, true)) 
		{
    			// die('Failed to create folders...');
			$data = array(
				'error'	=> 'Direktori ada, gagal buat',
			);
			//echo json_encode($data);
		}
		}
		if (file_exists('dnps5d/'.$structure_md5)) {
			$data = array(
				'error'	=> 'Direktori ada',
			);
			//echo json_encode($data);
		}
 		else
		{
 		if (!mkdir('dnps5d/'.$structure_md5, 0777, true)) 
		{
    			// die('Failed to create folders...');
			$data = array(
				'error'	=> 'Direktori ada, gagal buat',
			);
			//echo json_encode($data);
		}
		}
		umask($old_umask);
										$filename_md5 = md5($filename).'.pdf';
										$filename_md5 = md5($filename).'.pdf'; 
										$filename_md5_pv = md5($filename).'.jpg';	
										$sourcePath = $sumber.'/'.$filename; // Storing source path of the file in a variable
										$targetPath = 'berkas/'.$structure.'/'.$filename; // Target path where file is to be stored
										$targetPath_md5 = 'dnps5d/'.$structure_md5.'/'.$filename_md5; // Target path where fi
										//move_uploaded_file($sourcePath,$targetPath) ; // Moving Uploaded file
										//move_uploaded_file($sourcePath,$targetPath) ; // Moving Uploaded file normal
										//move_uploaded_file($sourcePath,$targetPath_md5) ; // Moving Uploaded file md5
										rename($sourcePath,$targetPath_md5);
										$data2['files']=$filename;
										$this->mod_operasiberkas->genPdfThumbnail($targetPath_md5,$filename_md5_pv); // generates /uploads/my.jpg
										$data2['files_md5']=$filename_md5;
										$data2['offlink']='berkas/'.$structure.'/'.$filename;
										$data2['linkb']='dnps5d/'.$structure_md5.'/'.$filename_md5;
										$data2['link3']='dnps5d/'.$structure_md5.'/'.$filename_md5_pv;
										$data2['tahunterbit']=$newDateString; 
										$data = array(
											'message'	=> 'Image Uploaded Successfully',
											'file' 		=> $targetPath,
											'db'		=> $this->mod_operasiberkas->save_paper($data2)	
											//'db2'		=> $this->mod_operasiberkas->save_paper_jpg($data3)	
										);
										//echo json_encode($data);
										//unlink($sourcePath);
	//echo 'selesai'.(date("Y-m-d",$t));
	} //end proses
	function busekae($kode,$page='1')
	{
		$data_paper=$this->mod_operasiberkas->daftar_paper_id($kode)->result_array();
		//print_r($data_paper);		
		unlink($data_paper[0]['link2']);	
		unlink($data_paper[0]['link3']);
		$this->mod_operasiberkas->gosok_file($kode);	
		redirect('k_adm');
	} //end busek ae
	function ae6()
	{
		$data=$this->db->query("update mdx_anggota set id=156 where id=8 ");
		//$data=$this->db->query("select * from mdx_jurnal_ass where tahunterbit='2018-04-06' order by tahunterbit desc limit 10");
		$data=$this->db->query("select * from mdx_anggota ");
		echo "<pre>";
		print_r($data->result_array());
		echo "</pre>v.103";
	}
	function ae8()
	{
		//$data=$this->db->query("select * from mdx_jurnal_ass");
		$data=$this->db->query("select * from mdx_jurnal_ass where tahunterbit='2018-05-28' order by tahunterbit desc limit 10");
		//$data=$this->db->query("select * from mdx_anggota ");
		echo "<pre>";
		print_r($data->result_array());
		echo "</pre>";
	}
	function lengkap()
	{
		$this->session->set_userdata('usrid','asas');
		$this->load->model('modules');
		$data['sistemgrant']='tamutakdiundang';
		$data['sistem']='cari';
		$data['halamanaktif']=1;
		$data['griyo']="adm/pasang_dewe";
		$tanggal=date('d/m/Y');
		$dir  = 'unggah';
		$data['files1'] = scandir($dir);
		$data['files2'] = scandir($dir, 1);
		$data['daftarjurnal']=$this->mod_operasiberkas->daftar_paper_baru(2000,0,$tanggal);
		$data['daftarindek']=$this->mod_operasiberkas->daftar_indek();
		$data['max_pg']=$this->mod_operasiberkas->get_total_hal();
		$data['paket']=$this->lib_tables->tabel_paket();
		$this->form_validation->set_rules('username', 'username', 'required');
		$this->form_validation->set_rules('password', 'password', 'required');
		$this->load->view('palengkap', $data);
	}
} //end k-adm controler v 1.03