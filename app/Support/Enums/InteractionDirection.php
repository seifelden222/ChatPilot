<?php

namespace App\Support\Enums;

enum InteractionDirection: string
{
    case Inbound = 'inbound';
    case Outbound = 'outbound';
    case System = 'system';
}
