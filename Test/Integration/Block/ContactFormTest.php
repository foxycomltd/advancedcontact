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

namespace Foxycom\AdvancedContact\Test\Integration\Block;

use Foxycom\AdvancedContact\Plugin\ContactForm;
use Magento\TestFramework\ObjectManager;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class ContactFormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppArea frontend
     */
    public function testIfFormBlockAndTemplateAreRewritten()
    {
        /** @var PageFactory $pageFactory */
        $pageFactory = ObjectManager::getInstance()->create(PageFactory::class);

        /** @var Page $resultPage */
        $resultPage = $pageFactory->create();

        $resultPage->getLayout()->getUpdate()->addHandle('contact_index_index');
        $block = $resultPage->getLayout()->getBlock('contactForm');

        if ($block === false) {
            $this->fail('Block does not exist');
        } else {
            $this->assertInstanceOf(ContactForm::class, $block);
        }

        $this->assertEquals($block->getTemplate(), 'Foxycom_AdvancedContact::form.phtml');
    }
}
