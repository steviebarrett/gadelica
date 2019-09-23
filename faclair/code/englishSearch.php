<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<?php
$result = '';
if (isset($_GET['searchTerm'])) { // check for search term in URL
  $result = $_GET['searchTerm'];
  echo '<title>Co-àite Briathrachais – ' . $result . '</title>';
}
else echo '<title>Co-àite Briathrachais</title>';
?>
  </head>
  <body style="padding-top: 20px;">
    <div class="container-fluid">
      <form action="englishSearch.php" method="get" autocomplete="off"> <!-- Search box -->
        <div class="form-group">
          <div class="input-group">
<?php
echo '<input type="text" class="form-control active" name="searchTerm"  data-toggle="tooltip" title="Enter English word here" ';
if ($result != '') { // display search term inside search box
  echo 'value="' . $result . '"/>';
}
else { echo 'autofocus="autofocus"/>'; }
?>
            <div class="input-group-append">
              <button class="btn btn-primary" type="submit" data-toggle="tooltip" title="Click to get Gaelic translation">Cuir Gàidhlig air</button>
            </div>
          </div>
        </div>
      </form>
<?php
if ($result != '') {
  $query = <<<SPQR
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX : <http://faclair.ac.uk/meta/>
SELECT DISTINCT ?gd ?id ?en ?lex
WHERE
{
  GRAPH ?lexicon {
    ?id rdfs:label ?gd .
    ?id :sense ?en .
    FILTER regex(?en, "^{$_GET['searchTerm']}") .
  }
  ?lexicon rdfs:label ?lex .
}
SPQR;
  $url = 'http://daerg.arts.gla.ac.uk:8080/fuseki/co-aite?output=json&query=' . urlencode($query);
  echo file_get_contents($url);
  $results = json_decode(file_get_contents($url),false)->results->bindings;
  echo '<div class="list-group list-group-flush">'; // display list of search results
  foreach ($results as $result) {
    echo '<a href="viewEntry.php?id=' . urlencode($result->id->value) . '" class="list-group-item list-group-item-action">' . $result->gd->value . ' <small>' . $result->en->value . ' (' . $result->lex->value . ')</small></a>';
  }
  echo '</div>';
}
else {
  echo 'Cuiribh briathar Beurla dhan bhocsa-luirg seo.';
}
?>
      <nav class="navbar navbar-dark bg-primary fixed-bottom navbar-expand-lg">
        <a class="navbar-brand" href="englishSearch.php">Co-àite Briathrachais</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
          <div class="navbar-nav">
             <a class="nav-item nav-link" href="about.html" data-toggle="tooltip" title="About this site">Mu dheidhinn</a>
          </div>
        </div>
      </nav>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>
