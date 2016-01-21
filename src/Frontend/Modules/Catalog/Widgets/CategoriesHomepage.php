<?php

namespace Frontend\Modules\Catalog\Widgets;

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Catalog\Engine\Model as FrontendCatalogModel;
use Frontend\Modules\Media\Engine\Helper as FrontendMediaHelper;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget with the Catalog-categories
 *
 * @author Tim van Wolfswinkel <tim@webleads.nl>
 */
class CategoriesHomepage extends FrontendBaseWidget
{
    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();
        $this->loadTemplate();
        $this->parse();
    }

    /**
     * Parse
     */
    private function parse()
    {
        // get categories
        $categories = FrontendCatalogModel::getAllCategories();

        $count = 0;
        // any categories?
        if (!empty($categories)) {
            // build link
            $link = FrontendNavigation::getURLForBlock('Catalog', 'Category');

            // loop and reset url
            foreach ($categories as $key => &$row) {

                if ($row['parent_id'] > 0 || $count >= 4) {
                    unset($categories[$key]);
                    continue;
                }

                //--Create url
                $row['url'] = $link . '/' . $row['url'];

                //--Get image
                $row['image'] = FrontendMediaHelper::getFromModule('Catalog', $row['id'], 0, 1, 'category');

                //--add count
                $count++;
            }
        }


        // assign comments
        $this->tpl->assign('categories', $categories);
    }
}