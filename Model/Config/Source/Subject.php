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

namespace Foxycom\AdvancedContact\Model\Config\Source;

use Foxycom\AdvancedContact\Model\Config;
use Magento\Framework\Option\ArrayInterface;

class Subject implements ArrayInterface
{
    protected $_config;

    public function __construct(
        Config $_config
    ) {
        $this->_config = $_config;
    }

    public function getOptions()
    {
        $output = [];
        $values = $this->_config->getSubjects();

        $output[''] = __('Select');
        if (!empty($values)) {
            foreach ($values as $key => $item) {
                $output[$item['name']] = $item['name'];
            }
        }

        return $output;
    }

    public function toOptionArray()
    {
        $output = [];
        $values = $this->_config->getSubjects();

        $output[] = ['value' => '', 'label' => __('Select')];

        if (!empty($values)) {
            foreach ($values as $key => $item) {
                $output[] = [
                    'value' => $key,
                    'label' => $item['name']
                ];
            }
        }

        return $output;
    }
}
