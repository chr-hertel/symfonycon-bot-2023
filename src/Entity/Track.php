<?php

declare(strict_types=1);

namespace App\Entity;

enum Track: string
{
    case SymfonyRoom = 'Symfony room';
    case SensioLabsRoom = 'SensioLabs room';
    case PlatformRoom = 'Platform.sh room';
}
