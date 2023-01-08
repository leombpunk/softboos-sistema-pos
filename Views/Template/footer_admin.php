    <script>
        const base_url = "<?= base_url();?>";
    </script>
    <!-- Essential javascripts for application to work-->
    <script src="<?= media(); ?>js/jquery-3.3.1.min.js"></script>
    <script src="<?= media(); ?>js/popper-1.14.7.min.js"></script>
    <!-- <script src="<?//= media(); ?>js/bootstrap.min.js"></script> -->
    <script src="<?= media(); ?>css/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="<?= media(); ?>js/main.js"></script>
    <script src="<?= media(); ?>js/plugins/jquery.dataTables.min.js"></script>
    <script src="<?= media(); ?>js/plugins/dataTables.bootstrap.min.js"></script>
    <!-- <script type="text/javascript" src="<?//= media(); ?>js/plugins/DataTables/datatables.min.js"></script> -->
    <script src="<?= media(); ?>js/plugins/select2.min.js"></script>
    <script src="<?= media(); ?>css/select-bootstrap/js/bootstrap-select.min.js"></script>
    <script src="<?= media(); ?>js/plugins/bootstrap-notify.min.js" type="text/javascript"></script>
    <script src="<?= media(); ?>js/plugins/sweetalert.min(2).js"></script>
    <script src="<?= media(); ?>js/plugins/pace.min.js"></script>
    <script src="<?= media(); ?>js/plugins/dropzone.js" type="text/javascript"></script>
    <script src="<?= media(); ?>js/jquery.inputmask.min.js" type="text/javascript"></script>
    <script src="<?= media(); ?>js/function_admin.js"></script>
    <!-- Page specific javascripts-->
    <?php 
    if (isset($data["page_specialjs"])){
        foreach ($data["page_specialjs"] as $key => $value) {
            echo $value;
        }
    }
    ?>
    <script src="<?= media(); ?>js/<?= $data["page_filejs"];?>"></script>
    
</body>
</html>