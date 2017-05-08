<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MainMenuBuilder
{
    /**
     * @var \Knp\Menu\FactoryInterface
     */
    protected $factory;

    /**
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * Constructor.
     *
     * @param \Knp\Menu\FactoryInterface $factory
     * @param \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Builds the main menu.
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createMenu()
    {
        $menu = $this->factory->createItem('root');

        if ($this->authorizationChecker->isGranted('ngbm:admin')) {
            $menu
                ->addChild('layout_resolver', array('route' => 'ngbm_admin_layout_resolver_index'))
                ->setLabel('menu.main_menu.layout_resolver')
                ->setExtra('translation_domain', 'ngbm_admin');

            $menu
                ->addChild('layouts', array('route' => 'ngbm_admin_layouts_index'))
                ->setLabel('menu.main_menu.layouts')
                ->setExtra('translation_domain', 'ngbm_admin');

            $menu
                ->addChild('shared_layouts', array('route' => 'ngbm_admin_shared_layouts_index'))
                ->setLabel('menu.main_menu.shared_layouts')
                ->setExtra('translation_domain', 'ngbm_admin');
        }

        return $menu;
    }
}
