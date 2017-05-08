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

namespace Foxycom\AdvancedContact\Test\Integration\Other;

use Foxycom\AdvancedContact\Model\Config;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractController;

class ContactFieldsTest extends AbstractController
{
    /**
     * @var Config
     */
    protected $config;

    protected function setUp()
    {
        parent::setUp();

        $this->config = ObjectManager::getInstance()->create(Config::class);
    }

    /**
     * @magentoConfigFixture current_store contact/fields/show_name 1
     * @magentoConfigFixture current_store contact/fields/show_phone 1
     * @magentoConfigFixture current_store contact/fields/show_subject 1
     */
    public function testIfFieldsAreShownWhenConfigIsEnabled()
    {
        $body = $this->_getPageBody();

        $this->assertEquals(true, $this->config->showName());
        $this->assertSelectCount('#name', true, $body);

        $this->assertEquals(true, $this->config->showPhone());
        $this->assertSelectCount('#telephone', true, $body);

        if (!empty($this->config->getSubjects())) {
            $this->assertEquals(true, $this->config->showSubject());
        } else {
            $this->assertEquals(false, $this->config->showSubject());
        }

        if (!empty($this->config->getSubjects())) {
            $this->assertSelectCount('#subject', true, $body);
        } else {
            $this->assertSelectCount('#subject', false, $body);
        }
    }

    /**
     * @magentoConfigFixture current_store contact/fields/show_name 0
     * @magentoConfigFixture current_store contact/fields/show_phone 0
     * @magentoConfigFixture current_store contact/fields/show_subject 0
     */
    public function testIfFieldsAreNotShownWhenConfigIsDisabled()
    {
        $body = $this->_getPageBody();

        $this->assertEquals(false, $this->config->showName());
        $this->assertSelectCount("#name", false, $body);

        $this->assertEquals(false, $this->config->showPhone());
        $this->assertSelectCount("#telephone", false, $body);

        $this->assertEquals(false, $this->config->showSubject());
        $this->assertSelectCount('#subject', false, $body);
    }

    /**
     * @magentoConfigFixture current_store contact/fields/name_required 1
     * @magentoConfigFixture current_store contact/fields/phone_required 1
     * @magentoConfigFixture current_store contact/fields/subject_required 1
     */
    public function testIfFieldsValidationIfRequired()
    {
        $params = [
            'name'      => '',
            'telephone' => '',
            'subject'   => '',
            'comment'   => 'Test comment',
            'email'     => 'user@example.com',
            'hideit'    => '',
        ];
        $this->getRequest()->setPostValue($params);
        $this->getRequest()->setMethod('POST');

        $this->dispatch('advancedcontact/index/post');
        $this->assertSessionMessages(
            $this->contains("We can't process your request right now. Sorry, that's all we know."),
            MessageInterface::TYPE_ERROR
        );
    }

    /**
     * @magentoConfigFixture current_store contact/fields/name_required 0
     * @magentoConfigFixture current_store contact/fields/phone_required 0
     * @magentoConfigFixture current_store contact/fields/subject_required 0
     */
    public function testIfFieldsValidationIfNotRequired()
    {
        $params = [
            'name'      => '',
            'telephone' => '',
            'subject'   => '',
            'comment'   => 'Test comment',
            'email'     => 'user@example.com',
            'hideit'    => '',
        ];
        $this->getRequest()->setPostValue($params);
        $this->getRequest()->setMethod('POST');

        $this->dispatch('advancedcontact/index/post');
        $this->assertSessionMessages(
            $this->contains($this->config->getThankYouMessage()),
            MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * @return string
     */
    protected function _getPageBody()
    {
        $this->getRequest()->setMethod('GET');
        $this->dispatch('contact/index/index');
        $body = $this->getResponse()->getBody();

        return $body;
    }
}
