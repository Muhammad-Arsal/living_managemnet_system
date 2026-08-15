<?php

namespace App\Http\Controllers\Backend\Staff;

use App\Http\Controllers\Backend\Tickets\PortalTicketController;

class TicketController extends PortalTicketController
{
    protected function portal(): string
    {
        return 'staff';
    }
}
