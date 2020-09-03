<!DOCTYPE html>
<html lang="en">
	<head>
		<?php $this->load->view("_template/head.php")?>
		<style>
			.after-submit,
			.hide {
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
	</head>
	<body>
		<?php $this->load->view("_template/nav.php")?>
		<div class="page-content">
			<?php $this->load->view("_template/sidebar.php")?>
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
					<form action="#" method="POST">
						<input type="hidden" name="status" id="status" value="<?=$wo_header['status']?>">
						<div class="card">
							<div class="card-body">
								<div class="row">
									<div class="col-md-12">
										<fieldset>
											<legend class="font-weight-semibold"><i class="icon-reading mr-2"></i>Edit Produksi</legend>
											
											<div class="form-group row">
												<label class="col-lg-3 col-form-label">ID Transaksi</label>
												<div class="col-lg-9">
													<input type="text" class="form-control" readonly="" value="<?= $wo_header['id_prodpos_header']?>" id="id_wo_header" nama="id_wo_header">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Outlet</label>
												<div class="col-lg-9">
													<input type="text" class="form-control" readonly="" value="<?= $wo_header['plant']?>" id="wo_plant" nama="wo_plant">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Item Produksi</label>
												<div class="col-lg-9">
													<input type="text" class="form-control" readonly="" value="<?= $wo_header['kode_paket'].' - '.$wo_header['nama_paket']?>">
													<input type="hidden" value="<?= $wo_header['kode_paket']?>" id="kode_paket" nama="kode_paket">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-lg-3 col-form-label">QTY Produksi</label>
												<div class="col-lg-9">
													<input type="text" class="form-control" readonly="" value="<?= number_format($wo_header['qty_paket'],4,'.','')?>" id="qty_paket" nama="qty_paket">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-lg-3 col-form-label">UOM</label>
												<div class="col-lg-9">
													<input type="text" class="form-control" readonly="" value="<?= $wo_header['uom_paket']?>" id="uom_paket" nama="uom_paket">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Posting Date</label>
												<div class="col-lg-9 input-group date">
													<input type="text" class="form-control" id="postDate" value="<?= date("d-m-Y", strtotime($wo_header['posting_date']))?>" readonly="" id="posting_date" nama="posting_date">
													<div class="input-group-prepend">
														<span class="input-group-text" id="basic-addon1">
															<i class="icon-calendar"></i>
														</span>
													</div>
												</div>
											</div>

											<?php if($wo_header['status'] != "2"):?>
												<div class="text-right hide" id="after-submit">
													<button type="button" class="btn btn-primary" name="save" id="save" onclick="addDatadb()">Save <i class="icon-pencil5 ml-2"></i></button>
													<?php if ($this->auth->is_have_perm('auth_approve')) : ?>
													<button type="button" class="btn btn-success" name="approve" id="approve" onclick="addDatadb(2)" >Approve <i class="icon-paperplane ml-2" ></input></i>
													<?php endif; ?>
												</div>
											<?php endif;?>
										</fieldset>
									</div>
								</div>	
							</div>
						</div>                    
						<div id="load" style="display:none"></div>  
						<div class="card">
							<div class="card-header">
								<legend class="font-weight-semibold"><i class="icon-list mr-2"></i>List Item</legend>
							</div>
							<div class="card-body">
								<div class="col-md-12" style="overflow:auto">
									<table id="table-manajemen" class="table table-striped " style="width:100%">
										<thead>
											<tr>
												<th>No</th>
												<th>Material No</th>
												<th>Material Desc</th>
												<th>Quantity</th>
												<th>Uom</th>
												<th>On Hand</th>
											</tr>
										</thead>
										<tbody></tbody>
									</table>
								</div>
							</div>
						</div>
					</form>
				</div>
				<?php $this->load->view("_template/footer.php")?>
			</div>
		</div>
		<?php $this->load->view("_template/js.php")?>
		<script>
			
            $(document).ready(function(){
				let id_wo_header = $('#id_wo_header').val();
				let kode_paket = $('#kode_paket').val();
				let qty_paket = $('#qty_paket').val();
				let stts = $('#status').val();
				let Quantity = ''

				$.post("<?php echo site_url('transaksi1/wo/wo_header_uom');?>",{material_no: kode_paket},(data)=>{
					const value = JSON.parse(data);
					if (value.data) {
						if(value.data[0]['U_Locked'] == 'N'){
							$("#btnAddListItem").removeClass('hide');
						}
						Quantity = value.data[0]['Qauntity']
					} else {
						Quantity = 1
					}

					$('#table-manajemen').DataTable({
						"ordering":false,  "paging": false, "searching":true,
						"drawCallback": function() {
							$('.form-control-select2').select2();
						},
						"initComplete": function(settings, json) {
							$("#after-submit").removeClass('hide');
						},
						"ajax": {
							"url":"<?php echo site_url('transaksi1/wopos/showDetailEdit');?>",
							"data":{ 
								id: id_wo_header, 
								kodepaket:kode_paket,
								qtypaket:qty_paket,
								qtyDefault:Quantity
							},
							"type":"POST"
						},
						"columns": [
							{"data":"no", "className":"dt-center"},
							{"data":"material_no", "className":"dt-center"},
							{"data":"descolumn"},
							{"data":"qty", "className":"dt-center"},
							{"data":"uom", "className":"dt-center"},
							{"data":"OnHand", "className":"dt-center"}
						]
					});
				});

				tbody = $("#table-manajemen tbody");
				tbody.on('change','.descmat', function(){
					tr = $(this).closest('tr');
					no = tr[0].rowIndex;
					const qty = $("option:selected", this).attr("matqty");
					const matrial_no = $("option:selected", this).val();
					const rel = $("option:selected", this).attr("rel");
					const onHand = $("option:selected", this).attr("onhand");
					const minStock = $("option:selected", this).attr("minstock");
					const uOm = $("option:selected", this).attr("uOm");
					table = document.getElementById("table-manajemen").rows[no].cells;
					table[1].innerHTML = matrial_no;
					table[3].innerHTML = `<input type="text" id="editqty" class="form-control" value="${qty}" ${rel == "N" ? "readonly": ""}>`;
					table[4].innerHTML = uOm;
					table[5].innerHTML = onHand;
				});
            });

			function addDatadb(id_approve = ''){
						
				idWoHeader 		= $('#id_wo_header').val();
				kodePaket 		= $('#kode_paket').val();
				approve			= id_approve;

				table = $('#table-manajemen > tbody');
				let matrialNo =[];
				let matrialDesc =[];
				let qty =[];
				let uom =[];
				let onHand =[];
				let minStock =[];
				let outStandTot =[];
				let validasiQty = true;
				let dataValidasi = [];
				table.find('tr').each(function(i, el){
					let td = $(this).find('td');
					
					if(parseFloat(td.eq(3).find('input').val().trim(),10) > parseFloat(td.eq(5).text())){
						dataValidasi.push(td.eq(1).text());
						validasiQty = false;
					}
					matrialNo.push(td.eq(1).text()); 
					matrialDesc.push(td.eq(2).find('select option:selected').text().trim());
					qty.push(td.eq(3).find('input').val());
					uom.push(td.eq(4).text());	
					onHand.push(td.eq(5).text());	
					minStock.push('');	
					outStandTot.push('');
				});
				if(!validasiQty){
					alert('Material Number '+dataValidasi.join()+' Quatity Tidak boleh Lebih Besar dari OnHand');
					return false;
				}
				$('#load').show();
				$("#after-submit").addClass('after-submit');

				setTimeout(() => {
					$.post("<?php echo site_url('transaksi1/wopos/addUpdateData')?>",{
						id_wo_header:idWoHeader, kd_paket:kodePaket, approve:approve, matrialNo:matrialNo, matrialDesc:matrialDesc, qty:qty, uom:uom, onHand:onHand, minStock:minStock, outStandTot:outStandTot
					}, function(){
						$('#load').hide();
					})
					.done(function() {
						location.replace("<?php echo site_url('transaksi1/wopos/')?>");
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