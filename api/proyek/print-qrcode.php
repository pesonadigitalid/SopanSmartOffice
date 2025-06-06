<?php
session_start();
$id = $_GET['id'];
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="description" content=""/>
        <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543"/>
        
        <title>SOPAN Smart Office - Smart office for smart people</title>
        
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"/>
        <link rel="stylesheet" href="<?php echo PRSONTEMPPATH; ?>css/print-style2.css" media="all"/>
        <link rel="stylesheet" href="<?php echo PRSONTEMPPATH; ?>css/font-awesome.min.css" media="all"/>
    </head>
    <body>
        <img src="https://chart.googleapis.com/chart?chs=500x500&cht=qr&chl=http%3A%2F%2F125.208.136.34:8007%2Fsmartoffice%2Fapi%2Fproyek%2FdoQR.php%3Fid%3D<?php echo $id; ?>&choe=UTF-8">
        <script type="text/javascript">
            window.onload = function () { window.print(); }
        </script>
    </body>
</html> 
</body>
</html>