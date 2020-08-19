<?php

require_once "includes/htmlHeader.php";

echo <<<HTML
<div class="list-group list-group-flush">
  <a class="list-group-item list-group-item-action" href="search.php?action=newSearch">search corpus</a>
  <a class="list-group-item list-group-item-action" href="browseCorpus.php">browse corpus</a>
  <a class="list-group-item list-group-item-action" href="slipBrowse.php">browse slips</a>
  <a class="list-group-item list-group-item-action" href="entries.php">browse entries</a>
  <a class="list-group-item list-group-item-action" href="docs.php">technical documentation</a>
</div>
HTML;

require_once "includes/htmlFooter.php";

?>
