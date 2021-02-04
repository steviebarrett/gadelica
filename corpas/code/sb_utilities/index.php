<?php
$dirHtml = "<ul>";
$dir = getcwd();
$files = scandir($dir);
$i = 0;
foreach ($files as $file) {
	if ($file == "index.php" || mb_substr($file, 0, 1) == ".") {continue;}
	$i++;
	$dirHtml .= <<<HTML
			<li><a href="#" id="file_{$i}" class="pdfLink" data-url="{$file}" data-index={$i}>{$file}</a></li> 	
HTML;
}
$dirHtml .= "</ul>";
?>

<html>
<head>
	<title>PDF browser</title>
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<script>
    $(function() {
      $('.pdfLink').on('click', function() {
        var url = $(this).attr('data-url');
        var index = $(this).attr('data-index');
        $('#pdfViewer').attr('data', url);
        $('#pdfViewer').attr('data-index', index);
      });

      $('.nextPdf').on('click', function() {
        var index = $('#pdfViewer').attr('data-index');
        var nextIndex = parseInt(index) + 1;
        console.log(nextIndex);
        var url = $('#file_'+nextIndex).attr("data-url");
        console.log(url);
        $('#pdfViewer').attr('data-index', nextIndex);
        $('#pdfViewer').attr('data', url);
      });
    });
	</script>
</head>
<body>

<div class="container" style="width: 960px; margin: 0 auto;">

	<div style="float:right; width: 75%;">
		<object id="pdfViewer" data="TD1.pdf" data-index=1 type="application/pdf" width="600px" height="900px">
			TD1.pdf
		</object>
		<a href="#" class="nextPdf">next >></a>
	</div>

	<div style="width: 25%">
		<?php echo $dirHtml; ?>
	</div>
</div>
</body>
</html>