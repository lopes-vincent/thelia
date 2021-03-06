<?php

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thelia\Tests\Module\Validator;

use PHPUnit\Framework\TestCase;
use Thelia\Core\Thelia;
use Thelia\Core\Translation\Translator;
use Thelia\Module\Validator\ModuleValidator;
use Thelia\Tools\Version\Version;

/**
 * Class ModuleValidator.
 *
 * @author Julien Chanséaume <jchanseaume@openstudio.fr>
 */
class ModuleValidatorTest extends TestCase
{
    public function testCheque(): void
    {
        $moduleChequePath = THELIA_MODULE_DIR.'Cheque';

        $moduleValidator = new ModuleValidator($moduleChequePath, $this->getStubTranslator());

        $moduleDescriptor = $moduleValidator->getModuleDescriptor();

        $this->assertInstanceOf('SimpleXMLElement', $moduleDescriptor);
        $this->assertEquals('2', $moduleValidator->getModuleVersion());

        $moduleDefinition = $moduleValidator->getModuleDefinition();

        $this->assertInstanceOf('Thelia\Module\Validator\ModuleDefinition', $moduleDefinition);

        $this->assertEquals('Cheque', $moduleDefinition->getCode());
        $this->assertEquals('Cheque\\Cheque', $moduleDefinition->getNamespace());

        // validate
        $this->expectException(\Thelia\Exception\ModuleException::class);
        $moduleValidator->validate();
    }

    public function testVirtualProductDelivery(): void
    {
        $modulePath = THELIA_MODULE_DIR.'VirtualProductDelivery';

        $moduleValidator = new ModuleValidator($modulePath, $this->getStubTranslator());

        $moduleDescriptor = $moduleValidator->getModuleDescriptor();

        $this->assertInstanceOf('SimpleXMLElement', $moduleDescriptor);
        $this->assertEquals('2', $moduleValidator->getModuleVersion());

        $moduleDefinition = $moduleValidator->getModuleDefinition();

        $this->assertInstanceOf('Thelia\Module\Validator\ModuleDefinition', $moduleDefinition);

        $this->assertEquals('VirtualProductDelivery', $moduleDefinition->getCode());
        $this->assertEquals('VirtualProductDelivery\\VirtualProductDelivery', $moduleDefinition->getNamespace());
        $this->assertCount(2, $moduleDefinition->getLanguages());
        $this->assertCount(0, $moduleDefinition->getDependencies());
        $this->assertCount(1, $moduleDefinition->getAuthors());
        $this->assertEquals('', $moduleDefinition->getDocumentation());
        $this->assertEquals('', $moduleDefinition->getLogo());
        $this->assertEquals('2.4.4', $moduleDefinition->getTheliaVersion());
        $this->assertTrue(
            Version::test(
                Thelia::THELIA_VERSION,
                $moduleDefinition->getTheliaVersion(),
                false,
                '>='
            )
        );

        // validate
        $moduleValidator->validate(false);
    }

    public function authorsProvider()
    {
        return [
            ['Module1', 2],
            ['Module2', 1],
            ['Module3', 1],
        ];
    }

    /**
     * @dataProvider authorsProvider
     *
     * @param $path
     * @param $expectedAuthors
     */
    public function testAuthorsTag($path, $expectedAuthors): void
    {
        $modulePath = __DIR__.'/Authors/'.$path;

        $moduleValidator = new ModuleValidator($modulePath, $this->getStubTranslator());
        $moduleDefinition = $moduleValidator->getModuleDefinition();

        $this->assertEquals($expectedAuthors, \count($moduleDefinition->getAuthors()), sprintf('%d author(s) was expected for module %s', $expectedAuthors, $path));
    }

    public function validatorProvider()
    {
        return [
            ['Module1', '\Thelia\Exception\ModuleException', 'The module Module1 requires Thelia'],
            ['Module2', '\Thelia\Exception\ModuleException', 'To activate module Module2, the following modules should be activated first'],
            ['Module3', '\Thelia\Exception\FileNotFoundException', 'Module Module3 should have a module.xml in the Config directory'],
            ['Module4', '\Thelia\Exception\FileNotFoundException', 'Module Module4 should have a config.xml in the Config directory'],
            ['Module5', '\Thelia\Module\Exception\InvalidXmlDocumentException', null],
        ];
    }

    /**
     * @dataProvider validatorProvider
     *
     * @param $path
     * @param $exceptionExpected
     * @param $exceptionMessage
     */
    public function testValidator($path, $exceptionExpected, $exceptionMessage): void
    {
        $modulePath = __DIR__.'ModuleValidatorTest.php/'.$path;
        /** @var \Exception $exception */
        $exception = null;

        try {
            $moduleValidator = new ModuleValidator($modulePath, $this->getStubTranslator('opiopi'));

            $moduleValidator->validate(true);
        } catch (\Exception $ex) {
            $exception = $ex;
        }

        if (null !== $exceptionExpected) {
            $this->assertInstanceOf(
                $exceptionExpected,
                $exception,
                $path.' module should return exception '.$exceptionExpected
            );

            if (null !== $exceptionMessage) {
                $this->assertNotEmpty(
                    $exception->getMessage(),
                    $path.' module exception should not be empty'
                );

                $this->assertTrue(
                    false !== strpos($exception->getMessage(), $exceptionMessage),
                    $path.' module exception should contain : '.$exceptionMessage
                );
            }
        } else {
            $this->assertNull(
                $exception,
                $path.' module should not return exception ['.$exception->getMessage().']'
            );
        }
    }

    public function testNonExistentModule(): void
    {
        $moduleChuckNorrisPath = THELIA_MODULE_DIR.'ChuckNorris';

        $this->expectException(\Thelia\Exception\FileNotFoundException::class);
        new ModuleValidator($moduleChuckNorrisPath, $this->getStubTranslator());
    }

    /**
     * @return Translator
     */
    private function getStubTranslator()
    {
        $stubTranslator = $this->getMockBuilder('\Thelia\Core\Translation\Translator')
            ->disableOriginalConstructor()
            ->getMock();

        $stubTranslator->expects($this->any())
            ->method('trans')
            ->willReturnCallback(
                    function ($l, $p) {
                        foreach ($p as $pk => $pv) {
                            $l = str_replace($pk, $pv, $l);
                        }

                        return $l;
                    }
            )
        ;

        return $stubTranslator;
    }
}
