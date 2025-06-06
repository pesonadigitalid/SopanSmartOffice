<?php
class Validasi
{
    /* Fungsi untuk menghapus semua karakter yang jelek kecuali spasi */        
    function validData($data)
    {
        return preg_replace('/[^a-zA-Z0-9\s]/', '', $data);
    }
    
    /* Fungsi untuk menghapus karakter sebagai anti ijeksi */
    function validInput($data)
    {
        $reg = "('union','\'',';','javascript','script','exec')";
        return(preg_replace($reg, " ", $data));
    }
        
    /* Fungsi untuk memeriksa data bertipe numeric */
    function dataNumeric($data)
    {
        if(is_numeric($data)) return true; else return false;
    }
        
    /* Fungsi untuk memfilter XSS */
    function validXSS($data)
    {
        return trim(htmlentities(strip_tags($data)));
    }
    
    /* fungsi untuk memfilter SQL injection */
    function validSql($data)
    {
        return mysql_escape_string($this->validData($this->validXSS($data)));
    }
    
    function validPureSql($data)
    {
        return mysql_real_escape_string($data);
    }
    
    /* fungsi untuk memeriksa tipe data integer */
    function validInt($data)
    {
        if(is_integer($this->dataNumeric($data))) return true; else return false;    
    }
    
    /* fungsi untuk memriksa format email */
    function validEmail($email)
    {
        $format="/^.+@.+\..+$/";
        $email=strtolower($email);
        if(preg_match($format,$email)) return true; else return false;
    }
    
    /* format untuk memeriksa format URL */
    function validUrl($url)
    {
        $format="/^(http|https):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(([0-9]{1,5})?\/.*)?$/";
        $url=strtolower($url);
        if(preg_match($format,$url)) return true; else return false;
    }
    
    /* Fungsi untuk memeriksa tipe gambar */
    function validImg($tipe)
    {
        $tipeBoleh=array('image/jpeg','image/jpg','image/gif','image/png','image/bmp');
        if(in_array($tipe,$tipeBoleh)) return true; else return false;
    }
    
    /* fungsi untuk memeriksa panjang karakter */
    function validPanjang($data,$min,$max)
    {
        $data=$this->validData($data);
        $min=$this->dataNumeric($this->validInt($min));
        $max=$this->dataNumeric($this->validInt($max));
        if(strlen($data) <= $min || strlen($data) >= $max) return true;else return false;
    }
    
    /* fungsi memeriksa keberadaan file */
    function validFile($file,$lokasi)
    {
        if(file_exists($lokasi."".$file) && is_file($lokasi."".$file)) return true;else return false;
    }
    
    /* fungsi memeriksa tipe file */
    function validTipeFile($file)
    {
        $tipeBoleh=array('zip','rar','doc','docx','txt','pdf','tar','tar.gz','gz','rtf');
        $tipe=explode('.',$this->validData($file));
        if(in_array($this->validData($tipe),$tipeBoleh)) return true; else return false;
    }    
    
    /* fungsi untuk mengijinkan post di delete atau tidak */
    function allowDelete($id)
    {
        $explode_id = explode(",",PRSONETERNALPOST);
        $return = true;
        foreach($explode_id as $eternal_id){
            if($id==$eternal_id){
                $return = false;
            }
        }
        
        return $return;
    }
}
?>