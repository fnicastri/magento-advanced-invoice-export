<?php

/**
 * Invoice Export
 *
 * @category IPaulK
 * @package IPaulK_InvoiceExport
 * @link https://github.com/iPaulK/mage-advanced-invoice-export/
 * @license https://opensource.org/licenses/MIT
 */

class IPaulK_InvoiceExport_Block_Adminhtml_Sales_Invoice_Grid extends Mage_Adminhtml_Block_Sales_Invoice_Grid
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function _prepareMassaction()
    {
        parent::_prepareMassaction();

        $this->getMassactionBlock()->addItem('exportAdvancedCsv', array(
             'label'=> Mage::helper('sales')->__('Export to Advanced CSV'),
             'url'  => $this->getUrl('invoiceexport/adminhtml_sales_invoice/exportAdvancedCsv'),
        ));

        return $this;
    }
}
