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
 * @category   BlueVisionTec
 * @package    BlueVisionTec_EnhancedPdfInvoice
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2013 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales Order Invoice PDF model
 *
 * @category   BlueVisionTec
 * @package    BlueVisionTec_EnhancedPdfInvoice
 * @author     Magento Core Team <core@magentocommerce.com>
 * @author     BlueVisionTec UG (haftungsbeschränkt) <magedev@bluevisiontec.eu>
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
  protected $_iFullPageWith = 590;
  
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
  protected $_iLeftMargin = 60;
  
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
    $sItalic = (Mage::getStoreConfig("bvt_enhancedpdfinvoice_config/design_settings/font_type") == "Helvetica") ? "-Oblique" : "-Italic";
    $font = Zend_Pdf_Font::fontWithName(Mage::getStoreConfig("bvt_enhancedpdfinvoice_config/design_settings/font_type").$sItalic);
    $object->setFont($font, $size);
    return $font;
  }
  
  public function newPage(array $settings = array())
  {
    $page = parent::newPage();
    $page = $this->_drawFoldingMarks($page);
    $page = $this->_drawFooter($page);
    return $page;
  }
  
  
  
  protected function _drawFoldingMarks($oPage)
  {
    $iWidth = 14;
    
    $oPage->setFillColor(new Zend_Pdf_Color_GrayScale(0));
    $oPage->setLineWidth(0);
    
    $x1 = $iWidth;
    $x2 = $x1 + $iWidth * 2;
    $y1 = 246;
    $y2 = $y1;
                    
    $oPage->drawLine($x1,$y1,$x2,$y2);
    
    $x1 = $iWidth * 2;
    $x2 = $x1 + $iWidth;
    $y1 = 420;
    $y2 = $y1;
                    
    $oPage->drawLine($x1,$y1,$x2,$y2);
    
    $x1 = $iWidth;
    $x2 = $x1 + $iWidth * 2;
    $y1 = 544;
    $y2 = $y1;
                    
    $oPage->drawLine($x1,$y1,$x2,$y2);
    
    return $oPage;
  }
  
  /**
     * Draw header for item table
     *
     * @param Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(Zend_Pdf_Page $page)
    {
        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $page->setLineColor(new Zend_Pdf_Color_RGB(0.8, 0.8, 0.8));
        $page->setLineWidth(0.5);
        $page->drawRectangle(60, $this->y, 560, $this->y -15);
        $this->y -= 10;
        $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));

        $lines[0][] = array(
            'text'  => Mage::helper('enhancedpdfinvoice')->__('SKU '),
            'feed'  => 70,
            'align' => 'left'
        );
        
        //columns headers
        $lines[0][] = array(
            'text' => Mage::helper('sales')->__('Products'),
            'feed' => 135
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Qty'),
            'feed'  => 415,
            'align' => 'left'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Single Price'),
            'feed'  => 350,
            'align' => 'left'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Tax'),
            'feed'  => 455,
            'align' => 'left'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Price'),
            'feed'  => 525,
            'align' => 'left'
        );

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 5
        );

        $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }
  
  protected function _drawFooter(Zend_Pdf_Page $page) {
    
    $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
    $font = $this->_setFontRegular($page, 7);
    $page->setLineWidth(0);
    
  
    $y = $this->_iBottomMargin;
    $x = $this->_iLeftMargin;
    
    $sCompany = trim(strip_tags(Mage::getStoreConfig('general/imprint/company_first', null))); // set store
    $sZip = trim(strip_tags(Mage::getStoreConfig('general/imprint/zip', null))); // set store
    $sCity = trim(strip_tags(Mage::getStoreConfig('general/imprint/city', null))); // set store
    $sZipCity = $sZip . " ". $sCity;
    $sStreet = trim(strip_tags(Mage::getStoreConfig('general/imprint/street', null))); // set store
    $sShop = trim(strip_tags(Mage::getStoreConfig('general/imprint/shop_name', null))); // set store
    $sCEO = trim(strip_tags(Mage::getStoreConfig('general/imprint/ceo', null)));
    $sOwner = trim(strip_tags(Mage::getStoreConfig('general/imprint/owner', null)));
    $sName = ($sCEO != "")? $sCEO : $sOwner;
    $sVATID = trim(strip_tags(Mage::getStoreConfig('general/imprint/vat_id', null)));
    $sTaxNumber = trim(strip_tags(Mage::getStoreConfig('general/imprint/tax_number', null)));
    
    $registerCourt = trim(strip_tags(Mage::getStoreConfig('general/imprint/court', null)));
    $registerNumber = trim(strip_tags(Mage::getStoreConfig('general/imprint/register_number', null)));
    
    $countryName = Mage::app()->getLocale()->getCountryTranslation(Mage::getStoreConfig('general/imprint/country', null));
    
    $register = $registerCourt;
    $register .= empty($registerNumber) ? "" : " / " . $registerNumber;
    
    $page->drawText(trim(strip_tags($register)),$x,$y,'UTF-8');
    $y += 20;
    
    $page->drawText(trim(strip_tags($countryName)),$x,$y,'UTF-8');
    $y += 10;
    $page->drawText(trim(strip_tags($sZipCity)),$x,$y,'UTF-8');
    $y += 10;
    $page->drawText(trim(strip_tags($sStreet)),$x,$y,'UTF-8');
    $y += 10;
    $page->drawText(trim(strip_tags($sName)),$x,$y,'UTF-8');
    $y += 10;
    if($sCompany != $sShop) {
      $page->drawText(trim(strip_tags($sCompany)),$x,$y,'UTF-8');
      $y += 10;
    }
    $font = $this->_setFontBold($page, 7);
    $page->drawText(trim(strip_tags($sShop)),$x,$y,'UTF-8');
    $font = $this->_setFontRegular($page, 7);
    
    $y = $this->_iBottomMargin;
    $x = ($this->_iFullPageWith) /2;
    
    $sPhone = trim(strip_tags(Mage::getStoreConfig('general/imprint/telephone', null))); // set store,
    $sFax = trim(strip_tags(Mage::getStoreConfig('general/imprint/fax', null))); // set store
    $sEmail = trim(strip_tags(Mage::getStoreConfig('general/imprint/email', null))); // set store
    $sWeb = trim(strip_tags(Mage::getStoreConfig('general/imprint/web', null))); // set store
    
    if($sPhone) {
      $sPhone = Mage::helper("enhancedpdfinvoice")->__("Phone").": " . $sPhone;
    }
    if($sFax) {
      $sFax = Mage::helper("enhancedpdfinvoice")->__("Fax").": " . $sFax;
    }
    if($sEmail) {
      $sEmail = Mage::helper("enhancedpdfinvoice")->__("E-Mail").": " . $sEmail;
    }
    if($sWeb) {
      $sWeb = Mage::helper("enhancedpdfinvoice")->__("Web").": " . $sWeb;
    }
    
    $iMaxTextWidth = max(
      $this->widthForStringUsingFontSize($sPhone, $font, 7),
      $this->widthForStringUsingFontSize($sFax, $font, 7),
      $this->widthForStringUsingFontSize($sEmail, $font, 7),
      $this->widthForStringUsingFontSize($sWeb, $font, 7)
    );
    
    $x = $x - ($iMaxTextWidth/2);
    
    $y += 20;
    
    if($sVATID != "") {
      $sVATID = Mage::helper("enhancedpdfinvoice")->__("VAT ID").": ".$sVATID;
      $page->drawText(trim(strip_tags($sVATID)),$x,$y,'UTF-8');
      $y += 10;
    }
    if($sTaxNumber != "") {
      $sTaxNumber = Mage::helper("enhancedpdfinvoice")->__("Tax number").": ".$sTaxNumber;
      $page->drawText(trim(strip_tags($sTaxNumber)),$x,$y,'UTF-8');
      $y += 10;
    }
    $y += 10;
    $page->drawText(trim(strip_tags($sWeb)),$x,$y,'UTF-8');
    $y += 10;
    $page->drawText(trim(strip_tags($sEmail)),$x,$y,'UTF-8');
    $y += 10;
    if($sFax) {
      $page->drawText(trim(strip_tags($sFax)),$x,$y,'UTF-8');
    }
    $y += 10;
    $page->drawText(trim(strip_tags($sPhone)),$x,$y,'UTF-8');
    
    $y = $this->_iBottomMargin;
    $y += 30;
    
    $sBankName = trim(strip_tags(Mage::getStoreConfig('general/imprint/bank_name', null))); // set store,
    $sBankCodeNumber = trim(strip_tags(Mage::getStoreConfig('general/imprint/bank_code_number', null))); // set store
    $sBankAccount = trim(strip_tags(Mage::getStoreConfig('general/imprint/bank_account', null))); // set store
    $sIban = trim(strip_tags(Mage::getStoreConfig('general/imprint/iban', null))); // set store
    $sSwift = trim(strip_tags(Mage::getStoreConfig('general/imprint/swift', null))); // set store
    
    if($sBankName) {
      $sBankName = Mage::helper("enhancedpdfinvoice")->__("Bank").": " . $sBankName;
    }
    if($sBankCodeNumber) {
      $sBankCodeNumber = Mage::helper("enhancedpdfinvoice")->__("Bank code number").": " . $sBankCodeNumber;
    }
    if($sBankAccount) {
      $sBankAccount = Mage::helper("enhancedpdfinvoice")->__("Bank account number").": " . $sBankAccount;
    }
    if($sIban) {
      $sIban = Mage::helper("enhancedpdfinvoice")->__("IBAN").": " . $sIban;
    }
    if($sSwift) {
      $sSwift = Mage::helper("enhancedpdfinvoice")->__("BIC/SWIFT").": " . $sSwift;
    }
    
    $iMaxTextWidth = max(
      $this->widthForStringUsingFontSize($sBankName, $font, 7),
      $this->widthForStringUsingFontSize($sBankCodeNumber, $font, 7),
      $this->widthForStringUsingFontSize($sBankAccount, $font, 7),
      $this->widthForStringUsingFontSize($sIban, $font, 7),
      $this->widthForStringUsingFontSize($sSwift, $font, 7)
    );
    
    $x = $this->_iFullPageWith - $this->_iRightMargin - $iMaxTextWidth;
    
    $page->drawText(trim(strip_tags($sIban)),$x,$y,'UTF-8');
    $y += 10;
    $page->drawText(trim(strip_tags($sSwift)),$x,$y,'UTF-8');
    $y += 10;
    $page->drawText(trim(strip_tags($sBankAccount)),$x,$y,'UTF-8');
    $y += 10;
    $page->drawText(trim(strip_tags($sBankCodeNumber)),$x,$y,'UTF-8');
    $y += 10;
    $page->drawText(trim(strip_tags($sBankName)),$x,$y,'UTF-8');
    
    
    
    return $page;
    
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

          $this->_insertOrderHead(
              $page,
              $order,
              $invoice,
              Mage::getStoreConfigFlag(
                self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID, 
                $order->getStoreId()
              )
          );
          /* Add document text and number */
          $this->insertDocumentNumber(
              $page,
              Mage::helper('sales')->__('Invoice # ') 
                . $invoice->getIncrementId()
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
          
          $yBeforeTotals = $this->y;
          
          /* Add totals */
          $this->insertTotals($page, $invoice);
          if ($invoice->getStoreId()) {
              Mage::app()->getLocale()->revert();
          }
          
          $yAfterTotals = $this->y;
          $this->y = $yBeforeTotals;
          
          if(Mage::getStoreConfig(
            'bvt_enhancedpdfinvoice_config/custom_settings/display_tax_box', 
            $store
          )) {
            $this->y -= 15;
            $this->_insertTaxBox($page, $invoice);
          }
          
          $this->y = ($this->y < $yAfterTotals) ? $this->y : $yAfterTotals;
          
          $this->_insertPaymentInfo($page, $order);
          
          $this->_insertFooterText($page, $invoice->getStore());
          
          if(Mage::getStoreConfig(
            'bvt_enhancedpdfinvoice_config/custom_settings/performance_date', 
            $store
          )) {
            $this->_insertPerformanceDate($page, $invoice->getStore());
          }
          
          
          
          
      }
      $this->_afterGetPdf();
      return $pdf;
  }
      
  /**
  * Insert performance date
  *
  * @param Zend_Pdf_Page $page
  * @param int $y
  * @param null invoice
  */
  protected  function _insertTaxBox(&$page, $invoice)
  {
    $order = $invoice->getOrder();
    $taxCollection = Mage::getModel('tax/sales_order_tax')->getCollection();
    $taxCollection->addFieldToFilter('order_id',$order->getId());
    
    $y = $this->y;
    
    $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
    $page->setLineWidth(0.5);
    $page->drawRectangle($this->_iLeftMargin, $y-3, 320, $y+12);
    $page->setFillColor(new Zend_Pdf_Color_Rgb(1, 1, 1));
    
    $lines = array();
    
    $lines[0][] = array(
            'text'  => Mage::helper("enhancedpdfinvoice")->__("Tax description"),
            'feed'  => $this->_iLeftMargin+4,
            'align' => 'left',
        );
    $lines[0][] = array(
            'text'  => Mage::helper("enhancedpdfinvoice")->__("Tax percentage"),
            'feed'  => $this->_iLeftMargin+130,
            'align' => 'left',
        );
    $lines[0][] = array(
            'text'  => Mage::helper("enhancedpdfinvoice")->__("Tax amount"),
            'feed'  => $this->_iLeftMargin+200,
            'align' => 'left',
        );
    $i = 1;
    if($taxCollection->count()) {
      foreach($taxCollection as $taxClass) {
        
        $lines[$i][] = array(
            'text'  => $taxClass->getTitle(),
            'feed'  => $this->_iLeftMargin+4,
            'align' => 'left',
        );
        $lines[$i][] = array(
            'text'  => (float) $taxClass->getPercent() ."%",
            'feed'  => $this->_iLeftMargin+130,
            'align' => 'left',
        );
        $lines[$i][] = array(
            'text'  => $order->formatPriceTxt($taxClass->getBaseAmount()),
            'feed'  => $this->_iLeftMargin+200,
            'align' => 'left',
        );
        $i++;
        $y-=15;
        $page->drawRectangle($this->_iLeftMargin, $y-3, 320, $y+12);
      }
    }    
    
    $lineBlock = array(
      'lines'  => $lines,
      'height' => 15,
    );
    
    $page->setFillColor(new Zend_Pdf_Color_Rgb(0, 0, 0));
    
    $page = $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
    $this->setPage($page);
  }
  
  /**
  * Insert performance date
  *
  * @param Zend_Pdf_Page $page
  * @param null $store
  */
  protected  function _insertPerformanceDate(&$page, $store = null)
  {
    $this->y = $this->y ? $this->y : 100;

    $perfDate = Mage::helper("enhancedpdfinvoice")->__("Performance date");
    $perfDate .= ": " . Mage::helper("enhancedpdfinvoice")->__(date("F"));
    $page->drawText(
      $perfDate, 
      $this->_iLetterWindowLeft, $this->y, 'UTF-8');
    $this->y -= 12;
  }
  
  /**
  * Insert custom footer text
  *
  * @param Zend_Pdf_Page $page
  * @param null $store
  */
  protected  function _insertFooterText(&$page, $store = null)
  {
    $this->y = $this->y ? $this->y : 100;
    
    $footerText = Mage::getStoreConfig(
      'bvt_enhancedpdfinvoice_config/custom_settings/footer_text', 
      $store
    );
           
    $footerTextLines = explode("\n",$footerText);
    
    foreach($footerTextLines as $footerLine) {
      $page->drawText(
        strip_tags($footerLine), 
        $this->_iLetterWindowLeft, $this->y, 'UTF-8');
      $this->y -= 12;
    }
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
    $image = Mage::getStoreConfig(
      'bvt_enhancedpdfinvoice_config/design_settings/logo_image', 
      $store
    );
    if ($image) {
      $image = Mage::getBaseDir('media') . '/bvt/enhancedpdfinvoice/' . $image;
      if (is_file($image)) {
        $image       = Zend_Pdf_Image::imageWithPath($image);
        //top border of the page
        $top         = $this->_iFullPageHeight - $this->_iTopMargin; 
        $widthLimit  = Mage::getStoreConfig(
          'bvt_enhancedpdfinvoice_config/design_settings/logo_image_width', 
          $store
        );
        $heightLimit = Mage::getStoreConfig(
          'bvt_enhancedpdfinvoice_config/design_settings/logo_image_height', 
          $store
        );
        
        $widthLimit = empty($widthLimit) ? 200 : $widthLimit;
        $heightLimit = empty($heightLimit) ? 120 : $heightLimit;
        
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
      
      $sStoreAdress = trim(strip_tags(
        Mage::getStoreConfig('general/imprint/shop_name', $store)
      ));
      $sStoreAdress .=  $this->_sSeparatorSign;
      $sStoreAdress .= trim(strip_tags(
        Mage::getStoreConfig('general/imprint/company_first', $store)
      ));
      $sStoreAdress .=  $this->_sSeparatorSign;
      $sStoreAdress .= trim(strip_tags(
        Mage::getStoreConfig('general/imprint/street', $store)
      ));
      $sStoreAdress .=  $this->_sSeparatorSign;
      $sStoreAdress .= trim(strip_tags(
        Mage::getStoreConfig('general/imprint/zip', $store)
      ));
      $sStoreAdress .=  " ";
      $sStoreAdress .= trim(strip_tags(
        Mage::getStoreConfig('general/imprint/city', $store)
      ));
      $sStoreAdress .=  $this->_sSeparatorSign;
      $sStoreAdress .= Mage::app()->getLocale()->getCountryTranslation(
        Mage::getStoreConfig('general/imprint/country', $store)
      );

      $page->drawText(trim(strip_tags($sStoreAdress)),
                      $this->_logoRight + 5,
                      $this->_logoBottom + 10,
                      'UTF-8');
                      
      $x1 = $this->_iLeftMargin;
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
  protected function insertCustomerBillingAddress(&$oPage, $oOrder,$oInvoice = null)
  {
    $oPage->setFillColor(new Zend_Pdf_Color_GrayScale(0));
    $font = $this->_setFontRegular($oPage, 7);
    $oPage->setLineWidth(0);
    $store = $oInvoice->getStore();
    //$sStoreAdress = trim(strip_tags(Mage::getStoreConfig('general/imprint/shop_name', $store)));
    //$sStoreAdress .=  $this->_sSeparatorSign;
    $sStoreAdress = trim(strip_tags(Mage::getStoreConfig('general/imprint/company_first', $store)));
    $sStoreAdress .=  $this->_sSeparatorSign;
    $sStoreAdress .= trim(strip_tags(Mage::getStoreConfig('general/imprint/street', $store)));
    $sStoreAdress .=  $this->_sSeparatorSign;
    $sStoreAdress .= trim(strip_tags(Mage::getStoreConfig('general/imprint/zip', $store)));
    $sStoreAdress .=  " ";
    $sStoreAdress .= trim(strip_tags(Mage::getStoreConfig('general/imprint/city', $store)));
    $sStoreAdress .=  $this->_sSeparatorSign;
    $sStoreAdress .= Mage::app()->getLocale()->getCountryTranslation(Mage::getStoreConfig('general/imprint/country', $store));

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
      strip_tags(trim($oBillingAddress->getPostcode()." ".$oBillingAddress->getCity())), 
      $this->_iLetterWindowLeft, $this->y, 'UTF-8');
    $this->y -= 12;    
    
    $countryName = Mage::app()->getLocale()->getCountryTranslation($oBillingAddress->getCountry());
    
    $oPage->drawText(
      strip_tags(trim($countryName)), 
      $this->_iLetterWindowLeft, $this->y, 'UTF-8');
    $this->y -= 12;    
    
    
    
  }
  
  /**
    * Insert payment info to pdf page
    *
    * @param Zend_Pdf_Page $page
    * @param Mage_Sales_Model_Order $order
    */
  protected function _insertPaymentInfo(&$page, $order)
  {
    $top = $this->y;
    /* Payment */
    $paymentInfo = Mage::helper('payment')->getInfoBlock($order->getPayment())
        ->setIsSecureMode(true)
        ->toPdf();
    $paymentInfo = htmlspecialchars_decode(strip_tags($paymentInfo), ENT_QUOTES);
    $payment = explode('{{pdf_row_separator}}', $paymentInfo);
    foreach ($payment as $key=>$value){
        if (strip_tags(trim($value)) == '') {
            unset($payment[$key]);
        }
    }
    reset($payment);

    $this->y = $top - 40;

    if (!$order->getIsVirtual()) {

        $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineWidth(0.5);
        $page->drawRectangle($this->_iLeftMargin, $this->y, 275, $this->y-25);
        $page->drawRectangle(275, $this->y, 560, $this->y-25);

        $this->y -= 15;
        $this->_setFontBold($page, 12);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $page->drawText(Mage::helper('sales')->__('Payment Method'), $this->_iLeftMargin + 10, $this->y, 'UTF-8');
        $page->drawText(Mage::helper('sales')->__('Shipping Method:'), 285, $this->y , 'UTF-8');

        $this->y -=10;
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));

        $this->_setFontRegular($page, 10);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

        $paymentLeft = $this->_iLeftMargin + 10;
        $yPayments   = $this->y - 15;
    }
    else {
        $yPayments   = $this->y;
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
        $yPayments = min($this->y, $yPayments);
        $page->drawLine($this->_iLeftMargin,  ($top - 25), 25,  $yPayments);
        $page->drawLine(570, ($top - 25), 570, $yPayments);
        $page->drawLine($this->_iLeftMargin,  $yPayments,  570, $yPayments);

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
            . $order->formatPriceTxt($order->getShippingInclTax()) . ")"; //getShippingInclTax // getShippingAmount
            // shipping excl. tax: $order->getShippingAmount()

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
        $page->drawLine($this->_iLeftMargin,  $methodStartY, $this->_iLeftMargin,  $currentY); //left
        $page->drawLine($this->_iLeftMargin,  $currentY,     560, $currentY); //bottom
        $page->drawLine(560, $currentY,     560, $methodStartY); //right

        $this->y = $currentY;
        $this->y -= 15;
    }
  }
  
  /**
    * Insert order to pdf page
    *
    * @param Zend_Pdf_Page $page
    * @param Mage_Sales_Model_Order $obj
    * @param Mage_Sales_Model_Invoice $invoice
    * @param bool $putOrderId
    */
  protected function _insertOrderHead(&$page, $obj, $oInvoice, $putOrderId = true)
  {
      if ($obj instanceof Mage_Sales_Model_Order) {
          $shipment = null;
          $order = $obj;
      } elseif ($obj instanceof Mage_Sales_Model_Order_Shipment) {
          $shipment = $obj;
          $order = $shipment->getOrder();
      }

      $this->insertCustomerBillingAddress($page,$order,$oInvoice);
      
      $this->y = $this->y ? $this->y : $this->_iFullPageHeight - $this->_iTopMargin - $this->_iLetterWindowTop - $this->_iLetterWindowHeight -10;

      $this->y -= 20;
      
      $this->_setFontBold($page, 14);
      $page->drawText(
              Mage::helper('sales')->__('Invoice'), $this->_iLeftMargin, $this->y, 'UTF-8'
          );
          
      if($putOrderId)
      {
        $font = $this->_setFontBold($page, 10);
        $sOrderNumber = Mage::helper('sales')->__('Order #').$order->getIncrementId();
        $iWidth = $this->widthForStringUsingFontSize($sOrderNumber, $font, 10);
        $page->drawText(
                $sOrderNumber, ($this->_iFullPageWith - $this->_iRightMargin) - $iWidth, $this->y, 'UTF-8'
            );
      }
          
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

       $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
  }

}