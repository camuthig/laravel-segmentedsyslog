<?php

use Camuthig\SegmentedSyslog\Handler\SegmentedSyslogHandler;
use Monolog\Logger;

class SegmentedSyslogHandlerTest extends \TestCase {

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructMessageTooShort() {
        new SegmentedSyslogHandler('log', LOG_USER, Logger::DEBUG, true, LOG_PID, 17);
    }

    /**
     * @dataProvider handleProvider
     */
    public function testHandle($string) {
        $length = 50;
        $handler = \Mockery::mock(
            'Camuthig\SegmentedSyslog\Handler\SegmentedSyslogHandler[isHandling,write]',
            array('log', LOG_USER, Logger::DEBUG, true, LOG_PID, $length)
        )
        ->shouldAllowMockingProtectedMethods();

        $record = array(
            'message'    => $string,
            'context'    => array(),
            'level'      => Logger::DEBUG,
            'level_name' => 'DEBUG',
            'channel'    => 'production',
            'datetime'   => (object) array(
                'date'          => '2015-10-23 00:50:03.006436',
                'timezone_type' => 3,
                'timezone'      => 'UTC'
            ),
            'extra'      => array()
        );

        // How many segments we should expect to be written.
        $stringSegments = count(str_split($string, $length - 17));

        $handler->shouldReceive('isHandling')
            ->times($stringSegments+1)
            ->andReturn(true);
        $handler->shouldReceive('write')
            ->times($stringSegments)
            ->with(Mockery::on(function($arg) use ($record){
                return (bool)preg_match('/^[0-9a-f]{10}\:[0-9]{1,2}\:[0-9]{1,2}/', $arg['message']);
            }))
            ->andReturn(false);

        $handler->handle($record);
    }

    public function handleProvider() {
        return array(
            array(
                'A string I want to print with a bunch of characters'
            ),
            array(
                'A short string'
            )
        );
    }
}