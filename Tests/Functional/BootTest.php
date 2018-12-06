<?php

declare(strict_types=1);

/*
 * This file is part of the FOSOAuthServerBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\OAuthServerBundle\Tests\Functional;

use Closure;
use FOS\OAuthServerBundle\Model\Client;
use FOS\OAuthServerBundle\Model\ClientManager;
use FOS\OAuthServerBundle\Tests\Functional\TestBundle\Document\Client as MongoDBClient;
use FOS\OAuthServerBundle\Tests\Functional\TestBundle\Entity\Client as ORMClient;
use Symfony\Component\HttpKernel\KernelInterface;

class BootTest extends TestCase
{
    /**
     * @dataProvider getTestBootData
     *
     * @param string $env
     */
    public function testBoot($env)
    {
        try {
            $kernel = static::createKernel(['env' => $env]);
            $kernel->boot();

            // no exceptions were thrown
            self::assertTrue(true);
        } catch (\Exception $exception) {
            $this->fail($exception->getMessage());
        }
    }

    /**
     * @dataProvider getTestBootData
     *
     * @param string $env
     */
    public function testCreateClient($env)
    {
        $kernel = static::createKernel(['env' => $env]);
        $kernel->boot();

        /** @var ClientManager $clientManager */
        $clientManager = $kernel->getContainer()->get('test.fos_oauth_server.client_manager.default');
        self::assertInstanceOf(ClientManager::class, $clientManager);

        $client = $clientManager->createClient();
        $secret = $client->getSecret();
        $clientManager->updateClient($client);

        $client = $clientManager->findClientByPublicId($client->getPublicId());
        self::assertInstanceOf(Client::class, $client);

        self::assertSame($secret, $client->getSecret());
    }

    public function getTestBootData()
    {
        return [
//            ['orm'],
            ['mongodb'],
        ];
    }
}
