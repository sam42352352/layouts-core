<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API;

use Lakion\ApiTestCase\JsonApiTestCase as BaseJsonApiTestCase;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use Netgen\BlockManager\Tests\Persistence\Doctrine\DatabaseTrait;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

abstract class JsonApiTestCase extends BaseJsonApiTestCase
{
    use DatabaseTrait;

    /**
     * @var \Netgen\BlockManager\Tests\Kernel\MockerContainer
     */
    protected $clientContainer;

    public function setUp()
    {
        parent::setUp();

        $this->setUpClient();
        $this->mockQueryType();
        $this->createDatabase();

        $this->expectedResponsesPath = __DIR__ . '/responses/expected';
    }

    public function tearDown()
    {
        $this->closeDatabase();
    }

    public function setUpClient()
    {
        parent::setUpClient();

        // We're using the container from kernel to bypass injection of
        // Symfony\Bundle\FrameworkBundle\Test\TestContainer on Symfony 4.1

        /** @var \Netgen\BlockManager\Tests\Kernel\MockerContainer $clientContainer */
        $clientContainer = self::$kernel->getContainer();

        $this->clientContainer = $clientContainer;

        $this->client->setServerParameter('CONTENT_TYPE', 'application/json');
        $this->client->setServerParameter('PHP_AUTH_USER', (string) getenv('SF_USERNAME'));
        $this->client->setServerParameter('PHP_AUTH_PW', (string) getenv('SF_PASSWORD'));
    }

    protected function mockQueryType()
    {
        $searchFixtures = require __DIR__ . '/fixtures/search.php';

        /** @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface $queryTypeRegistry */
        $queryTypeRegistry = $this->clientContainer->get('netgen_block_manager.collection.registry.query_type');

        $queryType = new QueryType('my_query_type', $searchFixtures, count($searchFixtures));
        $queryTypeRegistry->addQueryType('my_query_type', $queryType);
    }

    /**
     * Asserts that response is empty and has No Content status code.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    protected function assertEmptyResponse(Response $response)
    {
        $this->assertEmpty($response->getContent());
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * Asserts that response has a proper JSON exception content.
     * If statusCode is set, asserts that response has given status code.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param int $statusCode
     * @param string $message
     */
    protected function assertException(Response $response, $statusCode = Response::HTTP_BAD_REQUEST, $message = null)
    {
        if (isset($_SERVER['OPEN_ERROR_IN_BROWSER']) && true === $_SERVER['OPEN_ERROR_IN_BROWSER']) {
            $this->showErrorInBrowserIfOccurred($response);
        }

        $this->assertResponseCode($response, $statusCode);
        $this->assertHeader($response, 'application/json');
        $this->assertExceptionResponse($response, $statusCode, $message);
    }

    /**
     * Asserts that exception response has a correct response status text and code.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param int $statusCode
     * @param string $message
     */
    protected function assertExceptionResponse(Response $response, $statusCode = Response::HTTP_BAD_REQUEST, $message = null)
    {
        $responseContent = json_decode($response->getContent(), true);
        $this->assertInternalType('array', $responseContent);

        $this->assertArrayHasKey('status_code', $responseContent);
        $this->assertArrayHasKey('status_text', $responseContent);

        $this->assertEquals($statusCode, $responseContent['status_code']);
        $this->assertEquals(Response::$statusTexts[$statusCode], $responseContent['status_text']);

        if ($message !== null) {
            $this->assertEquals($message, $responseContent['message']);
        }
    }

    /**
     * Pretty encodes the provided array.
     *
     * @param array $content
     *
     * @throws \RuntimeException If encoding failed
     *
     * @return string
     */
    protected function jsonEncode(array $content)
    {
        $encodedContent = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if (!is_string($encodedContent)) {
            throw new RuntimeException(
                sprintf(
                    'There was an error encoding the value: %s',
                    json_last_error_msg()
                )
            );
        }

        return $encodedContent;
    }
}
