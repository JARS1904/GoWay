<?php
require_once 'config/conexion_bd.php';
$tables_res = $conexion->query("SHOW TABLES");
$markdown = "# Database Schema\n\n";

while($table_row = $tables_res->fetch_array()) {
    $table = $table_row[0];
    $markdown .= "## Table: `$table`\n";
    $markdown .= "| Field | Type | Null | Key | Default | Extra |\n";
    $markdown .= "|-------|------|------|-----|---------|-------|\n";
    
    $cols_res = $conexion->query("DESCRIBE `$table`");
    while($col = $cols_res->fetch_assoc()) {
        $markdown .= "| {$col['Field']} | {$col['Type']} | {$col['Null']} | {$col['Key']} | " . ($col['Default'] ?? 'NULL') . " | {$col['Extra']} |\n";
    }
    $markdown .= "\n";
}

file_put_contents('db_schema_dump.md', $markdown);
echo "Schema dumped to db_schema_dump.md";
?>
