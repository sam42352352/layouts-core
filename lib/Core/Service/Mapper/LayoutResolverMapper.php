<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule as PersistenceRule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target as PersistenceTarget;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition as PersistenceCondition;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Core\Values\LayoutResolver\RuleDraft;
use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use Netgen\BlockManager\Core\Values\LayoutResolver\TargetDraft;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Core\Values\LayoutResolver\ConditionDraft;

class LayoutResolverMapper extends Mapper
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper
     */
    protected $layoutMapper;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     * @param \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper $layoutMapper
     */
    public function __construct(Handler $persistenceHandler, LayoutMapper $layoutMapper)
    {
        parent::__construct($persistenceHandler);

        $this->layoutMapper = $layoutMapper;
    }

    /**
     * Builds the API rule value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule|\Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft
     */
    public function mapRule(PersistenceRule $rule)
    {
        $handler = $this->persistenceHandler->getLayoutResolverHandler();

        $layout = null;
        try {
            $layout = $this->persistenceHandler->getLayoutHandler()->loadLayout(
                $rule->layoutId,
                Layout::STATUS_PUBLISHED
            );

            $layout = $this->layoutMapper->mapLayoutInfo($layout);
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $persistenceTargets = $handler->loadRuleTargets($rule);

        $targets = array();
        foreach ($persistenceTargets as $persistenceTarget) {
            $targets[] = $this->mapTarget($persistenceTarget);
        }

        $persistenceConditions = $handler->loadRuleConditions($rule);

        $conditions = array();
        foreach ($persistenceConditions as $persistenceCondition) {
            $conditions[] = $this->mapCondition($persistenceCondition);
        }

        $ruleData = array(
            'id' => $rule->id,
            'status' => $rule->status,
            'layout' => $layout,
            'enabled' => $rule->enabled,
            'priority' => $rule->priority,
            'comment' => $rule->comment,
            'targets' => $targets,
            'conditions' => $conditions,
        );

        return $rule->status === PersistenceRule::STATUS_PUBLISHED ?
            new Rule($ruleData) :
            new RuleDraft($ruleData);
    }

    /**
     * Builds the API target value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target $target
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target|\Netgen\BlockManager\API\Values\LayoutResolver\TargetDraft
     */
    public function mapTarget(PersistenceTarget $target)
    {
        $targetData = array(
            'id' => $target->id,
            'status' => $target->status,
            'ruleId' => $target->ruleId,
            'type' => $target->type,
            'value' => $target->value,
        );

        return $target->status === PersistenceRule::STATUS_PUBLISHED ?
            new Target($targetData) :
            new TargetDraft($targetData);
    }

    /**
     * Builds the API condition value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition $condition
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition|\Netgen\BlockManager\API\Values\LayoutResolver\ConditionDraft
     */
    public function mapCondition(PersistenceCondition $condition)
    {
        $conditionData = array(
            'id' => $condition->id,
            'status' => $condition->status,
            'ruleId' => $condition->ruleId,
            'type' => $condition->type,
            'value' => $condition->value,
        );

        return $condition->status === PersistenceRule::STATUS_PUBLISHED ?
            new Condition($conditionData) :
            new ConditionDraft($conditionData);
    }
}
