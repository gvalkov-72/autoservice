<?php

return [

    'title' => 'Автосервиз',
    'title_prefix' => '',
    'title_postfix' => '',

    'use_ico_only' => false,
    'use_full_favicon' => false,
    'google_fonts' => ['allowed' => true],

    'logo' => '<b>Авто</b>Сервиз',
    'logo_img' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_alt' => 'Лого',

    'auth_logo' => ['enabled' => false],
    'preloader' => ['enabled' => false],

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_image' => false,
    'usermenu_desc' => false,

    'layout_topnav' => false,
    'layout_boxed' => false,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => true,
    'layout_fixed_footer' => false,
    'layout_dark_mode' => false,

    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_topnav' => 'navbar-white navbar-light',

    'dashboard_url' => 'dashboard',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => false,
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,

    /* ---------- МЕНЮ ---------- */
    'menu' => [
        ['header' => 'ОСНОВНИ МОДУЛИ'],
        [
            'text' => 'Клиенти',
            'url'  => 'admin/customers',
            'icon' => 'fas fa-fw fa-users',
        ],
        [
            'text' => 'Автомобили',
            'url'  => 'admin/vehicles',
            'icon' => 'fas fa-fw fa-car',
        ],
        [
            'text' => 'Артикули',
            'url'  => 'admin/products',
            'icon' => 'fas fa-fw fa-cubes',
        ],
        [
            'text' => 'Услуги',
            'url'  => 'admin/services',
            'icon' => 'fas fa-fw fa-wrench',
        ],
        [
            'text' => 'Поръчки',
            'url'  => 'admin/work-orders',
            'icon' => 'fas fa-fw fa-clipboard-list',
        ],
        [
            'text' => 'Фактури',
            'url'  => 'admin/invoices',
            'icon' => 'fas fa-fw fa-file-invoice-dollar',
        ],
        [
            'text' => 'Плащания',
            'url'  => 'admin/payments',
            'icon' => 'fas fa-fw fa-money-bill-wave',
        ],

        ['header' => 'СКЛАД И ОТЧЕТИ'],
        [
            'text' => 'Наличности',
            'url'  => 'admin/stock',
            'icon' => 'fas fa-fw fa-boxes',
        ],
        [
            'text' => 'Месечен отчет',
            'url'  => 'admin/reports/monthly',
            'icon' => 'fas fa-fw fa-chart-line',
        ],

        ['header' => 'АДМИНИСТРАЦИЯ'],
        [
            'text' => 'Потребители',
            'url'  => 'admin/users',
            'icon' => 'fas fa-fw fa-users-cog',
            'can'  => 'admin',
        ],
        [
            'text' => 'Роли',
            'url'  => 'admin/roles',
            'icon' => 'fas fa-fw fa-user-shield',
            'can'  => 'admin',
        ],
        [
            'text' => 'Права',
            'url'  => 'admin/permissions',
            'icon' => 'fas fa-fw fa-key',
            'can'  => 'admin',
        ],

        ['header' => 'СИСТЕМНИ НАСТРОЙКИ'],
        [
            'text' => 'Данни на Автосервиза',
            'url'  => 'admin/company-settings',
            'icon' => 'fas fa-fw fa-cog',
        ],
        [
            'text' => 'Изход',
            'url'  => 'logout',
            'icon' => 'fas fa-fw fa-sign-out-alt',
        ],
    ],

    /* ---------- PLUGINS ---------- */
    'plugins' => [
        'Datatables' => [
            'active' => true,
            'files'  => [
                ['type' => 'css', 'asset' => false, 'location' => '//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css'],
                ['type' => 'js',  'asset' => false, 'location' => '//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js'],
            ],
        ],
        'Select2' => [
            'active' => true,
            'files'  => [
                ['type' => 'css', 'asset' => false, 'location' => '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'],
                ['type' => 'js',  'asset' => false, 'location' => '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'],
            ],
        ],
    ],

    'livewire' => false,
];
