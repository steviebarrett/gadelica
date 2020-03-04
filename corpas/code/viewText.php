<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>Corpas na Gàidhlig</title>
  </head>
  <body>
    <div class="container-fluid">

<?php
$text = $_GET['ref'];
echo '<h1>' . $text . '</h1>';
$dir = '../xml/';
$files = scandir($dir);
foreach ($files as $nextFile) {
  if (substr($nextFile,  strlen($nextFile)-4  ) == '.xml') {
    // find text element within files (recursively)



  }
}




/*
$text = new SimpleXMLElement("../xml/" . $_GET["t"] . ".xml", LIBXML_XINCLUDE, true);
//$text->registerXPathNamespace('dasg', 'https://dasg.ac.uk/corpus/');
//$subtext = $text->xpath('//dasg:text[@ref="' . $_GET["ref"] . '"]')[0];
$xsl = new DOMDocument;
$xsl->load('corpus.xsl');
$proc = new XSLTProcessor;
$proc->importStyleSheet($xsl);
echo $proc->transformToXML($text);
*/
?>


    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  </body>
</html>
