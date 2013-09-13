<?php
class BlueVisionTec_EnhancedPdfInvoice_Model_Config_Source_TaxDisplay
{
  public function toOptionArray()
  {
    return array(
      array('value' => 'percent', 'label' => Mage::helper('enhancedpdfinvoice')->__('Percent')),
      array('value' => 'amount', 'label' => Mage::helper('enhancedpdfinvoice')->__('Amount')),
    );
  }
}