<?php
	use Dplus\ProcessWire\DplusWire;
	use Dplus\Dpluso\ScreenFormatters\TableScreenMaker;

	function renderNavTree($items, $maxDepth = 3) {
		// if we've been given just one item, convert it to an array of items
		//
		if($items instanceof \Processwire\Page) $items = array($items);
		// if there aren't any items to output, exit now
		if(!count($items)) return;
		// $out is where we store the markup we are creating in this function
		// start our <ul> markup
		echo "<div class='list-group'>";
		// cycle through all the items
		foreach($items as $item) {
			// markup for the list item...
			// if current item is the same as the page being viewed, add a "current" class to it
			// markup for the link
			if($item->id == DplusWire::wire('page')->id) {
				echo "<a href='$item->url' class='list-group-item bg-primary'>$item->title</a>";
			} else {
				echo "<a href='$item->url' class='list-group-item'>$item->title</a>";
			}
			// if the item has children and we're allowed to output tree navigation (maxDepth)
			// then call this same function again for the item's children
			if($item->hasChildren() && $maxDepth) {
				renderNavTree($item->children, $maxDepth-1);
			}
			// close the list item
			//echo "</li>";
		}
		// end our <ul> markup
		echo "</div>";
	}

	function generate_documentationmenu(\Processwire\Page $page, $maxdepth = 4) {
		$page = DplusWire::wire('pages')->get('/documentation/');

		if (DplusWire::wire('page')->id == $page->id) {
			generate_documentationsubmenu($page, 1);
		} else {
			generate_documentationsubmenu($page, $maxdepth);
		}
	}

	function generate_documentationsubmenu($items, $maxdepth) {
		if ($items instanceof \Processwire\Page) $items = array($items);
		// if there aren't any items to output, exit now
		if (!count($items)) return;

		$parents = array();
		foreach(DplusWire::wire('page')->parents as $parent) {
			$parents[] = $parent->id;
		}


		echo "<ul class='list-unstyled docs-nav'>";
		// cycle through all the items
		foreach ($items as $item) {
			// markup for the list item...
			// if current item is the same as the page being viewed, add a "current" class to it
			// markup for the link
			if ($item->dplusfunction == '' || has_dpluspermission(DplusWire::wire('user')->loginid, $item->dplusfunction)) {
				if ($item->id == DplusWire::wire('page')->id) {
					echo "<li class='active'>$item->title</li>";
					$parents[] = $item->id;
				} elseif (in_array($item->id, $parents)) {
					echo "<li class='active'>$item->title</li>";
				} else {
					echo "<li><a href='$item->url'>$item->title</a></li>";
				}
			}


			if (in_array($item->id, $parents)) {
				if ($item->hasChildren() && $maxdepth) {
					generate_documentationsubmenu($item->children, $maxdepth-1);
				}
			} elseif ($maxdepth == 1) {
				if ($item->hasChildren() && $maxdepth) {
					generate_documentationsubmenu($item->children, $maxdepth-1);
				}
			}

			// close the list item
			//echo "</li>";
		}
		// end our <ul> markup
		echo "</ul>";
	}
/* =============================================================
   STRING FUNCTIONS
 ============================================================ */
	function cleanforjs($str) {// DEPRECATED 3/5/2018 MOVED TO Stringer.class.php
		return urlencode(str_replace(' ', '-', str_replace('#', '', $str)));
	}

	function determine_qty(Processwire\WireInput $input, $requestmethod, $itemID) {
		if (DplusWire::wire('modules')->isInstalled('CaseQtyBottle')) {
			$qtypercase = DplusWire::wire('modules')->get('CaseQtyBottle');
			if (!empty($itemID)) {
				$qty = $qtypercase->generate_qtyfromcasebottle($itemID, $input->$requestmethod->text('bottle-qty'), $input->$requestmethod->text('case-qty'));
			}
		} else {
			$qty = !empty($input->$requestmethod->text('qty')) ? $input->$requestmethod->text('qty') : 1;
		}
		return $qty;
	}


/* =============================================================
   URL FUNCTIONS
 ============================================================ */
	function paginate($url, $page, $insertafter, $hash) { // DEPRECATED 3/5/2018 MOVED TO Paginator
		if (strpos($url, 'page') !== false) {
			$regex = "((page)\d{1,3})";
			if ($page > 1) { $replace = "page".$page; } else {$replace = ""; }
			$newurl = preg_replace($regex, $replace, $url);
		} else {
			$insertafter = str_replace('/', '', $insertafter)."/";
			$regex = "(($insertafter))";
			if ($page > 1) { $replace = $insertafter."page".$page."/";} else {$replace = $insertafter; }
			$newurl = preg_replace($regex, $replace, $url);
		}
		return $newurl . $hash;
	 }

