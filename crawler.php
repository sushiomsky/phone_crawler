<?php
include 'config.inc.php';
include 'dbconnect.inc.php';

// It may take a whils to crawl a site ...
set_time_limit(10000);

// Inculde the phpcrawl-mainclass
include("libs/PHPCrawler.class.php");

// Extend the class and override the handleDocumentInfo()-method 
class MyCrawler extends PHPCrawler 
{
  function handleDocumentInfo($DocInfo) 
  {
    // Just detect linebreak for output ("\n" in CLI-mode, otherwise "<br>").
    if (PHP_SAPI == "cli") $lb = "\n";
    else $lb = "<br />";

    // Print the URL and the HTTP-status-Code
    echo "Page requested: ".$DocInfo->url." (".$DocInfo->http_status_code.")".$lb;
    
    // Print the refering URL
    echo "Referer-page: ".$DocInfo->referer_url.$lb;
    
    // Print if the content of the document was be recieved or not
    if ($DocInfo->received == true)
      echo "Content received: ".$DocInfo->bytes_received." bytes".$lb;
    else
      echo "Content not received".$lb; 
    
    // Now you should do something with the content of the actual
    // received page or file ($DocInfo->source), we skip it in this example 
    
    echo $lb;
    
    flush();
  } 
}



do
{
$query  = "SELECT id, url FROM urls WHERE visited = 0 LIMIT 1;";
$result = mysql_query($query)or die(mysql_error());

$row = mysql_fetch_array($result, MYSQL_ASSOC);

//open url
//$content = file_get_contents($row['url']);
//$pattern = '`.*?((http|https)://[\w#$&+,\/:;=?@.-]+)[^\w#$&+,\/:;=?@.-]*?`i';

// of the crawler (see class-reference for more options and details)
// and start the crawling-process.

$crawler = new MyCrawler();

// URL to crawl
$crawler->setURL($row['url']);

// Only receive content of files with content-type "text/html"
$crawler->addContentTypeReceiveRule("#text/html#");

// Ignore links to pictures, dont even request pictures
$crawler->addURLFilterRule("#\.(jpg|jpeg|gif|png)$# i");

// Store and send cookie-data like a browser does
$crawler->enableCookieHandling(true);

// Set the traffic-limit to 1 MB (in bytes,
// for testing we dont want to "suck" the whole site)
$crawler->setTrafficLimit(1000 * 1024);

// Thats enough, now here we go
$crawler->go();

$pattern = '`.*?((http|https)://[\w#$&+,\/:;=?@.-]+)[^\w#$&+,\/:;=?@.-]*?`i';
preg_match_all($pattern,$crawler->content,$matches);
foreach ($matches[1] as $url){
    $query = "INSERT INTO urls(url) VALUES('$url')";
    mysql_query($query)or die(mysql_error());
}

$content = preg_replace('#(( +)|(\t+)|(\-+))#', '', $content);
$pattern = '`.*?((0150|01505|0152|0151|01511|01512|01514|01515|0152|01520|01522|01525|0155|0157|01570|01575|01577|01578|0159|0160|0162|0163|0170|0171|0172|0173|0174|0175|0177|0178|0179)\d{6,8})[^\w#$&+,\/:;=?@.-]*?`i';
preg_match_all($pattern,$crawler->content,$matches);
foreach ($matches[1] as $number){
    echo $number;
    $query = "INSERT INTO mobiles(number) VALUES('$number')";
    mysql_query($query)or die(mysql_error());
}

$query = "UPDATE urls SET visited = 1 WHERE id = ".$row['id'];
mysql_query($query)or die(mysql_error());
}
while ($row['id'] >= 0)
// At the end, after the process is finished, we print a short
// report (see method getProcessReport() for more information)
$report = $crawler->getProcessReport();

if (PHP_SAPI == "cli") $lb = "\n";
else $lb = "<br />";
    
echo "Summary:".$lb;
echo "Links followed: ".$report->links_followed.$lb;
echo "Documents received: ".$report->files_received.$lb;
echo "Bytes received: ".$report->bytes_received." bytes".$lb;
echo "Process runtime: ".$report->process_runtime." sec".$lb; 

?>
