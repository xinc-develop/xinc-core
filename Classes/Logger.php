<?php
/*
 * Xinc - Continuous Integration.
 *
 * @author    David Ellis <username@example.com>
 * @author    Gavin Foster <username@example.com>
 * @author    Arno Schneider <username@example.com>
 * @copyright 2007 David Ellis, One Degree Square
 * @copyright 2015-2016 Xinc Development Team, https://github.com/xinc-develop/
 * @license   http://www.gnu.org/copyleft/lgpl.html GNU/LGPL, see license.php
 *            This file is part of Xinc.
 *            Xinc is free software; you can redistribute it and/or modify
 *            it under the terms of the GNU Lesser General Public License as
 *            published by the Free Software Foundation; either version 2.1 of
 *            the License, or (at your option) any later version.
 *
 *            Xinc is distributed in the hope that it will be useful,
 *            but WITHOUT ANY WARRANTY; without even the implied warranty of
 *            MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *            GNU Lesser General Public License for more details.
 *
 *            You should have received a copy of the GNU Lesser General Public
 *            License along with Xinc, write to the Free Software Foundation,
 *            Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @homepage  https://github.com/xinc-develop/xinc-core/
 */

namespace Xinc\Core;

/**
 * The Xinc log facility
 */
class Logger
{
    /**
     * Path to the main log file.
     *
     * @var string
     */
    private $file;

    /**
     * Path to the build log file.
     *
     * @var string
     */
    private $buildLogFile;

    /**
     * Queue of logger messages.
     *
     * @var array of Xinc::Core::Logger::Message messages
     */
    private $logQueue = array();

    /**
     * Maximum messages in queue.
     */
    private $max = 50;

    /**
     * Loglevel
     */
    private $logLevel = 2;

//! @{
    const LOG_LEVEL_DEBUG = 0;
    const LOG_LEVEL_VERBOSE = 1;
    const LOG_LEVEL_INFO = 2;
    const LOG_LEVEL_WARN = 3;
    const LOG_LEVEL_ERROR = 4;

    const DEFAULT_LOG_LEVEL = 2;

    const DEBUG = self::LOG_LEVEL_DEBUG;
    const VERBOSE = self::LOG_LEVEL_VERBOSE;
    const INFO = self::LOG_LEVEL_INFO;
    const WARN = self::LOG_LEVEL_WARN;
    const ERROR = self::LOG_LEVEL_ERROR;

    public static $logLevelError = array(self::LOG_LEVEL_ERROR, 'error');
    public static $logLevelWarn = array(self::LOG_LEVEL_WARN, 'warn');
    public static $logLevelInfo = array(self::LOG_LEVEL_INFO, 'info');
    public static $logLevelVerbose = array(self::LOG_LEVEL_VERBOSE, 'verbose');
    public static $logLevelDebug = array(self::LOG_LEVEL_DEBUG, 'debug');

    private $logLevelSet = false;
//! @}

    /**
     * Setter for loglevel
     */
    public function setLogLevel($level)
    {
        $this->logLevelSet = true;
        if ($level != $this->getLogLevel()) {
            $this->logLevel = $level;
            $this->info("Setting loglevel to $level");
        }
    }

    /**
     * getter for the currently used loglevel
     */
    public function getLogLevel()
    {
        return $this->logLevel;
    }

    /**
     * Add a new log message to the logger queue.
     *
     * @param string   $priority
     * @param string   $msg
     * @param resource $fileHandle to write to instead of logfile
     *
     * @todo parse log level to display from a config
     */
    private function log($priority, $msg, $fileHandle = null)
    {
        if ($priority[0] < $this->logLevel && $fileHandle === null) {
            return;
        }

        $logTime = time();

        $this->logQueue[] = new Logger\Message($priority[1], $logTime, $msg);

        /* ensure the output messages line up vertically */
        $prioritystr = '['.$priority[1].']';
        $timestr = '['.date('Y-m-d H:i:s - T', $logTime).']';
        while (strlen($prioritystr) < 7) {
            $prioritystr .= ' ';
        }
        $message = $prioritystr.'  '.$timestr.' '.$msg."\n";
        $message = getmypid().': '.$message;

        if (defined('STDERR')) {
            fputs(STDERR, getmypid().': '.$prioritystr.'  '.$msg."\n");
        } else {
            echo '<!-- LogMessage: '.$message." -->\n";
        }

        if ($this->file != null) {
            if ($fileHandle !== null) {
                fputs($fileHandle, $message);
            } else {
                error_log($message, 3, $this->file);
            }
        } elseif ($fileHandle !== null) {
            fputs($fileHandle, $message);
        }

        if (count($this->logQueue) > $this->max) {
            $this->flush();
        }
    }

