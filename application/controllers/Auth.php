<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        # config
        $this->load->config('recaptcha');

        # library
        $this->load->library('Googleplus');

        # helper
        $this->load->helper('encryption_id');
        $this->load->helper('project');

        # model
        $this->load->model('Md_users');
        $this->load->model('Md_mailbox');
        $this->load->model('Md_logsistem');

        date_default_timezone_set('Asia/Jakarta');
    }

    public function index()
    {

        //mengecek token yang dikirim dari google ketika login via gmail
        if (isset($_GET['code'])) {

            $this->googleplus->getAuthenticate();
            $this->session->set_userdata('loginwithgoogle', true);
            $this->session->set_userdata('user_profile', $this->googleplus->getUserInfo());

            // print_r($this->googleplus->getAccessToken());
            // die;

            redirect('auth/signin');
        }

        $pageData['sitekey'] = $this->config->item('recaptcha_site_key');
        $pageData['login_url'] = $this->googleplus->loginURL();
        $this->load->view('view_login', $pageData);
    }

    public function signin()
    {
        //Mengambil data login via akun gmail
        if ($this->session->userdata('loginwithgoogle') == true) {
            $info = $data['user_profile'] = $this->session->userdata('user_profile');
            $email = $info['email'];
            $dataPengguna = $this->Md_users->getUserByUsernameOrEmail($email);

            if($dataPengguna){
                $data = array(
                    'pengguna_id' => encrypt($dataPengguna->pengguna_id),
                    'nama'      => $dataPengguna->nama,
                    'lo9s1st3m' => 'allowed',
                    'is_admin'  => false,
                    'double_akses'  => $dataPengguna->is_admin,
                    'foto'  => $dataPengguna->foto
                );
                // menyimpan data di session
                $this->session->set_userdata($data);
                addLog('Login', 'as User', 'pengguna ID' . $dataPengguna->pengguna_id);
                redirect('dashboard');
            }else{
                $this->session->unset_userdata('loginwithgoogle');
                $this->session->unset_userdata('user_profile');

                $this->session->set_flashdata('alert', 'danger');
                $this->session->set_flashdata('message', 'Anda tidak memiliki izin akses');
                redirect(base_url() . 'auth', 'refresh');
            }
        } else {

            // memastikan data required terisi
            $this->form_validation->set_rules('username', 'field username', 'required');
            $this->form_validation->set_rules('password', 'field password', 'required');
            $this->form_validation->set_rules('g-recaptcha_response', 'field recaptcha', 'required');

            //siteKey and SecretKey Google
            $secretKey=$this->config->item('recaptcha_secret_key');
            $siteKey=$this->config->item('recaptcha_site_key');
            
            define($secretKey, $siteKey);

            // Take Token Google Captcha
            $token = $this->input->post('g-recaptcha_response');
        
            // call curl to POST request
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => $secretKey, 'response' => $token)));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            $arrResponse = json_decode($response, true);

            # jika respons false maka redirect ke auth
            if(!$arrResponse["success"]){
                $this->session->set_flashdata('alert', 'danger');
                $this->session->set_flashdata('message', 'Ulangi Captcha');
                redirect(base_url() . 'auth', 'refresh');
            }
            
            // verify the response
            if($arrResponse["success"] != '1' && $arrResponse["score"] <= 0.5) {
                //redirect bila response lebih cepat dari 0.5 second
                $this->session->set_flashdata('alert', 'danger');
                $this->session->set_flashdata('message', 'Ulangi Captcha');
                redirect(base_url() . 'auth', 'refresh');
            } else {
                if ($this->form_validation->run() != FALSE) {
                    $username = strtolower(trim($this->input->post('username')));
                    $password = $this->input->post('password');
                    $dataPengguna = $this->Md_users->getUserByUsernameOrEmail($username);

                    // jika data karyawan ada
                    if ($dataPengguna) {
                        //cek password dengan metode password hash
                        if (password_verify($password, $dataPengguna->password)) {
                            $data = array(
                                'pengguna_id' => encrypt($dataPengguna->pengguna_id),
                                'nama'      => $dataPengguna->nama,
                                'lo9s1st3m' => 'allowed',
                                'is_admin'  => false,
                                'double_akses'  => $dataPengguna->is_admin,
                                'foto'  => $dataPengguna->foto
                            );
                            // menyimpan data di session
                            $this->session->set_userdata($data);
                            addLog('Login', 'as User', 'pengguna ID' . $dataPengguna->pengguna_id);
                            redirect('dashboard');
                        } else {
                            $this->session->set_flashdata('alert', 'danger');
                            $this->session->set_flashdata('message', 'Password Anda Salah, Silahkan Ulangi');
                            redirect(base_url() . 'auth', 'refresh');
                        }
                    } else { //data karyawan tidak ada atau tidak aktif
                        $this->session->set_flashdata('alert', 'danger');
                        $this->session->set_flashdata('message', 'Anda tidak memiliki izin akses');
                        redirect(base_url() . 'auth', 'refresh');
                    }
                } else { //form validasi false
                    $this->session->set_flashdata('alert', 'danger');
                    $this->session->set_flashdata('message', 'Seluruh field wajib');
                    redirect(base_url() . 'auth', 'refresh');
                }
            }
        }
    }

    public function signup($argv=''){
        if($argv == ''){

            if (isset($_GET['code'])) {
                $this->googleplus->getAuthenticate();
                $info = $this->googleplus->getUserInfo();
                $email = $info['email'];
                $nama = $info['name'];

                $dataPengguna = $this->Md_users->getUserByEmail($email);
                if($dataPengguna){
                    $this->session->set_flashdata('alert', 'warning');
                    $this->session->set_flashdata('message', 'Akun gmail anda sudah terdaftar di sistem kami !');
                    redirect(base_url() . 'auth/signup', 'refresh');
                }

                $password = 12345678;
                $dataInsert=[
                    'nama'          => $nama,
                    'email'         => $email,
                    'password'      => password_hash($password, PASSWORD_DEFAULT),
                    'status'        => 1,
                    'tglpost'       => date('Y-m-d H:i:s'),
                    'foto'          => 'default.png',
                    'status_active' => null
                ];

                $id = $this->Md_users->addData($dataInsert);
                # merekam log sistem
                /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                $keterangan = 'pengguna_id '.$id.', nama '.$nama.', email '.$email.', status_active Need Cofirm';
                addLog('Register', 'Pengguna', $keterangan,$id);

                # insert mailbox
                $code = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10).'&pid?='.encrypt($id);
                $msg = 'Klik link ini untuk <a href="'.base_url().'auth/verifikasi?code='.$code.'"> Verifikasi Akun </a> anda ..';
                $dataInsert = array(
                    'to' => $email,
                    'from' => 'pm@ptpn5.co.id',
                    'subject' => 'Verifikasi Akun',
                    'isi' => $msg,
                    'tglpost' => date('Y-m-d H:i:s'),
                    'statuskirim' => 'Draft',
                    'status' => 1,
                    'author' => $id
                );
                $this->Md_mailbox->addMailbox($dataInsert);


                if ($this->db->trans_status() == TRUE) {
                    $this->db->trans_commit();

                    #update kode aktivasi
                    $dataUpdate=['kode_aktivasi' => $code];
                    $this->Md_users->updateData($id,$dataUpdate);

                    $this->session->set_flashdata('alert', 'success');
                    $this->session->set_flashdata('message', 'Akun anda berhasil di daftarkan, cek email untuk verifikasi akun !');
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('alert', 'danger');
                    $this->session->set_flashdata('message', 'Akun anda gagal didaftarkan !');
                }
                redirect(base_url() . 'auth/signup', 'refresh');
            }

            $pageData['login_url'] = $this->googleplus->loginURL();
            $this->load->view('view_register',$pageData);
        }
        else if ($argv == 'submit'){

            $this->form_validation->set_rules('nama', 'field nama', 'required');
            $this->form_validation->set_rules('email', 'field email', 'required');
            $this->form_validation->set_rules('password', 'field password', 'required');
            $this->form_validation->set_rules('repassword', 'field repassword', 'required');

            if ($this->form_validation->run() != FALSE) {

                $nama       = $this->input->post('nama');
                $email      = $this->input->post('email');
                $password   = $this->input->post('password');
                $repassword = $this->input->post('repassword');

                $pengguna = $this->Md_users->getUserByEmail($email);
                if($pengguna){
                    echo json_encode(array('status' => 'warning', 'message' => 'Email ini sudah terdaftar ..!'));
                    die;
                }

                if($password !== $repassword){
                    echo json_encode(array('status' => 'warning', 'message' => 'Password tidak cocok !'));
                    die;
                }

                $dataInsert=[
                    'nama'          => $nama,
                    'email'         => $email,
                    'password'      => password_hash($password, PASSWORD_DEFAULT),
                    'status'        => 1,
                    'tglpost'       => date('Y-m-d H:i:s'),
                    'foto'          => 'default.png',
                    'status_active' => null
                ];

                $this->db->trans_begin();

                $id = $this->Md_users->addData($dataInsert);
                # merekam log sistem
                /* contoh : addLog('Add Data', 'nama tabel', 'keterangan') */
                $keterangan = 'pengguna_id '.$id.', nama '.$nama.', email '.$email.', status_active Need Cofirm';
                addLog('Register', 'Pengguna', $keterangan, $id);

                # insert mailbox
                $code = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10).'&pid?='.encrypt($id);
                $msg = 'Klik link ini untuk <a href="'.base_url().'auth/verifikasi?code='.$code.'"> Verifikasi Akun </a> anda ..';
                $dataInsert = array(
                    'to' => $email,
                    'from' => 'pm@ptpn5.co.id',
                    'subject' => 'Verifikasi Akun',
                    'isi' => $msg,
                    'tglpost' => date('Y-m-d H:i:s'),
                    'statuskirim' => 'Draft',
                    'status' => 1,
                    'author' => $id
                );
                $this->Md_mailbox->addMailbox($dataInsert);

                if ($this->db->trans_status() == TRUE) {
                    $this->db->trans_commit();

                    #update kode aktivasi
                    $dataUpdate=['kode_aktivasi' => $code];
                    $this->Md_users->updateData($id,$dataUpdate);

                    echo json_encode(array('status' => 'success', 'message' => 'Akun email anda berhasil didaftarkan, silahkan login !'));
                    die;
                } else {
                    $this->db->trans_rollback();
                    echo json_encode(array('status' => 'danger', 'message' => 'Data Gagal disimpan !'));
                    die;
                }
                    
            }else{
                echo json_encode(array('status' => 'danger', 'message' => 'Semua field wajib di isi !'));
                die;
            }
        }
    }

    public function forgot_password($argv=''){
        if($argv==''){
            $this->load->view('view_forgot_password');
        }
        else if ($argv == 'submit'){

            $this->form_validation->set_rules('email', 'field email', 'required');

            if ($this->form_validation->run() != FALSE) {

                $email = $this->input->post('email');

                $pengguna = $this->Md_users->getUserByEmail($email);
                if(!empty($pengguna)){
                    # cek sudah reset password apa belum
                    $reset_password = $this->Md_mailbox->getMailboxByToAndSubject($pengguna->email, 'Reset Password');
                    if (!isset($reset_password)) {
                        $send = TRUE;
                    } else {
                        $start = new DateTime($reset_password->tglpost);
                        $end = new DateTime(date('Y-m-d H:i:s'));
                        $diff = date_diff($end, $start);
                        $minute = $diff->format('%i');

                        if ($minute < 5) {
                            $send = FALSE;
                        } else {
                            $send = TRUE;
                        }
                    }

                    if ($send == TRUE) {
                        //enkripsi id
                        $length = 8;
                        $randomPass = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);

                        $msg = 'Dear ' . ucwords($pengguna->nama) . ',<br/><br/>
                                Berikut adalah informasi reset password yang di kirimkan melalui sistem Project Management PTPN V :<br><br><i>
                                Password Baru : ' . $randomPass . '
                                <i><br/><br/>
                                <br><br><br>Terimakasih.<br><br>
                                <br/>Do not reply to this computer-generated email. <br/>';

                        $dataInsert = array(
                            'to' => $pengguna->email,
                            'from' => 'pm@ptpn5.co.id',
                            'subject' => 'Reset Password',
                            'isi' => $msg,
                            'tglpost' => date('Y-m-d H:i:s'),
                            'statuskirim' => 'Draft',
                            'status' => 1,
                            'author' => $pengguna->pengguna_id
                        );

                        $dataUpdate = array(
                            'password' => password_hash($randomPass, PASSWORD_DEFAULT)
                        );

                        if($dataInsert){
                            $this->Md_mailbox->addMailbox($dataInsert);
                        }

                        $this->Md_users->updateData($pengguna->pengguna_id, $dataUpdate);

                        echo json_encode(array('status' => 'success', 'message' => 'Reset Password akan dikirim ke Email yang dituju !'));
                        die;

                    } else {
                        echo json_encode(array('status' => 'warning', 'message' => 'Reset Password anda sudah dikirim ke Email yang dituju, silahkan tunggu 5 menit lagi untuk mengirim ulang !'));
                        die;
                    }
                }else{
                    echo json_encode(array('status' => 'warning', 'message' => 'Email tidak ditemukan !'));
                    die;
                }
            }else{
                echo json_encode(array('status' => 'danger', 'message' => 'Email wajib di isi !'));
                die;
            }

        }
    }

    public function signout() {
        $this->session->sess_destroy();
        redirect(base_url() . 'auth', 'refresh');
    }

    public function switch($argv=''){
        if($argv == kodeswitchakses('administrator')){

            $pengguna_id = decrypt($this->session->userdata('pengguna_id'));
            if(empty($pengguna_id)){
                redirect('auth');
            }

            $data = $this->Md_users->getUserByPenggunaId($pengguna_id);
            if($data->is_admin != 'Yes'){
                redirect('auth');
            }
            $data = ['is_admin' => true];
            $this->session->set_userdata($data);
            addLog('Login', 'as Administrator', 'pengguna ID' . $pengguna_id);
            redirect('dashboard');

        }else if ($argv == kodeswitchakses('user')){

            $pengguna_id = decrypt($this->session->userdata('pengguna_id'));
            if(empty($pengguna_id)){
                redirect('auth');
            }
            $data = ['is_admin' => false];
            $this->session->set_userdata($data);
            addLog('Login', 'as User', 'pengguna ID' . $pengguna_id);
            redirect('dashboard');
        }else{
            redirect('dashboard/page_404');
        }
    }
}