/* =============================================================
   ORDERS FUNCTIONS
 ============================================================ */
	/**
	 * Returns HTML Tracking Link
	 * @param  string $servicetype Ex. Fedex, UPS, USPS
	 * @param  string $tracknbr    Tracking Number
	 * @return string             URL
	 */
	function generate_trackingurl($carrier, $tracknbr) {
		$href = false;
		if (strpos(strtolower($servicetype), 'fed') !== false) {
			$href = "https://www.fedex.com/apps/fedextrack/?action=track&trackingnumber=$tracknbr&cntry_code=us";
		} elseif (strpos(strtolower($servicetype), 'ups') !== false) {
			$href = "http://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=$tracknbr&loc=en_us";
		} elseif (strpos(strtolower($servicetype), 'gro') !== false) {
			$href = "http://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=$tracknbr&loc=en_us";
		} elseif (strpos(strtolower($servicetype), 'usps') !== false) {
			$href = "https://tools.usps.com/go/TrackConfirmAction?tLabels=$tracknbr";
		} elseif (strpos(strtolower($servicetype), 'spee') !== false) {
			$href = "http://packages.speedeedelivery.com/index.php?barcodes=$tracknbr";
		}
		return $href;
	}

/* =============================================================
  FILE FUNCTIONS
============================================================ */
	function writedplusfile($data, $filename) {
		$file = '';
		foreach ($data as $key => $value) {
			if (is_string($key)) {
				if (is_string($value)) {
					$file .= $key . "=" . $value . "\n";
				} else {
					$file .= $key . "\n";
				}
			} else {
				$file .= $value . "\n";
			}

		}
		$vard = "/usr/capsys/ecomm/" . $filename;
		$handle = fopen($vard, "w") or die("cant open file");
		fwrite($handle, $file);
		fclose($handle);
	}

/**
 * Writes an array one datem per line into the dplus directory
 * @param  array $data      Array of Lines for the request
 * @param  string $filename What to name File
 * @return void
 */
function write_dplusfile($data, $filename) {
	$file = '';
	foreach ($data as $line) {
		$file .= $line . "\n";
	}
	$vard = "/usr/capsys/ecomm/" . $filename;
	$handle = fopen($vard, "w") or die("cant open file");
	fwrite($handle, $file);
	fclose($handle);
}

	function writedataformultitems($data, $items, $qtys) {
		for ($i = 0; $i < sizeof($items); $i++) {
			$itemID = str_pad(DplusWire::wire('sanitizer')->text($items[$i]), 30, ' ');
			$qty = DplusWire::wire('sanitizer')->text($qtys[$i]);

			if (empty($qty)) {$qty = "1"; }
			$data[] = "ITEMID=".$itemID."QTY=".$qty;
		}
		return $data;
	}

	/**
	 * [convertfiletojson description]
	 * @param  [string] $file [String that contains file location]
	 * @return [string]       [Returns json-encode string]
	 */
	function convertfiletojson($file) {
		$json = file_get_contents($file);
		$json = preg_replace('~[\r\n]+~', '', $json);
		$json = utf8_clean($json);
		return $json;
	}

	function hash_templatefile($filename) {
		$hash = hash_file(DplusWire::wire('config')->userAuthHashType, DplusWire::wire('config')->paths->templates.$filename);
		return DplusWire::wire('config')->urls->templates.$filename.'?v='.$hash;
	}

	function hash_modulefile($filename) {
		$hash = hash_file(DplusWire::wire('config')->userAuthHashType, DplusWire::wire('config')->paths->siteModules.$filename);
		return DplusWire::wire('config')->urls->siteModules.$filename.'?v='.$hash;
	}

	function curl_redir($url) {
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $url,
			CURLOPT_FOLLOWLOCATION => true
		));
		return curl_exec($curl);
	}

 /* =============================================================
   PROCESSWIRE USER FUNCTIONS
 ============================================================ */
	function setup_user($sessionID) {
		$loginrecord = get_loginrecord($sessionID);
		$loginID = $loginrecord['loginid'];
		$user = LogmUser::load($loginID);
		DplusWire::wire('user')->fullname = $loginrecord['loginname'];
		DplusWire::wire('user')->loginid = $loginrecord['loginid'];
		DplusWire::wire('user')->has_customerrestrictions = $loginrecord['restrictcustomers'];
		DplusWire::wire('user')->salespersonid = $loginrecord['salespersonid'];
		DplusWire::wire('user')->mainrole = $user->get_dplusorole();
		DplusWire::wire('user')->addRole($user->get_dplusrole());
	}
