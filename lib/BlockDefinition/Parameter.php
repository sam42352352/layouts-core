<?php

namespace Netgen\BlockManager\BlockDefinition;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class Parameter
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $isRequired;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * Constructor.
     *
     * @param string $name
     * @param bool $isRequired
     * @param array $options
     */
    public function __construct($name = null, $isRequired = false, array $options = array())
    {
        $this->name = $name;
        $this->isRequired = (bool)$isRequired;

        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $this->options = $optionsResolver->resolve($options);
    }

    /**
     * Returns the parameter name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns if the parameter is required.
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->isRequired;
    }

    /**
     * Returns the parameter options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Configures the options for this parameter.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    abstract public function configureOptions(OptionsResolver $optionsResolver);

    /**
     * Returns the Symfony form type which matches this parameter.
     *
     * @return string
     */
    abstract public function getFormType();

    /**
     * Maps the parameter options to Symfony form options.
     *
     * @return array
     */
    abstract public function mapFormTypeOptions();
}
