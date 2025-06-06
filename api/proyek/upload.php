<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

$FileType = antiSQLInjection($_POST['FileType']);
$IDProyek = antiSQLInjection($_POST['IDProyek']);
$Name = antiSQLInjection($_POST['Name']);
do {
    $HashCode = HASH_PREFIX . md5(date("YmdHis") . rand(1, 1000000000));
    $cek = $db->get_row("SELECT * FROM tb_proyek_file WHERE HashCode='$HashCode'");
} while ($cek);

if ($_FILES['file']) {
    $file_name = $AwsS3->uploadFileDirect("proyek_file",  $_FILES['file']);
    $query = $db->query("INSERT INTO tb_proyek_file SET IDProyek='$IDProyek', Name='$Name', HashCode='$HashCode', FileType='$FileType', FileName='$file_name', CreatedBy='" . $_SESSION["uid"] . "', DateCreated=NOW()");
    if ($query) {
        echo "1";
    } else {
        echo "0";
    }
} else {
    echo "0";
}
