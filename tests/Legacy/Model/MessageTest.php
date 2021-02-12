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

namespace Thelia\Tests\Model;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormFactoryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Validator\ValidatorBuilder;
use Thelia\Core\EventDispatcher\EventDispatcher;
use Thelia\Core\Form\TheliaFormFactory;
use Thelia\Core\Form\TheliaFormValidator;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\HttpFoundation\Session\Session;
use Thelia\Core\Template\ParserContext;
use Thelia\Core\Template\TheliaTemplateHelper;
use Thelia\Core\Translation\Translator;
use Thelia\Model\ConfigQuery;
use Thelia\Model\Message as ModelMessage;
use TheliaSmarty\Template\SmartyParser;

/**
 * Class CustomerTest.
 *
 * @author Etienne Roudeix <eroudeix@openstudio.fr>
 */
class MessageTest extends TestCase
{
    /** @var ContainerBuilder */
    protected $container;

    /** @var SmartyParser */
    protected $parser;

    /** @var TheliaTemplateHelper */
    protected $templateHelper;

    private $backup_mail_template = 'undefined';

    protected function setUp(): void
    {
        $this->backup_mail_template = ConfigQuery::read('active-mail-template', 'default');

        ConfigQuery::write('active-mail-template', 'test');

        $this->templateHelper = new TheliaTemplateHelper();

        @mkdir($this->templateHelper->getActiveMailTemplate()->getAbsolutePath(), 0777, true);

        $container = new ContainerBuilder();

        $session = new Session(new MockArraySessionStorage());
        $request = new Request();
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $dispatcher = new EventDispatcher();
        $container->set('event_dispatcher', $dispatcher);

        $request->setSession($session);

        $translator = new Translator($requestStack);

        $formFactoryBuilder = new FormFactoryBuilder();
        $validatorBuilder = new ValidatorBuilder();

        $parserContext = new ParserContext(
            $requestStack,
            new TheliaFormFactory(
                $requestStack,
                $container->get('event_dispatcher'),
                $translator,
                $formFactoryBuilder,
                $validatorBuilder,
                []
            ),
            new TheliaFormValidator($translator, 'dev')
        );

        $container->set('request', $request);

        $this->parser = new SmartyParser($requestStack, $dispatcher, $parserContext, $this->templateHelper, 'dev', true);
        $this->parser->setTemplateDefinition($this->templateHelper->getActiveMailTemplate());

        $container->set('thelia.parser', $this->parser);

        $this->container = $container;
    }

    /**
     * Create message with HTML and TEXT body from message HTMl and TEXT fields.
     */
    public function testMessageWithTextAndHtmlBody(): void
    {
        $message = new ModelMessage();

        $message->setLocale('fr_FR');

        $message->setSubject('The subject');
        $message->setHtmlMessage('The HTML content');
        $message->setTextMessage('The TEXT content');

        $instance = \Swift_Message::newInstance();

        $message->buildMessage($this->parser, $instance);

        $this->assertEquals('The subject', $instance->getSubject());
        $this->assertEquals('The HTML content', $instance->getBody());
        $this->assertEquals('The TEXT content', $instance->getChildren()[0]->getBody());
    }

    /**
     * Create message with TEXT body only from message HTMl and TEXT fields.
     */
    public function testMessageWithTextOnlyBody(): void
    {
        $message = new ModelMessage();

        $message->setLocale('fr_FR');

        $message->setSubject('The subject');
        $message->setTextMessage('The TEXT content');

        $instance = \Swift_Message::newInstance();

        $message->buildMessage($this->parser, $instance);

        $this->assertEquals('The subject', $instance->getSubject());
        $this->assertEquals('The TEXT content', $instance->getBody());
        $this->assertCount(0, $instance->getChildren());
    }

