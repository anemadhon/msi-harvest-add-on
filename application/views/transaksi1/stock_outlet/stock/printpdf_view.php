<?php
$SAP_MSI = $this->load->database('SAP_MSI', TRUE);
foreach($data as $row) 
  $plant=$row["plant"];
  $id=$row["id_user_approved"];
  $SAP_MSI->select('WhsName');
  $SAP_MSI->from('OWHS');
  $SAP_MSI->where('WhsCode',$plant);
  $query = $SAP_MSI->get();
  $temp = $query->result_array();

  $reck=$temp[0]['WhsName'];
?>
<style type="text/css">

.style5 {font-size: 10px}
.style7 {
	font-size: 28px;
	font-weight: bold;
}
.style8 {
  font-size: 9px;
  margin-left:100px;
}
.style10 {font-size: 24px}
.style12 {font-size: 18px}
.head {font-size:13px}
.m {margin:10px 10px 0 20px}
.space {margin:10px 10px 15px 20px}

</style>
<table width="300">
  <tr>
    <td width="650">
      <img src="<?php echo base_url('/files/');?>assets/images/logo.jpeg" alt="logo-harvest" width="270">
    </td>
    <td colspan="2" align="center"><span class="style7">STOCK OPNAME</span></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
  <tr>
    <td><strong>PT. Mount Scopus Indonesia</strong></td>
    <td>No.</td>
    <td>:&nbsp;<?php echo $row["opname_no"];?></td>
  </tr>
  <tr>
    <td>Plaza Simatupang Lt. 8 - 9</td>
    <td>Date</td>
    <td>:&nbsp;<?php echo date("d-m-Y",strtotime($row['created_date']));
	?></td>
  </tr>
  <tr>
    <td>Jl. T.B. Simatupang Kav 1S-1</td>
    <td>Outlet</td>
    <td>:&nbsp;<strong><?php echo $plant.' - '.$reck;?></strong></td>
  </tr>
  <tr>
    <td>Jakarta Selatan, 12310, Indonesia</td>
    <td></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Ph. +62 21 726 06680 / Fax. +62 21 727 971 59</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<p>&nbsp;</p>
<table style="border-collapse:collapse;" width="750" border="1" align="center">
  <tr class="head">
    <td width="20" align="center"><strong>No</strong></td>
    <td width="90" align="center"><strong>Item Group</strong></td>
    <td width="90" align="center"><strong>Item Code</strong></td>
    <td width="200" align="center"><strong>Item Name</strong></td>
    <td width="60" align="center"><strong>On Hand</strong></td>
    <td width="35" align="center"><strong>UOM</strong></td>
    <td width="46" align="center"><strong>Store</strong></td>
    <td width="47" align="center"><strong>Freezer</strong></td>
    <td width="47" align="center"><strong>Chiller</strong></td>
    <td width="50" align="center"><strong>Pastry</strong></td>
    <td width="50" align="center"><strong>Cookies Room</strong></td>
    <td width="50" align="center"><strong>Hot Kitchen</strong></td>
    <td width="35" align="center"><strong>Bar</strong></td>
    <td width="50" align="center"><strong>Cake Shop</strong></td>
    <td width="50" align="center"><strong>Total</strong></td>
    <td width="55" align="center"><strong>Variance</strong></td>
    
  </tr>
<?php
$no = 1;
foreach($data as $row1){
?>
  <!-- <tr>
    <td align="center"><?php echo $no++; ?></td>
    <td>&nbsp;<?php echo $row1["material_no"]; ?></td>
    <td>&nbsp;<?php echo $row1["material_desc"]; ?></td>
    <td align="right">&nbsp;<?php echo $row1["uom"]; ?>&nbsp;</td>
    <td align="right">&nbsp;<?php echo $row1["num"];?> &nbsp;</td>
    <td align="right"><?php $qty=$row1["requirement_qty"];echo substr($qty,0,-2); ?>&nbsp;</td>
    <td align="center"><?php echo $no++; ?></td>
    <td align="center"><?php echo $no++; ?></td>
    <td align="center"><?php echo $no++; ?></td>
    <td align="center"><?php echo $no++; ?></td>
    <td align="center"><?php echo $no++; ?></td>
    <td align="center"><?php echo $no++; ?></td>
    <td align="center"><?php echo $no++; ?></td>
    <td align="center"><?php echo $no++; ?></td>
    <td align="center"><?php echo $no++; ?></td>
    <td align="center"><?php echo $no++; ?></td>
  </tr> -->
<?php   
}
?>
</table>
<p>&nbsp;</p>
<table width="571" align="center">
  <tr>
    <td width="144" align="center">User / Admin SO</td>
    <td width="136" align="center">Area Manager</td>
    <td width="136" align="center">Regional Manager</td>
    <td width="135" align="center">???</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="center">(...)</td>
    <td align="center">(...)</td>
    <td align="center">(...)</td>
    <td align="center">(...)</td>
  </tr>
</table>
<!-- <p>&nbsp;</p>
<p>&nbsp;</p>
<div align="justify"></div>
<table width="640" align="center">
  <tr>
    <td width="10"><span class="style5">1.</span></td>
    <td width="618"><span class="style3 style5"> FILLOUT IN DUPLICATE RETAIL YELLOW COPY FOR YOURFILE</span></td>
  </tr>
  <tr>
    <td><span class="style5">2.</span></td>
    <td><span class="style3 style5"> DEPARTEMENT MANAGERS MUST SIGN PRIOR TO ORDERING</span></td>
  </tr>
  <tr>
    <td><span class="style5">3.</span></td>
    <td><span class="style3 style5"> RECEIVING - RED</span></td>
  </tr>
  <tr>
    <td><span class="style5">4.</span></td>
    <td><span class="style3 style5"> USER - BLUE</span></td>
  </tr>
</table>
<p class="style1">&nbsp;</p>
<h5 class="style1">&nbsp;</h5> -->
