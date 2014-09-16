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
 * Email Template Mailer Model
 *
 * @category   BlueVisionTec
 * @package    BlueVisionTec_EnhancedPdfInvoice
 * @author     BlueVisionTec UG (haftungsbeschränkt) <magedev@bluevisiontec.eu>
 */
class BlueVisionTec_EnhancedPdfInvoice_Model_Core_Email_Template_Mailer extends Mage_Core_Model_Email_Template_Mailer {
  
  /**
   * @var Mage_Core_Model_Template
   */
  protected $_emailTemplate;
  
  /**
    * Send all emails from email list
    * @see self::$_emailInfos
    *
    * @return Mage_Core_Model_Email_Template_Mailer
    */
  public function send()
  {
      $emailTemplate = $this->getEmailTemplate();
      // Send all emails from corresponding list
      while (!empty($this->_emailInfos)) {
          $emailInfo = array_pop($this->_emailInfos);
          // Handle "Bcc" recepients of the current email
          $emailTemplate->addBcc($emailInfo->getBccEmails());
          // Set required design parameters and delegate email sending to Mage_Core_Model_Email_Template
          $emailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $this->getStoreId()))
              ->sendTransactional(
              $this->getTemplateId(),
              $this->getSender(),
              $emailInfo->getToEmails(),
              $emailInfo->getToNames(),
              $this->getTemplateParams(),
              $this->getStoreId()
          );
      }
      return $this;
  }
  
  /**
   * get email template
   *
   * @return Mage_Core_Model_Template
   */
  public function getEmailTemplate() {
    if(! ($this->_emailTemplate instanceof Mage_Core_Model_Template)) {
      $this->_emailTemplate = Mage::getModel('core/email_template');
    }
    return $this->_emailTemplate;
  }
  
  /**
   * set email template
   *
   * @var Mage_Core_Model_Template
   */
  public function setEmailTemplate($emailTemplate) {
    $this->_emailTemplate = $emailTemplate;
  }
}