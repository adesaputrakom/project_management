<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Md_member extends CI_Model {
    var $table = 'member';

    function addDatas($data){
        $this->db->insert_batch($this->table, $data);
    }

    function updateData($id,$data){
        $this->db->where('member_id', $id);
        $this->db->update($this->table, $data);
    }

    function updateDatas($data){
        $this->db->update_batch($this->table,$data,'member_id');
    }

    function getMemberByWorkspaceid($workspace_id){
        $this->db->select('m.member_id,m.pengguna_id,p.nama,p.email');
        $this->db->from($this->table.' as m');
        $this->db->join('pengguna p','p.pengguna_id=m.pengguna_id and p.status=1');
        $this->db->where('m.workspace_id', $workspace_id);
        $this->db->where('m.status',1);
        return $this->db->get()->result();
    }

    function getMemberByWorkspaceidAndPenggunaId($workspace_id, $pengguna_id){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('workspace_id', $workspace_id);
        $this->db->where('pengguna_id', $pengguna_id);
        $this->db->where('status',1);
        return $this->db->get()->row();
    }
}

/* End of file Md_member.php */
/* Location: ./application/models/Md_member.php */
