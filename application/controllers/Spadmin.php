<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Spadmin extends CI_Controller {
    public function __construct() {
        parent::__construct();
        
        # load model
        $this->load->model('Md_logsistem');
        $this->load->model('Md_users');
        $this->load->model('Md_departement');
        $this->load->model('Md_board');
        $this->load->model('Md_label');
        $this->load->model('Md_workspace');
        $this->load->model('Md_board_relation');
        $this->load->model('Md_member');
        $this->load->model('Md_card');
        $this->load->model('Md_history_card');
        $this->load->model('Md_assignment');
        $this->load->model('Md_cardlabel');
        $this->load->model('Md_comment');
        $this->load->model('Md_attachment');
        $this->load->model('Md_carddetail');
        $this->load->model('Md_unitkerja');

        # library
        $this->load->library('upload');

        # helper
        $this->load->helper('encryption_id');
        $this->load->helper('datatable_serverside');
        $this->load->helper('project');
        
        date_default_timezone_set('Asia/Jakarta');
        
         // untuk mengecek sesion sestiap ada request
        $is_validate = $this->session->userdata('lo9s1st3m');
        if (!$is_validate) {
            redirect(base_url() . 'auth', 'refresh');
        }
    }

    public function index($argv = '', $argv1 = '', $argv2 = '', $argv3 = '') {
        redirect('dashboard','refresh');
    }

    public function users($argv = '', $argv1 = '', $argv2 = '', $argv3 = '')
    {
        if($argv == ''){

            # config column, penamaan field yang akan ditampilkan (harus sesuai didatabase)
            $pageData['coloumns'] = [

                #<thead>        #field dari tabel database
                'No'            => 'no',
                'Username'      => 'username',
                'Nama'          => 'nama',
                'Email'         => 'email',
                'Jenis Kelamin' => 'jeniskelamin',
                'Departement'   => 'departement',
                'Foto'          => 'foto',
                'Hakakses'      => 'hakakses',
                'Aksi'          => 'pengguna_id',
            ];

            #size thead
            $pageData['size'] = ['2','100','100',false,'100','100','100','100','10'];

            #request ajax list data
            $pageData['url'] = base_url().'spadmin/users/list';

            # option parameter filter
            $pageData['dataFilter'] = array(
                array(
                    'nama_filter' => 'Pilih Jenis Kelamin',
                    'id_filter' => 'jeniskelamin',
                    'option_filter' => array(
                        array('id' => 'laki-laki', 'attr' => 'laki-laki'),
                        array('id' => 'perempuan', 'attr' => 'perempuan'),
                    )
                ),
                array(
                    'nama_filter' => 'Pilih Hakakses',
                    'id_filter' => 'is_admin',
                    'option_filter' => array(
                        array('id' => 'Yes', 'attr' => 'Administrator'),
                        array('id' => 'No', 'attr' => 'User'),
                    )
                ),
            );

            # data master yang dikirim
            $pageData['departments'] = $this->Md_departement->getAllDepartement();

            $pageData['page_name'] = 'view_users';
            $this->load->view('ui/spadmin/index', $pageData);

        }else if ($argv == 'list'){

            # receive parameter by method post
            $postData = $this->input->post();

            # sebutkan parameter filter didalam array (sesuaikan dengan field yang ditabel)
            $postData['filtering']=['jeniskelamin','is_admin'];

            # sebutkan parameter searching didalam array (sesuaikan dengan field yang ditabel)
            $postData['searching']=['nama','email'];

            # kirim parameter postData dan nama Model dari tabel ini (example : user , maka pake Md_users)
            $response = getDataTable($postData,'Md_users');

            $data=[];
            if($response['data']){
                $records = $response['data'];
                # memunculkan data dari value field yang ingin ditampilkan
                foreach ($records as $record) {

                    $record->foto = $record->foto ? $record->foto : 'default.png'; // foto
                    $record->is_admin = $record->is_admin == 'Yes' ? '<span class="bg-success">Administrator</span>' : '<span class="text-purple"> User </span>'; // hakakses

                    $data[] = [
                        "no"            => $record->no,
                        "username"      => $record->username,
                        "nama"          => $record->nama,
                        "email"         => $record->email,
                        "jeniskelamin"  => $record->jeniskelamin,
                        "departement"   => $record->departement,
                        "hakakses"      => $record->is_admin,
                        "foto"          => '<img src="'.base_url().'assets/uploads/foto/'.$record->foto.'" alt="User Avatar" class="img-size-50 mr-3 img-circle">',
                        "pengguna_id"   => '<a class="btn btn-primary" href="javascript:edit(\'' . encrypt($record->pengguna_id) . '\');">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a class="btn btn-danger" href="javascript:hapus(\'' . encrypt($record->pengguna_id) . '\');">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>',
                    ];
                }
            }

            $output = array_merge($response['meta'],["aaData" => $data]);
            
            echo json_encode($output);
            die;
        }else if ($argv == 'add'){
            $this->form_validation->set_rules('nama', 'field nama', 'required');
            $this->form_validation->set_rules('username', 'field username', 'required');
            $this->form_validation->set_rules('email', 'field email', 'required');
            $this->form_validation->set_rules('jeniskelamin', 'field jeniskelamin', 'required');
            $this->form_validation->set_rules('company', 'field company', 'required');
            $this->form_validation->set_rules('departement_id', 'field departement_id', 'required');

            if ($this->form_validation->run() != FALSE) {
                $nama = $this->input->post('nama');
                $username = $this->input->post('username');
                $email = $this->input->post('email');
                $jeniskelamin = $this->input->post('jeniskelamin') ? $this->input->post('jeniskelamin') : null;
                $company = $this->input->post('company');
                $tempatlahir = $this->input->post('tempatlahir') ? $this->input->post('tempatlahir') : null;
                $tgllahir = $this->input->post('tanggallahir');
                if($tgllahir){
                    $tgllahir = str_replace('/','-',$this->input->post('tanggallahir'));
                    $tgllahir = date('Y-m-d', strtotime($tgllahir));
                }else{
                    $tgllahir = null;
                }
                $departement_id = $this->input->post('departement_id') ? decrypt($this->input->post('departement_id')) : null;
                $posisi = $this->input->post('posisi') ? $this->input->post('posisi') : null;
                $agama = $this->input->post('agama') ? $this->input->post('agama') : null;
                $pendidikan = $this->input->post('pendidikan') ? $this->input->post('pendidikan') : null;
                $alamat = $this->input->post('alamat') ? $this->input->post('alamat') : null;
                $is_admin = $this->input->post('is_admin') ? 'No' : 'Yes';
                $password = password_hash('12345678', PASSWORD_DEFAULT); //password default

                $author = decrypt($this->session->userdata('pengguna_id'));
                $datetime = date('Y-m-d H:i:s');

                #upload foto
                if (!empty($_FILES['foto']['name'])) {
                    $config['upload_path'] = "./assets/uploads/foto";
                    $config['allowed_types'] = 'jpeg|jpg|png';
                    $config['encrypt_name'] = TRUE;
                    $config['max_size'] = '2480'; //in Kilobit => 2 Mb
                    $this->upload->initialize($config);
                    if ($this->upload->do_upload("foto")) {
                        $data = $this->upload->data();
                        $foto = $data['file_name'];
                        $filesize = round($_FILES['foto']['size']/1024); //in Kb
                    } else {
                        echo json_encode(array('status' => 'gagal', 'message' => 'Foto gagal di upload !'));
                        die;
                    }
                } else {
                    $foto = 'default.png';
                }

                $dataInsert = [
                    'nama'              => $nama,
                    'jeniskelamin'      => $jeniskelamin,
                    'email'             => strtolower($email),
                    'username'          => $username,
                    'foto'              => $foto,
                    'password'          => $password,
                    'company'           => $company,
                    'departement_id'    => $departement_id,
                    'position'          => $posisi,
                    'tempat_lahir'      => $tempatlahir,
                    'tgl_lahir'         => $tgllahir,
                    'pendidikan'        => $pendidikan,
                    'agama'             => $agama,
                    'alamat'            => $alamat,
                    'is_admin'          => $is_admin,
                    'status_active'     => 'Yes',
                    'author'            => $author,
                    'tglpost'           => $datetime,
                    'status'            => 1
                ];

                #cek data ini sudah ada apa belum
                $cek = $this->Md_users->getUserByUsernameOrEmail($username);
                $cek2 = $this->Md_users->getUserByUsernameOrEmail(strtolower($email));
                if(empty($cek) && empty($cek2)){

                    $this->db->trans_begin();
                    $id = $this->Md_users->addData($dataInsert);
    
                    # merekam log sistem
                    /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                    $keterangan = 'nama '.$nama.', jeniskelamin '.$jeniskelamin.', email '.$email.', username '.$username.', 
                                    foto '.$foto.', password '.$password.', company '.$company.', departement_id '.$departement_id.', 
                                    position '.$posisi.', tempat_lahir '.$tempatlahir.', tgl_lahir '.$tgllahir.', pendidikan '.$pendidikan.', 
                                    agama '.$agama.', alamat '.$alamat.', is_admin '.$is_admin;

                    addLog('Add Data', 'Pengguna', $keterangan);
    
                    if ($this->db->trans_status() == TRUE) {
                        $this->db->trans_commit();
                        echo json_encode(array('status' => 'success', 'message' => 'Data Berhasil disimpan ..'));
                        die;
                    } else {
                        $this->db->trans_rollback();
                        echo json_encode(array('status' => 'gagal', 'message' => 'Data Gagal disimpan !'));
                        die;
                    }

                }else{
                    echo json_encode(array('status' => 'invalid', 'message' => 'Data sudah ada !'));
                    die;
                }

            }else{
                echo json_encode(array('status' => 'invalid', 'message' => 'Semua fill wajib terisi !'));
                die;
            }
        }else if ($argv == 'edit'){
            $valid = $argv1 == '' ? FALSE : (is_int(decrypt($argv1)) ? TRUE : FALSE);
            if (!$valid) {
                echo json_encode(array('data' => FALSE));
                die;
            }

            $pengguna_id = decrypt($argv1);
            $data = $this->Md_users->getUserByPenggunaId($pengguna_id);

            $row['data'] = FALSE;
            if($data){
                $row['data'] = TRUE;
                $row['pengguna_id'] = encrypt($data->pengguna_id);
                $row['nama'] = $data->nama;
                $row['jeniskelamin'] = $data->jeniskelamin;
                $row['email'] = $data->email;
                $row['username'] = $data->username;
                $row['company'] = $data->company;
                $row['departement_id'] = encrypt($data->departement_id);
                $row['posisi'] = $data->position;
                $row['tanggallahir'] = $data->tgl_lahir ? date('d/m/Y',strtotime($data->tgl_lahir)) : null;
                $row['tempatlahir'] = $data->tempat_lahir;
                $row['pendidikan'] = $data->pendidikan;
                $row['agama'] = $data->agama;
                $row['alamat'] = $data->alamat;
                $row['is_admin'] = $data->is_admin;
            }

            echo json_encode($row);
            die;

        }else if ($argv == 'update'){
            $this->form_validation->set_rules('pengguna_id', 'field pengguna_id', 'required');
            $this->form_validation->set_rules('nama', 'field nama', 'required');
            $this->form_validation->set_rules('username', 'field username', 'required');
            $this->form_validation->set_rules('email', 'field email', 'required');
            $this->form_validation->set_rules('jeniskelamin', 'field jeniskelamin', 'required');
            $this->form_validation->set_rules('company', 'field company', 'required');
            $this->form_validation->set_rules('departement_id', 'field departement_id', 'required');

            if ($this->form_validation->run() != FALSE) {
                $pengguna_id = $this->input->post('pengguna_id') ? decrypt($this->input->post('pengguna_id')) : redirect(base_url());
                $nama = $this->input->post('nama');
                $username = $this->input->post('username');
                $email = $this->input->post('email');
                $jeniskelamin = $this->input->post('jeniskelamin') ? $this->input->post('jeniskelamin') : null;
                $company = $this->input->post('company');
                $tempatlahir = $this->input->post('tempatlahir') ? $this->input->post('tempatlahir') : null;
                $tgllahir = $this->input->post('tanggallahir');
                if($tgllahir){
                    $tgllahir = str_replace('/','-',$this->input->post('tanggallahir'));
                    $tgllahir = date('Y-m-d', strtotime($tgllahir));
                }else{
                    $tgllahir = null;
                }
                $departement_id = $this->input->post('departement_id') ? decrypt($this->input->post('departement_id')) : null;
                $posisi = $this->input->post('posisi') ? $this->input->post('posisi') : null;
                $agama = $this->input->post('agama') ? $this->input->post('agama') : null;
                $pendidikan = $this->input->post('pendidikan') ? $this->input->post('pendidikan') : null;
                $alamat = $this->input->post('alamat') ? $this->input->post('alamat') : null;
                $is_admin = $this->input->post('is_admin') ? 'No' : 'Yes';

                $author = decrypt($this->session->userdata('pengguna_id'));
                $datetime = date('Y-m-d H:i:s');

                #data sebelumnya
                $dataUsers = $this->Md_users->getUserByPenggunaId($pengguna_id);

                #upload foto
                if (!empty($_FILES['foto']['name'])) {
                    $config['upload_path'] = "./assets/uploads/foto";
                    $config['allowed_types'] = 'jpeg|jpg|png';
                    $config['encrypt_name'] = TRUE;
                    $config['max_size'] = '2480'; //in Kilobit => 2 Mb
                    $this->upload->initialize($config);
                    if ($this->upload->do_upload("foto")) {
                        $data = $this->upload->data();
                        $foto = $data['file_name'];
                        $filesize = round($_FILES['foto']['size']/1024); //in Kb
                    } else {
                        echo json_encode(array('status' => 'gagal', 'message' => 'Foto gagal di upload !'));
                        die;
                    }
                } else {
                    $foto = $dataUsers->foto;
                }

                $dataUpdate = [
                    'nama'              => $nama,
                    'jeniskelamin'      => $jeniskelamin,
                    'email'             => $email,
                    'username'          => $username,
                    'foto'              => $foto,
                    'company'           => $company,
                    'departement_id'    => $departement_id,
                    'position'          => $posisi,
                    'tempat_lahir'      => $tempatlahir,
                    'tgl_lahir'         => $tgllahir,
                    'pendidikan'        => $pendidikan,
                    'agama'             => $agama,
                    'alamat'            => $alamat,
                    'is_admin'          => $is_admin,
                    'author'            => $author,
                    'tglpost'           => $datetime,
                    'status'            => 1
                ];

                #cek data ini sudah ada apa belum
                $cek = $this->Md_users->getUserByUsername($username, $pengguna_id);
                $cek2 = $this->Md_users->getUserByEmail($email, $pengguna_id);

                if(empty($cek) && empty($cek2)){

                    $this->db->trans_begin();
                    $this->Md_users->updateData($pengguna_id, $dataUpdate);
    
                    # merekam log sistem
                    /* contoh : addLog('Update Data', 'nama tabel', 'keterangan') */
                    $keterangan = 'nama '.$nama.', jeniskelamin '.$jeniskelamin.', email '.$email.', username '.$username.', 
                                    foto '.$foto.', company '.$company.', departement_id '.$departement_id.', 
                                    position '.$posisi.', tempat_lahir '.$tempatlahir.', tgl_lahir '.$tgllahir.', pendidikan '.$pendidikan.', 
                                    agama '.$agama.', alamat '.$alamat.', is_admin '.$is_admin;
                    addLog('Update Data', 'Pengguna', $keterangan);
    
                    if ($this->db->trans_status() == TRUE) {
                        $this->db->trans_commit();
                        echo json_encode(array('status' => 'success', 'message' => 'Data Berhasil disimpan ..'));
                        die;
                    } else {
                        $this->db->trans_rollback();
                        echo json_encode(array('status' => 'gagal', 'message' => 'Data Gagal disimpan !'));
                        die;
                    }

                }else{
                    echo json_encode(array('status' => 'invalid', 'message' => 'Data sudah ada !'));
                    die;
                }

            }else{
                echo json_encode(array('status' => 'invalid', 'message' => 'Semua fill wajib terisi !'));
                die;
            }
        }else if ($argv == 'hapus'){
            $valid = $argv1 == '' ? FALSE : (is_int(decrypt($argv1)) ? TRUE : FALSE);
            if (!$valid) {
                echo json_encode(array('data' => FALSE));
                die;
            }

            $this->db->trans_begin();
            $pengguna_id = decrypt($argv1);
            $this->Md_users->updateData($pengguna_id, ['status'=>2]);

            # merekam log sistem
            /* contoh : addLog('Delete Data', 'nama tabel', 'keterangan') */
            addLog('Delete Data', 'Pengguna', 'Pengguna_id ' .$pengguna_id);

            if ($this->db->trans_status() == TRUE) {
                $this->db->trans_commit();
                echo json_encode(array('data' => true));
                die;
            } else {
                $this->db->trans_rollback();
                echo json_encode(array('data' => false));
                die;
            }

        }else{
            redirect('dashboard/page_404');
        }
    }

    public function departement($argv = '', $argv1 = '', $argv2 = '', $argv3 = '')
    {
        if($argv == ''){

            # config column, penamaan field yang akan ditampilkan (harus sesuai didatabase)
            $pageData['coloumns'] = [
                #<thead>        #field dari tabel database
                'No'            => 'no',
                'Departement'   => 'departement',
                'Action'        => 'departement_id'
            ];

            #size thead
            $pageData['size'] = ['10',false,'100'];

            # option parameter filter
            $pageData['dataFilter'] = array();

            #request ajax list data
            $pageData['url'] = base_url().'spadmin/departement/list';

            $pageData['page_name'] = 'view_departement';
            $this->load->view('ui/spadmin/index', $pageData);

        }else if ($argv == 'list'){

            # receive parameter by method post
            $postData = $this->input->post();

            # sebutkan parameter filter didalam array (sesuaikan dengan field yang ditabel)
            $postData['filtering']=[];

            # sebutkan parameter searching didalam array (sesuaikan dengan field yang ditabel)
            $postData['searching']=['departement'];

            # kirim parameter postData dan nama Model dari tabel ini (example : user , maka pake Md_users)
            $response = getDataTable($postData,'Md_departement');

            $data=[];
            if($response['data']){
                $records = $response['data'];
                # memunculkan data dari value field yang ingin ditampilkan
                foreach ($records as $record) {
                    $data[] = [
                        "no"             => $record->no,
                        "departement"    => $record->departement,
                        "departement_id" => '<a class="btn btn-primary" href="javascript:edit(\'' . encrypt($record->departement_id) . '\');">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a class="btn btn-danger" href="javascript:hapus(\'' . encrypt($record->departement_id) . '\');">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>',
                    ];
                }
            }

            $output = array_merge($response['meta'],["aaData" => $data]);
            
            echo json_encode($output);
            die;
        }else if ($argv == 'add'){
            $this->form_validation->set_rules('departement', 'field departement', 'required');

            if ($this->form_validation->run() != FALSE) {
                $departement = $this->input->post('departement');
                $author = decrypt($this->session->userdata('pengguna_id'));
                $datetime = date('Y-m-d H:i:s');

                $dataInsert = [
                    'departement'   => $departement,
                    'author'        => $author,
                    'tglpost'       => $datetime,
                    'status'        => 1
                ];

                #cek data ini sudah ada apa belum
                $cek = $this->Md_departement->getDepartementByDepartement($departement);
                if(empty($cek)){

                    $this->db->trans_begin();
                    $id = $this->Md_departement->addData($dataInsert);
    
                    # merekam log sistem
                    /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                    addLog('Add Data', 'Departement', 'Departement_id ' .$id.' departement '.$departement);
    
                    if ($this->db->trans_status() == TRUE) {
                        $this->db->trans_commit();
                        echo json_encode(array('status' => 'success', 'message' => 'Data Berhasil disimpan ..'));
                        die;
                    } else {
                        $this->db->trans_rollback();
                        echo json_encode(array('status' => 'gagal', 'message' => 'Data Gagal disimpan !'));
                        die;
                    }

                }else{
                    echo json_encode(array('status' => 'invalid', 'message' => 'Data sudah ada !'));
                    die;
                }

            }else{
                echo json_encode(array('status' => 'invalid', 'message' => 'Semua fill wajib terisi !'));
                die;
            }
        }else if ($argv == 'edit'){
            $valid = $argv1 == '' ? FALSE : (is_int(decrypt($argv1)) ? TRUE : FALSE);
            if (!$valid) {
                echo json_encode(array('data' => FALSE));
                die;
            }

            $departement_id = decrypt($argv1);
            $data = $this->Md_departement->getDepartementByDepartementId($departement_id);

            $row['data'] = FALSE;
            if($data){
                $row['data'] = TRUE;
                $row['departement_id'] = encrypt($data->departement_id);
                $row['departement'] = $data->departement;
            }

            echo json_encode($row);
            die;

        }else if ($argv == 'update'){
            $this->form_validation->set_rules('departement_id', 'field departement_id', 'required');
            $this->form_validation->set_rules('departement', 'field departement', 'required');

            if ($this->form_validation->run() != FALSE) {
                $departement_id = decrypt($this->input->post('departement_id'));
                $departement = $this->input->post('departement');

                $dataUpdate = [
                    'departement'   => $departement,
                ];

                #cek data ini sudah ada apa belum
                $cek = $this->Md_departement->getDepartementByDepartement($departement);
                $update = false;
                if($cek){
                    if($cek->departement_id == $departement_id){
                        $update = true;
                    }
                }else{
                    $update = true;
                }

                if($update){

                    $this->db->trans_begin();
                    $this->Md_departement->updateData($departement_id, $dataUpdate);
    
                    # merekam log sistem
                    /* contoh : addLog('Update Data', 'nama tabel', 'keterangan') */
                    addLog('Update Data', 'Departement', 'Departement_id ' .$departement_id.' departement '.$departement);
    
                    if ($this->db->trans_status() == TRUE) {
                        $this->db->trans_commit();
                        echo json_encode(array('status' => 'success', 'message' => 'Data Berhasil disimpan ..'));
                        die;
                    } else {
                        $this->db->trans_rollback();
                        echo json_encode(array('status' => 'gagal', 'message' => 'Data Gagal disimpan !'));
                        die;
                    }

                }else{
                    echo json_encode(array('status' => 'invalid', 'message' => 'Data sudah ada !'));
                    die;
                }

            }else{
                echo json_encode(array('status' => 'invalid', 'message' => 'Semua fill wajib terisi !'));
                die;
            }
        }else if ($argv == 'hapus'){
            $valid = $argv1 == '' ? FALSE : (is_int(decrypt($argv1)) ? TRUE : FALSE);
            if (!$valid) {
                echo json_encode(array('data' => FALSE));
                die;
            }

            $this->db->trans_begin();
            $departement_id = decrypt($argv1);
            $this->Md_departement->updateData($departement_id, ['status'=>2]);

            # merekam log sistem
            /* contoh : addLog('Delete Data', 'nama tabel', 'keterangan') */
            addLog('Delete Data', 'Departement', 'Departement_id ' .$departement_id);

            if ($this->db->trans_status() == TRUE) {
                $this->db->trans_commit();
                echo json_encode(array('data' => true));
                die;
            } else {
                $this->db->trans_rollback();
                echo json_encode(array('data' => false));
                die;
            }

        }else{
            redirect('dashboard/page_404');
        }
    }

    public function boards($argv = '', $argv1 = '', $argv2 = '', $argv3 = '')
    {
        if($argv == ''){

            # config column, penamaan field yang akan ditampilkan (harus sesuai didatabase)
            $pageData['coloumns'] = [
                #<thead>        #field dari tabel database
                'No'            => 'no',
                'Board'         => 'board',
                'Color'         => 'color',
                'Action'        => 'board_id'
            ];

            #size thead
            $pageData['size'] = ['10',false,'100','100'];

            #request ajax list data
            $pageData['url'] = base_url().'spadmin/boards/list';

            # option parameter filter
            $pageData['dataFilter'] = array();

            $pageData['page_name'] = 'view_board';
            $this->load->view('ui/spadmin/index', $pageData);

        }else if ($argv == 'list'){

            # receive parameter by method post
            $postData = $this->input->post();

            # sebutkan parameter filter didalam array (sesuaikan dengan field yang ditabel)
            $postData['filtering']=[];

            # sebutkan parameter searching didalam array (sesuaikan dengan field yang ditabel)
            $postData['searching']=['board'];

            # kirim parameter postData dan nama Model dari tabel ini (example : board , maka pake Md_bard)
            $response = getDataTable($postData,'Md_board');

            $data=[];
            if($response['data']){
                $records = $response['data'];
                # memunculkan data dari value field yang ingin ditampilkan
                foreach ($records as $record) {
                    $data[] = [
                        "no"             => $record->no,
                        "board"          => $record->board,
                        "color"          => "<span class='btn' style='background-color : $record->color'> $record->color </span>",
                        "board_id"       => '<a class="btn btn-primary" href="javascript:edit(\'' . encrypt($record->board_id) . '\');">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a class="btn btn-danger" href="javascript:hapus(\'' . encrypt($record->board_id) . '\');">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>',
                    ];
                }
            }

            $output = array_merge($response['meta'],["aaData" => $data]);
            
            echo json_encode($output);
            die;
        }else if ($argv == 'add'){
            $this->form_validation->set_rules('board', 'field board', 'required');
            $this->form_validation->set_rules('color', 'field color', 'required');

            if ($this->form_validation->run() != FALSE) {
                $board = $this->input->post('board');
                $color = $this->input->post('color');
                $author = decrypt($this->session->userdata('pengguna_id'));
                $datetime = date('Y-m-d H:i:s');

                $dataInsert = [
                    'board'         => $board,
                    'color'         => $color,
                    'author'        => $author,
                    'tglpost'       => $datetime,
                    'status'        => 1
                ];

                #cek data ini sudah ada apa belum
                $cek = $this->Md_board->getBoardByBoard($board);
                if(empty($cek)){

                    $this->db->trans_begin();
                    $id = $this->Md_board->addData($dataInsert);
    
                    # merekam log sistem
                    /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                    addLog('Add Data', 'Board', 'board_id ' .$id.' board '.$board.' color '.$color);
    
                    if ($this->db->trans_status() == TRUE) {
                        $this->db->trans_commit();
                        echo json_encode(array('status' => 'success', 'message' => 'Data Berhasil disimpan ..'));
                        die;
                    } else {
                        $this->db->trans_rollback();
                        echo json_encode(array('status' => 'gagal', 'message' => 'Data Gagal disimpan !'));
                        die;
                    }

                }else{
                    echo json_encode(array('status' => 'invalid', 'message' => 'Data sudah ada !'));
                    die;
                }

            }else{
                echo json_encode(array('status' => 'invalid', 'message' => 'Semua fill wajib terisi !'));
                die;
            }
        }else if ($argv == 'edit'){
            $valid = $argv1 == '' ? FALSE : (is_int(decrypt($argv1)) ? TRUE : FALSE);
            if (!$valid) {
                echo json_encode(array('data' => FALSE));
                die;
            }

            $board_id = decrypt($argv1);
            $data = $this->Md_board->getBoardByBoardId($board_id);

            $row['data'] = FALSE;
            if($data){
                $row['data'] = TRUE;
                $row['board_id'] = encrypt($data->board_id);
                $row['board'] = $data->board;
                $row['color'] = $data->color;
            }

            echo json_encode($row);
            die;

        }else if ($argv == 'update'){
            $this->form_validation->set_rules('board_id', 'field board_id', 'required');
            $this->form_validation->set_rules('board', 'field board', 'required');
            $this->form_validation->set_rules('color', 'field color', 'required');

            if ($this->form_validation->run() != FALSE) {
                $board_id = decrypt($this->input->post('board_id'));
                $board = $this->input->post('board');
                $color = $this->input->post('color');

                $dataUpdate = [
                    'board'   => $board,
                    'color'   => $color,
                ];

                #cek data ini sudah ada apa belum
                $cek = $this->Md_board->getBoardByBoard($board);
                $update = false;
                if($cek){
                    if($cek->board_id == $board_id){
                        $update = true;
                    }
                }else{
                    $update = true;
                }

                if($update){

                    $this->db->trans_begin();
                    $this->Md_board->updateData($board_id, $dataUpdate);
    
                    # merekam log sistem
                    /* contoh : addLog('Update Data', 'nama tabel', 'keterangan') */
                    addLog('Update Data', 'Board', 'Board_id ' .$board_id.' board '.$board.' color '.$color);
    
                    if ($this->db->trans_status() == TRUE) {
                        $this->db->trans_commit();
                        echo json_encode(array('status' => 'success', 'message' => 'Data Berhasil disimpan ..'));
                        die;
                    } else {
                        $this->db->trans_rollback();
                        echo json_encode(array('status' => 'gagal', 'message' => 'Data Gagal disimpan !'));
                        die;
                    }

                }else{
                    echo json_encode(array('status' => 'invalid', 'message' => 'Data sudah ada !'));
                    die;
                }

            }else{
                echo json_encode(array('status' => 'invalid', 'message' => 'Semua fill wajib terisi !'));
                die;
            }
        }else if ($argv == 'hapus'){
            $valid = $argv1 == '' ? FALSE : (is_int(decrypt($argv1)) ? TRUE : FALSE);
            if (!$valid) {
                echo json_encode(array('data' => FALSE));
                die;
            }

            $this->db->trans_begin();
            $board_id = decrypt($argv1);
            $this->Md_board->updateData($board_id, ['status'=>2]);

            # merekam log sistem
            /* contoh : addLog('Delete Data', 'nama tabel', 'keterangan') */
            addLog('Delete Data', 'Board', 'Board_id ' .$board_id);

            if ($this->db->trans_status() == TRUE) {
                $this->db->trans_commit();
                echo json_encode(array('data' => true));
                die;
            } else {
                $this->db->trans_rollback();
                echo json_encode(array('data' => false));
                die;
            }

        }else{
            redirect('dashboard/page_404');
        }
    }

    public function label($argv = '', $argv1 = '', $argv2 = '', $argv3 = '')
    {
        if($argv == ''){

            # config column, penamaan field yang akan ditampilkan (harus sesuai didatabase)
            $pageData['coloumns'] = [
                #<thead>        #field dari tabel database
                'No'            => 'no',
                'Label'         => 'label',
                'Color'         => 'color',
                'Action'        => 'label_id'
            ];

            #size thead
            $pageData['size'] = ['10',false,'100','100'];

            #request ajax list data
            $pageData['url'] = base_url().'spadmin/label/list';

            # option parameter filter
            $pageData['dataFilter'] = array();

            $pageData['page_name'] = 'view_label';
            $this->load->view('ui/spadmin/index', $pageData);

        }else if ($argv == 'list'){

            # receive parameter by method post
            $postData = $this->input->post();

            # sebutkan parameter filter didalam array (sesuaikan dengan field yang ditabel)
            $postData['filtering']=[];

            # sebutkan parameter searching didalam array (sesuaikan dengan field yang ditabel)
            $postData['searching']=['board'];

            # kirim parameter postData dan nama Model dari tabel ini (example : label , maka pake Md_label)
            $response = getDataTable($postData,'Md_label');

            $data=[];
            if($response['data']){
                $records = $response['data'];
                # memunculkan data dari value field yang ingin ditampilkan
                foreach ($records as $record) {
                    $data[] = [
                        "no"             => $record->no,
                        "label"          => $record->label,
                        "color"          => "<span class='btn' style='background-color : $record->color'> $record->color </span>",
                        "label_id"       => '<a class="btn btn-primary" href="javascript:edit(\'' . encrypt($record->label_id) . '\');">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a class="btn btn-danger" href="javascript:hapus(\'' . encrypt($record->label_id) . '\');">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>',
                    ];
                }
            }

            $output = array_merge($response['meta'],["aaData" => $data]);
            
            echo json_encode($output);
            die;
        }else if ($argv == 'add'){
            $this->form_validation->set_rules('label', 'field label', 'required');
            $this->form_validation->set_rules('color', 'field color', 'required');

            if ($this->form_validation->run() != FALSE) {
                $label = $this->input->post('label');
                $color = $this->input->post('color');
                $author = decrypt($this->session->userdata('pengguna_id'));
                $datetime = date('Y-m-d H:i:s');

                $dataInsert = [
                    'label'         => $label,
                    'color'         => $color,
                    'author'        => $author,
                    'tglpost'       => $datetime,
                    'status'        => 1
                ];

                #cek data ini sudah ada apa belum
                $cek = $this->Md_label->getLabelByLabel($label);
                if(empty($cek)){

                    $this->db->trans_begin();
                    $id = $this->Md_label->addData($dataInsert);
    
                    # merekam log sistem
                    /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                    addLog('Add Data', 'Label', 'label_id ' .$id.' label '.$label.' color '.$color);
    
                    if ($this->db->trans_status() == TRUE) {
                        $this->db->trans_commit();
                        echo json_encode(array('status' => 'success', 'message' => 'Data Berhasil disimpan ..'));
                        die;
                    } else {
                        $this->db->trans_rollback();
                        echo json_encode(array('status' => 'gagal', 'message' => 'Data Gagal disimpan !'));
                        die;
                    }

                }else{
                    echo json_encode(array('status' => 'invalid', 'message' => 'Data sudah ada !'));
                    die;
                }

            }else{
                echo json_encode(array('status' => 'invalid', 'message' => 'Semua fill wajib terisi !'));
                die;
            }
        }else if ($argv == 'edit'){
            $valid = $argv1 == '' ? FALSE : (is_int(decrypt($argv1)) ? TRUE : FALSE);
            if (!$valid) {
                echo json_encode(array('data' => FALSE));
                die;
            }

            $label_id = decrypt($argv1);
            $data = $this->Md_label->getLabelByLabelid($label_id);

            $row['data'] = FALSE;
            if($data){
                $row['data'] = TRUE;
                $row['label_id'] = encrypt($data->label_id);
                $row['label'] = $data->label;
                $row['color'] = $data->color;
            }

            echo json_encode($row);
            die;

        }else if ($argv == 'update'){
            $this->form_validation->set_rules('label_id', 'field label_id', 'required');
            $this->form_validation->set_rules('label', 'field label', 'required');
            $this->form_validation->set_rules('color', 'field color', 'required');

            if ($this->form_validation->run() != FALSE) {
                $label_id = decrypt($this->input->post('label_id'));
                $label = $this->input->post('label');
                $color = $this->input->post('color');

                $dataUpdate = [
                    'label'   => $label,
                    'color'   => $color,
                ];

                #cek data ini sudah ada apa belum
                $cek = $this->Md_label->getLabelByLabel($label);
                $update = false;
                if($cek){
                    if($cek->label_id == $label_id){
                        $update = true;
                    }
                }else{
                    $update = true;
                }

                if($update){

                    $this->db->trans_begin();
                    $this->Md_label->updateData($label_id, $dataUpdate);
    
                    # merekam log sistem
                    /* contoh : addLog('Update Data', 'nama tabel', 'keterangan') */
                    addLog('Update Data', 'Label', 'label_id ' .$label_id.' label '.$label.' color '.$color);
    
                    if ($this->db->trans_status() == TRUE) {
                        $this->db->trans_commit();
                        echo json_encode(array('status' => 'success', 'message' => 'Data Berhasil disimpan ..'));
                        die;
                    } else {
                        $this->db->trans_rollback();
                        echo json_encode(array('status' => 'gagal', 'message' => 'Data Gagal disimpan !'));
                        die;
                    }

                }else{
                    echo json_encode(array('status' => 'invalid', 'message' => 'Data sudah ada !'));
                    die;
                }

            }else{
                echo json_encode(array('status' => 'invalid', 'message' => 'Semua fill wajib terisi !'));
                die;
            }
        }else if ($argv == 'hapus'){
            $valid = $argv1 == '' ? FALSE : (is_int(decrypt($argv1)) ? TRUE : FALSE);
            if (!$valid) {
                echo json_encode(array('data' => FALSE));
                die;
            }

            $this->db->trans_begin();
            $label_id = decrypt($argv1);
            $this->Md_label->updateData($label_id, ['status'=>2]);

            # merekam log sistem
            /* contoh : addLog('Delete Data', 'nama tabel', 'keterangan') */
            addLog('Delete Data', 'label', 'label_id ' .$label_id);

            if ($this->db->trans_status() == TRUE) {
                $this->db->trans_commit();
                echo json_encode(array('data' => true));
                die;
            } else {
                $this->db->trans_rollback();
                echo json_encode(array('data' => false));
                die;
            }

        }else{
            redirect('dashboard/page_404');
        }
    }

    public function workspaces($argv = '', $argv1 = '', $argv2 = '', $argv3 = ''){

        if($argv == ''){
            $pageData['workspaces'] = $this->Md_workspace->getAllworkspace();
            $pageData['departements'] = $this->Md_departement->getAlldepartement();
            $pageData['unitkerjas'] = $this->Md_unitkerja->getAllunitkerja();
            $pageData['users'] = $this->Md_users->getAllUserActive();
            $pageData['boards'] = $this->Md_board->getAllBoard();
            $pageData['page_name'] = 'view_workspaces';
            
            $this->load->view('ui/spadmin/index', $pageData);

        }else if ($argv == 'addMember'){
            $valid = $argv1 == '' ? FALSE : (is_int(decrypt($argv1)) ? TRUE : FALSE);
            if (!$valid) {
                echo json_encode(array('data' => FALSE));
                die;
            }

            $pengguna_id = decrypt($argv1);
            $data = $this->Md_users->getUserByPenggunaId($pengguna_id);

            $row['data'] = FALSE;
            if($data){
                $row['data'] = TRUE;
                $row['pengguna_id'] = encrypt($data->pengguna_id);
                $row['nama'] = $data->nama;
            }

            echo json_encode($row);
            die;
        }else if ($argv == 'addTeamDepartement'){

            $valid = $argv1 == '' ? FALSE : (is_int(decrypt($argv1)) ? TRUE : FALSE);
            if (!$valid) {
                echo json_encode(array('data' => FALSE));
                die;
            }

            $departement_id = decrypt($argv1);
            $data = $this->Md_users->getUserByDepartementid($departement_id);

            $row['data'] = FALSE;
            if($data){
                $row['data'] = TRUE;
                $row['pengguna_id'][] = encrypt($data->pengguna_id);
                $row['nama'][] = $data->nama;
            }

            echo json_encode($row);
            die;
        }else if ($argv == 'add'){
            $this->form_validation->set_rules('workspace', 'field workspace', 'required');
            $this->form_validation->set_rules('board_id[]', 'field board_id[]', 'required');
            $this->form_validation->set_rules('pengguna_id[]', 'field pengguna_id[]', 'required');
            $this->form_validation->set_rules('color', 'field color', 'required');
            $this->form_validation->set_rules('createdby', 'field createdby', 'required');

            if ($this->form_validation->run() != FALSE) {
                $workspace = $this->input->post('workspace');
                $color = $this->input->post('color');
                $deskripsi = $this->input->post('deskripsi');
                $departement_id = $this->input->post('departement_id') ? decrypt($this->input->post('departement_id')) : null;
                $unitkerja_id = $this->input->post('unitkerja_id') ? decrypt($this->input->post('unitkerja_id')) : null;
                $board_id = $this->input->post('board_id') ? array_map('decrypt', $this->input->post('board_id')) : die();
                $pengguna_id = $this->input->post('pengguna_id') ? array_map('decrypt', $this->input->post('pengguna_id')) : die();
                $createdby = $this->input->post('createdby') ? decrypt($this->input->post('createdby')) : die();

                $author = decrypt($this->session->userdata('pengguna_id'));
                $datetime = date('Y-m-d H:i:s');

                #upload thumbnail
                if (!empty($_FILES['file']['name'])) {
                    $config['upload_path'] = "./assets/uploads/file";
                    $config['allowed_types'] = 'jpeg|jpg|png';
                    $config['encrypt_name'] = TRUE;
                    $config['max_size'] = '2480'; //in Kilobit => 2 Mb
                    $this->upload->initialize($config);
                    if ($this->upload->do_upload("file")) {
                        $data = $this->upload->data();
                        $thumbnail = $data['file_name'];
                        $size = round($_FILES['file']['size']/1024); //in Kb
                    } else {
                        echo json_encode(array('status' => 'gagal', 'message' => 'Foto gagal di upload !'));
                        die;
                    }
                } else {
                    $thumbnail = 'thumbnail.jpg';
                }

                # insert workspaces
                $dataInsert = [
                    'nm_workspace'   => $workspace,
                    'description'    => $deskripsi,
                    'color'          => $color,
                    'thumbnail'      => $thumbnail,
                    'departement_id' => $departement_id,
                    'unitkerja_id'   => $unitkerja_id,
                    'author'         => $author,
                    'created'        => $createdby,
                    'tglpost'        => $datetime,
                    'status'         => 1
                ];

                #cek data ini sudah ada apa belum
                $cek = $this->Md_workspace->getWorkspaceByWorkspaceAndDepartement($workspace,$departement_id);
                if(empty($cek)){

                    $this->db->trans_begin();
                    $id = $this->Md_workspace->addData($dataInsert);
    
                    # merekam log sistem
                    /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                    addLog('Add Data', 'Workspace', 'Workspaces ' .$workspace.' deskripsi '.$deskripsi.' color '.$color);

                    # insert board relation
                    if($board_id){
                        $dataInsert=[];
                        foreach($board_id as $list){
                            $dataInsert[] = [
                                'workspace_id'  => $id,
                                'board_id'      => $list,
                                'author'        => $author,
                                'tglpost'       => $datetime,
                                'status'        => 1
                            ];
                        }

                        if($dataInsert){
                            $this->Md_board_relation->addDatas($dataInsert);

                            for($i=0; $i<count($dataInsert); $i++){
                                # merekam log sistem
                                /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                                addLog('Add Data', 'Board Relation', 'workspace_id' .$id.' Board_id '.$dataInsert[$i]['board_id']);
                            }
                        }
                    }

                    # push created by to pengguna id
                    if(in_array($createdby,$pengguna_id) == false){
                        array_push($pengguna_id,$createdby);
                    }

                    # insert member
                    if($pengguna_id){
                        $dataInsert=[];
                        foreach($pengguna_id as $list){
                            $dataInsert[] = [
                                'workspace_id'  => $id,
                                'pengguna_id'   => $list,
                                'author'        => $author,
                                'tglpost'       => $datetime,
                                'status'        => 1
                            ];
                        }

                        if($dataInsert){
                            $this->Md_member->addDatas($dataInsert);

                            for($i=0; $i<count($dataInsert); $i++){
                                # merekam log sistem
                                /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                                addLog('Add Data', 'Member', 'workspace_id' .$id.' pengguna_id '.$dataInsert[$i]['pengguna_id']);
                            }
                        }

                    }

    
                    if ($this->db->trans_status() == TRUE) {
                        $this->db->trans_commit();
                        echo json_encode(array('status' => 'success', 'message' => 'Data Berhasil disimpan ..'));
                        die;
                    } else {
                        $this->db->trans_rollback();
                        echo json_encode(array('status' => 'gagal', 'message' => 'Data Gagal disimpan !'));
                        die;
                    }

                }else{
                    echo json_encode(array('status' => 'invalid', 'message' => 'Data sudah ada !'));
                    die;
                }

            }else{
                echo json_encode(array('status' => 'invalid', 'message' => 'Semua fill wajib terisi !'));
                die;
            }
        }else if ($argv == 'edit'){
            $valid = $argv1 == '' ? FALSE : (is_int(decrypt($argv1)) ? TRUE : FALSE);
            if (!$valid) {
                echo json_encode(array('data' => FALSE));
                die;
            }

            $workspace_id = decrypt($argv1);
            $data = $this->Md_workspace->getWorkspaceById($workspace_id);

            $row['data'] = FALSE;
            if($data){
                $row['data'] = TRUE;
                $row['workspace_id'] = encrypt($data->workspace_id);
                $row['workspace'] = $data->nm_workspace;
                $row['description'] = $data->description;
                $row['thumbnail'] = $data->thumbnail;
                $row['color'] = $data->color;
                $row['createdby'] = encrypt($data->created);
                $row['departement_id'] = encrypt($data->departement_id);
                $row['unitkerja_id'] = encrypt($data->unitkerja_id);
                #data board
                $dataBoard = $this->Md_board_relation->getBoardrelationByWorkspaceid($data->workspace_id);
                if($dataBoard){
                    foreach($dataBoard as $list){
                        $row['board_id'][] = encrypt($list->board_id);
                        $row['board'][] = $list->board;
                    }
                }else{
                    $row['data'] = FALSE;
                }

                #data member
                $dataMember = $this->Md_member->getMemberByWorkspaceid($data->workspace_id);
                if($dataMember){
                    foreach($dataMember as $list){
                        $row['pengguna_id'][] = encrypt($list->pengguna_id);
                        $row['nama'][] = $list->nama;
                        $row['email'][] = $list->email;
                    }
                }else{
                    $row['data'] = FALSE;
                }
            }

            echo json_encode($row);
            die;
        }else if ($argv == 'update'){
            $this->form_validation->set_rules('workspace_id', 'field workspace_id', 'required');
            $this->form_validation->set_rules('workspace', 'field workspace', 'required');
            $this->form_validation->set_rules('board_id[]', 'field board_id[]', 'required');
            $this->form_validation->set_rules('pengguna_id[]', 'field pengguna_id[]', 'required');
            $this->form_validation->set_rules('color', 'field color', 'required');
            $this->form_validation->set_rules('createdby', 'field createdby', 'required');

            if ($this->form_validation->run() != FALSE) {
                $workspace_id = decrypt($this->input->post('workspace_id'));
                $workspace = $this->input->post('workspace');
                $color = $this->input->post('color');
                $deskripsi = $this->input->post('deskripsi');
                $departement_id = $this->input->post('departement_id') ? decrypt($this->input->post('departement_id')) : null;
                $unitkerja_id = $this->input->post('unitkerja_id') ? decrypt($this->input->post('unitkerja_id')) : null;
                $board_id = $this->input->post('board_id') ? array_map('decrypt', $this->input->post('board_id')) : die();
                $pengguna_id = $this->input->post('pengguna_id') ? array_map('decrypt', $this->input->post('pengguna_id')) : die();
                $createdby = $this->input->post('createdby') ? decrypt($this->input->post('createdby')) : die();

                $author = decrypt($this->session->userdata('pengguna_id'));
                $datetime = date('Y-m-d H:i:s');

                #upload thumbnail
                if (!empty($_FILES['file']['name'])) {
                    $config['upload_path'] = "./assets/uploads/file";
                    $config['allowed_types'] = 'jpeg|jpg|png';
                    $config['encrypt_name'] = TRUE;
                    $config['max_size'] = '2480'; //in Kilobit => 2 Mb
                    $this->upload->initialize($config);
                    if ($this->upload->do_upload("file")) {
                        $data = $this->upload->data();
                        $thumbnail = $data['file_name'];
                        $size = round($_FILES['file']['size']/1024); //in Kb
                    } else {
                        echo json_encode(array('status' => 'gagal', 'message' => 'Foto gagal di upload !'));
                        die;
                    }
                } else {
                    #cek data thumbnail di database
                    $cekData = $this->Md_workspace->getWorkspaceById($workspace_id);
                    if(isset($cekData->thumbnail)){
                        if($cekData->thumbnail != ""){
                            $thumbnail = $cekData->thumbnail;
                        }else{
                            $thumbnail = 'thumbnail.jpg';
                        }
                    }
                }

                # update workspaces
                $dataUpdate = [
                    'nm_workspace'   => $workspace,
                    'description'    => $deskripsi,
                    'color'          => $color,
                    'thumbnail'      => $thumbnail,
                    'departement_id' => $departement_id,
                    'unitkerja_id' => $unitkerja_id,
                    'author'         => $author,
                    'created'        => $createdby,
                ];

                #cek data ini sudah ada apa belum // kecuali id yang saat ini 
                $cek = $this->Md_workspace->getWorkspaceByWorkspaceAndDepartement($workspace,$departement_id,$workspace_id);
                if(empty($cek)){

                    $this->db->trans_begin();
                    $id = $this->Md_workspace->updateData($workspace_id, $dataUpdate);
    
                    # merekam log sistem
                    /* contoh : addLog('Update Data', 'nama tabel', 'keterangan') */
                    addLog('Update Data', 'Workspace', 'Workspaces ' .$workspace.' deskripsi '.$deskripsi.' color '.$color);

                    # insert board relation
                    if($board_id){
                        #cek data board relation di database
                        $cekData = $this->Md_board_relation->getBoardrelationByWorkspaceid($workspace_id);
                        if($cekData){
                            $recordBoard = array_column($cekData,'board_id');
                            $board_relation_id = array_column($cekData,'board_relation_id');
                        }

                        $dataInsert=[];
                        foreach($board_id as $list){
                            // insert baru
                            if(in_array($list,$recordBoard)==false){
                                $dataInsert[] = [
                                    'workspace_id'  => $workspace_id,
                                    'board_id'      => $list,
                                    'author'        => $author,
                                    'tglpost'       => $datetime,
                                    'status'        => 1
                                ];
                            }
                        }

                        $dataUpdate=[];
                        foreach($recordBoard as $index=>$list){
                            // update status 2 => hapus
                            if(in_array($list,$board_id)==false){
                                $dataUpdate[] = [
                                    'board_relation_id'  => $board_relation_id[$index],
                                    'status'             => 2
                                ];
                            }
                        }

                        if($dataInsert){
                            $this->Md_board_relation->addDatas($dataInsert);

                            for($i=0; $i<count($dataInsert); $i++){
                                # merekam log sistem
                                /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                                addLog('Add Data', 'Board Relation', 'workspace_id' .$id.' Board_id '.$dataInsert[$i]['board_id']);
                            }
                        }

                        if($dataUpdate){
                            $this->Md_board_relation->updateDatas($dataUpdate);

                            for($i=0; $i<count($dataUpdate); $i++){
                                # merekam log sistem
                                /* contoh : addLog('Update Data', 'nama tabel', 'keterangan') */
                                addLog('Update Data', 'Board Relation', 'Board_relation_id '.$dataUpdate[$i]['board_relation_id']);
                            }
                        }
                    }

                    # push created by to pengguna id
                    if(in_array($createdby,$pengguna_id) == false){
                        array_push($pengguna_id,$createdby);
                    }

                    # insert member
                    if($pengguna_id){

                        #cek data member di database
                        $cekData = $this->Md_member->getMemberByWorkspaceid($workspace_id);
                        if($cekData){
                            $recordMember = array_column($cekData,'pengguna_id');
                            $member_id = array_column($cekData,'member_id');
                        }

                        $dataInsert=[];
                        foreach($pengguna_id as $list){
                            // insert baru
                            if(in_array($list,$recordBoard)==false){
                                $dataInsert[] = [
                                    'workspace_id'  => $workspace_id,
                                    'pengguna_id'   => $list,
                                    'author'        => $author,
                                    'tglpost'       => $datetime,
                                    'status'        => 1
                                ];
                            }
                        }

                        $dataUpdate=[];
                        foreach($recordMember as $index=>$list){
                            // update status 2 => hapus
                            if(in_array($list,$pengguna_id)==false){
                                $dataUpdate[] = [
                                    'member_id'     => $member_id[$index],
                                    'status'        => 2
                                ];
                            }
                        }

                        if($dataInsert){
                            $this->Md_member->addDatas($dataInsert);

                            for($i=0; $i<count($dataInsert); $i++){
                                # merekam log sistem
                                /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                                addLog('Add Data', 'Member', 'workspace_id' .$id.' pengguna_id '.$dataInsert[$i]['pengguna_id']);
                            }
                        }

                        if($dataUpdate){
                            $this->Md_member->updateDatas($dataUpdate);

                            for($i=0; $i<count($dataUpdate); $i++){
                                # merekam log sistem
                                /* contoh : addLog('Update Data', 'nama tabel', 'keterangan') */
                                addLog('Update Data', 'Member', 'member_id '.$dataUpdate[$i]['member_id']);
                            }
                        }
                    }
    
                    if ($this->db->trans_status() == TRUE) {
                        $this->db->trans_commit();
                        echo json_encode(array('status' => 'success', 'message' => 'Data Berhasil disimpan ..'));
                        die;
                    } else {
                        $this->db->trans_rollback();
                        echo json_encode(array('status' => 'gagal', 'message' => 'Data Gagal disimpan !'));
                        die;
                    }

                }else{
                    echo json_encode(array('status' => 'invalid', 'message' => 'Data sudah ada !'));
                    die;
                }

            }else{
                echo json_encode(array('status' => 'invalid', 'message' => 'Semua fill wajib terisi !'));
                die;
            }
        }else if ($argv == 'hapus'){
            $valid = $argv1 == '' ? FALSE : (is_int(decrypt($argv1)) ? TRUE : FALSE);
            if (!$valid) {
                echo json_encode(array('data' => FALSE));
                die;
            }

            $this->db->trans_begin();
            $workspace_id = decrypt($argv1);
            $this->Md_workspace->updateData($workspace_id, ['status'=>2]);

            # merekam log sistem
            /* contoh : addLog('Delete Data', 'nama tabel', 'keterangan') */
            addLog('Delete Data', 'Workspace', 'Workspace_id ' .$workspace_id);

            if ($this->db->trans_status() == TRUE) {
                $this->db->trans_commit();
                echo json_encode(array('data' => true));
                die;
            } else {
                $this->db->trans_rollback();
                echo json_encode(array('data' => false));
                die;
            }

        }else{
            redirect('dashboard/page_404');
        }
    }

    public function kanbanboard($argv = '', $argv1 = '', $argv2 = '', $argv3 = ''){
        if($argv == 'get'){
            $valid = $argv1 == '' ? FALSE : (is_int(decrypt($argv1)) ? TRUE : FALSE);
            if (!$valid) {
                redirect(base_url().'workspaces');
            }

            $workspace_id = decrypt($argv1);

            #data workspaces
            $workspaces = $this->Md_workspace->getWorkspaceById($workspace_id);

            #data board
            $cards = [];
            $assigments = [];
            $labels = [];
            $comments = [];
            $boards = $this->Md_board_relation->getBoardrelationByWorkspaceid($workspace_id);
            if($boards){
                foreach($boards as $list){
                    $datacard = $this->Md_card->getCardByWorkspaceidAndBoardid($workspace_id,$list->board_id);
                    $cards[encrypt($list->board_id)]=[];
                    if($datacard){
                        $cards[encrypt($list->board_id)] = $datacard;

                        # data Asignment, label dan jumlah comment pada card
                        foreach($datacard as $card){
                            $dataAssignment = $this->Md_assignment->getAssignmentByCardid($card->card_id);
                            $assigments[encrypt($card->card_id)] = !empty($dataAssignment) ? $dataAssignment : [];

                            $cardlabel = $this->Md_cardlabel->getCardlabelByCardid($card->card_id);
                            $labels[encrypt($card->card_id)] = !empty($cardlabel) ? $cardlabel : [];

                            $dataComment = $this->Md_comment->getCountCommentByCardid($card->card_id);
                            $comments[encrypt($card->card_id)] = !empty($dataComment) ? $dataComment->jumlah : 0;
                        }
                    }
                }
            }
            
            $pageData['dataworkspace'] = $workspaces;
            $pageData['databoard'] = $boards;
            $pageData['datacard'] = $cards;
            $pageData['dataAssignment'] = $assigments;
            $pageData['dataLabel'] = $labels;
            $pageData['dataComment'] = $comments;
            $pageData['labels'] = $this->Md_label->getAllLabel();
            $pageData['unitkerjas'] = $this->Md_unitkerja->getAllunitkerja();
            $pageData['page_name'] = 'view_kanbanboard';
            $this->load->view('ui/spadmin/index', $pageData);
        }else if ($argv == 'getData'){
            #get kanban board
            $workspace_id = decrypt($argv1);
            $unitkerja_id = $argv2 ? decrypt($argv2) : null;
            #data workspaces
            $workspaces = $this->Md_workspace->getWorkspaceById($workspace_id);

            #data board
            $dtcard = [];
            $boards = $this->Md_board_relation->getBoardrelationByWorkspaceid($workspace_id);
            if($boards){
                foreach($boards as $list){
                    $dtcard['board_id'][] = encrypt($list->board_id);
                    $datacard = $this->Md_card->getCardByWorkspaceidAndBoardid($workspace_id,$list->board_id,$unitkerja_id);
                    if($datacard){
                        # data Asignment, label dan jumlah comment pada card
                        foreach($datacard as $card){
                            $dtcard[encrypt($list->board_id)]['card_id'][] = encrypt($card->card_id);
                            $dtcard[encrypt($list->board_id)]['namacard'][] = $card->nama_card.' ('.$card->unitkerja.')';

                            $dataAssignment = $this->Md_assignment->getAssignmentByCardid($card->card_id);
                            $dtcard[encrypt($list->board_id)]['assignment'][] = !empty($dataAssignment) ? $dataAssignment : [];

                            if($card->duedate){
                                $duedateCard = getDuedate($card->duedate);
                                $card->duedate = date('d/M/Y H:i', strtotime($card->duedate));
                                $class = $duedateCard == 'Has Expired' ? 'text-danger' : 'text-info';
                                $dtcard[encrypt($list->board_id)]['deadline'][] = '<small class="badge badge-default '.$class.'" title="'.$card->duedate.'"><i class="far fa-calendar"></i> '.$duedateCard.' |</small>';
                            }else{
                                $dtcard[encrypt($list->board_id)]['deadline'][] = '';
                            }

                            $cardlabel = $this->Md_cardlabel->getCardlabelByCardid($card->card_id);
                            $dtcard[encrypt($list->board_id)]['label'][] = !empty($cardlabel) ? $cardlabel : [];

                            $dataComment = $this->Md_comment->getCountCommentByCardid($card->card_id);
                            $dtcard[encrypt($list->board_id)]['jumlahkomentar'][] = !empty($dataComment) ? $dataComment->jumlah : 0;
                        }
                    }else{
                        $dtcard[encrypt($list->board_id)] = [];
                    }
                }

                $row['data'] = TRUE;
                $row['datas'] = $dtcard;
            }else{
                $row['data'] = false;
            }
            echo json_encode($row);
            die;

        }else if ($argv == 'saveCard'){
            $this->form_validation->set_rules('workspace_id', 'field workspace_id', 'required');
            $this->form_validation->set_rules('board_id', 'field board_id', 'required');
            $this->form_validation->set_rules('card', 'field card', 'required');
            $this->form_validation->set_rules('unitkerja_id', 'field unitkerja_id', 'required');

            if ($this->form_validation->run() != FALSE) {
                $workspace_id   = decrypt($this->input->post('workspace_id'));
                $board_id       = decrypt($this->input->post('board_id'));
                $unitkerja_id   = decrypt($this->input->post('unitkerja_id'));
                $card           = $this->input->post('card');
                $author         = decrypt($this->session->userdata('pengguna_id'));
                $datetime       = date('Y-m-d H:i:s');

                $dataInsert = [
                    'nama_card'     => $card,
                    'workspace_id'  => $workspace_id,
                    'board_id'      => $board_id,
                    'unitkerja_id'  => $unitkerja_id,
                    'author'        => $author,
                    'tglpost'       => $datetime,
                    'status'        => 1
                ];

                # insert card
                $this->db->trans_begin();
                $id = $this->Md_card->addData($dataInsert);

                # merekam log sistem
                /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                addLog('Add Data', 'Card', 'card_id ' .$id.' card '.$card.' board_id '.$board_id.' workspace_id '.$workspace_id);

                if ($this->db->trans_status() == TRUE) {
                    $this->db->trans_commit();
                    echo json_encode(array('status' => 'success', 'message' => 'Data Berhasil disimpan ..'));
                    die;
                } else {
                    $this->db->trans_rollback();
                    echo json_encode(array('status' => 'gagal', 'message' => 'Data Gagal disimpan !'));
                    die;
                }

            }else{
                echo json_encode(array('status' => 'invalid', 'message' => 'Semua fill wajib terisi !'));
                die;
            }
        }else if ($argv == 'updateCard'){

            $board_id       = $this->input->get('board_id');
            $card_id        = $this->input->get('card_id');
            $author         = decrypt($this->session->userdata('pengguna_id'));
            $nama           = $this->session->userdata('nama');
            $datetime       = date('Y-m-d H:i:s');

            $valid_board = $board_id == '' ? FALSE : (is_int(decrypt($board_id)) ? TRUE : FALSE);
            $valid_card = $card_id == '' ? FALSE : (is_int(decrypt($card_id)) ? TRUE : FALSE);
            if (!$valid_board || !$valid_card) {
                echo json_encode(['status'=>'gagal']);
                die;
            }

            $card = $this->Md_card->getCardById(decrypt($card_id));

            $this->db->trans_begin();

            if($card->board_id != decrypt($board_id)){
                
                # update board
                $dataUpdate=[
                    'board_id'  => decrypt($board_id)
                ];
                $this->Md_card->updateData(decrypt($card_id), $dataUpdate);

                $start = $card->board; // board sebelumnya

                # data tahapan/board baru
                $board = $this->Md_board->getBoardByBoardId(decrypt($board_id));
                $end = $board->board; // board saat ini

                #insert history card
                $history = '<span class="text-primary">'.$nama.'</span> Memindahkan card dari '.$start.' ke tahapan '.$end;
                $dataInsert=[
                    'card_id'   => decrypt($card_id),
                    'history'   => $history,
                    'author'    => $author,
                    'tglpost'   => $datetime,
                    'status'    => 1
                ];

                $this->Md_history_card->addData($dataInsert);
            }
            
            if ($this->db->trans_status() == TRUE) {
                $this->db->trans_commit();
                echo json_encode(array('status' => 'success', 'message' => 'Card telah dipindahkan ..'));
                die;
            } else {
                $this->db->trans_rollback();
                echo json_encode(array('status' => 'gagal', 'message' => 'Card gagal dipindahkan !'));
                die;
            }

        }else if ($argv == 'showCard'){
            $valid = $argv1 == '' ? FALSE : (is_int(decrypt($argv1)) ? TRUE : FALSE);
            if (!$valid) {
                echo json_encode(['status'=>false]);
                die;
            }

            $row=[];
            $card_id = decrypt($argv1);
            $dataCard = $this->Md_card->getCardById($card_id);
            if($dataCard){

                $row['namacard'] = $dataCard->nama_card;
                $row['deskripsi'] = $dataCard->deskripsi;
                $row['duedateCard'] = $dataCard->duedate ? date('d/m/Y H:i:s', strtotime($dataCard->duedate)) : null;
                $row['created'] = $dataCard->created;

                # get item / card detail
                $dataItem = $this->Md_carddetail->getCarddetailByCardid($card_id);
                $dataAssignmentItem = [];
                $dataDuedate = [];
                if($dataItem){
                    foreach ($dataItem as $list){
                        $assigmentItem = $this->Md_assignment->getAssignmentByCarddetailid($list->carddetail_id);
                        $dataAssignmentItem[encrypt($list->carddetail_id)] = !empty($assigmentItem) ? $assigmentItem->nama : null;

                        $list->carddetail_id = encrypt($list->carddetail_id);
                        $list->card_id = encrypt($list->card_id);
                        $list->author = encrypt($list->author);

                        if($list->duedate){

                            // perhitungan waktu tersisa
                            $duedate = getDuedate($list->duedate, $list->startdate);

                            $dataDuedate[$list->carddetail_id] = $duedate;
                            $list->duedate = date('d-M-Y H:i', strtotime($list->duedate));
                            $list->startdate = date('d-M-Y H:i', strtotime($list->startdate));
                        }

                    }
                }
                $row['carddetail'] = !empty($dataItem) ? $dataItem : (object)[];
                $row['assignmentItem'] = $dataAssignmentItem;
                $row['duedate'] = $dataDuedate;

                #get Assignment card
                $dataAssignment = $this->Md_assignment->getAssignmentByCardid($card_id);
                $assignmentname = [];
                if($dataAssignment){
                    foreach($dataAssignment as $list){
                        $list->assignment_id = encrypt($list->assignment_id);
                        $list->pengguna_id = encrypt($list->pengguna_id);

                        array_push($assignmentname,$list->nama);
                    }
                }
                
                $row['assignmentname'] = $assignmentname ? implode(", ", $assignmentname) : '-';
                $row['assignmentCard'] = !empty($dataAssignment) ? $dataAssignment : (object)[];

                #get Attachment
                $dataAttachment = $this->Md_attachment->getAttachmentByCardid($card_id);
                if($dataAttachment){
                    foreach($dataAttachment as $list){
                        $list->attachment_id = encrypt($list->attachment_id);
                        $list->card_id = encrypt($list->card_id);
                        $list->files = htmlspecialchars($list->files);
                        $list->attachment = htmlspecialchars($list->attachment);
                    }
                }
                $row['attachment'] = !empty($dataAttachment) ? $dataAttachment : (object)[];

                #get card Label
                $dataCardlabel = $this->Md_cardlabel->getCardlabelByCardid($card_id);
                if($dataCardlabel){
                    foreach($dataCardlabel as $list){
                        $list->cardlabel_id = encrypt($list->cardlabel_id);
                        $list->label_id = encrypt($list->label_id);
                    }
                }
                $row['cardlabel'] = !empty($dataCardlabel) ? $dataCardlabel : (object)[];

                #get commentar
                $dataComment = $this->Md_comment->getCommentByCardid($card_id);
                if($dataComment){
                    foreach($dataComment as $list){
                        $list->comment_id = encrypt($list->comment_id);
                    }
                }
                $row['komentar'] = !empty($dataComment) ? $dataComment : (object)[];
                $row['me']         = $this->session->userdata('nama');

                # get history card
                $dataHistoryCard = $this->Md_history_card->getHistoryCardByCardId($card_id);
                if($dataHistoryCard){
                    foreach($dataHistoryCard as $list){
                        $list->tglpost = date('d-M-Y H:i',strtotime($list->tglpost));
                    }
                }
                $row['cardhistory'] = !empty($dataHistoryCard) ? $dataHistoryCard : (object)[];
            }

            echo json_encode(['status'=>true, 'data'=>$row]);
            die;
        }else if ($argv == 'saveItem'){
            $this->form_validation->set_rules('card_id', 'field card_id', 'required');
            $this->form_validation->set_rules('list', 'field list', 'required');

            if ($this->form_validation->run() != FALSE) {
                $card_id = decrypt($this->input->post('card_id'));
                $list = $this->input->post('list');
                $assignment_list = $this->input->post('assignment_list') ? decrypt($this->input->post('assignment_list')) : null;

                $startdate = $this->input->post('startdate');
                if($startdate){
                    $startdate = str_replace('/','-',$this->input->post('startdate'));
                    $startdate = date('Y-m-d H:i:s', strtotime($startdate));
                }else{
                    $startdate = null;
                }

                $deadline = $this->input->post('deadline');
                if($deadline){
                    $deadline = str_replace('/','-',$this->input->post('deadline'));
                    $deadline = date('Y-m-d H:i:s', strtotime($deadline));
                }else{
                    $deadline = null;
                }

                if($startdate != null && $deadline != null){
                    if(strtotime($deadline) < strtotime($startdate)){
                        echo json_encode(array('status' => 'invalid', 'message' => 'Tanggal start date harus lebih kecil dari end date !'));
                        die;
                    }
                }else if ($startdate != null && $deadline == null){
                    echo json_encode(array('status' => 'invalid', 'message' => 'Tanggal end date harus di isi !'));
                    die;
                }else if ($deadline != null && $startdate == null){
                    echo json_encode(array('status' => 'invalid', 'message' => 'Tanggal start date harus di isi !'));
                    die;
                }

                $author = decrypt($this->session->userdata('pengguna_id'));
                $datetime = date('Y-m-d H:i:s');

                # insert card_detail
                $dataInsert = [
                    'list'           => $list,
                    'card_id'        => $card_id,
                    'startdate'      => $startdate,
                    'duedate'        => $deadline,
                    'author'         => $author,
                    'tglpost'        => $datetime,
                    'status'         => 1,
                    'finish'         => 'No',
                ];

                $this->db->trans_begin();
                $id = $this->Md_carddetail->addData($dataInsert);

                # merekam log sistem
                /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                addLog('Add Data', 'Card detail', 'carddetail_id ' .$id.' list '.$list.' duedate '.$deadline);

                if($assignment_list){
                    $dataInsert = [
                        'pengguna_id'    => $assignment_list,
                        'author'         => $author,
                        'tglpost'        => $datetime,
                        'status'         => 1,
                        'carddetail_id'  => $id
                    ];
                    $assignment_id = $this->Md_assignment->addData($dataInsert);
    
                    # merekam log sistem
                    /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                    addLog('Add Data', 'Card assignment', 'assignment_id ' .$assignment_id.' carddetail_id '.$id);
                }

                if ($this->db->trans_status() == TRUE) {
                    $this->db->trans_commit();
                    echo json_encode(array('status' => 'success', 'message' => 'Data Berhasil disimpan ..'));
                    die;
                } else {
                    $this->db->trans_rollback();
                    echo json_encode(array('status' => 'gagal', 'message' => 'Data Gagal disimpan !'));
                    die;
                }
            }else{
                echo json_encode(array('status' => 'invalid', 'message' => 'Semua fill wajib terisi !'));
                die;
            }
        }else if ($argv == 'hapusItem'){
            $valid = $argv1 == '' ? FALSE : (is_int(decrypt($argv1)) ? TRUE : FALSE);
            if (!$valid) {
                echo json_encode(array('data' => FALSE));
                die;
            }

            $this->db->trans_begin();
            $carddetail_id = decrypt($argv1);

            $assignment = $this->Md_assignment->getAssignmentByCarddetailid($carddetail_id);
            if($assignment){
                $author = decrypt($this->session->userdata('pengguna_id'));
                if($assignment->pengguna_id != $author){
                    echo json_encode(array('data' => false, 'Anda tidak diizinkan menghapus item ini !'));
                    die;
                }

                $this->Md_assignment->updateData($assignment->assignment_id, ['status'=>2]);
                # merekam log sistem
                /* contoh : addLog('Delete Data', 'nama tabel', 'keterangan') */
                addLog('Delete Data', 'Assignment', 'assignment_id ' .$assignment->assignment_id);

            }
            $this->Md_carddetail->updateData($carddetail_id, ['status'=>2]);

            # merekam log sistem
            /* contoh : addLog('Delete Data', 'nama tabel', 'keterangan') */
            addLog('Delete Data', 'Card detail', 'carddetail_id ' .$carddetail_id);

            if ($this->db->trans_status() == TRUE) {
                $this->db->trans_commit();
                echo json_encode(array('data' => true));
                die;
            } else {
                $this->db->trans_rollback();
                echo json_encode(array('data' => false, 'Ups.. Data gagal di hapus !'));
                die;
            }

        }else if ($argv == 'checklistItem'){
            $carddetail_id = $this->input->get('carddetail_id');
            $finish = $this->input->get('finish');
            $author = decrypt($this->session->userdata('pengguna_id'));
            $datetime       = date('Y-m-d H:i:s');

            $valid = $carddetail_id == '' ? FALSE : (is_int(decrypt($carddetail_id)) ? TRUE : FALSE);
            if (!$valid) {
                echo json_encode(array('data' => FALSE));
                die;
            }

            $this->db->trans_begin();

            # pengecekan item ini milik siapa
            $dataAssignment = $this->Md_assignment->getAssignmentByCarddetailid(decrypt($carddetail_id));
            if($dataAssignment){
                if($dataAssignment->pengguna_id != $author){
                    echo json_encode(array('data' => false, 'Anda tidak dapat checklist item ini !'));
                    die;
                }
            }else{
                # set yang mengerjakan item ini
                $dataInsert=[
                    'pengguna_id'   => $author,
                    'carddetail_id' => decrypt($carddetail_id),
                    'author'        => $author,
                    'tglpost'       => $datetime,
                    'status'        => 1
                ];

                $id = $this->Md_assignment->addData($dataInsert);
                # merekam log sistem
                /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                addLog('Add Data', 'Assignment', 'assignment_id ' .$id.' carddetail_id '.$carddetail_id.' pengguna_id '.$author);
            }

            #update carddetail
            $dataUpdate = [
                'finish'    => $finish
            ];
            $this->Md_carddetail->updateData(decrypt($carddetail_id),$dataUpdate);
            # merekam log sistem
            /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
            addLog('Update Data', 'Card detail', 'Carddetailid ' .decrypt($carddetail_id).' finish '.$finish);

            if ($this->db->trans_status() == TRUE) {
                $this->db->trans_commit();
                echo json_encode(array('data' => true));
                die;
            } else {
                $this->db->trans_rollback();
                echo json_encode(array('data' => false));
                die;
            }
        }else if ($argv == 'editItem'){
            $valid = $argv1 == '' ? FALSE : (is_int(decrypt($argv1)) ? TRUE : FALSE);
            if (!$valid) {
                echo json_encode(array('data' => FALSE));
                die;
            }

            #get Data card detail (item)
            $row=[];
            $dataItem = $this->Md_carddetail->getCarddetailById(decrypt($argv1));
            if($dataItem){
                $row['carddetail_id'] = encrypt($dataItem->carddetail_id);
                $row['list']          = $dataItem->list; 
                $row['duedate']       = $dataItem->duedate ? date('d/m/Y H:i:s', strtotime($dataItem->duedate)) : null;
                $row['startdate']     = $dataItem->startdate ? date('d/m/Y H:i:s', strtotime($dataItem->startdate)) : null;

                $dataAssignment = $this->Md_assignment->getAssignmentByCarddetailid(decrypt($argv1));
                $row['assignment_list'] = !empty($dataAssignment) ? encrypt($dataAssignment->pengguna_id) : '';

                echo json_encode(array('data' => TRUE, 'respon'=>$row));
                die;
            }else{
                echo json_encode(array('data' => FALSE));
                die;
            }
        }else if ($argv == 'updateItem'){
            $this->form_validation->set_rules('carddetail_id', 'field carddetail_id', 'required');
            $this->form_validation->set_rules('card_id', 'field card_id', 'required');
            $this->form_validation->set_rules('list', 'field list', 'required');

            if ($this->form_validation->run() != FALSE) {
                $carddetail_id = decrypt($this->input->post('carddetail_id'));
                $card_id = decrypt($this->input->post('card_id'));
                $list = $this->input->post('list');
                $assignment_list = $this->input->post('assignment_list') ? decrypt($this->input->post('assignment_list')) : null;

                $startdate = $this->input->post('startdate');
                if($startdate){
                    $startdate = str_replace('/','-',$this->input->post('startdate'));
                    $startdate = date('Y-m-d H:i:s', strtotime($startdate));
                }else{
                    $startdate = null;
                }

                $deadline = $this->input->post('deadline');
                if($deadline){
                    $deadline = str_replace('/','-',$this->input->post('deadline'));
                    $deadline = date('Y-m-d H:i:s', strtotime($deadline));
                }else{
                    $deadline = null;
                }

                if($startdate != null && $deadline != null){
                    if(strtotime($deadline) < strtotime($startdate)){
                        echo json_encode(array('status' => 'invalid', 'message' => 'Tanggal start date harus lebih kecil dari end date !'));
                        die;
                    }
                }else if ($startdate != null && $deadline == null){
                    echo json_encode(array('status' => 'invalid', 'message' => 'Tanggal end date harus di isi !'));
                    die;
                }else if ($deadline != null && $startdate == null){
                    echo json_encode(array('status' => 'invalid', 'message' => 'Tanggal start date harus di isi !'));
                    die;
                }

                $author = decrypt($this->session->userdata('pengguna_id'));
                $datetime = date('Y-m-d H:i:s');

                # insert card_detail
                $dataUpdate = [
                    'list'           => $list,
                    'card_id'        => $card_id,
                    'startdate'      => $startdate,
                    'duedate'        => $deadline,
                ];

                $this->db->trans_begin();
                $this->Md_carddetail->updateData($carddetail_id,$dataUpdate);

                # merekam log sistem
                /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                addLog('Update Data', 'Card detail', 'carddetail_id ' .$carddetail_id.' list '.$list.' duedate '.$deadline);

                if($assignment_list){
                    $dataAssignment = $this->Md_assignment->getAssignmentByCarddetailid($carddetail_id);
                    if($dataAssignment){
                        if($dataAssignment->pengguna_id != $assignment_list){
                            $dataInsert = [
                                'pengguna_id'    => $assignment_list,
                                'author'         => $author,
                                'tglpost'        => $datetime,
                                'status'         => 1,
                                'carddetail_id'  => $carddetail_id
                            ];
                            $assignment_id = $this->Md_assignment->addData($dataInsert);

                            # merekam log sistem
                            /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                            addLog('Add Data', 'Card assignment', 'assignment_id ' .$assignment_id.' carddetail_id '.$carddetail_id);

                            $dataUpdate = [
                                'status' => 2
                            ];
                            # merekam log sistem
                            /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                            addLog('Delete Data', 'Assignment', 'Assignment ID ' .decrypt($dataAssignment->assignment_id));
                        }
                    }else{
                        $dataInsert = [
                            'pengguna_id'    => $assignment_list,
                            'author'         => $author,
                            'tglpost'        => $datetime,
                            'status'         => 1,
                            'carddetail_id'  => $carddetail_id
                        ];
                        $assignment_id = $this->Md_assignment->addData($dataInsert);

                        # merekam log sistem
                        /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                        addLog('Add Data', 'Card assignment', 'assignment_id ' .$assignment_id.' carddetail_id '.$carddetail_id);
                    }
                }

                if ($this->db->trans_status() == TRUE) {
                    $this->db->trans_commit();
                    echo json_encode(array('status' => 'success', 'message' => 'Data Berhasil disimpan ..'));
                    die;
                } else {
                    $this->db->trans_rollback();
                    echo json_encode(array('status' => 'gagal', 'message' => 'Data Gagal disimpan !'));
                    die;
                }
            }else{
                echo json_encode(array('status' => 'invalid', 'message' => 'Semua fill wajib terisi !'));
                die;
            }
        }else if ($argv == 'card'){
            
            # simpan deskripsi
            if($argv1 == 'saveDeskripsi'){
                $card_id    = decrypt($this->input->get('cardid'));
                $deskripsi  = $this->input->get('deskripsi');

                $dataUpdate = [
                    'deskripsi' => $deskripsi
                ];

                $this->db->trans_begin();
                $this->Md_card->updateData($card_id, $dataUpdate);
                # merekam log sistem
                /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                addLog('Update Data', 'Card', 'card_id ' .$card_id.' deskripsi '.$deskripsi);

                if ($this->db->trans_status() == TRUE) {
                    $this->db->trans_commit();
                    echo json_encode(array('status' => 'success', 'message' => 'Data Berhasil disimpan ..'));
                    die;
                } else {
                    $this->db->trans_rollback();
                    echo json_encode(array('status' => 'gagal', 'message' => 'Data Gagal disimpan !'));
                    die;
                }

            } # simpan duedate / deadline card
            else if ($argv1 == 'saveDuedate'){
                $card_id    = decrypt($this->input->post('cardid'));
                $duedate    = $this->input->post('duedate');

                if($duedate){
                    $duedate = str_replace('/','-',$this->input->post('duedate'));
                    $duedate = date('Y-m-d H:i:s', strtotime($duedate));
                }else{
                    $duedate = null;
                }

                $dataUpdate = [
                    'duedate' => $duedate
                ];

                $this->db->trans_begin();
                $this->Md_card->updateData($card_id, $dataUpdate);
                # merekam log sistem
                /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                addLog('Update Data', 'Card', 'card_id ' .$card_id.' duedate '.$duedate);

                if ($this->db->trans_status() == TRUE) {
                    $this->db->trans_commit();
                    echo json_encode(array('status' => 'success', 'message' => 'Data Berhasil disimpan ..'));
                    die;
                } else {
                    $this->db->trans_rollback();
                    echo json_encode(array('status' => 'gagal', 'message' => 'Data Gagal disimpan !'));
                    die;
                }
            } # simpan file project
            else if ($argv1 == 'saveFile'){

                $author = decrypt($this->session->userdata('pengguna_id'));
                $datetime = date('Y-m-d H:i:s');
                $card_id = decrypt($this->input->post('card_id'));

                if (!empty($_FILES['file']['name'])) {
                    $attachment = $_FILES['file']['name'];
                    $config['upload_path'] = "./assets/uploads/file_project";
                    $config['allowed_types'] = 'docx|xlsx|pptx|jpg|jpeg|png|png|pdf|rar|zip|txt';
                    $config['encrypt_name'] = TRUE;
                    $config['max_size'] = '5480'; //in Kilobit => 5 Mb
                    $this->upload->initialize($config);
                    if ($this->upload->do_upload("file")) {
                        $data = $this->upload->data();
                        $filename = $data['file_name'];
                        $filesize = round($_FILES['file']['size']/1024); //in Kb
                    } else {
                        echo json_encode(array('status' => 'gagal', 'message' => 'File gagal di upload !'));
                        die;
                    }

                    $dataInsert=[
                        'card_id'       => $card_id,
                        'files'         => $filename,
                        'attachment'    => $attachment,
                        'author'        => $author,
                        'tglpost'       => $datetime,
                        'status'        => 1
                    ];

                    $this->db->trans_begin();
                    $id = $this->Md_attachment->addData($dataInsert);

                    # merekam log sistem
                    /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                    addLog('Update Data', 'Attachment', 'attachment_id ' .$id.' attachment '.$attachment.' files '.$filename.' card_id '.$card_id);

                    if ($this->db->trans_status() == TRUE) {
                        $this->db->trans_commit();
                        echo json_encode(array('status' => 'success', 'message' => 'Data Berhasil disimpan ..'));
                        die;
                    } else {
                        $this->db->trans_rollback();
                        echo json_encode(array('status' => 'gagal', 'message' => 'Data Gagal disimpan !'));
                        die;
                    }
                }
            } # delete file project
            else if ($argv1 == 'deleteFile'){
                $valid = $argv2 == '' ? FALSE : (is_int(decrypt($argv2)) ? TRUE : FALSE);
                if (!$valid) {
                    echo json_encode(array('data' => FALSE, 'message'=> 'Tidak dapat memperoses hapus data !'));
                    die;
                }

                $id = decrypt($argv2);

                $dataUpdate=[
                    'status' => 2
                ];

                $this->db->trans_begin();
                $this->Md_attachment->updateData($id, $dataUpdate);
                # merekam log sistem
                /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                addLog('Delete Data', 'Attachment', 'attachment_id ' .$id);

                if ($this->db->trans_status() == TRUE) {
                    $this->db->trans_commit();
                    echo json_encode(array('data' => true, 'message' => 'File Berhasil dihapus ..'));
                    die;
                } else {
                    $this->db->trans_rollback();
                    echo json_encode(array('data' => false, 'message' => 'File Gagal dihapus !'));
                    die;
                }
            } # join team
            else if ($argv1 == 'joinTeam'){
                $author = decrypt($this->session->userdata('pengguna_id'));
                $datetime = date('Y-m-d H:i:s');
                $card_id = decrypt($this->input->get('card_id'));

                $cek = $this->Md_assignment->getAssignmentByCardidAndPenggunaid($card_id,$author);
                if(!$cek){
                    $dataInsert=[
                        'pengguna_id'   => $author,
                        'card_id'       => $card_id,
                        'author'        => $author,
                        'tglpost'       => $datetime,
                        'status'        => 1
                    ];
    
                    $this->db->trans_begin();
                    $id = $this->Md_assignment->addData($dataInsert);
                    # merekam log sistem
                    /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                    addLog('Add Data', 'Assignment', 'assignment_id ' .$id.' card_id '.$card_id);
    
                    if ($this->db->trans_status() == TRUE) {
                        $this->db->trans_commit();
                        echo json_encode(array('data' => true, 'message' => 'Data Berhasil disimpan'));
                        die;
                    } else {
                        $this->db->trans_rollback();
                        echo json_encode(array('data' => false, 'message' => 'Data Gagal disimpan !'));
                        die;
                    }
                }else{
                    echo json_encode(array('data' => 'exist', 'message' => 'Anda sudah terdaftar pada team ini !'));
                    die;
                }
            } # set label
            else if ($argv1 == 'setLabel'){
                $author = decrypt($this->session->userdata('pengguna_id'));
                $datetime = date('Y-m-d H:i:s');
                $card_id = decrypt($this->input->get('card_id'));
                $label_id = decrypt($this->input->get('label_id'));

                $cek = $this->Md_cardlabel->getCardlabelByCardidAndLabelid($card_id, $label_id);

                $this->db->trans_begin();
                if($cek){
                    $dataUpdate=[
                        'status'    => 2
                    ];
                    $this->Md_cardlabel->updateData($cek->cardlabel_id, $dataUpdate);
                    # merekam log sistem
                    /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                    addLog('Delete Data', 'Card label', 'cardlabel_id ' .$cek->cardlabel_id);
                }else{
                    $dataInsert=[
                        'card_id'       => $card_id,
                        'label_id'      => $label_id,
                        'author'        => $author,
                        'tglpost'       => $datetime,
                        'status'        => 1
                    ];
                    $id = $this->Md_cardlabel->addData($dataInsert);
                    # merekam log sistem
                    /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                    addLog('Add Data', 'Card label', 'cardlabel_id ' .$id);
                }

                if ($this->db->trans_status() == TRUE) {
                    $this->db->trans_commit();
                    echo json_encode(array('data' => true, 'message' => 'Data Berhasil disimpan'));
                    die;
                } else {
                    $this->db->trans_rollback();
                    echo json_encode(array('data' => false, 'message' => 'Data Gagal disimpan !'));
                    die;
                }
            } # delete card
            else if ($argv1 == 'deleteCard'){
                $valid = $argv2 == '' ? FALSE : (is_int(decrypt($argv2)) ? TRUE : FALSE);
                if (!$valid) {
                    echo json_encode(array('data' => FALSE, 'message'=> 'Tidak dapat memperoses hapus data !'));
                    die;
                }

                $id = decrypt($argv2);

                $dataUpdate=[
                    'status' => 2
                ];

                $this->db->trans_begin();
                $this->Md_card->updateData($id, $dataUpdate);
                # merekam log sistem
                /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                addLog('Delete Data', 'Card', 'card_id ' .$id);

                if ($this->db->trans_status() == TRUE) {
                    $this->db->trans_commit();
                    echo json_encode(array('data' => true, 'message' => 'Card Berhasil dihapus ..'));
                    die;
                } else {
                    $this->db->trans_rollback();
                    echo json_encode(array('data' => false, 'message' => 'Card Gagal dihapus !'));
                    die;
                }
            } # get data card
            else if ($argv1 == 'getCard'){
                $valid = $argv2 == '' ? FALSE : (is_int(decrypt($argv2)) ? TRUE : FALSE);
                if (!$valid) {
                    echo json_encode(array('data' => FALSE));
                    die;
                }

                #get Data card
                $datacard = $this->Md_card->getCardById(decrypt($argv2));
                if($datacard){
                    $row['namacard']  = htmlspecialchars($datacard->nama_card); 

                    echo json_encode(array('data' => TRUE, 'respon'=>$row));
                    die;
                }else{
                    echo json_encode(array('data' => FALSE));
                    die;
                }
            } # update nama card
            else if($argv1 == 'updateNamaCard'){
                $card_id    = decrypt($this->input->post('cardid'));
                $namacard   = $this->input->post('namacard');

                $dataUpdate=[
                    'nama_card' => $namacard,
                ];

                $this->db->trans_begin();
                $id = $this->Md_card->updateData($card_id, $dataUpdate);

                # merekam log sistem
                /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                addLog('Update Data', 'Card', 'card_id ' .$card_id.' namacard '.$namacard);

                if ($this->db->trans_status() == TRUE) {
                    $this->db->trans_commit();
                    echo json_encode(array('status' => 'success', 'message' => 'Data Berhasil disimpan ..'));
                    die;
                } else {
                    $this->db->trans_rollback();
                    echo json_encode(array('status' => 'gagal', 'message' => 'Data Gagal disimpan !'));
                    die;
                }
            }
        }else if ($argv == 'sendcomment'){
            $this->form_validation->set_rules('card_id', 'field card_id', 'required');
            $this->form_validation->set_rules('komentar', 'field komentar', 'required');

            if ($this->form_validation->run() != FALSE) {
                $card_id = decrypt($this->input->post('card_id'));
                $komentar = $this->input->post('komentar');
                $author = decrypt($this->session->userdata('pengguna_id'));
                $datetime = date('Y-m-d H:i:s');

                $dataInsert = [
                    'card_id'   => $card_id,
                    'comment'   => $komentar,
                    'author'    => $author,
                    'tglpost'   => $datetime,
                    'status'    => 1
                ];

                $this->db->trans_begin();
                $id = $this->Md_comment->addData($dataInsert);
                # merekam log sistem
                /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                addLog('Add Data', 'Comment', 'comment_id ' .$id.' card_id '.$card_id.' comment '.$komentar);

                if ($this->db->trans_status() == TRUE) {
                    $this->db->trans_commit();
                    echo json_encode(array('status' => 'success', 'message' => 'Komentar Berhasil disimpan ..'));
                    die;
                } else {
                    $this->db->trans_rollback();
                    echo json_encode(array('status' => 'gagal', 'message' => 'Komentar gagal disimpan !'));
                    die;
                }

            }else{
                echo json_encode(array('status' => 'invalid', 'message' => 'Komentar wajib di isi !!'));
                die;
            }
        }
    }

    public function unitkerja($argv = '', $argv1 = '', $argv2 = '', $argv3 = '')
    {
        if($argv == ''){

            # config column, penamaan field yang akan ditampilkan (harus sesuai didatabase)
            $pageData['coloumns'] = [
                #<thead>        #field dari tabel database
                'No'            => 'no',
                'Unitkerja'   => 'unitkerja',
                'Action'        => 'unitkerja_id'
            ];

            #size thead
            $pageData['size'] = ['10',false,'100'];

            # option parameter filter
            $pageData['dataFilter'] = array();

            #request ajax list data
            $pageData['url'] = base_url().'spadmin/unitkerja/list';

            $pageData['page_name'] = 'view_unitkerja';
            $this->load->view('ui/spadmin/index', $pageData);

        }else if ($argv == 'list'){

            # receive parameter by method post
            $postData = $this->input->post();

            # sebutkan parameter filter didalam array (sesuaikan dengan field yang ditabel)
            $postData['filtering']=[];

            # sebutkan parameter searching didalam array (sesuaikan dengan field yang ditabel)
            $postData['searching']=['unitkerja'];

            # kirim parameter postData dan nama Model dari tabel ini (example : user , maka pake Md_unitkerja)
            $response = getDataTable($postData,'Md_unitkerja');

            $data=[];
            if($response['data']){
                $records = $response['data'];
                # memunculkan data dari value field yang ingin ditampilkan
                foreach ($records as $record) {
                    $data[] = [
                        "no"             => $record->no,
                        "unitkerja"    => $record->unitkerja,
                        "unitkerja_id" => '<a class="btn btn-primary" href="javascript:edit(\'' . encrypt($record->unitkerja_id) . '\');">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a class="btn btn-danger" href="javascript:hapus(\'' . encrypt($record->unitkerja_id) . '\');">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>',
                    ];
                }
            }

            $output = array_merge($response['meta'],["aaData" => $data]);
            
            echo json_encode($output);
            die;
        }else if ($argv == 'add'){
            $this->form_validation->set_rules('unitkerja', 'field unitkerja', 'required');

            if ($this->form_validation->run() != FALSE) {
                $unitkerja = $this->input->post('unitkerja');
                $author = decrypt($this->session->userdata('pengguna_id'));
                $datetime = date('Y-m-d H:i:s');

                $dataInsert = [
                    'unitkerja'   => $unitkerja,
                    'author'        => $author,
                    'tglpost'       => $datetime,
                    'status'        => 1
                ];

                #cek data ini sudah ada apa belum
                $cek = $this->Md_unitkerja->getUnitkerjaByUnitkerja($unitkerja);
                if(empty($cek)){

                    $this->db->trans_begin();
                    $id = $this->Md_unitkerja->addData($dataInsert);
    
                    # merekam log sistem
                    /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                    addLog('Add Data', 'Unitkerja', 'unitkerja_id ' .$id.' unitkerja '.$unitkerja);
    
                    if ($this->db->trans_status() == TRUE) {
                        $this->db->trans_commit();
                        echo json_encode(array('status' => 'success', 'message' => 'Data Berhasil disimpan ..'));
                        die;
                    } else {
                        $this->db->trans_rollback();
                        echo json_encode(array('status' => 'gagal', 'message' => 'Data Gagal disimpan !'));
                        die;
                    }

                }else{
                    echo json_encode(array('status' => 'invalid', 'message' => 'Data sudah ada !'));
                    die;
                }

            }else{
                echo json_encode(array('status' => 'invalid', 'message' => 'Semua fill wajib terisi !'));
                die;
            }
        }else if ($argv == 'edit'){
            $valid = $argv1 == '' ? FALSE : (is_int(decrypt($argv1)) ? TRUE : FALSE);
            if (!$valid) {
                echo json_encode(array('data' => FALSE));
                die;
            }

            $unitkerja_id = decrypt($argv1);
            $data = $this->Md_unitkerja->getUnitkerjaByUnitkerjaId($unitkerja_id);

            $row['data'] = FALSE;
            if($data){
                $row['data'] = TRUE;
                $row['unitkerja_id'] = encrypt($data->unitkerja_id);
                $row['unitkerja'] = $data->unitkerja;
            }

            echo json_encode($row);
            die;

        }else if ($argv == 'update'){
            $this->form_validation->set_rules('unitkerja_id', 'field unitkerja_id', 'required');
            $this->form_validation->set_rules('unitkerja', 'field unitkerja', 'required');

            if ($this->form_validation->run() != FALSE) {
                $unitkerja_id = decrypt($this->input->post('unitkerja_id'));
                $unitkerja = $this->input->post('unitkerja');

                $dataUpdate = [
                    'unitkerja'   => $unitkerja,
                ];

                #cek data ini sudah ada apa belum
                $cek = $this->Md_unitkerja->getUnitkerjaByUnitkerja($unitkerja);
                $update = false;
                if($cek){
                    if($cek->unitkerja_id == $unitkerja_id){
                        $update = true;
                    }
                }else{
                    $update = true;
                }

                if($update){

                    $this->db->trans_begin();
                    $this->Md_unitkerja->updateData($unitkerja_id, $dataUpdate);
    
                    # merekam log sistem
                    /* contoh : addLog('Update Data', 'nama tabel', 'keterangan') */
                    addLog('Update Data', 'unitkerja', 'unitkerja_id ' .$unitkerja_id.' unitkerrja '.$unitkerja);
    
                    if ($this->db->trans_status() == TRUE) {
                        $this->db->trans_commit();
                        echo json_encode(array('status' => 'success', 'message' => 'Data Berhasil disimpan ..'));
                        die;
                    } else {
                        $this->db->trans_rollback();
                        echo json_encode(array('status' => 'gagal', 'message' => 'Data Gagal disimpan !'));
                        die;
                    }

                }else{
                    echo json_encode(array('status' => 'invalid', 'message' => 'Data sudah ada !'));
                    die;
                }

            }else{
                echo json_encode(array('status' => 'invalid', 'message' => 'Semua fill wajib terisi !'));
                die;
            }
        }else if ($argv == 'hapus'){
            $valid = $argv1 == '' ? FALSE : (is_int(decrypt($argv1)) ? TRUE : FALSE);
            if (!$valid) {
                echo json_encode(array('data' => FALSE));
                die;
            }

            $this->db->trans_begin();
            $unitkerja_id = decrypt($argv1);
            $this->Md_unitkerja->updateData($unitkerja_id, ['status'=>2]);

            # merekam log sistem
            /* contoh : addLog('Delete Data', 'nama tabel', 'keterangan') */
            addLog('Delete Data', 'Unitkerja', 'unitkerja_id ' .$unitkerja_id);

            if ($this->db->trans_status() == TRUE) {
                $this->db->trans_commit();
                echo json_encode(array('data' => true));
                die;
            } else {
                $this->db->trans_rollback();
                echo json_encode(array('data' => false));
                die;
            }

        }else{
            redirect('dashboard/page_404');
        }
    }
}