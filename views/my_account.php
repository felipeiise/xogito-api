<!doctype html>
<html lang="en">
<head>
    <?php
    $page_title = 'My Account';
    require_once 'head.php';
    ?>
    <script>
        function fill(user){
            $('#full_name').val(user.full_name);
        }

        $(document).ready(function() {
            $('#update').submit(function(event) {
                let full_name = $('#full_name').val();
                $.ajax({
                    method: 'PATCH',
                    url: API_URL + '/user',
                    data: JSON.stringify({
                        full_name: full_name
                    })
                }).done(function(data) {
                    if (data.status === 'invalid_name') {
                        let alert_type = 'danger';
                        $('#alert_message').html(data.message);
                        $('#alert').addClass('alert-' + alert_type).removeClass('d-none').data('type', alert_type);
                    }
                    if (data.status === 'success') {
                        let alert_type = 'success';
                        $('#alert_message').html(data.message);
                        $('#alert').addClass('alert-' + alert_type).removeClass('d-none').data('type', alert_type);
                        let fields = {'full_name': full_name};
                        updateSessionStorage('user', fields);
                    }
                });
                event.preventDefault();
            });
        });
    </script>
</head>
<body>
<?php require_once 'header.php';?>
<div class="container-fluid">
    <div class="row">
        <?php require_once 'menu.php';?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Edit your account information</h1>
            </div>
            <?php
            $breadcrumb = [
                'Dashboard' => 'dashboard',
                'My Account' => '',
            ];
            require_once 'breadcrumb.php';
            ?>
            <form id="update">
                <div class="d-none alert alert-dismissible show" id="alert" role="alert">
                    <span id="alert_message"></span>
                    <button type="button" class="btn-close" id="alert_hide" aria-label="Close"></button>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="full_name" value="" placeholder="Full name">
                    <label for="full_name">Full name</label>
                </div>
                <button class="w-100 btn btn-lg btn-primary mb-3" type="submit">Save</button>
            </form>
        </main>
    </div>
</div>
<?php require_once 'footer.php';?>
</body>
</html>