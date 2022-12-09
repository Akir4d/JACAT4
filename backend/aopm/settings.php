<?php
$reqiredModules = [
    "intl",
    "curl",
    "json",
    "mbstring",
    // "mysqli",
    // "oci8_12c",
    // "oci8_19",
    // "odbc",
    // "pdo_firebird",
    // "pdo_mysql",
    // "pdo_oci",
    // "pdo_odbc",
    // "pdo_pgsql",
    // "pdo_sqlite",
    // "pdo_sqlsrv",
    // "pgsql",
    // "sqlite3",
    //"sqlsrv"
];

$nameConvertions = [
    "MySQLi" => ["mysqli"],
    "OCI8" => ["oci8_12c", "oci8_19", "pdo_oci"],
    "Postgre" => ["pgsql", "pdo_pgsql"],
    "SQLite3" => ["sqlite3", "pdo_sqlite"],
    "SQLSRV" => ["sqlsrv","pdo_sqlsrv"]
];

