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

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Route\ConfigInterface as RouteConfig;
use Magento\Framework\App\Router\Base as BaseRouter;
use Magento\TestFramework\Request;

class RouteConfigTest extends \PHPUnit_Framework_TestCase
{
    protected $frontName = 'advancedcontact';

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function setUp()
    {
        $this->objectManager = ObjectManager::getInstance();
    }

    /**
     * @magentoAppArea frontend
     */
    public function testTheModuleRegisteredFrontName()
    {
        /** @var RouteConfig $routeConfig */
        $routeConfig = $this->objectManager->create(RouteConfig::class);
        $this->assertContains('Foxycom_AdvancedContact', $routeConfig->getModulesByFrontName($this->frontName));
    }

    /**
     * @magentoAppArea frontend
     */
    public function testTheModulePostIndexActionCanBeFound()
    {
        /** @var Request $request */
        $request = $this->objectManager->create(Request::class);
        $request->setModuleName($this->frontName);
        $request->setControllerName('index');
        $request->setActionName('post');

        /** @var BaseRouter $baseRouter */
        $baseRouter = $this->objectManager->create(BaseRouter::class);

        $expectedAction = \Foxycom\AdvancedContact\Controller\Index\Post::class;
        $this->assertInstanceOf($expectedAction, $baseRouter->match($request));
    }
}
