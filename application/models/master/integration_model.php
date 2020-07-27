<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Integration_model extends CI_Model{

    function __construct(){
        parent::__construct(); 
	}
	
	function freeze(){
		$kd_plant = $this->session->userdata['ADMIN']['plant'];
		$this->db->select('status, freeze, am_approved, rm_approved');
		$this->db->from('t_opname_header');
		$this->db->where('plant',$kd_plant);
		$this->db->order_by('id_opname_header','desc');
		$this->db->limit(1);
		
		$query = $this->db->get();
	
		if(($query)&&($query->num_rows() > 0)){
		  return $query->row_array();
		}else{
		  return FALSE;
		}
	}

    public function showIntegration(){
		$kd_plant = $this->session->userdata['ADMIN']['plant'];
		$sql = "SELECT * FROM error_log WHERE modul NOT LIKE 'Module:%' AND Whs = '".$kd_plant."' ORDER BY time_error DESC";
		$query = $this->db->query($sql);
		$num = $query->num_rows();
		$result = $query->result();

		if($num > 0){
			return $result;
		}else{
			return FALSE;
		}	
	}
}