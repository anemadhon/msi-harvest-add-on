<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Productcosting_model extends CI_Model {

	function getProdCostData($fromDate='', $toDate='', $status=''){
		$kd_plant = $this->session->userdata['ADMIN']['plant'];
		$this->db->distinct();
		$this->db->select('a.id_prod_cost_header, a.product_name, a.existing_bom_code, a.existing_bom_name, a.product_qty, a.product_uom, a.status, a.created_date, a.status_head,
		(SELECT admin_realname FROM d_admin WHERE admin_id = a.id_user_input) as created_by,
		(SELECT admin_realname FROM d_admin WHERE admin_id = a.id_user_approved) as approved_by');
		$this->db->from('t_prod_cost_header a');
		$this->db->join('t_prod_cost_detail b', 'a.id_prod_cost_header = b.id_prod_cost_header');
		$this->db->where('a.plant', $kd_plant);
		
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

		$query = $this->db->get();
		$data = $query->result_array();
		return $data;
	}
	
	function getCategory(){
		$SAP_MSI = $this->load->database('SAP_MSI', TRUE);
		$SAP_MSI->select('yc.Code, yc.Name');
		$SAP_MSI->from('@YBC_COST as yc');
		$query = $SAP_MSI->get();

		if(($query)&&($query->num_rows() > 0)){
			return $query->result_array();
		}else{
			return FALSE;
		}
	}

	function sap_costing_existing_bom_references($material_no=''){
		$SAP_MSI = $this->load->database('SAP_MSI', TRUE);
		$SAP_MSI->select("T0.Code, T1.ItemName, T0.U_Locked, T1.InvntryUom, T0.Qauntity");
		$SAP_MSI->from('OITT T0');
		$SAP_MSI->join('OITM T1','T1.ItemCode = T0.Code');
		$SAP_MSI->where("ISNULL(U_CantProduce,'') <> 'Y' ", null, false);
		
		if($material_no != ''){
			$SAP_MSI->where('T0.Code',$material_no);
		}
		$query = $SAP_MSI->get();

		if(($query)&&($query->num_rows() > 0)){
			return $query->result_array();
		}else{
			return FALSE;
		}
	}

	function getDataForQFactorFormula($code){
		$SAP_MSI = $this->load->database('SAP_MSI', TRUE);
		$SAP_MSI->select('yc.U_QFactor as q_factor, yc.U_MinCost as min_cost, yc.U_MaxCost as max_cost');
		$SAP_MSI->from('@YBC_COST as yc');
		$SAP_MSI->where('Code', $code);
		$query = $SAP_MSI->get();

		if(($query)&&($query->num_rows() > 0)){
			return $query->row_array();
		}else{
			return FALSE;
		}
	}

	function showMatrialGroup(){
        $SAP_MSI = $this->load->database('SAP_MSI', TRUE);
        $SAP_MSI->distinct();
        $SAP_MSI->select('ItmsGrpNam');
        $SAP_MSI->from('OITB t0');
        $SAP_MSI->join('OITM t1','t0.ItmsGrpCod = t1.ItmsGrpCod','inner');
        $SAP_MSI->where('t1.validFor', 'Y');

        $query = $SAP_MSI->get();
        $ret = $query->result_array();
        return $ret;
	}

	function getAllDataItems($itmGrp = ''){
		$kd_plant = $this->session->userdata['ADMIN']['plant'];
        $SAP_MSI = $this->load->database('SAP_MSI', TRUE);
        $SAP_MSI->select('t0.ItemCode as MATNR,t0.ItemName as MAKTX,t0.ItmsGrpCod as DISPO,t0.InvntryUom as UNIT,t1.ItmsGrpNam as DSNAM');
        $SAP_MSI->from('OITM  t0');
        $SAP_MSI->join('oitb t1','t1.ItmsGrpCod = t0.ItmsGrpCod','inner');
        $SAP_MSI->where('validFor', 'Y');
		$SAP_MSI->where('t0.InvntItem', 'Y');

		if($itmGrp != ''){
			$SAP_MSI->where('t1.ItmsGrpNam', $itmGrp);
		}
		
		$query = $SAP_MSI->get();
        
        if(($query)&&($query->num_rows() > 0)) {
            return $query->result_array();
        }else {
            return FALSE;
        }
	}

	function getDataItemSelected($itemSelect='') {
		$kd_plant = $this->session->userdata['ADMIN']['plant'];
		$SAP_MSI = $this->load->database('SAP_MSI', TRUE);
		$SAP_MSI->select('t0.ItemCode as MATNR, t0.ItemName as MAKTX, t0.ItmsGrpCod as DISPO, t0.BuyUnitMsr as UNIT, t0.InvntryUom as UNIT1, t0.NumInBuy as NUM, t1.ItmsGrpNam as DSNAM, t2.OnHand');
		$SAP_MSI->from('OITM t0');
		$SAP_MSI->join('OITB t1','t1.ItmsGrpCod = t0.ItmsGrpCod','inner');
		$SAP_MSI->join('OITW t2','t2.ItemCode = t0.ItemCode','inner');
		$SAP_MSI->where('validFor', 'Y');
		$SAP_MSI->where('t0.InvntItem', 'Y');
		$SAP_MSI->where('WhsCode ', $kd_plant);
	
		if($itemSelect != ''){
			$SAP_MSI->where('t0.ItemCode', $itemSelect);
		}
		
		$item_groups = $SAP_MSI->get();
		
		if ($item_groups->num_rows() > 0) {
			return $item_groups->row_array();
		} else {
			return FALSE;
		}
	}

	function getDataLastPurchaseItemSelected($itemSelect=''){
		$SAP_MSI = $this->load->database('SAP_MSI', TRUE);
		$SAP_MSI->select('LastPurPrc as LastPrice'); 
		$SAP_MSI->from('OITM');
		$SAP_MSI->where('itemcode',$itemSelect);

		$last = $SAP_MSI->get();
		
		if ($last->num_rows() > 0) {
			return $last->row_array();
		} else {
			return FALSE;
		}
	}

	function getExistingBomData($material_no=''){
		$SAP_MSI = $this->load->database('SAP_MSI', TRUE);
		$SAP_MSI->select("T0.Code, T1.ItemName, T0.U_Locked, T1.InvntryUom, T0.Qauntity");
		$SAP_MSI->from('OITT T0');
		$SAP_MSI->join('OITM T1','T1.ItemCode = T0.Code');
		$SAP_MSI->where("ISNULL(U_CantProduce,'') <> 'Y' ", null, false);
		
		if($material_no != ''){
			$SAP_MSI->where('T0.Code',$material_no);
		}
		$query = $SAP_MSI->get();

		if(($query)&&($query->num_rows() > 0)){
			return $query->result_array();
		}else{
			return FALSE;
		}
	}

	function getDataDetailForExistingBom($kode_paket){
		$SAP_MSI = $this->load->database('SAP_MSI', TRUE);
		$SAP_MSI->select("a.Father id_mpaket_header,a.ChildNum id_mpaket_h_detail, a.Code material_no, b.ItemName material_desc, a.Quantity quantity, b.InvntryUom uom");
		$SAP_MSI->from('ITT1 a');
		$SAP_MSI->join('OITM b','a.Code = b.ItemCode');
		$SAP_MSI->where('a.Father', $kode_paket);

		$query = $SAP_MSI->get();

		if(($query)&&($query->num_rows() > 0)){
			return $query->result_array();
		}else{
			return FALSE;
		}
	}

	/* function getDataDetailValidForExistingBom($material_no){
		$SAP_MSI = $this->load->database('SAP_MSI', TRUE);
		$SAP_MSI->select('OITM.validFor,OITB.DecreasAc');
		$SAP_MSI->from('OITM');
		$SAP_MSI->join('OITB','OITM.ItmsGrpCod = OITB.ItmsGrpCod');
		$SAP_MSI->where('ItemCode',$material_no);
		$query = $SAP_MSI->get();

		if(($query)&&($query->num_rows() > 0)){
			return $query->result_array();
		}else{
		return FALSE;
		}
	} */

	function getDataDetailUOMForExistingBom($material_no){
		$SAP_MSI = $this->load->database('SAP_MSI', TRUE);
		$SAP_MSI->select('InvntryUom as UNIT');
		$SAP_MSI->from('OITM');

		if($material_no != ''){
			$SAP_MSI->where('ItemCode',$material_no);
		}
		$query = $SAP_MSI->get();

		if(($query)&&($query->num_rows() > 0)){
			return $query->result_array();
		}else{
			return FALSE;
		}
	}

	function getDataDetailItemBOMForExistingBom($kode_paket,$material_no){
		$SAP_MSI = $this->load->database('SAP_MSI', TRUE);
		$SAP_MSI->distinct();
		$SAP_MSI->select("T1.U_SubsName as NAME,T1.U_ItemCodeBOM,T1.U_SubsQty,T1.U_SubsCode,T1.Code,T1.U_SubsUOM");
		$SAP_MSI->from('@MSI_ALT_ITM_HDR T0');
		$SAP_MSI->join('@MSI_ALT_ITM_DTL T1','T1.Code = T0.Code');
		$SAP_MSI->where('T0.Code',$kode_paket);
		$SAP_MSI->where('T1.U_ItemCodeBOM',$material_no);
		$query = $SAP_MSI->get();
		if(($query)&&($query->num_rows() > 0)){
			return $query->result_array();
		}else{
			return FALSE;
		}
	} 

	function selectIDPlant($id_outlet,$posting_date="") {

		$this->db->select_max('id_prod_cost_plant');
		$this->db->from('t_prod_cost_header');
		$this->db->where('plant', $id_outlet);
		$this->db->where('posting_date', $posting_date);

		$query = $this->db->get();

		if(($query)&&($query->num_rows() > 0)) {
			$prod_cost = $query->row_array();
			$id_prod_cost_outlet = $prod_cost['id_prod_cost_plant'] + 1;
		} else {
			$id_prod_cost_outlet = 1;
		}

		return $id_prod_cost_outlet;
	}

	function insertHeaderProdCost($data) {
		if($this->db->insert('t_prod_cost_header', $data))
			return $this->db->insert_id();
		else
			return FALSE;
	}

	function insertDetailProdCost($data) {
		if($this->db->insert('t_prod_cost_detail', $data))
			return $this->db->insert_id();
		else
			return FALSE;
	}

	function deleteProdCostHeader($id){
		$data = $this->selectProdCostHeader($id);
		$status = $data['status'];
		if ($status!=2) {
			if($this->selectProdCostDetailForDelete($id)){
				$this->db->where('id_prod_cost_header', $id);
				if($this->db->delete('t_prod_cost_header'))
					return TRUE;
				else
					return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	function selectProdCostHeader($id){
		$this->db->from('t_prod_cost_header');
		$this->db->where('id_prod_cost_header', $id);
		$query = $this->db->get();

		if(($query)&&($query->num_rows() > 0)){
			return $query->row_array();
		}else{
			return FALSE;
		}
	}

	function selectProdCostDetailForDelete($id) {
		$this->db->where('id_prod_cost_header', $id);
		if($this->db->delete('t_prod_cost_detail'))
			return TRUE;
		else
			return FALSE;
	}

	function selectDataDetail($id,$type){
		$this->db->from('t_prod_cost_detail');
		$this->db->where('id_prod_cost_header', $id);
		$this->db->where('item_type', $type);
		$query = $this->db->get();

		if(($query)&&($query->num_rows() > 0)){
			return $query->result_array();
		}else{
			return FALSE;
		}
	}

	function updateDataProdCostHeader($prod_cost_header){
		$update = array(
			'category_code' => $prod_cost_header['category_code'],
			'category_name' => $prod_cost_header['category_name'],
			'category_q_factor' => $prod_cost_header['category_q_factor'],
			'category_min' => $prod_cost_header['category_min'],
			'category_max' => $prod_cost_header['category_max'],
			'product_name' => $prod_cost_header['product_name'],
			'product_qty' => $prod_cost_header['product_qty'],
			'product_uom' => $prod_cost_header['product_uom'],
			'product_selling_price' => $prod_cost_header['product_selling_price'],
			'product_q_factor' => $prod_cost_header['product_q_factor'],
			'product_percentage' => $prod_cost_header['product_percentage'],
			'product_result' => $prod_cost_header['product_result'],
			'status' => $prod_cost_header['status'],
			'status_head' => $prod_cost_header['status_head'],
			'id_user_approved' => $prod_cost_header['id_user_approved']
		);
		$this->db->where('id_prod_cost_header', $prod_cost_header['id_prod_cost_header']);
        if($this->db->update('t_prod_cost_header', $update))
			return TRUE;
		else
			return FALSE;
	}
	
	function reject($reject){
		$update = array(
			'status_head' => $reject['status_head'],
			'reject_reason' => $reject['reject_reason'],
			'id_user_approved' => $reject['id_user_approved']
		);
		$this->db->where('id_prod_cost_header', $reject['id_prod_cost_header']);
        if($this->db->update('t_prod_cost_header', $update))
			return TRUE;
		else
			return FALSE;
	}
}