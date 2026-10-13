<?php

namespace App\Console\Commands;

use App\Support\AuditLogger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RunDatabaseBackup extends Command
{
    protected $signature = 'backup:run';
    protected $description = 'Generate a full SQL backup of the SB-Serve database';

    public function handle(): int
    {
        $path = storage_path('app/backups');
        if (! File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $filename = 'sbserve-backup-' . now()->format('Y-m-d-His') . '.sql';
        $filepath = $path . DIRECTORY_SEPARATOR . $filename;

        $database = config('database.connections.mysql.database');
        $tables = DB::select('SHOW TABLES');
        $tableKey = "Tables_in_{$database}";

        $sql = "-- SB-Serve Database Backup\n-- Generated: " . now()->toDateTimeString() . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;

            $createStmt = DB::select("SHOW CREATE TABLE `{$tableName}`")[0];
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createStmt->{'Create Table'} . ";\n\n";

            $rows = DB::table($tableName)->get();

            if ($rows->count() > 0) {
                $columns = array_keys((array) $rows->first());
                $columnList = implode('`, `', $columns);

                foreach ($rows->chunk(100) as $chunk) {
                    $sql .= "INSERT INTO `{$tableName}` (`{$columnList}`) VALUES\n";
                    $valueLines = [];

                    foreach ($chunk as $row) {
                        $values = array_map(function ($value) {
                            if (is_null($value)) {
                                return 'NULL';
                            }
                            return "'" . str_replace(
                                ["\\", "'", "\n", "\r"],
                                ["\\\\", "\\'", "\\n", "\\r"],
                                (string) $value
                            ) . "'";
                        }, (array) $row);

                        $valueLines[] = '(' . implode(', ', $values) . ')';
                    }

                    $sql .= implode(",\n", $valueLines) . ";\n\n";
                }
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        File::put($filepath, $sql);

        AuditLogger::log("Generated database backup: {$filename}");

        $this->info("Backup completed: {$filename}");

        return self::SUCCESS;
    }
}
