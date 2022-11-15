<!doctype html>
<html lang="en">
<head>
    <?php
    $page_title = 'Dashboard';
    require_once 'head.php';
    ?>
    <script>
        function fill(user){
            $('#full_name').html(user.full_name);
        }
    </script>
</head>
<body>
<?php
require_once 'header.php';
?>
<div class="container-fluid">
    <div class="row">
        <?php require_once 'menu.php';?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Welcome <span id="full_name"></span> to <?php echo COMPANY_NAME;?> Dashboard!</h1>
            </div>
            <?php
            $breadcrumb = array(
                'Dashboard' => ''
            );
            require_once 'breadcrumb.php';
            ?>
        </main>
    </div>
</div>
<?php require_once 'footer.php';?>
</body>
</html>