<?php namespace Camuthig\SegmentedSyslog\Handler;

use Monolog\Handler\SyslogHandler;
use Monolog\Logger;

class SegmentedSyslogHandler extends SyslogHandler {

    /**
     * The maximum length for Syslog messages
     * @var int
     */
    protected $length;

    /**
     * The number of bytes needed for our segment identification message.
     * @var int
     */
    protected $identificationLength = 17;

    /**
     * @param string  $ident
     * @param mixed   $facility
     * @param integer $level    The minimum logging level at which this handler will be triggered
     * @param boolean $bubble   Whether the messages that are handled can bubble up the stack or not
     * @param int     $logopts  Option flags for the openlog() call, defaults to LOG_PID
     * @param int     $length   The maximum length to allow for each log message segment
     */
    public function __construct(
        $ident,
        $facility = LOG_USER,
        $level = Logger::DEBUG,
        $bubble = true,
        $logopts = LOG_PID,
        $length = 1024
    ) {
        if ($length <= $this->identificationLength) {
            throw new \InvalidArgumentException(
                "The maximum message length must be at least greater than the identification length of " .
                    $this->identificationLength,
                1);

        }
        parent::__construct($ident, $facility, $level, $bubble, $logopts);

        $this->length = $length;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(array $record)
    {
        if (!$this->isHandling($record)) {
            return false;
        }

        $segmentedMessage = str_split($record['message'], $this->length - $this->identificationLength);
        $totalSegments = count($segmentedMessage);
        $messageId = substr(uniqid(), 3, 13);
        for ($index=0; $index < $totalSegments; $index++) {
            $recordSegment = $record;
            $recordSegment['message'] =
                strval($messageId) . ':' .
                strval($index+1) . ':' .
                strval($totalSegments) . ' ' .
                $segmentedMessage[$index];
            $segmentResult = parent::handle($recordSegment);
        }

        return $segmentResult;
    }

}