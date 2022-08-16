<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Md_mailbox extends CI_Model {
    var $table = 'mailbox';
    
    function addMailbox($data) {
        $this->db->insert($this->table, $data);
    }
    
    function addMultipleMailbox($data = array()) {
        $this->db->insert_batch($this->table,$data);
    }
    
    function updateMailbox($id, $data) {
        $this->db->where('mailbox_id', $id);
        $this->db->update($this->table, $data);
    }
    
    function getMailboxById($kode) {
        return $this->db->get_where($this->table, array('mailbox_id' => $kode))->row();
    }
    
    function getMailboxByToAndSubject($to,$subject) {
        return $this->db->get_where($this->table, array('to' => $to, 'subject' => $subject, 'status' => 1))->row();
    }
    
    function getMailboxByStatusKirimByLimit($status,$limit) {
        $this->db->select('mailbox.*')
                ->from('mailbox as mailbox')
                ->where('mailbox.status', 1)
                ->where('mailbox.statuskirim', $status)
                ->limit($limit)
                ->order_by('mailbox_id', 'asc');
        return $this->db->get()->result();
    }
    
    function getMailbox(){ //yang belum dikirim
        $this->db->where('statuskirim', 'Draft');
        $this->db->where('status', 1);
        $this->db->order_by('mailbox_id', 'asc');
        return $this->db->get($this->table)->row();
    }    
}

/* End of file Md_mailbox.php */
/* Location: ./application/models/Md_mailbox.php */