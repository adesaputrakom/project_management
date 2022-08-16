<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Md_logsistem extends CI_Model {
    var $table = 'logsistem';

    function addLog($data) {
        $this->db->insert($this->table, $data);      
    }

    function getAllLogsistem($limit=''){
        $this->db->select('l.*, p.nama');
        $this->db->from($this->table.' as l');
        $this->db->join('pengguna p','p.pengguna_id=l.pengguna_id');
        $this->db->where('l.status',1);
        if($limit){
            $this->db->limit($limit);
        }
        $this->db->order_by('l.logsistem_id','desc');
        return $this->db->get()->result();
    }
}

/* End of file Md_logsistem.php */
/* Location: ./application/models/Md_logsistem.php */
