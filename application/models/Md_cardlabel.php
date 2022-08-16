<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Md_cardlabel extends CI_Model {
    var $table = 'card_label';

    function addData($data){
        $this->db->insert($this->table, $data); 
        return $this->db->insert_id();
    }

    function getCardlabelByCardid($card_id){
        $this->db->select('cl.cardlabel_id, l.label_id, l.label, l.color');
        $this->db->from($this->table.' as cl');
        $this->db->join('label l','l.label_id = cl.label_id');
        $this->db->where('cl.status',1);
        $this->db->where('cl.card_id',$card_id);
        return $this->db->get()->result();
    }

    function updateData($id,$data){
        $this->db->where('cardlabel_id', $id);
        $this->db->update($this->table, $data);
    }

    function getCardlabelByCardidAndLabelid($card_id, $label_id){
        $this->db->select('cl.cardlabel_id, l.label_id, l.label, l.color');
        $this->db->from($this->table.' as cl');
        $this->db->join('label l','l.label_id = cl.label_id');
        $this->db->where('cl.status',1);
        $this->db->where('cl.card_id',$card_id);
        $this->db->where('cl.label_id',$label_id);
        return $this->db->get()->row();
    }
}

/* End of file Md_cardlabel.php */
/* Location: ./application/models/Md_cardlabel.php */
