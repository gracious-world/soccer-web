<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use App\Models\Func\SysConfig;

use Config;
use Log;

class BaseJob extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $logName = 'jobs';
    protected $logFile = 'base-jobs';
    protected $logger;
    protected $logPath;
    public function __construct()
    {
        $this->setLogFile();
        // pr($this->logFile);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }

    protected function setLogFile() {
        if (SysConfig::check('service_write_log', true)) {
            $this->logPath = Config::get('custom-sysconfig.default-log-path') . $this->logName . DS . date('Ym') . DS . date('d');
            $this->logger = new Logger(get_called_class());
            $this->logFile = $this->logPath . DS . ($this->logFile ? $this->logFile : get_called_class()) . '.log';
            $this->logger->pushHandler(new StreamHandler($this->logFile, Logger::INFO));
        }
    }

    protected function writeLog($sString, array $context = []) {
        if ($sString) {
            $this->logger->addInfo('pid=' . posix_getpid() . ': ' . $sString, $context);
        }
    }
}