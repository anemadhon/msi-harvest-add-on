<!DOCTYPE html>
<html lang="en">
	<head>
		<?php  $this->load->view("_template/head.php")?>
		<style>
			th{
				text-align:center;
			}
			td{
				text-align:center;
			}
		</style>
		<style>
			.after-submit {
				display: none;
			}
			#load,
			#load:before,
			#load:after {
				background: #777;
				-webkit-animation: load1 1s infinite ease-in-out;
				animation: load1 1s infinite ease-in-out;
				width: 1em;
				height: 4em;
			}
			#load {
				color: #777;
				text-indent: -9999em;
				margin: 88px auto;
				position: relative;
				font-size: 11px;
				-webkit-transform: translateZ(0);
				-ms-transform: translateZ(0);
				transform: translateZ(0);
				-webkit-animation-delay: -0.16s;
				animation-delay: -0.16s;
			}
			#load:before,
			#load:after {
				position: absolute;
				top: 0;
				content: '';
			}
			#load:before {
				left: -1.5em;
				-webkit-animation-delay: -0.32s;
				animation-delay: -0.32s;
			}
			#load:after {
				left: 1.5em;
			}
			@-webkit-keyframes load1 {
				0%,
				80%,
				100% {
					box-shadow: 0 0;
					height: 4em;
				}
				40% {
					box-shadow: 0 -2em;
					height: 5em;
				}
			}
			@keyframes load1 {
				0%,
				80%,
				100% {
					box-shadow: 0 0;
					height: 4em;
				}
				40% {
					box-shadow: 0 -2em;
					height: 5em;
				}
			}
		</style>
		<style>
			#indicatorCosting {
				padding: 2px;
			}
		</style>
	</head>
	<body>
	<?php  $this->load->view("_template/nav.php")?>
		<div class="page-content">
			<?php  $this->load->view("_template/sidebar.php")?>
			<div class="content-wrapper">
				<div class="content">
					<?php if ($this->session->flashdata('success')): ?>
						<div class="alert alert-success" role="alert">
							<?php echo $this->session->flashdata('success'); ?>
						</div>
					<?php endif; ?>
					<?php if ($this->session->flashdata('failed')): ?>
						<div class="alert alert-danger" role="alert">
							<?php echo $this->session->flashdata('failed'); ?>
						</div>
					<?php endif; ?>
                    <form action="#" method="POST" autocomplete="off">
						<div class="card">
							<div class="card-body">
								<div class="row">
									<div class="col-md-12">
										<fieldset>
											<legend class="font-weight-semibold"><i class="icon-reading mr-2"></i>Edit Product Costing</legend>
											<div class="form-group row">
												<label class="col-lg-3 col-form-label">No. Product</label>
												<div class="col-lg-9">
													<input type="text" class="form-control" value="<?php echo $pc['prod_cost_no'] ?>" readOnly>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Product</label>
												<div class="col-lg-9">
													<input type="text" class="form-control" name="doc_status" id="docStatus" value="<?php echo $pc['existing_bom_code'] ? 'Existing' : 'New' ?>" readOnly>
													<input type="hidden" id="idProdCost" value="<?php echo $pc['id_prod_cost_header'] ?>">
												</div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Costing Type</label>
												<div class="col-lg-9">
													<input type="text" class="form-control" id="productType" value="<?php echo $pc['product_type'] == 1 ? 'WP' : 'Finish Goods' ?>" readOnly>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Category</label>
												<div class="col-lg-9">
													<select name="category" id="category" class="form-control form-control-select2" data-live-search="true" onchange="getDataForQFactorFormula(this.value)">
													<option value="">Select Category</option>
														<?php foreach($categories as $key=>$value){?>
															<option value="<?=$value['Code']?>" desc="<?=$value['Name']?>" <?php echo $value['Code'] == $pc['category_code'] ? 'selected' : '' ?>><?=$value['Name']?></option>
														<?php }; ?>
													</select>
													<input type="hidden" id="qFactorSAP" value="<?php echo $pc['q_factor_sap'] ?>">
													<input type="hidden" id="minCostSAP" value="<?php echo $pc['min'] ?>">
													<input type="hidden" id="maxCostSAP" value="<?php echo $pc['max'] ?>">
												</div>
											</div>

											<?php if($pc['existing_bom_code']) {?>
											<div class="form-group row" id="existingCost">
												<label class="col-lg-3 col-form-label">Existing Bom</label>
												<div class="col-lg-9">
													<input type="text" class="form-control" name="existing_bom" id="existingBom" value="<?php echo $pc['existing_bom_code'].' - '.$pc['existing_bom_name'] ?>" readOnly>
												</div>
											</div>
											<?php } ?>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Name</label>
												<div class="col-lg-9">
													<input type="text" class="form-control" name="product_name" id="productName" value="<?php echo $pc['product_name'] ?>">
												</div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Qty Produksi</label>
												<div class="col-lg-9">
													<input type="text" class="form-control" name="product_qty" id="productQty" value="<?php echo $pc['product_qty'] ?>" onchange="multiplyingQtyItems_setTotalCost(this.value)">
												</div>
											</div>	

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">UOM</label>
												<div class="col-lg-9">
													<input type="text" class="form-control" name="product_uom" id="productUom" value="<?php echo $pc['product_uom'] ?>" <?php echo $pc['existing_bom_code'] ? 'readOnly' : '' ?>>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Posting Date</label>
												<div class="col-lg-9 input-group date">
													<input type="text" class="form-control" id="postDate" value="<?php echo date('d-m-Y', strtotime($pc['posting_date'])) ?>" readonly autocomplate="off">
													<div class="input-group-prepend">
														<span class="input-group-text" id="basic-addon1">
															<i class="icon-calendar"></i>
														</span>
													</div>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Status</label>
												<div class="col-lg-9">
													<input type="text" class="form-control" id="status" value="<?php echo ($pc['status'] == 1 || $pc['status_head'] == 0) ? 'Not Approved' : 'Approved' ?>" readOnly>
												</div>
											</div>
											
											<?php if ($pc['status'] == 2) { ?>
											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Head of Department</label>
												<div class="col-lg-9">
													<input type="text" class="form-control" value="<?php echo $pc['status'] == 2 && $pc['status_head'] == 2 ? 'Approved' : ($pc['status_head'] == 0 ? 'Rejected' : 'Not Approved') ?>" readOnly>
												</div>
											</div>
											<?php } ?>
											
											<?php if ($pc['status_head'] == 0 && $pc['reject_reason']) { ?>
											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Reject Reason</label>
												<div class="col-lg-9">
													<input type="text" class="form-control" value="<?php echo $pc['reject_reason']?>" readOnly>
												</div>
											</div>
											<?php } ?>
											
											<div class="form-group row wp">
												<label class="col-lg-3 col-form-label">Selling Price (include Tax)</label>
												<div class="col-lg-9">
													<input type="text" class="form-control" name="product_sell_price" id="productSellPrice" value="<?php echo $pc['product_selling_price'] ?>" onchange="setProdCostPercentage(this.value)">
												</div>
											</div>
											
											<div class="form-group row wp">
												<label class="col-lg-3 col-form-label">Product Costing</label>
												<div class="col-lg-9">
													<p class="mt-1"><span id="percentageCosting"></span> <span id="indicatorCosting"></span></p>
												</div>
											</div>

											<?php 
											$isUser = 0;
											if ($this->auth->is_head_dept()) {
												foreach ($this->auth->is_head_dept()['users'] as $user) {
													if ($user['admin_id'] == $pc['user_input']) {
														$isUser = $user['admin_id'];
														break;
													};
												}
											}
											?>

											<div class="text-right" id="after-submit" style="display: none;">
												<?php if ($pc['status'] == 1 && $this->auth->is_have_perm('auth_approve') || $pc['status_head'] == 0) : ?>
													<button type="button" class="btn btn-primary" name="save" id="save" onclick="addDatadb(1)">Save <i class="icon-pencil5 ml-2"></i></button>
													<button type="button" class="btn btn-success" name="approve" id="approve" onclick="addDatadb(2)" >Approve <i class="icon-paperplane ml-2" ></input></i>
												<?php elseif($pc['status'] == 2 && $this->auth->is_have_perm('auth_approve') && $pc['status_head'] != 0 && $this->auth->is_head_dept()['head_dept'] == $pc['user_login'] && $isUser != 0) : ?>
													<?php if($pc['status_head'] == 1) : ?>
														<button type="button" class="btn btn-danger" name="reject" id="reject" data-toggle="modal" data-target="#exampleModal" data-backdrop="static">Reject<i class="icon-paperplane ml-2"></i></button>	
													<?php endif; ?>
													<?php if($pc['status'] != 2 || $pc['status_head'] != 2) : ?>
														<button type="button" class="btn btn-success" name="approve" id="approve" onclick="addDatadb(3)" >Approve <i class="icon-paperplane ml-2" ></input></i>
													<?php endif; ?>
												<?php endif; ?>
											</div>
											
										</fieldset>
									</div>
								</div>
							</div>
						</div> 
						<div id="load" style="display:none"></div>  

						<div class="card">
							<div class="card-body">
								<div class="row">
									<div class="col-md-12">
										<div class="text-left">
											<p>Total Ingredients Cost : <span id="totAllIngCost">0</span></p>
											<p>Total Packaging Cost : <span id="totAllPackCost">0</span></p>
											<p>Q Factor : <span id="qFactorResult">0</span></p>
											<p>Total Product Cost : <span id="totProdCost">0</span></p>
											<p>Total Product Cost / Qty Produksi: <span id="totProdCostDivQtyProd">0</span></p>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="card">
							<div class="card-body">
								<div class="row">
									<legend class="font-weight-semibold"><i class="icon-list mr-2"></i>List Item Ingredients</legend>
									<div class="col-md-8 mb-2 after-doc" style="display: none;">
										<div class="text-left">
											<select name="item_group_ing" id="itemGroupIng" class="form-control form-control-select2" data-live-search="true">
												<option value="">Select Item Group</option>
												<?php foreach($matrialGroup as $key=>$value){?>
													<option value="<?=$value['ItmsGrpNam']?>" desc="<?=$value['ItmsGrpNam']?>"><?=$value['ItmsGrpNam']?></option>
												<?php };?>
												<option value="1" desc="Costing WP">Costing WP</option>
												<option value="2" desc="Costing Finish Goods">Costing Finish Goods</option>
											</select>
										</div>
									</div>
									<?php if($pc['status_head'] != 2) : ?>
									<div class="col-md-4 mb-2 after-doc" style="display: none;">
										<div class="text-right">
											<input type="button" class="btn btn-primary" value="Add" id="addTableIng" onclick="onAddrowItemIngredients()"> 
											<input type="button" value="Delete" class="btn btn-danger" id="deleteRecordIng"> 
										</div>
									</div>
									<?php endif; ?>
									<div class="col-md-12" style="overflow: auto" >
										<table class="table table-striped" id="tblItemIngredients">
											<thead>
												<tr>
													<th><input type="checkbox" name="checkall_ing" id="checkallIng"></th>
													<th>No</th>
													<th>Item Code</th>
													<th>Item Desc</th>
													<th>UOM</th>
													<th>Unit Cost</th>
													<th>Quantity</th>
													<th>Total Cost</th>
												</tr>
											</thead>
											<tbody></tbody>
										</table>
									</div>
								</div>
							</div>
						</div> 

						<div class="card">
							<div class="card-body">
								<div class="row">
									<legend class="font-weight-semibold"><i class="icon-list mr-2"></i>List Item Packaging</legend>
									<div class="col-md-8 mb-2 after-doc" style="display: none;">
										<div class="text-left">
											<select name="item_group_pack" id="itemGroupPack" class="form-control form-control-select2" data-live-search="true">
												<option value="">Select Item Group</option>
												<?php foreach($matrialGroup as $key=>$value){?>
													<option value="<?=$value['ItmsGrpNam']?>" desc="<?=$value['ItmsGrpNam']?>"><?=$value['ItmsGrpNam']?></option>
												<?php };?>
												<option value="1" desc="Costing WP">Costing WP</option>
												<option value="2" desc="Costing Finish Goods">Costing Finish Goods</option>
											</select>
										</div>
									</div>
									<?php if($pc['status_head'] != 2) : ?>
									<div class="col-md-4 mb-2 after-doc" style="display: none;">
										<div class="text-right">
											<input type="button" class="btn btn-primary" value="Add" id="addTablePack" onclick="onAddrowItemPackaging()"> 
											<input type="button" value="Delete" class="btn btn-danger" id="deleteRecordPack"> 
										</div>
									</div>
									<?php endif; ?>
									<div class="col-md-12" style="overflow: auto" >
										<table class="table table-striped" id="tblItemPackaging">
											<thead>
												<tr>
													<th><input type="checkbox" name="checkall_pack" id="checkallPack"></th>
													<th>No</th>
													<th>Item Code</th>
													<th>Item Desc</th>
													<th>UOM</th>
													<th>Unit Cost</th>
													<th>Quantity</th>
													<th>Total Cost</th>
												</tr>
											</thead>
											<tbody></tbody>
										</table>
									</div>
								</div>
							</div>
						</div> 
                    </form>          
				</div>
				<!-- Modal -->
				<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="exampleModalLabel">Reject Reason</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								<div class="form-group">
									<label for="message-text" class="col-form-label">Reason</label>
									<textarea class="form-control" id="reason"></textarea>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-danger" onclick="reject()">Send</button>
							</div>
						</div>
					</div>
				</div>
				<?php $this->load->view("_template/footer.php")?>
			</div>
		</div>
        <?php  $this->load->view("_template/js.php")?>
		<script>
			$(document).ready(function(){

				const date = new Date();
				const today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
				var optSimple = {
					format: 'dd-mm-yyyy',
					todayHighlight: true,
					orientation: 'bottom right',
					autoclose: true
				};

				if ($('#productType').val() == 'Finish Goods') {
					$('.wp').show();
				} else {
					$('.wp').hide();
				}

				$('#productSellPrice').val(parseFloat($('#productSellPrice').val()).toLocaleString(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4}))

				$('#productSellPrice').change(function () {
					if ($(this).val() && $(this).val().includes(',')) {
						$(this).val($(this).val().replace(',','').replace(',',''));
						$(this).val(parseFloat($(this).val()).toLocaleString(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4}));
					} else if ($(this).val() && !$(this).val().includes(',')) {
						$(this).val(parseFloat($(this).val()).toLocaleString(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4}));
					} else {
						$(this).val(0);
					}
				});

				$('#tblItemIngredients').DataTable({
					"ordering":false, "paging": false, "searching":true,
					drawCallback: function() {
						$('.form-control-select2').select2();
					},
					"initComplete": function(settings, json) {
						$(".after-doc").show();
						setFoodCostItem();
						setProdCostPercentage($('#productSellPrice').val());
					},
					"ajax": {
						"url":"<?php echo site_url('transaksi1/productcosting/showDetailEdit');?>",
						"data":{ 
							id: $('#idProdCost').val(),
							type: 1
						},
						"type":"POST"
					},
					"columns": [
						{"data":"0", "className":"dt-center", render:function(data, type, row, meta){
							rr=`<input type="checkbox" value="${data}" class="check_delete_ing" id="dt_ing_${data}">`;
							return rr;
						}},
						{"data":"1", "className":"dt-center"},
						{"data":"2", "className":"dt-center"},
						{"data":"3"},
						{"data":"4"},
						{"data":"5"},
						{"data":"6", render:function(data, type, row, meta){
							rr = `<input type="hidden" class="form-control ing" name="ing" id="ing_${row['1']}" value="1"><input type="text" class="form-control qty-ing" id="qtyCostingIng_${row['1']}" value="${data}" matqty="${data}" style="width:90px" autocomplete="off">`
							return rr;
						}},
						{"data":"7"}
					]
				});
				
				$("#tblItemPackaging").DataTable({
					"ordering":false,
					"paging":false,
					drawCallback: function() {
						$('.form-control-select2').select2();
					},
					"initComplete": function(settings, json) {
						$(".after-doc").show();
						setMaterialCostItem();
						setProdCostPercentage($('#productSellPrice').val());
					},
					"ajax": {
						"url":"<?php echo site_url('transaksi1/productcosting/showDetailEdit');?>",
						"data":{ 
							id: $('#idProdCost').val(),
							type: 2
						},
						"type":"POST"
					},
					"columns": [
						{"data":"0", "className":"dt-center", render:function(data, type, row, meta){
							rr=`<input type="checkbox" value="${data}" class="check_delete_pack" id="dt_pack_${data}">`;
							return rr;
						}},
						{"data":"1", "className":"dt-center"},
						{"data":"2", "className":"dt-center"},
						{"data":"3"},
						{"data":"4"},
						{"data":"5"},
						{"data":"6", render:function(data, type, row, meta){
							rr = `<input type="hidden" class="form-control pack" name="pack" id="pack_${row['1']}" value="2"><input type="text" class="form-control qty-pack" id="qtyCostingPack_${row['1']}" value="${data}" matqty="${data}" style="width:90px" autocomplete="off">`
							return rr;
						}},
						{"data":"7"}
					]
				});

				// untuk check all
				$("#checkallIng").click(function(){
					if($(this).is(':checked')){
						$(".check_delete_ing").prop('checked', true);
					}else{
						$(".check_delete_ing").prop('checked', false);
					}
				});
				
				$("#checkallPack").click(function(){
					if($(this).is(':checked')){
						$(".check_delete_pack").prop('checked', true);
					}else{
						$(".check_delete_pack").prop('checked', false);
					}
				});

				$("#deleteRecordIng").click(function(){
					let deleteidArrIng = [];
					let getTableIng = $("#tblItemIngredients").DataTable();
					$("input:checkbox[class=check_delete_ing]:checked").each(function(){
						deleteidArrIng.push($(this).val());
					})
					// mengecek ckeckbox tercheck atau tidak
					if(deleteidArrIng.length > 0){
						var confirmDeleteIng = confirm("Do you really want to Delete records?");
						if(confirmDeleteIng == true){
							$("input:checkbox[class=check_delete_ing]:checked").each(function(){
								getTableIng.row($(this).closest("tr")).remove().draw();
								setTotalFoodCost();
								setProdCostPercentage($('#productSellPrice').val());
							});
						}
					}
				});
				
				$("#deleteRecordPack").click(function(){
					let deleteidArrPack = [];
					let getTablePack = $("#tblItemPackaging").DataTable();
					$("input:checkbox[class=check_delete_pack]:checked").each(function(){
						deleteidArrPack.push($(this).val());
					})
					// mengecek ckeckbox tercheck atau tidak
					if(deleteidArrPack.length > 0){
						var confirmDeletePack = confirm("Do you really want to Delete records?");
						if(confirmDeletePack == true){
							$("input:checkbox[class=check_delete_pack]:checked").each(function(){
								getTablePack.row($(this).closest("tr")).remove().draw();
								setTotalMaterialCost();
								setProdCostPercentage($('#productSellPrice').val());
							});
						}
					}
				});

				let tbodyIng = $("#tblItemIngredients tbody");
				tbodyIng.on('change','.qty-ing', function(){
					let trIng = $(this).closest('tr');
					let noIng = trIng[0].rowIndex;
					setTotalCostIng($(this).val(),noIng);
					setProdCostPercentage($('#productSellPrice').val());
				});
				tbodyIng.on('change','.cost-ing', function(){
					let trIng = $(this).closest('tr');
					let noIng = trIng[0].rowIndex;
					if ($(this).val() && $(this).val().includes(',')) {
						$(this).val($(this).val().replace(',','').replace(',',''));
						$(this).val(parseFloat($(this).val()).toLocaleString(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4}));
					} else if ($(this).val() && !$(this).val().includes(',')) {
						$(this).val(parseFloat($(this).val()).toLocaleString(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4}));
					} else {
						$(this).val(0);
					}
					setTotalCostByPriceIng($(this).val(),noIng);
					setProdCostPercentage($('#productSellPrice').val());
				});
				let tbodyPack = $("#tblItemPackaging tbody");
				tbodyPack.on('change','.qty-pack', function(){
					let trPack = $(this).closest('tr');
					let noPack = trPack[0].rowIndex;
					setTotalCostPack($(this).val(),noPack);
					setProdCostPercentage($('#productSellPrice').val());
				});
				tbodyPack.on('change','.cost-pack', function(){
					let trPack = $(this).closest('tr');
					let noPack = trPack[0].rowIndex;
					if ($(this).val() && $(this).val().includes(',')) {
						$(this).val($(this).val().replace(',','').replace(',',''));
						$(this).val(parseFloat($(this).val()).toLocaleString(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4}));
					} else if ($(this).val() && !$(this).val().includes(',')) {
						$(this).val(parseFloat($(this).val()).toLocaleString(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4}));
					} else {
						$(this).val(0);
					}
					setTotalCostByPricePack($(this).val(),noPack);
					setProdCostPercentage($('#productSellPrice').val());
				});
				
			});

			function getDataForQFactorFormula($code){
				$.post("<?php echo site_url('transaksi1/productcosting/getDataForQFactorFormula');?>",{code:$code},(data) => {
					const value = JSON.parse(data);
					$("#qFactorSAP").val(value.data['q_factor'].slice(0,-2));
					$("#minCostSAP").val(value.data['min_cost'].slice(0,-2));
					$("#maxCostSAP").val(value.data['max_cost'].slice(0,-2));
					setTotalFoodCost();
					setTotalMaterialCost();
					setProdCostPercentage($('#productSellPrice').val());
				});
			}

			function multiplyingQtyItems_setTotalCost(productQty){
				if ($('#docStatus').val() == 'Existing') {
					let tableIng = $("#tblItemIngredients tbody");
					let tablePack = $("#tblItemPackaging tbody");
					tableIng.find('tr').each(function(i, el){
						let tdIng = $(this).find('td');
						let costIng = parseFloat(tdIng.eq(5).text() ? tdIng.eq(5).text().replace(',','').replace(',','') : 0);
						let qtyIng = parseFloat($('input:text', this).attr('matqty'));
						tdIng.eq(6).find('input:text').val(parseFloat(productQty) * qtyIng);
						tdIng.eq(7).text(qtyIng * costIng);
					});
					tablePack.find('tr').each(function(i, el){
						let tdPack = $(this).find('td');
						let costPack = parseFloat(tdPack.eq(5).text() ? tdPack.eq(5).text().replace(',','').replace(',','') : 0);
						let qtyPack = parseFloat($('input:text', this).attr("matqty"));
						tdPack.eq(6).find('input:text').val(parseFloat(productQty) * qtyPack);
						tdPack.eq(7).text(parseFloat(qtyPack * costPack).toLocaleStrPack(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4}));
					});
					setTotalFoodCost();
					setTotalMaterialCost();
					setProdCostPercentage($('#productSellPrice').val());
				} else {
					setTotalProdCostDivQtyProduct();
				}
			}

			function onAddrowItemIngredients(){
				let getTable = $("#tblItemIngredients").DataTable();
				count = getTable.rows().count() + 1;
				let elementSelect = document.getElementsByClassName(`dt-ing-${count}`);
				let itmGrp = $('#itemGroupIng option:selected').val();
				if (itmGrp) {
					getTable.row.add({
						"0":"",
						"1":count,
						"2":`<select class="form-control form-control-select2 dt-ing-${count} selectIng" data-live-search="true" id="selectDetailMatrialIng_${count}" data-count="${count}">
										<option value="">Select Item</option>
										${showMatrialDetailDataIng(elementSelect)}
									</select>`,
						"3":"",
						"4":`<input type="text" class="form-control uom-ing" id="uomCostingIng_${count}" value="" style="width:90px" autocomplete="off">`,
						"5":`<input type="text" class="form-control cost-ing" id="costCostingIng_${count}" value="" style="width:90px" autocomplete="off">`,
						"6":"",
						"7":""
						}).draw();
					count++;
				} else {
					alert('Silahkan Pilih Material Grup');
					return false;
				}

				tbody = $("#tblItemIngredients tbody");
				tbody.on('change','.selectIng', function(){
					tr = $(this).closest('tr');
					no = tr[0].rowIndex;
					id = $('.dt-ing-'+no+' option:selected').attr('rel');
					setValueTableIngredients(id,no);
				});
				tbody.on('change','.qty-ing', function(){
					tr = $(this).closest('tr');
					no = tr[0].rowIndex;
					setTotalCostIng($(this).val(),no);
					setProdCostPercentage($('#productSellPrice').val());
				});
				tbody.on('change','.cost-ing', function(){
					tr = $(this).closest('tr');
					no = tr[0].rowIndex;
					setTotalCostByPriceIng($(this).val(),no);
					setProdCostPercentage($('#productSellPrice').val());
				});
			}

			function showMatrialDetailDataIng(selectTable){
				const select = selectTable ? selectTable : $('#matrialGroupIngredients');
				let itmGrp = $('#itemGroupIng option:selected').val();
				$.ajax({
					url: "<?php echo site_url('transaksi1/productcosting/addItemRow');?>",
					data: {
						itmGrp:itmGrp
					},
					type: "POST",
					success:function(res) {
						optData = JSON.parse(res);
						optData.forEach((val)=>{						
							$("<option />", {value:val.MATNR, text:val.MATNR+' - '+val.MAKTX, rel:val.MATNR}).appendTo(select);
						})
						$("<option />", {value:'-', text:'other', rel:'-'}).appendTo(select);
					}
				});	
			}

			function setValueTableIngredients(id,no){
				let tbodyItemIngredientsRows = document.getElementById("tblItemIngredients").rows[no].cells;
				if (id == '-') {
					tbodyItemIngredientsRows[3].innerHTML = `<input type="text" class="form-control" id="productNameIng_${no}" value="" style="width:300px" autocomplete="off">`;
					tbodyItemIngredientsRows[4].innerHTML = `<input type="text" class="form-control uom-ing" id="uomCostingIng_${no}" value="" style="width:90px" autocomplete="off">`;
					tbodyItemIngredientsRows[5].innerHTML = `<input type="text" class="form-control cost-ing" id="costCostingIng_${no}" value="" style="width:90px" autocomplete="off">`;
				} else {
					$.post(
						"<?php echo site_url('transaksi1/productcosting/getdataDetailMaterialSelect')?>",{ MATNR:id },(res)=>{
							matSelect = JSON.parse(res);
							tbodyItemIngredientsRows[3].innerHTML = matSelect.data.MAKTX;
							tbodyItemIngredientsRows[4].innerHTML = matSelect.data.UNIT1;
							tbodyItemIngredientsRows[5].innerHTML = matSelect.dataLast.LastPrice == ".000000" ? "0.0000" : parseFloat(matSelect.dataLast.LastPrice).toLocaleString(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4});
						}
					)
				}
			}

			function setTotalCostIng(qty,no){
				let docStatus = $('#docStatus').val();
				let tbodyItemIngredientsRows = document.getElementById("tblItemIngredients").rows[no].cells;
				let itemCodeSelected = ((docStatus == 'Existing' && tbodyItemIngredientsRows[2].children[0]) || tbodyItemIngredientsRows[2].children[0]) ? tbodyItemIngredientsRows[2].children[0].value : tbodyItemIngredientsRows[2].innerHTML;
				let lastPrice = itemCodeSelected == '-' ? tbodyItemIngredientsRows[5].children[0].value.replace(',','').replace(',','') : tbodyItemIngredientsRows[5].innerHTML.replace(',','').replace(',','');
				tbodyItemIngredientsRows[7].innerHTML = (parseFloat(lastPrice) * parseFloat(qty)).toLocaleString(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4});
				setTotalFoodCost();
				setProdCostPercentage($('#productSellPrice').val());
			}
			
			function setTotalCostByPriceIng(price,no){
				let tbodyItemIngredientsRows = document.getElementById("tblItemIngredients").rows[no].cells;
				let qty = tbodyItemIngredientsRows[6].children[1].value ? tbodyItemIngredientsRows[6].children[1].value : 0;
				tbodyItemIngredientsRows[7].innerHTML = (parseFloat(price) * parseFloat(qty)).toLocaleString(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4});
				setTotalFoodCost();
				setProdCostPercentage($('#productSellPrice').val());
			}

			function setFoodCostItem(){
				let tableIng = $("#tblItemIngredients tbody");
				tableIng.find('tr').each(function(i, el){
					let td = $(this).find('td');
					td.eq(7).text(parseFloat(td.eq(5).text().replace(',','').replace(',','') * td.eq(6).find('input:text').val()).toLocaleString(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4}));
				});
				setTotalFoodCost();
				setProdCostPercentage($('#productSellPrice').val());
			}

			function setTotalFoodCost(){
				let tableIng = $("#tblItemIngredients tbody");
				let totCost = 0;
				tableIng.find('tr').each(function(i, el){
					let td = $(this).find('td');
					totCost += parseFloat(td.eq(7).text() ? td.eq(7).text().replace(',','').replace(',','') : 0);
					$('#totAllIngCost').text(totCost.toLocaleString(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4}));
				});
				setQFactor();
			}
			
			function onAddrowItemPackaging(){
				let getTable = $("#tblItemPackaging").DataTable();
				count = getTable.rows().count() + 1;
				let elementSelect = document.getElementsByClassName(`dt-pack-${count}`);
				let itmGrp = $('#itemGroupPack option:selected').val();
				if (itmGrp) {
					getTable.row.add({
						"0":"",
						"1":count,
						"2":`<select class="form-control form-control-select2 dt-pack-${count} selectPack" data-live-search="true" id="selectDetailMatrialPack_${count}" data-count="${count}">
										<option value="">Select Item</option>
										${showMatrialDetailDataPack(elementSelect)}
									</select>`,
						"3":"",
						"4":`<input type="text" class="form-control uom-pack" id="uomCostingPack_${count}" value="" style="width:90px" autocomplete="off">`,
						"5":`<input type="text" class="form-control cost-pack" id="costCostingPack_${count}" value="" style="width:90px" autocomplete="off">`,
						"6":"",
						"7":""
						}).draw();
					count++;
				} else {
					alert('Silahkan Pilih Material Grup');
					return false;
				}

				tbody = $("#tblItemPackaging tbody");
				tbody.on('change','.selectPack', function(){
					tr = $(this).closest('tr');
					no = tr[0].rowIndex;
					id = $('.dt-pack-'+no+' option:selected').attr('rel');
					setValueTablePackaging(id,no);
				});
				tbody.on('change','.qty-pack', function(){
					tr = $(this).closest('tr');
					no = tr[0].rowIndex;
					setTotalCostPack($(this).val(),no);
					setProdCostPercentage($('#productSellPrice').val());
				});
				tbody.on('change','.cost-pack', function(){
					tr = $(this).closest('tr');
					no = tr[0].rowIndex;
					setTotalCostByPricePack($(this).val(),no);
					setProdCostPercentage($('#productSellPrice').val());
				});
			}

			function showMatrialDetailDataPack(selectTable){
				const select = selectTable ? selectTable : $('#matrialGroupPackaging');
				let itmGrp = $('#itemGroupPack option:selected').val();
				$.ajax({
					url: "<?php echo site_url('transaksi1/productcosting/addItemRow');?>",
					data: {
						itmGrp:itmGrp
					},
					type: "POST",
					success:function(res) {
						optData = JSON.parse(res);
						optData.forEach((val)=>{						
							$("<option />", {value:val.MATNR, text:val.MATNR+' - '+val.MAKTX, rel:val.MATNR}).appendTo(select);
						})
						$("<option />", {value:'-', text:'other', rel:'-'}).appendTo(select);
					}
				});			
			}

			function setValueTablePackaging(id,no){
				let tbodyItemPackagingRows = document.getElementById("tblItemPackaging").rows[no].cells;
				if (id == '-') {
					tbodyItemPackagingRows[3].innerHTML = `<input type="text" class="form-control" id="productNamePack_${no}" value="" style="width:300px" autocomplete="off">`;
					tbodyItemPackagingRows[4].innerHTML = `<input type="text" class="form-control uom-pack" id="uomCostingPack_${no}" value="" style="width:90px" autocomplete="off">`;
					tbodyItemPackagingRows[5].innerHTML = `<input type="text" class="form-control cost-pack" id="costCostingPack_${no}" value="" style="width:90px" autocomplete="off">`;
				} else {
					$.post(
						"<?php echo site_url('transaksi1/productcosting/getdataDetailMaterialSelect')?>",{ MATNR:id },(res)=>{
							matSelect = JSON.parse(res);
							tbodyItemPackagingRows[3].innerHTML = matSelect.data.MAKTX;
							tbodyItemPackagingRows[4].innerHTML = matSelect.data.UNIT1;
							tbodyItemPackagingRows[5].innerHTML = matSelect.dataLast.LastPrice == ".000000" ? "0.0000" : parseFloat(matSelect.dataLast.LastPrice).toLocaleString(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4});
						}
					)
				}
			}

			function setTotalCostPack(qty,no){
				let docStatus = $('#docStatus').val();
				let tbodyItemPackagingRows = document.getElementById("tblItemPackaging").rows[no].cells;
				let itemCodeSelected = ((docStatus == 'Existing' && tbodyItemPackagingRows[2].children[0]) || tbodyItemPackagingRows[2].children[0]) ? tbodyItemPackagingRows[2].children[0].value : tbodyItemPackagingRows[2].innerHTML;
				let lastPrice = itemCodeSelected == '-' ? tbodyItemPackagingRows[5].children[0].value.replace(',','').replace(',','') : tbodyItemPackagingRows[5].innerHTML.replace(',','').replace(',','');
				tbodyItemPackagingRows[7].innerHTML = (parseFloat(lastPrice)*parseFloat(qty)).toLocaleString(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4});
				setTotalMaterialCost();
				setProdCostPercentage($('#productSellPrice').val());
			}
			
			function setTotalCostByPricePack(price,no){
				let tbodyItemPackagingRows = document.getElementById("tblItemPackaging").rows[no].cells;
				let qty = tbodyItemPackagingRows[6].children[1].value ? tbodyItemPackagingRows[6].children[1].value : 0;
				tbodyItemPackagingRows[7].innerHTML = (parseFloat(price)*parseFloat(qty)).toLocaleString(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4});
				setTotalMaterialCost();
				setProdCostPercentage($('#productSellPrice').val());
			}

			function setMaterialCostItem(){
				let tablePack = $("#tblItemPackaging tbody");
				let tblItemPackagingCountRow = $('#tblItemPackaging > tbody tr');
				tablePack.find('tr').each(function(i, el){
					let tdPack = $(this).find('td');
					console.log(tblItemPackagingCountRow.length)
					console.log(tdPack.eq(2).has('select'))
					console.log(tdPack.eq(2).has('select').length)
					console.log(tdPack.eq(2).text())
					if (tblItemPackagingCountRow.length > 0 && tblItemPackagingCountRow.text() != 'No data available in table') {
						if (tdPack.eq(2).text() || (tdPack.eq(2).has('select').length > 0 && tdPack.eq(2).find('select option:selected').val())) {
							tdPack.eq(7).text(parseFloat(tdPack.eq(5).text().replace(',','').replace(',','') * tdPack.eq(6).find('input:text').val()).toLocaleString(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4}));
						}
					}
				});
				setTotalMaterialCost();
				setProdCostPercentage($('#productSellPrice').val());
			}

			function setTotalMaterialCost(){
				let tablePack = $("#tblItemPackaging tbody");
				let totCost = 0;
				tablePack.find('tr').each(function(i, el){
					let td = $(this).find('td');
					totCost += parseFloat(td.eq(7).text() ? td.eq(7).text().replace(',','').replace(',','') : 0);
					$('#totAllPackCost').text(totCost.toLocaleString(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4}));
				});
				setQFactor();
			}

			function setQFactor(){
				let totFood = parseFloat($('#totAllIngCost').text().replace(',','').replace(',',''));
				let totMaterial = parseFloat($('#totAllPackCost').text().replace(',','').replace(',',''));
				let qFactorSAP = parseFloat($("#qFactorSAP").val()) * (1/100);
				let qFactor = qFactorSAP * (totFood + totMaterial);
				$('#qFactorResult').text(qFactor.toLocaleString(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4}));
				setTotalProdCost();
			}

			function setTotalProdCost(){
				let totFood = parseFloat($('#totAllIngCost').text().replace(',','').replace(',',''));
				let totMaterial = parseFloat($('#totAllPackCost').text().replace(',','').replace(',',''));
				let qFactorResult = parseFloat($('#qFactorResult').text().replace(',','').replace(',',''));
				let result = (totFood + totMaterial + qFactorResult) + (0.1 * (totFood + totMaterial + qFactorResult))
				$('#totProdCost').text(result.toLocaleString(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4}));
				setTotalProdCostDivQtyProduct();
			}

			function setTotalProdCostDivQtyProduct(){
				let productQty = $('#productQty').val() ? $('#productQty').val() : 0;
				let totProdCost = parseFloat($('#totProdCost').text().replace(',','').replace(',',''));
				let result = totProdCost / parseFloat(productQty);
				$('#totProdCostDivQtyProd').text(result ? result.toLocaleString(undefined, {minimumFractionDigits: 4, maximumFractionDigits: 4}) : 0);
			}

			function setProdCostPercentage(price){
				let pricePB1 = parseFloat(price ? price.replace(',','').replace(',','') : 0) / (110/100);
				let totProdCost = parseFloat($('#totProdCost').text().replace(',','').replace(',',''));
				let percentage = (totProdCost / pricePB1) * 100;

				$('#percentageCosting').text(`${$('#productType').val() == 'Finish Goods' ? (percentage ? percentage.toFixed(4) : 0) : 0} %`);
				setPercentageColor();
			}

			function setPercentageColor(){
				let percentageCost = $('#percentageCosting').text().split(' ');
				let min = parseFloat($("#minCostSAP").val()) * (1/100);
				let max = parseFloat($("#maxCostSAP").val()) * (1/100);

				if ($('#percentageCosting').text() == '0 %' && $('#productType').val() == 'Finish Goods') {
					$('#after-submit').hide();
				} else {
					$('#after-submit').show();
				}
				
				if (parseFloat(percentageCost[0] / 100) > max) {
					$('#indicatorCosting').text('Product Cost above Threshold');
					$('#indicatorCosting').css('background-color','red');
					$('#indicatorCosting').css('color','black');
				} else if (parseFloat(percentageCost[0] / 100) < min) {
					$('#indicatorCosting').text('Product Cost below Threshold');
					$('#indicatorCosting').css('background-color','yellow');
					$('#indicatorCosting').css('color','black');
				} else {
					$('#indicatorCosting').text('Product Cost within Threshold, Ok to continue');
					$('#indicatorCosting').css('background-color','green');
					$('#indicatorCosting').css('color','white');
				}

			}

			function addDatadb(id_approve){
				let idDoc = $('#idProdCost').val();
				let categoryCode = $('#category option:selected').val();
				let categoryName = $('#category option:selected').text();
				let categoryQF = $('#qFactorSAP').val();
				let categoryMinCost = $('#minCostSAP').val();
				let categoryMaxCost = $('#maxCostSAP').val();
				let productName = $('#productName').val();
				let productQty = $('#productQty').val();
				let productUom = $('#productUom').val();
				let productSellPrice = $('#productSellPrice').val().replace(',','').replace(',','');
				let productQFactor = $('#qFactorResult').text().replace(',','').replace(',','');
				let productResult = $('#totProdCost').text().replace(',','').replace(',','');
				let productPercentage = $('#percentageCosting').text().split(' ');
				let productResultDivQtyProd = $('#totProdCostDivQtyProd').text().replace(',','').replace(',','');
				let approve = id_approve;

				let tblItemIngredients = $('#tblItemIngredients > tbody');
				let tblItemPackaging = $('#tblItemPackaging > tbody');
				let tblItemPackagingCountRow = $('#tblItemPackaging > tbody tr');
				let matrialNo = [];
				let matrialDesc = [];
				let itemType = [];
				let itemQty = [];
				let itemUom = [];
				let itemCost = [];
				let validasi = true;
				let dataValidasi = [];
				let errorMesseges = [];
				tblItemIngredients.find('tr').each(function(i, el){
					let tdIng = $(this).find('td');
					matrialNo.push(tdIng.eq(2).has('select').length > 0 ? tdIng.eq(2).find('select option:selected').val() : tdIng.eq(2).text());
					matrialDesc.push(tdIng.eq(3).children().length == 0 ? tdIng.eq(3).text() : tdIng.eq(3).children(0).val()); 
					itemUom.push(tdIng.eq(4).has('input:text').length > 0 ? tdIng.eq(4).find('input').val() : tdIng.eq(4).text());	
					itemCost.push(tdIng.eq(5).has('input:text').length > 0 ? tdIng.eq(5).find('input').val() : tdIng.eq(5).text());
					itemQty.push(tdIng.eq(6).find('input:text').val());
					itemType.push(tdIng.eq(6).find('input:hidden').val());
					if(tdIng.eq(6).find('input:text').val() == ''){
						dataValidasi.push(tdIng.eq(2).has('select').length > 0 ? tdIng.eq(2).find('select option:selected').val() : tdIng.eq(2).text());
						validasi = false;
					}
				});
				tblItemPackaging.find('tr').each(function(i, el){
					let tdPack = $(this).find('td');
					if (tblItemPackagingCountRow.length > 0 && tblItemPackagingCountRow.text() != 'No data available in table') {
						if (tdPack.eq(2).text() || (tdPack.eq(2).has('select').length > 0 && tdPack.eq(2).find('select option:selected').val())) {
							matrialNo.push(tdPack.eq(2).has('select').length > 0 ? tdPack.eq(2).find('select option:selected').val() : tdPack.eq(2).text());
							matrialDesc.push(tdPack.eq(3).children().length == 0 ? tdPack.eq(3).text() : tdPack.eq(3).children(0).val()); 
							itemUom.push(tdPack.eq(4).has('input:text').length > 0 ? tdPack.eq(4).find('input').val() : tdPack.eq(4).text());	
							itemCost.push(tdPack.eq(5).has('input:text').length > 0 ? tdPack.eq(5).find('input').val() : tdPack.eq(5).text());
							itemQty.push(tdPack.eq(6).find('input:text').val());
							itemType.push(tdPack.eq(6).find('input:hidden').val());
							if(tdPack.eq(6).find('input:text').val() == ''){
								dataValidasi.push(tdIng.eq(2).has('select').length > 0 ? tdIng.eq(2).find('select option:selected').val() : tdIng.eq(2).text());
								validasi = false;
							}
						}
					}
				});
				if(categoryCode == ''){
					errorMesseges.push('Category harus di pilih. \n');
				}
				if(productName.trim() == ''){
					errorMesseges.push('Product Name harus di isi. \n');
				}
				if(productQty.trim() == ''){
					errorMesseges.push('Product Qty harus di isi. \n');
				}
				if(productUom.trim() == ''){
					errorMesseges.push('Product UOM harus di isi. \n');
				}
				if(productSellPrice.trim() == ''){
					errorMesseges.push('Selling Price Product harus di isi. \n');
				}
				if(!validasi){
					errorMesseges.push('Quatity untuk Material Number '+dataValidasi.join()+' Tidak boleh Kosong, Harap isi Quantity');
				}
				if (errorMesseges.length > 0) {
					alert(errorMesseges.join(''));
					return false;
				}
				$('#load').show();
				$("#after-submit").addClass('after-submit');

				setTimeout(() => {
					$.post("<?php echo site_url('transaksi1/productcosting/updateData')?>",{
						id:idDoc,
						categoryCode:categoryCode,
						categoryName:categoryName,
						categoryQF:categoryQF,
						categoryMin:categoryMinCost,
						categoryMax:categoryMaxCost,
						productName:productName,
						productQty:productQty,
						productUom:productUom,
						productSellPrice:productSellPrice,
						productQFactor:productQFactor,
						productResult:productResult,
						productPercentage:productPercentage[0],
						productResultDivQtyProd:productResultDivQtyProd,
						approve:approve, 
						matrialNo:matrialNo, 
						matrialDesc:matrialDesc, 
						itemQty:itemQty, 
						itemUom:itemUom,
						itemCost:itemCost,
						itemType:itemType
					}, function(){
						$('#load').hide();
					})
					.done(function() {
						location.replace("<?php echo site_url('transaksi1/productcosting/')?>");
					})
					.fail(function(xhr, status) {
						alert(`Terjadi Error (${xhr.status} : ${xhr.statusText}), Silahkan Coba Lagi`);
						location.reload(true);
					});
				}, 600);
			}

			function reject(){
				let idDoc = $('#idProdCost').val();
				let reason = $('#reason').val();
				let status = $('#status').val();

				if (status == 'Approved' && reason.trim() == '') {
					alert('Alasan Tidak Boleh Kosong')
					return false;
				}

				$.post("<?php echo site_url('transaksi1/productcosting/reject')?>", {
					id:idDoc, reason:reason
				}, function(res){
					location.replace("<?php echo site_url('transaksi1/productcosting/')?>");
				}
				);
			}
		</script>
	</body>
</html>