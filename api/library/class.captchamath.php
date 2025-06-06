<?php
class CaptchaMath {
 /**
 * CaptchaMath 
 *
 * @author didik dwi prasetyo <didik_rpl@yahoo.com>
 * modified by odick 
 * @version    Release: 0.1.0
 */
  private static $bil = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);

  private static $operasi = array(
    // method, label, generator, simbol
    array('kurang', 'label', 'pengurangan', '-'),
    array('tambah',  'label', 'penambahan', '+'),
  );

  // Operasi penambahan
  private static function tambah($bil1, $bil2) {
    return $bil1 + $bil2;
  }

  // Generator penambahan bilangan
  private static function penambahan($bil) {
    return mt_rand(0, 9 - $bil);
  }

  // Operasi pengurangan
  private static function kurang($bil1, $bil2) {
    return $bil1 - $bil2;
  }

  // Generator pengurangan bilangan
  private static function pengurangan($bil) {
    return mt_rand(0, $bil);
  }

  // Mencetak label operasi
  private static function label($op, $bil1, $bil2) {
    return $bil1 . ' ' . $op . ' ' . $bil2;
  }

  // Generator ekspresi operasi
  public static function createEkspresi() {
    $len = count(self::$operasi);
    // Memilih operasi secara acak
    $op = self::$operasi[mt_rand(0, $len-1)];

    // Men-generate operand pertama
    $a  = mt_rand(0, 9);
    $bil1 = self::$bil[$a];

    // Operand kedua bergantung pada operator
    // dan dievaluasi oleh generator agar sesuai
    $b  = isset($op[2]) ? self::$op[2]($a) :
      mt_rand(0, 9);
    // Menghasilkan operand kedua
    $bil2 = self::$bil[$b];

    // Men-generate ekspresi
    $ekspresi = self::$op[1]($op[3], $bil1, $bil2);
    return array($op[3], $bil1, $bil2, $ekspresi);
  }


  /**
   * Mengevaluasi hasil operasi
   * @param String $nama nama operasi
   * @param String $a operand 1
   * @param String $b operand 2
   * @param String $jawaban jawaban
   */
  public static function evaluasi($nama, $a, $b,
  $jawaban) {
    $op = array();
    // Memastikan bahwa operasi sesuai
    foreach (self::$operasi as $v) {
      if ($v[3] === $nama) {
        $op = $v;
        break;
      }
    }

    // Mendapatkan operand (numerik)
    $bil1 = array_search($a, self::$bil);
    $bil2 = array_search($b, self::$bil);

    if (empty($op) || $bil1 === false
    || $bil2 === false) {
      return false;
    }

    // Evaluasi dan bandingkan dengan jawaban
    $val = self::$op[0]($bil1, $bil2);
    return (self::$bil[$val] == $jawaban);
  }
}
?>