<?php 
include 'config.inc.php';
include 'dbconnect.inc.php';

do
{
$query  = "SELECT id, url FROM urls WHERE visited = 0 LIMIT 1;";
$result = mysql_query($query)or die(mysql_error());

$row = mysql_fetch_array($result, MYSQL_ASSOC);

//open url
$content = file_get_contents($row['url']);
$pattern = '`.*?((http|https)://[\w#$&+,\/:;=?@.-]+)[^\w#$&+,\/:;=?@.-]*?`i';

preg_match_all($pattern,$content,$matches);
foreach ($matches[1] as $url){
    $query = "INSERT INTO urls(url) VALUES('$url')";
    mysql_query($query)or die(mysql_error());
}

$content = preg_replace('#(( +)|(\t+)|(\-+))#', '', $content);
$pattern = '`.*?((0150|01505|0152|0151|01511|01512|01514|01515|0152|01520|01522|01525|0155|0157|01570|01575|01577|01578|0159|0160|0162|0163|0170|0171|0172|0173|0174|0175|0177|0178|0179)\d{6,8})[^\w#$&+,\/:;=?@.-]*?`i';
preg_match_all($pattern,$content,$matches);
foreach ($matches[1] as $number){
    echo $number;
    $query = "INSERT INTO mobiles(number) VALUES('$number')";
    mysql_query($query)or die(mysql_error());
}

$query = "UPDATE urls SET visited = 1 WHERE id = ".$row['id'];
mysql_query($query)or die(mysql_error());
}while ($row['id'] >= 0)
?>
