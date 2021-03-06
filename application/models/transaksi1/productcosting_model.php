<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Productcosting_model extends CI_Model {

	function getProdCostData($fromDate='', $toDate='', $status='', $whoIsLogin){
		$kd_plant = $this->session->userdata['ADMIN']['plant'];
		$this->db->distinct();
		$this->db->select("a.id_prod_cost_header, a.product_name, a.existing_bom_code, a.existing_bom_name, a.product_qty, a.product_uom, a.status, a.created_date, a.status_head, a.approved_user_date, a.approved_head_dept_date, a.rejected_head_dept_date, a.prod_cost_no, a.status_cat_approver, a.status_cost_control, a.approved_cat_approver_date, a.rejected_cat_approver_date, a.approved_cost_control_date, a.rejected_cost_control_date, a.product_type,
		(SELECT admin_realname FROM d_admin WHERE admin_id = a.id_user_input) as created_by,
		(SELECT admin_realname FROM d_admin WHERE admin_id = a.id_user_approved) as approved_by,
		(SELECT dept FROM t_department WHERE dept_head_id = a.id_head_dept) as dept,
		(SELECT admin_realname FROM d_admin WHERE admin_username = a.category_approver) as category_approver,
		(SELECT admin_realname FROM d_admin WHERE admin_id = a.id_cost_control) as cost_control,
		(SELECT admin_realname FROM d_admin WHERE admin_id = a.id_head_dept) as head_dept");
		$this->db->from('t_prod_cost_header a');
		$this->db->join('t_prod_cost_detail b', 'a.id_prod_cost_header = b.id_prod_cost_header');
		$this->db->where('a.plant', $kd_plant);
		
		if ($whoIsLogin['flag'] == '01') {
			$this->db->where('a.id_user_input', $whoIsLogin['id']);
		} elseif ($whoIsLogin['flag'] == '02') {
			$this->db->where('a.id_user_approved', $whoIsLogin['id']);
		} elseif ($whoIsLogin['flag'] == '0203') {
			$this->db->where('a.id_user_approved', $whoIsLogin['id']);
			$this->db->where('a.category_approver', $whoIsLogin['username']);
		} elseif ($whoIsLogin['flag'] == '03') {
			$this->db->where('a.category_approver', $whoIsLogin['username']);
		} elseif ($whoIsLogin['flag'] == '0304') {
			$this->db->where('a.category_approver', $whoIsLogin['username']);
			$this->db->where('a.id_cost_control', $whoIsLogin['id']);
		} elseif ($whoIsLogin['flag'] == '04') {
			$this->db->where('a.id_cost_control', $whoIsLogin['id']);
		}
		
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

		$this->db->order_by('a.id_prod_cost_header','desc');

		$query = $this->db->get();
		$data = $query->result_array();
		return $data;
	}

	function getCatApprover($user){
		$SAP_MSI = $this->load->database('SAP_MSI', TRUE);
		$SAP_MSI->select('U_CatApprov as approver');
		$SAP_MSI->from('@YBC_COST as yc');
		$SAP_MSI->where('U_CatApprov', $user);
		$query = $SAP_MSI->get();

		if(($query)&&($query->num_rows() > 0)){
			return $query->row_array();
		}else{
			return FALSE;
		}
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
		$SAP_MSI->select('yc.U_QFactor as q_factor, yc.U_MinCost as min_cost, yc.U_MaxCost as max_cost, U_CatApprov as approver');
		$SAP_MSI->from('@YBC_COST as yc');
		$SAP_MSI->where('Code', $code);
		$query = $SAP_MSI->get();

		if(($query)&&($query->num_rows() > 0)){
			return $query->row_array();
		}else{
			return FALSE;
		}
	}

	function showMatrialGroup($flag){
        $SAP_MSI = $this->load->database('SAP_MSI', TRUE);
        $SAP_MSI->distinct();
        $SAP_MSI->select('ItmsGrpNam');
        $SAP_MSI->from('OITB t0');
		$SAP_MSI->join('OITM t1','t0.ItmsGrpCod = t1.ItmsGrpCod','inner');
		if ($flag == 'PK') {
			$SAP_MSI->where('t0.U_GroupType', 'PK');
		} 
		if ($flag == 'NONEPK') {
			$SAP_MSI->where("ISNULL(U_GroupType,'') <> 'PK' ", null, false);
		}
        $SAP_MSI->where('t1.validFor', 'Y');

        $query = $SAP_MSI->get();
        $ret = $query->result_array();
        return $ret;
	}

	function getAllDataItems($itmGrp = '', $type){
		$kd_plant = $this->session->userdata['ADMIN']['plant'];
        $SAP_MSI = $this->load->database('SAP_MSI', TRUE);
        $SAP_MSI->select('t0.ItemCode as MATNR,t0.ItemName as MAKTX,t0.ItmsGrpCod as DISPO,t0.InvntryUom as UNIT,t1.ItmsGrpNam as DSNAM, t1.U_PlusTaxCost as TAX');
        $SAP_MSI->from('OITM t0');
        $SAP_MSI->join('oitb t1','t1.ItmsGrpCod = t0.ItmsGrpCod','inner');
        $SAP_MSI->where('validFor', 'Y');
		$SAP_MSI->where('t0.InvntItem', 'Y');
		if ($type == 'pack') {
			$SAP_MSI->where('t1.U_GroupType', 'PK');
		} 
		if ($type == 'ing') {
			$SAP_MSI->where("ISNULL(t1.U_GroupType,'') <> 'PK' ", null, false);
		}
		if($itmGrp != '' && $itmGrp != 'all'){
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
	
		if($itemSelect != ''){
			$SAP_MSI->where('t0.ItemCode', $itemSelect);
		}
		
		$item_groups = $SAP_MSI->get();
		
		if ($item_groups->num_rows() > 0) {
			return $item_groups->row_array();
		} else {
			return $this->getDataItemSelectedWPSelling($itemSelect);
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
			return $this->getDataLastPurchaseItemSelectedWPSelling($itemSelect);
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

	function getDataDetailForExistingBomIng($kode_paket){
		$SAP_MSI = $this->load->database('SAP_MSI', TRUE);
		$SAP_MSI->select("a.Father id_mpaket_header,a.ChildNum id_mpaket_h_detail, a.Code material_no, b.ItemName material_desc, a.Quantity quantity, b.InvntryUom uom");
		$SAP_MSI->from('ITT1 a');
		$SAP_MSI->join('OITM b','a.Code = b.ItemCode');
		$SAP_MSI->join('OITB c','c.itmsgrpcod = b.ItmsGrpCod');
		$SAP_MSI->where('a.Father', $kode_paket);
		$SAP_MSI->where("ISNULL(c.U_GroupType,'') <> 'PK' ", null, false);

		$query = $SAP_MSI->get();

		if(($query)&&($query->num_rows() > 0)){
			return $query->result_array();
		}else{
			return FALSE;
		}
	}
	
	function getDataDetailForExistingBomPack($kode_paket){
		$SAP_MSI = $this->load->database('SAP_MSI', TRUE);
		$SAP_MSI->select("a.Father id_mpaket_header,a.ChildNum id_mpaket_h_detail, a.Code material_no, b.ItemName material_desc, a.Quantity quantity, b.InvntryUom uom");
		$SAP_MSI->from('ITT1 a');
		$SAP_MSI->join('OITM b','a.Code = b.ItemCode');
		$SAP_MSI->join('OITB c','c.itmsgrpcod = b.ItmsGrpCod');
		$SAP_MSI->where('a.Father', $kode_paket);
		$SAP_MSI->where('c.U_GroupType', 'PK');

		$query = $SAP_MSI->get();

		if(($query)&&($query->num_rows() > 0)){
			return $query->result_array();
		}else{
			return FALSE;
		}
	}

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
		if($this->db->insert('t_prod_cost_header', $data)) {
			return $this->db->insert_id();
		}else{
			return FALSE;
		}
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
			'category_approver' => $prod_cost_header['category_approver'],
			'product_name' => $prod_cost_header['product_name'],
			'product_qty' => $prod_cost_header['product_qty'],
			'product_uom' => $prod_cost_header['product_uom'],
			'product_selling_price' => $prod_cost_header['product_selling_price'],
			'product_q_factor' => $prod_cost_header['product_q_factor'],
			'product_percentage' => $prod_cost_header['product_percentage'],
			'product_result' => $prod_cost_header['product_result'],
			'product_result_div_product_qty' => $prod_cost_header['product_result_div_product_qty'],
			'lastmodified' => $prod_cost_header['lastmodified']
		);
		if ($prod_cost_header['flag'] == 2) {
			$update['status'] = $prod_cost_header['status'];
			$update['approved_user_date'] = $prod_cost_header['approved_user_date'];
			$update['id_user_approved'] = $prod_cost_header['id_user_approved'];
			$update['status_head'] = $prod_cost_header['status_head'];
			$update['status_cat_approver'] = $prod_cost_header['status_cat_approver'];
			$update['status_cost_control'] = $prod_cost_header['status_cost_control'];
			if ($prod_cost_header['status_head'] == 2) {
				$update['approved_head_dept_date'] = $prod_cost_header['approved_head_dept_date'];
				$update['id_head_dept'] = $prod_cost_header['id_head_dept'];
			}
		} elseif ($prod_cost_header['flag'] == 3) {
			$update['status'] = $prod_cost_header['status'];
			$update['status_head'] = $prod_cost_header['status_head'];
			$update['approved_head_dept_date'] = $prod_cost_header['approved_head_dept_date'];
			$update['id_head_dept'] = $prod_cost_header['id_head_dept'];
			if (isset($prod_cost_header['head_dept_username'])) {
				$update['status_cat_approver'] = $prod_cost_header['status_cat_approver'];
				$update['approved_cat_approver_date'] = $prod_cost_header['approved_cat_approver_date'];
			}
		} elseif ($prod_cost_header['flag'] == 4) {
			$update['status_cat_approver'] = $prod_cost_header['status_cat_approver'];
			$update['approved_cat_approver_date'] = $prod_cost_header['approved_cat_approver_date'];
		} elseif ($prod_cost_header['flag'] == 5) {
			$update['status_cost_control'] = $prod_cost_header['status_cost_control'];
			$update['approved_cost_control_date'] = $prod_cost_header['approved_cost_control_date'];
			$update['id_cost_control'] = $prod_cost_header['id_cost_control'];
		}
		
		$this->db->where('id_prod_cost_header', $prod_cost_header['id_prod_cost_header']);
        if($this->db->update('t_prod_cost_header', $update))
			return TRUE;
		else
			return FALSE;
	}
	
	function reject($reject){
		if ($reject['whosRejectFlag'] == 1) {
			$update = array(
				'status' => 1,
				'status_head' => $reject['status_head'],
				'reject_reason' => $reject['reject_reason'],
				'id_user_approved' => $reject['id_user_approved'],
				'rejected_head_dept_date' => $reject['rejected_head_dept_date']
			);
		} elseif ($reject['whosRejectFlag'] == 2) {
			$update = array(
				'status' => 1,
				'status_head' => 1,
				'status_cat_approver' => $reject['status_cat_approver'],
				'reject_reason' => $reject['reject_reason'],
				'rejected_cat_approver_date' => $reject['rejected_cat_approver_date']
			);
		} elseif ($reject['whosRejectFlag'] == 3) {
			$update = array(
				'status' => 1,
				'status_head' => 1,
				'status_cat_approver' => 1,
				'status_cost_control' => $reject['status_cost_control'],
				'reject_reason' => $reject['reject_reason'],
				'id_cost_control' => $reject['id_cost_control'],
				'rejected_cost_control_date' => $reject['rejected_cost_control_date']
			);
		}
		$this->db->where('id_prod_cost_header', $reject['id_prod_cost_header']);
        if($this->db->update('t_prod_cost_header', $update))
			return TRUE;
		else
			return FALSE;
	}

	function updateNoProdCost($no, $id){
		$update = array(
			'prod_cost_no' => $no
		);
		$this->db->where('id_prod_cost_header', $id);
        if($this->db->update('t_prod_cost_header', $update))
			return TRUE;
		else
			return FALSE;
	}

	function getAllDataItemsWPSelling($type){
		$this->db->distinct();
		$this->db->select("a.product_name as MAKTX, a.prod_cost_no as MATNR, 'N' as TAX");
		$this->db->from('t_prod_cost_header a');
		$this->db->join('t_prod_cost_detail b', 'a.id_prod_cost_header = b.id_prod_cost_header');
		if ($type == 'all') {
			$this->db->where('a.product_type', 1);
			$this->db->or_where('a.product_type', 2);
			$this->db->where('a.status_head', 2);
			$this->db->where('a.status_cat_approver', 2);
			$this->db->where('a.status_cost_control', 2);
		}
		if ($type == 1) {
			$this->db->where('a.product_type', $type);
		}
		if ($type == 2) {
			$this->db->where('a.product_type', $type);
			$this->db->where('a.status_head', 2);
			$this->db->where('a.status_cat_approver', 2);
			$this->db->where('a.status_cost_control', 2);
		}

		$query = $this->db->get();
		$data = $query->result_array();
		return $data;
	}

	/* function getAllDataItemsWPSelling($type){
		$this->db->distinct();
		$this->db->select("a.product_name as MAKTX, a.prod_cost_no as MATNR, 'N' as TAX");
		$this->db->from('t_prod_cost_header a');
		$this->db->join('t_prod_cost_detail b', 'a.id_prod_cost_header = b.id_prod_cost_header');
		$this->db->where('a.status_head', 2);
		if ($type == 'all') {
			$this->db->where('a.product_type', 1);
			$this->db->or_where('a.product_type', 2);
			$this->db->where('a.status_cat_approver', 2);
			$this->db->where('a.status_cost_control', 2);
		}
		if ($type != 'all') {
			$this->db->where('a.product_type', $type);
		}
		if ($type == 2) {
			$this->db->where('a.status_cat_approver', 2);
			$this->db->where('a.status_cost_control', 2);
		}

		$query = $this->db->get();
		$data = $query->result_array();
		return $data;
	} */
	
	function getDataItemSelectedWPSelling($code){
		$this->db->distinct();
		$this->db->select('a.product_name as MAKTX, a.product_uom as UNIT1');
		$this->db->from('t_prod_cost_header a');
		$this->db->join('t_prod_cost_detail b', 'a.id_prod_cost_header = b.id_prod_cost_header');
		$this->db->where('a.prod_cost_no', $code);

		$query = $this->db->get();
		return $query->row_array();
	}
	
	function getDataLastPurchaseItemSelectedWPSelling($code){
		$this->db->distinct();
		$this->db->select('a.product_result_div_product_qty as LastPrice');
		$this->db->from('t_prod_cost_header a');
		$this->db->join('t_prod_cost_detail b', 'a.id_prod_cost_header = b.id_prod_cost_header');
		$this->db->where('a.prod_cost_no', $code);

		$query = $this->db->get();
		return $query->row_array();
	}

	function getDeptUserLogin($user_id){
		$this->db->select('dept');
		$this->db->from('t_department a');
		$this->db->join('d_admin b', 'a.dept_head_id = b.dept_manager');
		$this->db->where('b.admin_id', $user_id);

		$query = $this->db->get();
		return $query->row_array();
	}

	//for dashboard
	function getAllProdCostData($flag = ''){
		$kd_plant = $this->session->userdata['ADMIN']['plant'];
		$this->db->distinct();
		$this->db->select('a.id_prod_cost_header');
		$this->db->from('t_prod_cost_header a');
		$this->db->join('t_prod_cost_detail b', 'a.id_prod_cost_header = b.id_prod_cost_header');
		$this->db->where('a.plant', $kd_plant);
		if ($flag == '') {
			$this->db->where('a.status', 1);
		}
		if ($flag == 'hd') {
			$this->db->where('a.status', 2);
			$this->db->where('a.status_head', 1);
		}
		if ($flag == 'ca') {
			$this->db->where('a.product_type', 2);
			$this->db->where('a.status', 2);
			$this->db->where('a.status_head', 2);
			$this->db->where('a.status_cat_approver', 1);
		}
		if ($flag == 'cc') {
			$this->db->where('a.product_type', 2);
			$this->db->where('a.status', 2);
			$this->db->where('a.status_head', 2);
			$this->db->where('a.status_cat_approver', 2);
			$this->db->where('a.status_cost_control', 1);
		}
		if ($flag == 'done') {
			$this->db->where('a.product_type', 2);
			$this->db->where('a.status', 2);
			$this->db->where('a.status_head', 2);
			$this->db->where('a.status_cat_approver', 2);
			$this->db->where('a.status_cost_control', 2);
		}

		$query = $this->db->get();
		return $query->num_rows();
	}
	
	function getAllProdCostDataRejected($flag){
		$kd_plant = $this->session->userdata['ADMIN']['plant'];
		$this->db->distinct();
		$this->db->select('a.id_prod_cost_header');
		$this->db->from('t_prod_cost_header a');
		$this->db->join('t_prod_cost_detail b', 'a.id_prod_cost_header = b.id_prod_cost_header');
		$this->db->where('a.plant', $kd_plant);
		if ($flag == 'hd') {
			$this->db->where('a.status_head', 0);
		}
		if ($flag == 'ca') {
			$this->db->where('a.product_type', 2);
			$this->db->where('a.status_cat_approver', 0);
		}
		if ($flag == 'cc') {
			$this->db->where('a.product_type', 2);
			$this->db->where('a.status_cost_control', 0);
		}

		$query = $this->db->get();
		return $query->num_rows();
	}
	//for dashboard
}