<?php

/**
 * Author : Yogi Pratama | Mail me [at] youputra@gmail.com
 * Description :
 * Licence to
 * @copyright 2013.
 */

require_once "../vendor/autoload.php";

class AwsS3
{
    protected $s3;
    protected $config;
    protected $key = AWS_S3_KEY;
    protected $secret = AWS_S3_SECRET;
    protected $bucket = "lintasdaya";
    protected $region = "ap-southeast-1";
    protected $version = "latest";
    protected $tempPathLocation = "../../files/temp/";
    protected $suffix = "_sopan";

    function __construct()
    {
        $this->config = [
            "region"  => $this->region,
            "version" => $this->version,
            "credentials" => [
                "key"    => $this->key,
                "secret" => $this->secret,
                "bucket" => $this->bucket
            ]
        ];
        $this->s3 = Aws\S3\S3Client::factory($this->config);
    }

    function uploadFile($s3Folder, $files, $preferedFileName = "")
    {
        try {
            $tmpName = $files['tmp_name'];
            $extension = explode('.', $files['name']);
            $extension = strtolower(end($extension));

            $key = md5(uniqid());

            if ($preferedFileName == "")
                $tmp_file_name = date("YmdHis") . "-{$key}.{$extension}";
            else
                $tmp_file_name = $preferedFileName;

            $tmp_file_path = $this->tempPathLocation . "{$tmp_file_name}";

            move_uploaded_file($tmpName, $tmp_file_path);

            $s3Path = $s3Folder . $this->suffix . "/" . $tmp_file_name;

            $this->s3->putObject([
                "Bucket" => $this->bucket,
                "Key" => $s3Path,
                "Body" => fopen($tmp_file_path, "rb"),
                "ACL" => "public-read"
            ]);

            unlink($tmp_file_path);

            return true;
        } catch (\Aws\S3\Exception\S3Exception $ex) {
            return false;
        }
    }

    function uploadFileDirect($s3Folder, $files)
    {
        try {
            $tmpName = $files['tmp_name'];
            $fileName = date("YmdHis") . "_" . str_replace(" ", "_", strtolower($files['name']));

            $s3Path = $s3Folder . $this->suffix . "/" . $fileName;

            $this->s3->putObject([
                "Bucket" => $this->bucket,
                "Key" => $s3Path,
                "Body" => fopen($tmpName, "rb"),
                "ACL" => "public-read"
            ]);

            return  $fileName;
        } catch (\Aws\S3\Exception\S3Exception $ex) {
            var_dump($ex->getMessage());
            return false;
        }
    }

    function uploadFileDirect2($s3Folder, $tmp_file, $filename)
    {
        try {
            $tmpName = $tmp_file;
            $fileName = date("YmdHis") . "_" . str_replace(" ", "_", strtolower($filename));

            $s3Path = $s3Folder . $this->suffix . "/" . $fileName;

            $this->s3->putObject([
                "Bucket" => $this->bucket,
                "Key" => $s3Path,
                "Body" => fopen($tmpName, "rb"),
                "ACL" => "public-read"
            ]);

            return  $fileName;
        } catch (\Aws\S3\Exception\S3Exception $ex) {
            var_dump($ex->getMessage());
            return false;
        }
    }

    function deleteFile($s3Path)
    {
        try {
            $this->s3->deleteObject([
                "Bucket" => $this->bucket,
                "Key" => $s3Path
            ]);
            return true;
        } catch (\Aws\S3\Exception\S3Exception $ex) {
            return false;
        }
    }

    function getFiles($prefix)
    {
        return $this->s3->getIterator("ListObjects", ["Bucket" => $this->bucket, "Prefix" => $prefix]);
    }

    function getObjectUrl($key)
    {
        return $this->s3->getObjectUrl($this->bucket, $key);
    }
}
