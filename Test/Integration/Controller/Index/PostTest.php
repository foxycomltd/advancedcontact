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

namespace Foxycom\AdvancedContact\Test\Integration\Controller\Index;

use Foxycom\AdvancedContact\Controller\Index\Post;
use Foxycom\AdvancedContact\Model\Config;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\Transport;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Request;
use Magento\TestFramework\TestCase\AbstractController;

class PostTest extends AbstractController
{
    protected $postAction = 'advancedcontact/index/post';
    /**
     * @var Config
     */
    protected $config;

    protected function setUp()
    {
        parent::setUp();

        $this->config = ObjectManager::getInstance()->create(Config::class);
    }

    public function testCanHandleGetRequest()
    {
        $this->getRequest()->setMethod('GET');
        $this->dispatch($this->postAction);

        $this->assertSame(404, $this->getResponse()->getHttpResponseCode());
        $this->assert404NotFound();
    }

    public function testIfRedirectToContactIfPostIsEmpty()
    {
        $data = [];
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setParams($data);
        $this->dispatch('advancedcontact/index/post');

        $this->assertRedirect();
    }

    public function testIfFormSubmissionIsWorking()
    {
        $data = [];
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setParams($data);
        $this->dispatch($this->postAction);

        $this->assertRedirect();
    }

    /**
     * @magentoConfigFixture current_store contact/fields/show_subject 0
     * @magentoConfigFixture current_store contact/contact/thank_you_message Test
     */
    public function testThankYouMessage()
    {
        $params = [
            'name'    => 'customer name',
            'comment' => 'comment',
            'email'   => 'user@example.com',
            'hideit'  => '',
        ];
        $this->getRequest()->setPostValue($params);
        $this->getRequest()->setMethod('POST');

        $this->dispatch('' . $this->postAction);
        $this->assertSessionMessages(
            $this->contains($this->config->getThankYouMessage()),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store contact/email/recipient_email test@example.com
     * @magentoConfigFixture current_store contact/fields/show_subject 1
     * @magentoConfigFixture current_store contact/fields/subject_required 1
     * @magentoConfigFixture current_store contact/fields/subjects a:2:{s:18:"_1493729795757_757";a:2:{s:4:"name";s:6:"Test 1";s:15:"recipient_email";s:17:"test1@example.com";}s:18:"_1493729798586_586";a:2:{s:4:"name";s:6:"Test 2";s:15:"recipient_email";s:0:"";}}
     */
    public function testEmailRecipients()
    {
        $params = [
            'subject'   => 'Test 1',
            'name'      => 'customer name',
            'telephone' => '123',
            'comment'   => 'comment',
            'email'     => 'user@example.com',
            'hideit'    => '',
        ];

        /** @var Request $request */
        $request = ObjectManager::getInstance()->create(Request::class);
        $request->setMethod('POST');
        $request->setPostValue($params);

        /** @var ManagerInterface $messageManager */
        $messageManager = ObjectManager::getInstance()->create(ManagerInterface::class);

        /** @var TransportBuilder|\PHPUnit_Framework_MockObject_MockObject $transportBuilderMock */
        $transportBuilderMock = $this->getMock(TransportBuilder::class, [], [], '', false);

        /** @var Transport\\PHPUnit_Framework_MockObject_MockObject $transportMock */
        $transportMock = $this->getMock(Transport::class, [], [], '', false);
        $transportBuilderMock->method('setTemplateOptions')->willReturn($transportBuilderMock);
        $transportBuilderMock->method('setTemplateIdentifier')->willReturn($transportBuilderMock);
        $transportBuilderMock->method('setTemplateVars')->willReturn($transportBuilderMock);
        $transportBuilderMock->method('setFrom')->willReturn($transportBuilderMock);
        $transportBuilderMock->method('setReplyTo')->willReturn($transportBuilderMock);
        $transportBuilderMock->method('getTransport')->willReturn($transportMock);

        $stateInterface = ObjectManager::getInstance()->create(StateInterface::class);
        $scopeConfig    = ObjectManager::getInstance()->create(ScopeConfigInterface::class);
        $storeManager   = ObjectManager::getInstance()->create(StoreManagerInterface::class);

        /** @var Context|\PHPUnit_Framework_MockObject_MockObject $contextMock */
        $contextMock = $this->getMock(Context::class, [], [], '', false);
        $contextMock->method('getRequest')->willReturn($request);
        $contextMock->method('getMessageManager')->willReturn($messageManager);

        /** @var Post $postControllerMock |\PHPUnit_Framework_MockObject_MockObject */
        $postControllerMock = $this->getMock(
            Post::class, ['_redirect'],
            [$contextMock, $transportBuilderMock, $stateInterface, $scopeConfig, $storeManager],
            '',
            true
        );
        $postControllerMock->method('_redirect')->willReturn(true);

        // Expected behaviour
        $transportBuilderMock->expects($this->once())->method('addTo')->with($this->equalTo('test1@example.com'));

        $postControllerMock->execute();
    }
}
