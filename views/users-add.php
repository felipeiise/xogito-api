<?php

declare(strict_types=1);

if (!in_array('Administrator', USER_ROLES)) {
    header('Location: /');
    exit();
}
?>
<!doctype html>
<html lang="en">
<head>
    <?php
    $page_title = 'Add a new user';
    require_once 'head.php';
    ?>
    <script>
        function fill(user){
            // nothing to fill here
        }

        $(document).on('click', '.deactivate_user', function (){
            let id = $(this).data('user');
            $('#modal_id').val(id);
        });

        $(document).ready(function() {

            // get roles
            $.ajax({
                method: 'GET',
                url: API_URL + '/roles'
            }).done(function(data) {
                let roles = JSON.parse(data);
                let total = roles.length;

                if (total >= 1) {
                    let result = '';
                    $(roles).each(function(index, role) {
                        $('#role').append($('<option>', {
                            value: role.id,
                            text: role.name
                        }));
                    });
                }
            });

            // add a new user
            $('#register').submit(function(event) {
                let data_post = {
                    full_name: $('#full_name').val(),
                    email: $('#email').val(),
                    password: $('#password').val(),
                    role: $('#role').val()
                }
                $.ajax({
                    method: 'POST',
                    url: API_URL + '/user',
                    data: JSON.stringify(data_post)
                }).done(function(data) {
                    let alert = $('#alert');
                    if (
                        data.status === 'email_already_used' ||
                        data.status === 'invalid_name' ||
                        data.status === 'invalid_email' ||
                        data.status === 'invalid_password' ||
                        data.status === 'invalid_permissions'
                    ) {
                        let alert_type = 'danger';
                        $('#alert_message').html(data.message);
                        alert.addClass('alert-' + alert_type).removeClass('d-none').data('type', alert_type);
                    } else {
                        alert.addClass('d-none');
                        window.location.href = '/users';
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
                <h1 class="h2">Add a new user</h1>
            </div>
            <?php
            $breadcrumb = array(
                'Dashboard' => 'dashboard',
                'Users' => 'users',
                'Add a new user' => ''
            );
            require_once 'breadcrumb.php';
            ?>
            <div class="d-none alert alert-dismissible show" id="alert" role="alert">
                <span id="alert_message"></span>
                <button type="button" class="btn-close" id="alert_hide" aria-label="Close"></button>
            </div>
            <div class="col-sm-5">
                <form class="mb-3" id="register">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="full_name">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password">
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Select the user role</label>
                        <select class="form-select" aria-label="Select the user role" id="role">
                            <option selected></option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </main>
    </div>
</div>
<?php require_once 'footer.php';?>
</body>
</html>