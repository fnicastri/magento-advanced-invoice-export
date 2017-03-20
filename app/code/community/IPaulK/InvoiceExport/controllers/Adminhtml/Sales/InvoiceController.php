<?php

/**
 * Invoice Export
 *
 * @category IPaulK
 * @package IPaulK_InvoiceExport
 * @link https://github.com/iPaulK/mage-advanced-invoice-export/
 * @license https://opensource.org/licenses/MIT
 */

require_once(Mage::getModuleDir('controllers', 'Mage_Adminhtml') . DS . 'Sales' . DS . 'InvoiceController.php');

class IPaulK_InvoiceExport_Adminhtml_Sales_InvoiceController extends Mage_Adminhtml_Sales_InvoiceController
{
    /**
     * Export invoice grid to CSV format
     */
    public function exportAdvancedCsvAction()
    {
        $invoicesIds = $this->getRequest()->getPost('invoice_ids');
        if (!empty($invoicesIds)) {
            Mage::register('invoice_ids', $invoicesIds);
            $fileName   = 'invoices.csv';
            $grid       = $this->getLayout()->createBlock('ipaulk_invoiceexport/adminhtml_sales_invoice_grid_csv');
            $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
        }
    }
}
