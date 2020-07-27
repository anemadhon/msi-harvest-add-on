<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require('./application/third_party/PHPExcel/PHPExcel.php');

class Stock extends CI_Controller {
    public function __construct(){
        parent::__construct();

        $this->load->library('auth');  
		if(!$this->auth->is_logged_in()) {
			redirect(base_url());
        }
        $this->load->library('form_validation');
        $this->load->library('l_general');

        // load model
        $this->load->model('transaksi1/stock_model', 'st_model');
    }

    public function index(){
        $object['opname_header']['freeze'] = $this->st_model->freeze();
        $arr_ids = explode(", ",$this->session->userdata['ADMIN']['admin_perm_grup_ids']);
        $ids = '';
        foreach($arr_ids as $val){
            if($val == 14){
                $ids = $val;
            }elseif($val == 10064){
                $ids = $val;
            }
        }
        $object['opname_header']['ids'] = $ids;
        $this->load->view('transaksi1/stock_outlet/stock/list_view',$object);
    }

    public function showAllData(){
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
			$date_to2 = $year.'-'.$day.'-'.$month.' 23:59:59';
        }else{
            $date_to2='';
        }

        $rs = $this->st_model->t_opname_headers($date_from2, $date_to2, $status);
		$data = array();
		$status_string='';

        foreach($rs as $key=>$val){
			if($val['status'] =='1'){
				$status_string= 'Not Approved';
			}else if($val['status'] =='2'){
				$status_string= 'Approved';
			}else{
				$status_string= 'Cancel';
            }
            
            if($val['am_approved'] =='1'){
				$am_status= 'Rejected';
			}else if($val['am_approved'] =='2'){
				$am_status= 'Approved';
			}else{
				$am_status= '';
            }
            
            if($val['rm_approved'] =='1'){
				$rm_status= 'Rejected';
			}else if($val['rm_approved'] =='2'){
				$rm_status= 'Approved';
			}else{
				$rm_status= '';
			}
            
            if($val['freeze'] =='N'){
				$freeze= 'No';
			}else{
				$freeze= 'Yes';
			}

            $nestedData = array();
            $nestedData['id_opname_header'] = $val['id_opname_header'];
            $nestedData['opname_no'] = $val['opname_no'];
            $nestedData['created_date'] = date("d-m-Y",strtotime($val['created_date']));
            $nestedData['posting_date'] = date("d-m-Y",strtotime($val['posting_date']));
            $nestedData['request_reason'] = $val['request_reason'];
            $nestedData['status'] = $status_string; 
            $nestedData['am_status'] = $am_status; 
            $nestedData['rm_status'] = $rm_status; 
            $nestedData['created_by'] = $val['user_input'];
            $nestedData['approved_by'] = $val['user_approved'];
            $nestedData['last_modified'] = date("d-m-Y",strtotime($val['lastmodified']));
            $nestedData['to_plant'] = $val['to_plant'];
            $nestedData['back'] = ($val['back'] =='1')?'Not Integrated':'Integrated';
            $nestedData['status_real'] = $val['status'];
            $nestedData['freeze'] = $freeze;
            $data[] = $nestedData;

        }

