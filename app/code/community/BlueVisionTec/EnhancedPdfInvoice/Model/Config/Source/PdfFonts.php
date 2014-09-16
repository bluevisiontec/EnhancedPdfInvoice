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
 * pdf font options source
 *
 * @category   BlueVisionTec
 * @package    BlueVisionTec_EnhancedPdfInvoice
 * @author     BlueVisionTec UG (haftungsbeschränkt) <magedev@bluevisiontec.eu>
 */
class BlueVisionTec_EnhancedPdfInvoice_Model_Config_Source_PdfFonts
{
  /**
   * pdf font options
   *
   * @return array
   */
  public function toOptionArray()
  {
    return array(
      array('value' => 'Courier', 'label' => Mage::helper('enhancedpdfinvoice')->__('Courier')),
      array('value' => 'Helvetica', 'label' => Mage::helper('enhancedpdfinvoice')->__('Helvetica')),
      array('value' => 'Times-Roman', 'label' => Mage::helper('enhancedpdfinvoice')->__('Times-Roman')),
    );
  }
}