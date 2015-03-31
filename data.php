<?php

header('Content-Type: application/json');

$link = mysqli_connect('localhost', 'root', '', 'example');

$data = array();

//If a page number and amount of rows per page has been provided
if(isset($_GET['page_num']) && isset($_GET['rows_per_page']))
{
  //Fetch a subset of the data
  $sql = "
  SELECT
    `name`
  FROM
    `people`
  ORDER BY
    `name`
  LIMIT
    ".mysqli_real_escape_string($link, ($_GET['page_num']-1) * $_GET['rows_per_page']).",
    ".mysqli_real_escape_string($link, $_GET['rows_per_page']);

  $result = mysqli_query($link, $sql) or die(mysqli_error($link));

  while($row = mysqli_fetch_assoc($result))
  {
    $data[] = $row;
  }
}
//No page number or rows per page variables provided
else
{
  //Fetch a count of all rows available
  $sql = "
  SELECT
    COUNT(`id`) AS `total_rows`
  FROM
    `people`";

  $result = mysqli_query($link, $sql) or die(mysqli_error($link));
  $row = mysqli_fetch_assoc($result);
  $data = $row;
}

echo json_encode($data);