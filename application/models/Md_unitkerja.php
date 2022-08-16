<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Md_unitkerja extends CI_Model {
    var $table = 'unitkerja';

    function getDataTable(){
        $this->db->select('d.*, ROW_NUMBER() OVER() AS no');
        $this->db->from($this->table.' as d');
        $this->db->where('status',1);
        return $this->db->get()->result();
    }

    function getAllunitkerja(){
        $this->db->where('status', 1);
        return $this->db->get($this->table)->result();
    }

    function addData($data){
        $this->db->insert($this->table, $data); 
        return $this->db->insert_id();
    }

    function getUnitkerjaByUnitkerja($unitkerja){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('status',1);
        $this->db->where('lower(unitkerja)',strtolower($unitkerja));
        return $this->db->get()->row();
    }

    function getUnitkerjaByunitkerjaId($unitkerja_id){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('status',1);
        $this->db->where('unitkerja_id',$unitkerja_id);
        return $this->db->get()->row();
    }

    function updateData($id,$data){
        $this->db->where('unitkerja_id', $id);
        $this->db->update($this->table, $data);
    }
}

/* End of file Md_unitkerja.php */
/* Location: ./application/models/Md_unitkerja.php */
