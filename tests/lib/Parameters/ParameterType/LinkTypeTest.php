<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Item\CmsItemLoaderInterface;
use Netgen\BlockManager\Item\NullCmsItem;
use Netgen\BlockManager\Item\Registry\ValueTypeRegistry;
use Netgen\BlockManager\Item\ValueType\ValueType;
use Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\BlockManager\Parameters\ParameterType\LinkType;
use Netgen\BlockManager\Parameters\Value\LinkValue;
use Netgen\BlockManager\Tests\TestCase\ExportObjectTrait;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class LinkTypeTest extends TestCase
{
    use ExportObjectTrait;
    use ParameterTypeTestTrait;

    /**
     * @var \Netgen\BlockManager\Item\Registry\ValueTypeRegistryInterface
     */
    private $valueTypeRegistry;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $cmsItemLoaderMock;

    public function setUp(): void
    {
        $this->valueTypeRegistry = new ValueTypeRegistry();
        $this->valueTypeRegistry->addValueType('default', new ValueType(['isEnabled' => true]));

        $this->cmsItemLoaderMock = $this->createMock(CmsItemLoaderInterface::class);

        $this->type = new LinkType($this->valueTypeRegistry, new RemoteIdConverter($this->cmsItemLoaderMock));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::__construct
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertSame('link', $this->type->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::configureOptions
     * @dataProvider validOptionsProvider
     */
    public function testValidOptions(array $options, array $resolvedOptions): void
    {
        $parameter = $this->getParameterDefinition($options);
        $this->assertSame($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidArgumentException
     * @dataProvider invalidOptionsProvider
     */
    public function testInvalidOptions(array $options): void
    {
        $this->getParameterDefinition($options);
    }

    public function validOptionsProvider(): array
    {
        return [
            [
                [],
                ['value_types' => ['default'], 'allow_invalid_internal' => false],
            ],
            [
                ['value_types' => ['value']],
                ['value_types' => ['value'], 'allow_invalid_internal' => false],
            ],
            [
                ['allow_invalid_internal' => false],
                ['value_types' => ['default'], 'allow_invalid_internal' => false],
            ],
            [
                ['allow_invalid_internal' => true],
                ['value_types' => ['default'], 'allow_invalid_internal' => true],
            ],
        ];
    }

    public function invalidOptionsProvider(): array
    {
        return [
            [
                [
                    'value_types' => 42,
                ],
                [
                    'allow_invalid_internal' => 1,
                ],
                [
                    'allow_invalid_internal' => 0,
                ],
                [
                    'undefined_value' => 'Value',
                ],
            ],
        ];
    }

    /**
     * @param mixed $value
     * @param bool $isRequired
     * @param array $valueTypes
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::getRequiredConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, bool $isRequired, array $valueTypes, bool $isValid): void
    {
        $parameter = $this->getParameterDefinition(['required' => $isRequired, 'value_types' => $valueTypes]);
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
        $this->assertSame($isValid, $errors->count() === 0);
    }

    public function validationProvider(): array
    {
        return [
            [null, true, [], true],
            [null, false, [], true],
            [new LinkValue(['linkType' => 'url', 'link' => 'http://a.com', 'linkSuffix' => 'suffix']), true, [], true],
            [new LinkValue(['linkType' => 'url', 'link' => 'http://a.com', 'newWindow' => true]), true, [], true],
            [new LinkValue(['linkType' => 'url', 'link' => 'http://a.com', 'newWindow' => false]), true, [], true],
            [new LinkValue(['linkType' => null, 'link' => null]), true, [], true],
            [new LinkValue(['linkType' => null, 'link' => 'http://a.com']), true, [], false],
            [new LinkValue(['linkType' => null, 'link' => null]), false, [], true],
            [new LinkValue(['linkType' => null, 'link' => 'http://a.com']), false, [], false],
            [new LinkValue(['linkType' => 'url', 'link' => null]), true, [], true],
            [new LinkValue(['linkType' => 'url', 'link' => 'http://a.com']), true, [], true],
            [new LinkValue(['linkType' => 'url', 'link' => null]), false, [], true],
            [new LinkValue(['linkType' => 'url', 'link' => 'http://a.com']), false, [], true],
            [new LinkValue(['linkType' => 'url', 'link' => 'invalid']), true, [], false],
            [new LinkValue(['linkType' => 'url', 'link' => 'invalid']), false, [], false],
            [new LinkValue(['linkType' => 'email', 'link' => null]), true, [], true],
            [new LinkValue(['linkType' => 'email', 'link' => 'a@a.com']), true, [], true],
            [new LinkValue(['linkType' => 'email', 'link' => null]), false, [], true],
            [new LinkValue(['linkType' => 'email', 'link' => 'a@a.com']), false, [], true],
            [new LinkValue(['linkType' => 'email', 'link' => 'invalid']), true, [], false],
            [new LinkValue(['linkType' => 'email', 'link' => 'invalid']), false, [], false],
            [new LinkValue(['linkType' => 'phone', 'link' => null]), true, [], true],
            [new LinkValue(['linkType' => 'phone', 'link' => 'a@a.com']), true, [], true],
            [new LinkValue(['linkType' => 'phone', 'link' => null]), false, [], true],
            [new LinkValue(['linkType' => 'phone', 'link' => 'a@a.com']), false, [], true],
            [new LinkValue(['linkType' => 'internal', 'link' => null]), true, [], true],
            [new LinkValue(['linkType' => 'internal', 'link' => 'value://42']), true, [], false],
            [new LinkValue(['linkType' => 'internal', 'link' => 'default://42']), true, [], true],
            [new LinkValue(['linkType' => 'internal', 'link' => null]), false, [], true],
            [new LinkValue(['linkType' => 'internal', 'link' => 'value://42']), false, [], false],
            [new LinkValue(['linkType' => 'internal', 'link' => 'default://42']), false, [], true],
            [new LinkValue(['linkType' => 'internal', 'link' => 'value']), true, [], false],
            [new LinkValue(['linkType' => 'internal', 'link' => 'value']), false, [], false],
            [new LinkValue(['linkType' => 'internal', 'link' => null]), true, ['value'], true],
            [new LinkValue(['linkType' => 'internal', 'link' => 'value://42']), true, ['value'], true],
            [new LinkValue(['linkType' => 'internal', 'link' => null]), false, ['value'], true],
            [new LinkValue(['linkType' => 'internal', 'link' => 'value://42']), false, ['value'], true],
            [new LinkValue(['linkType' => 'internal', 'link' => 'value']), true, ['value'], false],
            [new LinkValue(['linkType' => 'internal', 'link' => 'value']), false, ['value'], false],
            [new LinkValue(['linkType' => 'internal', 'link' => null]), true, ['other'], true],
            [new LinkValue(['linkType' => 'internal', 'link' => 'value://42']), true, ['other'], false],
            [new LinkValue(['linkType' => 'internal', 'link' => null]), false, ['other'], true],
            [new LinkValue(['linkType' => 'internal', 'link' => 'value://42']), false, ['other'], false],
            [new LinkValue(['linkType' => 'internal', 'link' => 'value']), true, ['other'], false],
            [new LinkValue(['linkType' => 'internal', 'link' => 'value']), false, ['other'], false],
        ];
    }

    /**
     * @param mixed $value
     * @param mixed $convertedValue
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::toHash
     * @dataProvider toHashProvider
     */
    public function testToHash($value, $convertedValue): void
    {
        $this->assertSame($convertedValue, $this->type->toHash($this->getParameterDefinition(), $value));
    }

    public function toHashProvider(): array
    {
        return [
            [
                42,
                null,
            ],
            [
                new LinkValue(
                    [
                        'linkType' => 'url',
                        'link' => 'http://www.google.com',
                        'linkSuffix' => '?suffix',
                        'newWindow' => true,
                    ]
                ),
                [
                    'link_type' => 'url',
                    'link' => 'http://www.google.com',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ],
            ],
        ];
    }

    /**
     * @param array $value
     * @param array $expectedValue
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::fromHash
     * @dataProvider fromHashProvider
     */
    public function testFromHash($value, array $expectedValue): void
    {
        $convertedValue = $this->type->fromHash($this->getParameterDefinition(), $value);

        $this->assertInstanceOf(LinkValue::class, $convertedValue);
        $this->assertSame($expectedValue, $this->exportObject($convertedValue));
    }

    public function fromHashProvider(): array
    {
        return [
            [
                42,
                [
                    'linkType' => null,
                    'link' => null,
                    'linkSuffix' => null,
                    'newWindow' => false,
                ],
            ],
            [
                [],
                [
                    'linkType' => null,
                    'link' => null,
                    'linkSuffix' => null,
                    'newWindow' => false,
                ],
            ],
            [
                [
                    'link_type' => 'url',
                    'link' => 'http://www.google.com',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ],
                [
                    'linkType' => 'url',
                    'link' => 'http://www.google.com',
                    'linkSuffix' => '?suffix',
                    'newWindow' => true,
                ],
            ],
            [
                [
                    'link_type' => 'url',
                    'link' => 'http://www.google.com',
                ],
                [
                    'linkType' => 'url',
                    'link' => 'http://www.google.com',
                    'linkSuffix' => null,
                    'newWindow' => false,
                ],
            ],
        ];
    }

    /**
     * @param mixed $value
     * @param mixed $convertedValue
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::__construct
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertToRemoteId
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::export
     * @dataProvider exportProvider
     */
    public function testExport($value, $convertedValue): void
    {
        $this->cmsItemLoaderMock
            ->expects($this->any())
            ->method('load')
            ->with($this->equalTo('42'), $this->equalTo('my_value_type'))
            ->will(
                $this->returnValue(
                    new CmsItem(
                        [
                            'value' => 42,
                            'remoteId' => 'abc',
                        ]
                    )
                )
            );

        $this->assertSame($convertedValue, $this->type->export($this->getParameterDefinition(), $value));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertToRemoteId
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::export
     */
    public function testExportWithNullCmsItem(): void
    {
        $this->cmsItemLoaderMock
            ->expects($this->any())
            ->method('load')
            ->with($this->equalTo('24'), $this->equalTo('my_value_type'))
            ->will($this->returnValue(new NullCmsItem('my_value_type')));

        $this->assertSame(
            [
                'link_type' => 'internal',
                'link' => 'null://0',
                'link_suffix' => '?suffix',
                'new_window' => true,
            ],
            $this->type->export(
                $this->getParameterDefinition(),
                new LinkValue(
                    [
                        'linkType' => 'internal',
                        'link' => 'my_value_type://24',
                        'linkSuffix' => '?suffix',
                        'newWindow' => true,
                    ]
                )
            )
        );
    }

    public function exportProvider(): array
    {
        return [
            [
                42,
                null,
            ],
            [
                new LinkValue(
                    [
                        'linkType' => 'url',
                        'link' => 'http://www.google.com',
                        'linkSuffix' => '?suffix',
                        'newWindow' => true,
                    ]
                ),
                [
                    'link_type' => 'url',
                    'link' => 'http://www.google.com',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ],
            ],
            [
                new LinkValue(
                    [
                        'linkType' => 'internal',
                        'link' => 'my-value-type://42',
                        'linkSuffix' => '?suffix',
                        'newWindow' => true,
                    ]
                ),
                [
                    'link_type' => 'internal',
                    'link' => 'my-value-type://abc',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ],
            ],
            [
                new LinkValue(
                    [
                        'linkType' => 'internal',
                        'link' => 'invalid',
                        'linkSuffix' => '?suffix',
                        'newWindow' => true,
                    ]
                ),
                [
                    'link_type' => 'internal',
                    'link' => 'null://0',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ],
            ],
        ];
    }

    /**
     * @param mixed $value
     * @param array  $expectedValue
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::__construct
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertFromRemoteId
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::import
     * @dataProvider importProvider
     */
    public function testImport($value, array $expectedValue): void
    {
        $this->cmsItemLoaderMock
            ->expects($this->any())
            ->method('loadByRemoteId')
            ->with($this->equalTo('abc'), $this->equalTo('my_value_type'))
            ->will(
                $this->returnValue(
                    new CmsItem(
                        [
                            'value' => 42,
                            'remoteId' => 'abc',
                        ]
                    )
                )
            );

        $convertedValue = $this->type->import($this->getParameterDefinition(), $value);

        $this->assertInstanceOf(LinkValue::class, $convertedValue);
        $this->assertSame($expectedValue, $this->exportObject($convertedValue));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter::convertFromRemoteId
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::import
     */
    public function testImportWithNullCmsItem(): void
    {
        $this->cmsItemLoaderMock
            ->expects($this->any())
            ->method('loadByRemoteId')
            ->with($this->equalTo('def'), $this->equalTo('my_value_type'))
            ->will($this->returnValue(new NullCmsItem('my_value_type')));

        $importedValue = $this->type->import(
            $this->getParameterDefinition(),
            [
                'link_type' => 'internal',
                'link' => 'my_value_type://def',
                'link_suffix' => '?suffix',
                'new_window' => true,
            ]
        );

        $this->assertInstanceOf(LinkValue::class, $importedValue);

        $this->assertSame(
            [
                'linkType' => 'internal',
                'link' => 'null://0',
                'linkSuffix' => '?suffix',
                'newWindow' => true,
            ],
            $this->exportObject($importedValue)
        );
    }

    public function importProvider(): array
    {
        return [
            [
                42,
                [
                    'linkType' => null,
                    'link' => null,
                    'linkSuffix' => null,
                    'newWindow' => false,
                ],
            ],
            [
                [],
                [
                    'linkType' => null,
                    'link' => null,
                    'linkSuffix' => null,
                    'newWindow' => false,
                ],
            ],
            [
                [
                    'link_type' => 'url',
                    'link' => 'http://www.google.com',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ],
                [
                    'linkType' => 'url',
                    'link' => 'http://www.google.com',
                    'linkSuffix' => '?suffix',
                    'newWindow' => true,
                ],
            ],
            [
                [
                    'link_type' => 'url',
                    'link' => 'http://www.google.com',
                ],
                [
                    'linkType' => 'url',
                    'link' => 'http://www.google.com',
                    'linkSuffix' => null,
                    'newWindow' => false,
                ],
            ],
            [
                [
                    'link_type' => 'internal',
                    'link' => 'my-value-type://abc',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ],
                [
                    'linkType' => 'internal',
                    'link' => 'my-value-type://42',
                    'linkSuffix' => '?suffix',
                    'newWindow' => true,
                ],
            ],
            [
                [
                    'link_type' => 'internal',
                    'link' => 'invalid',
                    'link_suffix' => '?suffix',
                    'new_window' => true,
                ],
                [
                    'linkType' => 'internal',
                    'link' => 'null://0',
                    'linkSuffix' => '?suffix',
                    'newWindow' => true,
                ],
            ],
            [
                [
                    'link_type' => 'internal',
                ],
                [
                    'linkType' => 'internal',
                    'link' => 'null://0',
                    'linkSuffix' => null,
                    'newWindow' => false,
                ],
            ],
        ];
    }

    /**
     * @param mixed $value
     * @param bool $isEmpty
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\LinkType::isValueEmpty
     * @dataProvider emptyProvider
     */
    public function testIsValueEmpty($value, bool $isEmpty): void
    {
        $this->assertSame($isEmpty, $this->type->isValueEmpty($this->getParameterDefinition(), $value));
    }

    public function emptyProvider(): array
    {
        return [
            [null, true],
            [new LinkValue(), true],
            [new LinkValue(['linkType' => 'url']), true],
            [new LinkValue(['linkType' => 'url', 'link' => 'http://www.google.com']), false],
            [new LinkValue(['linkType' => 'url', 'linkSuffix' => '?suffix']), false],
            [new LinkValue(['linkType' => 'email']), true],
            [new LinkValue(['linkType' => 'email', 'link' => 'test@example.com']), false],
            [new LinkValue(['linkType' => 'email', 'linkSuffix' => '?suffix']), true],
            [new LinkValue(['linkType' => 'tel']), true],
            [new LinkValue(['linkType' => 'tel', 'link' => '123456']), false],
            [new LinkValue(['linkType' => 'tel', 'linkSuffix' => '?suffix']), true],
            [new LinkValue(['linkType' => 'internal']), true],
            [new LinkValue(['linkType' => 'internal', 'link' => 'my_value_type://42']), false],
            [new LinkValue(['linkType' => 'internal', 'linkSuffix' => '?suffix']), true],
        ];
    }
}
