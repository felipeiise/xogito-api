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
    $page_title = 'Users';
    require_once 'head.php';
    ?>
    <script>
        function fill(user){
            // nothing to fill here
        }

        // to use this function you have to uncomment check_login.js line 14
        // function check(roles) {
        //     if (!roles.includes('Administrator')) {
        //         window.location.href = '/';
        //     }
        // }

        $(document).on('click', '.deactivate_user', function (){
            let id = $(this).data('user');
            $('#modal_id').val(id);
        });

        $(document).ready(function() {

            // get users
            $.ajax({
                method: 'GET',
                url: API_URL + '/users'
            }).done(function(data) {
                let users = JSON.parse(data);
                let total = users.length;

                if (total >= 1) {
                    let result = '';
                    $(users).each(function(index, user) {
                        result += '<tr>'
                        result += '<td><img src="../dist/img/person-dash.svg" title="Deactivate user" alt="Deactivate user" data-bs-toggle="modal" data-bs-target="#deactivateModal" width="24" height="24" class="deactivate_user" data-user="' + user.id + '"></td>';
                        result += '<td>' + user.id + '</td>';
                        result += '<td>' + user.full_name + '</td>';
                        result += '<td>' + user.email + '</td>';
                        result += '<td>' + user.created_at + '</td>';
                        result += '</tr>'
                    });
                    $('#results').html(result);
                    $('#users').removeClass('d-none');
                } else {
                    $('#users').addClass('d-none');
                    $('#no_records').removeClass('d-none');
                }
            });

        });

        $(document).on('click', '.modal_deactivate', function (){
            let id = $('#modal_id').val();
            $.ajax({
                method: 'PATCH',
                url: API_URL +'/user/deactivate',
                data: JSON.stringify({id: id})
            }).done(function(data) {
                if (data.status === 'invalid_operation') {
                    let alert_type = 'danger';
                    $('#alert_message').html(data.message);
                    $('#alert').addClass('alert-' + alert_type).removeClass('d-none').data('type', alert_type);
                }
                if (data.status === 'success') {
                    window.location.href = '/users';
                }
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
                <h1 class="h2">Users</h1>
            </div>
            <?php
            $breadcrumb = array(
                'Dashboard' => 'dashboard',
                'Users' => '',
            );
            require_once 'breadcrumb.php';
            ?>
            <div class="mb-3">
                <a class="btn btn-outline-primary" href="/users-add" role="button">Add a new user</a>
            </div>
            <div class="d-none alert alert-dismissible show" id="alert" role="alert">
                <span id="alert_message"></span>
                <button type="button" class="btn-close" id="alert_hide" aria-label="Close"></button>
            </div>
            <div id="no_records" class="d-none">
                <h5 class="h5">No records found.</h5>
            </div>
            <div class="modal fade" id="deactivateModal" tabindex="-1" aria-labelledby="deactivateModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="deactivateModalLabel">Do you want to deactivate this user?</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" id="modal_id" value="">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-danger modal_deactivate" data-bs-dismiss="modal">Deactivate</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive d-none" id="users">
                <table class="table table-striped table-hover table-sm">
                    <thead>
                    <tr>
                        <th class="min" scope="col"></th>
                        <th scope="col">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Registration Date</th>
                    </tr>
                    </thead>
                    <tbody id="results">
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>
<?php require_once 'footer.php';?>
</body>
</html>