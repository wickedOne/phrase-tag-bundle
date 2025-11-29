<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use WickedOne\PhraseTagBundle\Service\PhraseTaggerFactory;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set(PhraseTaggerFactory::class)
        ->args([
            service('http_client'),
            service('monolog.logger'),
        ]);
};
