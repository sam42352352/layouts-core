<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Generator;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\StatusStringTrait;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use function iterator_to_array;

/**
 * Rule value visitor.
 *
 * @see \Netgen\Layouts\API\Values\LayoutResolver\Rule
 *
 * @implements \Netgen\Layouts\Transfer\Output\VisitorInterface<\Netgen\Layouts\API\Values\LayoutResolver\Rule>
 */
final class RuleVisitor implements VisitorInterface
{
    use StatusStringTrait;

    public const ENTITY_TYPE = 'rule';

    public function accept(object $value): bool
    {
        return $value instanceof Rule;
    }

    public function visit(object $value, OutputVisitor $outputVisitor): array
    {
        $layout = $value->getLayout();

        return [
            '__type' => self::ENTITY_TYPE,
            'id' => $value->getId()->toString(),
            'status' => $this->getStatusString($value),
            'layout_id' => $layout instanceof Layout ? $layout->getId()->toString() : null,
            'is_enabled' => $value->isEnabled(),
            'priority' => $value->getPriority(),
            'comment' => $value->getComment(),
            'targets' => iterator_to_array($this->visitTargets($value, $outputVisitor)),
            'conditions' => iterator_to_array($this->visitConditions($value, $outputVisitor)),
        ];
    }

    /**
     * Visit the given $rule targets into hash representation.
     *
     * @return \Generator<string, mixed>
     */
    private function visitTargets(Rule $rule, OutputVisitor $outputVisitor): Generator
    {
        foreach ($rule->getTargets() as $target) {
            yield $target->getId()->toString() => $outputVisitor->visit($target);
        }
    }

    /**
     * Visit the given $rule conditions into hash representation.
     *
     * @return \Generator<string, array<string, mixed>>
     */
    private function visitConditions(Rule $rule, OutputVisitor $outputVisitor): Generator
    {
        foreach ($rule->getConditions() as $condition) {
            yield $condition->getId()->toString() => $outputVisitor->visit($condition);
        }
    }
}
