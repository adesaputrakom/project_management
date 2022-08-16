<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Md_workspace extends CI_Model {
    var $table = 'workspace';

    function getAllworkspace(){
        $this->db->select('w.*, tb_member.totalmember, tb_brelation.totalboard, d.departement');
        $this->db->join('departement d','d.departement_id = w.departement_id','left');
        $this->db->from($this->table.' as w');
        $this->db->join("(select workspace_id,count(workspace_id) as totalmember from member where status=1 group by workspace_id) as tb_member","tb_member.workspace_id = w.workspace_id");
        $this->db->join("(select workspace_id,count(workspace_id) as totalboard from board_relation where status=1 group by workspace_id) as tb_brelation","tb_brelation.workspace_id = w.workspace_id");

        $this->db->where('w.status', 1);
        return $this->db->get()->result();
    }

    function addData($data){
        $this->db->insert($this->table, $data); 
        return $this->db->insert_id();
    }

    function getWorkspaceByWorkspaceAndDepartement($workspace,$departement_id,$except=''){
        $this->db->select('*');
        $this->db->from($this->table.' as w');
        $this->db->where('w.status',1);
        $this->db->where('lower(w.nm_workspace)',strtolower($workspace));

        if($departement_id){
            $this->db->where('w.departement_id',$departement_id);
        }else{
            $this->db->where('w.departement_id is null');
        }

        if($except){ //kecuali
            $this->db->where('w.workspace_id !=',$except);
        }
        return $this->db->get()->result();
    }

    function updateData($id,$data){
        $this->db->where('workspace_id', $id);
        $this->db->update($this->table, $data);
    }

    function getWorkspaceById($id){
        $this->db->select('w.*, d.departement');
        $this->db->from($this->table.' as w');
        $this->db->join('departement d','d.departement_id = w.departement_id and d.status=1','left');
        $this->db->where('w.status',1);
        $this->db->where('w.workspace_id',$id);
        return $this->db->get()->row();
    }
}

/* End of file Md_workspace.php */
/* Location: ./application/models/Md_workspace.php */
