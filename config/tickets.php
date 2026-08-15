<?php

use App\Models\Admin;
use App\Models\Staff;

return [

    /*
    |--------------------------------------------------------------------------
    | Ticket actor capabilities
    |--------------------------------------------------------------------------
    |
    | Portals listed here can create tickets or view/reply to every ticket.
    | A future Tenant portal is integrated by adding its morph alias, these
    | capability lists, an assignable mapping, routes, and panel views.
    |
    */

    'creators' => [
        'council',
        'staff',
    ],

    'view_all' => [
        'admin',
    ],

    'reply_all' => [
        'admin',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default assignee portal per creator
    |--------------------------------------------------------------------------
    |
    | Council assigns Staff. Staff assigns Admin. Keys are portal keys from
    | config/portals.php (and Relation morph map aliases).
    |
    */

    'assignable' => [
        'council' => [
            'portal' => 'staff',
            'model' => Staff::class,
        ],
        'staff' => [
            'portal' => 'admin',
            'model' => Admin::class,
        ],
    ],

    'email_types' => [
        'created' => 'ticket_created',
        'replied' => 'ticket_replied',
    ],

    'attachments' => [
        'disk' => 'local',
        'max_files' => 10,
        'max_kilobytes' => 10240,
        'mimes' => ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'csv', 'zip'],
    ],

];
