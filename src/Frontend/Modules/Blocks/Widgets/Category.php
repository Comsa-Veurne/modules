<?php

namespace Frontend\Modules\Blocks\Widgets;

use Frontend\Core\Engine\Base\Widget;
use Frontend\Core\Engine\Navigation;
use Frontend\Modules\Blocks\Engine\Model as FrontendBlocksModel;

/**
 * This is a widget with the Blocks-categories
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Category extends Widget
{
    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();
        $this->header->addCSS('/src/Frontend/Modules/' . $this->getModule() . '/Layout/Css/Blocks.css');

        $this->loadTemplate();
        $this->parse();
    }

    /**
     * Parse
     */
    private function parse()
    {
        // get categories
        $category = FrontendBlocksModel::getCategoryById($this->data['id']);
        $blocks = FrontendBlocksModel::getAllByCategory($this->data['id']);

        // assign comments
        $this->tpl->assign('category', $category);
        $this->tpl->assign('blocks', $blocks);
    }
}
