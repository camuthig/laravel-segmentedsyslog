<?php namespace Camuthig\SegmentedSyslog;

use Camuthig\SegmentedSyslog\SegmentedSyslogWriter;
use Illuminate\Log\LogServiceProvider;
use Monolog\Logger;

class SegmentedSyslogServiceProvider extends LogServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('camuthig/segmentedsyslog');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $logger = new SegmentedSyslogWriter(new Logger($this->app['env']), $this->app['events']);

        $this->app->instance('log', $logger);

        if (isset($this->app['log.setup']))
        {
            call_user_func($this->app['log.setup'], $logger);
        }
    }

}
