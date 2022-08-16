<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pmcron extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        # helper
        $this->load->helper('encryption_id');
        $this->load->helper('project');

        # model
        $this->load->model('Md_mailbox');

        date_default_timezone_set('Asia/Jakarta');
    }

    public function index()
    {
        // sending mail box
        var_dump(sendMail());
    }
}
