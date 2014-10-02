<?php

/**
 * This file is part of the WarGaming API package
 *
 * (c) Vitaliy Zhuk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace WarGaming\Api\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WarGaming\Api\Events;
use WarGaming\Api\Events\MethodStartEvent;
use WarGaming\Api\Events\MethodCompleteEvent;
use WarGaming\Api\Events\MethodErrorEvent;
use WarGaming\Api\Events\RequestCompleteEvent;
use WarGaming\Api\Events\RequestErrorEvent;
use WarGaming\Api\Events\RequestStartEvent;
use WarGaming\Api\Exception\MethodNotValidException;

/**
 * Debug subscriber.
 * View request for next debugging.
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class DebugSubscriber implements EventSubscriberInterface
{
    const DEBUG_START       = 0b00000001;
    const DEBUG_COMPLETE    = 0b00000010;
    const DEBUG_ERROR       = 0b00000100;

    /**
     * @var resource
     */
    private $stdOut;

    /**
     * @var int
     */
    private $mode;

    /**
     * Construct
     *
     * @param resource $stdOut
     * @param int      $mode
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($stdOut, $mode = 0b00000111)
    {
        if (!is_resource($stdOut)) {
            throw new \InvalidArgumentException(sprintf(
                'The stdout must be a resource, but "%s" given.',
                is_object($stdOut) ? get_class($stdOut) : gettype($stdOut)
            ));
        }

        $this->stdOut = $stdOut;
        $this->mode = $mode;
    }

    /**
     * Event on api method start
     *
     * @param MethodStartEvent $event
     */
    public function methodStart(MethodStartEvent $event)
    {
        if (!($this->mode & self::DEBUG_START)) {
            return;
        }

        $method = $event->getMethod();

        $this->writeln(sprintf(
            'Start API method: %s',
            get_class($method)
        ));
    }

    /**
     * Event on api method complete
     */
    public function methodComplete()
    {
        if (!($this->mode & self::DEBUG_COMPLETE)) {
            return;
        }

        $this->writeln('Success process API method.');

        $this->writeln(null);
    }

    /**
     * Event in api method error
     *
     * @param MethodErrorEvent $event
     */
    public function methodError(MethodErrorEvent $event)
    {
        if (!($this->mode & self::DEBUG_COMPLETE)) {
            return;
        }

        $exception = $event->getException();

        $this->writeln(sprintf(
            'Error with process API method with message: %s',
            $exception->getMessage()
        ));

        if ($exception instanceof MethodNotValidException) {
            $this->writeln("Method not valid:");

            foreach ($exception->getViolationList() as $violation) {
                /** @var \Symfony\Component\Validator\ConstraintViolationInterface $violation */
                $this->writeln(sprintf(
                    "%s: %s",
                    $violation->getPropertyPath(),
                    $violation->getMessage()
                ));
            }
        }

        $this->writeln(null);
    }

    /**
     * Event on request start
     *
     * @param RequestStartEvent $event
     */
    public function requestStart(RequestStartEvent $event)
    {
        if (!($this->mode & self::DEBUG_START)) {
            return;
        }

        $request = $event->getRequest();

        $this->writeln(sprintf(
            'Start request to URI: %s %s',
            $request->getMethod(),
            $request->getUrl()
        ));

        if ('POST' === $request->getMethod()) {
            // @todo: debug post data.
        }
    }

    /**
     * Event on request complete
     *
     * @param RequestCompleteEvent $event
     */
    public function requestComplete(RequestCompleteEvent $event)
    {
        if (!($this->mode & self::DEBUG_COMPLETE)) {
            return;
        }

        $response = $event->getResponse();

        $this->writeln(sprintf(
            'Success process request. Content length: %d.',
            (string) $response->getHeader('Content-Length')
        ));
    }

    /**
     * Event on request error
     *
     * @param RequestErrorEvent $event
     */
    public function requestError(RequestErrorEvent $event)
    {
        if (!($this->mode & self::DEBUG_ERROR)) {
            return;
        }

        $message = $event->getException()->getMessage();

        $this->writeln(sprintf(
            'Request error with message: %s',
            $message
        ));
    }

    /**
     * Write lines to std out
     *
     * @param array|string $lines
     */
    private function writeln($lines)
    {
        if (!is_array($lines)) {
            $lines = array($lines);
        }

        foreach ($lines as $line) {
            $line = rtrim($line) . "\n";

            fwrite($this->stdOut, $line);
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::METHOD_START => array(
                array('methodStart')
            ),

            Events::METHOD_COMPLETE => array(
                array('methodComplete')
            ),

            Events::METHOD_ERROR => array(
                array('methodError')
            ),

            Events::REQUEST_START => array(
                array('requestStart')
            ),

            Events::REQUEST_COMPLETE => array(
                array('requestComplete')
            ),

            Events::REQUEST_ERROR => array(
                array('requestError')
            )
        );
    }
}
