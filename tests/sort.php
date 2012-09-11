<?php
error_reporting(E_ALL);

require realpath(dirname(__FILE__)).'/../lessc.inc.php';

// sorts the selectors in stylesheet in order to normalize it for comparison

$exe = array_shift($argv); // remove filename

if (!$fname = array_shift($argv)) {
	$fname = "php://stdin";
}

class lesscNormalizedV1 extends lessc {
	public $numberPrecision = 3;

	public function compileValue($value) {
		if ($value[0] == "raw_color") {
			$value = $this->coerceColor($value);
		}

		return parent::compileValue($value);
	}
}

class SortingFormatterV1 extends lessc_formatter_lessjsV1 {
	function sortKey($block) {
		if (!isset($block->sortKey)) {
			sort($block->selectors, SORT_STRING);
			$block->sortKey = implode(",", $block->selectors);
		}

		return $block->sortKey;
	}

	function sortBlock($block) {
		usort($block->children, function($a, $b) {
			$sort = strcmp($this->sortKey($a), $this->sortKey($b));
			if ($sort == 0) {
				// TODO
			}
			return $sort;
		});

	}

	function block($block) {
		$this->sortBlock($block);
		return parent::block($block);
	}

}

$less = new lesscNormalizedV1();
$less->setFormatter(new SortingFormatterV1);
echo $less->parse(file_get_contents($fname));

