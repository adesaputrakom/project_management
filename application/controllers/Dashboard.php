<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller {
    public function __construct() {
        parent::__construct();
        
        # load model

        # library
        $this->load->model('Md_users');
        $this->load->model('Md_workspace');
        $this->load->model('Md_board');
        $this->load->model('Md_card');
        $this->load->model('Md_logsistem');
        $this->load->model('Md_departement');
        $this->load->model('Md_unitkerja');

        # helper
        $this->load->helper('encryption_id');
        $this->load->helper('project');

        # library
        $this->load->library('upload');
        
        date_default_timezone_set('Asia/Jakarta');
        
         // untuk mengecek sesion sestiap ada request
        $is_validate = $this->session->userdata('lo9s1st3m');
        if (!$is_validate) {
            redirect(base_url() . 'auth', 'refresh');
        }
    }

    public function index($argv = '', $argv1 = '', $argv2 = '', $argv3 = '') {

        # lengkapi data terlebih dahulu untuk pengguna baru
        $id = decrypt($this->session->userdata('pengguna_id'));
        $dataPengguna = $this->Md_users->getUserByPenggunaId($id);
        if($dataPengguna){
            if($dataPengguna->status_active == null){
                redirect(site_url('dashboard/profile'));
            }
        }else{
            redirect(site_url('auth/signout'));
        }

        # jumlah data
        $pageData['users'] = count($this->Md_users->getAllUserActive());
        $pageData['workspaces'] = count($this->Md_workspace->getAllworkspace());
        $pageData['boards'] = count($this->Md_board->getAllBoard());
        $pageData['cards'] = count($this->Md_card->getAllcard());

        $is_admin = $this->session->userdata('is_admin');
        $pageData['page_name'] = 'view_dashboard';

        if($is_admin){
            $now = date("Y-m-d H:i:s");
            $datalogAll = array();
            $logAll = $this->Md_logsistem->getAllLogsistem(50);
            $no=1;
            if ($logAll) {
                foreach ($logAll as $list) {
                    $row = array();
                    $row['keterangan'] = '<span class="text-primary">'.$list->nama . '</span> ' . $list->jenislog . ' ' . $list->keterangan;
                    $durasi = getDuration($list->tgl, $now)->detik;
                    if ($durasi < 60) {
                        $row['waktu'] = 'Just Now';
                    } else if ($durasi < 3600) {
                        $hasil = intval($durasi / 60);
                        $row['waktu'] = $hasil . ' Mins ago';
                    } else if ($durasi < 86400) {
                        $hasil = intval($durasi / 3600);
                        $row['waktu'] = $hasil . ' Hours ago';
                    } else if ($durasi > 86400) {
                        $hasil = intval($durasi / 86400);
                        $row['waktu'] = $hasil . ' Days ago';
                    }
    
                    if ($no == 1)
                        $row['bullet'] = "bg-info";
                    if ($no == 2)
                        $row['bullet'] = "bg-danger";
                    if ($no == 3)
                        $row['bullet'] = "bg-purple";
                    if ($no == 4) {
                        $row['bullet'] = "bg-success";
                        $no = 1;
                    }
    
                    $datalogAll[] = (object) $row;
                    $no++;
                }
            }
            #logsistem
            $pageData['logsistem'] = $datalogAll;
    
            $this->load->view('ui/spadmin/index', $pageData);
        }else{
            $this->load->view('ui/user/index', $pageData);
        }
    }

    public function profile(){

        $id = decrypt($this->session->userdata('pengguna_id'));
        $dataPengguna = $this->Md_users->getUserByPenggunaId($id);

        if($dataPengguna->status_active != null){
            redirect(site_url('dashboard'));
        }
        # data master yang dikirim
        $pageData['profil'] = $dataPengguna;
        $pageData['departments'] = $this->Md_departement->getAllDepartement();
        $this->load->view('view_profile',$pageData);
    }

    public function update_profile(){
        $this->form_validation->set_rules('nama', 'field nama', 'required');
        $this->form_validation->set_rules('email', 'field email', 'required');
        $this->form_validation->set_rules('username', 'field username', 'required');
        $this->form_validation->set_rules('pass', 'field pass', 'required');
        $this->form_validation->set_rules('jeniskelamin', 'field jeniskelamin', 'required');
        $this->form_validation->set_rules('tempatlahir', 'field tempatlahir', 'required');
        $this->form_validation->set_rules('tanggallahir', 'field tanggallahir', 'required');
        $this->form_validation->set_rules('pendidikan', 'field pendidikan', 'required');
        $this->form_validation->set_rules('alamat', 'field alamat', 'required');
        $this->form_validation->set_rules('company', 'field company', 'required');
        $this->form_validation->set_rules('posisi', 'field posisi', 'required');
        $this->form_validation->set_rules('agama', 'field agama', 'required');

        if ($this->form_validation->run() != FALSE) {
            $nama           = $this->input->post('nama');
            $email          = $this->input->post('email');
            $username       = $this->input->post('username');
            $pass           = $this->input->post('pass');
            $jeniskelamin   = $this->input->post('jeniskelamin');
            $tempatlahir    = $this->input->post('tempatlahir');

            $tgllahir = str_replace('/','-',$this->input->post('tanggallahir'));
            $tgllahir = date('Y-m-d', strtotime($tgllahir));

            $pendidikan     = $this->input->post('pendidikan');
            $alamat         = $this->input->post('alamat');
            $company        = $this->input->post('company');
            $posisi         = $this->input->post('posisi');
            $agama          = $this->input->post('agama');
            $departement_id = $this->input->post('departement_id') ? decrypt($this->input->post('departement_id')) : null;
            $author         = decrypt($this->session->userdata('pengguna_id'));

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

            $dataUpdate = [
                'nama'              => $nama,
                'email'             => $email,
                'username'          => $username,
                'password'          => password_hash($pass, PASSWORD_DEFAULT),
                'tempat_lahir'      => $tempatlahir,
                'tgl_lahir'         => $tgllahir,
                'jeniskelamin'      => $jeniskelamin,
                'pendidikan'        => $pendidikan,
                'alamat'            => $alamat,
                'company'           => $company,
                'position'          => $posisi,
                'agama'             => $agama,
                'departement_id'    => $departement_id,
                'foto'              => $foto,
                'status_active'     => 'Yes'
            ];

            $json = json_encode($dataUpdate);

            # update users
            $this->db->trans_begin();
            $this->Md_users->updateData($author, $dataUpdate);

            # merekam log sistem
            /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
            addLog('Update Data', 'Profil Users', $json);

            if ($this->db->trans_status() == TRUE) {
                $this->db->trans_commit();
                // menyimpan data di session
                $sess=[
                    'foto'  => $foto,
                    'nama'  => $nama
                ];
                $this->session->set_userdata($sess);

                $this->session->set_flashdata('alert','success');
                redirect('dashboard/profile');
            } else {
                $this->db->trans_rollback();
                $this->session->set_flashdata('alert','error');
                redirect('dashboard/profile');
            }

        }else{
            $this->session->set_flashdata('alert','warning');
            redirect('dashboard/profile');
        }
    }

    public function page_404(){
        $this->load->view('ui/page_404');
    }
}