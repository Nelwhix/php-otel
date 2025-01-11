<?php

namespace App\Enums;

enum OrderStatus: int
{
    case Pending = 0;
    case Processing = 1;
    case Shipped = 2;
    case Delivered = 3;
    case Cancelled = 4;
}
