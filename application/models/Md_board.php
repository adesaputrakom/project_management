<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Md_board extends CI_Model {
    var $table = 'board';

    function getDataTable(){
        $this->db->select('b.*, ROW_NUMBER() OVER() AS no');
        $this->db->from($this->table.' as b');
        $this->db->where('status',1);
        return $this->db->get()->result();
    }

    function getAllBoard(){
        $this->db->where('status', 1);
        return $this->db->get($this->table)->result();
    }

    function addData($data){
        $this->db->insert($this->table, $data); 
        return $this->db->insert_id();
    }

    function getBoardByBoard($board){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('status',1);
        $this->db->where('lower(board)',strtolower($board));
        return $this->db->get()->row();
    }

    function getBoardByBoardId($board_id){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('status',1);
        $this->db->where('board_id',$board_id);
        return $this->db->get()->row();
    }

    function updateData($id,$data){
        $this->db->where('board_id', $id);
        $this->db->update($this->table, $data);
    }
}

/* End of file Md_board.php */
/* Location: ./application/models/Md_board.php */
