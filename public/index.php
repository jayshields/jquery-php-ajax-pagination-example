<?php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
  'db.options' => array(
    'driver' => 'pdo_mysql',
    'host' => 'localhost',
    'dbname' => 'example',
    'user' => 'root',
    'password' => '',
    'driverOptions' => array(\PDO::ATTR_EMULATE_PREPARES => false)
  ),
));

$app->get('/', function() use($app) {
  return file_get_contents(__DIR__."/../resources/views/index.html");
});

$app->get('/data/page/{page_num}/{rows_per_page}', function($page_num, $rows_per_page) use($app) {
  $result = $app['db']->fetchAll("
  SELECT
    `name`
  FROM
    `people`
  ORDER BY
    `name`
  LIMIT
    ?, ?",
  array(((int)$page_num - 1) * (int)$rows_per_page, (int)$rows_per_page));

  return $app->json($result);
});

$app->get('/data/countrows', function() use($app) {
  $result = $app['db']->fetchAssoc("
  SELECT
    COUNT(`id`) AS `total_rows`
  FROM
    `people`");

  return $app->json($result);
});

$app->run();