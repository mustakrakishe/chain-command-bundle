<?php

namespace Mustakrakishe\FooBundle;

use Mustakrakishe\FooBundle\DependencyInjection\MustakrakisheFooExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class MustakrakisheFooBundle extends AbstractBundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new MustakrakisheFooExtension();
    }
}
