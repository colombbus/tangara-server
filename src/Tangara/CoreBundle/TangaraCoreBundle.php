<?php

namespace Tangara\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TangaraCoreBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
