<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Goodissue_model extends CI_Model {

    public function getDataGI_Header($fromDate='', $toDate='', $status=''){
        $kd_plant = $this->session->userdata['ADMIN']['plant'];
        $this->db->select('*, (select admin_realname from d_admin where admin_id = t_issue_header1.id_user_input) as user_input, (select admin_realname from d_admin where admin_id = t_issue_header1.id_user_approved) as user_approved');
        $this->db->from('t_issue_header1');
        $this->db->where('plant', $kd_plant);
        if((!empty($fromDate)) || (!empty($toDate))){
            if( (!empty($fromDate)) || (!empty($toDate)) ) {
            $this->db->where("posting_date BETWEEN '$fromDate' AND '$toDate'");
            } else if( (!empty($fromDate))) {
            $this->db->where("posting_date >= '$fromDate'");
            } else if( (!empty($toDate))) {
            $this->db->where("posting_date <= '$toDate'");
            }
        }
        if((!empty($status))){
            $this->db->where('status', $status);
        }
        $this->db->order_by('id_issue_header', 'desc');
        $query = $this->db->get();
        $gi = $query->result_array();
        return $gi;

    }

    function showMatrialGroup(){
        $SAP_MSI = $this->load->database('SAP_MSI', TRUE);
        $SAP_MSI->select('ItmsGrpNam');
        $SAP_MSI->from('OITB');

        $query = $SAP_MSI->get();
        $gi = $query->result_array();
        return $gi;
    }

    function showReason(){
        $this->db->select('reason_id, reason_name');
        $this->db->from('m_issue_reason');

        $query = $this->db->get();
        $gi_r = $query->result_array();
        return $gi_r;
    }

    function getDataMaterialGroup($item_group_code ='all'){
        $kd_plant = $this->session->userdata['ADMIN']['plant'];
        $trans_type = 'grnonpo';

        $SAP_MSI = $this->load->database('SAP_MSI', TRUE);
        $SAP_MSI->select('t0.ItemCode as MATNR,t0.ItemName as MAKTX,t0.ItmsGrpCod as DISPO,t0.InvntryUom as UNIT,t1.ItmsGrpNam as DSNAM');
        $SAP_MSI->from('OITM  t0');
        $SAP_MSI->join('oitb t1','t1.ItmsGrpCod = t0.ItmsGrpCod','inner');
        $SAP_MSI->where('validFor', 'Y');
        $SAP_MSI->where('InvntItem', 'Y');

        if($item_group_code !='all'){
            $SAP_MSI->where('t1.ItmsGrpNam', $item_group_code);
        }

        $query = $SAP_MSI->get();

        if(($query)&&($query->num_rows()>0))
            return $query->result_array();
		else
			return FALSE;
    }

    function getDataMaterialGroupSelect($itemSelect){
        $trans_type = 'stdstock';
        $kd_plant = $this->session->userdata['ADMIN']['plant'];
        if(($itemSelect != '') || ($itemSelect != null)){

            $SAP_MSI = $this->load->database('SAP_MSI', TRUE);
            $SAP_MSI->select('t0.ItemCode as MATNR,t0.ItemName as MAKTX,t0.ItmsGrpCod as DISPO,t0.InvntryUom as UNIT, (t1.OnHand - t1.IsCommited) as QTYWH');
            $SAP_MSI->from('OITM t0');
            $SAP_MSI->join('OITW t1','t1.ItemCode = t0.ItemCode','inner');
            $SAP_MSI->where('validFor', 'Y');
            $SAP_MSI->where('InvntItem', 'Y');
            $SAP_MSI->where('t0.ItemCode',$itemSelect);
            $SAP_MSI->where('t1.WhsCode',$kd_plant);

            $query = $SAP_MSI->get();
            return $query->result_array();
        }else{
            return false;
        }
    }

    function id_gi_plant_new_select($id_outlet,$created_date="",$id_gi_header="") {

        if (empty($created_date))
           $created_date=$this->m_general->posting_date_select_max();
        if (empty($id_outlet))
           $id_outlet=$this->session->userdata['ADMIN']['plant'];

		$this->db->select_max('id_issue_plant');
		$this->db->from('t_issue_header1');
		$this->db->where('plant', $id_outlet);
	  	$this->db->where('posting_date', $created_date);
        if (!empty($id_gi_header)) {
    		$this->db->where('id_issue_header <> ', $id_gi_header);
        }

		$query = $this->db->get();

		if($query->num_rows() > 0) {
			$stdstock = $query->row_array();
			$id_stdstock_outlet = $stdstock['id_issue_plant'] + 1;
		}	else {
			$id_stdstock_outlet = 1;
		}

		return $id_stdstock_outlet;
    }

    function gi_header_insert($data) {
		if($this->db->insert('t_issue_header1', $data))
			return $this->db->insert_id();
		else
			return FALSE;
    }

    function gi_details_insert($data) {
		if($this->db->insert('t_issue_detail', $data))
			return $this->db->insert_id();
		else
			return FALSE;
    }

    function gi_header_select($id_issue_header) {
		$this->db->from('t_issue_header1');
		$this->db->where('id_issue_header', $id_issue_header);

		$query = $this->db->get();

		if($query->num_rows() > 0)
			return $query->row_array();
		else
			return FALSE;
    }

    function gi_detail_data_select($id_gi_header) {
        $this->db->from('t_issue_detail');
        $this->db->where('id_issue_header', $id_gi_header);

        $query = $this->db->get();

        if(($query)&&($query->num_rows() > 0))
            return $query->result_array();
        else
            return FALSE;
    }

    function in_whs_qty($plant,$item_code){
        $SAP_MSI = $this->load->database('SAP_MSI', TRUE);
        $SAP_MSI->select('(OnHand - IsCommited) as OnHand');
        $SAP_MSI->from('OITW');
        $SAP_MSI->where('WhsCode', $plant);
        $SAP_MSI->where('ItemCode', $item_code);
  
        $query = $SAP_MSI->get();
        $inwhs = $query->row_array();
        return $inwhs;
    }

    function update_gi_header($data){
        $update = array(
            'status' => $data['status'],
            'id_user_approved' => $data['id_user_approved'],
            'no_acara' => $data['no_acara'],
            'posting_date' => $data['posting_date']

        );
        $this->db->where('id_issue_header', $data['id_issue_header']);
        if($this->db->update('t_issue_header1', $update))
			return TRUE;
		else
			return FALSE;
    }

    function gi_header_delete($id_issue_header){
        $data = $this->gi_header_select($id_issue_header);
        $back = $data['status'];
        if($back == 1){
          if($this->gi_details_delete($id_issue_header)){
            $this->db->where('id_issue_header', $id_issue_header);
            if($this->db->delete('t_issue_header1'))
                return TRUE;
            else
                return FALSE;
            }
        }else{
          return FALSE;
        }  
      }

    function gi_details_delete($id_issue_header){
        $this->db->where('id_issue_header', $id_issue_header);
		if($this->db->delete('t_issue_detail'))
			return TRUE;
		else
			return FALSE;
    }

    function gi_header_cancel($data){
        $update = array(
            'status' => $data['cancel'],
            'id_user_cancel' => $data['id_user_cancel']

        );
        $this->db->where('id_issue_header', $data['id_issue_header']);
        if($this->db->update('t_issue_header1', $update))
			return TRUE;
		else
			return FALSE;
    }

    function printPdf($id){
        $this->db->select('*');
        $this->db->from('t_issue_header1');
        $this->db->join('t_issue_detail','t_issue_header1.id_issue_header = t_issue_detail.id_issue_header','inner');
        $this->db->where('t_issue_header1.id_issue_header',$id);

        $query = $this->db->get();
        $gi = $query->result_array();
        return $gi;
    }
}