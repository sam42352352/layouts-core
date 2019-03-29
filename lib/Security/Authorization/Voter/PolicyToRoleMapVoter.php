<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Votes on Netgen Layouts permissions (nglayouts:*) by mapping the permissions to built-in roles (ROLE_NGBM_*).
 */
final class PolicyToRoleMapVoter extends Voter
{
    /**
     * Map of supported permissions to their respective roles.
     */
    private const POLICY_TO_ROLE_MAP = [
        'nglayouts:block:add' => self::ROLE_EDITOR,
        'nglayouts:block:edit' => self::ROLE_EDITOR,
        'nglayouts:block:delete' => self::ROLE_EDITOR,
        'nglayouts:block:reorder' => self::ROLE_EDITOR,
        'nglayouts:layout:add' => self::ROLE_ADMIN,
        'nglayouts:layout:edit' => self::ROLE_EDITOR,
        'nglayouts:layout:delete' => self::ROLE_ADMIN,
        'nglayouts:layout:clear_cache' => self::ROLE_ADMIN,
        'nglayouts:mapping:edit' => self::ROLE_ADMIN,
        'nglayouts:mapping:activate' => self::ROLE_ADMIN,
        'nglayouts:mapping:delete' => self::ROLE_ADMIN,
        'nglayouts:mapping:reorder' => self::ROLE_ADMIN,
        'nglayouts:collection:edit' => self::ROLE_EDITOR,
        'nglayouts:collection:items' => self::ROLE_EDITOR,
        'nglayouts:ui:access' => self::ROLE_ADMIN,
        'nglayouts:api:read' => self::ROLE_API,
    ];

    /**
     * The identifier of the admin role. Users having this role
     * have full and unrestricted access to the entire system.
     */
    private const ROLE_ADMIN = 'ROLE_NGBM_ADMIN';

    /**
     * The identifier of the editor role. Users having this role
     * have full access only to the layout editing interface.
     */
    private const ROLE_EDITOR = 'ROLE_NGBM_EDITOR';

    /**
     * The identifier of the API role. Users having this role
     * have access to read only data of the API endpoints.
     */
    private const ROLE_API = 'ROLE_NGBM_API';

    /**
     * @var \Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface
     */
    private $accessDecisionManager;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager)
    {
        $this->accessDecisionManager = $accessDecisionManager;
    }

    protected function supports($attribute, $subject): bool
    {
        return is_string($attribute) && mb_strpos($attribute, 'nglayouts:') === 0;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        if (!isset(self::POLICY_TO_ROLE_MAP[$attribute])) {
            return false;
        }

        return $this->accessDecisionManager->decide(
            $token,
            [self::POLICY_TO_ROLE_MAP[$attribute]],
            $subject
        );
    }
}
