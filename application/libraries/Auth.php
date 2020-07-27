<?php

class Auth {

	protected $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('master/permission_model', 'm_perm');
		
		
	}
	
	function is_logged_in() {
		
		if ($this->CI->session) {

			if ($this->CI->session->userdata('logged_in')){
				return TRUE;
			}else{
				return FALSE;
			}

		} else {
			return FALSE;
		}
		
	}

	function is_have_perm($perm_name, $admin_id = 0) {
		$this->CI->lang->load('g_perm', $this->CI->session->userdata('lang_name'));
		if($admin_id === 0)
			$admin_id = $this->CI->session->userdata['ADMIN']['admin_id'];

			

		if(is_array($perm_name)) {

			foreach($perm_name as $perm_value) {

				if(empty($perm_value))
					return TRUE;


				$perm_group_ids = $this->CI->m_perm->admin_perm_group_ids_select($admin_id);
				
				foreach($perm_group_ids as $perm_group_id) {
					$admin_perm = $this->CI->m_perm->admin_perm_select($perm_group_id);

					$perm_code = $this->CI->m_perm->perm_code_select($perm_value);
	
					@$perm_have = substr_count($admin_perm, $perm_code);
	

					if($perm_have || ($admin_perm == "*"))
						return TRUE;
				}


			}
			return FALSE;

		} else {

			$perm_group_ids = $this->CI->m_perm->admin_perm_group_ids_select($admin_id);

			
				
			
			foreach($perm_group_ids as $perm_group_id) {
				
				$admin_perm = $this->CI->m_perm->admin_perm_select($perm_group_id);
				
				$perm_code = $this->CI->m_perm->perm_code_select($perm_name);
				
				@$perm_have = substr_count($admin_perm, $perm_code);


				if($perm_have || ($admin_perm == "*"))
					return TRUE;

			}

			return FALSE;

		}

	}

	function perm_code_merge($perm_code) {
		$this->CI->lang->load('g_perm', $this->CI->session->userdata('lang_name'));
		$temp = array_keys($perm_code);

		foreach($temp as $key1 => $value1) {
			foreach($perm_code[$value1] as $key2 => $value2) {
				if(!is_array($value2))
					$return[] = $value2;
			}
		}
		return $return;
	}
	
	function perm_group_detail($group_id) {
		$this->CI->lang->load('g_perm', $this->CI->session->userdata('lang_name'));
		if(!$perm_group = $this->CI->m_perm->perm_group_select($group_id))
    	return FALSE;

		if(!$perms = $this->CI->m_perm->perms_select_all())
			return FALSE;

		$detail = array();
		// will be $detail[$i][$j]
		// $i and $j declared below, based on content of $perms

		foreach ($perms->result_array() as $perm) {

			
			$i = $perm['cat_id']; // array dimension 1
			$j = $perm['perm_order']; // array dimension 2

			// define GROUP permission
			if(!isset($detail[$i][0])) {
				$detail[$i][0] = $perm;
				$detail[$i][0]['category_name'] = $perm['category_name'];
				$detail[$i][0]['category_descr'] = $this->CI->lang->line('perm_opt_category_'.$perm['category_name']);
				$detail[$i][0]['category_code'] = $perm['category_code'];
			}

			// define permission
			if(substr_count($perm_group['group_perms'], $perm['category_code'].sprintf("%02s", $perm['perm_code']))) {
				$detail[$i][$j] = $perm;
				$detail[$i][$j]['perm_descr'] = $this->CI->lang->line('perm_opt_'.$perm['perm_name']);
			}

		}

		return $detail;

	}

	function perm_group_detail_show($group_id) {
		$this->CI->lang->load('g_perm', $this->CI->session->userdata('lang_name'));
		$return = '';

		$detail = $this->perm_group_detail($group_id);

		foreach ($detail as $key1 => $detail1) {

    	$count = count($detail1);

			if($count > 1) {
				$j = 1; // counter to match with last of $detail1
				foreach ($detail1 as $key2 => $detail2) {

					if($key2 == 0) {
						$return .= "<strong>".$detail2['category_descr']."</strong>\n";
						$return .= "<ul>\n";
					} else {
						$return .= "<li>".$detail2['perm_descr']."</li>\n";
					}

					if($j == ($count)) {
						$return .= "</ul>\n";
					}

					$j++;
				}
			}

		}

		return $return;

	}

	function startup() {
		$this->CI->lang->load('g_perm', $this->CI->session->userdata('lang_name'));
		$startup = $this->CI->session->userdata('startup');

		if(!empty($startup)) {

			$this->CI->session->unset_userdata('startup');
			$this->CI->session->unset_userdata('password');

		}
	}

	function is_have_perm_category($perm_name, $admin_id = 0) {
		if($admin_id === 0)
			$admin_id = $this->CI->session->userdata['ADMIN']['admin_id'];
	
		if(is_array($perm_name)) {
			foreach($perm_name as $perm_value) {
				if(empty($perm_value))
					return TRUE;
	
					$perm_groups_id = $this->CI->m_perm->admin_perm_group_ids_select($admin_id);

					foreach($perm_groups_id as $perm_group_id) {
						$admin_perm = $this->CI->m_perm->admin_perm_select($perm_group_id);
						$perm_code = $this->CI->m_perm->perm_category_code_select($perm_value);
						@$perm_have = substr_count($admin_perm, $perm_code);

						if($perm_have || ($admin_perm == "*"))
							return TRUE;
					}
	
				}
	
				return FALSE;
	
			} else {
	
				$perm_groups_id = $this->CI->m_perm->admin_perm_group_ids_select($admin_id);
	
				foreach($perm_groups_id as $perm_group_id) {
					$admin_perm = $this->CI->m_perm->admin_perm_select($perm_group_id);
					$perm_code = $this->CI->m_perm->perm_category_code_select($perm_name);
					@$perm_have = substr_count($admin_perm, $perm_code);
	
					if($perm_have || ($admin_perm == "*"))
						return TRUE;
				}
	
				return FALSE;
	
			}
	
		}

}