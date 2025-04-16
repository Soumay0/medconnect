<?php
function get_navigation() {
    $current_page = basename($_SERVER['PHP_SELF']);
    $is_admin = isset($_SESSION['admin_id']);
    
    $nav = [
        'home' => [
            'url' => $is_admin ? 'admin/dashboard.php' : 'index.php',
            'icon' => $is_admin ? 'fa-tachometer-alt' : 'fa-home',
            'text' => $is_admin ? 'Dashboard' : 'Home'
        ],
        'doctors' => [
            'url' => $is_admin ? 'admin/doctors.php' : 'doctors.php',
            'icon' => 'fa-user-md',
            'text' => 'Doctors'
        ],
        'appointments' => [
            'url' => $is_admin ? 'admin/appointments.php' : 'appointments.php',
            'icon' => 'fa-calendar-check',
            'text' => 'Appointments'
        ]
    ];
    
    foreach ($nav as $key => $item) {
        $active = ($current_page == basename($item['url'])) ? 'active' : '';
        echo <<<HTML
        <a href="{$item['url']}" class="$active">
            <i class="fas {$item['icon']}"></i> {$item['text']}
        </a>
HTML;
    }
}
?>