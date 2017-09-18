<?php
	if ($input->urlSegment1) {
		switch($input->urlSegment1) {
			case 'ii-sales-order-formatter':
				$page->title = "Item Info - Sales Order Format";
				$include = $config->paths->content."item-information/screen-formatters/ii-sales-order-formatter.php";
				break;
			case 'ii-sales-history-formatter':
				$page->title = "Item Info - Sales History Format";
				$include = $config->paths->content."item-information/screen-formatters/ii-sales-history-formatter.php";
				break;
			case 'ii-purchase-order-formatter':
				$page->title = "Item Info - Purchase Order Format";
				$include = $config->paths->content."item-information/screen-formatters/ii-purchase-order-formatter.php";
				break;
			case 'ii-purchase-history-formatter':
				$page->title = "Item Info - Purchase History Format";
				$include = $config->paths->content."item-information/screen-formatters/ii-purchase-history-formatter.php";
				break;
			case 'ii-quotes-formatter':
				$page->title = "Item Info - Quote Format";
				$include = $config->paths->content."item-information/screen-formatters/ii-quotes-formatter.php";
				break;
			case 'ci-open-invoices-formatter':
				$page->title = "Cust Info - Open Invoices Format";
				$include = $config->paths->content."cust-information/screen-formatters/ci-open-invoices-formatter.php";
				break;
			case 'ci-payment-history-formatter':
				$page->title = "Cust Info - Payment History Format";
				$include = $config->paths->content."cust-information/screen-formatters/ci-payment-history-formatter.php";
				break;
			case 'ci-quotes-formatter':
				$page->title = "Cust Info - Quotes Format";
				$include = $config->paths->content."cust-information/screen-formatters/ci-quotes-formatter.php";
				break;
			case 'ci-sales-order-formatter':
				$page->title = "Cust Info - Sales Order Format";
				$include = $config->paths->content."cust-information/screen-formatters/ci-sales-order-formatter.php";
				break;
			case 'ci-sales-history-formatter':
				$page->title = "Cust Info - Sales History Format";
				$include = $config->paths->content."cust-information/screen-formatters/ci-sales-history-formatter.php";
				break;
		}
		$config->scripts->append($config->urls->templates.'scripts/table-formatter.js');
	} else {
		$formatters = array(
			'II Sales Order' => 'ii-sales-order-formatter',
			'II Sales History' => 'ii-sales-history-formatter',
			'II Purchase Order' => 'ii-purchase-order-formatter',
			'II Purchase History' => 'ii-purchase-history-formatter',
			'II Quotes' => 'ii-quotes-formatter',
			'CI Open Invoices' => 'ci-open-invoices-formatter',
			'CI Payment History' => 'ci-payment-history-formatter',
			'CI Quotes' => 'ci-quotes-formatter',
			'CI Sales Order' => 'ci-sales-order-formatter',
			'CI Sales History' => 'ci-sales-history-formatter'
						   );
	}

?>


<?php include('./_head.php'); ?>
	<div class="jumbotron pagetitle">
		<div class="container">
			<h1><?php echo $page->get('pagetitle|headline|title') ; ?></h1>
		</div>
	</div>
	<div class="container page">
		<?php if ($input->urlSegment1) : ?>

			<?php include $include; ?>

		<?php else : ?>
			<div class="row">
				<div class="col-sm-3">
					<div class="list-group">
				  		<?php foreach ($formatters as $label => $link) : ?>
				  			<a href="<?php echo $link.'/'; ?>" class="list-group-item"><?php echo $label; ?></a>
				  		<?php endforeach; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php $setequalheights = array('.featured-item .panel-body', '.featured-item .panel-header'); ?>
<?php include('./_foot.php'); // include footer markup ?>
