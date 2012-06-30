<?php
class BlueVisionTec_EnhancedPdfInvoice_Model_Config_Source_PdfFonts
{
  public function toOptionArray()
  {
    return array(
      array('value' => 'Courier', 'label' => Mage::helper('enhancedpdfinvoice')->__('Courier')),
      array('value' => 'Helvetica', 'label' => Mage::helper('enhancedpdfinvoice')->__('Helvetica')),
      array('value' => 'Times-Roman', 'label' => Mage::helper('enhancedpdfinvoice')->__('Times-Roman')),
    );
  }
}