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
                    <form enctype="multipart/form-data" method="post" id="formfile">
						<div class="card">
                        	<div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <fieldset>
                                            <legend class="font-weight-semibold"><i class="icon-reading mr-2"></i>Tambah Stock Opname</legend>
                                            <div class="form-group row">
												<label class="col-lg-3 col-form-label">ID Transaksi</label>
												<div class="col-lg-9">
													<input type="text" class="form-control" placeholder="ID Transaksi" readOnly>
												</div>
                                            </div>
                                            
                                            <div class="form-group row">
												<label class="col-lg-3 col-form-label">Stock Opname Number</label>
												<div class="col-lg-9">
													<input type="text" class="form-control" placeholder="(Auto Number after Posting to SAP)" readOnly>
												</div>
                                            </div>
                                            
                                            <div class="form-group row">
												<label class="col-lg-3 col-form-label">Outlet</label>
												<div class="col-lg-9">
													<input type="text" class="form-control" id="outlet" value="<?= $plant ?>" readOnly>
												</div>
                                            </div>
                                            
                                            <div class="form-group row" hidden>
												<label class="col-lg-3 col-form-label">Storage Location</label>
												<div class="col-lg-9">
													<input type="text" class="form-control" value="<?= $storage_location ?>" readOnly>
												</div>
                                            </div>
                                            
                                            <div class="form-group row">
												<label class="col-lg-3 col-form-label">Status</label>
												<div class="col-lg-9">
												<input type="hidden" class="form-control" value="1" id="status">
													<input type="text" class="form-control" value="Not Approved" readOnly>
												</div>
											</div>

                                           	<div class="form-group row" hidden>
												<label class="col-lg-3 col-form-label">Material Group</label>
												<div class="col-lg-9">
													<select class="form-control form-control-select2" data-live-search="true" id="materialGroup" name="materialGroup" onchange="showMatrialDetail(this.value)">
														<option value="">Select Item</option>
														<option value="all">All</option>
														<?php foreach($matrialGroup as $key=>$val):?>
															<option value="<?=$val['ItmsGrpNam']?>"><?=$val['ItmsGrpNam']?></option>
														<?php endforeach;?>
													</select>
												</div>
											</div>

                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Posting Date</label>
                                                <div class="col-lg-9 input-group date">
                                                    <input type="text" class="form-control" id="postDate" autocomplete="off">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1">
                                                            <i class="icon-calendar"></i>
                                                        </span>
                                                    </div>
                                                </div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">File upload</label>
												<div class="col-lg-9">
													<input type="file" name="file" id="file" class="file-input" data-show-preview="false">
													<span class="form-text text-muted">Download Template <a href="<?php echo base_url()?>transaksi1/stock/downloadExcel/"><strong>Here.</strong></a></span>
												</div>
											</div>

											<div class="text-right mb-3 after-upload" style="display:none">
                                                <button type="button" class="btn btn-primary" name="save" id="save" onclick="addDatadb()">Save <i class="icon-pencil5 ml-2"></i></button>
												<?php if ($this->auth->is_have_perm('auth_approve')) : ?>
												<button type="button" class="btn btn-success" name="approve" id="approve" onclick="addDatadb(2)">Approve<i class="icon-paperplane ml-2"></i></button>
												<?php endif; ?>
                                            </div>
											
                                        </fieldset>
                                    </div>
                                </div>
							</div>
						</div> 

						<div id="load" style="display:none"></div>

						<div class="text-center mb-3" id="loadOnSubmit" style="display:none"><h4>Please Wait, Data On Submit...</h4></div>

						<div class="card after-upload" style="display:none">
							<div class="card-body">
								<div class="row">
									<legend class="font-weight-semibold"><i class="icon-list mr-2"></i>List Item</legend>
									<div class="col-md-12" style="overflow: auto">
										<table class="table table-striped" id="tblWhole">
											<thead>
												<tr>
													<th>WHS Code</th>
													<th>Item Group</th>
													<th>Item Code</th>
													<th>Item Name</th>
													<th>On Hand</th>
													<th>UOM</th>
													<?php foreach($head as $key=>$val):?>
														<th><?=$val['Name']?></th>
													<?php endforeach;?>
													<th>Akumulasi</th>
												</tr>
											</thead>
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

			$("#deleteRecord").click(function(){
				let deleteidArr=[];
				$("input:checkbox[class=check_delete]:checked").each(function(){
					deleteidArr.push($(this).val());
				})

				// mengecek ckeckbox tercheck atau tidak
				if(deleteidArr.length > 0){
					var confirmDelete = confirm("Do you really want to Delete records?");
					if(confirmDelete == true){
						$("input:checked").each(function(){
							table.row($(this).closest("tr")).remove().draw();;
						});
					}
				}
				
			});

			checkcheckbox = () => {
				let totalChecked = 0;
				$(".check_delete").each(function(){
					if($(this).is(":checked")){
						totalChecked += 1;
					}
				});
			}

			const date = new Date();
			const today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
			var optSimple = {
				format: 'dd-mm-yyyy',
				todayHighlight: true,
				orientation: 'bottom right',
				autoclose: true
			};

			$('#postDate').datepicker(optSimple);
			$('#postDate').datepicker();
			
			$('#formfile').submit(function(e){
				e.preventDefault();
				$.ajax({
					url:"<?php echo site_url('transaksi1/stock/readFile');?>",
					type:"POST",
					data:new FormData(this),
					processData:false,
					contentType:false,
					cache:false,
					beforeSend: function() {
						$('#load').show();
					},
					success:function(res) {
						row = JSON.parse(res);
						if (row.error.length > 0) {
							alert('Terdapat Data yang Tidak Cocok, Silahkan Cek Kembali File Anda')
							return false;
						} else {
							table = $("#tblWhole").DataTable({
								"ordering":false,
								"paging":false,
								"data":row.data,
								"columns": [
									{"data":"whscode", "className":"dt-center"},
									{"data":"itmgrp", "className":"dt-center"},
									{"data":"itmcode", "className":"dt-center"},
									{"data":"itmname", "className":"dt-center"},
									{"data":"onhand", "className":"dt-center"},
									{"data":"uom", "className":"dt-center"},
									{"data":"qr1", "className":"dt-center"},
									{"data":"qr2", "className":"dt-center"},
									{"data":"qr3", "className":"dt-center"},
									{"data":"qr4", "className":"dt-center"},
									{"data":"qr5", "className":"dt-center"},
									{"data":"qr6", "className":"dt-center"},
									{"data":"qr7", "className":"dt-center"},
									{"data":"qr8", "className":"dt-center"},
									{"data":"akm", "className":"dt-center"}
								],
								drawCallback: function() {
									$('.form-control-select2').select2();
								}
							});
						}
					},
					complete: function() {
						$('#load').hide();
						$('.after-upload').show();
					}
				});	
			});

			$('.fileinput-remove-button').click(function(){
				location.reload(true);
			})
		});

		function addDatadb(id_approve=''){
			if($('#postDate').val() ==''){
				alert('Tanggal Posting harus di isi');
				return false;
			}
			const status= document.getElementById('status').value;
			const MatrialGroup= document.getElementById('materialGroup').value;
			const postDate= document.getElementById('postDate').value;
			const approve = id_approve;
			const tbodyTable = $('#tblWhole > tbody');
			let qr_row = [];
			let qr_row_temp = [];
			let itmGrpName =[];
			let matrialNo =[];
			let matrialDesc =[];
			let qty =[];
			let uom =[];
			let onhand =[];
			tbodyTable.find('tr').each(function(i, el){
				let td = $(this).find('td');
				itmGrpName.push(td.eq(1).text()); 
				matrialNo.push(td.eq(2).text()); 
				matrialDesc.push(td.eq(3).text());
				onhand.push(td.eq(4).text());
				uom.push(td.eq(5).text());
				qty.push(td.eq(14).text());
				qr_row_temp.push(('1|'+(td.eq(6).text()?td.eq(6).text():'0')),('2|'+(td.eq(7).text()?td.eq(7).text():'0')),('3|'+(td.eq(8).text()?td.eq(8).text():'0')),('4|'+(td.eq(9).text()?td.eq(9).text():'0')),('5|'+(td.eq(10).text()?td.eq(10).text():'0')),('6|'+(td.eq(11).text()?td.eq(11).text():'0')),('7|'+(td.eq(12).text()?td.eq(12).text():'0')),('8|'+(td.eq(13).text()?td.eq(13).text():'0')));
				let temp = qr_row_temp.map(() => qr_row_temp.splice(0,8));
				qr_row.push(temp[0]);
			})

			$('#load').show();

			setTimeout(() => {
				$.post("<?php echo site_url('transaksi1/stock/addData')?>", {
					appr: approve, matGroup:MatrialGroup, stts: status, postDate: postDate, detMatrialNo: matrialNo, detMatrialDesc: matrialDesc, detQty: qty, detUom: uom, OnHand:onhand, ItemGrp:itmGrpName, Qr:qr_row
				}, function(){
					$('#load').hide();
				})
				.done(function() {
					location.replace("<?php echo site_url('transaksi1/stock/')?>");
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