<?php

namespace Frontend\Modules\Catalog\Widgets;

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Modules\Catalog\Engine\Model as FrontendCatalogModel;
use Frontend\Modules\Media\Engine\Helper as FrontendMediaHelper;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget with recent products
 *
 * @author Tim van Wolfswinkel <tim@webleads.nl>
 */
class Frontpage extends FrontendBaseWidget
{
    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();

        $this->header->addJS('/src/Frontend/Modules/' . $this->getModule() . '/Js/Frontpage.js');

        $this->loadTemplate();
        $this->parse();
    }

    /**
     * Parse
     */
    private function parse()
    {
        // get list of recent products
        $products = FrontendCatalogModel::getAllForFrontpage();

        foreach ($products as &$product) {
            $product['image'] = FrontendMediaHelper::getFromModule('Catalog', $product['id'], 0, 1, 'product');
        }

        unset($product);

        $productsTotal = array();

        while (count($productsTotal) < 20) {
            foreach ($products as $product) {
                $productsTotal[] = $product;
            }
        }

        $this->tpl->assign('products', $products);
        $this->tpl->assign('productsCarousel', $productsTotal);
    }
}