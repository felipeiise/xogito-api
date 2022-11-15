<?php
$nav_links = [
    '/dashboard' => [
        'icon' => 'home', 'title' => 'Dashboard',
        'allowed_roles' => ['Administrator', 'User']
    ],
    '/my-account' => [
        'icon' => 'file-text', 'title' => 'My Account',
        'allowed_roles' => ['Administrator', 'User']
    ],
    '/users' => [
        'icon' => 'users', 'title' => 'Users',
        'allowed_roles' => ['Administrator']
    ]
];

$nav_link = '';
foreach ($nav_links as $nav_key => $nav_value) {
    if (array_intersect($nav_value['allowed_roles'],USER_ROLES)) {
        $nav_link .= '<li class="nav-item">';
        $nav_link .= '<a class="nav-link" href="' . $nav_key . '">';
        $nav_link .= '<span data-feather="' . $nav_value['icon'] . '" class="align-text-bottom"></span>';
        $nav_link .= $nav_value['title'];
        $nav_link .= '</a>';
        $nav_link .= '</li>';
    }
}
?>
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3 sidebar-sticky">
        <ul class="nav flex-column">
            <?php echo $nav_link;?>
        </ul>
    </div>
</nav>