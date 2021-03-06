<?php if(!defined('BASEPATH')) exit('No direct script access allowed') ?>

<?php
switch($tp){

case 'periksa':

$Qdetail = $this->db->query("SELECT a.name, a.judul, a.latar_belakang, SUM(b.amount) AS nominal FROM proposal a JOIN proposal_dana b ON b.proposal_id=a.id WHERE a.id='$dx'"); $detail = $Qdetail->result_object();

/*$Qket = $this->db->query("SELECT value AS keterangan FROM proposal_checklist WHERE proposal_id='$dx' AND checklist_id='13'"); $ket = $Qket->result_object();

$Qket1 = $this->db->query("SELECT value AS keterangan FROM proposal_checklist WHERE proposal_id='$dx' AND checklist_id='14'"); $ket1 = $Qket1->result_object();
*/
//edit ferdi
$Qket = $this->db->query("SELECT value AS keterangan FROM pemeriksaan_tu WHERE id_proposal='$dx'"); $ket = $Qket->result_object();
//edit ferdi
$Qket1 = $this->db->query("SELECT value AS keterangan FROM pemeriksaan_walikota WHERE id_proposal='$dx'"); $ket1  = $Qket1->result_object();

$Qdisposisi = $this->db->query("SELECT path FROM proposal_disposisi WHERE id_proposal='$dx'"); $dis = $Qdisposisi->result_object();

$Qcek = $this->db->query("SELECT jenis_id FROM proposal WHERE id = '$dx'"); $cek = $Qcek->result_object();
?>

<div role="main" class="content-main" style="margin:120px 0 50px">
    <div class="wrapper-half">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- <h1 class="page-title page-title-border">Detail Pemeriksaan Proposal Hibah Bansos Masuk</h1> -->
        <?php
        if(isset($_SESSION['notify'])){
            echo '<div class="alert-bar alert-bar-'.$_SESSION['notify']['type'].'" style="width:100%">'.$_SESSION['notify']['message'].'</div>';
            unset($_SESSION['notify']);
        }
        ?>

        <form action="<?php echo base_url('process/skpd/periksa/'.$dx) ?>" method="post" class="form-check form-global" enctype="multipart/form-data" class="page-title page-title-border">
			<h1 class="page-title page-title-border">Pemeriksaan Proposal Hibah Bansos Hasil Seleksi Pertimbangan</h1>
   
			<p class="label">Nama (Individu atau Organisasi)</p>
            <p><?php echo $detail[0]->name ?></p>
            <p class="label">Judul Kegiatan</p>
            <p><?php echo $detail[0]->judul ?></p>
            <p class="label">Deskripsi Singkat Kegiatan</p>
            <p><?php echo $detail[0]->latar_belakang ?></p>
            <p class="label">Nominal yang Diajukan di Proposal</p>
            <p><?php echo 'Rp. '.number_format($detail[0]->nominal,0,",",".").',-' ?></p>
            <p class="label">Keterangan dari TU</p>
            <p><?php if(isset($ket[0]->keterangan)) echo $ket[0]->keterangan; else echo '-'; ?></p>
            <p class="label">Keterangan dari Walikota</p>
            <p><?php if(isset($ket1[0]->keterangan)) echo $ket1[0]->keterangan; else echo '-'; ?></p>
			<p class="label">File Disposisi</p>
			<p><?php if(isset($dis[0]->path)) echo '<a class="info" target="_blank" href="'.base_url('media/disposisi/'.$dis[0]->path).'">Download File</a>'; else echo '-';?></p>
            <!-- <p class="label">Keterangan dari Tim Pertimbangan</p>
            <p>OK</p> -->
			
			<div class="control-group">
                    <label class="control-label" for="">Upload Hasil Koreksi Usulan Dana Calon Penerima</label>
                    <div class="controls file">
                         <input type="file" name="disposisi" accept="application/pdf" required>
                    </div>
                </div>
			
            <h2></h2>
            
            <h3 style="color:#ec7404">Evaluasi Administrasi</h3>
            <ul class="category-list list-nostyle">
                <?php
                // $Qlist = $this->db->query("SELECT id, name FROM checklist WHERE proses = 'skpd' ORDER BY id ASC limit 1");

                //edit ferdi
                $Qlist = $this->db->query("SELECT id, name FROM v_skpd WHERE part = '1' ORDER BY id ASC");

                foreach($Qlist->result_object() as $list){
                    echo '<li>
                            <label class="checkbox">
                                <input type="checkbox" name="syarat1[]" value="'.$list->id.'">
                                '.$list->name.'
                            </label>
                        <li>';
                }
                ?>
            </ul>
            <h3 style="color:#ec7404">Evaluasi Teknis</h3>
			<ul class="category-list list-nostyle">
                <?php
				
				/*if($cek[0]->jenis_id == 2){
					 $Qlist = $this->db->query("SELECT id, name FROM checklist WHERE proses = 'skpd' and keterangan = 'non fisik' ORDER BY sequence ASC limit 12");
				}else{
					 $Qlist = $this->db->query("SELECT id, name FROM checklist WHERE proses = 'skpd' ORDER BY sequence ASC limit 1,13");
				}
				*/


                //edit ferdi
                if($cek[0]->jenis_id == 2){
                     $Qlist = $this->db->query("SELECT id, name FROM v_skpd WHERE part = '2' and keterangan = 'non fisik' ORDER BY sequence ASC");
                }else{
                     $Qlist = $this->db->query("SELECT id, name FROM v_skpd WHERE part = '2' ORDER BY sequence ASC");
                }
               
                
				$loop = 1;
                foreach($Qlist->result_object() as $list){
                    echo '<li>
                            <label class="checkbox">
                                <input type="checkbox" name="syarat2[]" value="'.$list->id.'">
                                '.$loop.'. '.$list->name.'
								
                            </label>
                        <li>';
						$loop = $loop + 1;
                }
                ?>
            </ul>
			
			<div class="col-wrapper clearfix">
                <h3 style="color:#ec7404">Pemberian Rekomendasi Dana</h3>
                <div class="control-group">
                    <label class="control-label radio-inline radio">
                        <input type="radio" name="beri" value="1">
                        Ya
                    </label>
                   <label class="control-label radio-inline radio">
                        <input type="radio" name="beri" value="0">
                        Tidak
                    </label>
                </div>
                <div class="control-group">
                <h3 style="color:#ec7404">Besar Rekomendasi Dana</h3>
                    <div class="controls" >
                        <input id="besar" type="text" placeholder="Rp" name="besar" onchange="changeFormat()">
                    </div>
                </div>
            </div>
			
			<script type="text/javascript">
				$(document).ready(function(){				
					$("div.control-group input[type=radio]").on('change',function(){
						var thelength = $("div.control-group input[type=radio]").length;
						//alert("ok");
						document.getElementById('besar').value='';
						});
					
						 });

                function changeFormat(){
                    var bilangan = document.getElementById('besar').value;
                    var number_string = bilangan.toString(),
                        sisa    = number_string.length % 3,
                        rupiah  = number_string.substr(0, sisa),
                        ribuan  = number_string.substr(sisa).match(/\d{3}/g);
                            
                    if (ribuan) {
                        separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }
                    document.getElementById('besar').value= rupiah;
                }
			</script>
            <h3 style="color:#ec7404">Keterangan</h3>
            <textarea rows="5" name="keterangan"></textarea>

            <?php
                $export = base_url('process/pdf/export/'.date('d M Y').'/4/'.$dx);
            ?>

            <div class="control-actions">
                <input type="hidden" name="user_id" value="<?php echo $_SESSION['sabilulungan']['uid']; ?>">
                <input type="hidden" name="role_id" value="<?php echo $_SESSION['sabilulungan']['role']; ?>">
                <input type="submit" name="lanjut" class="btn-red btn-plain btn" style="display:inline" value="Disposisi ke Tim Pertimbangan" />
                <input type="submit" name="tolak" class="btn-red btn-plain btn" style="display:inline" value="Ditolak" />
                <a target="_blank" <?php echo ' href="'.$export.'"'; ?> class="btn-orange btn-plain btn" style="display:inline">Cetak Formulir</a>
                <a href="<?php echo site_url('report') ?>" class="btn-grey btn-plain btn" style="display:inline">Kembali</a>
            </div>
        </form>
    </div>
</div>
<!-- content-main -->

<?php
break;

case 'edit':

$Qdetail = $this->db->query("SELECT a.name, a.judul, a.latar_belakang, SUM(b.amount) AS nominal FROM proposal a JOIN proposal_dana b ON b.proposal_id=a.id WHERE a.id='$dx'"); $detail = $Qdetail->result_object();

/*$Qket = $this->db->query("SELECT value AS keterangan FROM proposal_checklist WHERE proposal_id='$dx' AND checklist_id='13'"); $ket = $Qket->result_object();

$Qket1 = $this->db->query("SELECT value AS keterangan FROM proposal_checklist WHERE proposal_id='$dx' AND checklist_id='14'"); $ket1 = $Qket1->result_object();
*/
//edit ferdi
$Qket = $this->db->query("SELECT value AS keterangan FROM pemeriksaan_tu WHERE id_proposal='$dx'"); $ket = $Qket->result_object();
//edit ferdi
$Qket1 = $this->db->query("SELECT value AS keterangan FROM pemeriksaan_walikota WHERE id_proposal='$dx'"); $ket1  = $Qket1->result_object();

/*$Qedit = $this->db->query("SELECT checklist_id, value FROM proposal_checklist WHERE `proposal_id`='$dx' AND checklist_id IN (15,16)"); $edit = $Qedit->result_object();

$Qedit1 = $this->db->query("SELECT checklist_id, value FROM proposal_checklist WHERE `proposal_id`='$dx' AND checklist_id IN (17,25)"); $edit1 = $Qedit1->result_object();

$Qedit2 = $this->db->query("SELECT checklist_id, value FROM proposal_checklist WHERE `proposal_id`='$dx' AND checklist_id BETWEEN 18 AND 24");*/

$Qedit = $this->db->query("SELECT rekomendasi, value, keterangan FROM pemeriksaan_skpd WHERE `id_proposal`='$dx'");  $edit = $Qedit->result_object();

$Qkoreksi = $this->db->query("SELECT path FROM proposal_koreksi WHERE id_proposal='$dx'"); $kor = $Qkoreksi->result_object();
?>

<div role="main" class="content-main" style="margin:120px 0 50px">
    <div class="wrapper-half">
        <!-- <h1 class="page-title page-title-border">Detail Pemeriksaan Proposal Hibah Bansos Masuk</h1> -->
        <?php
        if(isset($_SESSION['notify'])){
            echo '<div class="alert-bar alert-bar-'.$_SESSION['notify']['type'].'" style="width:100%">'.$_SESSION['notify']['message'].'</div>';
            unset($_SESSION['notify']);
        }
        ?>

        <form action="<?php echo base_url('process/skpd/edit/'.$dx) ?>" method="post" class="form-check form-global" enctype="multipart/form-data">
            <h1 class="page-title page-title-border">Pemeriksaan Proposal Hibah Bansos Hasil Seleksi Pertimbangan</h1>
            <p class="label">Nama (Individu atau Organisasi)</p>
            <p><?php echo $detail[0]->name ?></p>
            <p class="label">Judul Kegiatan</p>
            <p><?php echo $detail[0]->judul ?></p>
            <p class="label">Deskripsi Singkat Kegiatan</p>
            <p><?php echo $detail[0]->latar_belakang ?></p>
            <p class="label">Nominal yang Diajukan di Proposal</p>
            <p><?php echo 'Rp. '.number_format($detail[0]->nominal,0,",",".").',-' ?></p>
            <p class="label">Keterangan dari TU</p>
            <p><?php if(isset($ket[0]->keterangan)) echo $ket[0]->keterangan; else echo '-'; ?></p>
            <p class="label">Keterangan dari Walikota</p>
            <p><?php if(isset($ket1[0]->keterangan)) echo $ket1[0]->keterangan; else echo '-'; ?></p>
			<p class="label">File Exist</p>
			<p><?php if(isset($kor[0]->path)) echo '<a class="info" target="_blank" href="'.base_url('media/proposal_koreksi/'.$kor[0]->path).'">Download File</a>'; else echo '-';?></p>
			<div class="control-group">
                    <label class="control-label" for="">Update Hasil Koreksi Usulan Dana Calon Penerima</label>
                    <div class="controls file">
                        <input type="file" name="koreksi_usulan" accept="application/pdf" required>
                    </div>
            </div>
            <!-- <p class="label">Keterangan dari Tim Pertimbangan</p>
            <p>OK</p> -->
            <h2></h2>
            <div class="col-wrapper clearfix">
                <h3 style="color:#ec7404">Pemberian Rekomendasi Dana</h3>
                <div class="control-group">
                    <label class="control-label radio-inline radio">
                        <input type="radio" name="beri" value="1" <?php if($edit[0]->rekomendasi==1) echo " checked"; ?>>
                        Ya
                    </label>
                   <label class="control-label radio-inline radio">
                        <input type="radio" name="beri" value="0" <?php if($edit[0]->rekomendasi==0) echo " checked"; ?>>
                        Tidak
                    </label>
                </div>



                <div class="control-group">
                <h3 style="color:#ec7404">Besar Rekomendasi Dana</h3>
                    <div class="controls">
                        <input id="besar" type="text" placeholder="Rp" name="besar" onchange="changeFormat()" value="<?php if(isset($edit[0]->value)) echo $edit[0]->value; ?>">
                    </div>
                </div>
            </div>
            <script type="text/javascript">
				$(document).ready(function(){				
					$("div.control-group input[type=radio]").on('change',function(){
						var thelength = $("div.control-group input[type=radio]").length;
						//alert("ok");
						document.getElementById('besar').value='';
						});
					
						 });

                 function changeFormat(){
                    var bilangan = document.getElementById('besar').value;
                    var number_string = bilangan.toString(),
                        sisa    = number_string.length % 3,
                        rupiah  = number_string.substr(0, sisa),
                        ribuan  = number_string.substr(sisa).match(/\d{3}/g);
                            
                    if (ribuan) {
                        separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }
                    document.getElementById('besar').value= rupiah;
                }
				</script>
            <h3 style="color:#ec7404">Evaluasi Administrasi</h3>
            <ul class="category-list list-nostyle">
                <?php
                $Qlist = $this->db->query("SELECT id, name FROM checklist WHERE proses = 'skpd' ORDER BY id ASC limit 1");

                foreach($Qlist->result_object() as $list){
                    echo '<li>
                            <label class="checkbox">
                                <input type="checkbox" name="syarat1[]" value="'.$list->id.'">
                                '.$list->name.'
                            </label>
                        <li>';
                }
                ?>
            </ul>
            <h3 style="color:#ec7404">Evaluasi Teknis</h3>
			<ul class="category-list list-nostyle">
                <?php
				
				/*if($cek[0]->jenis_id == 2){
                     $Qlist = $this->db->query("SELECT id, name FROM checklist WHERE proses = 'skpd' and keterangan = 'non fisik' ORDER BY sequence ASC limit 12");
                }else{
                     $Qlist = $this->db->query("SELECT id, name FROM checklist WHERE proses = 'skpd' ORDER BY sequence ASC limit 1,13");
                }
                */


                //edit ferdi
                if($cek[0]->jenis_id == 2){
                     $Qlist = $this->db->query("SELECT id, name FROM v_skpd WHERE part = '2' and keterangan = 'non fisik' ORDER BY sequence ASC");
                }else{
                     $Qlist = $this->db->query("SELECT id, name FROM v_skpd WHERE part = '2' ORDER BY sequence ASC");
                }
				
               
                
				$loop = 1;
                foreach($Qlist->result_object() as $list){
                    echo '<li>
                            <label class="checkbox">
                                <input type="checkbox" name="syarat2[]" value="'.$list->id.'">
                                '.$loop.'. '.$list->name.'
								
                            </label>
                        <li>';
						$loop = $loop + 1;
                }
                ?>
            </ul>
            <h3 style="color:#ec7404">Keterangan</h3>
            <textarea rows="5" name="keterangan"><?php if(isset($edit[0]->keterangan)) echo $edit[0]->keterangan; ?></textarea>
            <?php
                $export = base_url('process/pdf/export/'.date('d M Y').'/4/'.$dx);
            ?>
            <div class="control-actions">
                <input type="hidden" name="user_id" value="<?php echo $_SESSION['sabilulungan']['uid']; ?>">
                <input type="hidden" name="role_id" value="<?php echo $_SESSION['sabilulungan']['role']; ?>">
                <input type="submit" name="lanjut" class="btn-red btn-plain btn" style="display:inline" value="Simpan" />
                <a target="_blank" <?php echo ' href="'.$export.'"'; ?> class="btn-orange btn-plain btn" style="display:inline">Cetak Formulir</a>
                <!-- <input type="submit" name="tolak" class="btn-red btn-plain btn" style="display:inline" value="Ditolak" /> -->
                <a href="<?php echo site_url('report') ?>" class="btn-grey btn-plain btn" style="display:inline">Kembali</a>
            </div>
        </form>
    </div>
</div>
<!-- content-main -->

<?php
break;

}