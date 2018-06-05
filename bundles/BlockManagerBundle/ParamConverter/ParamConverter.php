<?php

namespace Netgen\Bundle\BlockManagerBundle\ParamConverter;

use Netgen\BlockManager\Exception\InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ParamConverterConfiguration;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class ParamConverter implements ParamConverterInterface
{
    protected static $statusPublished = 'published';

    protected static $statusDraft = 'draft';

    protected static $statusArchived = 'archived';
    private static $routeStatusParam = '_ngbm_status';

    public function apply(Request $request, ParamConverterConfiguration $configuration)
    {
        $sourceAttributeNames = $this->getSourceAttributeNames();
        foreach ($sourceAttributeNames as $sourceAttributeName) {
            if (!$request->attributes->has($sourceAttributeName)) {
                return false;
            }
        }

        $values = [];
        foreach ($sourceAttributeNames as $sourceAttributeName) {
            $values[$sourceAttributeName] = $request->attributes->get($sourceAttributeName);

            if (empty($values[$sourceAttributeName])) {
                if ($configuration->isOptional()) {
                    return false;
                }

                throw new InvalidArgumentException(
                    $sourceAttributeName,
                    'Required request attribute is empty.'
                );
            }
        }

        $routeStatusParam = $request->attributes->get(self::$routeStatusParam);
        $queryPublishedParam = $request->query->get('published');

        $values['status'] = self::$statusDraft;
        if (in_array($routeStatusParam, [self::$statusPublished, self::$statusDraft, self::$statusArchived], true)) {
            $values['status'] = $routeStatusParam;
        } elseif ($queryPublishedParam === 'true') {
            $values['status'] = self::$statusPublished;
        }

        if ($request->attributes->has('locale')) {
            $values['locale'] = $request->attributes->get('locale');
        }

        $request->attributes->set(
            $this->getDestinationAttributeName(),
            $this->loadValue($values)
        );

        return true;
    }

    public function supports(ParamConverterConfiguration $configuration)
    {
        return is_a($configuration->getClass(), $this->getSupportedClass(), true);
    }

    /**
     * Returns source attribute name.
     *
     * @return array
     */
    abstract public function getSourceAttributeNames();

    /**
     * Returns destination attribute name.
     *
     * @return string
     */
    abstract public function getDestinationAttributeName();

    /**
     * Returns the supported class.
     *
     * @return string
     */
    abstract public function getSupportedClass();

    /**
     * Returns the value.
     *
     * @param array $values
     *
     * @return \Netgen\BlockManager\API\Values\Value
     */
    abstract public function loadValue(array $values);
}
