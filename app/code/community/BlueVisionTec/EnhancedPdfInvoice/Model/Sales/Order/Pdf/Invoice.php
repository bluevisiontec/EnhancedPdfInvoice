<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   BlueVisionTec
 * @package    BlueVisionTec_EnhancedPdfInvoice
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2012 BlueVisionTec e.U. (http://www.bluevisiontec.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales Order Invoice PDF model
 *
 * @category   BlueVisionTec
 * @package    BlueVisionTec_EnhancedPdfInvoice
 * @author     Magento Core Team <core@magentocommerce.com>
 * @author     BlueVisionTec e.U. <magedev@bluevisiontec.eu>
 */
class BlueVisionTec_EnhancedPdfInvoice_Model_Sales_Order_Pdf_Invoice extends Mage_Sales_Model_Order_Pdf_Invoice
{

  /**
   * right end position of logo
   * 
   * @var int
   */
  protected $_logoRight;
  
  /**
   * bottom end position of logo
   * 
   * @var int
   */
  protected $_logoBottom;
  
  /**
   * full page width
   * 
   * @var int
   */
  protected $_iFullPageWith = 595;
  
  /**
   * full page height
   * 
   * @var int
   */
  protected $_iFullPageHeight= 842;
  
  /**
   * left page margin
   * 
   * @var int
   */
  protected $_iLeftMargin = 25;
  
  /**
   * right page margin
   * 
   * @var int
   */
  protected $_iRightMargin = 25;
  
  /**
   * top page margin
   * 
   * @var int
   */
  protected $_iTopMargin = 12;
  
  /**
   * left bottom margin
   * 
   * @var int
   */
  protected $_iBottomMargin = 25;
  
  /**
   * letter window left
   * 
   * @var int
   */
  protected $_iLetterWindowLeft = 60; // ~21mm
  
  /**
   * letter window top
   * 
   * @var int
   */
  protected $_iLetterWindowTop = 139; // ~49mm
  
  /**
   * letter window width
   * 
   * @var int
   */
  protected $_iLetterWindowWidth = 210; // ~74mm
  
  /**
   * letter window height
   * 
   * @var int
   */
  protected $_iLetterWindowHeight = 85; // ~49mm
  
  /**
   * separator sign
   * 
   * @var string
   */
  protected $_sSeparatorSign = " \xE2\x80\xA2 ";
  
  
 /**
  * Set font as regular
  *
  * @param  Zend_Pdf_Page $object
  * @param  int $size
  * @return Zend_Pdf_Resource_Font
  */
  protected function _setFontRegular($object, $size = 7)
  {
    $font = Zend_Pdf_Font::fontWithName(Mage::getStoreConfig("bvt_enhancedpdfinvoice_config/design_settings/font_type"));
    $object->setFont($font, $size);
    return $font;
  }
  
  /**
  * Set font as bold
  *
  * @param  Zend_Pdf_Page $object
  * @param  int $size
  * @return Zend_Pdf_Resource_Font
  */
  protected function _setFontBold($object, $size = 7)
  {
    $font = Zend_Pdf_Font::fontWithName(Mage::getStoreConfig("bvt_enhancedpdfinvoice_config/design_settings/font_type")."-Bold");
    $object->setFont($font, $size);
    return $font;
  }

  /**
  * Set font as italic
  *
  * @param  Zend_Pdf_Page $object
  * @param  int $size
  * @return Zend_Pdf_Resource_Font
  */
  protected function _setFontItalic($object, $size = 7)
  {
    $font = Zend_Pdf_Font::fontWithName(Mage::getStoreConfig("bvt_enhancedpdfinvoice_config/design_settings/font_type")."-Italic");
    $object->setFont($font, $size);
    return $font;
  }
  
  /**
  * Return PDF document
  *
  * @param  array $invoices
  * @return Zend_Pdf
  */
  public function getPdf($invoices = array())
  {
      $this->_beforeGetPdf();
      $this->_initRenderer('invoice');

      $pdf = new Zend_Pdf();
      $this->_setPdf($pdf);
      $style = new Zend_Pdf_Style();
      $this->_setFontBold($style, 10);

      foreach ($invoices as $invoice) {
          if ($invoice->getStoreId()) {
              Mage::app()->getLocale()->emulate($invoice->getStoreId());
              Mage::app()->setCurrentStore($invoice->getStoreId());
          }
          $page  = $this->newPage();
          $order = $invoice->getOrder();
          /* Add image */
          $this->insertLogo($page, $invoice->getStore());
          /* Add address */
          $this->insertAddress($page, $invoice->getStore());
          /* Add head */

          $this->_insertOrder(
              $page,
              $order,
              $invoice,
              Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID, $order->getStoreId()
              )
          );
          /* Add document text and number */
          $this->insertDocumentNumber(
              $page,
              Mage::helper('sales')->__('Invoice # ') . $invoice->getIncrementId()
          );
          /* Add table */
          $this->_drawHeader($page);
          /* Add body */
          foreach ($invoice->getAllItems() as $item){
              if ($item->getOrderItem()->getParentItem()) {
                  continue;
              }
              /* Draw item */
              $this->_drawItem($item, $page, $order);
              $page = end($pdf->pages);
          }
          /* Add totals */
          $this->insertTotals($page, $invoice);
          if ($invoice->getStoreId()) {
              Mage::app()->getLocale()->revert();
          }
      }
      $this->_afterGetPdf();
      return $pdf;
  }
  
  /**
  * Insert logo to pdf page
  *
  * @param Zend_Pdf_Page $page
  * @param null $store
  */
  protected function insertLogo(&$page, $store = null)
  {
    $this->y = $this->y ? $this->y : 815;
    $image = Mage::getStoreConfig('bvt_enhancedpdfinvoice_config/design_settings/logo_image', $store);
    if ($image) {
      $image = Mage::getBaseDir('media') . '/bvt/enhancedpdfinvoice/' . $image;
      if (is_file($image)) {
        $image       = Zend_Pdf_Image::imageWithPath($image);
        $top         = $this->_iFullPageHeight - $this->_iTopMargin; //top border of the page
        $widthLimit  = 200; //half of the page width
        $heightLimit = 120; //assuming the image is not a "skyscraper"
        $width       = $image->getPixelWidth();
        $height      = $image->getPixelHeight();

        //preserving aspect ratio (proportions)
        $ratio = $width / $height;
        if ($ratio > 1 && $width > $widthLimit) {
          $width  = $widthLimit;
          $height = $width / $ratio;
        } elseif ($ratio < 1 && $height > $heightLimit) {
          $height = $heightLimit;
          $width  = $height * $ratio;
        } elseif ($ratio == 1 && $height > $heightLimit) {
          $height = $heightLimit;
          $width  = $widthLimit;
        }
        
        if($height > $heightLimit) {
          $height = $heightLimit;
          $width  = $height * $ratio;
        }

        $y1 = $top - $height;
        $y2 = $top;
        $x1 = $this->_iLeftMargin;
        $x2 = $x1 + $width;
        
        //coordinates after transformation are rounded by Zend
        $page->drawImage($image, $x1, $y1, $x2, $y2);

        $this->y = $y1 - 10;
        
        $this->_logoRight = $width + $x1;
        $this->_logoBottom = $this->y;
      }
    }
  }
  
  /**
  * Insert address to pdf page
  *
  * @param Zend_Pdf_Page $page
  * @param null $store
  */
  protected function insertAddress(&$page, $store = null)
  {
      $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
      $font = $this->_setFontRegular($page, 7);
      $page->setLineWidth(0);
      $this->y = $this->y ? $this->y : 815;
      $top = 815;
      
      $sStoreAdress = trim(strip_tags(Mage::getStoreConfig('general/imprint/shop_name', $store)));
      $sStoreAdress .=  $this->_sSeparatorSign;
      $sStoreAdress .= trim(strip_tags(Mage::getStoreConfig('general/imprint/company_first', $store)));
      $sStoreAdress .=  $this->_sSeparatorSign;
      $sStoreAdress .= trim(strip_tags(Mage::getStoreConfig('general/imprint/street', $store)));
      $sStoreAdress .=  $this->_sSeparatorSign;
      $sStoreAdress .= trim(strip_tags(Mage::getStoreConfig('general/imprint/zip', $store)));
      $sStoreAdress .=  " ";
      $sStoreAdress .= trim(strip_tags(Mage::getStoreConfig('general/imprint/city', $store)));
      $sStoreAdress .=  $this->_sSeparatorSign;
      $sStoreAdress .= trim(strip_tags(Mage::getStoreConfig('general/imprint/web', $store)));

      $page->drawText(trim(strip_tags($sStoreAdress)),
                      $this->_logoRight + 5,
                      $this->_logoBottom + 10,
                      'UTF-8');
                      
      $x1 = $this->_logoRight + 5;
      $x2 = $this->_iFullPageWith - $this->_iRightMargin;
      $y1 = $this->_logoBottom + 8;
      $y2 = $y1;
                      
      $page->drawLine($x1,$y1,$x2,$y2);
                      
      $this->y = ($this->y > $y1) ? $y1 : $this->y;
  }
  
  /**
   * Insert customer billing address to pdf page
   *
   * @param Zend_Pdf_Page $oPage
   * @param Mage_Sales_Model_Order $oOrder
   */
  protected function insertCustomerBillingAddress(&$oPage, $oOrder)
  {
    $oPage->setFillColor(new Zend_Pdf_Color_GrayScale(0));
    $font = $this->_setFontRegular($oPage, 7);
    $oPage->setLineWidth(0);
    
    $sStoreAdress = trim(strip_tags(Mage::getStoreConfig('general/imprint/shop_name', $store)));
    $sStoreAdress .=  $this->_sSeparatorSign;
    $sStoreAdress .= trim(strip_tags(Mage::getStoreConfig('general/imprint/company_first', $store)));
    $sStoreAdress .=  $this->_sSeparatorSign;
    $sStoreAdress .= trim(strip_tags(Mage::getStoreConfig('general/imprint/street', $store)));
    $sStoreAdress .=  $this->_sSeparatorSign;
    $sStoreAdress .= trim(strip_tags(Mage::getStoreConfig('general/imprint/zip', $store)));
    $sStoreAdress .=  " ";
    $sStoreAdress .= trim(strip_tags(Mage::getStoreConfig('general/imprint/city', $store)));

    $oPage->drawText(trim(strip_tags($sStoreAdress)),
                    $this->_iLetterWindowLeft,
                    $this->_iFullPageHeight - $this->_iTopMargin - $this->_iLetterWindowTop,
                    'UTF-8');
                    
    $x1 = $this->_iLetterWindowLeft;
    $x2 = $this->_iLetterWindowLeft + $this->_iLetterWindowWidth;
    $y1 = $this->_iFullPageHeight - $this->_iTopMargin - $this->_iLetterWindowTop -2;
    $y2 = $y1;
                    
    $oPage->drawLine($x1,$y1,$x2,$y2);
         
    $y1 -= 12;     
         
    $this->y = ($this->y > $y1) ? $y1 : $this->y;
    
    $oBillingAddress = $oOrder->getBillingAddress();
    
    $font = $this->_setFontBold($oPage, 10);
    
    if($oBillingAddress->getCompany())
    {
      $oPage->drawText(
        strip_tags(trim($oBillingAddress->getCompany())), 
        $this->_iLetterWindowLeft, $this->y, 'UTF-8');
      $this->y -= 12;
      
      $font = $this->_setFontRegular($oPage, 10);
    }
    
    $sName = Mage::helper('enhancedpdfinvoice')->__($oBillingAddress->getSalutation());
    $sName .= ($sName != "") ? " " : "";
    $sName .= $oBillingAddress->getFirstname();
    $sName .= " ". $oBillingAddress->getLastname();
    
    $oPage->drawText(
      strip_tags(trim($sName)), 
      $this->_iLetterWindowLeft, $this->y, 'UTF-8');
    $this->y -= 12;
    
    $font = $this->_setFontRegular($oPage, 10);
    
    $sStreet = $oBillingAddress->getStreet(1);
    $sStreet .= ($oBillingAddress->getStreet(2)) ? ", ".$oBillingAddress->getStreet(2) : "";
    
    $oPage->drawText(
      strip_tags(trim($sStreet)), 
      $this->_iLetterWindowLeft, $this->y, 'UTF-8');
    $this->y -= 12;
    
    $oPage->drawText(
      strip_tags(trim($oBillingAddress->getCountry()." - ".$oBillingAddress->getPostcode()." ".$oBillingAddress->getCity())), 
      $this->_iLetterWindowLeft, $this->y, 'UTF-8');
    $this->y -= 12;    
    
  }
  
  /**
    * Insert order to pdf page
    *
    * @param Zend_Pdf_Page $page
    * @param Mage_Sales_Model_Order $obj
    * @param Mage_Sales_Model_Invoice $invoice
    * @param bool $putOrderId
    */
  protected function _insertOrder(&$page, $obj, $oInvoice, $putOrderId = true)
  {
      if ($obj instanceof Mage_Sales_Model_Order) {
          $shipment = null;
          $order = $obj;
      } elseif ($obj instanceof Mage_Sales_Model_Order_Shipment) {
          $shipment = $obj;
          $order = $shipment->getOrder();
      }

      $this->insertCustomerBillingAddress($page,$order);
      
      $this->y = $this->y ? $this->y : $this->_iFullPageHeight - $this->_iTopMargin - $this->_iLetterWindowTop - $this->_iLetterWindowHeight -10;

      $this->y -= 20;
      
      $this->_setFontBold($page, 14);
      $page->drawText(
              Mage::helper('sales')->__('Invoice'), $this->_iLeftMargin, $this->y, 'UTF-8'
          );
          
      $this->y -= 20;
      
      
      $font = $this->_setFontBold($page, 10);
      
      $oModelCustomer = Mage::getModel('customer/customer')->load($order->getCustomerId());
      $sCustomerNumber = $oModelCustomer->getIncrementId();
      $sCustomerNumber = ($sCustomerNumber) ? $sCustomerNumber : "-";
      
      $page->drawText(
              Mage::helper('sales')->__('Customer-Nr.: ').$sCustomerNumber, $this->_iLeftMargin, $this->y, 'UTF-8'
          );
 
      if($oInvoice !== null)
      {
        $sInvoiceNumber =  Mage::helper('sales')->__('Invoice-Nr.: ').$oInvoice->getIncrementId();
        $iWidth = $this->widthForStringUsingFontSize($sInvoiceNumber, $font, 10);
        $page->drawText(
              $sInvoiceNumber, ($this->_iFullPageWith - $this->_iLeftMargin - $this->_iRightMargin)/2 - $iWidth/2, $this->y, 'UTF-8'
            );
      }    
      $sOrderDate = Mage::helper('sales')->__('Order Date: ') . Mage::helper('core')->formatDate(
              $order->getCreatedAtStoreDate(), 'medium', false
          );
          
      $iWidth = $this->widthForStringUsingFontSize($sOrderDate, $font, 10);
      
      $page->drawText(
             $sOrderDate, ($this->_iFullPageWith - $this->_iRightMargin) - $iWidth, $this->y, 'UTF-8'
          );
      
      $this->_setFontRegular($page, 10);
      
      $this->y -= 10;

      $top = $this->y;
      /*if ($putOrderId) {
          $page->drawText(
              Mage::helper('sales')->__('Order # ') . $order->getRealOrderId(), 35, ($top -= 30), 'UTF-8'
          );
      }*/

      $top -= 10;
      

      /* Payment */
      $paymentInfo = Mage::helper('payment')->getInfoBlock($order->getPayment())
          ->setIsSecureMode(true)
          ->toPdf();
      $paymentInfo = htmlspecialchars_decode($paymentInfo, ENT_QUOTES);
      $payment = explode('{{pdf_row_separator}}', $paymentInfo);
      foreach ($payment as $key=>$value){
          if (strip_tags(trim($value)) == '') {
              unset($payment[$key]);
          }
      }
      reset($payment);

      /* Shipping Address and Method */
      if (!$order->getIsVirtual()) {
          /* Shipping Address */
          $shippingAddress = $this->_formatAddress($order->getShippingAddress()->format('pdf'));
          $shippingMethod  = $order->getShippingDescription();
      }

      $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

      $this->_setFontRegular($page, 10);

      if (!$order->getIsVirtual()) {

          $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
          $page->setLineWidth(0.5);
          $page->drawRectangle(25, $this->y, 275, $this->y-25);
          $page->drawRectangle(275, $this->y, 570, $this->y-25);

          $this->y -= 15;
          $this->_setFontBold($page, 12);
          $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
          $page->drawText(Mage::helper('sales')->__('Payment Method'), 35, $this->y, 'UTF-8');
          $page->drawText(Mage::helper('sales')->__('Shipping Method:'), 285, $this->y , 'UTF-8');

          $this->y -=10;
          $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));

          $this->_setFontRegular($page, 10);
          $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

          $paymentLeft = 35;
          $yPayments   = $this->y - 15;
      }
      else {
          $yPayments   = $addressesStartY;
          $paymentLeft = 285;
      }

      foreach ($payment as $value){
          if (trim($value) != '') {
              //Printing "Payment Method" lines
              $value = preg_replace('/<br[^>]*>/i', "\n", $value);
              foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
                  $page->drawText(strip_tags(trim($_value)), $paymentLeft, $yPayments, 'UTF-8');
                  $yPayments -= 12;
              }
          }
      }

      if ($order->getIsVirtual()) {
          // replacement of Shipments-Payments rectangle block
          $yPayments = min($addressesEndY, $yPayments);
          $page->drawLine(25,  ($top - 25), 25,  $yPayments);
          $page->drawLine(570, ($top - 25), 570, $yPayments);
          $page->drawLine(25,  $yPayments,  570, $yPayments);

          $this->y = $yPayments - 15;
      } else {
          $topMargin    = 15;
          $methodStartY = $this->y;
          $this->y     -= 15;

          foreach (Mage::helper('core/string')->str_split($shippingMethod, 45, true, true) as $_value) {
              $page->drawText(strip_tags(trim($_value)), 285, $this->y, 'UTF-8');
              $this->y -= 15;
          }

          $yShipments = $this->y;
          $totalShippingChargesText = "(" . Mage::helper('sales')->__('Total Shipping Charges') . " "
              . $order->formatPriceTxt($order->getShippingAmount()) . ")";

          $page->drawText($totalShippingChargesText, 285, $yShipments - $topMargin, 'UTF-8');
          $yShipments -= $topMargin + 10;

          $tracks = array();
          if ($shipment) {
              $tracks = $shipment->getAllTracks();
          }
          if (count($tracks)) {
              $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
              $page->setLineWidth(0.5);
              $page->drawRectangle(285, $yShipments, 510, $yShipments - 10);
              $page->drawLine(400, $yShipments, 400, $yShipments - 10);
              //$page->drawLine(510, $yShipments, 510, $yShipments - 10);

              $this->_setFontRegular($page, 9);
              $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
              //$page->drawText(Mage::helper('sales')->__('Carrier'), 290, $yShipments - 7 , 'UTF-8');
              $page->drawText(Mage::helper('sales')->__('Title'), 290, $yShipments - 7, 'UTF-8');
              $page->drawText(Mage::helper('sales')->__('Number'), 410, $yShipments - 7, 'UTF-8');

              $yShipments -= 20;
              $this->_setFontRegular($page, 8);
              foreach ($tracks as $track) {

                  $CarrierCode = $track->getCarrierCode();
                  if ($CarrierCode != 'custom') {
                      $carrier = Mage::getSingleton('shipping/config')->getCarrierInstance($CarrierCode);
                      $carrierTitle = $carrier->getConfigData('title');
                  } else {
                      $carrierTitle = Mage::helper('sales')->__('Custom Value');
                  }

                  //$truncatedCarrierTitle = substr($carrierTitle, 0, 35) . (strlen($carrierTitle) > 35 ? '...' : '');
                  $maxTitleLen = 45;
                  $endOfTitle = strlen($track->getTitle()) > $maxTitleLen ? '...' : '';
                  $truncatedTitle = substr($track->getTitle(), 0, $maxTitleLen) . $endOfTitle;
                  //$page->drawText($truncatedCarrierTitle, 285, $yShipments , 'UTF-8');
                  $page->drawText($truncatedTitle, 292, $yShipments , 'UTF-8');
                  $page->drawText($track->getNumber(), 410, $yShipments , 'UTF-8');
                  $yShipments -= $topMargin - 5;
              }
          } else {
              $yShipments -= $topMargin - 5;
          }

          $currentY = min($yPayments, $yShipments);

          // replacement of Shipments-Payments rectangle block
          $page->drawLine(25,  $methodStartY, 25,  $currentY); //left
          $page->drawLine(25,  $currentY,     570, $currentY); //bottom
          $page->drawLine(570, $currentY,     570, $methodStartY); //right

          $this->y = $currentY;
          $this->y -= 15;
      }
  }

}