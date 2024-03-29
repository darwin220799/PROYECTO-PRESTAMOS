<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CREDIMOTOS</title>
	<link rel="shortcut icon" href="<?=site_url().'assets/img/favicon-32x32.png'?>">
    <!-- Custom fonts for this template--> 
    <link href="<?php echo site_url() ?>assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?php echo site_url() ?>assets/css/sb-admin-2.css" rel="stylesheet">
    <link href="<?php echo site_url() ?>assets/css/style-2.css" rel="stylesheet">
    <link href="<?php echo site_url() ?>assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="<?php echo site_url() ?>assets/css/style.css" rel="stylesheet">
    <!-- cargar url para leer css para impresiones -->  
    <script> 
        const print_style = "<?php  echo site_url()?>assets/css/print.css" 
    </script>
    <script type="text/javascript">base_url = '<?= base_url();?>'</script>

    <!-- Agregar toastr -->
    <link href="<?php echo site_url() ?>assets/libs/toastr/toastr.min.css" rel="stylesheet">
    <!-- <script src="<?php echo site_url() ?>assets/libs/toastr/toastr.min.js"></script> -->

    <!-- AGREGAR sweetalert2-11.4.29 (Alertas con estilos)-->
    <script src="<?php echo site_url() ?>assets/libs/sweetalert2-11.4.29/dist/sweetalert2.min.js"></script>
    <link href="<?php echo site_url() ?>assets/libs/sweetalert2-11.4.29/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- AGREGAR SELECT 2  SELECT CON BUSCADOR-->
    <script src="<?php echo site_url() ?>assets/libs/jquery-3.6.0.min.js"></script>
    <!-- SELECT2 sin CDN (incorporado en el proyecto) -->
    <link href="<?php echo site_url() ?>assets/libs/select2-4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="<?php echo site_url(); ?>assets/libs/select2-4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- CDN de JQUERY -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script> -->
    <!-- Select2 sin CDN (incorporado en el proyecto) -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> -->

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php $this->load->view('admin/components/sidebar'); ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php $this->load->view('admin/components/navbar'); ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                  <?php $this->load->view($subview); ?>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; DWG</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="<?php echo site_url() ?>assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?php echo site_url() ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?php echo site_url() ?>assets/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?php echo site_url() ?>assets/js/sb-admin-2.min.js"></script>

    <script src="<?php echo site_url() ?>assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo site_url() ?>assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
      $(document).ready(function() {
        $('#dataTable').DataTable({
          "order": [],
        });
      });
    </script> 

    <script src="<?php echo site_url(); ?>assets/js/script.js"></script>

    <script src="<?php echo site_url() ?>assets/libs/toastr/toastr.min.js"></script>
</body>

</html>