<?php

function validate($review)
{
  $errors = [];
  //内容が正しく入力されているかチェック
  if (!strlen($review['contents'])) {
    $errors['contents'] = 'ストレスの内容を入力してください。' . PHP_EOL;
  } elseif (strlen($review['contents']) > 255) {
    $errors['contents'] = 'ストレス内容は255文字以内でお願い致します。' . PHP_EOL;
  }
  //危険度が正しく入力されているかチェック
  if ($review['dangerous'] < 1 || $review['dangerous'] > 5) {
    $errors['dangerous'] = '危険度の記入する整数は１〜５の整数でお願い致します。';
  }
  //日時が正しく入力されているかチェック
  if (!$date = $review['date']) {
    $errors['date'] = '例）2022-02-22のように入力してください。' . PHP_EOL;
  }
  return $errors;
}



function dbConnect()
{
  $link = mysqli_connect('db', 'book_log', 'pass', 'book_log');
  if (!$link) {
    echo 'データベースに接続出来ませんでした。' . PHP_EOL;
    echo 'Debugging error' . mysqli_connect_error() . PHP_EOL;
    exit;
  }
  return $link;
}

$link = dbConnect();




function create_log($link)
{
  $review = [];

  echo 'ストレスログを登録いたします。' . PHP_EOL;
  echo '内容:';
  $review['contents'] = trim(fgets(STDIN));
  echo 'ストレスの強さ（５点満点の整数）:';
  $review['dangerous'] = (int) trim(fgets(STDIN));
  echo '日時(例 2021-02-25):';
  $review['date'] = trim(fgets(STDIN));

  $validated = validate($review);
  if (count($validated) > 0) {
    foreach ($validated as $error) {
      echo $error . PHP_EOL;
    }
    return;
  }
  $sql =  <<<EOT
INSERT INTO create_log (
  contents,
  dangerous,
  date
) VALUES (
  "{$review['contents']}",
  "{$review['dangerous']}",
  "{$review['date']}"
);
EOT;


  $result = mysqli_query($link, $sql);
  if ($result) {
    echo '登録が完了いたしました。' . PHP_EOL . PHP_EOL;
  } else {
    echo 'Debugging error:' . mysqli_error($link) . PHP_EOL;
  }
}

//$log = array();

function listLog($link)
{
  echo 'ストレスログを表示いたします。' . PHP_EOL;

  $sql = 'SELECT id, contents, dangerous, date FROM create_log';

  $results = mysqli_query($link, $sql);
  while ($review = mysqli_fetch_assoc($results)) {
    echo '内容:' . $review['contents'] . PHP_EOL;
    echo 'ストレスの強さ:' . $review['dangerous'] . PHP_EOL;
    echo '日時' . $review['date'] . PHP_EOL;
    echo '-----------' . PHP_EOL;
  }
  mysqli_free_result($results);





  //  foreach ($log as $logs) {
  //    echo '内容:' . $logs['content'] . PHP_EOL;
  //    echo 'ストレスの強さ（５点満点）:' . $logs['dangerous'] . PHP_EOL;
  //    echo '日時(例 2021/2/25):' . $logs['date'] . PHP_EOL;
  //    echo '----------' . PHP_EOL;
  //  }
}





while (true) {
  echo '1:ストレスログを登録します' . PHP_EOL;
  echo '2:ストレスログを表示いたします' . PHP_EOL;
  echo '3:アプリ終了' . PHP_EOL;
  echo '番号を1,2,3から選択してください:';
  $number = trim(fgets(STDIN));

  if ($number === '1') {
    create_log($link);
  } elseif ($number === '2') {
    listLog($link);
  } elseif ($number === '3') {
    mysqli_close($link);
    break;
  }
}