        $json_data = array(
            "recordsTotal"    => 10, 
            "recordsFiltered" => 12,
            "data"            => $data 
        );
        echo json_encode($json_data);
     }

    public function add(){
        $object['request_reason'] = ['Pastry', 'Cake Shop', 'Store', 'Bar'];
		$object['matrialGroup'] = $this->st_model->showMatrialGroup();
		$object['head'] = $this->st_model->headTemplate();
		$object['plant'] = $this->session->userdata['ADMIN']['plant'].' - '.$this->session->userdata['ADMIN']['plant_name'];
    	$object['storage_location'] = $this->session->userdata['ADMIN']['storage_location'].' - '.$this->session->userdata['ADMIN']['storage_location_name'];
    
        $this->load->view('transaksi1/stock_outlet/stock/add_view', $object);
    }

    public function addData(){
        $plant = $this->session->userdata['ADMIN']['plant'];
        $storage_location = $this->session->userdata['ADMIN']['storage_location'];
        $plant_name = $this->session->userdata['ADMIN']['plant_name'];
        $storage_location_name = $this->session->userdata['ADMIN']['storage_location_name'];
        $admin_id = $this->session->userdata['ADMIN']['admin_id'];

        $opname_header['posting_date'] = $this->l_general->str_to_date($this->input->post('postDate'));
        $opname_header['status'] = $this->input->post('appr')? $this->input->post('appr') : '1';
        $opname_header['item_group_code'] = $this->input->post('matGroup');
        $opname_header['created_date'] = date('Y-m-d');
        $opname_header['plant'] = $plant;
        $opname_header['plant_name'] = $plant_name;
        $opname_header['storage_location_name'] = $storage_location_name ;
        $opname_header['storage_location'] = $storage_location ;
        $opname_header['id_opname_plant'] = $this->st_model->id_opname_plant_new_select($opname_header['plant'],$opname_header['created_date']);
        $opname_header['id_user_input'] = $admin_id;
        $opname_header['opname_no'] = '';
        $opname_header['id_user_approved'] = $this->input->post('appr')? $admin_id : 0;
        $opname_header['id_am'] = 0;
        $opname_header['id_rm'] = 0;
        $opname_header['am_approved'] = 0;
        $opname_header['rm_approved'] = 0;
        $opname_header['freeze'] = 'Y';
        $opname_header['back'] = 1;

        $opname_details['material_no'] = $this->input->post('detMatrialNo');
        $count = count($opname_details['material_no']);

        if($id_opname_header = $this->st_model->opname_header_insert($opname_header)){
            $input_detail_success = false;
            for($i =0; $i < $count; $i++){
                $opname_detail['id_opname_header'] = (int)$id_opname_header;
                $opname_detail['id_opname_h_detail'] = $i+1;
                $opname_detail['item_grp_name'] = $this->input->post('ItemGrp')[$i];
                $opname_detail['material_no'] = $this->input->post('detMatrialNo')[$i];
                $opname_detail['material_desc'] = $this->input->post('detMatrialDesc')[$i];
                $opname_detail['requirement_qty'] = (float)$this->input->post('detQty')[$i];
                $opname_detail['uom'] = $this->input->post('detUom')[$i];
                $opname_detail['stock'] = $this->input->post('OnHand')[$i];

                $countRoom = count($this->input->post('Qr')[$i]);

                if($id_opname_detail = $this->st_model->opname_detail_insert($opname_detail)){
                    for ($j=0; $j < $countRoom; $j++) { 
                        $temp = $this->input->post('Qr')[$i][$j];
                        $temp_data = explode('|',$temp);
                        $opname_room_detail['id_opname_header'] = $id_opname_header;
                        $opname_room_detail['id_opname_detail'] = $id_opname_detail;
                        $opname_room_detail['id_room'] = $temp_data[0];
                        $opname_room_detail['requirement_qty'] = $temp_data[1];
                        $opname_room_detail['uom'] = $this->input->post('detUom')[$i];

                        if ($this->st_model->opname_room_detail_insert($opname_room_detail)) {
                            $input_detail_success = TRUE;
                        }
                    }
                }
            }
        }

        if($input_detail_success){
            return $this->session->set_flashdata('success', "Stock Opname Telah Terbentuk");
        }else{
            return $this->session->set_flashdata('failed', "Stock Opname Gagal Terbentuk");
        }
    }

    public function edit(){
        $id_opname_header = $this->uri->segment(4);
        $object['plant_name'] = $this->session->userdata['ADMIN']['plant'].' - '.$this->session->userdata['ADMIN']['plant_name'];
		$object['storage_location_name'] = $this->session->userdata['ADMIN']['storage_location'].' - '.$this->session->userdata['ADMIN']['storage_location_name'];
        $object['data'] = $this->st_model->opname_header_select($id_opname_header);
        $object['head'] = $this->st_model->headTemplate();
		$object['opname_header']['id_opname_header'] = $id_opname_header;

        if($object['data']['status'] == '1'){
            $object['opname_header']['status_string'] = 'Not Approved';                              
        }else if($object['data']['status'] == '2'){
            $object['opname_header']['status_string'] = 'Approved';
        }else{
            $object['opname_header']['status_string'] = 'Cancel';
        }
        
        if($object['data']['am_approved'] == '0'){
            $object['opname_header']['status_string_am'] = 'Not Approved';                              
        }else if($object['data']['am_approved'] == '2'){
            $object['opname_header']['status_string_am'] = 'Approved';
        }else{
            $object['opname_header']['status_string_am'] = 'Rejected';
        }
        
        if($object['data']['rm_approved'] == '0'){
            $object['opname_header']['status_string_rm'] = 'Not Approved';                              
        }else if($object['data']['rm_approved'] == '2'){
            $object['opname_header']['status_string_rm'] = 'Approved';
        }else{
            $object['opname_header']['status_string_rm'] = 'Rejected';
		}
		
        $object['opname_header']['posting_date'] = $object['data']['posting_date'];
        $object['opname_header']['opname_no'] = $object['data']['opname_no'];
        $object['opname_header']['request_reason'] = $object['data']['request_reason'];
        $object['opname_header']['item_group_code'] = $object['data']['item_group_code'];
        $object['opname_header']['status'] = $object['data']['status'];
        $object['opname_header']['request_reason'] = $object['data']['request_reason'];
        $object['opname_header']['am_approved'] = $object['data']['am_approved'];
        $object['opname_header']['rm_approved'] = $object['data']['rm_approved'];
        $object['opname_header']['id_am'] = $object['data']['id_am'];
        $object['opname_header']['id_rm'] = $object['data']['id_rm'];

        $arr_ids = explode(", ",$this->session->userdata['ADMIN']['admin_perm_grup_ids']);
        $ids = '';
        foreach($arr_ids as $val){
            if($val == 14){
                $ids = $val;
            }elseif($val == 10064){
                $ids = $val;
            }
        }
        $object['opname_header']['ids'] = $ids;

        $this->load->view('transaksi1/stock_outlet/stock/edit_view', $object);
    }

    public function showStockDetail(){
        $id_opname_header = $this->input->post('id_opname_header');
        $stts = $this->input->post('status');
        $rs = $this->st_model->opname_details_select($id_opname_header);
        $head = $this->st_model->headTemplate();
        $dt = array();
        $i = 1;
        if($rs){
            foreach($rs as $key=>$value){
                $room = $this->st_model->opname_room_select($id_opname_header,$value['id_opname_detail']);
                $nestedData=array();
                $room_data=array();
                $nestedData['no'] = $i;
                $nestedData['whscode'] = $this->session->userdata['ADMIN']['plant'];
                $nestedData['itmgrp'] = $value['item_grp_name'];
                $nestedData['itmcode'] = $value['material_no'];
                $nestedData['itmname'] = $value['material_desc'];
                $nestedData['onhand'] = $value['stock'];
                $nestedData['uom'] = $value['uom'];
                $nestedData['akm'] = $value['requirement_qty'];
                for ($j=1; $j <= count($head); $j++) { 
                    $nestedData['qr'.$room[$j-1]['id_room']] = $room[$j-1]['requirement_qty'];
                }
                $dt[] = $nestedData;
                $i++;
            }
        }

        $json_data = array(
            "data" => $dt
        );
        echo json_encode($json_data);
    }
    
    function getdataDetailMaterial(){
		$item_group_code = $this->input->post('matGroup');
		
		if($item_group_code == 'all'){
			$data = $this->st_model->sap_item_groups_select_all_grnonpo();
		}else{
			$data = $this->st_model->sap_items_select_by_item_group($item_group_code, 'stock');
		}
        echo json_encode($data);

	}
	
	function getdataDetailMaterialSelect(){
        $itemSelect = $this->input->post('MATNR');
        
        $dataMatrialSelect = $this->st_model->sap_item_groups_select_all_grnonpo($itemSelect);

        echo json_encode($dataMatrialSelect);
    }
    
    public function chageDataDB(){
        $id_opname_header = $this->input->post('idopname_header');
        $sum = count($this->input->post('idopname_detail'));
        $succes_update_detail = false;
        for($i = 0; $i < $sum; $i++){
            $opname_detail['id_opname_detail'] = $this->input->post('idopname_detail')[$i];
            $opname_detail['requirement_qty'] = $this->input->post('qty')[$i];
            if($this->st_model->changeUpdateToDb($opname_detail))
            $succes_update_detail = true;
        }

        if($succes_update_detail){
            return $this->session->set_flashdata('success', "Stock Opname Berhasil di Update");
        }else{
            return $this->session->set_flashdata('failed', "Stock Opname Gagal di Update");
        } 
    }
    
    public function addDataUpdate(){
        $admin_id = $this->session->userdata['ADMIN']['admin_id'];

        $opname_header['id_opname_header'] = $this->input->post('idHead');
        $opname_header['posting_date'] = $this->l_general->str_to_date($this->input->post('postDate'));
        $opname_header['status'] = $this->input->post('appr')? $this->input->post('appr') : '1';
        $opname_header['id_user_approved'] = $this->input->post('appr')? $admin_id : 0;
        $opname_header['am_approved'] = 0;
        $opname_header['rm_approved'] = 0;

        $opname_details['material_no'] = $this->input->post('detMatrialNo');
        $count = count($opname_details['material_no']);

        $id_detail = $this->st_model->opname_details_select($opname_header['id_opname_header']);

        if($this->st_model->opname_header_update($opname_header)){
            $update_detail_success = false;
            if ($this->st_model->opname_details_delete($opname_header['id_opname_header'])) {
                for($i =0; $i < $count; $i++){
                    $opname_detail['id_opname_header'] = (int)$opname_header['id_opname_header'];
                    $opname_detail['id_opname_h_detail'] = $i+1;
                    $opname_detail['item_grp_name'] = $this->input->post('ItemGrp')[$i];
                    $opname_detail['material_no'] = $this->input->post('detMatrialNo')[$i];
                    $opname_detail['material_desc'] = $this->input->post('detMatrialDesc')[$i];
                    $opname_detail['requirement_qty'] = (float)$this->input->post('detQty')[$i];
                    $opname_detail['uom'] = $this->input->post('detUom')[$i];
                    $opname_detail['stock'] = $this->input->post('OnHand')[$i];
    
                    $countRoom = count($this->input->post('Qr')[$i]);
    
                    if($id_opname_detail = $this->st_model->opname_detail_insert($opname_detail)){
                        if ($this->st_model->opname_room_delete($opname_header['id_opname_header'],$id_detail[$i]['id_opname_detail'])) {
                            for ($j=0; $j < $countRoom; $j++) { 
                                $temp = $this->input->post('Qr')[$i][$j];
                                $temp_data = explode('|',$temp);
                                $opname_room_detail['id_opname_header'] = (int)$opname_header['id_opname_header'];
                                $opname_room_detail['id_opname_detail'] = $id_opname_detail;
                                $opname_room_detail['id_room'] = $temp_data[0];
                                $opname_room_detail['requirement_qty'] = $temp_data[1];
                                $opname_room_detail['uom'] = $this->input->post('detUom')[$i];
        
                                if ($this->st_model->opname_room_detail_insert($opname_room_detail)) {
                                    $update_detail_success = TRUE;
                                }
                            }
                        }
                    }
                }
            }
        }

        if($update_detail_success){
            return $this->session->set_flashdata('success', "Stock Opname Berhasil di Update");
        }else{
            return $this->session->set_flashdata('failed', "Stock Opname Gagal di Update");
        }
    }

    public function actionAreaMgr(){
        $admin_id = $this->session->userdata['ADMIN']['admin_id'];

        $opname_header['id_opname_header'] = $this->input->post('idHead');
        if ($this->input->post('action')==1) {
            $opname_header['request_reason'] = $this->input->post('Reason');
        }
        $opname_header['id_am'] = $this->input->post('idMgr')=='am' ? $admin_id : 0;
        $opname_header['am_approved'] = $this->input->post('idMgr')=='am' ? $this->input->post('action') : 0;

        $succes_update_detail = false;
        if($this->st_model->opname_header_update_area_mgr($opname_header))
            $succes_update_detail = true;

        if($succes_update_detail && $this->input->post('action')==1){
            return $this->session->set_flashdata('failed', "Stock Opname Rejected By Area Manager");
        } else {
            return $this->session->set_flashdata('success', "Stock Opname Berhasil Approved By Area Manager");
        }
    }

    public function actionRegMgr(){
        $admin_id = $this->session->userdata['ADMIN']['admin_id'];

        $opname_header['id_opname_header'] = $this->input->post('idHead');
        if ($this->input->post('action')==1) {
            $opname_header['request_reason'] = $this->input->post('Reason');
        }
        $opname_header['id_rm'] = $this->input->post('idMgr')=='rm' ? $admin_id : 0;
        $opname_header['rm_approved'] = $this->input->post('idMgr')=='rm' ? $this->input->post('action') : 0;

        $succes_update_detail = false;
        if($this->st_model->opname_header_update_reg_mgr($opname_header))
            $succes_update_detail = true;

        if($succes_update_detail && $this->input->post('action')==1){
            return $this->session->set_flashdata('failed', "Stock Opname Rejected By Regional Manager");
        } else {
            return $this->session->set_flashdata('success', "Stock Opname Berhasil Approved By Regional Manager");
        }
    }

    public function deleteData(){
        $id_opname_header = $this->input->post('deleteArr');
        $deleteData = false;
        foreach($id_opname_header as $id){
            if($this->st_model->t_opname_header_delete($id))
            $deleteData = true;
        }
        
        if($deleteData){
            return $this->session->set_flashdata('success', "Stock Opname Berhasil dihapus");
        }else{
            return $this->session->set_flashdata('failed', "Stock Opname Approved, Gagal dihapus");
        }
    }

    function readFile(){

        $config['upload_path'] = './files/uploads';
        $config['allowed_types'] = 'xls|xlsx|xlsm|csv';
        $config['max_size'] = '10000';
        $config['overwrite'] = TRUE;
        $config['file_name'] = 'stock-opname-'.$this->session->userdata['ADMIN']['plant'].'-'.date('d-m-Y');

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('file')) {
            $data_upload = $this->upload->data();

            
            $excelreader = new PHPExcel_Reader_Excel2007();
            $loadexcel   = $excelreader->load('files/uploads/'.$data_upload['file_name']);
            $sheet       = $loadexcel->getActiveSheet()->toArray(null, true, true ,true);

            $data = array();
            $error = array();

            $numrow = 9;
            $no=0;
            foreach($sheet as $row){
                if($numrow > 17){
                    $object['validate'] = $this->st_model->checkTemplate($row['C']);
                    if ($object['validate']) {
                        array_push($data, array(
                            'no' => ($no-8),
                            'whscode' => $row['A'],
                            'itmgrp' => $row['B'],
                            'itmcode' => $row['C'],
                            'itmname' => $row['D'],
                            'onhand' => $row['E'],
                            'uom' => $row['F'],
                            'qr1' => $row['G'],
                            'qr2' => $row['H'],
                            'qr3' => $row['I'],
                            'qr4' => $row['J'],
                            'qr5' => $row['K'],
                            'qr6' => $row['L'],
                            'qr7' => $row['M'],
                            'qr8' => $row['N'],
                            'akm' => ($row['G']+$row['H']+$row['I']+$row['J']+$row['K']+$row['L']+$row['M']+$row['N'])
                        ));
                    } else {
                        array_push($error, array(
                            'error' => 1
                        ));
                    }
                }
                $numrow++;
                $no++;
            }

            $json_data = array(
                "data"  => $data,
                "error" => $error
            );

            echo json_encode($json_data);
        } else {
            echo $this->upload->display_errors();
        }
    }
    
    function printpdf(){
		$id_opname_header = $this->uri->segment(4);
		$data['data'] = $this->st_model->tampil($id_opname_header);
		
		ob_start();
		$content = $this->load->view('transaksi1/stock_outlet/stock/printpdf_view',$data);
		$content = ob_get_clean();		
		require_once(APPPATH.'libraries/html2pdf/html2pdf.class.php');
		try
		{
			$html2pdf = new HTML2PDF('P', 'A4', 'en');
			$html2pdf->setTestTdInOnePage(false);
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
			$html2pdf->Output('print.pdf');
		}
		catch(HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
		
    }

    function downloadExcel(){
        $kd_plant = $this->session->userdata['ADMIN']['plant'];
        $plant_name = $this->session->userdata['ADMIN']['plant_name'];
        $object['dataOnHand'] = $this->st_model->template();
        $object['head'] = $this->st_model->headTemplate();

        $excel = new PHPExcel();

        // set config for image
        $imgHead = new PHPExcel_Worksheet_Drawing();
        $imgHead->setName('Logo');
        $imgHead->setDescription('Logo');
        $imgHead->setPath('./files/assets/images/logo.jpeg');
        $imgHead->setHeight(70);
        $imgHead->setCoordinates('D1');
        $imgHead->setOffsetX(220);
        $imgHead->setWorksheet($excel->getActiveSheet());

        //set config for column width
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(9);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(9);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(9);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(9);
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth(9);
        $excel->getActiveSheet()->getColumnDimension('J')->setWidth(9);
        $excel->getActiveSheet()->getColumnDimension('K')->setWidth(13);
        $excel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
        $excel->getActiveSheet()->getColumnDimension('M')->setWidth(9);
        $excel->getActiveSheet()->getColumnDimension('N')->setWidth(10);

        // set config for title header file
        $excel->getActiveSheet()->getStyle('A5')->getAlignment()
              ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $excel->getActiveSheet()->getStyle('A6')->getAlignment()
              ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $excel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
        $excel->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);

        $excel->getActiveSheet()->mergeCells('A5:N5');
        $excel->setActiveSheetIndex(0)->setCellValue('A5', 'Template Stock Opname'); 
        $excel->getActiveSheet()->mergeCells('A6:N6');
        $excel->setActiveSheetIndex(0)->setCellValue('A6', 'Outlet '.$kd_plant.' '.$plant_name); 

        //style of border
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '00000000'),
                ),
            ),
        );
        
        $excel->getActiveSheet()->getStyle('A9:N9')->applyFromArray($styleArray);

        // set config for title header table 
        $excel->getActiveSheet()->getStyle('A9:N9')->getAlignment()
              ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $excel->getActiveSheet()->getStyle('A9:N9')->getAlignment()
              ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $excel->getActiveSheet()->getStyle('A9:N9')->getFont()->setBold(true);
        
        $excel->getActiveSheet()->getRowDimension('9')->setRowHeight(20);

        $excel->setActiveSheetIndex(0)->setCellValue('A9', "WHSCODE"); 
        $excel->setActiveSheetIndex(0)->setCellValue('B9', "Item Group"); 
        $excel->setActiveSheetIndex(0)->setCellValue('C9', "Item Code"); 
        $excel->setActiveSheetIndex(0)->setCellValue('D9', "Item Name"); 
        $excel->setActiveSheetIndex(0)->setCellValue('E9', "On Hand");
        $excel->setActiveSheetIndex(0)->setCellValue('F9', "UOM"); 
        foreach( range('G', 'N') as $key=>$alp) { 
            $excel->setActiveSheetIndex(0)->setCellValue($alp.'9', $object['head'][$key]['Name']);
        }

        $numrow = 10;
        foreach($object['dataOnHand'] as $key=>$r){ 
            $hrf = range('G', 'N');
            // applying border style
            $excel->getActiveSheet()->getStyle('A'.$numrow.':N'.$numrow)->applyFromArray($styleArray);

            // set config alignment body table
            $excel->getActiveSheet()->getStyle('A'.$numrow.':B'.$numrow)->getAlignment()
              ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $excel->getActiveSheet()->getStyle('E'.$numrow.':F'.$numrow)->getAlignment()
              ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $excel->setActiveSheetIndex(0)->setCellValue('A'.$numrow, $kd_plant);
            $excel->setActiveSheetIndex(0)->setCellValue('B'.$numrow, $r['ItmsGrpNam']);
            $excel->setActiveSheetIndex(0)->setCellValue('C'.$numrow, $r['ItemCode']);
            $excel->setActiveSheetIndex(0)->setCellValue('D'.$numrow, $r['ItemName']);
            $excel->setActiveSheetIndex(0)->setCellValue('E'.$numrow, (float)$r['OnHand']);
            $excel->setActiveSheetIndex(0)->setCellValue('F'.$numrow, $r['UNIT']);

            foreach( range('G', 'N') as $key=>$alp) { 
                $excel->setActiveSheetIndex(0)->setCellValue($alp.$numrow, '');
            }
            $numrow++;
        }
    
        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);
        // Set orientasi kertas jadi LANDSCAPE
        $excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        // Set judul file excel nya
        $excel->getActiveSheet(0)->setTitle("Laporan Stock Opname");
        $excel->setActiveSheetIndex(0);
        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Stock Opname.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $write->save('php://output');
    }
}
?>