<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="../dist/img/favicon.ico"/>
    <meta name="description" content="">
    <meta name="author" content="<?php echo AUTHOR;?>">
    <title>Signin - <?php echo COMPANY_NAME;?></title>
    <link href="../dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="../dist/js/jquery-3.6.1.min.js"></script>
    <script src="../dist/js/custom.js"></script>
    <script>
        $(document).ready(function() {
            $('#signin').submit(function(event) {
                let data_post = {
                    email: $('#email').val(),
                    password: $('#password').val()
                }
                $.ajax({
                    method: 'POST',
                    url: API_URL + '/login',
                    data: JSON.stringify(data_post)
                }).done(function(data) {
                    if (data.status === 'wrong_credentials' || data.status === 'invalid_email' || data.status === 'invalid_password') {
                        let alert_type = 'danger';
                        $('#alert_message').html(data.message);
                        $('#alert').addClass('alert-' + alert_type).removeClass('d-none').data('type', alert_type);
                    }
                    if (data.status === 'success') {
                        window.location.href = 'mfa';
                    }
                });
                event.preventDefault();
            });
        });
    </script>
    <link href="../dist/css/signin.css" rel="stylesheet">
</head>
<body class="text-center">
<main class="form-signin w-100 m-auto">
    <form id="signin">
        <img class="mb-4" src="../dist/img/xogito.svg" alt="" width="202" height="70">
        <h1 class="h3 mb-3 fw-normal text-light">Please sign in</h1>
        <div class="d-none alert alert-dismissible show" id="alert" role="alert">
            <span id="alert_message"></span>
            <button type="button" class="btn-close" id="alert_hide" aria-label="Close"></button>
        </div>
        <div class="form-floating">
            <input type="email" class="form-control" id="email" placeholder="name@example.com">
            <label for="email">Email address</label>
        </div>
        <div class="form-floating mb-3">
            <input type="password" class="form-control" id="password" placeholder="Password">
            <label for="password">Password</label>
        </div>
        <button class="w-100 btn btn-lg btn-primary mb-3" type="submit">Next</button>
        <div class="form-floating text-light">Don't have a Xogito account?</div>
        <div class="form-floating"><a href="/register">Create yours now</a></div>
        <?php require_once 'copyright.php';?>
    </form>
</main>
</body>
</html>