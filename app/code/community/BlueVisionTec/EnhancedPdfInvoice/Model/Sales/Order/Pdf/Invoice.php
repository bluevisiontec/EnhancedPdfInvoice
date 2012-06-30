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
        $top         = 830; //top border of the page
        $widthLimit  = 200; //half of the page width
        $heightLimit = 100; //assuming the image is not a "skyscraper"
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

        $y1 = $top - $height;
        $y2 = $top;
        $x1 = 25;
        $x2 = $x1 + $width;

        //coordinates after transformation are rounded by Zend
        $page->drawImage($image, $x1, $y1, $x2, $y2);

        $this->y = $y1 - 10;
      }
    }
  }

}