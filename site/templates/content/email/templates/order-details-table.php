<?php
    $tb = new Table('class=order');
    $tb->tablesection('thead');
        $tb->tr();
        $tb->th('', 'Item');
        $tb->th('', 'Qty.');
        $tb->th('', 'Price');
        $tb->th('', 'Ext. Price');
    $tb->closetablesection('thead');
    $tb->tablesection('tbody');
        foreach($orderdetails as $detail) {
            $tb->tr();
            $tb->td('', $detail['itemid']);
            $tb->td('', $detail['qtyordered']);
            $tb->td('', formatmoney($detail['price']). ' / '. $detail['uom']);
            $tb->td('class="text-right"', formatmoney($detail['extamt']));
        }
        $tb->tr();
        $tb->td('colspan=2', 'Tax');
        $tb->td('colspan=2', 'TBD');
        $tb->tr();
        $tb->td('colspan=2', 'Subtotal');
        $tb->td('colspan=2', '200.00');
    $tb->closetablesection('tbody');
    $orderdetailtable = $tb->close();
