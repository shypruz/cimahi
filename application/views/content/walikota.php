<?php if(!defined('BASEPATH')) exit('No direct script access allowed') ?>

<?php
switch($tp){

case 'periksa':

$Qdetail = $this->db->query("SELECT a.name, a.judul, a.latar_belakang, SUM(b.amount) AS nominal FROM proposal a JOIN proposal_dana b ON b.proposal_id=a.id WHERE a.id='$dx'"); $detail = $Qdetail->result_object();

/*$Qket = $this->db->query("SELECT value AS keterangan FROM proposal_checklist WHERE proposal_id='$dx' AND checklist_id=13"); $ket = $Qket->result_object();*/
//edit ferdi
$Qket = $this->db->query("SELECT value AS keterangan FROM pemeriksaan_tu WHERE id_proposal='$dx'"); $ket = $Qket->result_object();
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
		
		
        <form action="<?php echo base_url('process/walikota/periksa/'.$dx) ?>" method="post" class="form-check form-global" enctype="multipart/form-data">
            <h1 class="page-title page-title-border">Pemeriksaan Proposal Hibah Bansos Hasil Seleksi TU</h1>
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
		

		<div class="control-group">
                    <label class="control-label" for="">Disposisi</label>
                    <div class="controls file">
                        <input type="file" name="disposisi" accept="application/pdf">
                    </div>
                </div>
				
		
		
            <h3 style="color:#ec7404">Keterangan</h3>
            <textarea rows="5" name="keterangan"></textarea>

            <div class="control-actions">
                <input type="hidden" name="user_id" value="<?php echo $_SESSION['sabilulungan']['uid']; ?>">
                <input type="hidden" name="role_id" value="<?php echo $_SESSION['sabilulungan']['role']; ?>">
                <input type="submit" name="lanjut" class="btn-red btn-plain btn" style="display:inline" value="Disposisi ke Tim Pertimbangan" />
                <!--<input type="submit" name="tolak" class="btn-red btn-plain btn" style="display:inline" value="Ditolak" /> -->
                <a href="<?php echo site_url('report') ?>" class="btn-grey btn-plain btn" style="display:inline">Kembali</a>
            </div>
        </form>
    </div>
</div>
<!-- content-main -->

<?php
break;

case 'setuju':
?>

<div role="main" class="content-main" style="margin:120px 0 50px">
    <div class="wrapper">
        <!-- <h1 class="page-title page-title-border">Detail Pemeriksaan Proposal Hibah Bansos Masuk</h1> -->
        <?php
        if(isset($_SESSION['notify'])){
            echo '<div class="alert-bar alert-bar-'.$_SESSION['notify']['type'].'" style="width:100%">'.$_SESSION['notify']['message'].'</div>';
            unset($_SESSION['notify']);
        }         

       $Qdetail = $this->db->query("SELECT a.time_entry AS awal, b.name AS skpd, c.time_entry AS akhir FROM proposal a JOIN skpd b ON b.id=a.skpd_id JOIN proposal_approval c ON c.proposal_id=a.id WHERE a.id='$dx' AND c.flow_id='6'"); $detail = $Qdetail->result_object(); 
		    
        ?>

        <form action="<?php echo base_url('process/walikota/setuju/'.$dx) ?>" method="post" class="form-check form-global">
            <h1 class="page-title page-title-border">Pemeriksaan Proposal Hibah Bansos Hasil Seleksi TU</h1>
            <p class="label">No DNC PBH</p>
            <p>DNC3</p>
            <p class="label">SKPD</p>
            <p><?php if(isset($detail[0]->skpd)) echo $detail[0]->skpd; else echo '-'; ?></p>
            <p class="label">Tanggal Awal</p>
            <p><?php if(isset($detail[0]->awal)) echo date('M d, Y. g:i a', strtotime($detail[0]->awal)); else echo '-'; ?></p>
            <p class="label">Tanggal Akhir</p>
            <p><?php if(isset($detail[0]->akhir)) echo date('M d, Y. g:i a', strtotime($detail[0]->akhir)); else echo '-'; ?></p>

            <table class="table-global">
                <thead>
                    <tr>
                        <th rowspan="2">No.</th>
                        <th rowspan="2">Nama Lengkap Calon Penerima</th>
                        <th rowspan="2">Alamat Lengkap</th>
                        <th rowspan="2">Rencana Penggunaan</th>
                        <th class="has-sub" colspan="3">Besaran Belanja Hibah (Rp)</th>
                        <th rowspan="2">Keterangan</th>
                    </tr>
                    <tr>
                        <th>Permohonan</th>
                        <th>Hasil Evaluasi</th>
                        <th>Pertimbangan TAPD</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $Qlist = $this->db->select("id, name, address, latar_belakang")->from('proposal')->where('id', $dx)->get();

                    if($Qlist->num_rows){
                        $i = 1;
                        foreach($Qlist->result_object() as $list){
                            $Qmohon = $this->db->query("SELECT SUM(amount) AS mohon FROM proposal_dana WHERE `proposal_id`='$list->id'"); $mohon = $Qmohon->result_object(); 

                            /*$Qbesar = $this->db->query("SELECT value FROM proposal_checklist WHERE `proposal_id`='$list->id' AND checklist_id IN (26,28,29)"); $besar = $Qbesar->result_object(); */


                            //edit ferdi
                            $Qbesar = $this->db->query("SELECT value FROM verifikasi_pertimbangan WHERE `id_proposal`='$list->id'"); $besar = $Qbesar->result_object(); 

                            $Qbesar1 = $this->db->query("SELECT value , keterangan FROM verifikasi_tapd WHERE `id_proposal`='$list->id'"); $besar1 = $Qbesar1->result_object();

                            echo '<tr>
                                    <td>'.$i.'</td>
                                    <td>'.$list->name.'</td>
                                    <td>'.$list->address.'</td>
                                    <td>'.$list->latar_belakang.'</td>
                                    <td>Rp. '.number_format($mohon[0]->mohon,0,",",".").',-</td>
                                    <td>'; if(isset($besar[0]->value)) echo 'Rp. '.number_format($besar[0]->value,0,",",".").',-'; else echo '-'; echo '</td>
                                    <td>'; if(isset($besar1[0]->value)) echo 'Rp. '.number_format($besar1[0]->value,0,",",".").',-'; else echo '-'; echo '</td>
                                    <td>'; if(isset($besar1[0]->keterangan)) echo $besar1[0]->keterangan; else echo '-'; echo '</td>
                                </tr>';

                            $i++;
                        }
                    }
                    ?>
                </tbody>
            </table>

            <h3 style="color:#ec7404">Keterangan</h3>
            <textarea rows="5" name="keterangan"></textarea>

            <div class="control-actions">
                <input type="hidden" name="user_id" value="<?php echo $_SESSION['sabilulungan']['uid']; ?>">
                <input type="hidden" name="role_id" value="<?php echo $_SESSION['sabilulungan']['role']; ?>">
                <input type="submit" name="lanjut" class="btn-red btn-plain btn" style="display:inline" value="Setuju" />
                <input type="submit" name="tolak" class="btn-red btn-plain btn" style="display:inline" value="Ditolak" />
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

$Qket = $this->db->query("SELECT value AS keterangan FROM proposal_checklist WHERE proposal_id='$dx' AND checklist_id=13"); $ket = $Qket->result_object();

$Qedit = $this->db->query("SELECT checklist_id, value FROM proposal_checklist WHERE `proposal_id`='$dx' AND checklist_id='14'"); $edit = $Qedit->result_object();

$Qketerangan = $this->db->query("SELECT value AS keterangan_walikota FROM pemeriksaan_walikota WHERE `id_proposal`='$dx'"); $keterangan = $Qketerangan->result_object();

$Qedit1 = $this->db->query("SELECT value AS keterangan_tatausaha FROM pemeriksaan_tu WHERE `id_proposal`='$dx'"); $edit1 = $Qedit1->result_object();

$Qdisposisi = $this->db->query("SELECT path FROM proposal_disposisi WHERE id_proposal='$dx'"); $dis = $Qdisposisi->result_object();
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
        
        
        <form action="<?php echo base_url('process/walikota/edit/'.$dx) ?>" method="post" class="form-check form-global" enctype="multipart/form-data">
            <h1 class="page-title page-title-border">Pemeriksaan Proposal Hibah Bansos Hasil Seleksi TU</h1>
            <p class="label">Nama (Individu atau Organisasi)</p>
            <p><?php echo $detail[0]->name ?></p>
            <p class="label">Judul Kegiatan</p>
            <p><?php echo $detail[0]->judul ?></p>
            <p class="label">Deskripsi Singkat Kegiatan</p>
            <p><?php echo $detail[0]->latar_belakang ?></p>
            <p class="label">Nominal yang Diajukan di Proposal</p>
            <p><?php echo 'Rp. '.number_format($detail[0]->nominal,0,",",".").',-' ?></p>
            <p class="label">Keterangan dari TU</p>
            <p><?php if(isset($edit1[0]->keterangan_tatausaha)) echo $edit1[0]->keterangan_tatausaha; else echo '-'; ?></p>
        
				
				<div class="control-group">
                    <label class="control-label" for="">File Exist</label>
					<p><?php if(isset($dis[0]->path)) echo '<a class="info" target="_blank" href="'.base_url('media/disposisi/'.$dis[0]->path).'">Download File</a>'; else echo '-';?></p>
                </div>
				
                <div class="control-group">
                    <label class="control-label" for="">Update Disposisi</label>
                    <div class="controls file">
                        <input type="file" name="disposisi" accept="application/pdf" required>
                    </div>
                </div>
                
        
        
            <h3 style="color:#ec7404">Keterangan</h3>
            <textarea rows="5" name="keterangan"><?php if(isset($keterangan[0]->keterangan_walikota)) echo $keterangan[0]->keterangan_walikota; ?></textarea>

            <div class="control-actions">
                <input type="hidden" name="user_id" value="<?php echo $_SESSION['sabilulungan']['uid']; ?>">
                <input type="hidden" name="role_id" value="<?php echo $_SESSION['sabilulungan']['role']; ?>">
                <input type="submit" name="lanjut" class="btn-red btn-plain btn" style="display:inline" value="Disposisi ke Tim Pertimbangan" />
                <!--<input type="submit" name="tolak" class="btn-red btn-plain btn" style="display:inline" value="Ditolak" /> -->
                <a href="<?php echo site_url('report') ?>" class="btn-grey btn-plain btn" style="display:inline">Kembali</a>
            </div>
        </form>
    </div>
</div>
<!-- content-main -->

<?php
break;

case 'view':

/*$Qedit = $this->db->query("SELECT checklist_id, value FROM proposal_checklist WHERE `proposal_id`='$dx' AND checklist_id='30'"); $edit = $Qedit->result_object();*/

//edit ferdi
$Qedit = $this->db->query("SELECT value FROM verifikasi_walikota WHERE `id_proposal`='$dx'"); $edit = $Qedit->result_object();
?>

<div role="main" class="content-main" style="margin:120px 0 50px">
    <div class="wrapper">
        <!-- <h1 class="page-title page-title-border">Detail Pemeriksaan Proposal Hibah Bansos Masuk</h1> -->
        <?php
        if(isset($_SESSION['notify'])){
            echo '<div class="alert-bar alert-bar-'.$_SESSION['notify']['type'].'" style="width:100%">'.$_SESSION['notify']['message'].'</div>';
            unset($_SESSION['notify']);
        }         

        /* $Qdetail = $this->db->query("SELECT a.time_entry AS awal, b.name AS skpd, c.time_entry AS akhir FROM proposal a JOIN skpd b ON b.id=a.id JOIN proposal_approval c ON c.proposal_id=a.id WHERE a.id='$dx' AND c.flow_id='6'"); $detail = $Qdetail->result_object();  */
		
		$Qdetail = $this->db->query("SELECT a.time_entry AS awal, b.name AS skpd, c.time_entry AS akhir FROM proposal a JOIN skpd b ON b.id=a.skpd_id JOIN proposal_approval c ON c.proposal_id=a.id WHERE a.id='$dx' AND c.flow_id='6'"); $detail = $Qdetail->result_object(); 
		 
        ?>

        <form action="<?php echo base_url('process/walikota/view/'.$dx) ?>" method="post" class="form-check form-global">
            <h1 class="page-title page-title-border">Pemeriksaan Proposal Hibah Bansos Hasil Seleksi TU</h1>
            <p class="label">No DNC PBH</p>
            <p>DNC3</p>
            <p class="label">SKPD</p>
            <p><?php if(isset($detail[0]->skpd)) echo $detail[0]->skpd; else echo '-'; ?></p>
            <p class="label">Tanggal Awal</p>
            <p><?php if(isset($detail[0]->awal)) echo date('M d, Y. g:i a', strtotime($detail[0]->awal)); else echo '-'; ?></p>
            <p class="label">Tanggal Akhir</p>
            <p><?php if(isset($detail[0]->akhir)) echo date('M d, Y. g:i a', strtotime($detail[0]->akhir)); else echo '-'; ?></p>

            <table class="table-global">
                <thead>
                    <tr>
                        <th rowspan="2">No.</th>
                        <th rowspan="2">Nama Lengkap Calon Penerima</th>
                        <th rowspan="2">Alamat Lengkap</th>
                        <th rowspan="2">Rencana Penggunaan</th>
                        <th class="has-sub" colspan="3">Besaran Belanja Hibah (Rp)</th>
                        <th rowspan="2">Keterangan</th>
                    </tr>
                    <tr>
                        <th>Permohonan</th>
                        <th>Hasil Evaluasi</th>
                        <th>Pertimbangan TAPD</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $Qlist = $this->db->select("id, name, address, latar_belakang")->from('proposal')->where('id', $dx)->get();

                    if($Qlist->num_rows){
                        $i = 1;
                        foreach($Qlist->result_object() as $list){
                            $Qmohon = $this->db->query("SELECT SUM(amount) AS mohon FROM proposal_dana WHERE `proposal_id`='$list->id'"); $mohon = $Qmohon->result_object(); 

                            /*$Qbesar = $this->db->query("SELECT value FROM proposal_checklist WHERE `proposal_id`='$list->id' AND checklist_id IN (26,28,29)"); $besar = $Qbesar->result_object(); */

                            //edit ferdi
                            $Qbesar = $this->db->query("SELECT value FROM verifikasi_pertimbangan WHERE `id_proposal`='$list->id'"); $besar = $Qbesar->result_object(); 

                            $Qbesar1 = $this->db->query("SELECT value , keterangan FROM verifikasi_tapd WHERE `id_proposal`='$list->id'"); $besar1 = $Qbesar1->result_object();

                            echo '<tr>
                                    <td>'.$i.'</td>
                                    <td>'.$list->name.'</td>
                                    <td>'.$list->address.'</td>
                                    <td>'.$list->latar_belakang.'</td>
                                    <td>Rp. '.number_format($mohon[0]->mohon,0,",",".").',-</td>
                                    <td>'; if(isset($besar[0]->value)) echo 'Rp. '.number_format($besar[0]->value,0,",",".").',-'; else echo '-'; echo '</td>
                                    <td>'; if(isset($besar1[0]->value)) echo 'Rp. '.number_format($besar1[0]->value,0,",",".").',-'; else echo '-'; echo '</td>
                                    <td>'; if(isset($besar1[0]->keterangan)) echo $besar1[0]->keterangan; else echo '-'; echo '</td>
                                </tr>';

                            $i++;
                        }
                    }
                    ?>
                </tbody>
            </table>

            <h3 style="color:#ec7404">Keterangan</h3>
            <textarea rows="5" name="keterangan"><?php if(isset($edit[0]->value)) echo $edit[0]->value; ?></textarea>

            <div class="control-actions">
                <input type="hidden" name="user_id" value="<?php echo $_SESSION['sabilulungan']['uid']; ?>">
                <input type="hidden" name="role_id" value="<?php echo $_SESSION['sabilulungan']['role']; ?>">
                <input type="submit" name="lanjut" class="btn-red btn-plain btn" style="display:inline" value="Simpan" />
                <!-- <input type="submit" name="tolak" class="btn-red btn-plain btn" style="display:inline" value="Ditolak" /> -->
                <a href="<?php echo site_url('report') ?>" class="btn-grey btn-plain btn" style="display:inline">Kembali</a>
            </div>
        </form>
    </div>
</div>
<!-- content-main -->

<?php
break;


case 'verifikasi2':

$Qdetail = $this->db->query("SELECT a.name, a.judul, a.latar_belakang, SUM(b.amount) AS nominal FROM proposal a JOIN proposal_dana b ON b.proposal_id=a.id WHERE a.id='$dx'"); $detail = $Qdetail->result_object();

$Qket = $this->db->query("SELECT value AS keterangan FROM verifikasi_tatausaha WHERE `id_proposal`='$dx'"); $ket = $Qket->result_object();

$Qedit = $this->db->query("SELECT checklist_id, value FROM proposal_checklist WHERE `proposal_id`='$dx' AND checklist_id='14'"); $edit = $Qedit->result_object();

$Qnota = $this->db->query("SELECT path FROM nota_dinas WHERE id_proposal='$dx'"); $nota = $Qnota->result_object();
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
        
        
        <form action="<?php echo base_url('process/walikota/verifikasi2/'.$dx) ?>" method="post" class="form-check form-global" enctype="multipart/form-data">
            <h1 class="page-title page-title-border">Pemeriksaan Walikota II</h1>
			<h3 style="color:#ec7404">Ringkasan Proposal</h3>
            <p class="label">Nama (Individu atau Organisasi)</p>
            <p><?php echo $detail[0]->name ?></p>
            <p class="label">Judul Kegiatan</p>
            <p><?php echo $detail[0]->judul ?></p>
            <p class="label">Deskripsi Singkat Kegiatan</p>
            <p><?php echo $detail[0]->latar_belakang ?></p>
            <p class="label">Nominal yang Diajukan</p>
            <p><?php echo 'Rp. '.number_format($detail[0]->nominal,0,",",".").',-' ?></p>
			<p class="label">Keterangan dari TU</p>
            <p><?php if(isset($ket[0]->keterangan)) echo $ket[0]->keterangan; else echo '-'; ?></p>
			<p class="label">Download Nota Dinas</p>
            <p><?php if(isset($nota[0]->path)) echo '<a class="info" target="_blank" href="'.base_url('media/nota_dinas/'.$nota[0]->path).'">Download File</a>'; else echo '-';?></p>
        
                 <div class="control-group">
                    <label class="control-label" for="">Upload Disposisi</label>
                    <div class="controls file">
                        <input type="file" name="disposisi" accept="application/pdf" required="">
                    </div>
                </div>
        
            <h3 style="color:#ec7404">Keterangan</h3>
            <textarea rows="5" name="keterangan"><?php if(isset($edit[0]->value)) echo $edit[0]->value; ?></textarea>

            <div class="control-actions">
                <input type="hidden" name="user_id" value="<?php echo $_SESSION['sabilulungan']['uid']; ?>">
                <input type="hidden" name="role_id" value="<?php echo $_SESSION['sabilulungan']['role']; ?>">
                <input type="submit" name="lanjut" class="btn-red btn-plain btn" style="display:inline" value="Lanjut ke Proses Pencarian" />
                <!--<input type="submit" name="tolak" class="btn-red btn-plain btn" style="display:inline" value="Ditolak" /> -->
                <a href="<?php echo site_url('report') ?>" class="btn-grey btn-plain btn" style="display:inline">Kembali</a>
            </div>
        </form>
    </div>
</div>
<!-- content-main -->

<?php
break;


}