<?php

declare(strict_types=1);

http_response_code(404);

?>
<!doctype html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="../dist/img/favicon.ico"/>
    <meta name="description" content="">
    <meta name="author" content="<?php echo AUTHOR;?>">
    <title>Page not found - <?php echo COMPANY_NAME;?></title>
    <link href="../dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../dist/css/cover.css" rel="stylesheet">
    <script src="../dist/js/jquery-3.6.1.min.js"></script>
</head>
<body class="d-flex h-100 text-center text-white bg-dark">
<div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
    <main class="px-3 mt-auto">
        <h1>Page not found</h1>
        <p class="lead">This page doesn’t exist.</p>
        <p class="lead"><a href="/" class="btn btn-lg btn-secondary fw-bold border-white bg-white">Home</a></p>
    </main>
    <footer class="mt-auto text-white-50">
        <p>PHP task - User API for <a href="https://www.xogito.com/" target="_blank" class="text-white">Xogito</a>, by <a href="https://twitter.com/felipeiise" target="_blank" class="text-white">@felipeiise</a>.</p>
    </footer>
</div>
</body>
</html>