<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\MonologBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Sets the transport for Swiftmailer handlers depending on the existing
 * container definitions.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class AddSwiftMailerTransportPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $handlers = $container->getParameter('monolog.swift_mailer.handlers');

        foreach ($handlers as $id) {
            $definition = $container->getDefinition($id);
            $mailerId = (string) $definition->getArgument(0);

            if (
                $container->hasAlias($mailerId . '.transport.real') ||
                $container->hasDefinition($mailerId . '.transport.real')
            ) {
                $definition->addMethodCall(
                    'setTransport',
                    array(new Reference($mailerId . '.transport.real'))
                );
            } elseif (
                $container->hasAlias($mailerId . '.transport') ||
                $container->hasDefinition($mailerId . '.transport')
            ) {
                $definition->addMethodCall(
                    'setTransport',
                    array(new Reference($mailerId . '.transport'))
                );
            }
        }
    }
}
