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
											<legend class="font-weight-semibold"><i class="icon-reading mr-2"></i>Product Costing</legend>
											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Document</label>
												<div class="col-lg-9">
													<select name="doc_status" class="form-control form-control-select2" data-live-search="true" id="docStatus">
														<option value="">Select Document</option>
														<option value="1">New</option>
														<option value="2">Existing</option>
													</select>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Category</label>
												<div class="col-lg-9">
													<select name="category" id="category" class="form-control form-control-select2" data-live-search="true" onchange="getDataForQFactorFormula(this.value)">
													<option value="">Select Category</option>
														<?php foreach($categories as $key=>$value){?>
															<option value="<?=$value['Code']?>" desc="<?=$value['Name']?>"><?=$value['Name']?></option>
														<?php };?>
													</select>
													<input type="hidden" id="qFactorSAP" value="0">
													<input type="hidden" id="minCostSAP" value="0">
													<input type="hidden" id="maxCostSAP" value="0">
												</div>
											</div>

											<div class="form-group row" id="existingCost" style="display: none;">
												<label class="col-lg-3 col-form-label">Existing Bom</label>
												<div class="col-lg-9">
													<select class="form-control form-control-select2" data-live-search="true" id="existingBom" name="existing_bom" onchange="getExistingBomData(this.value)">
														<option value="">Select Item</option>
														<?php foreach($existing_bom as $key=>$value){?>
															<option value="<?=$key?>" desc="<?=$value?>"><?=$value?></option>
														<?php } ?>
													</select>
													<p id="txtProductQtyDefault"></p>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Name</label>
												<div class="col-lg-9">
													<input type="text" class="form-control" name="product_name" id="productName">
												</div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Qty Produksi</label>
												<div class="col-lg-9">
													<input type="text" id="productQty" name="product_qty" class="form-control" onchange="multiplyingQtyItems_setTotalCost(this.value)">
													<input type="hidden" id="productQtyDefault" value="0">
												</div>
											</div>	

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">UOM</label>
												<div class="col-lg-9">
													<input type="text" id="productUom" name="product_uom" class="form-control">
												</div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Posting Date</label>
												<div class="col-lg-9 input-group date">
													<input type="text" class="form-control" id="postDate" readonly autocomplate="off">
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
													<input type="text" class="form-control" value="Not Approved" readOnly>
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Selling Price (include Tax)</label>
												<div class="col-lg-9">
													<input type="text" class="form-control" name="product_sell_price" id="productSellPrice" onchange="setProdCostPercentage(this.value)">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Product Costing</label>
												<div class="col-lg-9">
													<p class="mt-1"><span id="percentageCosting"></span> <span id="indicatorCosting"></span></p>
												</div>
											</div>

											<div class="text-right" id="after-submit" style="display: none;">
												<button type="button" class="btn btn-primary" name="save" id="save" onclick="addDatadb(1)">Save <i class="icon-pencil5 ml-2"></i></button>
												<?php if ($this->auth->is_have_perm('auth_approve')) : ?>
												<button type="button" class="btn btn-success" name="approve" id="approve" onclick="addDatadb(2)" >Approve <i class="icon-paperplane ml-2" ></input></i>
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
											</select>
										</div>
									</div>
									<div class="col-md-4 mb-2 after-doc" style="display: none;">
										<div class="text-right">
											<input type="button" class="btn btn-primary" value="Add" id="addTableIng" onclick="onAddrowItemIngredients()"> 
											<input type="button" value="Delete" class="btn btn-danger" id="deleteRecordIng"> 
										</div>
									</div>
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
											<tbody>
												<tr id="firstRowIng" style="display: none;">
													<td><input type="checkbox" class="check_delete_ing" id="recordIng"/></td>
													<td>1</td>
													<td width="35%">
														<select class="form-control form-control-select2 dt_1" data-live-search="true" id="matrialGroupIngredients" onchange="setValueTableIngredients(this.value,1)">
															<option value="">Select Item</option>
														</select>
													</td>
													<td width="35%"></td>
													<td><input type="text" class="form-control uom-ing" name="uom_costing" id="uomCostingIng_1" style="width:90px" autocomplete="off"></td>
													<td><input type="text" class="form-control cost-ing" name="cost_costing" id="costCostingIng_1" style="width:90px" autocomplete="off"></td>
													<td><input type="hidden" class="form-control ing" name="ing" id="ing_1" value="1"><input type="text" class="form-control" name="qty_costing" id="qtyCostingIng_1" style="width:90px" autocomplete="off" onchange="setTotalCostIng(this.value,1)"></td>
													<td></td>
												</tr>
											</tbody>
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
											</select>
										</div>
									</div>
									<div class="col-md-4 mb-2 after-doc" style="display: none;">
										<div class="text-right">
											<input type="button" class="btn btn-primary" value="Add" id="addTablePack" onclick="onAddrowItemPackaging()"> 
											<input type="button" value="Delete" class="btn btn-danger" id="deleteRecordPack"> 
										</div>
									</div>
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
											<tbody>
												<tr id="firstRowPack" style="display: none;">
													<td><input type="checkbox" class="check_delete_pack" id="recordPack"/></td>
													<td>1</td>
													<td width="35%">
														<select class="form-control form-control-select2 dt_1" data-live-search="true" id="matrialGroupPackaging" onchange="setValueTablePackaging(this.value,1)">
															<option value="">Select Item</option>
														</select>
													</td>
													<td width="35%"></td>
													<td><input type="text" class="form-control uom-pack" name="uom_costing" id="uomCostingPack_1" style="width:90px" autocomplete="off"></td>
													<td><input type="text" class="form-control cost-pack" name="cost_costing" id="costCostingPack_1" style="width:90px" autocomplete="off"></td>
													<td><input type="hidden" class="form-control pack" name="pack" id="pack_1" value="2"><input type="text" class="form-control" name="qty_costing" id="qtyCostingPack_1" style="width:90px" autocomplete="off" onchange="setTotalCostPack(this.value,1)"></td>
													<td></td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div> 
                    </form>
                                           
				</div>
				<?php  $this->load->view("_template/footer.php")?>
			</div>
		</div>
        <?php  $this->load->view("_template/js.php")?>
		<script>
			$(document).ready(function(){

				$('#docStatus').change(function(){
					let docStatus = $(this).val();
					if (docStatus == 2) {
						$('#existingCost').show();
						$('#firstRowIng').hide();
						$('#firstRowPack').hide();
					} else {
						$('#existingCost').hide();
						$('#firstRowIng').show();
						$('#firstRowPack').show();
					}
					$('.after-doc').show();
				});

				$("#tblItemIngredients").DataTable({
					"ordering":false,
					"paging":false,
					drawCallback: function() {
						$('.form-control-select2').select2();
					}
				});
				
				$("#tblItemPackaging").DataTable({
					"ordering":false,
					"paging":false,
					drawCallback: function() {
						$('.form-control-select2').select2();
					}
				});

				const date = new Date();
				const today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
				var optSimple = {
					format: 'dd-mm-yyyy',
					todayHighlight: true,
					orientation: 'bottom right',
					autoclose: true
				};
				$('#postDate').datepicker(optSimple);
				
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
				tbodyIng.on('change','.descmat', function(){
					trIng = $(this).closest('tr');
					noIng = trIng[0].rowIndex;
					let productQty = $('#productQty').val();
					let qtyIng = $("option:selected", this).attr("matqty");
					let matrialNoIng = $("option:selected", this).attr("matno");
					let uomIng = $("option:selected", this).attr("uOm");
					let lastPriceIng = $("option:selected", this).attr("lastprice");
					let tableIng = document.getElementById("tblItemIngredients").rows[noIng].cells;
					tableIng[2].innerHTML = matrialNoIng;
					tableIng[4].innerHTML = uomIng;
					tableIng[5].innerHTML = lastPriceIng;
					tableIng[6].innerHTML = `<input type="hidden" class="form-control" id="typeCosting_${noIng}" name="ing" value="1"><input type="text" name="qty_costing" class="form-control" value="${parseFloat(qtyIng * productQty)}" style="width:90px" autocomplete="off" onchange="setTotalCostIng(this.value,${noIng})">`;
					tableIng[7].innerHTML = parseFloat((qtyIng * productQty) * lastPriceIng);
				});
				let tbodyPack = $("#tblItemPackaging tbody");
				tbodyPack.on('change','.qty-pack', function(){
					let trPack = $(this).closest('tr');
					let noPack = trPack[0].rowIndex;
					setTotalCostPack($(this).val(),noPack);
					setProdCostPercentage($('#productSellPrice').val());
				});
				tbodyPack.on('change','.descmat', function(){
					trPack = $(this).closest('tr');
					noPack = trPack[0].rowIndex;
					let productQty = $('#productQty').val();
					let qtyPack = $("option:selected", this).attr("matqty");
					let matrialNoPack = $("option:selected", this).attr("matno");
					let uomPack = $("option:selected", this).attr("uOm");
					let lastPricePack = $("option:selected", this).attr("lastprice");
					let tablePack = document.getElementById("tblItemIngredients").rows[noPack].cells;
					tablePack[2].innerHTML = matrialNoPack;
					tablePack[4].innerHTML = uomPack;
					tablePack[5].innerHTML = lastPricePack;
					tablePack[6].innerHTML = `<input type="hidden" class="form-control" id="typeCosting_${noPack}" name="pack" value="2"><input type="text" name="qty_costing" class="form-control" value="${parseFloat(qtyPack * productQty)}" style="width:90px" autocomplete="off" onchange="setTotalCostPack(this.value,${noPack})">`;
					tablePack[7].innerHTML = parseFloat((qtyPack * productQtyPack) * lastPricePack);
				});

				$('#itemGroupIng').change(function(){
					showMatrialDetailDataIng();
				});
				
				$('#itemGroupPack').change(function(){
					showMatrialDetailDataPack();
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

			function getExistingBomData(){
				$.post("<?php echo site_url('transaksi1/productcosting/getExistingBomData');?>",{material_no: $('#existingBom option:selected').val()},(data)=>{
					const value = JSON.parse(data);
					$("#productQtyDefault").val(value.data[0]['Qauntity'].slice(0,-2));
					$("#productUom").val(value.data[0]['InvntryUom']);
					$("#txtProductQtyDefault").text(`Suggest Qty : ${value.data[0]['Qauntity'].slice(0,-2)}`);

					showOnExistingBomChoosen();
				});
			}

			function showOnExistingBomChoosen(){
				let existingBomCode = $('#existingBom option:selected').val();
				let qtyDefaultexistingBom = $("#productQtyDefault").val();

				$.ajax({
					url:"<?php echo site_url('transaksi1/productcosting/showDetailInput');?>",
					type:"POST",
					data:{ 
						kode_paket:existingBomCode,
						Qty:1,
						qtyDefault: qtyDefaultexistingBom
					},
					success:function(res) {
						row = JSON.parse(res);
						let getTableIng = $("#tblItemIngredients").DataTable();
						let getTablePack = $("#tblItemPackaging").DataTable();
						
						getTableIng.row(0).remove().draw();
						getTableIng.rows.add(row.data).draw();
						getTablePack.row(0).remove().draw();
						getTablePack.rows.add(row.data).draw();
						addClassesIntoTable();
					},
				});
			}

			function addClassesIntoTable(){
				let tableIng = $("#tblItemIngredients tbody");
				let tablePack = $("#tblItemPackaging tbody");
				tableIng.find('tr').each(function(i, el){
					let tdIng = $(this).find('td');
					tdIng.eq(0).find('input:checkbox').addClass('check_delete_ing');
					tdIng.eq(6).find('input:hidden').addClass('ing');
					tdIng.eq(6).find('input:hidden').val(1);
					tdIng.eq(6).find('input:text').addClass('qty-ing');
				});
				tablePack.find('tr').each(function(i, el){
					let tdPack = $(this).find('td');
					tdPack.eq(0).find('input:checkbox').addClass('check_delete_pack');
					tdPack.eq(6).find('input:hidden').addClass('pack');
					tdPack.eq(6).find('input:hidden').val(2);
					tdPack.eq(6).find('input:text').addClass('qty-pack');
				});
			}

			function multiplyingQtyItems_setTotalCost(productQty){
				if ($('#docStatus option:selected').val() == 2) {
					let tableIng = $("#tblItemIngredients tbody");
					let tablePack = $("#tblItemPackaging tbody");
					tableIng.find('tr').each(function(i, el){
						let tdIng = $(this).find('td');
						let costIng = parseFloat(tdIng.eq(5).text());
						tdIng.eq(6).find('input:text').val(parseFloat(productQty) * parseFloat(tdIng.eq(6).find('input:text').val()));
						tdIng.eq(7).text(parseFloat(tdIng.eq(6).find('input:text').val()) * costIng);
					});
					tablePack.find('tr').each(function(i, el){
						let tdPack = $(this).find('td');
						let costPack = parseFloat(tdPack.eq(5).text());
						tdPack.eq(6).find('input:text').val(parseFloat(productQty) * parseFloat(tdPack.eq(6).find('input:text').val()));
						tdPack.eq(7).text(parseFloat(tdPack.eq(6).find('input:text').val()) * costPack);
					});
					setTotalFoodCost();
					setTotalMaterialCost();
					setProdCostPercentage($('#productSellPrice').val());
				}
			}

			function onAddrowItemIngredients(){
				let getTable = $("#tblItemIngredients").DataTable();
				count = getTable.rows().count() + 1;
				let elementSelect = document.getElementsByClassName(`dt-ing-${count}`);
				let itmGrp = $('#itemGroupIng option:selected').val();
				if (itmGrp) {
					getTable.row.add({
						"0":`<input type="checkbox" class="check_delete_ing" id="chkIng_${count}" value="${count}">`,
						"1":count,
						"2":`<select class="form-control form-control-select2 dt-ing-${count} selectIng" data-live-search="true" id="selectDetailMatrialIng_${count}" data-count="${count}">
										<option value="">Select Item</option>
										${showMatrialDetailDataIng(elementSelect)}
									</select>`,
						"3":"",
						"4":`<input type="text" class="form-control uom-ing" id="uomCostingIng_${count}" value="" style="width:90px" autocomplete="off">`,
						"5":`<input type="text" class="form-control cost-ing" id="costCostingIng_${count}" value="" style="width:90px" autocomplete="off">`,
						"6":`<input type="hidden" class="form-control ing" name="ing" id="ing_${count}" value="1"><input type="text" class="form-control qty-ing" id="qtyCostingIng_${count}" value="" style="width:90px" autocomplete="off">`,
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
					tbodyItemIngredientsRows[3].innerHTML = `<input type="text" class="form-control" id="productNameIng_${no}" value="" style="width:100%" autocomplete="off">`;
					tbodyItemIngredientsRows[4].innerHTML = `<input type="text" class="form-control uom-ing" id="uomCostingIng_${no}" value="" style="width:90px" autocomplete="off">`;
					tbodyItemIngredientsRows[5].innerHTML = `<input type="text" class="form-control cost-ing" id="costCostingIng_${no}" value="" style="width:90px" autocomplete="off">`;
				} else {
					$.post(
						"<?php echo site_url('transaksi1/productcosting/getdataDetailMaterialSelect')?>",{ MATNR:id },(res)=>{
							matSelect = JSON.parse(res);
							tbodyItemIngredientsRows[3].innerHTML = matSelect.data.MAKTX;
							tbodyItemIngredientsRows[4].innerHTML = matSelect.data.UNIT1;
							tbodyItemIngredientsRows[5].innerHTML = matSelect.dataLast.LastPrice == ".000000" ? "0.0000" : matSelect.dataLast.LastPrice.slice(0, -2);
						}
					)
				}
			}

			function setTotalCostIng(qty,no){
				let docStatus = $('#docStatus option:selected').val();
				let tbodyItemIngredientsRows = document.getElementById("tblItemIngredients").rows[no].cells;
				let itemCodeSelected = ((docStatus == 2 && tbodyItemIngredientsRows[2].children[0]) || tbodyItemIngredientsRows[2].children[0]) ? tbodyItemIngredientsRows[2].children[0].value : tbodyItemIngredientsRows[2].innerHTML;
				let lastPrice = itemCodeSelected == '-' ? tbodyItemIngredientsRows[5].children[0].value : tbodyItemIngredientsRows[5].innerHTML;
				tbodyItemIngredientsRows[7].innerHTML = parseFloat(lastPrice) * parseFloat(qty);
				setTotalFoodCost();
				setProdCostPercentage($('#productSellPrice').val());
			}

			function setTotalFoodCost(){
				let tableIng = $("#tblItemIngredients tbody");
				let totCost = 0;
				tableIng.find('tr').each(function(i, el){
					let td = $(this).find('td');
					totCost += parseFloat(td.eq(7).text() ? td.eq(7).text() : 0);
					$('#totAllIngCost').text(totCost.toLocaleString());
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
						"0":`<input type="checkbox" class="check_delete_pack" id="chkPack_${count}" value="${count}">`,
						"1":count,
						"2":`<select class="form-control form-control-select2 dt-pack-${count} selectPack" data-live-search="true" id="selectDetailMatrialPack_${count}" data-count="${count}">
										<option value="">Select Item</option>
										${showMatrialDetailDataPack(elementSelect)}
									</select>`,
						"3":"",
						"4":`<input type="text" class="form-control uom-pack" id="uomCostingPack_${count}" value="" style="width:90px" autocomplete="off">`,
						"5":`<input type="text" class="form-control cost-pack" id="costCostingPack_${count}" value="" style="width:90px" autocomplete="off">`,
						"6":`<input type="hidden" class="form-control pack" name="pack" id="pack_${count}" value="2"><input type="text" class="form-control qty-pack" id="qtyCostingPack_${count}" value="" style="width:90px" autocomplete="off">`,
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
					tbodyItemPackagingRows[3].innerHTML = `<input type="text" class="form-control" id="productNamePack_${no}" value="" style="width:100%" autocomplete="off">`;
					tbodyItemPackagingRows[4].innerHTML = `<input type="text" class="form-control uom-pack" id="uomCostingPack_${no}" value="" style="width:90px" autocomplete="off">`;
					tbodyItemPackagingRows[5].innerHTML = `<input type="text" class="form-control cost-pack" id="costCostingPack_${no}" value="" style="width:90px" autocomplete="off">`;
				} else {
					$.post(
						"<?php echo site_url('transaksi1/productcosting/getdataDetailMaterialSelect')?>",{ MATNR:id },(res)=>{
							matSelect = JSON.parse(res);
							tbodyItemPackagingRows[3].innerHTML = matSelect.data.MAKTX;
							tbodyItemPackagingRows[4].innerHTML = matSelect.data.UNIT1;
							tbodyItemPackagingRows[5].innerHTML = matSelect.dataLast.LastPrice == ".000000" ? "0.0000" : matSelect.dataLast.LastPrice.slice(0, -2);
						}
					)
				}
			}

			function setTotalCostPack(qty,no){
				let docStatus = $('#docStatus option:selected').val();
				let tbodyItemPackagingRows = document.getElementById("tblItemPackaging").rows[no].cells;
				let itemCodeSelected = ((docStatus == 2 && tbodyItemPackagingRows[2].children[0]) || tbodyItemPackagingRows[2].children[0]) ? tbodyItemPackagingRows[2].children[0].value : tbodyItemPackagingRows[2].innerHTML;
				let lastPrice = itemCodeSelected == '-' ? tbodyItemPackagingRows[5].children[0].value : tbodyItemPackagingRows[5].innerHTML;
				tbodyItemPackagingRows[7].innerHTML = parseFloat(lastPrice)*parseFloat(qty);
				setTotalMaterialCost();
				setProdCostPercentage($('#productSellPrice').val());
			}

			function setTotalMaterialCost(){
				let tablePack = $("#tblItemPackaging tbody");
				let totCost = 0;
				tablePack.find('tr').each(function(i, el){
					let td = $(this).find('td');
					totCost += parseFloat(td.eq(7).text() ? td.eq(7).text() : 0);
					$('#totAllPackCost').text(totCost.toLocaleString());
				});
				setQFactor();
			}

			function setQFactor(){
				let totFood = parseFloat($('#totAllIngCost').text().replace(',','').replace(',',''));
				let totMaterial = parseFloat($('#totAllPackCost').text().replace(',','').replace(',',''));
				let qFactorSAP = parseFloat($("#qFactorSAP").val()) * (1/100);
				let qFactor = qFactorSAP * (totFood + totMaterial);
				$('#qFactorResult').text(qFactor.toLocaleString());
				setTotalProdCost();
			}

			function setTotalProdCost(){
				let totFood = parseFloat($('#totAllIngCost').text().replace(',','').replace(',',''));
				let totMaterial = parseFloat($('#totAllPackCost').text().replace(',','').replace(',',''));
				let qFactorResult = parseFloat($('#qFactorResult').text().replace(',','').replace(',',''));
				let result = (totFood + totMaterial + qFactorResult) + (0.1 * (totFood + totMaterial + qFactorResult))
				$('#totProdCost').text(result.toLocaleString());
			}

			function setProdCostPercentage(price){
				let pricePB1 = parseFloat(price ? price : 0) / (110/100);
				let totProdCost = parseFloat($('#totProdCost').text().replace(',','').replace(',',''));
				let percentage = (totProdCost / pricePB1) * 100;

				$('#percentageCosting').text(`${percentage.toFixed(4)} %`);
				setPercentageColor();
			}

			function setPercentageColor(){
				let totFood = parseFloat($('#totAllIngCost').text());
				let qFactorResult = parseFloat($('#qFactorResult').text());
				let percentageCost = $('#percentageCosting').text().split(' ');
				let min = parseFloat($("#minCostSAP").val()) * (1/100);
				let max = parseFloat($("#maxCostSAP").val()) * (1/100);

				if ($('#percentageCosting').text() == '0 %' && totFood == '0' && qFactorResult == '0') {
					$('#after-submit').hide();
				} else {
					$('#after-submit').show();
				}
				
				if (parseFloat(percentageCost[0] / 100) > max) {
					$('#indicatorCosting').text('Merah');
					$('#indicatorCosting').css('background-color','red');
					$('#indicatorCosting').css('color','black');
				} else if (parseFloat(percentageCost[0] / 100) < min) {
					$('#indicatorCosting').text('Kuning');
					$('#indicatorCosting').css('background-color','yellow');
					$('#indicatorCosting').css('color','black');
				} else {
					$('#indicatorCosting').text('Hijau');
					$('#indicatorCosting').css('background-color','green');
					$('#indicatorCosting').css('color','white');
				}

			}

			function addDatadb(id_approve){
				let docStatus = $('#docStatus option:selected').val();
				let categoryCode = $('#category option:selected').val();
				let categoryName = $('#category option:selected').text();
				let categoryQF = $('#qFactorSAP').val();
				let categoryMinCost = $('#minCostSAP').val();
				let categoryMaxCost = $('#maxCostSAP').val();
				let existingBomCode	= $('#existingBom option:selected').val();
				let existingBomName	= $('#existingBom option:selected').text().split(' - ');
				let productName = $('#productName').val();
				let productQty = $('#productQty').val();
				let productUom = $('#productUom').val();
				let productSellPrice = $('#productSellPrice').val();
				let productQFactor = $('#qFactorResult').text().replace(',','').replace(',','');
				let productResult = $('#totProdCost').text().replace(',','').replace(',','');
				let productPercentage = $('#percentageCosting').text().split(' ');
				let postDate = $('#postDate').val();
				let approve = id_approve;

				let tblItemIngredients = $('#tblItemIngredients > tbody');
				let tblItemPackaging = $('#tblItemPackaging > tbody');
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
						dataValidasi.push(tdIng.eq(2).find('select option:selected').val());
						validasi = false;
					}
				});
				tblItemPackaging.find('tr').each(function(i, el){
					let tdPack = $(this).find('td');
					if (i+1 > 0) {
						matrialNo.push(tdPack.eq(2).has('select').length > 0 ? tdPack.eq(2).find('select option:selected').val() : tdPack.eq(2).text());
						matrialDesc.push(tdPack.eq(3).children().length == 0 ? tdPack.eq(3).text() : tdPack.eq(3).children(0).val()); 
						itemUom.push(tdPack.eq(4).has('input:text').length > 0 ? tdPack.eq(4).find('input').val() : tdPack.eq(4).text());	
						itemCost.push(tdPack.eq(5).has('input:text').length > 0 ? tdPack.eq(5).find('input').val() : tdPack.eq(5).text());
						itemQty.push(tdPack.eq(6).find('input:text').val());
						itemType.push(tdPack.eq(6).find('input:hidden').val());
					}
				});
				if(docStatus == ''){
					errorMesseges.push('Document harus di pilih. \n');
				}
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
				if(postDate.trim() == ''){
					errorMesseges.push('Posting Date harus di isi. \n');
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
					$.post("<?php echo site_url('transaksi1/productcosting/addData')?>",{
						categoryCode:categoryCode,
						categoryName:categoryName,
						categoryQF:categoryQF,
						categoryMin:categoryMinCost,
						categoryMax:categoryMaxCost,
						existingBomCode:existingBomCode,
						existingBomName:existingBomName[1],
						productName:productName,
						productQty:productQty,
						productUom:productUom,
						productSellPrice:productSellPrice,
						productQFactor:productQFactor,
						productResult:productResult,
						productPercentage:productPercentage[0],
						postDate:postDate, 
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
		</script>
	</body>
</html>