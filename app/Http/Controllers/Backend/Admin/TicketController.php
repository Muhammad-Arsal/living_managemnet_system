<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Backend\Tickets\PortalTicketController;

class TicketController extends PortalTicketController
{
    protected function portal(): string
    {
        return 'admin';
    }

    protected function allowsCreate(): bool
    {
        return false;
    }
}
