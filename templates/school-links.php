<?php
require __DIR__ . '/../vendor/autoload.php';

$dataHelper = new CbsiamMetrics\DataHelper();
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
			<?php if (count($pageScore->scores) > 0): ?>
				<?php foreach($pageScore->scores as $j => $score): ?>
				<div class="page-score">
					<div class="d-flex">
						<div id="heading-score-<?= $i . $j ?>" role="tab">
							<a href="#collapse-score-<?= $i . $j ?>" class="collapsed" data-parent="card-block-<?= $i ?>" data-toggle="collapse" aria-expanded="false" aria-controls="collapse-score-<?= $i. $j ?>">
								Score (out of 100)
							</a>
						</div>
						<div class="score"><?= $score->speedScore ?></div>
						<div>
							<span class="dt"><?= date('n-j-Y h:i:s A', $score->getTimestamp()) ?></span>
							<i data-key="<?= $score->urlKey ?>" class="fa fa-close fa-lg"></i>
						</div>
					</div>
					<div id="collapse-score-<?= $i . $j ?>" class="collapse data-table" role="tabpanel" aria-labelledby="heading-score-<?= $i . $j ?>">
						<?php include "page-scores.php" ?>
					</div>
				</div>
				<?php endforeach;?>
			<?php else: ?>
				<div class="page-score" style="display:none">
					<div class="d-flex">
						<div role="tab">
							<a href="#" class="collapsed" data-toggle="collapse" aria-expanded="false">
								Score (out of 100)
							</a>
						</div>
						<div class="score"></div>
						<div>
							<span class="dt"></span>
							<i class="fa fa-close fa-lg"></i>
						</div>
					</div>
					<div class="collapse" role="tabpanel">
					</div>
				</div>
			<?php endif; ?>
			<button class="btn btn-primary">Get Latest Pagespeed Results</button>
		</div>
	</div>
</div>
<?php endforeach; ?>
</ul>
