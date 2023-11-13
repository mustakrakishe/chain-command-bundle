<?php

namespace Mustakrakishe\BarBundle;

use Mustakrakishe\BarBundle\DependencyInjection\MustakrakisheBarExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class MustakrakisheBarBundle extends AbstractBundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new MustakrakisheBarExtension();
    }
}
