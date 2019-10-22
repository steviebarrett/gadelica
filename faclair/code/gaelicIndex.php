<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>Stòras-Brì</title>
  </head>
  <body style="padding-top: 20px;">
    <div class="container-fluid">
<?php
$query = <<<SPQR
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX : <http://faclair.ac.uk/meta/>
SELECT ?id ?gd ?en
WHERE
{
  ?id rdfs:label ?gd .
  GRAPH ?g {
    ?id :sense ?en .
  }
}
SPQR;
$url = 'http://daerg.arts.gla.ac.uk:8080/fuseki/Faclair?output=json&query=' . urlencode($query);
$results = json_decode(file_get_contents($url),false)->results->bindings;
$hws = [];
foreach ($results as $nextResult) {
  $pair = array($nextResult->gd->value, $nextResult->id->value);
  $hws[] = implode("|", $pair);
}
$hws = array_unique($hws);
usort($hws,'gdCompare');
function gdCompare($s, $t) {
  $accentedvowels = array('à','è','ì','ò','ù','À','È','Ì','Ò','Ù');
  $unaccentedvowels = array('a','e','i','o','u','A','E','I','O','U');
  $str3 = str_replace($accentedvowels,$unaccentedvowels,$s);
  $str4 = str_replace($accentedvowels,$unaccentedvowels,$t);
  return strcasecmp($str3,$str4);
}
?>
      <table class="table table-hover">
        <tbody>
<?php
foreach ($hws as $nextHw) {
  $pair = explode("|", $nextHw);
  echo '<tr><td>' . $pair[0] . '</td><td><small>';
  $enstr = '';
  foreach ($results as $nextResult) {
    if ($nextResult->id->value == $pair[1]) {
      $enstr .= $nextResult->en->value . ', ';
    }
  }
  echo trim($enstr,' ,') . '</small></td></tr>';
}
?>
        </tbody>
      </table>
      <nav class="navbar navbar-dark bg-primary fixed-bottom navbar-expand-lg">
        <a class="navbar-brand" href="index.php">Stòras-Brì</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
          <div class="navbar-nav">
             <a class="nav-item nav-link" href="about.html" data-toggle="tooltip" title="About this site">fios</a>
             <a class="nav-item nav-link" href="random.php" data-toggle="tooltip" title="View random entry">iongnadh</a>
          </div>
        </div>
      </nav>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>
