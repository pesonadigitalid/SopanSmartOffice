<?php
session_start();
include_once "../config/connection.php";
$karyawan = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='" . $_SESSION["uid"] . "'");
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>SOPAN Smart Office - Integrated System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no" />
  <link rel="apple-touch-icon" href="../../themes/pages/ico/60.png">
  <link rel="apple-touch-icon" sizes="76x76" href="../../themes/pages/ico/76.png">
  <link rel="apple-touch-icon" sizes="120x120" href="../../themes/pages/ico/120.png">
  <link rel="apple-touch-icon" sizes="152x152" href="../../themes/pages/ico/152.png">
  <link rel="icon" type="image/x-icon" href="favicon.ico" />
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-touch-fullscreen" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="default">
  <meta content="" name="description" />
  <meta content="" name="author" />
  <link href="../../themes/assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" />
  <link href="../../themes/assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link href="../../themes/assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
  <link href="../../themes/assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css" media="screen" />
  <link href="../../themes/assets/plugins/bootstrap-select2/select2.css" rel="stylesheet" type="text/css" media="screen" />
  <link href="../../themes/assets/plugins/switchery/css/switchery.min.css" rel="stylesheet" type="text/css" media="screen" />
  <link href="../../themes/assets/plugins/mapplic/css/mapplic.css" rel="stylesheet" type="text/css" />
  <link href="../../themes/pages/css/pages-icons.css" rel="stylesheet" type="text/css">
  <link class="main-stylesheet" href="../../themes/pages/css/pages.css" rel="stylesheet" type="text/css" />
  <!--[if lte IE 9]>
  <link href="../../themes/assets/plugins/codrops-dialogFx/dialog.ie.css" rel="stylesheet" type="text/css" media="screen" />
  <![endif]-->
</head>

