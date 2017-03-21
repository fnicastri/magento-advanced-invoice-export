<?php

/**
 * Invoice Export
 *
 * @category IPaulK
 * @package IPaulK_InvoiceExport
 * @link https://github.com/iPaulK/mage-advanced-invoice-export/
 * @license https://opensource.org/licenses/MIT
 */

class IPaulK_InvoiceExport_Block_Adminhtml_Sales_Invoice_Grid_Csv extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_invoice_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'sales/order_invoice_grid_collection';
    }

    protected function _prepareCollection()
    {
        $orderAliasName = 'order';
        $invoiceAliasName = 'invoice';
        $billingAliasName = 'billing_o_a';
        $shippingAliasName = 'shipping_o_a';
        $joinTable = 'sales_flat_order_address';

        $invoice_ids = Mage::registry('invoice_ids');
        
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->addFieldToFilter( 'main_table.entity_id', array('in' => $invoice_ids) );
        $collection->getSelect()
            ->joinLeft(
                array($orderAliasName => 'sales_flat_order'),
                "(main_table.order_id = {$orderAliasName}.entity_id)",
                array(
                    $orderAliasName . '.customer_email as customer_email',
                    $orderAliasName . '.shipping_description as shipping_description',
                    $orderAliasName . '.store_name as store_name',
                )
            )
            ->joinLeft(
                array($invoiceAliasName => 'sales_flat_invoice'),
                "(main_table.order_id = {$invoiceAliasName}.order_id)",
                array(
                    $invoiceAliasName . '.shipping_amount as shipping_amount',
                    $invoiceAliasName . '.tax_amount as tax_amount',
                    $invoiceAliasName . '.shipping_tax_amount as shipping_tax_amount',
                    $invoiceAliasName . '.subtotal as subtotal',
                    $invoiceAliasName . '.discount_amount as discount_amount',
                    $invoiceAliasName . '.total_qty as total_qty',
                )
            )
            ->joinLeft(
                array($billingAliasName => $joinTable),
                "(main_table.order_id = {$billingAliasName}.parent_id"
                    . " AND {$billingAliasName}.address_type = 'billing')",
                array(
                    $billingAliasName . '.firstname as billing_firstname',
                    $billingAliasName . '.lastname as billing_lastname',
                    $billingAliasName . '.street as billing_street',
                    $billingAliasName . '.city as billing_city',
                    $billingAliasName . '.telephone as billing_telephone',
                    $billingAliasName . '.postcode as billing_postcode',
                    $billingAliasName . '.region as billing_region',
                    $billingAliasName . '.country_id as billing_country',
                )
            )
            ->joinLeft(
                array($shippingAliasName => $joinTable),
                "(main_table.order_id = {$shippingAliasName}.parent_id"
                    . " AND {$shippingAliasName}.address_type = 'shipping')",
                array(
                    $shippingAliasName . '.firstname as shipping_firstname',
                    $shippingAliasName . '.lastname as shipping_lastname',
                    $shippingAliasName . '.street as shipping_street',
                    $shippingAliasName . '.city as shipping_city',
                    $shippingAliasName . '.telephone as shipping_telephone',
                    $shippingAliasName . '.postcode as shipping_postcode',
                    $shippingAliasName . '.region as shipping_region',
                    $shippingAliasName . '.country_id as shipping_country',
                )
            );

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('sales')->__('Invoice #'),
            'index'     => 'increment_id',
            'type'      => 'text',
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('sales')->__('Invoice Date'),
            'index'     => 'created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('order_increment_id', array(
            'header'    => Mage::helper('sales')->__('Order #'),
            'index'     => 'order_increment_id',
            'type'      => 'text',
        ));

        $this->addColumn('order_created_at', array(
            'header'    => Mage::helper('sales')->__('Order Date'),
            'index'     => 'order_created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('state', array(
            'header'    => Mage::helper('sales')->__('Status'),
            'index'     => 'state',
            'type'      => 'options',
            'options'   => Mage::getModel('sales/order_invoice')->getStates(),
        ));

        $this->addColumn('customer_email', array(
            'header'    => Mage::helper('sales')->__('Customer Email'),
            'index' => 'customer_email',
        ));

        $this->addColumn('total_qty', array(
            'header'    => Mage::helper('sales')->__('Total Qty'),
            'index' => 'total_qty',
        ));

        $this->addColumn('grand_total', array(
            'header'    => Mage::helper('customer')->__('Grand Total'),
            'index'     => 'grand_total',
            'type'      => 'currency',
            'align'     => 'right',
            'currency'  => 'order_currency_code',
        ));

        $this->addColumn('subtotal', array(
            'header'    => Mage::helper('customer')->__('Subtotal'),
            'index'     => 'subtotal',
            'type'      => 'currency',
            'align'     => 'right',
            'currency'  => 'order_currency_code',
        ));

        $this->addColumn('tax_amount', array(
            'header'    => Mage::helper('customer')->__('Total Tax'),
            'index'     => 'tax_amount',
            'type'      => 'currency',
            'align'     => 'right',
            'currency'  => 'order_currency_code',
        ));

        $this->addColumn('discount_amount', array(
            'header'    => Mage::helper('customer')->__('Discount Amount'),
            'index'     => 'discount_amount',
            'type'      => 'currency',
            'align'     => 'right',
            'currency'  => 'order_currency_code',
        ));

        // Billing Address
        $this->addColumn('billing_firstname', array(
            'header' => Mage::helper('sales')->__('Billing Address Firstname'),
            'index' => 'billing_firstname',
        ));

        $this->addColumn('billing_lastname', array(
            'header' => Mage::helper('sales')->__('Billing Address Lastname'),
            'index' => 'billing_lastname',
        ));

        $this->addColumn('billing_street', array(
            'header' => Mage::helper('sales')->__('Billing Address Street'),
            'index' => 'billing_street',
        ));

        $this->addColumn('billing_city', array(
            'header' => Mage::helper('sales')->__('Billing Address City'),
            'index' => 'billing_city',
        ));

        $this->addColumn('billing_postcode', array(
            'header' => Mage::helper('sales')->__('Billing Address Postcode'),
            'index' => 'billing_postcode',
        ));

        $this->addColumn('billing_telephone', array(
            'header' => Mage::helper('sales')->__('Billing Address Telephone'),
            'index' => 'billing_telephone',
        ));

        $this->addColumn('billing_region', array(
            'header' => Mage::helper('sales')->__('Billing Address Region'),
            'index' => 'billing_region',
        ));

        $this->addColumn('billing_country', array(
            'header' => Mage::helper('sales')->__('Billing Address Country'),
            'index' => 'billing_country',
        ));

        // Shipping Address
        $this->addColumn('shipping_firstname', array(
            'header' => Mage::helper('sales')->__('Shipping Address Firstname'),
            'index' => 'shipping_firstname',
        ));

        $this->addColumn('shipping_lastname', array(
            'header' => Mage::helper('sales')->__('Shipping Address Lastname'),
            'index' => 'shipping_lastname',
        ));

        $this->addColumn('shipping_street', array(
            'header' => Mage::helper('sales')->__('Shipping Address Street'),
            'index' => 'shipping_street',
        ));

        $this->addColumn('shipping_city', array(
            'header' => Mage::helper('sales')->__('Shipping Address City'),
            'index' => 'shipping_city',
        ));

        $this->addColumn('shipping_postcode', array(
            'header' => Mage::helper('sales')->__('Shipping Address Postcode'),
            'index' => 'shipping_postcode',
        ));

        $this->addColumn('shipping_telephone', array(
            'header' => Mage::helper('sales')->__('Shipping Address Telephone'),
            'index' => 'shipping_telephone',
        ));

        $this->addColumn('shipping_region', array(
            'header' => Mage::helper('sales')->__('Shipping Address Region'),
            'index' => 'shipping_region',
        ));

        $this->addColumn('shipping_country', array(
            'header' => Mage::helper('sales')->__('Shipping Address Country'),
            'index' => 'shipping_country',
        ));

        $this->addColumn('shipping_description', array(
            'header'    => Mage::helper('sales')->__('Shipping Description'),
            'index'     => 'shipping_description',
        ));
        
        $this->addColumn('shipping_amount', array(
            'header'    => Mage::helper('sales')->__('Shipping amount'),
            'index'     => 'shipping_amount',
            'type'      => 'currency',
            'align'     => 'right',
            'currency'  => 'order_currency_code',
        ));

        $this->addColumn('shipping_tax_amount', array(
            'header'    => Mage::helper('customer')->__('Shipping Tax Amount'),
            'index'     => 'shipping_tax_amount',
            'type'      => 'currency',
            'align'     => 'right',
            'currency'  => 'order_currency_code',
        ));

        $this->addColumn('payment_info', array(
            'header'    => Mage::helper('sales')->__('Payment Info'),
            'renderer'  =>  'IPaulK_InvoiceExport_Block_Adminhtml_Sales_Invoice_Grid_Renderer_Paymentinfo',
        ));

        $this->addColumn('product_info', array(
            'header'    => Mage::helper('sales')->__('Product Ids'),
            'renderer'  =>  'IPaulK_InvoiceExport_Block_Adminhtml_Sales_Invoice_Grid_Renderer_ProductsInfo',
        ));

        return parent::_prepareColumns();
    }
}