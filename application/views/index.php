<!DOCTYPE html>
<?php
	
	$jagwarning=false;
	for($i=101; $i<=111; $i++){
	   if($data[$i] > 0) { 
		   $jagwarning=true;
			break; 
		}
	}

	if ($opname_header['freeze']){
		$freeze = $opname_header['freeze']['freeze'];
	} else {
		$freeze = 'N';
	}
?>
<html lang="en">
	<head>
		<?php  $this->load->view("_template/head.php")?>
	</head>
	<body>
	<?php  $this->load->view("_template/nav.php")?>
		<div class="page-content">
			<?php  $this->load->view("_template/sidebar.php")?>
			<div class="content-wrapper">
				<div class="content">

					<!-- Dashboard content -->
					<div class="row">
						<div class="col-xl-8"></div>

						<div class="col-xl-4">

							<!-- Daily sales -->
							<div class="card">
								<div class="card-header header-elements-inline" style="margin-bottom:-10px; display: block;">
									
									<div class="header-elements" style="position: relative;">
									<h4>Perhatian</h4>
										<span class="font-weight-bold text-danger-600 ml-2" style="position: absolute; right: 0;"><?= $tglterkini ?></span>
									</div>
									<div class="header-elements">
										<h5>Harap Segera Tindak Lanjuti</h5>
									</div>
									
								</div>

								<div class="table-responsive">
									<?php if($jagwarning):?>
									<table class="table text-nowrap">
										<thead>
											<tr>
												<th class="w-100">Data Baru</th>
												<th>Jumlah</th>
											</tr>
										</thead>
										<tbody>
											<?php for($i=101; $i<=104; $i++):?>
											<tr>
												<td>
												<?php if ($freeze=='N' && !$opname_header['ids']):?>
												<i class="icon-checkmark3 font-size-sm mr-1" style="color: #2196f3;"></i><a href="<?php echo site_url($link[$i])?>" class="font-size-sm mr-1"><?=$nama[$i]?> </a> 
												<?php else: ?>
												<i class="icon-checkmark3 font-size-sm mr-1" style="color: #2196f3;"></i><span><?=$nama[$i]?> </span> 
												<?php endif; ?>
												</td>
												<td>
													<h6 class="font-weight-semibold mb-0"><?=$data[$i]['Total']?></h6>
												</td>
											</tr>

											<?php endfor;?>
											<tr>
												<td>
												<?php if ($freeze=='N' && !$opname_header['ids']):?>
												<i class="icon-checkmark3 font-size-sm mr-1" style="color: #2196f3;"></i><a href="<?php echo site_url($link[105])?>" class="font-size-sm mr-1"><?=$nama[105]?> </a> 
												<?php else: ?>
												<i class="icon-checkmark3 font-size-sm mr-1" style="color: #2196f3;"></i><span><?=$nama[105]?> </span>
												<?php endif; ?>
												</td>
												<td>
													<h6 class="font-weight-semibold mb-0"><?=$data[105]?></h6>
												</td>
											</tr>

										</tbody>
									</table>
											<?php endif;?>
								</div>
							</div>
							<!-- /daily sales -->

						</div>
					</div>
					<!-- /dashboard content -->

				</div>
				<?php $this->load->view("_template/footer.php")?>
			</div>
		</div>
		<?php  $this->load->view("_template/js.php")?>
	</body>
</html>