<body class="fixed-header no-header">
  <div class="page-container " style="padding-left: 0">
    <div class="header transparent">
      <div class="container-fluid relative">
        <div class="pull-left full-height visible-sm visible-xs">
          <div class="header-inner">
            <a href="#" class="btn-link toggle-sidebar visible-sm-inline-block visible-xs-inline-block padding-5" data-toggle="sidebar">
              <span class="icon-set menu-hambuger"></span>
            </a>
          </div>
        </div>
        <div class="pull-center hidden-md hidden-lg">
          <div class="header-inner">
            <div class="brand inline">
              <strong style="font-size: 16px; color: #000;display: inline-block;font-style: italic;">SOPAN Smart Office</strong>
            </div>
          </div>
        </div>
        <div class="pull-right full-height visible-sm visible-xs">
          <div class="header-inner">
            <a href="#" class="btn-link visible-sm-inline-block visible-xs-inline-block" data-toggle="quickview" data-toggle-element="#quickview">
              <span class="icon-set menu-hambuger-plus"></span>
            </a>
          </div>
        </div>
      </div>
      <div class="pull-left sm-table hidden-xs hidden-sm">
        <div class="header-inner">
          <div class="brand inline" style="text-align: left; padding-left: 50px;">
            <strong style="font-size: 16px; color: #000;display: inline-block;font-style: italic;padding-left:0px;">SOPAN Smart Office</strong>
            <br /><span style="padding-left:0px;">Integrated Smart System</span>
          </div>
        </div>
      </div>
      <div class=" pull-right">
        <div class="visible-lg visible-md m-t-10">
          <div class="pull-left p-r-10 p-t-10 fs-16 font-heading">
            <span class="semi-bold"><?php echo $_SESSION['userLevelName']; ?></span>
          </div>
          <div class="dropdown pull-right">
            <button class="profile-dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="thumbnail-wrapper d32 circular inline m-t-5">
                <?php if ($karyawan->Foto == "") { ?>
                  <img src="../../themes/assets/img/profiles/avatar.jpg" width="32" height="32">
                <?php } else { ?>
                  <img src="<?php echo "https://lintasdaya.s3-ap-southeast-1.amazonaws.com/karyawan_photo_sopan/" . $query->Foto; ?>" width="32" height="32">
                <?php } ?>
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="page-content-wrapper full-height">
      <div class="content full-width full-height overlay-footer">
        <!-- START CONTENT INNER -->
        <div class="map-controls" style="display: none">
          <div class="pull-left">
            <div class="btn-group btn-group-vertical" data-toggle="buttons-radio">
              <button id="map-zoom-in" class="btn btn-success btn-xs"><i class="fa fa-plus"></i>
              </button>
              <button id="map-zoom-out" class="btn btn-success btn-xs"><i class="fa fa-minus"></i>
              </button>
            </div>
          </div>
        </div>
        <!-- Map -->
        <div class="map-container full-width full-height">
          <div id="google-map" class="full-width full-height"></div>
        </div>
        <!-- END CONTENT INNER -->
      </div>
      <div class="container-fluid container-fixed-lg pull-bottom hidden-xs">
        <div class="copyright sm-text-center" style="border-top:0">
          <p class="small no-margin pull-left sm-pull-reset">
            <span class="hint-text">SOPAN Smart Office. Copyright &copy; 2016. Build with love by </span>
            <span class="font-montserrat"><a href="">Pesona Creative</a></span>.
            <span class="hint-text"> All rights reserved. </span>
          </p>
          <div class="clearfix"></div>
        </div>
      </div>
    </div>
  </div>
  <script src="../../themes/assets/plugins/pace/pace.min.js" type="text/javascript"></script>
  <script src="../../themes/assets/plugins/jquery/jquery-1.11.1.min.js" type="text/javascript"></script>
  <script src="../../themes/assets/plugins/modernizr.custom.js" type="text/javascript"></script>
  <script src="../../themes/assets/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
  <script src="../../themes/assets/plugins/boostrapv3/js/bootstrap.min.js" type="text/javascript"></script>
  <script src="../../themes/assets/plugins/jquery/jquery-easy.js" type="text/javascript"></script>
  <script src="../../themes/assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
  <script src="../../themes/assets/plugins/jquery-bez/jquery.bez.min.js"></script>
  <script src="../../themes/assets/plugins/jquery-ios-list/jquery.ioslist.min.js" type="text/javascript"></script>
  <script src="../../themes/assets/plugins/jquery-actual/jquery.actual.min.js"></script>
  <script src="../../themes/assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js"></script>
  <script type="text/javascript" src="../../themes/assets/plugins/bootstrap-select2/select2.min.js"></script>
  <script type="text/javascript" src="../../themes/assets/plugins/classie/classie.js"></script>
  <script src="../../themes/assets/plugins/switchery/js/switchery.min.js" type="text/javascript"></script>
  <script src="../../themes/assets/plugins/mapplic/js/hammer.js"></script>
  <script src="../../themes/assets/plugins/mapplic/js/jquery.mousewheel.js"></script>
  <script src="../../themes/assets/plugins/mapplic/js/mapplic.js"></script>

  <script src="../../themes/pages/js/pages.min.js"></script>
  <script type="text/javascript" src="http://maps.google.com/maps/api/js?v=3&sensor=false&key=<?php echo GOOGLE_API_KEY; ?>"></script>
  <script src="../../themes/assets/js/prettify.js" type="text/javascript"></script>
  <script src="../../themes/assets/js/gmaps.js" type="text/javascript"></script>
  <script type="text/javascript">
    var map;
    $(document).ready(function() {
      prettyPrint();
      map = new GMaps({
        div: '#google-map',
        lat: -8.4503776,
        lng: 115.3153362,
        zoom: 9,
      });
      <?php
      $query = $db->get_results("SELECT * FROM tb_proyek WHERE STATUS='2' AND Longitute IS NOT NULL AND Latitute IS NOT NULL");
      if ($query) {
        foreach ($query as $data) {
          $pelanggan = $db->get_row("SELECT * FROM tb_pelanggan WHERE IDPelanggan='" . $data->IDClient . "'");
      ?>
          map.addMarker({
            lat: <?php echo $data->Latitute; ?>,
            lng: <?php echo $data->Longitute; ?>,
            title: '<?php echo $data->KodeProyek . "/", $data->Tahun; ?>',
            infoWindow: {
              content: `<p><strong>Kode Proyek: </strong> <?php echo $data->KodeProyek; ?><br/>
              <strong>Tahun: </strong>  <?php echo $data->Tahun; ?><br/>
              <strong>Nama Proyek: </strong> <?php echo $data->NamaProyek; ?><br/>  <br/>

              <strong>Client:</strong><br/>
              <?php echo $pelanggan->NamaPelanggan; ?><br/>
              <?php if ($pelanggan->Alamat != "-" && $pelanggan->Alamat != "") echo $pelanggan->Alamat . "<br/>"; ?>
              <?php if ($pelanggan->Provinsi != "-" && $pelanggan->Provinsi != "") echo $pelanggan->Provinsi; ?>
              </p>
              `
            }
          });
      <?php
        }
      }
      ?>

    });
  </script>
  <style type="text/css">
    .gm-bundled-control>div {
      top: 50px !important;
    }

    .gm-style div:nth-child(11) {
      top: 50px !important;
    }
  </style>
</body>

</html>