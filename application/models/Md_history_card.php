<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Md_history_card extends CI_Model {
    var $table = 'history_card';

    function addData($data){
        $this->db->insert($this->table, $data); 
        return $this->db->insert_id();
    }

    function getHistoryCardByCardId($card_id){
        $this->db->select('h.*');
        $this->db->from($this->table.' as h');
        $this->db->where('h.status',1);
        $this->db->where('h.card_id',$card_id);
        $this->db->order_by('h.historycard_id','desc');
        return $this->db->get()->result();
    }
}

/* End of file Md_history_card.php */
/* Location: ./application/models/Md_history_card.php */
