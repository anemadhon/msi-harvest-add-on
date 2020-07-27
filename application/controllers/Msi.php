<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Msi extends CI_Controller {
	
	public function __construct(){
		parent::__construct();  
		$this->load->library('auth');  
		if(!$this->auth->is_logged_in()) {
			redirect(base_url());
		}
		
		$this->load->library('l_general');
        
		// load model
		$this->load->model('dashboard_model', 'dash_model');
		$this->load->model('transaksi1/stock_model', 'st_model');
	}

	public function dashboard(){
		
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
		
		$object['tglterkini'] = date("j M Y",strtotime($this->dash_model->posting_date_select_max()));

		$object['nama'][101]="PO from Vendor";
		$object['data'][101] = $this->dash_model->getCountPOVendor();
		$object['link'][101] = "/transaksi1/pofromvendor/add";

		$object['nama'][102]="Good Receipt From CK";
		$object['data'][102] = $this->dash_model->getCountGRfromKitchen();
		$object['link'][102] = "/transaksi1/grfromkitchensentul/add";

		$object['nama'][103] = "Good Issue Stock Transfer Antar Outlet";
		$object['data'][103] = $this->dash_model->getCountTransferOut();
		$object['link'][103] = "/transaksi1/transferoutinteroutlet/add";

		$object['nama'][104] = "Good Receipt From Outlet";
		$object['data'][104] = $this->dash_model->getCountTransferIn();
		$object['link'][104] = "/transaksi1/transferininteroutlet/add";
		
		$object['nama'][105] = "Integration Log";
		$object['data'][105] = $this->dash_model->getCountIntLog();
		$object['link'][105] = "/master/integration";

		$this->load->view('index', $object);
	}
	
	public function inpofromvendor(){
		
		$this->load->view('template/header');
		$this->load->view('transaksi1/eksternal/po_from_vendor');
		$this->load->view('template/footer');
	}
	
	public function purchaserequest(){
		
		$this->load->view('template/header');
		$this->load->view('transaksi1/eksternal/purchase_request');
		$this->load->view('template/footer');
	}
	
}
