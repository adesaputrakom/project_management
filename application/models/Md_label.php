<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Md_label extends CI_Model {
    var $table = 'label';

    function getDataTable(){
        $this->db->select('l.*, ROW_NUMBER() OVER() AS no');
        $this->db->from($this->table.' as l');
        $this->db->where('status',1);
        return $this->db->get()->result();
    }

    function getAllLabel(){
        $this->db->where('status', 1);
        return $this->db->get($this->table)->result();
    }

    function addData($data){
        $this->db->insert($this->table, $data); 
        return $this->db->insert_id();
    }

    function getLabelByLabel($label){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('status',1);
        $this->db->where('lower(label)',strtolower($label));
        return $this->db->get()->row();
    }

    function getLabelByLabelid($label_id){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('status',1);
        $this->db->where('label_id',$label_id);
        return $this->db->get()->row();
    }

    function getLabelByCardid($card_id){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('status',1);
        $this->db->where('card_id',$card_id);
        return $this->db->get()->result();
    }

    function updateData($id,$data){
        $this->db->where('label_id', $id);
        $this->db->update($this->table, $data);
    }
}

/* End of file Md_label.php */
/* Location: ./application/models/Md_label.php */
