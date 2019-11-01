<?php
class User_model extends CI_Model {
  public function __construct()
  {
    $this->load->database();
  }

  public function getDetail($id)
  {
    $query = $this->db->get_where('client', array('id' => $id));
    return $query->result_array();
  }
}