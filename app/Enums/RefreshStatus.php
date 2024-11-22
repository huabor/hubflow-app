<?php

declare(strict_types=1);

namespace App\Enums;

enum RefreshStatus: int
{
    case NEW = 0;
    case DONE = 1;
    case QUEUED = 2;
    case RUNNING = 3;
}
