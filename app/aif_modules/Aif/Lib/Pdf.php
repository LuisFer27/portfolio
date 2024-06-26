<?php
/**
 *
 */

//AifExternal::call('/fpdf/fpdf.php') || die('FPDF is required');
//AifExternal::call('/phpqrcode/qrlib.php') || die('PHPQRCODE is required');
//AifExternal::call('/php-barcode-master/barcode.php') || die('BARCODE is required');


/**
 *
 * @author mygnet
 *
 */
class AifLibPdf {
    /**
     *
     * @var unknown
     */
    public $formatHeader = NULL;
    /**
     *
     * @var unknown
     */
    public $formatFooter = NULL;
    /**
     *
     * @param string $orientation
     * @param string $unit
     * @param string $size
     */
    public function __construct($orientation = 'P', $unit = 'mm', $size = 'Letter') {
        parent::FPDF($orientation, $unit, $size);
        $this->SetCreator('Aif '.Aif::$version);
    }
    /**
     * (non-PHPdoc)
     * @see FPDF::Header()
     */
    public function Header($pg = null, $ii =null, $total=null, $qrXpg =null, $max=null, $my=null, $pMax=null, $id=null, $line=null) {
        if ($pg) {
          /*$this->SetLineWidth($line);
          $this->SetFont('Arial','B',8);
                $this->text(40, $my - 1, utf8_decode($total . ' códigos [ ' . ($ii + 1) . ' - ' . ($ii + $qrXpg > $max ? $max : $ii + $qrXpg) . ' ] '));
                $this->text(155, $my - 1, utf8_decode('Página ' . ($pg) . ' de ' . $pMax));
                $this->text(105, $my - 1, $id . '/P' . $pg);

            $this->Ln(10);*/
        }
    }
    /**
     * (non-PHPdoc)
     * @see FPDF::Footer()
     */
    public function Footer($code = null) {
      if ($code) {
      $dir = 'temp/';
      //Si no existe la carpeta la creamos
      if (!file_exists($dir))
            mkdir($dir);
     //Declaramos la ruta y nombre del archivo a generar
      $filename = $dir.$code.'.png';
  		barcode('temp/'.$code.'.png', $code, 10, 'horizontal', 'code39', false);
  		$this->Image($dir.basename($filename),60,264.7,98,11,'PNG');
      unlink ($filename);
        /*if ($this->formatFooter) {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->SetTextColor(128);
            $this->Cell(0, 10, 'Página ' . $this->PageNo(), 0, 0, 'C');
        }*/
      }
    }

}
