<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Md_users extends CI_Model
{
    var $table = 'pengguna';

    function getDataTable(){
        $this->db->select('p.*, d.departement, ROW_NUMBER() OVER() AS no');
        $this->db->from($this->table.' as p');
        $this->db->join('departement as d','d.departement_id=p.departement_id and d.status=1','left');
        $this->db->where('p.status',1);
        return $this->db->get()->result();
    }

    function getUserByUsernameOrEmail($username){
        $this->db->group_start();
        $this->db->where('username', $username);
        $this->db->or_where("lower(replace(email,' ','')) = ", $username);
        $this->db->group_end();
        $this->db->where('status', 1);
        return $this->db->get($this->table)->row();
    }

    function addData($data){
        $this->db->insert($this->table, $data); 
        return $this->db->insert_id();
    }

    function getUserByPenggunaId($pengguna_id){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('status',1);
        $this->db->where('pengguna_id',$pengguna_id);
        return $this->db->get()->row();
    }

    function updateData($id,$data){
        $this->db->where('pengguna_id', $id);
        $this->db->update($this->table, $data);
    }

    function getUserByUsername($username,$pengguna_id=''){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('status',1);
        $this->db->where('username',$username);
        if($pengguna_id){
            $this->db->where('pengguna_id !=',$pengguna_id);
        }
        return $this->db->get()->row();
    }

    function getUserByEmail($email,$pengguna_id=''){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('status',1);
        $this->db->where('lower(email)',strtolower($email));
        if($pengguna_id){
            $this->db->where('pengguna_id !=',$pengguna_id);
        }
        return $this->db->get()->row();
    }

    public function getAllUserActive()
    {
        $this->db->select('u.pengguna_id,u.nama,u.email,d.departement');
        $this->db->from($this->table.' as u');
        $this->db->join('departement d','d.departement_id=u.departement_id','left');
        $this->db->where('u.status',1);
        $this->db->where('u.status_active','Yes');
        return $this->db->get()->result();
    }

    function getUserByDepartementid($departement_id){
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('status',1);
        $this->db->where('departement_id',$departement_id);
        $this->db->where('status_active','Yes');
        return $this->db->get()->row();
    }

}

/* End of file Md_users.php */
/* Location: ./application/models/Md_users.php */