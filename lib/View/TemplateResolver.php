<?php

namespace Netgen\BlockManager\View;

use InvalidArgumentException;
use Netgen\BlockManager\View\Matcher\MatcherInterface;

class TemplateResolver implements TemplateResolverInterface
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface[]
     */
    protected $matchers = array();

    /**
     * @var array
     */
    protected $config = array();

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\Matcher\MatcherInterface[] $matchers
     * @param array $config
     */
    public function __construct(array $matchers = array(), array $config = array())
    {
        $this->matchers = $matchers;
        $this->config = $config;
    }

    /**
     * Resolves a view template.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @throws \InvalidArgumentException If there's no template defined for specified view
     *
     * @return string
     */
    public function resolveTemplate(ViewInterface $view)
    {
        $matchedConfig = false;
        $context = $view->getContext();

        if (!isset($this->config[$context])) {
            throw new InvalidArgumentException(
                sprintf(
                    'No configuration could be found for context "%s"',
                    $context
                )
            );
        }

        foreach ($this->config[$context] as $config) {
            $matchConfig = $config['match'];
            if (!$this->matches($view, $matchConfig)) {
                continue;
            }

            $matchedConfig = $config;
            break;
        }

        if (is_array($matchedConfig)) {
            return $matchedConfig['template'];
        }

        throw new InvalidArgumentException(
            sprintf(
                'No template could be found for view %s',
                get_class($view)
            )
        );
    }

    /**
     * Matches the view to provided config with configured matchers.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     * @param array $matchConfig
     *
     * @return bool
     */
    protected function matches(ViewInterface $view, array $matchConfig)
    {
        foreach ($matchConfig as $matcher => $matcherConfig) {
            if (!isset($this->matchers[$matcher])) {
                throw new InvalidArgumentException(
                    sprintf(
                        'No matcher could be found with identifier "%s"',
                        $matcher
                    )
                );
            }

            if (!$this->matchers[$matcher] instanceof MatcherInterface) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Matcher %s needs to implement MatcherInterface',
                        $matcher
                    )
                );
            }

            $matcherConfig = !is_array($matcherConfig) ? array($matcherConfig) : $matcherConfig;
            $this->matchers[$matcher]->setConfig($matcherConfig);
            if (!$this->matchers[$matcher]->match($view)) {
                return false;
            }
        }

        return true;
    }
}
