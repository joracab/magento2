<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magento
 * @package     Magento_Widget
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Test\Integrity\Magento\Widget;

class TemplateFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Check if all the declared widget templates actually exist
     *
     * @param string $class
     * @param string $template
     * @dataProvider widgetTemplatesDataProvider
     */
    public function testWidgetTemplates($class, $template)
    {
        /** @var $blockFactory \Magento\Core\Model\BlockFactory */
        $blockFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\BlockFactory');
        /** @var \Magento\Core\Block\Template $block */
        $block = $blockFactory->createBlock($class);
        $this->assertInstanceOf('Magento\Core\Block\Template', $block);
        $block->setTemplate((string)$template);
        $this->assertFileExists($block->getTemplateFile());
    }

    /**
     * Collect all declared widget blocks and templates
     *
     * @return array
     */
    public function widgetTemplatesDataProvider()
    {
        $result = array();
        /** @var $model \Magento\Widget\Model\Widget */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Widget\Model\Widget');
        foreach ($model->getWidgetsArray() as $row) {
            /** @var $instance \Magento\Widget\Model\Widget\Instance */
            $instance = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Widget\Model\Widget\Instance');
            $config = $instance->setType($row['type'])->getWidgetConfigAsArray();
            $class = $row['type'];
            if (is_subclass_of($class, 'Magento\Core\Block\Template')) {
                if (isset($config['parameters']) && isset($config['parameters']['template'])
                    && isset($config['parameters']['template']['values'])) {
                    $templates = $config['parameters']['template']['values'];
                    foreach ($templates as $template) {
                        if (isset($template['value'])) {
                            $result[] = array($class, (string)$template['value']);
                        }
                    }
                }
            }
        }
        return $result;
    }
}
