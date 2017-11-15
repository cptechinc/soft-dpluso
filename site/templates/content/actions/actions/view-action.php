<?php
	// $action is Loaded by Crud Controller
	$actiondisplay = new UserActionDisplay($page->fullURL);
    
    $contactinfo = get_customercontact($action->customerlink, $action->shiptolink, $action->contactlink, false);
	
    if ($action->is_rescheduled()) {
        $rescheduledtask = get_useraction($action->rescheduledlink, true, false);
    }
?>

<div>
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#action" aria-controls="action" role="tab" data-toggle="tab">Action ID: <?= $actionid; ?></a></li>
	</ul>
	<br>
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="action"><?php include $config->paths->content."actions/actions/view/view-action-details.php"; ?></div>
	</div>
</div>
