<?php
declare(strict_types = 1);
namespace Slothsoft\Core;

use InvalidArgumentException;

/**
 * Interactive process execution helper.
 *
 * @see https://github.com/uuf6429/InterExec
 * @deprecated Included for historical compatibility only. This API is out of support and should not be used in new code.
 */
class InterExec {
    
    /**
     * The command to run.
     */
    public string $command_to_run = '';
    
    /**
     * Environment variables (null to use existing variables).
     */
    public ?array $environment_vars = null;
    
    /**
     * Time, in seconds, after which command is forcefully aborted.
     */
    public int $timeout = 0;
    
    /**
     * All the program's standard output till now.
     */
    public string $stdout = '';
    
    /**
     * All the program's error output till now.
     */
    public string $stderr = '';
    
    /**
     * Program's exit code (only set after the program quits).
     */
    public int $return = 0;
    
    /**
     * Timestamp in seconds of start of execution.
     */
    public float $time_start = 0.0;
    
    /**
     * The time taken for the program to run and close.
     */
    public float $time_taken = 0;
    
    /**
     * Legacy timeout counter name used by the original implementation.
     */
    public float $taken = 0;
    
    /**
     * If enabled, fixes a problem with popen not allowing spaces inside program path (even when quoted).
     */
    public bool $fix_windows_path = true;
    
    /**
     * Interval between ticks, in seconds (a value of zero disables interval)
     */
    public float $tick_interval = 0;
    
    /**
     * Array containing event callbacks.
     */
    protected array $events = array();
    
    /**
     * Process resource.
     *
     * @var resource
     */
    public $process_handle = null;
    
    /**
     * Size of buffer for reading from pipes.
     */
    public int $data_buffer_size = 4096;
    
    /**
     * Process I/O pipes.
     */
    public ?array $pipes = null;
    
    /**
     * Pipe type, pipe or pty (Linux only, PHP must be compiled with --enable-pty)
     * Constants : PIPE_TYPE_DEFAULT, PIPE_TYPE_PTY
     */
    public string $pipeType = self::PIPE_TYPE_DEFAULT;
    
    const STDIN = 0;
    
    const STDOUT = 1;
    
    const STDERR = 2;
    
    const PIPE_TYPE_DEFAULT = 'pipe';
    
    const PIPE_TYPE_PTY = 'pty';
    
    /**
     * Creates new instance.
     *
     * @param string $command_to_run
     *            The command line to execute.
     * @param array $environment_vars
     *            (Optional) Environment variables.
     * @return void
     */
    public function __construct($command_to_run, $environment_vars = null) {
        $this->command_to_run = $command_to_run;
        $this->environment_vars = $environment_vars;
    }
    
    /**
     * Call callback when an event is triggered.
     *
     * @param string $event
     *            Name of event.
     * @param callable $callback
     *            The callback to call.
     * @return void
     */
    public function on($event, $callback) {
        $this->events[$event] = $callback;
    }
    
    /**
     * Trigger an event.
     *
     * @param string $event
     *            Name of event.
     * @param array $args
     *            (Optional) Arguments to pass to event.
     *            Note that the first argument is always $this.
     * @return mixed Value resulting from event callback.
     * @throws InvalidArgumentException
     */
    protected function fire($event, $args = array()) {
        if (! is_array($args))
            throw new InvalidArgumentException('Event arguments should be an array');
        if (isset($this->events[$event])) {
            array_unshift($args, $this);
            return call_user_func_array($this->events[$event], $args);
        }
        return null;
    }
    
    /**
     * Returns whether process is currently running or not.
     *
     * @return boolean
     */
    public function is_running() {
        if (is_resource($this->process_handle)) {
            $stat = proc_get_status($this->process_handle);
            return ! (! $stat['running'] || $stat['signaled'] || $stat['stopped']);
        }
        return false;
    }
    
    /**
     * Returns whether stream currently has pending content or not.
     *
     * @param resource $stream
     *            The stream resource.
     * @return boolean True if there is unread content, false otherwise.
     */
    protected function stream_has_content($stream) {
        $stat = stream_get_meta_data($stream);
        // print_r($stat);
        return ! $stat['eof']; // && !$stat['blocked'];
    }
    
