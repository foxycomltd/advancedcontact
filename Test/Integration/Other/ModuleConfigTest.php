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

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Module\ModuleList;
use Magento\TestFramework\ObjectManager;

class ModuleConfigTest extends \PHPUnit_Framework_TestCase
{
    private $_moduleName = 'Foxycom_AdvancedContact';

    /**
     * @magentoConfigFixture current_store contact/email/recipient_email peraperic
     */
    public function testTheModuleIsRegistered()
    {
        $registrar = new ComponentRegistrar();
        $this->assertArrayHasKey($this->_moduleName, $registrar->getPaths(ComponentRegistrar::MODULE));
    }

    public function testTheModuleIsConfiguredAndEnabledIntestEnv()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = ObjectManager::getInstance();

        /** @var ModuleList $moduleList */
        $moduleList = $objectManager->create(ModuleList::class);

        $this->assertTrue($moduleList->has($this->_moduleName), 'The module is enabled in test env.');
    }

    public function testTheModuleIsConfiguredAndEnabledInRealEnv()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = ObjectManager::getInstance();

        $directoryList    = $objectManager->create(DirectoryList::class, ['root' => BP]);
        $reader           = $objectManager->create(DeploymentConfig\Reader::class, ['dirList' => $directoryList]);
        $deploymentConfig = $objectManager->create(DeploymentConfig::class, ['reader' => $reader]);

        /** @var ModuleList $moduleList */
        $moduleList = $objectManager->create(ModuleList::class, ['config' => $deploymentConfig]);

        $this->assertTrue($moduleList->has($this->_moduleName), 'The module is enabled in real env.');
    }
}
