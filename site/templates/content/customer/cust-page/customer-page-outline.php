<?php
	error_reporting(error_reporting() & ~E_NOTICE);
	$customer = get_customer_info(session_id(), $custID, false);
	$shiptocount = get_shipto_count($user->loginid, $user->hascontactrestrictions, $custID, false);
	$address = $city = $state = $zip = $phone = $contact = "";
?>
<div class="row">
	<div class="col-sm-4 form-group">
		<a href="<?= $config->pages->customer; ?>" class="btn btn-primary"><i class="glyphicon glyphicon-share-alt fliphorizontal"></i> Back To Customer Index</a>
	</div>
	<div class="col-sm-4 form-group">
		<a href="<?= $config->pages->customer.'redir/?action=ci-customer&custID='.$custID; ?>" class="btn btn-primary"><i class="fa fa-address-card" aria-hidden="true"></i> View in CI</a>
	</div>
</div>
<div class="row">
	<div class="col-sm-6">
    	<?php include $config->paths->content.'customer/cust-page/customer-contact-form.php'; ?>
    </div>
    <div class="col-sm-6">
    	<?php include $config->paths->content.'customer/cust-page/customer-shiptos-form.php'; ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
    	<?php include $config->paths->content.'customer/cust-page/notes/notes-panel.php'; ?>
    </div>
    <div class="col-sm-6">
    	<?php include $config->paths->content.'customer/cust-page/tasks/tasks-panel.php'; ?>
    </div>
</div>
<div class="row">
	<div class="col-sm-12">
    	<?php include $config->paths->content.'customer/cust-page/customer-contacts.php'; ?>
    </div>
</div>
<div class="row">
	<div class="col-sm-12">
    	<?php include $config->paths->content.'customer/cust-page/orders/orders-panel.php'; ?>
    </div>
</div>
<div class="row">
	<div class="col-sm-12">
    	<?php include $config->paths->content.'customer/cust-page/quotes/quotes-panel.php'; ?>
    </div>
</div>
