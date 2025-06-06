<?php

/**
 * Author : Yogi Pratama | Mail me [at] youputra@gmail.com
 * Description :
 * Licence to
 * @copyright 2013.
 */

class Fungsi
{
    protected $db;

    function __construct()
    {
        $this->db = new ezSQL_mysql("root", "diadmin", "sopan", "localhost");
    }

    function antiSQLInjection($input)
    {
        $reg = "/(delete|update|union|insert|'|;|javascript|script|exec)/";
        return (preg_replace($reg, "", $input));
    }

    function encodeURL($data)
    {
        return substr(md5($data), 0, 10);
    }

    function createLink($var)
    {
        return str_replace(" ", "-", strtolower($var));
    }

    function get_real_ip()
    {
        if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            return $_SERVER["HTTP_CLIENT_IP"];
        } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_X_FORWARDED"])) {
            return $_SERVER["HTTP_X_FORWARDED"];
        } elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_FORWARDED"])) {
            return $_SERVER["HTTP_FORWARDED"];
        } else {
            return $_SERVER["REMOTE_ADDR"];
        }
    }

    function getExtension($str)
    {
        $i = strrpos($str, ".");
        if (!$i) {
            return "";
        }
        $l = strlen($str) - $i;
        $ext = substr($str, $i + 1, $l);
        return $ext;
    }

    function uploadImage($image, $image_loc, $destination)
    {
        $filename = stripslashes($image);
        $extension = $this->getExtension($filename);
        $extension = strtolower($extension);

        $extract = explode(".", $filename);
        $filename = $extract[0];

        if (($extension == "jpg") || ($extension == "jpeg") || ($extension == "png") || ($extension == "gif")) {
            $image_name = $filename . '.' . $extension;
            $newname = $destination . $image_name;
            $copied = copy($image_loc, $newname);
        }

        return $image_name;
    }

    function uploadFile($image, $image_loc, $destination)
    {
        $filename = stripslashes($image);
        $extension = $this->getExtension($filename);
        $extension = strtolower($extension);

        $extract = explode(".", $filename);
        $filename = $extract[0];

        $image_name = $filename . '.' . $extension;
        $newname = $destination . $image_name;
        $copied = copy($image_loc, $newname);

        return $image_name;
    }

    function uploadImage2($image, $destination, $width_size = "", $file_name = "")
    {
        $handle = new Upload($image);
        if ($handle->uploaded) {
            if ($file_name != "") {
                $handle->file_new_name_body = $file_name;
            }
            if ($width_size != "") {
                $handle->image_resize = true;
                $handle->image_x = $width_size;
                $handle->image_ratio_y = true;
            }
            $handle->allowed = array('image/*');
            $handle->Process($destination);
            if ($handle->processed) {
                $lastfile = $handle->file_dst_name;
                $handle->Clean();
                return $lastfile;
            } else {
                $handle->Clean();
                return null;
            }
        }
    }

    function discardImage($filename, $location = "../public/files/images/")
    {
        if (file_exists($location . $filename))
            unlink($location . $filename);
    }

    function wordLimiter($text, $limit = 160, $chars = '0123456789')
    {
        if (strlen($text) > $limit) {
            $words = str_word_count($text, 2, $chars);
            $words = array_reverse($words, TRUE);
            foreach ($words as $length => $word) {
                if ($length + strlen($word) >= $limit) {
                    array_shift($words);
                } else {
                    break;
                }
            }
            $words = array_reverse($words);
            $text = implode(" ", $words);
        }
        return $text;
    }

    function titleCase($words, $charList = null)
    {
        $words = strtolower($words);
        if (!isset($charList)) {
            $charList = " ";
        }
        $capitalizeNext = true;
        for ($i = 0, $max = strlen($words); $i < $max; $i++) {
            if (strpos($charList, $words[$i]) !== false) {
                $capitalizeNext = true;
            } else if ($capitalizeNext) {
                $capitalizeNext = false;
                $words[$i] = strtoupper($words[$i]);
            }
        }
        return $words;
    }

    function ENDate($date)
    {
        $exp = explode("/", $date);
        return $exp[2] . "-" . $exp[1] . "-" . $exp[0];
    }

    function IDDate($date)
    {
        $exp = explode("-", $date);
        return $exp[2] . "/" . $exp[1] . "/" . $exp[0];
    }

    function sendMail($emailTo = "", $message_subject = "", $message = "", $emailCC = "", $replyemail = "", $replyname = "")
    {
        $headers = "From: " . YGMAILNAME . " <" . YGMAILADDRS . "> \r\n";
        if ($emailCC != "")
            $headers .= "Cc: " . $emailCC . " \r\n";
        if ($replyemail != "" && $replyname != "")
            $headers .= "Reply-To: " . $replyname . " <" . $replyemail . "> \r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        return mail($emailTo, $message_subject, $message, $headers);
    }

    /**
     * $tanggal -> IDFormat e.g dd/mm/yyyy
     */
    function GetNoFaktur($tanggal, $tableName, $noFakturFieldName, $prefix = null)
    {
        $exp = explode("/", $tanggal);
        $tanggalNoFaktur = $exp[2] . "/" . $exp[1] . "/";
        $tanggalCond = $exp[2] . "-" . $exp[1];

        $noFaktur = "";
        $dataLast = $this->db->get_row("SELECT * FROM $tableName WHERE DATE_FORMAT(Tanggal,'%Y-%m')='" . $tanggalCond . "' ORDER BY $noFakturFieldName DESC");
        if ($dataLast) $last = intval(substr($dataLast->$noFakturFieldName, -3));
        else $last = 0;
        do {
            $last++;
            if ($last < 100 and $last >= 10)
                $last = "0" . $last;
            else if ($last < 10)
                $last = "00" . $last;

            if ($prefix != null) {
                $noFaktur = $prefix . $tanggalNoFaktur . $last;
            } else {
                $noFaktur = "DO/SPN/" . $tanggalNoFaktur . $last;
            }

            $checkNoFaktur = $this->db->get_row("SELECT * FROM $tableName WHERE $noFakturFieldName='$noFaktur'");
        } while ($checkNoFaktur);

        return $noFaktur;
    }

    function months()
    {
        return array(
            "Januari",
            "Februari",
            "Maret",
            "April",
            "Mei",
            "Juni",
            "Juli",
            "Agustus",
            "September",
            "Oktober",
            "November",
            "Desember",
        );
    }

    function getDiscountValue($discount, $price)
    {
        if (!$discount) return 0;
        if (strpos($discount, "%") !== false) {
            $discountPercentageSplit = explode("+", $discount);
            $totalDiscount = 0;
            $price = floatval($price);
            foreach ($discountPercentageSplit as $discountPercentage) {
                if ($discountPercentage) {
                    $discountPercentageNumber = floatval(str_replace("%", "", trim($discountPercentage)));
                    $diskon = ($price * $discountPercentageNumber) / 100;

                    $totalDiscount += $diskon;
                    $price -= $diskon;
                }
            }
            return round($totalDiscount);
        }
        return $discount;
    }

    function getPriceAfterDiscount($discount, $price)
    {
        $discountValue = $this->getDiscountValue($discount, $price);
        return $price - $discountValue;
    }

    function getPriceAfterDistributedDiscount($discount, $price, $numberOfItemsWithPrice)
    {
        if ($price <= 0) return $price;
        if (strpos($discount, "%") !== false) return $this->getPriceAfterDiscount($discount, $price);
        if ($numberOfItemsWithPrice <= 0) return $price;
        return $price - round($discount /  $numberOfItemsWithPrice);
    }

    function getDPP($ppn, $price){
        return round((100 / (100 + floatval($ppn))) * floatval($price));
    }
}
