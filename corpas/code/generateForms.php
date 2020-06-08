<?php

require_once "includes/htmlHeader.php";

$it = new RecursiveDirectoryIterator(INPUT_FILEPATH);
$words = [];
foreach (new RecursiveIteratorIterator($it) as $nextFile) {
  if ($nextFile->getExtension()=='xml') {
    $xml = simplexml_load_file($nextFile);
    $xml->registerXPathNamespace('dasg','https://dasg.ac.uk/corpus/');
    $status = $xml->xpath("/dasg:text/@status")[0];
    if ($status == 'tagged') {
      foreach ($xml->xpath("//dasg:w") as $nextWord) {
        $form = $nextWord;
        $lemma = (string)$nextWord['lemma'];
        if ($lemma=='') { $lemma = $form; }
        if (strtolower($lemma[0]) == $lemma[0]) { $form = strtolower($form); }
        $pos = (string)$nextWord['pos'];
        $words[] = $form . '|' . $lemma . '|' . $pos;
      }
    }
  }
}
echo '<p>' . count($words) . ' words in total</p>';
usort($words,'Functions::gdSort');

$lexicon = [];
foreach ($words as $nextWord) {
  if ($lexicon[$nextWord]) {
    $lexicon[$nextWord]++;
  }
  else {
    $lexicon[$nextWord] = 1;
  }
}


//$lexicon = array_unique($words);


echo '<p>' . count($lexicon) . ' distinct word forms</p>';
$lexemes = [];
foreach ($lexicon as $nextForm=>$nextCount) {
  $bits = explode('|',$nextForm);
  $lexemes[] = $bits[1];
}
echo '<p>' . count(array_unique($lexemes)) . ' distinct lexemes</p>';

echo <<<HTML
<table class="table">
  <thead>
    <tr><th>form</th><th>headword</th><th>pos</th><th>count</th></tr>
  </thead>
  <tbody>
HTML;

foreach ($lexicon as $nextForm=>$nextCount) {
  $bits = explode('|',$nextForm);
  echo '<tr><td>';
  echo $bits[0] . '</td><td>';
  echo $bits[1] . '</td><td>';
  echo $bits[2] . '</td><td>';
  echo $nextCount . '</td></tr>';
}

echo <<<HTML
  </tbody>
</table>
HTML;


require_once "includes/htmlFooter.php";

?>
