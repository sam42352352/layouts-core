<?php

namespace Netgen\BlockManager\Collection;

use Netgen\BlockManager\Collection\QueryType\Configuration\Form;
use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface;

final class QueryTypeFactory
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface
     */
    private $parameterBuilderFactory;

    public function __construct(ParameterBuilderFactoryInterface $parameterBuilderFactory)
    {
        $this->parameterBuilderFactory = $parameterBuilderFactory;
    }

    /**
     * Builds the query type.
     *
     * @param string $type
     * @param \Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface $handler
     * @param array $config
     *
     * @return \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    public function buildQueryType(
        $type,
        QueryTypeHandlerInterface $handler,
        array $config
    ) {
        $parameterBuilder = $this->parameterBuilderFactory->createParameterBuilder();
        $handler->buildParameters($parameterBuilder);
        $parameters = $parameterBuilder->buildParameters();

        $forms = array();

        if (isset($config['forms'])) {
            foreach ($config['forms'] as $formIdentifier => $formConfig) {
                if (!$formConfig['enabled']) {
                    continue;
                }

                $forms[$formIdentifier] = new Form(
                    array(
                        'identifier' => $formIdentifier,
                        'type' => $formConfig['type'],
                    )
                );
            }
        }

        return new QueryType(
            array(
                'type' => $type,
                'name' => isset($config['name']) ? $config['name'] : '',
                'forms' => $forms,
                'handler' => $handler,
                'parameters' => $parameters,
            )
        );
    }
}
