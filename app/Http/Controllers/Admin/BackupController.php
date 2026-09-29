<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BackupController extends Controller
{
    protected function getBackupPath()
    {
        return storage_path('app/backups/backup.sql');
    }

    public function index()
    {
        $backupPath = $this->getBackupPath();
        $lastBackup = null;
        $backupExists = false;
        $backupSize = null;
        $backupDate = null;

        if (file_exists($backupPath)) {
            $backupExists = true;
            $timestamp = filemtime($backupPath);
            $lastBackup = Carbon::createFromTimestamp($timestamp)->diffForHumans();
            $backupSize = $this->humanFileSize(filesize($backupPath));
            $backupDate = date('d M Y, h:i A', $timestamp);
        }

        return view('admin.backup.index', compact('lastBackup', 'backupExists', 'backupSize', 'backupDate'));
    }

    public function csrfToken()
    {
        return response()->json(['token' => csrf_token()]);
    }

    public function runBackup()
    {
        $backupPath = $this->getBackupPath();
        $dir = dirname($backupPath);

        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        try {
            @set_time_limit(300);
            @ini_set('memory_limit', '512M');
            $pdo = DB::connection()->getPdo();
            
            // Get all tables
            $tables = [];
            $result = $pdo->query("SHOW TABLES");
            while ($row = $result->fetch(\PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }

            $sql = "-- Database Backup\n";
            $sql .= "-- Generated: " . now()->toDateTimeString() . "\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $table) {
                // Drop table if exists
                $sql .= "DROP TABLE IF EXISTS `" . $table . "`;\n";

                // Table structure
                $res = $pdo->query("SHOW CREATE TABLE `" . $table . "`");
                $showCreateRow = $res->fetch(\PDO::FETCH_NUM);
                $sql .= $showCreateRow[1] . ";\n\n";

                // Table data
                $res = $pdo->query("SELECT * FROM `" . $table . "`");
                $numFields = $res->columnCount();

                while ($row = $res->fetch(\PDO::FETCH_NUM)) {
                    $sql .= "INSERT INTO `" . $table . "` VALUES(";
                    for ($j = 0; $j < $numFields; $j++) {
                        if ($row[$j] === null) {
                            $sql .= "NULL";
                        } elseif (isset($row[$j])) {
                            $escaped = str_replace(
                                ["\\", "\0", "\n", "\r", "'", '"', "\x1a"],
                                ["\\\\", "\\0", "\\n", "\\r", "\\'", '\\"', "\\Z"],
                                $row[$j]
                            );
                            $sql .= "'" . $escaped . "'";
                        } else {
                            $sql .= "''";
                        }
                        if ($j < ($numFields - 1)) {
                            $sql .= ",";
                        }
                    }
                    $sql .= ");\n";
                }
                $sql .= "\n\n\n";
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

            file_put_contents($backupPath, $sql);

            return redirect()->back()->with('success', 'Database backup completed successfully!');
        } catch (\Exception $e) {
            Log::error("Database backup failed: " . $e->getMessage());
            return redirect()->back()->with('error', 'Database backup failed: ' . $e->getMessage());
        }
    }

    public function downloadBackup()
    {
        $path = $this->getBackupPath();
        abort_unless(file_exists($path), 404, 'No backup file found.');
        return response()->download($path, 'db_backup_' . date('Ymd_His') . '.sql');
    }

    public function runRestore()
    {
        $backupPath = $this->getBackupPath();

        if (!file_exists($backupPath)) {
            return redirect()->back()->with('error', 'No backup file found to restore.');
        }

        try {
            @set_time_limit(600);
            @ini_set('memory_limit', '512M');

            $this->executeSqlDump($backupPath);

            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');

            return redirect()->back()->with('success', 'Database restored successfully from backup!');
        } catch (\Exception $e) {
            Log::error("Database restore failed: " . $e->getMessage());
            return redirect()->back()->with('error', 'Database restore failed: ' . $e->getMessage());
        }
    }

    public function uploadBackup(Request $request)
    {
        $request->validate([
            'sql_file' => ['required', 'file', 'max:102400'],
        ]);

        $path = $this->getBackupPath();
        $dir = dirname($path);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Move uploaded file to backup path
        $request->file('sql_file')->move($dir, basename($path));

        return $this->runRestore();
    }

    public function clearProducts()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $tables = [
                'products', 'product_images', 'product_variants', 'product_variant_values',
                'product_tags', 'product_relations', 'stock_histories', 'reviews',
                'campaign_products', 'wishlists', 'compare_lists', 'recently_viewed', 'cart_items'
            ];

            foreach ($tables as $tbl) {
                if (\Illuminate\Support\Facades\Schema::hasTable($tbl)) {
                    DB::statement("TRUNCATE TABLE `{$tbl}`");
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');

            return redirect()->back()->with('success', 'All products deleted successfully! Categories, brands, attributes, and tags remain intact.');
        } catch (\Exception $e) {
            Log::error("Clear products failed: " . $e->getMessage());
            return redirect()->back()->with('error', 'Clear products failed: ' . $e->getMessage());
        }
    }

    public function resetDb()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Preserves categories, brands, tags, attributes, attribute_values!
            $tablesToTruncate = [
                'orders', 'order_items', 'order_status_history', 'courier_logs',
                'financial_transactions', 'wallet_transactions', 'loyalty_points',
                'carts', 'cart_items', 'wishlists', 'compare_lists', 'recently_viewed',
                'products', 'product_images', 'product_variants', 'product_variant_values',
                'product_tags', 'product_relations', 'stock_histories', 'reviews',
                'campaign_products', 'campaigns'
            ];

            foreach ($tablesToTruncate as $tbl) {
                if (\Illuminate\Support\Facades\Schema::hasTable($tbl)) {
                    DB::statement("TRUNCATE TABLE `{$tbl}`");
                }
            }

            // Delete non-admin users
            $allAdminIds = \App\Models\User::whereHas('roles', function($q) {
                $q->whereIn('name', ['admin', 'super-admin']);
            })->pluck('id')->toArray();
            if (!in_array(1, $allAdminIds)) {
                $allAdminIds[] = 1;
            }
            DB::table('addresses')->whereNotIn('user_id', $allAdminIds)->delete();
            \App\Models\User::whereNotIn('id', $allAdminIds)->delete();

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');

            return redirect()->back()->with('success', 'Database reset completed! Products, orders, and customer accounts deleted. Categories and settings preserved.');
        } catch (\Exception $e) {
            Log::error("Database reset failed: " . $e->getMessage());
            return redirect()->back()->with('error', 'Database reset failed: ' . $e->getMessage());
        }
    }

    protected function executeSqlDump(string $filePath): void
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception("Cannot open backup file for reading.");
        }

        $pdo = DB::connection()->getPdo();
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0;");

        $query = '';
        $inString = false;
        $stringChar = '';
        $isEscaped = false;

        while (($line = fgets($handle)) !== false) {
            $trimmed = trim($line);

            // Skip empty lines and comments outside of strings
            if (!$inString && ($trimmed === '' || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '#'))) {
                continue;
            }

            $len = strlen($line);
            for ($i = 0; $i < $len; $i++) {
                $char = $line[$i];

                if ($isEscaped) {
                    $isEscaped = false;
                    $query .= $char;
                    continue;
                }

                if ($char === '\\') {
                    $isEscaped = true;
                    $query .= $char;
                    continue;
                }

                if ($inString) {
                    if ($char === $stringChar) {
                        $inString = false;
                    }
                    $query .= $char;
                } else {
                    if ($char === "'" || $char === '"' || $char === '`') {
                        $inString = true;
                        $stringChar = $char;
                        $query .= $char;
                    } elseif ($char === ';') {
                        $stmt = trim($query);
                        if ($stmt !== '') {
                            // If CREATE TABLE found without DROP TABLE IF EXISTS, drop it first
                            if (preg_match('/^CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?(?:`([^`]+)`|([a-zA-Z0-9_]+))/i', $stmt, $m)) {
                                $tblName = !empty($m[1]) ? $m[1] : $m[2];
                                $pdo->exec("DROP TABLE IF EXISTS `{$tblName}`;");
                            }
                            $pdo->exec($stmt);
                        }
                        $query = '';
                    } else {
                        $query .= $char;
                    }
                }
            }
        }

        $stmt = trim($query);
        if ($stmt !== '') {
            $pdo->exec($stmt);
        }

        fclose($handle);
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1;");
    }

    private function humanFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
