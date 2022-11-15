<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="../dist/img/favicon.ico"/>
    <meta name="description" content="">
    <meta name="author" content="<?php echo AUTHOR;?>">
    <title>Create your account - <?php echo COMPANY_NAME;?></title>
    <link href="../dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="../dist/js/jquery-3.6.1.min.js"></script>
    <script src="../dist/js/custom.js"></script>
    <script>
        $(document).ready(function() {

            $('#alert_hide').on('click', function() {
                let alert = $('#alert');
                let alert_type = alert.data('type');
                alert.removeClass('alert-' + alert_type).addClass('d-none');
            });

            $('#register').submit(function(event) {
                let data_post = {
                    full_name: $('#full_name').val(),
                    email: $('#email').val(),
                    password: $('#password').val()
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
                        data.status === 'invalid_password'
                    ) {
                        let alert_type = 'danger';
                        $('#alert_message').html(data.message);
                        alert.addClass('alert-' + alert_type).removeClass('d-none').data('type', alert_type);
                    } else {
                        alert.addClass('d-none');
                        window.location.href = 'qrcode';
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
    <form id="register">
        <img class="mb-4" src="../dist/img/xogito.svg" alt="" width="202" height="70">
        <h1 class="h3 mb-3 fw-normal text-light">Create your account</h1>
        <div class="d-none alert alert-dismissible show" id="alert" role="alert">
            <span id="alert_message"></span>
            <button type="button" class="btn-close" id="alert_hide" aria-label="Close"></button>
        </div>
        <div class="form-floating">
            <input type="text" class="form-control rounded-0 rounded-top" id="full_name" placeholder="John Doe">
            <label for="full_name">Full name</label>
        </div>
        <div class="form-floating">
            <input type="email" class="form-control rounded-0" id="email" placeholder="name@example.com">
            <label for="email">Email address</label>
        </div>
        <div class="form-floating mb-3">
            <input type="password" class="form-control" id="password" placeholder="Password">
            <label for="password">Password</label>
        </div>
        <button class="w-100 btn btn-lg btn-success mb-3" type="submit">Continue</button>
        <div class="form-floating text-light">Already have an account?</div>
        <div class="form-floating"><a href="/signin">Sign in</a></div>
        <?php require_once 'copyright.php';?>
    </form>
</main>
</body>
</html>