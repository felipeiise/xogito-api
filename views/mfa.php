<?php
if (GLOBAL_MFA_ENABLED === 'false') {
    header('Location: dashboard');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="../dist/img/favicon.ico"/>
    <meta name="description" content="">
    <meta name="author" content="<?php echo AUTHOR;?>">
    <title>MFA Authentication - <?php echo COMPANY_NAME;?></title>
    <link href="../dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="../dist/js/jquery-3.6.1.min.js"></script>
    <script src="../dist/js/custom.js"></script>
    <script>
        $(document).ready(function() {

            $('#token').focus();

            $('#alert_hide').on('click', function() {
                let alert = $('#alert');
                let alert_type = alert.data('type');
                alert.removeClass('alert-' + alert_type).addClass('d-none');
            });

            $('#mfa').submit(function(event) {
                let data_post = {
                    token: $('#token').val()
                }
                $.ajax({
                    method: 'POST',
                    url: API_URL + '/token',
                    data: JSON.stringify(data_post)
                }).done(function(data) {
                    if (data.status === 'invalid_login') {
                        window.location.href = '/signin';
                    } else if (data.status === 'invalid_token') {
                        let alert_type = 'danger';
                        $('#alert_message').html(data.message);
                        $('#alert').addClass('alert-' + alert_type).removeClass('d-none').data('type', alert_type);
                    } else {
                        window.location.href = '/dashboard';
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
    <form id="mfa">
        <img class="mb-4" src="../dist/img/xogito.svg" alt="" width="202" height="70">
        <h1 class="h3 mb-3 fw-normal text-light">Please type your MFA Authentication token</h1>
        <div class="mb-3 form-floating"><a href="/qrcode">Didn't you enabled MFA?</a></div>
        <div class="d-none alert alert-dismissible show" id="alert" role="alert">
            <span id="alert_message"></span>
            <button type="button" class="btn-close" id="alert_hide" aria-label="Close"></button>
        </div>
        <div class="form-floating mb-3">
            <input type="tel" maxlength="6" class="form-control" id="token" autocomplete="off">
            <label for="token">Authentication code</label>
        </div>
        <button class="w-100 btn btn-lg btn-primary mb-3" type="submit">Sign in</button>
        <div class="form-floating"><a href="/signout">Sign in using another account</a></div>
        <?php require_once 'copyright.php';?>
    </form>
</main>
</body>
</html>