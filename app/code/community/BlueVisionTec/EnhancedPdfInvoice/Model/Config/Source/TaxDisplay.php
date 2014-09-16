<?php
/**
 * Magento Module BlueVisionTec_EnhancedPdfInvoice
 *
 * @category   BlueVisionTec
 * @package    BlueVisionTec_EnhancedPdfInvoice
 * @copyright   Copyright (c) 2014 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * tax display options source
 *
 * @category   BlueVisionTec
 * @package    BlueVisionTec_EnhancedPdfInvoice
 * @author     BlueVisionTec UG (haftungsbeschränkt) <magedev@bluevisiontec.eu>
 */
class BlueVisionTec_EnhancedPdfInvoice_Model_Config_Source_TaxDisplay
{
	/**
	 * tax display options
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		return array(
			array('value' => 'percent', 'label' => Mage::helper('enhancedpdfinvoice')->__('Percent')),
			array('value' => 'amount', 'label' => Mage::helper('enhancedpdfinvoice')->__('Amount')),
		);
	}
}