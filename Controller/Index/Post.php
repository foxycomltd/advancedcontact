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

namespace Foxycom\AdvancedContact\Controller\Index;

use Foxycom\AdvancedContact\Model\Config;
use Magento\Contact\Controller\Index as ContactIndexController;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;

class Post extends ContactIndexController
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var Config
     */
    protected $config;

    public function execute()
    {
        if ($this->getRequest()->getMethod() !== 'POST') {
            return $this->_forward('noroute');
        }

        $post = $this->getRequest()->getPostValue();
        if (!$post) {
            $this->_redirect('contact/index/index');

            return;
        }

        $this->config = ObjectManager::getInstance()->create(Config::class);
        $this->inlineTranslation->suspend();
        try {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($post);

            $error = false;

            if ($this->config->isNameRequired() && $this->config->showName()) {
                if (!\Zend_Validate::is(trim($post['name']), 'NotEmpty')) {
                    $error = true;
                }
            }

            if ($this->config->isPhoneRequired() && $this->config->showPhone()) {
                if (!\Zend_Validate::is(trim($post['telephone']), 'NotEmpty')) {
                    $error = true;
                }
            }

            if ($this->config->isSubjectRequired() && $this->config->showSubject()) {
                if (!\Zend_Validate::is(trim($post['subject']), 'NotEmpty')) {
                    $error = true;
                }
            }

            if (!\Zend_Validate::is(trim($post['comment']), 'NotEmpty')) {
                $error = true;
            }

            if (!\Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                $error = true;
            }

            if (\Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                $error = true;
            }

            if ($error) {
                throw new \Exception();
            }

            $this->_transportBuilder->setTemplateIdentifier($this->config->getEmailTemplate())
                                    ->setTemplateOptions(
                                        [
                                            'area'  => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                                            'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                                        ]
                                    )
                                    ->setTemplateVars(['data' => $postObject])
                                    ->setFrom($this->config->getEmailSender())
                                    ->setReplyTo($post['email']);

            $recipientSet = false;
            if ($this->config->isSubjectRequired() && $this->config->showSubject()) {
                $subjects = $this->config->getSubjects();
                if (!empty($subjects)) {
                    foreach ($subjects as $subject) {
                        if ($subject['name'] == $postObject->getSubject() && !empty($subject['recipient_email'])) {
                            $this->_transportBuilder->addTo($subject['recipient_email']);
                            $recipientSet = true;
                        }
                    }
                }
            }

            if (!$recipientSet) {
                $this->_transportBuilder->addTo($this->config->getEmailRecipient());
            }

            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
            $this->messageManager->addSuccess($this->config->getThankYouMessage());
            $this->getDataPersistor()->clear('contact_us');
            $this->_redirect('contact/index');

            return;
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addError(__('We can\'t process your request right now. Sorry, that\'s all we know.'));
            $this->getDataPersistor()->set('contact_us', $post);
            $this->_redirect('contact/index');

            return;
        }
    }

    /**
     * Get Data Persistor
     *
     * @return DataPersistorInterface
     */
    private function getDataPersistor()
    {
        if ($this->dataPersistor === null) {
            $this->dataPersistor = ObjectManager::getInstance()->get(DataPersistorInterface::class);
        }

        return $this->dataPersistor;
    }
}
