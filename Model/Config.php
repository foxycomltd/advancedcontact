<?php
/**
 * Foxycom - eCommerce solutions
 *
 * Advanced Contact Form
 *
 * @author    Foxycom <support@foxycom.com>
 * @package   Foxycom_AdvancedContact
 * @copyright Copyright (c) 2017 Foxycom (https://foxycom.com)
 * @license   https://foxycom.com/license/osl-30
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Foxycom\AdvancedContact\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Unserialize\Unserialize;
use Magento\Store\Model\ScopeInterface;

class Config extends \Magento\Eav\Model\Config
{
    const XML_PATH_CONTACT_CONTACT_AJAX_ENABLED = 'contact/contact/ajax_enabled';
    const XML_PATH_CONTACT_CONTACT_THANK_YOU_URL = 'contact/contact/thank_you_url';
    const XML_PATH_CONTACT_CONTACT_THANK_YOU_MESSAGE = 'contact/contact/thank_you_message';

    const XML_PATH_CONTACT_FIELDS_SHOW_SUBJECT = 'contact/fields/show_subject';
    const XML_PATH_CONTACT_FIELDS_SUBJECT_REQUIRED = 'contact/fields/subject_required';
    const XML_PATH_CONTACT_FIELDS_SUBJECTS = 'contact/fields/subjects';
    const XML_PATH_CONTACT_FIELDS_SHOW_NAME = 'contact/fields/show_name';
    const XML_PATH_CONTACT_FIELDS_NAME_REQUIRED = 'contact/fields/name_required';
    const XML_PATH_CONTACT_FIELDS_SHOW_PHONE = 'contact/fields/show_phone';
    const XML_PATH_CONTACT_FIELDS_PHONE_REQUIRED = 'contact/fields/phone_required';

    const XML_PATH_EMAIL_TEMPLATE = 'contact/email/email_template';
    const XML_PATH_EMAIL_RECIPIENT = 'contact/email/recipient_email';
    const XML_PATH_EMAIL_SENDER = 'contact/email/sender_email_identity';

    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Serializer object
     *
     * @var Unserialize
     */
    protected $_serializer;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param Unserialize $serializer
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Unserialize $serializer
    ) {

        $this->_scopeConfig = $scopeConfig;
        $this->_serializer  = $serializer;
    }

    /**
     * Is Ajax Processing Enabled
     *
     * @return bool
     */
    public function isAjaxEnabled()
    {
        return (bool)$this->_scopeConfig->getValue(self::XML_PATH_CONTACT_CONTACT_AJAX_ENABLED,
            ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get thank you message
     *
     * @return string
     */
    public function getThankYouMessage()
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_CONTACT_CONTACT_THANK_YOU_MESSAGE,
            ScopeInterface::SCOPE_STORE);
    }

    /**
     * Show Subject Field
     *
     * @return bool
     */
    public function showSubject()
    {
        $subjects = $this->getSubjects();

        return (bool)$this->_scopeConfig->getValue(self::XML_PATH_CONTACT_FIELDS_SHOW_SUBJECT,
                ScopeInterface::SCOPE_STORE) && !empty($subjects);
    }

    /**
     * Is Subject Required
     *
     * @return bool
     */
    public function isSubjectRequired()
    {
        return (bool)$this->_scopeConfig->getValue(self::XML_PATH_CONTACT_FIELDS_SUBJECT_REQUIRED,
            ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get list of subjects
     *
     * @return bool|mixed
     */
    public function getSubjects()
    {
        return $this->_serializer->unserialize($this->_scopeConfig->getValue(self::XML_PATH_CONTACT_FIELDS_SUBJECTS,
            ScopeInterface::SCOPE_STORE));
    }

    /**
     * Show Name Field
     *
     * @return bool
     */
    public function showName()
    {
        return (bool)$this->_scopeConfig->getValue(self::XML_PATH_CONTACT_FIELDS_SHOW_NAME,
            ScopeInterface::SCOPE_STORE);
    }

    /**
     * Is Name Required
     *
     * @return bool
     */
    public function isNameRequired()
    {
        return (bool)$this->_scopeConfig->getValue(self::XML_PATH_CONTACT_FIELDS_NAME_REQUIRED,
            ScopeInterface::SCOPE_STORE);
    }

    /**
     * Show Phone Field
     *
     * @return bool
     */
    public function showPhone()
    {
        return (bool)$this->_scopeConfig->getValue(self::XML_PATH_CONTACT_FIELDS_SHOW_PHONE,
            ScopeInterface::SCOPE_STORE);
    }

    /**
     * Is Phone Required
     *
     * @return bool
     */
    public function isPhoneRequired()
    {
        return (bool)$this->_scopeConfig->getValue(self::XML_PATH_CONTACT_FIELDS_PHONE_REQUIRED,
            ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get email template
     *
     * @return string|int
     */
    public function getEmailTemplate()
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get email template
     *
     * @return string
     */
    public function getEmailSender()
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get email template
     *
     * @return string
     */
    public function getEmailRecipient()
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, ScopeInterface::SCOPE_STORE);
    }
}