    /**
     * This hack fixes a legacy issue in popen not handling escaped command filenames on Windows.
     * Basically, if we're on windows and the first command part is double quoted, we CD into the
     * directory and execute the command from there.
     *
     * @param string $commandPath
     *            The command to fix.
     * @return string The command with the path fixed.
     * @example : '"C:\a test\b.exe" -h' -> 'cd "C:\a test\" && b.exe -h'
     */
    protected function fix_windows_command_path($commandPath) {
        return trim(preg_replace('/^\s*"([^"]+?)\\\\([^\\\\]+)"\s?(.*)/s', 'cd "$1" && "$2" $3', $commandPath));
    }
    
    /**
     * @return void
     */
    protected function run_startup() {
        // initialize variables
        if ($this->fix_windows_path && DIRECTORY_SEPARATOR == '\\') {
            $this->command_to_run = $this->fix_windows_command_path($this->command_to_run);
        }
        $this->stdout = '';
        $this->stderr = '';
        $this->pipes = array();
        $this->time_start = microtime(true);
        $this->_last_buffer_data = '';
        
        // create process and pipes
        $this->process_handle = proc_open($this->command_to_run, array(
            self::STDIN => array(
                $this->pipeType,
                'r'
            ), // STDIN
            self::STDOUT => array(
                $this->pipeType,
                'w'
            ), // STDOUT
            self::STDERR => array(
                $this->pipeType,
                'w'
            ) // STDERR
        ), $this->pipes, null, $this->environment_vars);
        
        $this->fire('start');
        
        // avoid blocking on pipes
        stream_set_blocking($this->pipes[self::STDIN], false);
    }
    
    /**
     * @return void
     */
    protected function run_mainloop() {
        // wait for process to finish
        while (true) {
            $this->fire('tick');
            
            // if process quit, break main loop
            if (! $this->is_running()) {
                break;
            }
            
            // pipe stream wrappers
            $w = array(
                $this->pipes[self::STDIN]
            );
            $r = array(
                $this->pipes[self::STDOUT],
                $this->pipes[self::STDERR]
            );
            $e = null;
            
            // handle any pending I/O
            if (stream_select($r, $w, $e, null /* , 25000 */) > 0) {
                // handle STDOUT, STDERR
                foreach ($r as $h) {
                    // clear the buffer
                    $buf = '';
                    
                    // read data into buffer
                    $t = array_search($h, $this->pipes);
                    if ($t !== false /*TEST->*/ && $t != self::STDERR/*<-TEST*/) {
                        if ($this->stream_has_content($h)) {
                            $buf .= fread($h, $this->data_buffer_size);
                        }
                    }
                    
                    // if buffer is not empty...
                    if ($buf !== '') {
                        // fire output event
                        if ($t == self::STDOUT) {
                            $this->stdout .= $buf;
                            $lastBuffer = $buf;
                            $this->fire('output', array(
                                $buf
                            ));
                        }
                        
                        // fire error event
                        if ($t == self::STDERR) {
                            $this->stderr .= $buf;
                            $this->fire('error', array(
                                $buf
                            ));
                        }
                    }
                }
                
                // handle STDIN
                foreach ($w as $h) {
                    // fire input event
                    if ($h === $this->pipes[self::STDIN]) {
                        $data = $this->fire('input', array(
                            $lastBuffer
                        ));
                        fwrite($this->pipes[self::STDIN], $data ?: PHP_EOL);
                        fflush($this->pipes[self::STDIN]);
                        $lastBuffer = '';
                    }
                }
                
                // if process quit, break I/O loop
                if (! $this->is_running()) {
                    break;
                }
            }
            
            // calculate time taken so far
            $this->time_taken = microtime(true) - $this->time_start;
            
            // check for timeout
            if ($this->timeout && $this->taken > $this->timeout) {
                // TODO $this->signal(self::TIMEOUT);
                break;
            }
            
            // sleep for a while
            if ($this->tick_interval) {
                usleep($this->tick_interval * 1000000);
            }
        }
    }
    
    /**
     * @return mixed
     */
    protected function run_shutdown() {
        // close and clean used resources
        foreach ($this->pipes as $pipe)
            fclose($pipe);
        $this->pipes = null;
        $this->return = proc_close($this->process_handle);
        $this->process_handle = null;
        
        // calculate time taken so far
        $this->time_taken = microtime(true) - $this->time_start;
        
        $this->fire('stop', array(
            $this->return
        ));
    }
    
    /**
     * Runs the command!
     *
     * @return InterExec
     */
    public function run() {
        // run process loop
        $this->run_startup();
        $this->run_mainloop();
        $this->run_shutdown();
        
        // return result (for chaining)
        return $this;
    }
}


