<?php

/**
 * Invoice Export
 *
 * @category IPaulK
 * @package IPaulK_InvoiceExport
 * @link https://github.com/iPaulK/mage-advanced-invoice-export/
 * @license https://opensource.org/licenses/MIT
 */

class IPaulK_InvoiceExport_Block_Adminhtml_Sales_Invoice_Grid_Renderer_Paymentinfo extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {  
        $order_id = $row->getData('order_id');
        $order = Mage::getModel('sales/order')->load($order_id);
        $payment = $order->getPayment();
        $paymentInfoBlock = Mage::helper('payment')->getInfoBlock($payment);

        $info = '';
        if ($paymentInfoBlock instanceof Ess_M2ePro_Block_Adminhtml_Magento_Payment_Info) {
            $method = $channelFinalFee = $tax = $transactionsInfo;
            if ($paymentInfoBlock->getPaymentMethod() != '') {
                $method = Mage::helper('M2ePro')->__('Payment Method: ', $paymentInfoBlock->escapeHtml($paymentInfoBlock->getPaymentMethod()));
            }

            if ($paymentInfoBlock->getChannelOrderId() != '') {
                $message = Mage::helper('M2ePro')->__('%channel_title% Order ID', $paymentInfoBlock->getChannelTitle());
                $method .= Mage::helper('M2ePro')->__($message);
                $method .= ':';
                $method .= $paymentInfoBlock->getChannelOrderId();
            }

            if ($paymentInfoBlock->getChannelFinalFee() > 0) {
                $message = Mage::helper('M2ePro')->__('%channel_title% Final Fee', $paymentInfoBlock->getChannelTitle());
                $channelFinalFee .= Mage::helper('M2ePro')->__($message);
                $channelFinalFee .= ':';
                $channelFinalFee .=  !is_null($paymentInfoBlock->getOrder()) ? $paymentInfoBlock->getOrder()->formatPrice($paymentInfoBlock->getChannelFinalFee()) : $paymentInfoBlock->getChannelFinalFee();
            }

            if ($paymentInfoBlock->getTaxId() != '') {
                $message = Mage::helper('M2ePro')->__('Buyer Tax ID');
                $tax .= Mage::helper('M2ePro')->__($message);
                $tax .= ':';
                $tax .= $paymentInfoBlock->getTaxId();
            }

            $transactions = $paymentInfoBlock->getTransactions(); 
            if (!empty($transactions)) {
                $transactionsInfo = Mage::helper('M2ePro')->__('Transactions (#/fee/sum/date)');
                foreach ($transactions as $key => $transaction) {
                    $params[] = $transaction['transaction_id'];
                    $params[] = $transaction['fee'];
                    $params[] = $transaction['sum'];
                    $params[] = Mage::helper('core')->formatDate($transaction['transaction_date'], 'medium', true);

                    $transactionsInfo .= implode('/', $params);
                }
            }
$info .= <<<INFO
$method
$channelFinalFee
$tax
$transactionsInfo
INFO;
        } else if (
            ($paymentInfoBlock instanceof Ess_M2ePro_Block_Adminhtml_Magento_Payment_Info) 
                    && $_specificInfo = $paymentInfoBlock->getSpecificInformation()
        ) {
            foreach ($_specificInfo as $_label => $_value) {
                $info .= $paymentInfoBlock->escapeHtml($_label);
                $info .= ':'; 
                $info .= nl2br(implode($paymentInfoBlock->getValueAsArray($_value, true), "\n"));
            }
        }
        return $info;
    }
}