<?php
/**
 * SewaSathi - Database Connection Handler (PDO Singleton)
 */

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $host = getenv('DB_HOST') ?: '127.0.0.1';
            $port = getenv('DB_PORT') ?: '3306';
            $db   = getenv('DB_NAME') ?: 'sewasathi_db';
            $user = getenv('DB_USER') ?: 'sewasathi_user';
            $pass = getenv('DB_PASS') ?: 'sewasathi_pass';
            $charset = 'utf8mb4';

            // Check if running in Docker container where DB host might be 'db'
            $hostsToTry = [$host];
            if ($host === '127.0.0.1' || $host === 'localhost') {
                $hostsToTry[] = 'db';
            }

            $connected = false;
            $lastException = null;

            foreach ($hostsToTry as $currentHost) {
                $dsn = "mysql:host={$currentHost};port={$port};dbname={$db};charset={$charset}";
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ];

                try {
                    self::$instance = new PDO($dsn, $user, $pass, $options);
                    $connected = true;
                    break;
                } catch (PDOException $e) {
                    $lastException = $e;
                }
            }

            if (!$connected && $lastException) {
                // If standard connection fails, try root with rootpassword
                try {
                    $dsn = "mysql:host=db;port=3306;dbname=sewasathi_db;charset=utf8mb4";
                    self::$instance = new PDO($dsn, 'root', 'rootpassword', [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);
                } catch (PDOException $rootEx) {
                    die("<div style='font-family: sans-serif; padding: 2rem; background: #FEF2F2; border: 1px solid #F87171; border-radius: 8px; max-width: 600px; margin: 3rem auto; color: #991B1B;'>
                        <h2 style='margin-top:0;'>Database Connection Error</h2>
                        <p>Could not connect to SewaSathi MySQL database. Please make sure Docker or MySQL service is running.</p>
                        <p style='font-size:0.875rem; color:#7F1D1D;'><code>" . htmlspecialchars($lastException->getMessage()) . "</code></p>
                        <p style='font-size:0.875rem;'>Run <code>docker compose up -d</code> in your terminal.</p>
                    </div>");
                }
            }
        }

        return self::$instance;
    }
}
