<?php

declare(strict_types=1);

namespace Sai;

enum OS: string
{
    case Debian = 'debian';
    case Bookworm = 'bookworm';
    case Bullseye = 'bullseye';

    case Alpine = 'alpine';
    case Alpine319 = 'alpine3.19';
    case Alpine318 = 'alpine3.18';

    public function isAlpine(): bool
    {
        return in_array($this, [OS::Alpine, OS::Alpine318, OS::Alpine319]);
    }


    public function isDebian(): bool
    {
        return in_array($this, [OS::Debian, OS::Bookworm, OS::Bullseye]);
    }
}