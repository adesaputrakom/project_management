<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Md_attachment extends CI_Model {
    var $table = 'attachment';

    function addData($data){
        $this->db->insert($this->table, $data); 
        return $this->db->insert_id();
    }

    function getAttachmentByCardid($card_id){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('status',1);
        $this->db->where('card_id',$card_id);
        return $this->db->get()->result();
    }

    function updateData($id,$data){
        $this->db->where('attachment_id', $id);
        $this->db->update($this->table, $data);
    }
}

/* End of file Md_attachment.php */
/* Location: ./application/models/Md_attachment.php */
