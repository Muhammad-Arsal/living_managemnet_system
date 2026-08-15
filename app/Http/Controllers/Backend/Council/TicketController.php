<?php

namespace App\Http\Controllers\Backend\Council;

use App\Http\Controllers\Backend\Tickets\PortalTicketController;

class TicketController extends PortalTicketController
{
    protected function portal(): string
    {
        return 'council';
    }
}