    /**
     * Create message with HTML and TEXT body from message HTMl and TEXT fields
     * using a text and a html layout.
     */
    public function testMessageWithTextAndHtmlBodyAndTextAndHtmlLayout(): void
    {
        $message = new ModelMessage();

        $message->setLocale('fr_FR');

        $message->setSubject('The subject');
        $message->setTextMessage('The TEXT content');
        $message->setHtmlMessage('The HTML content');

        $message->setHtmlLayoutFileName('layout.html.tpl');
        $message->setTextLayoutFileName('layout.text.tpl');

        $path = $this->templateHelper->getActiveMailTemplate()->getAbsolutePath();

        file_put_contents($path.DS.'layout.html.tpl', 'HTML Layout: {$message_body nofilter}');
        file_put_contents($path.DS.'layout.text.tpl', 'TEXT Layout: {$message_body nofilter}');

        $instance = \Swift_Message::newInstance();

        $message->buildMessage($this->parser, $instance);

        $this->assertEquals('The subject', $instance->getSubject());
        $this->assertEquals('HTML Layout: The HTML content', $instance->getBody());
        $this->assertEquals('TEXT Layout: The TEXT content', $instance->getChildren()[0]->getBody());
    }

    /**
     * Create message with TEXT only body from message HTMl and TEXT fields
     * using a text only layout.
     */
    public function testMessageWithTextOnlyBodyAndTextOnlyLayout(): void
    {
        $message = new ModelMessage();

        $message->setLocale('fr_FR');

        $message->setSubject('The subject');
        $message->setTextMessage('The <TEXT> & content');

        $message->setTextLayoutFileName('layout3.text.tpl');

        $path = $this->templateHelper->getActiveMailTemplate()->getAbsolutePath();

        file_put_contents($path.DS.'layout3.text.tpl', 'TEXT Layout 3: {$message_body nofilter} :-) <>');

        $instance = \Swift_Message::newInstance();

        $message->buildMessage($this->parser, $instance);

        $this->assertEquals('The subject', $instance->getSubject());
        $this->assertEquals('TEXT Layout 3: The <TEXT> & content :-) <>', $instance->getBody());
        $this->assertCount(0, $instance->getChildren());
    }

    /**
     * Create message with TEXT and HTML body from message HTMl and TEXT fields
     * using a text only layout.
     */
    public function testMessageWithTextAndHtmlBodyAndTextOnlyLayout(): void
    {
        $message = new ModelMessage();

        $message->setLocale('fr_FR');

        $message->setSubject('The subject');
        $message->setTextMessage('The <TEXT> & content');
        $message->setHtmlMessage('The <HTML> & content');

        $message->setTextLayoutFileName('layout3.text.tpl');

        $path = $this->templateHelper->getActiveMailTemplate()->getAbsolutePath();

        file_put_contents($path.DS.'layout3.text.tpl', 'TEXT Layout 3: {$message_body nofilter} :-) <>');

        $instance = \Swift_Message::newInstance();

        $message->buildMessage($this->parser, $instance);

        $this->assertEquals('The subject', $instance->getSubject());
        $this->assertEquals('The <HTML> & content', $instance->getBody());
        $this->assertEquals('TEXT Layout 3: The <TEXT> & content :-) <>', $instance->getChildren()[0]->getBody());
    }

    /**
     * Create message with HTML and TEXT body from template HTMl and TEXT fields
     * using a text and a html layout.
     */
    public function testMessageWithTextAndHtmlBodyAndTextAndHtmlLayoutAndTextAndHtmlTemplate(): void
    {
        $message = new ModelMessage();

        $message->setLocale('fr_FR');

        $message->setSubject('The subject');
        $message->setTextMessage('The TEXT content');
        $message->setHtmlMessage('The HTML content');

        $message->setTextTemplateFileName('template4-text.txt');
        $message->setHtmlTemplateFileName('template4-html.html');

        $message->setHtmlLayoutFileName('layout4.html.tpl');
        $message->setTextLayoutFileName('layout4.text.tpl');

        $path = $this->templateHelper->getActiveMailTemplate()->getAbsolutePath();

        $this->parser->assign('myvar', 'my-value');

        file_put_contents($path.DS.'layout4.html.tpl', 'HTML Layout 4: {$message_body nofilter}');
        file_put_contents($path.DS.'layout4.text.tpl', 'TEXT Layout 4: {$message_body nofilter}');

        file_put_contents($path.DS.'template4-html.html', 'HTML <template> & content v={$myvar}');
        file_put_contents($path.DS.'template4-text.txt', 'TEXT <template> & content v={$myvar}');

        $instance = \Swift_Message::newInstance();

        $message->buildMessage($this->parser, $instance);

        $this->assertEquals('The subject', $instance->getSubject());
        $this->assertEquals('HTML Layout 4: HTML <template> & content v=my-value', $instance->getBody());
        $this->assertEquals('TEXT Layout 4: TEXT <template> & content v=my-value', $instance->getChildren()[0]->getBody());
    }

