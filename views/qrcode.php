<!doctype html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="../dist/img/favicon.ico"/>
    <meta name="description" content="">
    <meta name="author" content="<?php echo AUTHOR;?>">
    <title>Enable MFA Authentication - <?php echo COMPANY_NAME;?></title>
    <link href="../dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../dist/css/cover.css" rel="stylesheet">
    <script src="../dist/js/jquery-3.6.1.min.js"></script>
    <script src="../dist/js/custom.js"></script>
    <script>
        $(document).ready(function(){
            $.ajax({
                method: 'GET',
                url: API_URL + '/qrcode/mfa'
            }).done(function(data) {
                if (data.status === 'success') {
                    $('#mfa_secret').html(data.mfa_secret);
                    $('#mfa_image').html('<img alt="QR Code" style="width: 300px;" class="img-thumbnail" src="data:image/png;base64, ' + data.mfa_image + '"/>');
                } else {
                    window.location.href = '/signin';
                }
            });
        });
    </script>
</head>
<body class="d-flex h-100 text-center text-white bg-dark">
<div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
    <main class="px-3">
        <h1>Enable MFA Authentication</h1>
        <p class="lead">Multi-factor authentication (MFA) provides a method to verify a user's identity by requiring them to provide more than one piece of identifying information.</p>
        <p class="lead"><a href="https://auth0.com/blog/multifactor-authentication-mfa/" target="_blank" class="btn btn-lg btn-secondary fw-bold border-white bg-white">Learn more</a></p>
        <p class="lead">Scan the image below or enter the <span class="text-info">code</span> with your preferred MFA authenticator application like Google Authenticator or Authy.</p>
        <h3><span class="text-info mb-3" id="mfa_secret" style="display: block; height: 30px;"></span></h3>
        <div class="mb-3" id="mfa_image" style="display:block;"></div>
        <div class="form-floating text-light mb-3"><h3>Did you have enabled?</h3></div>
        <a class="btn btn-primary mb-3" href="/mfa" role="button">Continue</a>
    </main>
    <footer class="mt-auto text-white-50">
        <p>PHP task - User API for <a href="https://www.xogito.com/" target="_blank" class="text-white">Xogito</a>, by <a href="https://twitter.com/felipeiise" target="_blank" class="text-white">@felipeiise</a>.</p>
    </footer>
</div>
</body>
</html>