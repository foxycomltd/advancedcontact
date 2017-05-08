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

namespace Foxycom\AdvancedContact\Plugin;

use Foxycom\AdvancedContact\Model\Config;
use Foxycom\AdvancedContact\Model\Config\Source\Subject as SubjectSource;
use Magento\Contact\Block\ContactForm as MagentoContactForm;
use Magento\Framework\View\Element\Template\Context;

class ContactForm extends MagentoContactForm
{
    /**
     * Configuration
     *
     * @var \Foxycom\AdvancedContact\Model\Config
     */
    protected $_config;

    /**
     * Subject source
     *
     * @var SubjectSource
     */
    protected $_sourceSubject;

    /**
     * ContactForm constructor.
     *
     * @param Context $context
     * @param Config $_config
     * @param SubjectSource $_sourceSubject
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $_config,
        SubjectSource $_sourceSubject,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->_config        = $_config;
        $this->_sourceSubject = $_sourceSubject;
    }

    /**
     * Get config
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Returns action url for contact form
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('advancedcontact/index/post', ['_secure' => true]);
    }

    /**
     * Get Subject Select element
     *
     * @return string
     */
    public function getSubjectSelectHtml()
    {
        $selectBlock = $this->getLayout()->createBlock('Magento\Framework\View\Element\Html\Select')
                            ->setName('subject')
                            ->setId('subject')
                            ->setTitle(__('Subject'));

        if ($this->_config->isSubjectRequired()) {
            $selectBlock->setExtraParams('data-validate="{\'validate-select\':true, required:true}"');
        }

        $selectBlock->setOptions($this->_sourceSubject->getOptions());

        return $selectBlock->getHtml();
    }
}
