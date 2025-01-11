<?php

namespace App\Providers;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Database\Events\TransactionRolledBack;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class DataBaseQueryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->listenDB();

        Event::listen(TransactionBeginning::class, function (TransactionBeginning $event): void {
            Log::debug('START TRANSACTION');
        });

        Event::listen(TransactionCommitted::class, function (TransactionCommitted $event): void {
            Log::debug('COMMIT');
        });

        Event::listen(TransactionRolledBack::class, function (TransactionRolledBack $event): void {
            Log::debug('ROLLBACK');
        });
    }

    private function listenDB()
    {
        DB::listen(function ($query): void {
            $sql = $query->sql;

            foreach ($query->bindings as $binding) {
                if (is_string($binding)) {
                    $binding = "'{$binding}'";
                } elseif (is_bool($binding)) {
                    $binding = $binding ? '1' : '0';
                } elseif (is_int($binding)) {
                    $binding = (string) $binding;
                } elseif ($binding === null) {
                    $binding = 'NULL';
                } elseif ($binding instanceof Carbon) {
                    $binding = "'{$binding->toDateTimeString()}'";
                } elseif ($binding instanceof DateTime) {
                    $binding = "'{$binding->format('Y-m-d H:i:s')}'";
                }

                $sql = preg_replace('/\\?/', $binding, $sql, 1);
            }

            $tracesText = (env('APP_ENV') == 'local') ? $this->getTrace() : '';

            // PGSQL处理
            if (config('database.default') == 'pgsql') {
                // Log::debug(print_r(['time' => "{$query->time} ms", 'sql' => $sql, 'Executed at' => $tracesText], true));
                $sql = str_replace('"', "'", $sql);
                Log::debug('SQL', ['time' => "{$query->time} ms", 'sql' => $sql, 'Executed at' => $tracesText]);
            } else {
                Log::debug('SQL', ['time' => "{$query->time} ms", 'sql' => $sql, 'Executed at' => $tracesText]);
            }
        });
    }

    /**
     * 获取调用栈
     *
     * @return string
     * @author Shirolin
     * @since  2023-05-29
     */
    private function getTrace()
    {
        $traces = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 30);
        $tracesText = '';
        $num = 0;
        foreach ($traces as $trace) {
            // 过滤文件名中包含'DataBaseQueryServiceProvider'的文件
            if (isset($trace['file']) && strpos($trace['file'], 'DataBaseQueryServiceProvider') !== false) {
                continue;
            }
            // 过滤文件名中包含'Illuminate'的文件
            if (isset($trace['file']) && strpos($trace['file'], 'Illuminate') !== false) {
                continue;
            }

            if (isset($trace['file']) && isset($trace['line'])) {
                $tracesText .= "\n[{$num}]" . $trace['file'] . ':' . $trace['line'];
            }
            $num++;
        }

        return $tracesText;
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