    /**
     * Log a message with priority 'error'.
     *
     * @param string $msg
     * @param resource fileHandle
     */
    public function error($msg, $fileHandle = null)
    {
        $this->log(self::$logLevelError, $msg, $fileHandle);
    }

    /**
     * Log a message with priority 'warn'.
     *
     * @param string $msg
     * @param resource fileHandle
     */
    public function warn($msg, $fileHandle = null)
    {
        $this->log(self::$logLevelWarn, $msg, $fileHandle = null);
    }

    /**
     * Log a message with priority 'info'.
     *
     * @param string $msg
     * @param resource fileHandle
     */
    public function info($msg, $fileHandle = null)
    {
        $this->log(self::$logLevelInfo, $msg, $fileHandle = null);
    }

    /**
     * Log a message with priority 'debug'.
     *
     * @param string $msg
     * @param resource fileHandle
     */
    public function debug($msg, $fileHandle = null)
    {
        $this->log(self::$logLevelDebug, $msg, $fileHandle);
    }

    /**
     * Log a message with priority 'verbose'.
     *
     * @param string $msg
     * @param resource fileHandle
     */
    public function verbose($msg, $fileHandle = null)
    {
        $this->log(self::$logLevelVerbose, $msg, $fileHandle = null);
    }

    /**
     * Empty the log queue.
     */
    public function emptyLogQueue()
    {
        $this->resetLogQueue();
    }

    /**
     * Flush the log queue to the log file.
     */
    public function flush()
    {
        if (null == $this->buildLogFile) {
            $this->resetLogQueue();

            return;
        }
        $messageElements = array();
        for ($i = count($this->logQueue) - 1; $i >= 0; --$i) {
            $message = $this->logQueue[$i];
            $messageString = '<message priority="'.$message->priority.'" ';
            $messageString .= 'timestamp="'.$message->timestamp.'" ';
            $messageString .= 'time="'.date('Y-m-d H:i:s - T', $message->timestamp).'"><![CDATA[';
            $messageString .= base64_encode($message->message);
            $messageString .= ']]></message>';

            $messageElements[] = $messageString;
        }

        $previousLogMessages = '';

        $dirName = dirname($this->buildLogFile);
        if (!file_exists($dirName)) {
            mkdir($dirName, 0755, true);
        }
        if (file_exists($this->buildLogFile)) {
            // copying to temporary file for later inclusion via fgets, less memory consuming
            copy($this->buildLogFile, $this->buildLogFile.'.temp');
        }
        $fh = fopen($this->buildLogFile, 'w+');
        if (is_resource($fh)) {
            fputs($fh, '<?xml version="1.0"?>');
            fputs($fh, "\n");
            fputs($fh, '<build>');
            fputs($fh, "\n");
            fputs($fh, implode("\n", $messageElements));
            fputs($fh, "\n");
            if (file_exists($this->buildLogFile.'.temp')) {
                $fht = fopen($this->buildLogFile.'.temp', 'r');
                if (is_resource($fht)) {
                    $lineCounter = 0;
                    while ($line = fgets($fht)) {
                        // skip first two lines (xml decl and build opening element
                        if ($lineCounter > 2) {
                            fputs($fh, $line);
                        }
                        ++$lineCounter;
                    }
                    fclose($fht);
                    unlink($this->buildLogFile.'.temp');
                } else {
                    self::error('Cannot include previous log messages');
                }
            } else {
                fputs($fh, '</build>');
            }
            //fputs($fh, $previousLogMessages);
            fclose($fh);
            //file_put_contents($this->buildLogFile, $buildXml);
        } else {
            self::error('Cannot open: '.$this->buildLogFile.' for writing.');
        }
        $this->resetLogQueue();
    }

    /**
     * empties the log message queue
     */
    private function resetLogQueue()
    {
        $this->logQueue = array();
    }

    /**
     * Set the path to the log file.
     *
     * @param string $logFile
     */
    public function setBuildLogFile($logFile)
    {
        $this->buildLogFile = $logFile;
    }

    /**
     * @param string $logFile
     *
     * @throws Xinc\Core\Logger\Exception\NonWriteable
     */
    public function setXincLogFile($logFile)
    {
        $parentDir = dirname($logFile);

        if (!is_writeable($logFile) && !is_writeable($parentDir)) {
            $this->error('Cannot open "'.$logFile.'" for writing', STDERR);
            throw new Logger\Exception\NonWriteableException($logFile);
        }
        $this->file = $logFile;
    }
}
