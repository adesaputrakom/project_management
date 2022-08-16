<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Md_board_relation extends CI_Model {
    var $table = 'board_relation';

    function addDatas($data){
        $this->db->insert_batch($this->table, $data);
    }

    function updateData($id,$data){
        $this->db->where('board_relation_id', $id);
        $this->db->update($this->table, $data);
    }

    function updateDatas($data){
        $this->db->update_batch($this->table,$data,'board_relation_id');
    }

    function getBoardrelationByWorkspaceid($workspace_id){
        $this->db->select('br.board_relation_id, br.board_id,b.board,b.color');
        $this->db->from($this->table.' as br');
        $this->db->join('board b','b.board_id=br.board_id and b.status=1');
        $this->db->where('br.workspace_id', $workspace_id);
        $this->db->where('br.status',1);
        $this->db->order_by('br.board_relation_id','asc');
        return $this->db->get()->result();
    }
}

/* End of file Md_board_relation.php */
/* Location: ./application/models/Md_board_relation.php */
