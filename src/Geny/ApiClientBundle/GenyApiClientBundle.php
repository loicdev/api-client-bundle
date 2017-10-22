<?php

namespace Geny\ApiClientBundle;

use Geny\ApiClientBundle\DependencyInjection\Compiler\GuzzleCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GenyApiClientBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

    }
}
