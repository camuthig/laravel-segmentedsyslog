<?php namespace Camuthig\SegmentedSyslog;

use Config;
use Camuthig\SegmentedSyslog\Handler\SegmentedSyslogHandler;
use Illuminate\Log\Writer;

class SegmentedSyslogWriter extends Writer {

    /**
     * Configure the Laravel application to log messages using a SegmentedSyslogHandler.
     * Along with setting the identity and minimum log levels, this method also allows the developer
     * to set the facility to log messages with.
     *
     * @param  string  $name        The name of the application to log using
     * @param  int     $level       The level minimum level of messages to log
     * @param  string  $length      The maximum length that Syslog messages should follow
     * @param  mixed   $facility    The Syslog facility to log messages to
     * @param  boolean $bubble      Whether messages that are handled can bubble up the stack or not
     * @param  int     $logopts     Option flags for the openlog() call, defaults to LOG_PID
     * @return void
     */
    public function useSegmentedSyslog(
        $name = 'laravel',
        $level = 'debug',
        $length = 1024,
        $facility = LOG_USER,
        $bubble = true,
        $logopts = LOG_PID

    ) {
        if (empty($length)) {
            $length = Config::get('segmentedsyslog::logging.messageLength');
        }
        return $this->monolog->pushHandler(new SegmentedSyslogHandler($name, $facility, $level, $bubble, $logopts, $length));
    }
}
