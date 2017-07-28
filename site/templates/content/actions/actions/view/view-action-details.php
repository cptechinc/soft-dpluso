<table class="table table-bordered table-striped">
    <tr>
        <td>Action ID:</td> <td><?= $action->id; ?></td>
    </tr>
    <tr>
        <td>Action Type:</td> <td><?= $action->getactionsubtypedescription();; ?></td>
    </tr>
    <tr>
        <td>Written on:</td> <td><?php echo date('m/d/Y g:i A', strtotime($action->datecreated)); ?></td>
    </tr>
    <tr>
        <td>Written by:</td> <td><?php echo $action->createdby; ?></td>
    </tr>
    <tr>
        <td>Completed:</td> <td><?php echo date('m/d/Y g:i A', strtotime($action->datecompleted));  ?></td>
    </tr>
    <?php if ($action->hascustomerlink) : ?>
        <tr>
            <td>Customer:</td>
            <td><?php echo get_customer_name($action->customerlink); ?> <a href="<?php echo $action->generatecustomerurl(); ?>" target="_blank"><i class="glyphicon glyphicon-share"></i> Go to Customer Page</a></td>
        </tr>
    <?php endif; ?>
    <?php if ($action->hasshiptolink) : ?>
        <tr>
            <td>Ship-to:</td>
            <td><?php echo get_shipto_name($action->customerlink, $action->shiptolink, false); ?> <a href="<?php echo $action->generateshiptourl(); ?>" target="_blank"><i class="glyphicon glyphicon-share"></i> Go to Ship-to Page</a></td>
        </tr>
    <?php endif; ?>
    <?php if ($action->hascontactlink) : ?>
        <tr>
            <td>Action Contact:</td>
            <td><?php echo $action->contactlink; ?> <a href="<?php echo $action->generatecontacturl(); ?>" target="_blank"><i class="glyphicon glyphicon-share"></i> Go to Contact Page</a></td>
        </tr>
    <?php else : ?>
        <tr>
            <td class="text-center h5" colspan="2">
                Who to Contact
            </td>
        </tr>
        <tr>
            <td>Contact: </td>
            <td><?php echo $contactinfo['contact']; ?></td>
        </tr>
    <?php endif; ?>
    <tr>
        <td>Phone:</td>
        <td>
            <a href="tel:<?php echo $contactinfo['cphone']; ?>"><?php echo $contactinfo['cphone']; ?></a> &nbsp; <?php if ($contactinfo['cphext'] != '') {echo ' Ext. '.$contactinfo['cphext'];} ?>
        </td>
    </tr>
    <?php if ($contactinfo['ccellphone'] != '') : ?>
        <tr>
            <td>Cell Phone:</td>
            <td>
                <a href="tel:<?php echo $contactinfo['ccellphone']; ?>"><?php echo $contactinfo['ccellphone']; ?></a>
            </td>
        </tr>
    <?php endif; ?>
    <tr>
        <td>Email:</td>
        <td><a href="mailto:<?php echo $contactinfo['email']; ?>"><?php echo $contactinfo['email']; ?></a></td>
    </tr>
    <?php if ($action->hasorderlink) : ?>
        <tr>
            <td>Sales Order #:</td>
            <td><?php echo $action->salesorderlink; ?></td>
        </tr>
    <?php endif; ?>
    <?php if ($action->hasquotelink) : ?>
        <tr>
            <td>Quote #:</td>
            <td><?php echo $action->quotelink; ?></td>
        </tr>
    <?php endif; ?>
    <tr>
        <td class="control-label">Title</td> <td><?= $action->title; ?></td>
    </tr>
    <tr>
        <td colspan="2"><b>Notes</b><br><?php echo $action->textbody; ?></td>
    </tr>
</table>