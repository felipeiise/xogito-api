<?php
if (!empty($breadcrumb)) {
?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <?php
        $output = '';
        foreach ($breadcrumb as $list_title => $list_link) {
            if ($list_link === '') {
                $output .= '<li class="breadcrumb-item active" aria-current="page">' . $list_title . '</li>';
            } else {
                $output .= '<li class="breadcrumb-item"><a href="/' . $list_link . '">' . $list_title . '</a></li>';
            }
        }
        echo $output;
        ?>
    </ol>
</nav>
<?php
}