<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Md_card extends CI_Model {
    var $table = 'card';

    function addData($data){
        $this->db->insert($this->table, $data); 
        return $this->db->insert_id();
    }

    function getCardById($card_id){
        $this->db->select('c.*, b.board, p.nama as created');
        $this->db->from($this->table.' as c');
        $this->db->join('board b','b.board_id=c.board_id');
        $this->db->join('pengguna p','p.pengguna_id=c.author');
        $this->db->where('c.status',1);
        $this->db->where('c.card_id',$card_id);
        return $this->db->get()->row();
    }

    function updateData($id,$data){
        $this->db->where('card_id', $id);
        $this->db->update($this->table, $data);
    }

    function getCardByWorkspaceidAndBoardid($workspace_id, $board_id, $unitkerjaid=''){
        $this->db->select('c.*,u.unitkerja');
        $this->db->from($this->table.' as c');
        $this->db->join('unitkerja u','u.unitkerja_id=c.unitkerja_id');
        $this->db->where('c.status',1);
        $this->db->where('c.workspace_id',$workspace_id);
        $this->db->where('c.board_id',$board_id);
        if($unitkerjaid){
            $this->db->where('c.unitkerja_id',$unitkerjaid);
        }
        return $this->db->get()->result();
    }

    function getAllcard(){
        $this->db->select('c.*');
        $this->db->from($this->table.' as c');
        $this->db->join('workspace w','w.workspace_id=c.workspace_id and w.status=1');
        $this->db->where('c.status',1);
        return $this->db->get()->result();
    }
}

/* End of file Md_card.php */
/* Location: ./application/models/Md_card.php */
