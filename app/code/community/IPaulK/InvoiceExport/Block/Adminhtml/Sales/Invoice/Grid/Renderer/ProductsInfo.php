<?php

/**
 * Invoice Export
 *
 * @category IPaulK
 * @package IPaulK_InvoiceExport
 * @link https://github.com/iPaulK/mage-advanced-invoice-export/
 * @license https://opensource.org/licenses/MIT
 */

class IPaulK_InvoiceExport_Block_Adminhtml_Sales_Invoice_Grid_Renderer_ProductsInfo extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {  
        $entity_id = $row->getData('entity_id');
        $collection = $row->getItemsCollection();
        $productIds = [];
        foreach ($collection as $item) {
        	$productIds[] = $item->getProductId();
        }

        $info = implode(", ", $productIds);
        return $info;
    }
}