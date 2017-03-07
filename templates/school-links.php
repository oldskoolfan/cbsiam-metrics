<?php
require __DIR__ . '/../vendor/autoload.php';

$dataHelper = new CbsiamMetrics\DataHelper();
$viewHelper = new CbsiamMetrics\ViewHelper();
$pageScores = $dataHelper->getScoresForLinks();
?>
<ul id="accordion" role="tablist">
<?php foreach($pageScores as $i => $pageScore): ?>
<div data-url="<?= $pageScore->url ?>" class="card">
	<h5 id="heading<?= $i ?>" class="card-header" role="tab">
		<a href="#collapse<?= $i ?>" class="collapsed" data-parent="accordion" data-toggle="collapse" aria-expanded="false" aria-controls="collapse<?= $i ?>">
			<?= $pageScore->url ?>
		</a>
	</h5>
	<div id="collapse<?= $i ?>" class="collapse" role="tabpanel" aria-labelledby="heading<?= $i ?>">
		<div id="card-block-<?= $i ?>" class="card-block" role="tablist" data-id="<?= $i ?>" data-scores="<?= count($pageScore->scores) ?>">
			<?php
				if (count($pageScore->scores) > 0) {
					foreach ($pageScore->scores as $j => $score) {
						$scores = [];
						if (count($score->data) > 0) {
							foreach($score->data as $key => $val) {
								array_push($scores, [
									'key' => $key,
									'val' => $val,
								]);
							}
						}
						echo $viewHelper->render('page-score', [
							'rowId' => $i . $j,
							'cardId' => $i,
							'speedScore' => $score->speedScore,
							'dateTime' => date('n-j-Y h:i:s A', $score->getTimestamp()),
							'scores' => $scores,
						]);
					}
				}
			?>
			<button class="btn btn-primary">Get Latest Pagespeed Results</button>
		</div>
	</div>
</div>
<?php endforeach; ?>
</ul>
