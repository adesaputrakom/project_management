<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Md_carddetail extends CI_Model {
    var $table = 'card_detail';

    function addData($data){
        $this->db->insert($this->table, $data); 
        return $this->db->insert_id();
    }

    function getCarddetailByCardid($card_id){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('status',1);
        $this->db->where('card_id',$card_id);
        return $this->db->get()->result();
    }

    function getCarddetailById($id){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('status',1);
        $this->db->where('carddetail_id',$id);
        return $this->db->get()->row();
    }

    function updateData($id,$data){
        $this->db->where('carddetail_id', $id);
        $this->db->update($this->table, $data);
    }
}

/* End of file Md_carddetail.php */
/* Location: ./application/models/Md_carddetail.php */
