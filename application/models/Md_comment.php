<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Md_comment extends CI_Model {
    var $table = 'comment';

    function addData($data){
        $this->db->insert($this->table, $data); 
        return $this->db->insert_id();
    }

    function getCountCommentByCardid($card_id){
        $this->db->select('count(*) as jumlah');
        $this->db->from($this->table);
        $this->db->where('status',1);
        $this->db->where('card_id',$card_id);
        return $this->db->get()->row();
    }

    public function getCommentByCardid($card_id)
    {
        $this->db->select('c.comment_id,c.comment,p.nama,p.foto,c.tglpost');
        $this->db->from($this->table.' as c');
        $this->db->join('pengguna p','p.pengguna_id = c.author');
        $this->db->where('c.status',1);
        $this->db->where('c.card_id',$card_id);
        $this->db->order_by('c.comment_id','desc');
        return $this->db->get()->result();
    }

    function updateData($id,$data){
        $this->db->where('comment_id', $id);
        $this->db->update($this->table, $data);
    }
}

/* End of file Md_comment.php */
/* Location: ./application/models/Md_comment.php */
