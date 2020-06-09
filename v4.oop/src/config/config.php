<?php

if (!isset($magic) or !defined($magic)) {
  http_response_code(404);
  die("404 Not Found");
};


if (!_LOCAL_APP) {
  define("DB_USER", MYSQL_USER);
  define("DB_PASS", MYSQL_PASSWORD);
  define("DB_HOST", MYSQL_HOST);
  define("DB_NAME", MYSQL_DBNAME);
} else {
  define("DB_USER", "root");
  define("DB_PASS", "");
  define("DB_HOST", "localhost");
  define("DB_NAME", "t308");
}
