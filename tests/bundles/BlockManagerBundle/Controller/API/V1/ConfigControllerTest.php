<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ConfigControllerTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\Controller::checkPermissions
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Config\LoadConfig::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Config\LoadConfig::__invoke
     */
    public function testGetConfig()
    {
        /** @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $tokenManager */
        $tokenManager = $this->clientContainer->get('security.csrf.token_manager');
        $tokenId = $this->clientContainer->getParameter('netgen_block_manager.api.csrf_token_id');

        $currentToken = $tokenManager->getToken($tokenId);

        $this->client->request('GET', '/bm/api/v1/config');

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_OK);

        $responseContent = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseContent);
        $this->assertArrayHasKey('csrf_token', $responseContent);
        $this->assertEquals($currentToken, $responseContent['csrf_token']);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Config\LoadBlockTypes::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Config\LoadBlockTypes::__invoke
     */
    public function testGetBlockTypes()
    {
        $this->client->request('GET', '/bm/api/v1/config/block_types');

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_OK);

        $responseContent = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseContent);
        $this->assertArrayHasKey('block_types', $responseContent);
        $this->assertArrayHasKey('block_type_groups', $responseContent);

        $this->assertInternalType('array', $responseContent['block_types']);
        $this->assertNotEmpty($responseContent['block_types']);

        $this->assertInternalType('array', $responseContent['block_type_groups']);
        $this->assertNotEmpty($responseContent['block_type_groups']);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Config\LoadLayoutTypes::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Config\LoadLayoutTypes::__invoke
     */
    public function testGetLayoutTypes()
    {
        $this->client->request('GET', '/bm/api/v1/config/layout_types');

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_OK);

        $responseContent = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseContent);
        $this->assertNotEmpty($responseContent);
    }
}
