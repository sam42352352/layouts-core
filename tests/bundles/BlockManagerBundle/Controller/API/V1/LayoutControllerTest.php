<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class LayoutControllerTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadSharedLayouts::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadSharedLayouts::__invoke
     */
    public function testLoadSharedLayouts()
    {
        $this->client->request('GET', '/bm/api/v1/layouts/shared');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/shared_layouts',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Load::__invoke
     */
    public function testLoad()
    {
        $this->client->request('GET', '/bm/api/v1/layouts/1?html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/load_layout',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Load::__invoke
     */
    public function testLoadInPublishedState()
    {
        $this->client->request('GET', '/bm/api/v1/layouts/1?published=true&html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/load_published_layout',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Load::__invoke
     */
    public function testLoadWithNonExistentLayout()
    {
        $this->client->request('GET', '/bm/api/v1/layouts/9999');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadLayoutBlocks::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadLayoutBlocks::__invoke
     */
    public function testViewLayoutBlocks()
    {
        $this->client->request('GET', '/bm/api/v1/en/layouts/1/blocks?html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/load_layout_blocks',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadLayoutBlocks::__invoke
     */
    public function testViewLayoutBlocksInPublishedState()
    {
        $this->client->request('GET', '/bm/api/v1/en/layouts/1/blocks?published=true&html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/load_published_layout_blocks',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadLayoutBlocks::__invoke
     */
    public function testViewLayoutBlocksWithNonExistentLayout()
    {
        $this->client->request('GET', '/bm/api/v1/en/layouts/9999/blocks');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadLayoutBlocks::__invoke
     */
    public function testViewLayoutBlocksWithNonExistentLayoutLocale()
    {
        $this->client->request('GET', '/bm/api/v1/unknown/layouts/1/blocks');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "1"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadZoneBlocks::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadZoneBlocks::__invoke
     */
    public function testViewZoneBlocks()
    {
        $this->client->request('GET', '/bm/api/v1/en/layouts/1/zones/right/blocks?html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/load_zone_blocks',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadZoneBlocks::__invoke
     */
    public function testViewZoneBlocksInPublishedState()
    {
        $this->client->request('GET', '/bm/api/v1/en/layouts/1/zones/right/blocks?published=true&html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/load_published_zone_blocks',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadZoneBlocks::__invoke
     */
    public function testViewZoneBlocksWithNonExistentZone()
    {
        $this->client->request('GET', '/bm/api/v1/en/layouts/1/zones/unknown/blocks');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "unknown"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadZoneBlocks::__invoke
     */
    public function testViewZoneBlocksWithNonExistentLayout()
    {
        $this->client->request('GET', '/bm/api/v1/en/layouts/9999/zones/right/blocks');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "right"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LoadZoneBlocks::__invoke
     */
    public function testViewZoneBlocksWithNonExistentLayoutLocale()
    {
        $this->client->request('GET', '/bm/api/v1/unknown/layouts/1/zones/right/blocks');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "1"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LinkZone::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LinkZone::__invoke
     */
    public function testLinkZone()
    {
        $data = $this->jsonEncode(
            [
                'linked_layout_id' => 5,
                'linked_zone_identifier' => 'right',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/zones/right/link',
            [],
            [],
            [],
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LinkZone::__invoke
     */
    public function testLinkZoneWithNonExistentZone()
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/zones/unknown/link',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "unknown"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LinkZone::__invoke
     */
    public function testLinkZoneWithNonExistentLayout()
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/9999/zones/right/link',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "right"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LinkZone::__invoke
     */
    public function testLinkZoneWithMissingLinkedLayoutId()
    {
        $data = $this->jsonEncode(
            [
                'linked_zone_identifier' => 'right',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/zones/right/link',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "layoutId": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LinkZone::__invoke
     */
    public function testLinkZoneWithInvalidLinkedLayoutId()
    {
        $data = $this->jsonEncode(
            [
                'linked_layout_id' => [42],
                'linked_zone_identifier' => 'right',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/zones/right/link',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "layoutId": This value should be of type scalar.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LinkZone::__invoke
     */
    public function testLinkZoneWithMissingLinkedZoneIdentifier()
    {
        $data = $this->jsonEncode(
            [
                'linked_layout_id' => 5,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/zones/right/link',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "identifier": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LinkZone::__invoke
     */
    public function testLinkZoneWithInvalidLinkedZoneIdentifier()
    {
        $data = $this->jsonEncode(
            [
                'linked_layout_id' => 5,
                'linked_zone_identifier' => 42,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/zones/right/link',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "identifier": This value should be of type string.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LinkZone::__invoke
     */
    public function testLinkZoneWithNonExistentLinkedZone()
    {
        $data = $this->jsonEncode(
            [
                'linked_layout_id' => 5,
                'linked_zone_identifier' => 'unknown',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/zones/right/link',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "unknown"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LinkZone::__invoke
     */
    public function testLinkZoneWithNonExistentLinkedLayout()
    {
        $data = $this->jsonEncode(
            [
                'linked_layout_id' => 9999,
                'linked_zone_identifier' => 'right',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/zones/right/link',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "right"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\LinkZone::__invoke
     */
    public function testLinkZoneWithNonSharedLinkedLayout()
    {
        $data = $this->jsonEncode(
            [
                'linked_layout_id' => 2,
                'linked_zone_identifier' => 'right',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/zones/right/link',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "linkedZone" has an invalid state. Linked zone is not in the shared layout.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\UnlinkZone::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\UnlinkZone::__invoke
     */
    public function testUnlinkZone()
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            'DELETE',
            '/bm/api/v1/layouts/1/zones/right/link',
            [],
            [],
            [],
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\UnlinkZone::__invoke
     */
    public function testUnlinkZoneWithNonExistentZone()
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            'DELETE',
            '/bm/api/v1/layouts/1/zones/unknown/link',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "unknown"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\UnlinkZone::__invoke
     */
    public function testUnlinkZoneWithNonExistentLayout()
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            'DELETE',
            '/bm/api/v1/layouts/9999/zones/right/link',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "right"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator::validateCreateLayout
     */
    public function testCreate()
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 'My new layout',
                'description' => 'My new layout description',
                'locale' => 'en',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts?html=false',
            [],
            [],
            [],
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/create_layout',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator::validateCreateLayout
     */
    public function testCreateWithMissingDescription()
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 'My new layout',
                'locale' => 'en',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts?html=false',
            [],
            [],
            [],
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/create_layout_empty_description',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator::validateCreateLayout
     */
    public function testCreateWithEmptyDescription()
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 'My new layout',
                'description' => '',
                'locale' => 'en',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts?html=false',
            [],
            [],
            [],
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/create_layout_empty_description',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator::validateCreateLayout
     */
    public function testCreateWithInvalidLayoutType()
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => 42,
                'name' => 'My new layout',
                'locale' => 'en',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "layout_type": This value should be of type string.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator::validateCreateLayout
     */
    public function testCreateWithMissingLayoutType()
    {
        $data = $this->jsonEncode(
            [
                'name' => 'My new layout',
                'locale' => 'en',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "layout_type": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator::validateCreateLayout
     */
    public function testCreateWithInvalidName()
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 42,
                'locale' => 'en',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "name": This value should be of type string.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator::validateCreateLayout
     */
    public function testCreateWithMissingName()
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'locale' => 'en',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "name": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator::validateCreateLayout
     */
    public function testCreateWithInvalidDescription()
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 'My name',
                'description' => 42,
                'locale' => 'en',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "description": This value should be of type string.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator::validateCreateLayout
     */
    public function testCreateWithInvalidLocale()
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 'My new layout',
                'description' => 'My new layout description',
                'locale' => 42,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "locale": Expected argument of type "string", "integer" given'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator::validateCreateLayout
     */
    public function testCreateWithMissingLocale()
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 'My new layout',
                'description' => 'My new layout description',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "locale": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator::validateCreateLayout
     */
    public function testCreateWithNonExistentLocale()
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 'My new layout',
                'description' => 'My new layout description',
                'locale' => 'unknown',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "locale": This value is not a valid locale.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator::validateCreateLayout
     */
    public function testCreateWithNonExistingLayoutType()
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => 'unknown',
                'name' => 'My new layout',
                'locale' => 'en',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "layout_type" has an invalid state. Layout type does not exist.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator::validateCreateLayout
     */
    public function testCreateWithExistingName()
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 'My layout',
                'locale' => 'en',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "name" has an invalid state. Layout with provided name already exists.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Copy::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Copy::__invoke
     */
    public function testCopy()
    {
        $data = $this->jsonEncode(
            [
                'name' => 'My new layout name',
                'description' => 'My new layout description',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/copy?html=false',
            [],
            [],
            [],
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/copy_layout',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Copy::__invoke
     */
    public function testCopyInPublishedState()
    {
        $data = $this->jsonEncode(
            [
                'name' => 'My new layout name',
                'description' => 'My new layout description',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/6/copy?published=true&html=false',
            [],
            [],
            [],
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/copy_published_layout',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Copy::__invoke
     */
    public function testCopyWithNonExistingDescription()
    {
        $data = $this->jsonEncode(
            [
                'name' => 'My new layout name',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/copy?html=false',
            [],
            [],
            [],
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/copy_layout_without_description',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Copy::__invoke
     */
    public function testCopyWithEmptyDescription()
    {
        $data = $this->jsonEncode(
            [
                'name' => 'My new layout name',
                'description' => '',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/copy?html=false',
            [],
            [],
            [],
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/copy_layout_empty_description',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Copy::__invoke
     */
    public function testCopyWithNonExistingLayout()
    {
        $data = $this->jsonEncode(
            [
                'name' => 'My new layout name',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/9999/copy',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Copy::__invoke
     */
    public function testCopyWithInvalidName()
    {
        $data = $this->jsonEncode(
            [
                'name' => 42,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/copy',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "name": This value should be of type string.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Copy::__invoke
     */
    public function testCopyWithMissingName()
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/copy',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "name": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Copy::__invoke
     */
    public function testCopyWithExistingName()
    {
        $data = $this->jsonEncode(
            [
                'name' => 'My other layout',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/copy',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "layoutCopyStruct" has an invalid state. Layout with provided name already exists.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Copy::__invoke
     */
    public function testCopyWithInvalidDescription()
    {
        $data = $this->jsonEncode(
            [
                'name' => 'New name',
                'description' => 42,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/copy',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "description": This value should be of type string.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\ChangeType::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\ChangeType::__invoke
     */
    public function testChangeType()
    {
        $data = $this->jsonEncode(
            [
                'new_type' => '4_zones_b',
                'zone_mappings' => [
                    'left' => ['left'],
                    'right' => ['right'],
                ],
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/change_type?html=false',
            [],
            [],
            [],
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/change_type',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\ChangeType::__invoke
     */
    public function testChangeTypeWithoutMappings()
    {
        $data = $this->jsonEncode(
            [
                'new_type' => '4_zones_b',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/change_type?html=false',
            [],
            [],
            [],
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/change_type_without_mappings',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\CreateDraft::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\CreateDraft::__invoke
     */
    public function testCreateDraft()
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/draft?html=false',
            [],
            [],
            [],
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/create_layout_draft',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\CreateDraft::__invoke
     */
    public function testCreateDraftWithNonExistentLayout()
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/9999/draft',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\DiscardDraft::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\DiscardDraft::__invoke
     */
    public function testDiscardDraft()
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            'DELETE',
            '/bm/api/v1/layouts/1/draft',
            [],
            [],
            [],
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\DiscardDraft::__invoke
     */
    public function testDiscardDraftWithNonExistentLayout()
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            'DELETE',
            '/bm/api/v1/layouts/9999/draft',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\PublishDraft::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\PublishDraft::__invoke
     */
    public function testPublishDraft()
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/publish',
            [],
            [],
            [],
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\PublishDraft::__invoke
     */
    public function testPublishDraftWithNonExistentLayout()
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/9999/publish',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\RestoreFromArchive::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\RestoreFromArchive::__invoke
     */
    public function testRestoreFromArchive()
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/2/restore',
            [],
            [],
            [],
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\RestoreFromArchive::__invoke
     */
    public function testRestoreFromArchiveWithNonExistentLayout()
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/9999/restore',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Delete::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Delete::__invoke
     */
    public function testDelete()
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            'DELETE',
            '/bm/api/v1/layouts/1',
            [],
            [],
            [],
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Delete::__invoke
     */
    public function testDeleteWithNonExistentLayout()
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            'DELETE',
            '/bm/api/v1/layouts/9999',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "9999"'
        );
    }
}
