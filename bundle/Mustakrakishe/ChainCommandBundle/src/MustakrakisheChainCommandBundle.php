<?php

namespace Mustakrakishe\ChainCommandBundle;

use Mustakrakishe\ChainCommandBundle\DependencyInjection\MustakrakisheChainCommandExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class MustakrakisheChainCommandBundle extends AbstractBundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new MustakrakisheChainCommandExtension();
    }
}
