<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Productcosting extends CI_Controller{
     public function __construct(){
		parent::__construct();
		$this->load->library('auth');  
		if(!$this->auth->is_logged_in()) {
			redirect(base_url());
        }
        $this->load->library('form_validation');
        $this->load->library('l_general');
        
        // load model
		$this->load->model('transaksi1/productcosting_model', 'pc');
    }

    public function index(){
        $this->load->view('transaksi1/produksi/product_costing/list_view');
    }
	
	public function showListData(){
        $fromDate = $this->input->post('fDate');
        $toDate = $this->input->post('tDate');
		$status = $this->input->post('stts');

        $date_from2;
        $date_to2;

        if($fromDate != '') {
			$year = substr($fromDate, 6);
			$month = substr($fromDate, 3,2);
			$day = substr($fromDate, 0,2);
			$date_from2 = $year.'-'.$day.'-'.$month.' 00:00:00';
        }else{
            $date_from2='';
        }
        
        if($toDate != '') {
			$year = substr($toDate, 6);
			$month = substr($toDate, 3,2);
			$day = substr($toDate, 0,2);
			$date_to2 = $year.'-'.$day.'-'.$month.' 00:00:00';
        }else{
            $date_to2='';
        }
        
		$rs = $this->pc->getProdCostData($date_from2, $date_to2, $status);
		
		$data = array();

        foreach($rs as $key=>$val){

            $nestedData = array();
			$nestedData['id_prod_cost_header'] 		= $val['id_prod_cost_header'];
			$nestedData['product_name'] 			= $val['product_name'];
            $nestedData['existing_bom'] 			= $val['existing_bom_code'].' - '.$val['existing_bom_name'];
			$nestedData['product_qty'] 				= $val['product_qty'];
			$nestedData['product_uom'] 				= $val['product_uom'];
			$nestedData['status'] 					= $val['status'] == '1'  ||  $val['status_head'] == '0' ? 'Not Approved' : 'Approved' ;
			$nestedData['status_head'] 				= $val['status_head'] == '1' ? 'Not Approved' : ($val['status_head'] == '0' ? 'Rejected' : 'Approved') ;
			$nestedData['created_date'] 			= date("d-m-Y",strtotime($val['created_date']));
            $nestedData['created_by'] 				= $val['created_by'];
            $nestedData['approved_by'] 				= $val['status_head'] == '0' ? '' : $val['approved_by'];
            $nestedData['head_dept'] 				= $val['status_head'] != '1' ? $val['approved_by'] : '';
            $data[] = $nestedData;

        }
		
		$json_data = array(
            "data" => $data 
        );
		
        echo json_encode($json_data);
	}
	
	public function add(){
		$object['plant'] = $this->session->userdata['ADMIN']['plant']; 
        $object['plant_name'] = $this->session->userdata['ADMIN']['plant_name'];
		$object['categories'] = $this->pc->getCategory();
		$object['matrialGroup'] = $this->pc->showMatrialGroup();
		$existing_bom['bom'] = $this->pc->sap_costing_existing_bom_references();
		
        $object['existing_bom']['-'] = '';
        if($existing_bom['bom'] != FALSE){
            foreach($existing_bom['bom'] as $bom){
				$object['existing_bom'][$bom['Code']] = $bom['Code'].' - '.$bom['ItemName'];
            }
		}
       	
        $this->load->view('transaksi1/produksi/product_costing/add_view',$object);
	}

	public function getDataForQFactorFormula(){
		$code = $this->input->post('code');
		$data = $this->pc->getDataForQFactorFormula($code);
		$json_data=array(
			"data" => $data
		);
		echo json_encode($json_data);
	}

	function getdataDetailMaterialSelect(){
        $itemSelect = $this->input->post('MATNR');
        
        $dataMatrialSelect = $this->pc->getDataItemSelected($itemSelect);
        $dataLastPrice = $this->pc->getDataLastPurchaseItemSelected($itemSelect);

        $json_data = array(
            "data" => $dataMatrialSelect,
            "dataLast" => $dataLastPrice
        );

        echo json_encode($json_data);
	}

	function getExistingBomData(){
		$materialNo = $this->input->post('material_no');
		$data = $this->pc->getExistingBomData($materialNo);	
		$json_data=array(
			"data" => $data
		);
		echo json_encode($json_data);
	}

	public function addItemRow(){
		$itmGrp = $this->input->post('itmGrp');
		$rs = $this->pc->getAllDataItems($itmGrp);
		echo json_encode($rs);
	}

	public function showDetailInput(){
		$kode_paket = $this->input->post('kode_paket');
		$qty_header = $this->input->post('Qty');
		$qtyDefault = $this->input->post('qtyDefault');
        $rs = $this->pc->getDataDetailForExistingBom($kode_paket);
		
		$dt = array();
        $i = 1;
        if($rs){
            foreach($rs as $data){
		
				/* $querySAP = $this->pc->getDataDetailValidForExistingBom($data['material_no']);
				$validFor = '';
				$decreasAc = '';
				if($querySAP != false){
					$validFor = $querySAP[0]['validFor'];
					$decreasAc = $querySAP[0]['DecreasAc'];
				} */
				
				/* $getopenqty = $this->wovendor->wo_detail_openqty($data['material_no']);
				$openqty = '';
				if($getopenqty != false){
					$openqty = (float)$getopenqty[0]['OpenQty'];
				} */
				
				/* $ucaneditqty = $this->wovendor->wo_detail_ucaneditqty($kode_paket,$data['material_no']);
				$getlocked = $this->wovendor->sap_wo_select_locked($kode_paket); */

				//$getucaneditqty='';
				$getucaneditqty = '<input type="hidden" class="form-control" id="typeCosting_'.$i.'"><input type="text" id="qtyCosting_'.$i.'" class="form-control" value="'.number_format(($data['quantity'] / (float)$qtyDefault * (float)$qty_header),4,'.','').'" style="width:90px" autocomplete="off">';
				
				/* if($getlocked[0]['U_Locked'] == 'N' && $ucaneditqty[0]['CanEditQty'] == 'Y'){
				}else {
					$getucaneditqty = '<input type="text" id="editqty_'.$i.'" class="form-control" value="'.number_format(($data['quantity'] / (float)$qtyDefault * (float)$qty_header),4,'.','').'" readonly>';
				} */

				$queryUOM = $this->pc->getDataDetailUOMForExistingBom($data['material_no']);
				if(count($queryUOM)>0){
					$uom = $queryUOM[0]['UNIT'];
				} else {
					$uom = '';
				}
				
				$queryGetLastPurchase = $this->pc->getDataLastPurchaseItemSelected($data['material_no']);
				$lastPurchase = $queryGetLastPurchase['LastPrice'] == ".000000" ? "0.0000" : number_format($queryGetLastPurchase['LastPrice'],4,'.','');
				$querySAP2 = $this->pc->getDataDetailItemBOMForExistingBom($kode_paket,$data['material_no']);
				
				$select = '<select class="form-control form-control-select2 descmat" data-live-search="true" name="descmat" id="descmat_'.$i.'">
								<option value="'.$data['material_desc'].'" uOm="'.$uom.'" matqty="'.number_format(($data['quantity'] / (float)$qtyDefault * (float)$qty_header),4,'.','').'" matno="'.$data['material_no'].'" lastprice="'.$lastPurchase.'">'.$data['material_desc'].'</option>'; //$data['quantity'] * (float)$qty_header
								if($querySAP2){
									foreach($querySAP2 as $_querySAP2){
										$queryGetSubLastPurchase = $this->pc->getDataLastPurchaseItemSelected($_querySAP2['U_SubsCode']);
										$subLastPurchase = $queryGetSubLastPurchase['LastPrice'] == ".000000" ? "0.0000" : number_format($queryGetSubLastPurchase['LastPrice'],4,'.','');
										if($_querySAP2['U_ItemCodeBOM'] == $data['material_no']){
											$select .= '<option value="'.$_querySAP2['NAME'].'" uOm="'.$_querySAP2['U_SubsUOM'].'" matqty="'.number_format(((float)$_querySAP2['U_SubsQty'] / (float)$qtyDefault * (float)$qty_header),4,'.','').'" matno="'.$_querySAP2['U_SubsCode'].'" lastprice="'.$subLastPurchase.'">'.$_querySAP2['NAME'].'</option>'; //$_querySAP2['U_SubsQty'] * (float)$qty_header
										}
									}
								}
				$select .= '</select>';
				
				$descolumn = '';
				if($querySAP2){
					foreach($querySAP2 as $_querySAP2){
						if($_querySAP2['U_ItemCodeBOM'] == $data['material_no']){
							$descolumn = $select;
						}else{
							$descolumn = $data['material_no'];
						}
					}
				}else{
					$descolumn = $select;
				}

				$nestedData=array();
				$nestedData['0'] = '<input type="checkbox" id="chk_'.$i.'" value="'.$i.'">';
				$nestedData['1'] = $i;
				$nestedData['2'] = $data['material_no'];
				$nestedData['3'] = $descolumn;
				$nestedData['4'] = $uom;
				$nestedData['5'] = $lastPurchase;
				$nestedData['6'] = $getucaneditqty;
				$nestedData['7'] = '';
				/* $nestedData['id_mpaket_h_detail'] = $data['id_mpaket_h_detail'];
				$nestedData['material_desc'] = $data['material_desc'];
				$nestedData['validFor'] = $validFor;
				$nestedData['decreasAc'] = $decreasAc; */
				$dt[] = $nestedData;
				$i++;
			}
        }
        $json_data = array(
			"data" => $dt
		);
		echo json_encode($json_data);

	}

	public function addData(){
		$product_cost_header['id_prod_cost_plant'] = $this->pc->selectIDPlant($this->session->userdata['ADMIN']['plant'],$this->l_general->str_to_date($this->input->post('postDate')));
		$product_cost_header['prod_cost_no'] = '';
		$product_cost_header['plant'] = $this->session->userdata['ADMIN']['plant'];
		$product_cost_header['plant_name'] = $this->session->userdata['ADMIN']['plant_name'];
		$product_cost_header['category_code'] = $this->input->post('categoryCode');
		$product_cost_header['category_name'] = $this->input->post('categoryName');
		$product_cost_header['category_q_factor'] = $this->input->post('categoryQF');
		$product_cost_header['category_min'] = $this->input->post('categoryMin');
		$product_cost_header['category_max'] = $this->input->post('categoryMax');
		$product_cost_header['existing_bom_code'] = $this->input->post('existingBomCode');
		$product_cost_header['existing_bom_name'] = $this->input->post('existingBomName');
		$product_cost_header['product_name'] = $this->input->post('productName');
		$product_cost_header['product_qty'] = $this->input->post('productQty');
		$product_cost_header['product_uom'] = $this->input->post('productUom');
		$product_cost_header['product_selling_price'] = $this->input->post('productSellPrice');
		$product_cost_header['product_q_factor'] = $this->input->post('productQFactor');
		$product_cost_header['product_percentage'] = $this->input->post('productPercentage');
		$product_cost_header['product_result'] = $this->input->post('productResult');
		$product_cost_header['status'] = $this->input->post('approve');
		$product_cost_header['status_head'] = 1;
		$product_cost_header['posting_date'] = $this->l_general->str_to_date($this->input->post('postDate'));
		$product_cost_header['created_date'] = date('Y-m-d');
		$product_cost_header['lastmodified'] = date('Y-m-d H:i:s');
		$product_cost_header['id_user_input'] = $this->session->userdata['ADMIN']['admin_id'];
		$product_cost_header['id_user_approved'] = $this->input->post('approve') == 2 ? $this->session->userdata['ADMIN']['admin_id'] : 0 ;
		
		$count = count($this->input->post('matrialNo'));
		if($id_product_cost_header = $this->pc->insertHeaderProdCost($product_cost_header)) {
			$input_detail_success = FALSE;
			for($i = 0; $i < $count; $i++){
				$product_cost_detail['id_prod_cost_header'] = $id_product_cost_header;
				$product_cost_detail['id_prod_cost_h_detail'] = $i+1;
				$product_cost_detail['material_no'] = $this->input->post('matrialNo')[$i];
				$product_cost_detail['material_desc'] = $this->input->post('matrialDesc')[$i];
				$product_cost_detail['item_type'] = $this->input->post('itemType')[$i];
				$product_cost_detail['item_qty'] = $this->input->post('itemQty')[$i];
				$product_cost_detail['item_uom'] = $this->input->post('itemUom')[$i];
				$product_cost_detail['item_cost'] = $this->input->post('itemCost')[$i];
				if($this->pc->insertDetailProdCost($product_cost_detail) ){
					$input_detail_success = TRUE;
				}
			}
		}
        if($input_detail_success){
            return $this->session->set_flashdata('success', "Product Costing Telah Terbentuk");
        }else{
            return $this->session->set_flashdata('failed', "Product Costing Gagal Terbentuk");
        } 
	}

	public function deleteData(){
        $idProCost = $this->input->post('deleteArr');
        $deleteData = false;
        foreach($idProCost as $id){
            if($this->pc->deleteProdCostHeader($id))
            $deleteData = true;
        }
        
        if($deleteData){
            return $this->session->set_flashdata('success', "Product Costing Berhasil dihapus");
        }else{
            return $this->session->set_flashdata('failed', "Product Costing Approved, Gagal dihapus");
        }
	}

	public function edit(){
		$id = $this->uri->segment(4);
		$object['categories'] = $this->pc->getCategory();
		$object['data'] = $this->pc->selectProdCostHeader($id);
		$object['qf'] = $this->pc->getDataForQFactorFormula($object['data']['category_code']);
		$object['matrialGroup'] = $this->pc->showMatrialGroup();
		
        $object['pc']['id_prod_cost_header'] = $object['data']['id_prod_cost_header'];
        $object['pc']['category_code'] = $object['data']['category_code'];
        $object['pc']['category_name'] = $object['data']['category_name'];
        $object['pc']['category_q_factor'] = $object['data']['category_q_factor'];
        $object['pc']['q_factor_sap'] = $object['qf']['q_factor'];
        $object['pc']['min'] = $object['qf']['min_cost'];
        $object['pc']['max'] = $object['qf']['max_cost'];
        $object['pc']['existing_bom_code'] = $object['data']['existing_bom_code'];
        $object['pc']['existing_bom_name'] = $object['data']['existing_bom_name'];
        $object['pc']['product_name'] = $object['data']['product_name'];
        $object['pc']['product_qty'] = $object['data']['product_qty'];
        $object['pc']['product_uom'] = $object['data']['product_uom'];
        $object['pc']['product_selling_price'] = $object['data']['product_selling_price'];
		$object['pc']['status'] = $object['data']['status'];
		$object['pc']['status_head'] = $object['data']['status_head'];
		$object['pc']['reject_reason'] = $object['data']['reject_reason'];
        $object['pc']['posting_date'] = $object['data']['posting_date'];
        $object['pc']['user_input'] = $object['data']['id_user_input'];
		$object['pc']['plant'] = $this->session->userdata['ADMIN']['plant'].' - '.$this->session->userdata['ADMIN']['plant_name'];
		$object['pc']['user_login'] = $this->session->userdata['ADMIN']['admin_id'];
		
		
        $this->load->view('transaksi1/produksi/product_costing/edit_view', $object);
	}
	
	public function showDetailEdit(){
		$id = $this->input->post('id');
		$type = $this->input->post('type');
		$object = $this->pc->selectDataDetail($id,$type);
		$dt = array();
		$i = 1;
		if ($object) {
			foreach ($object as $data) {
				$nestedData=array();
				$nestedData['0'] = $data['id_prod_cost_detail'];
				$nestedData['1'] = $i;
				$nestedData['2'] = $data['material_no'];
				$nestedData['3'] = $data['material_desc'];
				$nestedData['4'] = $data['item_uom'];
				$nestedData['5'] = $data['item_cost'];
				$nestedData['6'] = $data['item_qty'];
				$nestedData['7'] = '';
				$dt[] = $nestedData;
				$i++;
			}
		}

		$json_data = array(
			"data" => $dt
		);
		echo json_encode($json_data);
	}

	public function updateData(){
		$id = $this->input->post('id');
		$approve = $this->input->post('approve');
		$prod_cost_header['id_prod_cost_header'] = $id;
		$prod_cost_header['category_code'] = $this->input->post('categoryCode');
		$prod_cost_header['category_name'] = $this->input->post('categoryName');
		$prod_cost_header['category_q_factor'] = $this->input->post('categoryQF');
		$prod_cost_header['category_min'] = $this->input->post('categoryMin');
		$prod_cost_header['category_max'] = $this->input->post('categoryMax');
		$prod_cost_header['product_name'] = $this->input->post('productName');
		$prod_cost_header['product_qty'] = $this->input->post('productQty');
		$prod_cost_header['product_uom'] = $this->input->post('productUom');
		$prod_cost_header['product_selling_price'] = $this->input->post('productSellPrice');
		$prod_cost_header['product_q_factor'] = $this->input->post('productQFactor');
		$prod_cost_header['product_percentage'] = $this->input->post('productPercentage');
		$prod_cost_header['product_result'] = $this->input->post('productResult');
		$prod_cost_header['status'] = $approve == 2 || $approve == 3 ? 2 : 1;
		$prod_cost_header['status_head'] = $approve == 3 ? 2 : 1;
		$prod_cost_header['id_user_approved'] = $approve == 2 || $approve == 3 ? $this->session->userdata['ADMIN']['admin_id'] : 0;
		$max = count($this->input->post('matrialNo'));

		$prod_cost_header_update = $this->pc->updateDataProdCostHeader($prod_cost_header);
		$succes_update = false;
		if($prod_cost_header_update){
			$this->pc->selectProdCostDetailForDelete($id);
			for($i = 0; $i < $max; $i++){
				$prod_cost_detail['id_prod_cost_header'] = $id;
				$prod_cost_detail['id_prod_cost_h_detail'] = $i+1;
				$prod_cost_detail['material_no'] = $this->input->post('matrialNo')[$i];
				$prod_cost_detail['material_desc'] = $this->input->post('matrialDesc')[$i];
				$prod_cost_detail['item_type'] = $this->input->post('itemType')[$i];
				$prod_cost_detail['item_qty'] = $this->input->post('itemQty')[$i];
				$prod_cost_detail['item_uom'] = $this->input->post('itemUom')[$i];
				$prod_cost_detail['item_cost'] = $this->input->post('itemCost')[$i];
							
				if($this->pc->insertDetailProdCost($prod_cost_detail)){
					$succes_update = TRUE;
				}
			}
		}
		if($succes_update){
            return $this->session->set_flashdata('success', "Product Costing Telah Berhasil Terupdate");
        }else{
            return $this->session->set_flashdata('failed', "Product Costing Gagal Terupdate");
        }
	} 

	function reject(){	
		$reject['id_prod_cost_header'] = $this->input->post('id');
		$reject['status_head'] = 0;
		$reject['reject_reason'] = $this->input->post('reason');
		$reject['id_user_approved'] = $this->session->userdata['ADMIN']['admin_id'];

		if($this->pc->reject($reject)){
			return $this->session->set_flashdata('failed', "Product Costing Rejected");
		} else {
			return $this->session->set_flashdata('failed', "Product Costing Gagal di Reject");
		}
	}
	
	//
	
	 public function wo_header_uom(){
		$material_no = $this->input->post('material_no');
		$data = $this->wovendor->sap_wo_headers_select_by_item($material_no);	
		$json_data=array(
			"data" => $data
		);
		echo json_encode($json_data);
     }

    
	
	public function showDetailEdit_(){
        $id_wo_header = $this->input->post('id');
        $kode_paket = $this->input->post('kodepaket');
		$qty_paket = $this->input->post('qtypaket');
		$qtyDefault = $this->input->post('qtyDefault');
		$rs = $this->wovendor->wo_details_select($id_wo_header,$kode_paket,$qty_paket);
		$object['data'] = $this->wovendor->wo_header_select($id_wo_header);
		$disabled = $object['data']['status'] == 2 ? 'disabled' : '';
		
		$dt = array();
        $i = 1;
        if($rs){
            foreach($rs as $data){
				$inWhs = $this->wovendor->wo_detail_onhand($data['material_no']);
				$queryUOM = $this->wovendor->wo_detail_uom($data['material_no']);
				if(count($queryUOM)>0){
					$uom = $queryUOM[0]['UNIT'];
				} else {
					$uom = '';
				}
				$ucaneditqty = $this->wovendor->wo_detail_ucaneditqty($kode_paket,$data['material_no']);
				$getlocked = $this->wovendor->sap_wo_select_locked($kode_paket);
				$getucaneditqty='';
				
				if($object['data']['status'] == 1){
					if($getlocked[0]['U_Locked'] == 'N' && $ucaneditqty[0]['CanEditQty'] == 'Y'){
						$getucaneditqty = '<input type="text" id="editqty_'.$i.'" class="form-control" value="'.number_format($data['qty'],4,'.','').'">';
					}else {
						$getucaneditqty = '<input type="text" id="editqty_'.$i.'" class="form-control" value="'.number_format($data['qty'],4,'.','').'" readonly>';
					}
				}else{
					$getucaneditqty = '<input type="text" id="editqty_'.$i.'" class="form-control" value="'.number_format($data['qty'],4,'.','').'" readonly>';
				}
				$querySAP2 = $this->wovendor->wo_detail_itemcodebom($kode_paket,$data['material_no']);
				$select = '<select class="form-control form-control-select2" data-live-search="true" name="descmat" id="descmat" '.$disabled.'>
								<option value="'.$data['material_no'].'" rel="'.$ucaneditqty[0]['CanEditQty'].'" matqty="'.number_format($data['qty'],4,'.','').'" onHand="'.number_format($data['OnHand'],4,'.','').'" minStock = "'.$data['MinStock'].'" uOm="'.$data['uom'].'" matdesc="'.$data['material_desc'].'">'.$data['material_desc'].'</option>';
								if($querySAP2){
									foreach($querySAP2 as $_querySAP2){
										$getonhandAlt = $this->wovendor->wo_detail_onhand($_querySAP2['U_SubsCode']);
										$onhandAlt = '';
										$minstockAlt = '';
										if($getonhandAlt != false){
											$onhandAlt = (float)$getonhandAlt[0]['OnHand'];
											$minstockAlt = (float)$getonhandAlt[0]['MinStock'];
										}
										if($_querySAP2['U_ItemCodeBOM'] = $data['material_no']){
											$select .= '<option value="'.$_querySAP2['U_SubsCode'].'" 
											rel="'.$ucaneditqty[0]['CanEditQty'].'" onHand="'.number_format((float)$onhandAlt,4,'.','').'" minStock = "'.$minstockAlt.'" uOm="'.$_querySAP2['U_SubsUOM'].'"
											matqty="'.number_format(((float)$_querySAP2['U_SubsQty'] / (float)$qtyDefault * (float)$qty_paket),4,'.','').'" matdesc="'.$_querySAP2['NAME'].'">'.$_querySAP2['NAME'].'</option>'; //* (float)$qty_paket
										}
									}
								}
				$select .= '</select>';
				$descolumn = '';
				if($querySAP2){
					foreach($querySAP2 as $_querySAP2){
						if($_querySAP2['U_ItemCodeBOM'] = $data['material_no']){
							$descolumn = $select;
						}else{
							$descolumn = $data['material_no'];
						}
					}
				}else{
					$descolumn = $select;
				}
			
				$nestedData=array();
				$nestedData['no'] = $i;
				$nestedData['id_produksi_detail'] = $data['id_produksi_detail'];
				$nestedData['id_produksi_header'] = $data['id_produksi_header'];
				$nestedData['material_no'] = $data['material_no'];
				$nestedData['material_desc'] = $data['material_desc'];
				$nestedData['qty'] = $getucaneditqty;
				$nestedData['uom'] = $uom;
				$nestedData['OnHand'] = $inWhs[0]['OnHand']!='.000000' ? number_format($inWhs[0]['OnHand'],4,'.','') : '0.0000';
				$nestedData['MinStock'] = $data['MinStock']; 
				$nestedData['OpenQty'] = $data['OpenQty'];
				$nestedData['descolumn'] = $descolumn;
				$dt[] = $nestedData;
				$i++;
			}
        }
        $json_data = array(
			"data" => $dt
		);
		echo json_encode($json_data);

	}
	
	public function addItemRowz(){
		$rs = $this->wovendor->sap_item();
		echo json_encode($rs);
	}

	function getdataDetailMaterialSelectO(){
        $itemSelect = $this->input->post('MATNR');
        
		$dataMatrialSelect = $this->wovendor->sap_item($itemSelect);
		
		$dt = array();
		foreach ($dataMatrialSelect as $data) {
			$getonhand = $this->wovendor->wo_detail_onhand($data['MATNR']);
			$onhand = '';
			$minstock = '';
			if($getonhand != false){
				$onhand = (float)$getonhand[0]['OnHand'];
				$minstock = (float)$getonhand[0]['MinStock'];
			}
			$getopenqty = $this->wovendor->wo_detail_openqty($data['MATNR']);
			$openqty = '';
			if($getopenqty != false){
				$openqty = (float)$getopenqty[0]['OpenQty'];
			}
			$uom = '';
			if($data['UNIT'] != '' || !empty($data['UNIT'])){
				$uom = $data['UNIT'];
			}

			$nestedData=array();
			$nestedData['MATNR'] = $data['MATNR'];
			$nestedData['MAKTX'] = $data['MAKTX'];
			$nestedData['qty'] = 0;
			$nestedData['UNIT'] = $uom;
			$nestedData['OnHand'] = $onhand; 
			$nestedData['MinStock'] = $minstock; 
			$nestedData['OpenQty'] = $openqty;
			$dt[] = $nestedData;
		}
		
		echo json_encode($dt);
        
	}
	
	public function showDetailInput_(){
		$kode_paket = $this->input->post('kode_paket');
		$qty_header = $this->input->post('Qty');
		$qtyDefault = $this->input->post('qtyDefault');
        $rs = $this->wovendor->wo_details_input_select($kode_paket);
		
		$dt = array();
        $i = 1;
        if($rs){
            foreach($rs as $data){
		
				$querySAP = $this->wovendor->wo_detail_valid($data['material_no']);
				$validFor = '';
				$decreasAc = '';
				if($querySAP != false){
					$validFor = $querySAP[0]['validFor'];
					$decreasAc = $querySAP[0]['DecreasAc'];
				}
				
				$qty_paket = $data['quantity'];
				
				$getonhand = $this->wovendor->wo_detail_onhand($data['material_no']);
				$onhand = '';
				$minstock = '';
				if($getonhand != false){
					$onhand = (float)$getonhand[0]['OnHand'];
					$minstock = (float)$getonhand[0]['MinStock'];
				}
				
				$getopenqty = $this->wovendor->wo_detail_openqty($data['material_no']);
				$openqty = '';
				if($getopenqty != false){
					$openqty = (float)$getopenqty[0]['OpenQty'];
				}
				
				$ucaneditqty = $this->wovendor->wo_detail_ucaneditqty($kode_paket,$data['material_no']);
				$getlocked = $this->wovendor->sap_wo_select_locked($kode_paket);

				$getucaneditqty='';
				
				if($getlocked[0]['U_Locked'] == 'N' && $ucaneditqty[0]['CanEditQty'] == 'Y'){
					$getucaneditqty = '<input type="text" id="editqty_'.$i.'" class="form-control" value="'.number_format(($data['quantity'] / (float)$qtyDefault * (float)$qty_header),4,'.','').'">';
				}else {
					$getucaneditqty = '<input type="text" id="editqty_'.$i.'" class="form-control" value="'.number_format(($data['quantity'] / (float)$qtyDefault * (float)$qty_header),4,'.','').'" readonly>';
				}

				$queryUOM = $this->wovendor->wo_detail_uom($data['material_no']);
				if(count($queryUOM)>0){
					$uom = $queryUOM[0]['UNIT'];
				} else {
					$uom = '';
				}
				
				$querySAP2 = $this->wovendor->wo_detail_itemcodebom($kode_paket,$data['material_no']);
				
				$select = '<select class="form-control form-control-select2" data-live-search="true" name="descmat" id="descmat">
								<option value="'.$data['material_no'].'" rel="'.$ucaneditqty[0]['CanEditQty'] .'" onHand="'.number_format($onhand,4,'.','').'" minStock = "'.$minstock.'" uOm="'.$uom.'" matqty="'.number_format(($data['quantity'] / (float)$qtyDefault * (float)$qty_header),4,'.','').'" matdesc="'.$data['material_desc'].'">'.$data['material_desc'].'</option>'; //$data['quantity'] * (float)$qty_header
								if($querySAP2){
									foreach($querySAP2 as $_querySAP2){
										$getonhandAlt = $this->wovendor->wo_detail_onhand($_querySAP2['U_SubsCode']);
										$onhandAlt = '';
										$minstockAlt = '';
										if($getonhandAlt != false){
											$onhandAlt = (float)$getonhandAlt[0]['OnHand'];
											$minstockAlt = (float)$getonhandAlt[0]['MinStock'];
										}
										if($_querySAP2['U_ItemCodeBOM'] = $data['material_no']){
											$select .= '<option value="'.$_querySAP2['U_SubsCode'].'" 
											rel="'.$ucaneditqty[0]['CanEditQty'].'" onHand="'.number_format((float)$onhandAlt,4,'.','').'" minStock = "'.$minstockAlt.'" uOm="'.$_querySAP2['U_SubsUOM'].'"
											matqty="'.number_format(((float)$_querySAP2['U_SubsQty'] / (float)$qtyDefault * (float)$qty_header),4,'.','').'" matdesc="'.$_querySAP2['NAME'].'">'.$_querySAP2['NAME'].'</option>'; //$_querySAP2['U_SubsQty'] * (float)$qty_header
										}
									}
								}
				$select .= '</select>';
				
				$descolumn = '';
				if($querySAP2){
					foreach($querySAP2 as $_querySAP2){
						if($_querySAP2['U_ItemCodeBOM'] = $data['material_no']){
							$descolumn = $select;
						}else{
							$descolumn = $data['material_no'];
						}
					}
				}else{
					$descolumn = $select;
				}
				
				$openitem = $this->wovendor->wo_detail_item();
				$qtyopen = '';
				foreach($openitem as $_openqty){
					if($_openqty['U_ItemCodeBOM'] = $data['material_no']){
						$qtyopen = $select;
					}else{
						$qtyopen = $data['material_no'];
					}
				}

				$nestedData=array();
				$nestedData['no'] = $i;
				$nestedData['id_mpaket_header'] = $data['id_mpaket_header'];
				$nestedData['id_mpaket_h_detail'] = $data['id_mpaket_h_detail'];
				$nestedData['material_no'] = $data['material_no'];
				$nestedData['material_desc'] = $data['material_desc'];
				$nestedData['qty'] = $getucaneditqty;
				$nestedData['uom'] = $uom;
				$nestedData['OnHand'] = number_format($onhand,4,'.',''); 
				$nestedData['MinStock'] = $minstock; 
				$nestedData['OpenQty'] = $openqty;
				$nestedData['validFor'] = $validFor;
				$nestedData['decreasAc'] = $decreasAc;
				$nestedData['descolumn'] = $descolumn;
				$dt[] = $nestedData;
				$i++;
			}
        }
        $json_data = array(
			"data" => $dt
		);
		echo json_encode($json_data);

	}
	
	public function addData_(){
		$produksi_header['plant'] = $this->session->userdata['ADMIN']['plant'];
		$produksi_header['storage_location'] = $this->session->userdata['ADMIN']['storage_location'];
		$produksi_header['posting_date'] = $this->l_general->str_to_date($this->input->post('postDate'));
		$produksi_header['id_produksi_plant'] = $this->wovendor->id_produksi_plant_new_select($this->session->userdata['ADMIN']['plant'],$this->input->post('postDate'));
		$produksi_header['produksi_no'] = '';

		$produksi_header['status'] = $this->input->post('approve')? $this->input->post('approve') : '1';
		$produksi_header['kode_paket'] = $this->input->post('woNumber');
		$produksi_header['nama_paket'] = $this->input->post('woDesc');
		$produksi_header['qty_paket'] = $this->input->post('qtyProd');
		$produksi_header['uom_paket'] = $this->input->post('uomProd');
		$produksi_header['id_user_input'] = $this->session->userdata['ADMIN']['admin_id'];
		$produksi_header['id_user_approved'] = $this->input->post('approve')? $this->session->userdata['ADMIN']['admin_id'] : 0 ;
		$produksi_header['created_date']=date('Y-m-d');
		$produksi_header['back']=1;
		
		/*Batch Number */
		$date=date('ym');
		$batch = $this->wovendor->wo_header_batch($produksi_header['kode_paket'],$this->session->userdata['ADMIN']['plant']);
		if(!empty($batch)){
			$date=date('ym');
			$count1=count($batch) + 1;
			if ($count1 > 9 && $count1 < 100){
				$dg="0";
			}else {
				$dg="00";
			}
			$num=$produksi_header['kode_paket'].$date.$dg.$count1;
			$produksi_header['num'] = $num;
		}else{
			$produksi_header['num'] = '';
		}
		
		$count = count($this->input->post('matrialNo'));
		if($id_produksi_header = $this->wovendor->produksi_header_insert($produksi_header)) {
			$input_detail_success = FALSE;
			for($i = 0; $i < $count; $i++){
				$produksi_detail['id_produksi_header'] = $id_produksi_header;
				$produksi_detail['qty'] = $this->input->post('qty')[$i];
				$produksi_detail['id_produksi_h_detail'] = $i+1;
				$produksi_detail['material_no'] = $this->input->post('matrialNo')[$i];
				$produksi_detail['num'] = '';
				$produksi_detail['material_desc'] = trim($this->input->post('matrialDesc')[$i]);
				$produksi_detail['uom'] = $this->input->post('uom')[$i];
				$produksi_detail['qc'] = '';
				$produksi_detail['OnHand'] = $this->input->post('onHand')[$i];
				$produksi_detail['MinStock'] = $this->input->post('minStock')[$i];
				$produksi_detail['OpenQty'] = $this->input->post('outStandTot')[$i];
				if($this->wovendor->produksi_detail_insert($produksi_detail) ){
					$input_detail_success = TRUE;
				}
			}
		}
        if($input_detail_success){
            return $this->session->set_flashdata('success', "Work Order Telah Terbentuk");
        }else{
            return $this->session->set_flashdata('failed', "Work Order Gagal Terbentuk");
        } 
	}
	

	public function addUpdateData(){
		$id_produksi_header = $this->input->post('id_wo_header');
		$kode_paket 		=$this->input->post('kd_paket');
		$approve 			=$this->input->post('approve');
		$produksi_header['id_produksi_header'] = $id_produksi_header;
		$produksi_header['status'] = $approve ? $approve: "1";
		$produksi_header['id_user_approved'] = $approve? $this->session->userdata['ADMIN']['admin_id'] : 0 ;
		$max = count($this->input->post('matrialNo'));

		$produksi_header_update = $this->wovendor->update_produksi_header($produksi_header);
		$succes_update = false;
		if($produksi_header_update){
			$this->wovendor->wo_details_delete($id_produksi_header);
			for($i=0; $i < $max; $i++){
				$produksi_detail['id_produksi_header'] = $id_produksi_header;
				$produksi_detail['qty'] = $this->input->post('qty')[$i];
				$produksi_detail['id_produksi_h_detail'] = $i+1;
				$produksi_detail['material_no'] = $this->input->post('matrialNo')[$i];
				$produksi_detail['num'] = '';
				$produksi_detail['material_desc'] = $this->input->post('matrialDesc')[$i];
				$produksi_detail['uom'] = $this->input->post('uom')[$i];
				$produksi_detail['qc'] = '';
				$produksi_detail['OnHand'] = $this->input->post('onHand')[$i];
				$produksi_detail['MinStock'] = $this->input->post('minStock')[$i];
				$produksi_detail['OpenQty'] = $this->input->post('outStandTot')[$i];
							
				if($this->wovendor->produksi_detail_insert($produksi_detail) ){
					$succes_update = TRUE;
				}
			}
		}
		if($succes_update){
            return $this->session->set_flashdata('success', "WO Telah Berhasil Terupdate");
        }else{
            return $this->session->set_flashdata('failed', "WO Gagal Terupdate");
        }
	} 
}
?>