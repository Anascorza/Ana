<?php
/**
 * includes/db.php — mantido por compatibilidade
 * As funções getDB(), getDBConnection() e executeQuery() estão em config/app.php
 */
if (!defined('DB_HOST')) {
    require_once __DIR__ . '/../config/app.php';
}
