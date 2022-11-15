<?php

declare(strict_types=1);

if (isset($_COOKIE['jwt'])) {
    unset($_COOKIE['jwt']);
    setcookie('jwt', '', -1, '/');
}

?>
<script src="../dist/js/jquery-3.6.1.min.js"></script>
<script>
    $(document).ready(function() {
        sessionStorage.clear();
        window.location.href = '/signin';
    });
</script>