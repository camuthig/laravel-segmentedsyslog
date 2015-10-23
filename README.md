# Laravel Segmented Syslog
Laravel Segmented Syslog provides an extension on the normal Laravel syslog service to enable breaking up log messages into chunks to fit into the maximum allowed length defined by different syslog implementations. Each messsage will include a message identifier as well as a total number of segments and the current segment number

The section below shows an example of the format. The identifier here would be ```56290be46d:1:2``` and ```56290be46d:2:2```

```
  Oct 22 09:16:36 computer.local laravel[34348]: test.DEBUG: 56290be46d:1:2 Debug log with a lot of [] []
  Oct 22 09:16:36 computer.local laravel[34348]: test.DEBUG: 56290be46d:2:2 text to display [] []
```

# Requirements
Segmented Syslog is tested on Laravel version 4.2 and will work on any PHP system >= 5.3

# Setup
1. Install segmented syslog

  ```php
  composer require camuthig/segmentedsyslog:dev-master
  ```
1. Replace the standard LogServiceProvider for SegmentedSyslogServiceProvider in config/app.php. Don't worry, the provider extends the base Laravel provider, so all functionality is still available.

  ```php
  'providers' => array(
    ...
    // 'Illuminate\Log\LogServiceProvider',
  	'Camuthig\SegmentedSyslog\SegmentedSyslogServiceProvider',
  	...
  ),
  ```
1. Update your Log handler in global.php
  ```php
  Log::useSegmentedSyslog();
  ```
  
# Configuration
When declaring the Log facade to use the segmented syslog, you are able to configure all of the properties for the syslog, with the parameters as follows

  ```php
  public function useSegmentedSyslog(
        $name = 'laravel',
        $level = 'debug',
        $length = 1024,
        $facility = LOG_USER,
        $bubble = true,
        $logopts = LOG_PID
    )
  ```


