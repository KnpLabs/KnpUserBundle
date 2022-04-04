<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\EventListener;

use FOS\UserBundle\EventListener\FlashListener;
use FOS\UserBundle\FOSUserEvents;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\Translation\TranslatorInterface;

class FlashListenerTest extends TestCase
{
    /** @var Event */
    private $event;

    /** @var FlashListener */
    private $listener;

    /**
     * @var TranslatorInterface&MockObject
     */
    private $translator;

    protected function setUp(): void
    {
        $this->event = new Event();

        $flashBag = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session\Flash\FlashBag')->getMock();

        $session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session\Session')->disableOriginalConstructor()->getMock();
        $session
            ->expects($this->once())
            ->method('getFlashBag')
            ->willReturn($flashBag);

        $request = new Request();
        $request->setSession($session);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $this->translator = $this->getMockBuilder(TranslatorInterface::class)->getMock();

        $this->listener = new FlashListener($requestStack, $this->translator);
    }

    public function testAddSuccessFlash()
    {
        $this->translator->method('trans')
            ->with('change_password.flash.success', [], 'FOSUserBundle')
            ->willReturn('Success message');

        $this->listener->addSuccessFlash($this->event, FOSUserEvents::CHANGE_PASSWORD_COMPLETED);
    }
}
