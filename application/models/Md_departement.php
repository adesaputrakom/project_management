<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Md_departement extends CI_Model {
    var $table = 'departement';

    function getDataTable(){
        $this->db->select('d.*, ROW_NUMBER() OVER() AS no');
        $this->db->from($this->table.' as d');
        $this->db->where('status',1);
        return $this->db->get()->result();
    }

    function getAlldepartement(){
        $this->db->where('status', 1);
        return $this->db->get($this->table)->result();
    }

    function addData($data){
        $this->db->insert($this->table, $data); 
        return $this->db->insert_id();
    }

    function getDepartementByDepartement($departement){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('status',1);
        $this->db->where('lower(departement)',strtolower($departement));
        return $this->db->get()->row();
    }

    function getDepartementByDepartementId($departement_id){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('status',1);
        $this->db->where('departement_id',$departement_id);
        return $this->db->get()->row();
    }

    function updateData($id,$data){
        $this->db->where('departement_id', $id);
        $this->db->update($this->table, $data);
    }
}

/* End of file Md_departement.php */
/* Location: ./application/models/Md_departement.php */
