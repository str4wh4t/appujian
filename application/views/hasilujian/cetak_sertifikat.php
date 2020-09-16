<?php
// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
	
	private $_masa_berlaku_sert ;
    
    public function Header() {
//        $image_file = K_PATH_IMAGES.'logo_example.jpg';
//	    $this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
	    $image_file = asset('assets/imgs/logo_undip.png');
        $this->Image($image_file, 20, 10, 22, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        $this->SetFont('helvetica', 'B', 18);
        $this->SetY(18);
        $this->SetX(50);
        $this->Cell(0, 15, 'UNIVERSITAS DIPONEGORO', 0, false, 'T', 0, '', 0, false, 'M', 'M');
        $this->SetY(28);
        $this->SetX(50);
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 10, 'LEMBAGA PENGEMBANGAN DAN PENJAMINAN MUTU PENDIDIKAN', 0, false, 'T', 0, '', 0, false, 'M', 'M');
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Sertifikat berlaku selama '. $this->_masa_berlaku_sert .' tahun sejak saat tes dilakukan', 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
    
    public function set_masa_berlaku_sert($masa_berlaku_sert){
    	$this->_masa_berlaku_sert = $masa_berlaku_sert;
    }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('Hasil Ujian');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, 50, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 10);

// add a page
$pdf->AddPage('L', 'A4');

$pdf->set_masa_berlaku_sert($ujian->masa_berlaku_sert);

$tmp_lahir =  strtoupper($mhs->tmp_lahir);

$d = $mhs->tgl_lahir;
$dt = DateTime::createFromFormat('Y-m-d', $d);
//$tgl_lahir_format = $dt->format('d F Y');
$tgl_lahir_format = strftime("%d %B %Y", $dt->getTimestamp());

$d = $hasil->tgl_mulai;
$dt = DateTime::createFromFormat('Y-m-d H:i:s', $d);
// $tgl_ujian_format = $dt->format('l, d F Y');
$tgl_ujian_format = strftime("%A, %d %B %Y", $dt->getTimestamp());

$hasil_akhir = number_format($hasil['nilai_bobot_benar'] / 3,2,'.', '') ;

// create some HTML content
$html = <<<EOD
<p style="text-align: center">
<b>Memberikan sertifikat kepada :</b>
</p>
<table style="width: 900px; margin-left: 100px;">
    <tr>
        <td bgcolor="#fff" style="width: 250px; border:none;"></td>
        <td style="width: 200px;">Nama</td>
        <td>: {$mhs->nama}</td>
    </tr>
    <tr>
        <td bgcolor="#fff" style="width: 250px; border:none;"></td>
        <td style="width: 200px;">No Peserta</td>
        <td>: {$mhs->nim}</td>
    </tr>
    <tr>
        <td bgcolor="#fff" style="width: 250px; border:none;"></td>
        <td style="width: 200px;">Tempat dan Tangal Lahir</td>
        <td>: {$tmp_lahir} / {$tgl_lahir_format}</td>
    </tr>
</table>
<p style="text-align: center">
Telah mengikuti {$ujian->matkul->nama_matkul} Universitas Diponegoro pada tanggal {$tgl_ujian_format} dengan hasil sebagai berikut :
</p>
<table style="width: 900px;">
    <tr style="height: 30px">
		<th bgcolor="#fff" style="width: 120px; border:none; border-right: 1px solid #000000;"></th>
EOD;

$txt_topik = '';
$txt_nilai = '';
foreach ($detail_ujian as $topik => $nilai){
	$txt_topik .= '<th style="text-align: center; border:1px solid #000000;">' . $topik . '</th>';
	$txt_nilai .= '<td style="text-align: center; border:1px solid #000000;">' . $nilai . '</td>';
}

$link = url('c/' . $mhs->nim . '/' . uuid_create_from_integer($hasil->ujian_id) );

$html .= <<<EOD
        {$txt_topik}
        <th style="text-align: center; border:1px solid #000000;">SKOR AKHIR</th>
    </tr>
    <tr style="height: 30px">
        <td bgcolor="#fff" style="width: 120px; border:none; border-right: 1px solid #000000;"></td>
        {$txt_nilai}
        <td style="text-align: center; border:1px solid #000000;">{$hasil_akhir}</td>
    </tr>
</table>
<p style="text-align: center">
<img src="{$mhs->foto}" style="width: 109px;height: 147px;" />
</p>
<p style="text-align: center">
Sertifikat diterbitkan oleh Universitas Diponegoro dan dapat divalidasi melalui <a href="{$link}">www.cat.undip.ac.id</a>
</p>
EOD;
// output the HTML content
$pdf->writeHTML($html, true, 0, true, 0);

$style = array(
    'border' => 1,
    'vpadding' => 2,
    'hpadding' => 2,
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255)
    'module_width' => 1, // width of a single module in points
    'module_height' => 1 // height of a single module in points
);
$pdf->write2DBarcode($link, 'QRCODE,Q', 175, 99, 40, 40, $style, 'N');

// reset pointer to the last page
$pdf->lastPage();
// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output($ujian->nama_ujian.'_'.$mhs->nim.'.pdf', 'I');
