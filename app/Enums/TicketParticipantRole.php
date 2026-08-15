<?php

namespace App\Enums;

enum TicketParticipantRole: string
{
    case Creator = 'creator';
    case Assignee = 'assignee';
    case Subscriber = 'subscriber';
}
