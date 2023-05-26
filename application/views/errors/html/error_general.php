<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Terjadi kesalahan">
    <meta name="keywords" content="">
    <meta name="author" content="<?= $_ENV['APP_NAME'] ?>">
    <title>ERROR :: <?= $_ENV['APP_NAME'] ?></title>
    <link rel="apple-touch-icon" href="<?= asset('uploads/img_app/' . APP_FAVICON_APPLE) ?>">
	  <link rel="shortcut icon" type="image/x-icon" href="<?= asset('uploads/img_app/' . APP_FAVICON) ?>">
    <link href="<?= base_url('assets/npm/node_modules/typeface-muli/index.css') ?>" rel="stylesheet">
  	<link href="<?= base_url('assets/npm/node_modules/typeface-open-sans/index.css') ?>" rel="stylesheet">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/template/robust/') ?>app-assets/css/vendors.css">
    <!-- END VENDOR CSS-->
    <!-- BEGIN ROBUST CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/template/robust/') ?>app-assets/css/app.css">
    <!-- END ROBUST CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/template/robust/') ?>app-assets/css/core/menu/menu-types/vertical-compact-menu.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/template/robust/') ?>app-assets/css/core/colors/palette-gradient.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/template/robust/') ?>assets/css/style.css">
    <!-- END Custom CSS-->
	  <style type="text/css">
		  .cover_body {
			    background-image: url(<?= base_url('assets/template/robust/') ?>app-assets/images/backgrounds/bg-9.jpg);
			    background-repeat: no-repeat;
			    -webkit-background-size: cover;
			    background-size: cover;
			}
	  </style>
  </head>
  <body class="vertical-layout vertical-compact-menu 1-column  bg-maintenance-image menu-expanded blank-page blank-page cover_body" data-open="click" data-menu="vertical-compact-menu" data-col="1-column">
    <!-- ////////////////////////////////////////////////////////////////////////////-->
    <div class="app-content content">
      <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body"><section class="flexbox-container">
    <div class="col-12 d-flex align-items-center justify-content-center">
        <div class="col-md-4 col-10 box-shadow-2 p-0">
            <div class="card border-grey border-lighten-3 px-1 py-1 box-shadow-3 m-0">
                <div class="card-body">
                    <span class="card-title text-center">
            			<img src="<?= asset('uploads/img_app/' . APP_LOGO) ?>" class="img-fluid mx-auto d-block pt-2" width="120" alt="logo">
            		</span>
                </div>
                <div class="card-body text-center">
<!--                    <h3>This page is under maintenance</h3>-->
	                <h3><?php echo $heading; ?></h3>
<!--                    <p>We're sorry for the inconvenience.-->
<!--                        <br> Please check back later.</p>-->
	                <p>
	                <?php echo $message; ?>
	                </p>
	                
<!--                    <div class="mt-2"><i class="fa fa-cog spinner font-large-2"></i></div>-->
                </div>
                <hr>
                <p class="socialIcon card-text text-center pt-2 pb-2">
<!--                    <a href="#" class="btn btn-social-icon mr-1 mb-1 btn-outline-facebook"><span class="fa fa-facebook"></span></a>-->
<!--                    <a href="#" class="btn btn-social-icon mr-1 mb-1 btn-outline-twitter"><span class="fa fa-twitter"></span></a>-->
<!--                    <a href="#" class="btn btn-social-icon mr-1 mb-1 btn-outline-linkedin"><span class="fa fa-linkedin font-medium-4"></span></a>-->
<!--                    <a href="#" class="btn btn-social-icon mr-1 mb-1 btn-outline-github"><span class="fa fa-github font-medium-4"></span></a>-->
	                <a href="<?= site_url('/dashboard') ?>">Kembali</a>
                </p>
            </div>
        </div>
    </div>
</section>
        </div>
      </div>
    </div>
    <!-- ////////////////////////////////////////////////////////////////////////////-->

    <!-- BEGIN VENDOR JS-->
    <script src="<?= base_url('assets/template/robust/') ?>app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->
    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN ROBUST JS-->
    <script src="<?= base_url('assets/template/robust/') ?>app-assets/js/core/app-menu.js"></script>
    <script src="<?= base_url('assets/template/robust/') ?>app-assets/js/core/app.js"></script>
    <!-- END ROBUST JS-->
    <!-- BEGIN PAGE LEVEL JS-->
    <!-- END PAGE LEVEL JS-->
  </body>
</html>
