<?php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

//Initialise the database connection
$app['db'] = function()
{
  $host = 'localhost';
  $db_name = 'example';
  $user = 'root';
  $pass = '';

  return new \PDO(
    "mysql:host={$host};dbname={$db_name}",
    $user,
    $pass,
    array(\PDO::ATTR_EMULATE_PREPARES => false));
};

//Set up the route for viewing the HTML frontend
$app->get('/', function() use($app) {
  return file_get_contents(__DIR__.'/../resources/views/index.html');
});

//Set up the route for accessing a page of the result set
$app->get('/data/page/{page_num}/{rows_per_page}', function($page_num, $rows_per_page) use($app) {

  //Put the limit parameters into variables
  $start = ((int)$page_num - 1) * (int)$rows_per_page;
  $total_rows = (int)$rows_per_page;

  //Prepare and execute the query
  $stmt = $app['db']->prepare('
  SELECT
    `name`
  FROM
    `people`
  ORDER BY
    `name`
  LIMIT
    :from, :total_rows');
  $stmt->bindParam('from', $start);
  $stmt->bindParam('total_rows', $total_rows);
  $stmt->execute();

  //Return the rows as JSON
  $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
  return $app->json($result);
});

//Set up the route for accessing the total rows in the result set
$app->get('/data/countrows', function() use($app) {

  //Execute the query
  $stmt = $app['db']->query('
  SELECT
    COUNT(`id`) AS `total_rows`
  FROM
    `people`');

  //Return the rows as JSON
  $result = $stmt->fetch(\PDO::FETCH_ASSOC);
  return $app->json($result);
});

$app->run();