    /**
     * Create message with HTML and TEXT body from template HTMl and TEXT fields
     * using a text and a html layout.
     */
    public function testMessageWithTextAndHtmlBodyAndTextAndHtmlLayoutAndTextAndHtmlTemplateWichExtendsLayout(): void
    {
        $message = new ModelMessage();

        $message->setLocale('fr_FR');

        $message->setSubject('The subject');
        $message->setTextMessage('The TEXT content');
        $message->setHtmlMessage('The HTML content');

        $message->setTextTemplateFileName('template5-text.txt');
        $message->setHtmlTemplateFileName('template5-html.html');

        //$message->setHtmlLayoutFileName('layout5.html.tpl');
        //$message->setTextLayoutFileName('layout5.text.tpl');

        $path = $this->templateHelper->getActiveMailTemplate()->getAbsolutePath();

        $this->parser->assign('myvar', 'my-value');

        file_put_contents($path.DS.'layout5.html.tpl', 'HTML Layout 5: {block name="message-body"}{$message_body nofilter}{/block}');
        file_put_contents($path.DS.'layout5.text.tpl', 'TEXT Layout 5: {block name="message-body"}{$message_body nofilter}{/block}');

        file_put_contents($path.DS.'template5-html.html', '{extends file="layout5.html.tpl"}{block name="message-body"}HTML <template> & content v={$myvar}{/block}');
        file_put_contents($path.DS.'template5-text.txt', '{extends file="layout5.text.tpl"}{block name="message-body"}TEXT <template> & content v={$myvar}{/block}');

        $instance = \Swift_Message::newInstance();

        $message->buildMessage($this->parser, $instance);

        $this->assertEquals('The subject', $instance->getSubject());
        $this->assertEquals('HTML Layout 5: HTML <template> & content v=my-value', $instance->getBody());
        $this->assertEquals('TEXT Layout 5: TEXT <template> & content v=my-value', $instance->getChildren()[0]->getBody());
    }

    /**
     * Create message with HTML and TEXT body from template HTMl and TEXT fields
     * using a text and a html layout.
     */
    public function testMessageWithTextAndHtmlBodyAndTextAndHtmlExtendableLayout(): void
    {
        $message = new ModelMessage();

        $message->setLocale('fr_FR');

        $message->setSubject('The subject');
        $message->setTextMessage('TEXT <template> & content v={$myvar}');
        $message->setHtmlMessage('HTML <template> & content v={$myvar}');

        $message->setHtmlLayoutFileName('layout6.html.tpl');
        $message->setTextLayoutFileName('layout6.text.tpl');

        $path = $this->templateHelper->getActiveMailTemplate()->getAbsolutePath();

        $this->parser->assign('myvar', 'my-value');

        file_put_contents($path.DS.'layout6.html.tpl', 'HTML Layout 6: {block name="message-body"}{$message_body nofilter}{/block}');
        file_put_contents($path.DS.'layout6.text.tpl', 'TEXT Layout 6: {block name="message-body"}{$message_body nofilter}{/block}');

        $instance = \Swift_Message::newInstance();

        $message->buildMessage($this->parser, $instance);

        $this->assertEquals('The subject', $instance->getSubject());
        $this->assertEquals('HTML Layout 6: HTML <template> & content v=my-value', $instance->getBody());
        $this->assertEquals('TEXT Layout 6: TEXT <template> & content v=my-value', $instance->getChildren()[0]->getBody());
    }

    protected function tearDown(): void
    {
        $dir = $this->templateHelper->getActiveMailTemplate()->getAbsolutePath();

        ConfigQuery::write('active-mail-template', $this->backup_mail_template);

        $fs = new Filesystem();

        $fs->remove($dir);
    }
}
