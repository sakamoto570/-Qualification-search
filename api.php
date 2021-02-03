<html>
<head lang="ja">
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<link href="style.css" rel="stylesheet" type="text/css">
<title>取得</title>
</head>
<body >
<h1>資格検索API</h1>

<div align="center">
キーワードから資格情報を検索するAPIです。「A　B」のように空白を開けて入力することでA∩Bの結果を表示をすることもできます。
結果をCSVで保存することもできます。
</div>


<form action="api.php" method = "post"> <!--  テキストボックスの設置-->
  <font color=red>キーワードを入力してください</font>
  <br>
  <input type = "text" name = "comment"><br/>
  <input type="submit" value="検索"/>

</form>

<?php
$api = "";
$visibility = "visibility:hidden";
if(isset($_POST['comment'])) {
  // テキストボックスで選択された値を受け取る
$api = $_POST['comment'];
//取得URLの作成とXMLの取得
$url = "http://webservice.recruit.co.jp/shingaku/license/v1/?key=21018ee984566366&keyword=".$api."&count=60";
$xml= simplexml_load_file($url);
//連想配列へ変換
$xml_array = json_decode(json_encode($xml), true);

$count = $xml_array["results_available"];
//検索結果の表示
$re = $api.':'.$count.'件の表示結果';
echo "<h2> $re</h2>";
for($i=0;$i<$count;$i++){
  $title[$i] = $xml -> license[$i] -> name;
  $overview[$i] = $xml -> license[$i] -> urls -> pc;
  $page[$i] = $xml -> license[$i] -> desc;

  echo "・"."<a href=".$overview[$i].">".$title[$i]."</a>";
  echo"</br>";
  echo "　".$page[$i];
  echo "<p>";

$visibility = "visibility:visible";
}
}

?>

<?php
  if(isset($_POST['api'])) {
    $api = $_POST['api'];

    $url = "http://webservice.recruit.co.jp/shingaku/license/v1/?key=21018ee984566366&keyword=".$api."&count=60";
    $xml= simplexml_load_file($url);

    $xml_array = json_decode(json_encode($xml), true);

    $count = $xml_array["results_available"];

    for($i=0;$i<$count;$i++){
      $title[$i] = $xml -> license[$i] -> name;
      $overview[$i] = $xml -> license[$i] -> urls -> pc;
      $page[$i] = $xml -> license[$i] -> desc;
  }

  $ary[0] = array("タイトル","概要","URL");
  for($i=1;$i<=$count;$i++){
    $ary[$i] =  array($title[$i-1],$page[$i-1],$overview[$i-1]);
  }
  //ファイル書き込み
  $f = fopen($api.".csv","w");


  if ( $f ) {
    fwrite($f, "\xEF\xBB\xBF");
    foreach($ary as $line){    // fputcsv関数でファイルに書き込みます。
      fputcsv($f,$line);
    }
  }

  if (fputcsv($f,$line,",",'"','"') === false) {
    echo "出力に失敗しました";
}else{
  echo "出力に成功しました";
}
  // ファイルを閉じます。
  fclose($f);
  $visibility = "visibility:hidden";
}
?>

<form action = "api.php" method = "post"><!--  koma1.phpへの移動-->
  <input type="submit" value="csvで保存する" style=<?php echo $visibility ?>>
  <input type="hidden" value=<?php echo $api; ?> name="api">
</form>

</body>
</div>
</html>
