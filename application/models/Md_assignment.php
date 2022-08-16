<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Md_assignment extends CI_Model {
    var $table = 'assignment';

    function addData($data){
        $this->db->insert($this->table, $data); 
        return $this->db->insert_id();
    }

    function getAssignmentByCardid($card_id){
        $this->db->select('a.assignment_id, a.pengguna_id, p.nama, p.foto');
        $this->db->from($this->table.' as a');
        $this->db->join('pengguna p','p.pengguna_id = a.pengguna_id');
        $this->db->where('a.status',1);
        $this->db->where('a.card_id',$card_id);
        return $this->db->get()->result();
    }

    function getAssignmentByCarddetailid($carddetail_id){
        $this->db->select('a.assignment_id, a.pengguna_id, p.nama, p.foto');
        $this->db->from($this->table.' as a');
        $this->db->join('pengguna p','p.pengguna_id = a.pengguna_id');
        $this->db->where('a.status',1);
        $this->db->where('a.carddetail_id',$carddetail_id);
        return $this->db->get()->row();
    }

    function updateData($id,$data){
        $this->db->where('assignment_id', $id);
        $this->db->update($this->table, $data);
    }

    function getAssignmentByCardidAndPenggunaid($card_id, $penggunaid){
        $this->db->select('a.assignment_id, a.pengguna_id, p.nama, p.foto');
        $this->db->from($this->table.' as a');
        $this->db->join('pengguna p','p.pengguna_id = a.pengguna_id');
        $this->db->where('a.status',1);
        $this->db->where('a.card_id',$card_id);
        $this->db->where('a.pengguna_id',$penggunaid);
        return $this->db->get()->row();
    }
}

/* End of file Md_assignment.php */
/* Location: ./application/models/Md_assignment.php */
