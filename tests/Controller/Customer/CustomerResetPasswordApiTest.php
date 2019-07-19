<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Controller\Customer;

use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\Sylius\ShopApiPlugin\Controller\JsonApiTestCase;
use Tests\Sylius\ShopApiPlugin\Controller\Utils\PurgeSpooledMessagesTrait;

final class CustomerResetPasswordApiTest extends JsonApiTestCase
{
    use PurgeSpooledMessagesTrait;

    /**
     * @test
     */
    public function it_allows_to_reset_customer_password(): void
    {
        $this->loadFixturesFromFiles(['channel.yml', 'customer.yml']);

        $data = '{"email": "oliver@queen.com"}';

        $this->client->request('PUT', '/shop-api/request-password-reset', [], [], self::CONTENT_TYPE_HEADER, $data);

        /** @var UserRepositoryInterface $userRepository */
        $userRepository = $this->get('sylius.repository.shop_user');

        /** @var ShopUserInterface $user */
        $user = $userRepository->findOneByEmail('oliver@queen.com');

        $newPasswords =
<<<EOT
        {
            "password" : {
                "first": "somepass",
                "second": "somepass"
            }
        }
EOT;

        $this->client->request('PUT', '/shop-api/password-reset/' . $user->getPasswordResetToken(), [], [], self::CONTENT_TYPE_HEADER, $newPasswords);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    protected function getContainer(): ContainerInterface
    {
        return static::$sharedKernel->getContainer();
    }
}
