<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../helpers/Normalizador.php';
abstract class Model {
    protected PDO $db;
    public function __construct() { $this->db = Database::getConnection(); }
}
