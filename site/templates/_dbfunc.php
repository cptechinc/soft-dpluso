<?php

/* =============================================================
	LOGIN FUNCTIONS
============================================================ */
	function is_valid_login($sessionID) {
		$sql = wire('database')->prepare("SELECT IF(validlogin = 'Y',1,0) FROM logperm WHERE sessionid = :sessionID LIMIT 1");
		$switching = array(':sessionID' => $sessionID);
		$sql->execute($switching);
		return $sql->fetchColumn();
	}

	function get_login_error_msg($sessionID) {
		$sql = wire('database')->prepare("SELECT errormsg FROM logperm WHERE sessionid = :sessionID");
		$switching = array(':sessionID' => $sessionID);
		$sql->execute($switching);
		return $sql->fetchColumn();
	}

	function get_login_name($sessionID) {
		$sql = wire('database')->prepare("SELECT salespername FROM logperm WHERE sessionid = :sessionID");
		$switching = array(':sessionID' => $sessionID);
		$sql->execute($switching);
		return $sql->fetchColumn();
	}

	function get_login_id($sessionID) {
		$sql = wire('database')->prepare("SELECT loginid FROM logperm WHERE sessionid = :sessionID");
		$switching = array(':sessionID' => $sessionID);
		$sql->execute($switching);
		return $sql->fetchColumn();
	}

	function does_user_have_contact_restrictions($sessionID) {
		$sql = wire('database')->prepare("SELECT IF(restrictaccess = 'Y',1,0) FROM logperm WHERE sessionid = :sessionID");
		$switching = array(':sessionID' => $sessionID);
		$sql->execute($switching);
		return $sql->fetchColumn();
	}

	function get_login_record($sessionID) {
		$sql = wire('database')->prepare("SELECT IF(restrictaccess = 'Y',1,0) as restrictedaccess, logperm.* FROM logperm WHERE sessionid = :sessionID");
		$switching = array(':sessionID' => $sessionID);
		$sql->execute($switching);
		return $sql->fetch(PDO::FETCH_ASSOC);
	}
/* =============================================================
	CUSTOMER FUNCTIONS
============================================================ */
	function has_access_to_customer($loginID, $restrictions, $custID, $debug) {
		$SHARED_ACCOUNTS = wire('config')->sharedaccounts;
		if ($restrictions) {
			$sql = wire('database')->prepare("SELECT COUNT(*) FROM (SELECT * FROM custindex WHERE custid = :custID AND source = 'CS') t WHERE splogin1 IN (:loginID, :shared) OR splogin2 = :loginID OR splogin3 = :loginID");
			$switching = array(':custID' => $custID, ':loginID' => $loginID, ':shared' => $SHARED_ACCOUNTS, ':loginID' => $loginID, ':loginID' => $loginID);
			$withquotes = array(true, true, true, true, true);
			if ($debug) {
				return returnsqlquery($sql->queryString, $switching, $withquotes);
			} else {
				$sql->execute($switching);
				return $sql->fetchColumn();
			}
		} else {
			return 1;
		}
	}

	function has_access_to_customer_shipto($loginID, $restrictions, $custID, $shipID, $debug) {
		$SHARED_ACCOUNTS = wire('config')->sharedaccounts;
		if ($restrictions) {
			$sql = wire('database')->prepare("SELECT COUNT(*) FROM (SELECT * FROM custindex WHERE custid = :custID AND shiptoid = :shipID AND source = 'CS') t WHERE splogin1 IN (:loginID, :shared) OR splogin2 = :loginID OR splogin3 = :loginID");
			$switching = array(':custID' => $custID, ':shipID' => $shipID, ':loginID' => $loginID, ':shared' => $SHARED_ACCOUNTS, ':loginID' => $loginID, ':loginID' => $loginID);
			$withquotes = array(true, true, true, true, true);
			if ($debug) {
				return returnsqlquery($sql->queryString, $switching, $withquotes);
			} else {
				$sql->execute($switching);
				return $sql->fetchColumn();
			}
		} else {
			return 1;
		}
	}

	function get_customer_name($custID) {
		$sql = wire('database')->prepare("SELECT name FROM custindex WHERE custid = :custID LIMIT 1");
		$switching = array(':custID' => $custID);
		$sql->execute($switching);
		return $sql->fetchColumn();
	}

	function get_shipto_name($custID, $shipID, $debug) {
		$sql = wire('database')->prepare("SELECT name FROM custindex WHERE custid = :custID AND shiptoid = :shipID LIMIT 1");
		$switching = array(':custID' => $custID, ':shipID' => $shipID); $withquotes = array(true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchColumn();
		}

	}

	function get_customer_info($sessionID, $custID, $debug) {
		$sql = wire('database')->prepare("SELECT custindex.*, customer.dateentered FROM custindex JOIN customer ON custindex.custid = customer.custid WHERE custindex.custid = :custID AND customer.sessionid = :sessionID LIMIT 1");
		$switching = array(':sessionID' => $sessionID, ':custID' => $custID); $withquotes = array(true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetch(PDO::FETCH_ASSOC);
		}
	}

	function get_first_custindex($debug) {
		$sql = wire('database')->prepare("SELECT * FROM custindex LIMIT 1");
		$switching = array(); $withquotes = array();
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute();
			return $sql->fetch(PDO::FETCH_ASSOC);
		}
	}

	function get_shipto_count($loginID, $restrictions, $custID, $debug) {
		$SHARED_ACCOUNTS = wire('config')->sharedaccounts;
		if ($restrictions) {
			$sql = wire('database')->prepare("SELECT COUNT(*) FROM view_distinct_cust_shiptos WHERE recno IN (SELECT recno FROM view_distinct_cust_shiptos WHERE splogin1 IN (:loginID, :shared)  OR splogin2 = :loginID  OR splogin3 = :loginID) AND custid = :custID");
			$switching = array(':loginID' => $loginID, ':shared' => $SHARED_ACCOUNTS, ':loginID' => $loginID, ':loginID' => $loginID, ':custID' => $custID);
			$withquotes = array(true, true, true, true);
		} else {
			$sql = wire('database')->prepare("SELECT COUNT(*) FROM view_distinct_cust_shiptos WHERE custid = :custID");
			$switching = array(':custID' => $custID); $withquotes = array(true);
		}
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchColumn();
		}
	}

	function get_shipto_info($custID, $shipID, $debug) {
		$sql = wire('database')->prepare("SELECT * FROM view_distinct_cust_shiptos WHERE custid = :custID AND shiptoid = :shipID");
		$switching = array(':custID' => $custID, ':shipID' => $shipID); $withquotes = array(true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetch(PDO::FETCH_ASSOC);
		}
	}

	function getcustomershiptos($custID, $loginID, $restrictions, $debug) {
		$SHARED_ACCOUNTS = wire('config')->sharedaccounts;
		if ($restrictions) {
			$sql = wire('database')->prepare("SELECT * FROM view_distinct_cust_shiptos WHERE custid = :custID AND recno IN (SELECT recno FROM view_distinct_cust_shiptos WHERE splogin1 IN (:loginID, :shared) OR splogin2 = :loginID  OR splogin3 = :loginID)");
			$switching = array(':custID' => $custID, ':loginID' => $loginID, ':shared' => $SHARED_ACCOUNTS, ':loginID' => $loginID, ':loginID' => $loginID);
			$withquotes = array(true, true, true, true);
		} else {
			$sql = wire('database')->prepare("SELECT * FROM view_distinct_cust_shiptos WHERE custid = :custID");
			$switching = array(':custID' => $custID); $withquotes = array(true);
		}

		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	function get_allowed_shiptos($custID, $loginID, $restrictions, $debug) { //DEPRECATE
		if ($restrictions) {
			$sql = wire('database')->prepare("SELECT * FROM custindex WHERE custid = :custID AND shiptoid != '' AND recno IN (SELECT recno FROM custindex WHERE (splogin1 IN (:loginID, :sharedaccounts) OR splogin2 = :loginID OR splogin3 = :loginID)) GROUP BY shiptoid");
			$switching = array(':custID' => $custID, ':loginID' => $loginID, ':sharedaccounts' => wire('config')->sharedaccounts, ':loginID' => $loginID, ':loginID' => $loginID);

			$withquotes = array(true, true, true, true, true);
		} else {
			$sql = wire('database')->prepare("SELECT * FROM custindex WHERE custid = :custID AND shiptoid != '' GROUP BY shiptoid");
			$switching = array(':custID' => $custID);
			$withquotes = array(true);
		}
		$sql->execute($switching);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	function get_contacts($loginID, $restrictions, $custID, $debug) {
		$SHARED_ACCOUNTS = wire('config')->sharedaccounts;
		if ($restrictions) {
			$sql = wire('database')->prepare("SELECT * FROM custindex WHERE custid = :custID AND recno IN (SELECT recno FROM custindex WHERE splogin1 IN (:loginID, :shared) OR splogin2 = :loginID  OR splogin3 = :loginID)");
			$switching = array(':custID' => $custID, ':loginID' => $loginID, ':shared' => $SHARED_ACCOUNTS, ':loginID' => $loginID, ':loginID' => $loginID);
			$withquotes = array(true, true, true, true);
		} else {
			$sql = wire('database')->prepare("SELECT * FROM custindex WHERE custid = :custID");
			$switching = array(':custID' => $custID); $withquotes = array(true);
		}

		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			$sql->setFetchMode(PDO::FETCH_CLASS, 'Contact');
			return $sql->fetchAll();
		}
	}

	function does_user_have_access_contact($loginID, $restrictions, $custID, $shipID, $contact, $debug) {
		$SHARED_ACCOUNTS = wire('config')->sharedaccounts;
		if ($restrictions) {
			$sql = wire('database')->prepare("SELECT COUNT(*) FROM custindex WHERE (splogin1 IN (:loginID, :shared) OR splogin2 = :loginID OR splogin3 = :loginID) AND custid = :custID AND shiptoid = :shipID AND contact = :contact");
			$switching = array(':loginID' => $loginID, ':shared' => $SHARED_ACCOUNTS, ':custID' => $custID, ':shipID' => $shipID, ':contact' => $contact);
			$withquotes = array(true, true, true, true, true);
		} else {
			$sql = wire('database')->prepare("SELECT COUNT(*) FROM custindex WHERE custid = :custID AND shiptoid = :shipID AND contact = :contact");
			$switching = array(':custID' => $custID, ':shipID' => $shipID, ':contact' => $contact);
			$withquotes = array(true, true, true);
		}
		$sql->execute($switching);
		if ($debug) { return returnsqlquery($sql->queryString, $switching, $withquotes); } else { if ($sql->fetchColumn() > 0){return true;} else {return false; } }
	}

	function getcustcontact($custID, $shipID, $contactID, $debug) {
		if (!empty($contactID)) {
			$sql = wire('database')->prepare("SELECT * FROM custindex WHERE custid = :custID AND shiptoid = :shipID AND contact = :contactid LIMIT 1");
			$switching = array(':custID' => $custID, ':shipID' => $shipID, ':contactid' => $contactID);
			$withquotes = array(true, true, true);
		} else {
			$sql = wire('database')->prepare("SELECT * FROM custindex WHERE custid = :custID AND shiptoid = :shipID LIMIT 1");
			$switching = array(':custID' => $custID, ':shipID' => $shipID);
			$withquotes = array(true, true);
		}

		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetch(PDO::FETCH_ASSOC);
		}
	}

	function getshiptocontact($custID, $shipID, $debug) {
		$sql = wire('database')->prepare("SELECT * FROM custindex WHERE custid = :custID AND shiptoid = :shipID LIMIT 1");
		$switching = array(':custID' => $custID, ':shipID' => $shipID);
		$withquotes = array(true, true, true);
		$sql->execute($switching);
		if ($debug) { return returnsqlquery($sql->queryString, $switching, $withquotes); } else { return $sql->fetch(PDO::FETCH_ASSOC); }
	}

	function edit_customercontact($custID, $shipID, $contactID, $contact, $debug) {
		$originalcontact = getcustcontact($custID, $shipID, $contactID, false);
		$query = returnpreppedquery($originalcontact, $contact);
		$sql = wire('database')->prepare("UPDATE custindex SET ".$query['setstatement']." WHERE custid = :custID AND shiptoid = :shipID AND contact = :contactID");
		$query['switching'][':custID'] = $custID; $query['switching'][':shipID'] = $shipID; $query['switching'][':contactID'] = $contactID;
		$query['withquotes'][] = true; $query['withquotes'][]= true; $query['withquotes'][] = true;
		if ($debug) {
			return returnsqlquery($sql->queryString, $query['switching'], $query['withquotes']);
		} else {
			if ($query['changecount'] > 0) {
				$sql->execute($query['switching']);
			}
			return returnsqlquery($sql->queryString, $query['switching'], $query['withquotes']);
		}
	}

/* =============================================================
	CUST INDEX FUNCTIONS
============================================================ */
	function get_distinct_custindex_paged($loginID, $limit = 10, $page = 1, $restrictions, $debug) {
		$SHARED_ACCOUNTS = wire('config')->sharedaccounts;
		$limiting = returnlimitstatement($limit, $page);

		if ($restrictions) {
			$sql = wire('database')->prepare("SELECT * FROM custindex WHERE custid IN (SELECT DISTINCT(custid) FROM custperm WHERE loginid = :loginID OR loginid = :shared) GROUP BY custid ".$limiting);
			$switching = array(':loginID' => $loginID, ':shared' => $SHARED_ACCOUNTS, ':loginID' => $loginID, ':loginID' => $loginID);
			$withquotes = array(true, true, true, true);
		} else {
			$sql = wire('database')->prepare("SELECT * FROM custindex WHERE shiptoid = '' GROUP BY custid " . $limiting);
			$switching = array();
			$withquotes = array();
		}

		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			$sql->setFetchMode(\PDO::FETCH_CLASS, 'Contact');
			return $sql->fetchAll();
		}
	}

	function get_distinct_custindex_count($loginID, $restrictions, $debug) {
		$SHARED_ACCOUNTS = wire('config')->sharedaccounts;
		if ($restrictions) {
			$sql = wire('database')->prepare("SELECT COUNT(*) FROM custindex WHERE custid IN (SELECT DISTINCT(custid) FROM custperm WHERE loginid = :loginID OR loginid = :shared)");
			$switching = array(':loginID' => $loginID, ':shared' => $SHARED_ACCOUNTS, ':loginID' => $loginID, ':loginID' => $loginID);
			$withquotes = array(true, true, true, true);
		} else {
			$sql = wire('database')->prepare("SELECT COUNT(*) FROM custindex WHERE shiptoid = ''");
			$switching = array(); $withquotes = array();
		}

		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchColumn();
		}
	}

	function get_custindex_keyword_paged($loginID, $limit = 10, $page = 1, $restrictions, $keyword, $debug) {
		$SHARED_ACCOUNTS = wire('config')->sharedaccounts;
		$limiting = returnlimitstatement($limit, $page);
		$search = '%'.str_replace(' ', '%',$keyword).'%';

		if ($restrictions) {
			$sql = wire('database')->prepare("SELECT * FROM custindex WHERE (custid, shiptoid) IN (SELECT custid, shiptoid FROM custperm WHERE loginid = :loginID OR loginid = :shared) AND UCASE(CONCAT(custid, ' ', name, ' ', shiptoid, ' ', addr1, ' ', ccity, ' ', cst, ' ', czip, ' ', cphone, ' ', contact, ' ', source, ' ', cphext)) LIKE UCASE(:search) ".$limiting);
			$switching = array(':loginID' => $loginID, ':shared' => $SHARED_ACCOUNTS, ':loginID' => $loginID, ':search' => $search);
			$withquotes = array(true, true, true, true);
		} else {
			$sql = wire('database')->prepare("SELECT * FROM custindex WHERE UCASE(CONCAT(custid, ' ', name, ' ', shiptoid, ' ', addr1, ' ', ccity, ' ', cst, ' ', czip, ' ', cphone, ' ', contact, ' ', source, ' ', cphext)) LIKE UCASE(:search) " . $limiting);
			$switching = array(':search' => $search); $withquotes = array(true);
		}

		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			$sql->setFetchMode(PDO::FETCH_CLASS, 'Contact');
			return $sql->fetchAll();
		}
	}

	function search_custindex_keyword_paged($loginID, $limit = 10, $page = 1, $restrictions, $keyword, $debug) {
		$SHARED_ACCOUNTS = wire('config')->sharedaccounts;
		$limiting = returnlimitstatement($limit, $page);
		$search = '%'.str_replace(' ', '%',$keyword).'%';

		if ($restrictions) {
			$sql = wire('database')->prepare("SELECT * FROM custindex WHERE (custid, shiptoid) IN (SELECT custid, shiptoid FROM custperm WHERE loginid = :loginID OR loginid = :shared) AND UCASE(CONCAT(custid, ' ', name, ' ', shiptoid, ' ', addr1, ' ', ccity, ' ', cst, ' ', czip, ' ', cphone, ' ', contact, ' ', source, ' ', cphext)) LIKE UCASE(:search) ".$limiting);
			$switching = array(':loginID' => $loginID, ':shared' => $SHARED_ACCOUNTS, ':loginID' => $loginID, ':search' => $search);
			$withquotes = array(true, true, true, true);
		} else {
			$sql = wire('database')->prepare("SELECT * FROM custindex WHERE UCASE(CONCAT(custid, ' ', name, ' ', shiptoid, ' ', addr1, ' ', ccity, ' ', cst, ' ', czip, ' ', cphone, ' ', contact, ' ', source, ' ', cphext)) LIKE UCASE(:search) " . $limiting);
			$switching = array(':search' => $search); $withquotes = array(true);
		}

		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			$sql->setFetchMode(PDO::FETCH_CLASS, 'Contact');
			return $sql->fetchAll();
		}
	}

	function get_custindex_keyword_count($loginID, $restrictions, $keyword, $debug) {
		$SHARED_ACCOUNTS = wire('config')->sharedaccounts;
		$search = '%'.str_replace(' ', '%',$keyword).'%';

		if ($restrictions) {
			$sql = wire('database')->prepare("SELECT COUNT(*) FROM custindex WHERE (custid, shiptoid) IN (SELECT custid, shiptoid FROM custperm WHERE loginid = :loginID OR loginid = :shared) AND UCASE(CONCAT(custid, ' ', name, ' ', shiptoid, ' ', addr1, ' ', ccity, ' ', cst, ' ', czip, ' ', cphone, ' ', contact, ' ', source, ' ', cphext)) LIKE UCASE(:search)");
			$switching = array(':loginID' => $loginID, ':shared' => $SHARED_ACCOUNTS, ':search' => $search);
			$withquotes = array(true, true, true, true);
		} else {
			$sql = wire('database')->prepare("SELECT COUNT(*) FROM custindex WHERE UCASE(CONCAT(custid, ' ', name, ' ', shiptoid, ' ', addr1, ' ', ccity, ' ', cst, ' ', czip, ' ', cphone, ' ', contact, ' ', source, ' ', cphext)) LIKE UCASE(:search)");
			$switching = array(':search' => $search); $withquotes = array(true);
		}

		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchColumn();
		}
	}

	function get_top_25_selling_customers($loginID, $restrictions, $debug) {
		$SHARED_ACCOUNTS = wire('config')->sharedaccounts;
		if ($restrictions) {
			$sql = wire('database')->prepare("SELECT custid, name, amountsold, timesold, lastsaledate FROM custindex WHERE splogin1 IN (:loginID, :sharedaccounts) OR splogin2 = :loginID OR splogin3 = :loginID GROUP BY custid ORDER BY CAST(amountsold as Decimal(10,8)) DESC LIMIT 25");
			$switching = array(':loginID' => $loginID, ':sharedaccounts' => $SHARED_ACCOUNTS); $withquotes = array(true);
		} else {
			$sql = wire('database')->prepare("SELECT custid, name, amountsold, timesold, lastsaledate FROM custindex GROUP BY custid ORDER BY CAST(amountsold as Decimal(10,8)) DESC LIMIT 25 ");
			$switching = array(); $withquotes = array();
		}

		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	function insertnewcustomer($customer, $debug) {
		$query = returninsertlinks($customer);
		$sql = wire('database')->prepare("INSERT INTO custindex (".$query['columnlist'].") VALUES (".$query['valuelist'].")");
		$switching = $query['switching']; $withquotes = $query['withquotes'];
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		}
	}

	function getmaxcustindexrecnbr() {
		$sql = wire('database')->prepare("SELECT MAX(recno) FROM custindex");
		$sql->execute();
		return $sql->fetchColumn();
	}

	function changecustindexcustid($originalcustID, $newcustID) {
		$sql = wire('database')->prepare("UPDATE custindex SET custid = :newcustid WHERE custid = :originalcustid");
		$switching = array(':newcustid' => $newcustID, ':originalcustid' => $originalcustID); $withquotes = array(true, true);
		$sql->execute($switching);
		return returnsqlquery($sql->queryString, $switching, $withquotes);
	}

/* =============================================================
	ORDERS FUNCTIONS
============================================================ */
	function get_salesrep_order_count($sessionID, $debug) {
		$sql = wire('database')->prepare("SELECT IF(COUNT(DISTINCT(custid)) > 1,COUNT(*),0) as count FROM ordrhed WHERE sessionid = :sessionID AND type = :type");
		$switching = array(':sessionID' => $sessionID, ':type' => 'O'); $withquotes = array(true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchColumn();
		}
	}

	function get_salesrep_orders_orderdate($sessionID, $limit = 10, $page = 1, $sortrule, $debug) {
		$limiting = returnlimitstatement($limit, $page);
		$sql = wire('database')->prepare("SELECT orderdate, STR_TO_DATE(orderdate, '%m/%d/%Y') as dateoforder, orderno, custpo, shiptoid, sname, saddress, saddress2, scity, sst, szip, havenote,
					status, havetrk, havedoc, odrsubtot, odrtax, odrfrt, odrmis, odrtotal, error, errormsg, shipdate, custid, custname, invdate, editord FROM ordrhed
					WHERE sessionid = :sessionID AND type = :type ORDER BY dateoforder $sortrule " . $limiting);
		$switching = array(':sessionID' => $sessionID, ':type' => 'O'); $withquotes = array(true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	function get_salesrep_orders_orderby($sessionID, $limit = 10, $page = 1, $sortrule, $orderby, $debug) {
		$limiting = returnlimitstatement($limit, $page);
		$sql = wire('database')->prepare("SELECT ordrhed.*, CAST(odrsubtot AS DECIMAL(8,2)) AS subtotal FROM ordrhed WHERE sessionid = :sessionID  AND type = :type ORDER BY $orderby $sortrule " . $limiting);
		$switching = array(':sessionID' => $sessionID, ':type' => 'O'); $withquotes = array(true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			$results = $sql->fetchAll(PDO::FETCH_ASSOC);
			return $results;
		}
	}

	function get_salesrep_orders($sessionID, $limit = 10, $page = 1, $debug) {
		$limiting = returnlimitstatement($limit, $page);
		$sql = wire('database')->prepare("SELECT * FROM ordrhed WHERE sessionid = :sessionID AND type = :type ".$limiting);
		$switching = array(':sessionID' => $sessionID, ':type' => 'O'); $withquotes = array(true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			$results = $sql->fetchAll(PDO::FETCH_ASSOC);
			return $results;
		}
	}

	function get_cust_order_count($sessionID, $custID, $debug) {
		$sql = wire('database')->prepare("SELECT COUNT(*) as count FROM ordrhed WHERE sessionid = :sessionID AND custid = :custID AND type = 'O'");
		$switching = array(':sessionID' => $sessionID, ':custID' => $custID); $withquotes = array(true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchColumn();
		}
	}

	function get_cust_orders($sessionID, $custID, $limit = 10, $page = 1, $debug) {
		$limiting = returnlimitstatement($limit, $page);
		$sql = wire('database')->prepare("SELECT * FROM ordrhed WHERE sessionid = :sessionID AND custid = :custID AND type = 'O' ".$limiting);
		$switching = array(':sessionID' => $sessionID, ':custID' => $custID); $withquotes = array(true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	function get_cust_orders_orderby($sessionID, $custID, $limit = 10, $page = 1, $sortrule, $orderby, $debug) {
		$limiting = returnlimitstatement($limit, $page);
		$sql = wire('database')->prepare("SELECT ordrhed.*, CAST(odrsubtot AS DECIMAL(8,2)) AS subtotal FROM ordrhed WHERE sessionid = :sessionID AND custid = :custID AND type = 'O' ORDER BY $orderby $sortrule ".$limiting);
		$switching = array(':sessionID' => $sessionID, ':custID' => $custID); $withquotes = array(true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	function get_cust_orders_orderdate($sessionID, $custID, $limit = 10, $page = 1, $sortrule, $debug) {
		$limiting = returnlimitstatement($limit, $page);
		$sql = wire('database')->prepare("SELECT orderdate, STR_TO_DATE(orderdate, '%m/%d/%Y') as dateoforder, orderno, custpo, shiptoid, sname, saddress, saddress2, scity, sst, szip, havenote, status, havetrk, havedoc, odrsubtot, odrtax, odrfrt, odrmis, odrtotal, error, errormsg, shipdate, custid, custname, invdate, editord FROM ordrhed WHERE sessionid = :sessionID AND custid = :custID AND type = 'O' ORDER BY dateoforder $sortrule ".$limiting);
		$switching = array(':sessionID' => $sessionID, ':custID' => $custID); $withquotes = array(true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	function get_custid_from_order($sessionID, $ordn) {
		$sql = wire('database')->prepare("SELECT custid FROM ordrhed WHERE sessionid = :sessionID AND orderno = :ordn LIMIT 1");
		$switching = array(':sessionID' => $sessionID, ':ordn' => $ordn);
		$sql->execute($switching);
		return $sql->fetchColumn();
	}

	function get_order_details($sessionID, $ordn, $debug) {
		$sql = wire('database')->prepare("SELECT * FROM ordrdet WHERE sessionid = :sessionID AND orderno = :ordn");
		$switching = array(':sessionID' => $sessionID, ':ordn' => $ordn); $withquotes = array(true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	function hasanorderlocked($sessionID) {
		$sql = wire('database')->prepare("SELECT COUNT(*) FROM ordlock WHERE sessionid = :sessionID");
		$switching = array(':sessionID' => $sessionID);
		$sql->execute($switching);
		return $sql->fetchColumn() > 0 ? true : false;
	}

	function getlockedordn($sessionID) {
		$sql = wire('database')->prepare("SELECT orderno FROM ordlock WHERE sessionid = :sessionID");
		$switching = array(':sessionID' => $sessionID);
		$sql->execute($switching);
		return $sql->fetchColumn();
	}

	function get_order_docs($sessionID, $ordn, $debug) { // FIXME USE PARAMATERS
		$sql = "SELECT * FROM orddocs WHERE sessionid = '$sessionID' AND orderno = '$ordn' AND itemnbr = '' ";
		if ($debug) {
			return $sql;
		} else {
			$results = wire('database')->query($sql);
			return $results;
		}
	}
/* =============================================================
	QUOTES FUNCTIONS
============================================================ */
	function hasaquotelocked($sessionID) {
		$sql = wire('database')->prepare("SELECT COUNT(*) FROM quotelock WHERE sessionid = :sessionID");
		$switching = array(':sessionID' => $sessionID); $withquotes = array(true);
		$sql->execute($switching);
		return $sql->fetchColumn();
	}

	function getlockedquotenbr($sessionID) {
		$sql = wire('database')->prepare("SELECT quotenbr FROM quotelock WHERE sessionid = :sessionID");
		$switching = array(':sessionID' => $sessionID); $withquotes = array(true);
		$sql->execute($switching);
		return $sql->fetchColumn();
	}

	function caneditquote($sessionID, $qnbr) {
		$sql = wire('database')->prepare("SELECT COUNT(*) FROM quotelock WHERE sessionid = :sessionID AND quotenbr = :qnbr");
		$switching = array(':sessionID' => $sessionID, ':qnbr' => $qnbr);
		$sql->execute($switching);
		return $sql->fetchColumn();
	}

	function get_cust_quote_count($sessionID, $custID, $debug) {
		$sql = wire('database')->prepare("SELECT COUNT(*) as count FROM quothed WHERE sessionid = :sessionID AND custid = :custID");
		$switching = array(':sessionID' => $sessionID, ':custID' => $custID); $withquotes = array(true,true);
		if ($debug) {
			returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchColumn();
		}
	}

	function get_cust_quotes($sessionID, $custID, $limit, $page, $debug) {
		$limiting = returnlimitstatement($limit, $page);
		$sql = wire('database')->prepare("SELECT * FROM quothed WHERE sessionid = :sessionID AND custid = :custID $limiting");
		$switching = array(':sessionID' => $sessionID, ':custID' => $custID); $withquotes = array(true, true);
		if ($debug) {
			returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	function getquotecustomer($sessionID, $qnbr, $debug) {
		$sql = wire('database')->prepare("SELECT custid FROM quothed WHERE sessionid = :sessionID AND quotnbr = :qnbr");
		$switching = array(':sessionID' => $sessionID, ':qnbr' => $qnbr); $withquotes = array(true, true);
		if ($debug) {
			returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchColumn();
		}
	}

	function getquoteshipto($sessionID, $qnbr, $debug) {
		$sql = wire('database')->prepare("SELECT shiptoid FROM quothed WHERE sessionid = :sessionID AND quotnbr = :qnbr");
		$switching = array(':sessionID' => $sessionID, ':qnbr' => $qnbr); $withquotes = array(true, true);
		if ($debug) {
			returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchColumn();
		}
	}

	function get_quotehead($sessionID, $qnbr, $debug) {
		$sql = wire('database')->prepare("SELECT * FROM quothed WHERE sessionid = :sessionID AND quotnbr = :qnbr");
		$switching = array(':sessionID' => $sessionID, ':qnbr' => $qnbr); $withquotes = array(true, true);
		if ($debug) {
			returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetch(PDO::FETCH_ASSOC);
		}
	}

	function get_quote_details($sessionID, $qnbr, $debug) {
		$sql = wire('database')->prepare("SELECT * FROM quotdet WHERE sessionid = :sessionID AND quotenbr = :qnbr");
		$switching = array(':sessionID' => $sessionID, ':qnbr' => $qnbr); $withquotes = array(true, true);
		if ($debug) {
			returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	function get_quoteline($sessionID, $qnbr, $line, $debug) {
		$sql = wire('database')->prepare("SELECT * FROM quotdet WHERE sessionid = :sessionID AND quotenbr = :qnbr AND linenbr = :line");
		$switching = array(':sessionID' => $sessionID, ':qnbr' => $qnbr, ':line' => $line); $withquotes = array(true, true, true);
		if ($debug) {
			return	returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetch(PDO::FETCH_ASSOC);
		}
	}

	function getquotelinedetail($sessionID, $qnbr, $line, $debug) {
		$sql = wire('database')->prepare("SELECT * FROM quotdet WHERE sessionid = :sessionID AND quotenbr = :qnbr AND linenbr = :linenbr");
		$switching = array(':sessionID' => $sessionID, ':qnbr' => $qnbr, ':linenbr' => $line); $withquotes = array(true, true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetch(PDO::FETCH_ASSOC);
		}
	}

	function insertquoteline($sessionID, $qnbr, $linenbr, $debug) {
		$sql = wire('database')->prepare("INSERT INTO quotdet (sessionid, quotenbr, linenbr) VALUES (:sessionID, :qnbr, :linenbr)");
		$switching = array(':sessionID' => $sessionID, ':qnbr' => $qnbr, ':linenbr' => $linenbr); $withquotes = array(true, true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return array('sql' => returnsqlquery($sql->queryString, $switching, $withquotes), 'insertedid' => wire('database')->lastInsertId());
		}
	}

	function nextquotelinenbr($sessionID, $qnbr) {
		$sql = wire('database')->prepare("SELECT MAX(linenbr) FROM quotdet WHERE sessionid = :sessionID AND quotenbr = :qnbr ");
		$switching = array(':sessionID' => $sessionID, ':qnbr' => $qnbr); $withquotes = array(true, true);
		$sql->execute($switching);
		return intval($sql->fetchColumn()) + 1;
	}

	function edit_quotehead($sessionID, $qnbr, $quote, $debug) {
		$originalquote = get_quotehead(session_id(), $qnbr, false);
		$query = returnpreppedquery($originalquote, $quote);
		$sql = wire('database')->prepare("UPDATE quothed SET ".$query['setstatement']." WHERE sessionid = :sessionID AND quotnbr = :quotnbr");
		$query['switching'][':sessionID'] = $sessionID; $query['switching'][':quotnbr'] = $qnbr;
		$query['withquotes'][]= true; $query['withquotes'][] = true;

		if ($debug) {
			return	returnsqlquery($sql->queryString, $query['switching'], $query['withquotes']);
		} else {
			if ($query['changecount'] > 0) {
				$sql->execute($query['switching']);
			}
			return returnsqlquery($sql->queryString, $query['switching'], $query['withquotes']);
		}
	}

	function edit_quoteline($sessionID, $qnbr, $newdetails, $debug) {
		$originaldetail = getquotelinedetail(session_id(), $qnbr, $newdetails['linenbr'], false);
		$query = returnpreppedquery($originaldetail, $newdetails);
		$sql = wire('database')->prepare("UPDATE quotdet SET ".$query['setstatement']." WHERE sessionid = :sessionID AND quotenbr = :qnbr AND linenbr = :linenbr");
		$query['switching'][':sessionID'] = $sessionID; $query['switching'][':qnbr'] = $qnbr; $query['switching'][':linenbr'] = $newdetails['linenbr'];
		$query['withquotes'][] = true; $query['withquotes'][]= true; $query['withquotes'][] = true;
		if ($debug) {
			return	returnsqlquery($sql->queryString, $query['switching'], $query['withquotes']);
		} else {
			if ($query['changecount'] > 0) {
				$sql->execute($query['switching']);
			}
			return returnsqlquery($sql->queryString, $query['switching'], $query['withquotes']);
		}
	}


/* =============================================================
	NOTES FUNCTIONS
============================================================ */
	function can_write_sales_note($sessionID, $ordn) {
		$sql = wire('database')->prepare("SELECT status FROM ordrhed WHERE sessionid = :sessionID AND orderno = :ordn LIMIT 1 ");
		$switching = array(':sessionID' => $sessionID, ':ordn' => $ordn);
		$sql->execute($switching);
		$status = $sql->fetchColumn();
		if (strtolower($status) == "open" || strtolower($status) == "new") {
			return true;
		} else {
			return false;
		}
	}

	function get_dplusnotes($sessionID, $key1, $key2, $type, $debug) {
		$sql = wire('database')->prepare("SELECT * FROM qnote WHERE sessionid = :sessionID AND key1 = :key1 AND key2 = :key2 AND rectype = :type");
		$switching = array(':sessionID' => $sessionID, ':key1' => $key1, ':key2' => $key2, ':type' => $type);
		$withquotes = array(true, true, true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	function getdplusnotecount($sessionID, $key1, $key2, $type, $debug) {
		$sql = wire('database')->prepare("SELECT COUNT(*) FROM qnote WHERE sessionid = :sessionID AND key1 = :key1 AND key2 = :key2 AND rectype = :type");
		$switching = array(':sessionID' => $sessionID, ':key1' => $key1, ':key2' => $key2, ':type' => $type);
		$withquotes = array(true, true, true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchColumn();
		}
	}

	function hasdplusnote($sessionID, $key1, $key2, $type) {
		if (getdplusnotecount($sessionID, $key1, $key2, $type, false)) {
			return 'Y';
		} else {
			return 'N';
		}
	}

	function get_dplusnote($sessionID, $key1, $key2, $type, $recnbr, $debug) {
		$sql = wire('database')->prepare("SELECT * FROM qnote WHERE sessionid = :sessionID AND key1 = :key1 AND key2 = :key2 AND rectype = :type AND recno = :recnbr");
		$switching = array(':sessionID' => $sessionID, ':key1' => $key1, ':key2' => $key2, ':type' => $type, ':recnbr' => $recnbr);
		$withquotes = array(true, true, true, true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetch(PDO::FETCH_ASSOC);
		}
	}


	function edit_note($sessionID, $key1, $key2, $form1, $form2, $form3, $form4, $form5, $note, $recnbr, $date, $time, $width) {
		$sql = wire('database')->prepare("UPDATE qnote SET notefld = :note  WHERE sessionid = :sessionID AND key1 = :key1 AND key2 = :key2 AND form1 = :form1 AND form2 = :form2 AND form3 = :form3 AND form4 = :form4 AND form5 = :form5 AND recno = :recnbr");
		$switching = array(':note' => $note, ':form1' => $form1, ':form2' => $form2, ':form3' => $form3, ':form4' => $form4, ':form5' => $form5, ':sessionID' => $sessionID, ':key1' => $key1, ':key2' => $key2, ':recnbr' => $recnbr);
		$withquotes = array(true, true, true, true, true, true, true, true, true, true);
		$sql->execute($switching);
		return returnsqlquery($sql->queryString, $switching, $withquotes);
	}

	function deletenote($sessionID, $key1, $key2, $form1, $form2, $form3, $form4, $form5, $rectype, $recnbr) {
		$sql = wire('database')->prepare("DELETE FROM qnote WHERE sessionid = :sessionID AND key1 = :key1 AND key2 = :key2 AND form1 = :form1 AND form2 = :form2 AND form3 = :form3 AND form4 = :form4 AND form5 = :form5 AND recno = :recnbr AND rectype = :rectype");
		$switching = array(':sessionID' => $sessionID, ':key1' => $key1, ':key2' => $key2, ':form1' => $form1, ':form2' => $form2, ':form3' => $form3, ':form4' => $form4, ':form5' => $form5, ':recnbr' => $recnbr, ':rectype' => $rectype);
		$withquotes = array(true, true, true, true, true, true, true, true, true, true);
		$sql->execute($switching);
		return returnsqlquery($sql->queryString, $switching, $withquotes);
	}

	function insert_note($sessionID, $key1, $key2, $form1, $form2, $form3, $form4, $form5, $note, $rectype, $recno, $date, $time, $width) { // FIXME USE PARAMATERS
		$sql = "INSERT INTO qnote (sessionid, notefld, key1, key2, form1, form2, form3, form4, form5, rectype, recno, date, time, colwidth) VALUES ('$sessionID', '$note',
		'$key1', '$key2', '$form1', '$form2', '$form3', '$form4', '$form5', '$rectype', '$recno', '$date', '$time', '$width')";
		wire('database')->query($sql);
		return $sql;
	}

	function insertdplusnote($sessionID, $key1, $key2, $form1, $form2, $form3, $form4, $form5, $note, $rectype, $recno, $date, $time, $width) {// FIXME USE PARAMATERS
		$sql = wire('database')->prepare("INSERT INTO qnote (sessionid, notefld, key1, key2, form1, form2, form3, form4, form5, rectype, recno, date, time, colwidth) VALUES (:sessionID, :note,
		:key1, :key2, :form1, :form2, :form3, :form4, :form5, :rectype, :recno, :date, :time, :width)");
		$switching = array(':sessionID' => $sessionID, ':note' => $note, ':key1' => $key1, ':key2' => $key2, ':form1' => $form1, ':form2' => $form2, ':form3' => $form3, ':form4' => $form4, ':form5' => $form5, ':rectype' => $rectype, ':recno' => $recno, ':date' => $date, ':time' => $time, ':width' => $width);
		$withquotes = array(true, true, true, true, true, true, true, true, true, true, true, true, true, true);
		$sql->execute($switching);
		return array('sql' => returnsqlquery($sql->queryString, $switching, $withquotes), 'insertedid' => wire('database')->lastInsertId());
	}

	function get_next_note_recno($sessionID, $key1, $key2, $rectype) { // FIXME USE PARAMATERS
		$sql = "SELECT MAX(recno) as max FROM qnote WHERE sessionid = '$sessionID' AND key1 = '$key1' AND key2 = '$key2' AND rectype = '$rectype'";
		$res = wire('database')->query($sql);
		$result = $res->fetchColumn();
		$nextrecnbr =  intval($result) + 1;
		return $nextrecnbr;
	}


/* =============================================================
	PRODUCT FUNCTIONS
============================================================ */
	function get_item_search_results($sessionID, $limit = 10, $page = 1, $debug) {
		$limiting = returnlimitstatement($limit, $page);
		$sql = wire('database')->prepare("SELECT * FROM pricing WHERE sessionid = :sessionID " .$limiting);
		$switching = array(':sessionID' => $sessionID); $withquotes = array(true);
		$sql->execute($switching);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	function get_item_search_results_count($sessionID, $debug) {
		$sql = wire('database')->prepare("SELECT COUNT(*) FROM pricing WHERE sessionid = :sessionID ");
		$switching = array(':sessionID' => $sessionID); $withquotes = array(true);
		$sql->execute($switching);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			return $sql->fetchColumn();
		}
	}

	function getitemavailability($sessionID, $itemID, $debug) {
		$sql = wire('database')->prepare("SELECT * FROM whseavail WHERE sessionid = :sessionID AND itemid = :itemid");
		$switching = array(':sessionID' => $sessionID, ':itemid' => $itemID); $withquotes = array(true, true);
		$sql->execute($switching);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	/* =============================================================
		USER ACTION FUNCTIONS
	============================================================ */

	function getuseractions($user, $querylinks, $limit, $page, $debug) {
		$limiting = returnlimitstatement($limit, $page);
		$query = returnwherelinks($querylinks);
		$andlinks = $query['wherestatement'];
		$sql = wire('database')->prepare("SELECT * FROM useractions WHERE $andlinks $limiting");
		$switching = $query['switching'];
		$withquotes = $query['withquotes'];
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			$sql->setFetchMode(PDO::FETCH_CLASS, 'UserAction');
			return $sql->fetchAll();
		}
	}

	function getuseractionscount($user, $querylinks, $debug) {
		$query = returnwherelinks($querylinks);
		$andlinks = $query['wherestatement'];
		$sql = wire('database')->prepare("SELECT COUNT(*) FROM useractions WHERE $andlinks");
		$switching = $query['switching'];
		$withquotes = $query['withquotes'];
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchColumn();
		}
	}

	function loaduseraction($id, $fetchclass, $debug) {
		$sql = wire('database')->prepare("SELECT * FROM useractions WHERE id = :id");
		$switching = array(':id' => $id); $withquotes = array(true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			if ($fetchclass) {
				$sql->setFetchMode(PDO::FETCH_CLASS, 'UserAction');
			}
			return $sql->fetch();
		}
	}

	function updateaction($actionID, $action, $debug) {
		$originalaction = loaduseraction($actionID, false, false); // (id, bool fetchclass, bool debug)
		$query = returnpreppedquery($originalaction, $action);
		$sql = wire('database')->prepare("UPDATE useractions SET ".$query['setstatement']." WHERE id = :actionid");
		$query['switching'][':actionid'] = $actionID;$query['withquotes'][] = true;
		if ($debug) {
			return returnsqlquery($sql->queryString, $query['switching'], $query['withquotes']);
		} else {
			$sql->execute($query['switching']);
			$success = $sql->rowCount();
			if ($success) {
				return array("error" => false,  "sql" => returnsqlquery($sql->queryString, $query['switching'], $query['withquotes']));
			} else {
				return array("error" => true,  "sql" => returnsqlquery($sql->queryString, $query['switching'], $query['withquotes']));
			}
		}

	}

	function insertaction($action, $debug) {
		$query = returninsertlinks($action);
		$sql = wire('database')->prepare("INSERT INTO useractions (".$query['columnlist'].") VALUES (".$query['valuelist'].")");
		$switching = $query['switching'];
		$withquotes = $query['withquotes'];
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return array('sql' => returnsqlquery($sql->queryString, $switching, $withquotes), 'insertedid' => wire('database')->lastInsertId());
		}
	}

	function get_useractions_maxrec($loginID) {
		$sql = wire('database')->prepare("SELECT MAX(id) AS id FROM useractions WHERE createdby = :login");
		$switching = array(':login' => $loginID);
		$withquotes = array(true, true);
		$sql->execute($switching);
		return $sql->fetchColumn();
	}

	function getparentaction($actionID, $debug) {
		$sql = wire('database')->prepare("SELECT actionlink FROM useractions WHERE id = :id");
		$switching = array(':id' => $actionID); $withquotes = array(true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchColumn();
		}
	}
/* =============================================================
	VENDOR FUNCTIONS
============================================================ */
	function getvendors($debug) {
		$sql = wire('database')->prepare("SELECT * FROM vendors WHERE shipfrom = ''");
		$switching = array(); $withquotes = array();
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute();
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	function getvendorshipfroms($vendorID, $debug) {
		$sql = wire('database')->prepare("SELECT * FROM vendors WHERE vendid = :vendor AND shipfrom != ''");
		$switching = array(':vendor' => $vendorID); $withquotes = array(true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	function getunitofmeasurements($debug) {
		$sql = wire('database')->prepare("SELECT * FROM unitofmeasure");
		if ($debug) {
			return $sql->queryString;
		} else {
			$sql->execute();
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	function getitemgroups($debug) {
		$sql = wire('database')->prepare("SELECT * FROM itemgroup");
		if ($debug) {
			return $sql->queryString;
		} else {
			$sql->execute();
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}
	}

/* =============================================================
	CART FUNCTIONS
============================================================ */
	function getcartheadcount($sessionID, $debug) {
		$sql = wire('database')->prepare("SELECT COUNT(*) FROM carthed WHERE sessionid = :sessionID");
		$switching = array(':sessionID' => $sessionID); $withquotes = array(true);
		$sql->execute($switching);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			return $sql->fetchColumn();
		}
	}

	function getcartcustomer($sessionID, $debug) {
		$sql = wire('database')->prepare("SELECT custid FROM carthed WHERE sessionid = :sessionID");
		$switching = array(':sessionID' => $sessionID); $withquotes = array(true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchColumn();
		}
	}

	function getcarthead($sessionID, $debug) {
		$sql = wire('database')->prepare("SELECT * FROM carthed WHERE sessionid = :sessionID");
		$switching = array(':sessionID' => $sessionID); $withquotes = array(true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetch(PDO::FETCH_ASSOC);
		}
	}

	function editcarthead($sessionID, $carthead, $debug) {
		$orginalcarthead = getcarthead($sessionID, false);
		$query = returnpreppedquery($originalcarthead, $carthead);
		$sql = wire('database')->prepare("UPDATE carthed SET ".$query['setstatement']." WHERE sessionid = :sessionID");
		$query['switching'][':sessionID'] = $sessionID; $query['withquotes'][] = true;
		if ($debug) {
			return returnsqlquery($sql->queryString, $query['switching'], $query['withquotes']);
		} else {
			if ($query['changecount'] > 0) {
				$sql->execute($query['switching']);
			}
			return returnsqlquery($sql->queryString, $query['switching'], $query['withquotes']);
		}
	}

	function getcart($sessionID, $debug) {
		$sql = wire('database')->prepare("SELECT * FROM cartdet WHERE sessionid = :sessionID");
		$switching = array(':sessionID' => $sessionID); $withquotes = array(true);
		$sql->execute($switching);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}

	}

	function insertcarthead($sessionID, $custID, $shipID, $debug) {
		$sql = wire('database')->prepare("INSERT INTO carthed (sessionid, custid, shiptoid, date, time) VALUES (:sessionID, :custID, :shipID, :date, :time)");
		$switching = array(':sessionID' => $sessionID, ':custID' => $custID, ':shipID' => $shipID, ':date' => date('Ymd'), ':time' =>date('His')); $withquotes = array(true, true, true, false, false);

		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		}
	}

	function getcartline($sessionID, $linenbr, $debug) {
		$sql = wire('database')->prepare("SELECT * FROM cartdet WHERE sessionid = :sessionID AND linenbr = :linenbr");
		$switching = array(':sessionID' => $sessionID, ':linenbr' => $linenbr); $withquotes = array(true, true);
		$sql->execute($switching);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			return $sql->fetch(PDO::FETCH_ASSOC);
		}
	}

	function insertcartline($sessionID, $linenbr, $debug) {
		$sql = wire('database')->prepare("INSERT INTO cartdet (sessionid, linenbr) VALUES (:sessionID, :linenbr)");
		$switching = array(':sessionID' => $sessionID, ':linenbr' => $linenbr); $withquotes = array(true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return array('sql' => returnsqlquery($sql->queryString, $switching, $withquotes), 'insertedid' => wire('database')->lastInsertId());
		}
	}

	function getcartlinedetail($sessionID, $linenbr, $debug) {
		return getcartline($sessionID, $linenbr, $debug);
	}

	function edit_cartline($sessionID, $newdetails, $debug) {
		$originaldetail = getcartlinedetail($sessionID, $newdetails['linenbr'], false);
		$query = returnpreppedquery($originaldetail, $newdetails);
		$sql = wire('database')->prepare("UPDATE cartdet SET ".$query['setstatement']." WHERE sessionid = :sessionID AND linenbr = :linenbr");
		$query['switching'][':sessionID'] = $sessionID; $query['switching'][':linenbr'] = $newdetails['linenbr'];
		$query['withquotes'][] = true; $query['withquotes'][]= true; $query['withquotes'][] = true;
		if ($debug) {
			return returnsqlquery($sql->queryString, $query['switching'], $query['withquotes']);
		} else {
			if ($query['changecount'] > 0) {
				$sql->execute($query['switching']);
			}
			return returnsqlquery($sql->queryString, $query['switching'], $query['withquotes']);
		}
	}

	function nextcartlinenbr($sessionID) {
		$sql = wire('database')->prepare("SELECT MAX(linenbr) FROM cartdet WHERE sessionid = :sessionID");
		$switching = array(':sessionID' => $sessionID); $withquotes = array(true);
		$sql->execute($switching);
		return intval($sql->fetchColumn()) + 1;
	}

	function getcreatedordn($sessionID, $debug) {
		$sql = wire('database')->prepare("SELECT ordernbr FROM logperm WHERE sessionid = :sessionID");
		$switching = array(':sessionID' => $sessionID); $withquotes = array(true);
		$sql->execute($switching);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			return $sql->fetchColumn();
		}
	}



/* =============================================================
	EDIT ORDER FUNCTIONS
============================================================ */
	function caneditorder($sessionID, $ordn, $debug) {
		$sql = wire('database')->prepare("SELECT editord FROM ordrhed WHERE sessionid = :sessionID AND orderno = :ordn LIMIT 1");
		$switching = array(':sessionID' => $sessionID, ':ordn' => $ordn); $withquotes = array(true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			$column = $sql->fetchColumn();
			if ($column != 'Y') { return false; } else { return true; }
		}
	}

	function get_customer_name_from_order($sessionID, $ordn) {
		$sql = wire('database')->prepare("SELECT custname FROM ordrhed WHERE sessionid = :sessionID AND orderno = :ordn LIMIT 1");
		$switching = array(':sessionID' => $sessionID, ':ordn' => $ordn);
		$sql->execute($switching);
		return $sql->fetchColumn();
	}

	function get_shiptoid_from_order($sessionID, $ordn) {
		$sql = wire('database')->prepare("SELECT shiptoid FROM ordrhed WHERE sessionid = :sessionID AND orderno = :ordn LIMIT 1");
		$switching = array(':sessionID' => $sessionID, ':ordn' => $ordn);
		$sql->execute($switching);
		return $sql->fetchColumn();
	}

	function get_orderhead($sessionID, $ordn, $debug) {
		$sql = wire('database')->prepare("SELECT * FROM ordrhed WHERE sessionid = :sessionID AND orderno = :ordn AND type = 'O'");
		$switching = array(':sessionID' => $sessionID, ':ordn' => $ordn); $withquotes = array(true, true);
		$sql->execute($switching);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			return $sql->fetch(PDO::FETCH_ASSOC);
		}
	}

	function getorderdetails($sessionID, $ordn, $debug) {
		$sql = wire('database')->prepare("SELECT * FROM ordrdet WHERE sessionid = :sessionID AND orderno = :ordn");
		$switching = array(':sessionID' => $sessionID, ':ordn' => $ordn); $withquotes = array(true, true);
		$sql->execute($switching);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	function getorderlinedetail($sessionID, $ordn, $linenumber, $debug) {
		$sql = wire('database')->prepare("SELECT * FROM ordrdet WHERE sessionid = :sessionID AND orderno = :ordn AND linenbr = :linenbr");
		$switching = array(':sessionID' => $sessionID, ':ordn' => $ordn, ':linenbr' => $linenumber); $withquotes = array(true, true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetch(PDO::FETCH_ASSOC);
		}
	}

	function getallorderdocs($sessionID, $ordn, $debug) {
		$sql = wire('database')->prepare("SELECT * FROM orddocs WHERE sessionid = :sessionID AND orderno = :ordn ORDER BY itemnbr ASC");
		$switching = array(':sessionID' => $sessionID, ':ordn' => $ordn); $withquotes = array(true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	function getordertracking($sessionID, $ordn, $debug) {
		$sql = wire('database')->prepare("SELECT * FROM ordrtrk WHERE sessionid = :sessionID AND orderno = :ordn");
		$switching = array(':sessionID' => $sessionID, ':ordn' => $ordn); $withquotes = array(true, true);
		$sql->execute($switching);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	function edit_orderhead($sessionID, $ordn, $order, $debug) {
		$orginalorder = get_orderhead(session_id(), $ordn, false);
		$query = returnpreppedquery($orginalorder, $order);
		$sql = wire('database')->prepare("UPDATE ordrhed SET ".$query['setstatement']." WHERE sessionid = :sessionID AND orderno = :ordn");
		$query['switching'][':sessionID'] = $sessionID; $query['switching'][':ordn'] = $ordn;
		$query['withquotes'][] = true; $query['withquotes'][]= true;

		if ($debug) {
			return	returnsqlquery($sql->queryString, $query['switching'], $query['withquotes']);
		} else {
			if ($query['changecount'] > 0) {
				$sql->execute($query['switching']);
			}
			return returnsqlquery($sql->queryString, $query['switching'], $query['withquotes']);
		}
	}

	function edit_orderhead_credit($sessionID, $ordn, $paytype, $ccno, $xpd, $ccv) {
		$sql = wire('database')->prepare("UPDATE ordrhed SET paytype = :paytype, ccno = AES_ENCRYPT(:ccno, HEX(:sessionID)), xpdate = AES_ENCRYPT(:xpd, HEX(:sessionID)), ccvalidcode = AES_ENCRYPT(:ccv, HEX(:sessionID)) WHERE sessionid = :sessionID AND orderno = :ordn");
		$switching = array(':paytype' => $paytype, ':ccno' => $ccno, ':sessionID' => $sessionID, ':xpd' => $xpd, ':ccv' => $ccv, ':sessionID' => $sessionID, ':ordn' => $ordn);
		$withquotes = array(true ,true, true, true, true, true,true);
		$sql->execute($switching);
		return returnsqlquery($sql->queryString, $switching, $withquotes);
	}

	function edit_orderline($sessionID, $ordn, $newdetails, $debug) {
		$originaldetail = getorderlinedetail($sessionID, $ordn, $newdetails['linenbr'], false);
		$query = returnpreppedquery($originaldetail, $newdetails);
		$sql = wire('database')->prepare("UPDATE ordrdet SET ".$query['setstatement']." WHERE sessionid = :sessionID AND orderno = :ordn AND linenbr = :linenbr");
		$query['switching'][':sessionID'] = $sessionID; $query['switching'][':ordn'] = $ordn; $query['switching'][':linenbr'] = $newdetails['linenbr'];
		$query['withquotes'][] = true; $query['withquotes'][]= true; $query['withquotes'][] = true;
		if ($debug) {
			return	returnsqlquery($sql->queryString, $query['switching'], $query['withquotes']);
		} else {
			if ($query['changecount'] > 0) {
				$sql->execute($query['switching']);
			}
			return returnsqlquery($sql->queryString, $query['switching'], $query['withquotes']);
		}
	}

	function insertorderline($sessionID, $ordn, $linenbr, $debug) {
		$sql = wire('database')->prepare("INSERT INTO ordrdet (sessionid, orderno, linenbr) VALUES (:sessionID, :ordn, :linenbr)");
		$switching = array(':sessionID' => $sessionID, ':ordn' => $ordn, ':linenbr' => $linenbr); $withquotes = array(true, true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return array('sql' => returnsqlquery($sql->queryString, $switching, $withquotes), 'insertedid' => wire('database')->lastInsertId());
		}
	}

	function getordercreditcard($sessionID, $ordn, $debug) {
		$sql = wire('database')->prepare("SELECT AES_DECRYPT(ccno , HEX(sessionid)) AS cardnumber, AES_DECRYPT(ccvalidcode , HEX(sessionid)) AS validation, AES_DECRYPT(xpdate, HEX(sessionid)) AS expiredate FROM ordrhed WHERE sessionid = :sessionID AND orderno = :ordn AND type = 'O'");
		$switching = array(':sessionID' => $sessionID, ':ordn' => $ordn); $withquotes = array(true, true);
		$sql->execute($switching);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			return $sql->fetch(PDO::FETCH_ASSOC);
		}
	}

	function getshipvias($sessionID) {
		$sql = wire('database')->prepare("SELECT code, via FROM shipvia WHERE sessionid = :sessionID");
		$switching = array(':sessionID' => $sessionID); $withquotes = array(true);
		$sql->execute($switching);
		return $sql->fetchAll(PDO::FETCH_ASSOC);
	}

/* =============================================================
	MISC ORDER FUNCTIONS
============================================================ */
	function getstates() {
		$sql = wire('database')->prepare("SELECT abbreviation as state, name FROM states");
		$sql->execute();
		return $sql->fetchAll(PDO::FETCH_ASSOC);
	}

	function getcountries() {
		$sql = wire('database')->prepare("SELECT * FROM countries");
		$sql->execute();
		return $sql->fetchAll(PDO::FETCH_ASSOC);
	}



/* =============================================================
	ITEM FUNCTIONS
============================================================ */
	function getiteminfo($sessionID, $itemID, $debug) {
		$sql = wire('database')->prepare("SELECT * FROM pricing WHERE sessionid = :sessionID AND itemid = :itemid LIMIT 1");
		$switching = array(':sessionID' => $sessionID, ':itemid' => $itemID); $withquotes = array(true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetch(PDO::FETCH_ASSOC);
		}
	}

	function getitemfromim($itemID, $debug) {
		$sql = wire('database')->prepare("SELECT * FROM pricing WHERE itemid = :itemid LIMIT 1");
		$switching = array(':itemid' => $itemID); $withquotes = array(true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetch(PDO::FETCH_ASSOC);
		}
	}

	/* =============================================================
		ITEM MASTER FUNCTIONS
	============================================================ */
	function search_itm($q, $onlyitemid, $custID, $limit, $page, $debug) {
		$limiting = returnlimitstatement($limit, $page);
		$search = '%'.str_replace(' ', '%', $q).'%';
		if (empty($custID)) {
			if ($onlyitemid) {
				$sql = wire('database')->prepare("SELECT * FROM itemsearch WHERE UCASE(itemid) LIKE UCASE(:search) AND origintype = 'I' GROUP BY itemid $limiting");
				$switching = array(':search' => $search); $withquotes = array(true);
			} else {
				$sql = wire('database')->prepare("SELECT * FROM itemsearch WHERE UCASE(CONCAT(itemid, ' ', originid, ' ', desc1, ' ', desc2)) LIKE UCASE(:search) AND origintype = 'I' GROUP BY itemid $limiting");
				$switching = array(':search' => $search); $withquotes = array(true);
			}
		} else {
			if ($onlyitemid) {
				$sql = wire('database')->prepare("SELECT * FROM itemsearch WHERE (originid = (:custID) AND UCASE(refitemid) LIKE UCASE(:search)) OR (UCASE(itemid) like UCASE(:search)) GROUP BY itemid $limiting ");
				$switching = array(':search' => $search, ':custID' => $custID); $withquotes = array(true, true);
			} else {
				$sql = wire('database')->prepare("SELECT * FROM itemsearch WHERE (UCASE(CONCAT(itemid, ' ', originid, ' ', desc1, ' ', desc2)) LIKE UCASE(:search) AND origintype = 'I') OR (UCASE(CONCAT(itemid, ' ', refitemid, ' ', originid, ' ', desc1, ' ', desc2)) LIKE UCASE(:search) AND originid = :custID) GROUP BY itemid $limiting");
				$switching = array(':search' => $search, ':custID' => $custID); $withquotes = array(true, true);
			}
		}
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	function validateitemid($itemID, $custID, $debug) {
		if (empty($custID)) {
			$sql = wire('database')->prepare("SELECT COUNT(*) FROM itemsearch WHERE UCASE(itemid) = UCASE(:itemID) AND originid = 'I'");
			$switching = array(':itemID' => $itemID); $withquotes = array(true);
		} else {
			$sql = wire('database')->prepare("SELECT COUNT(*) FROM itemsearch WHERE (originid = (:custID) AND UCASE(refitemid) = UCASE(:itemID)) OR (UCASE(itemid) = UCASE(:itemID) AND origintype = 'I')");
			$switching = array(':itemID' => $itemID, ':custID' => $custID); $withquotes = array(true, true);
		}

		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchColumn();
		}
	}

	function search_itmcount($q, $onlyitemid, $custID, $debug) {
		$search = '%'.str_replace(' ', '%', $q).'%';
		if (empty($custID)) {
			if ($onlyitemid) {
				$sql = wire('database')->prepare("SELECT COUNT(*) FROM itemsearch WHERE UCASE(itemid) LIKE UCASE(:search) AND origintype = 'I' GROUP BY itemid $limiting");
				$switching = array(':search' => $search); $withquotes = array(true);
			} else {
				$sql = wire('database')->prepare("SELECT COUNT(*) FROM itemsearch WHERE UCASE(CONCAT(itemid, ' ', originid, ' ', desc1, ' ', desc2)) LIKE UCASE(:search) AND origintype = 'I'");
				$switching = array(':search' => $search); $withquotes = array(true);
			}
		} else {
			if ($onlyitemid) {
				$sql = wire('database')->prepare("SELECT COUNT(*) FROM itemsearch WHERE (originid = (:custID) AND UCASE(refitemid) LIKE UCASE(:search)) OR (UCASE(itemid) like UCASE(:search))");
				$switching = array(':search' => $search, ':custID' => $custID); $withquotes = array(true, true);
			} else {
				$sql = wire('database')->prepare("SELECT COUNT(*) FROM itemsearch WHERE (UCASE(CONCAT(itemid, ' ', originid, ' ', desc1, ' ', desc2)) LIKE UCASE(:search) AND origintype = 'I') OR (UCASE(CONCAT(itemid, ' ', refitemid, ' ', originid, ' ', desc1, ' ', desc2)) LIKE UCASE(:search) AND originid = :custID)");
				$switching = array(':search' => $search, ':custID' => $custID); $withquotes = array(true, true);
			}
		}
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchColumn();
		}
	}

	function getitemdescription($itemID, $debug) {
		$sql = wire('database')->prepare("SELECT desc1 FROM itemsearch WHERE itemid = :itemid LIMIT 1");
		$switching = array(':itemid' => $itemID); $withquotes = array(true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchColumn();
		}
	}

	function getnextrecno($itemID, $nextorprev, $debug) {
		if ($nextorprev == 'next') {
			$sql = wire('database')->prepare("SELECT MAX(recno) + 1 FROM itemsearch WHERE itemid = :itemid");
		} else {
			$sql = wire('database')->prepare("SELECT MIN(recno) - 1 FROM itemsearch WHERE itemid = :itemid");
		}
		$switching = array(':itemid' => $itemID); $withquotes = array(true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchColumn();
		}
	}

	function getitembyrecno($recno, $debug) {
		$sql = wire('database')->prepare("SELECT itemid FROM itemsearch WHERE recno = :recno");
		$switching = array(':recno' => $recno); $withquotes = array(true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchColumn();
		}

	}

	/* =============================================================
		TABLE FORMATTER FUNCTIONS
	============================================================ */
	function getformatter($user, $formatter, $debug) {
		$sql = wire('database')->prepare("SELECT data FROM tableformatter WHERE user = :user AND formattertype = :formatter LIMIT 1");
		$switching = array(':user' => $user, ':formatter' => $formatter); $withquotes = array(true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchColumn();
		}
	}

	function addformatter($user, $formatter, $data, $debug) {
		$sql = wire('database')->prepare("INSERT INTO tableformatter (user, formattertype, data) VALUES (:user, :formatter, :data)");
		$switching = array(':user' => $user, ':formatter' => $formatter, ':data' => $data); $withquotes = array(true, true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return array('sql' => returnsqlquery($sql->queryString, $switching, $withquotes), 'insertedid' => wire('database')->lastInsertId());
		}
	}

	function checkformatterifexists($user, $formatter, $debug) {
		$sql = wire('database')->prepare("SELECT COUNT(*) FROM tableformatter WHERE user = :user AND formattertype = :formatter");
		$switching = array(':user' => $user, ':formatter' => $formatter); $withquotes = array(true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchColumn();
		}
	}

	function getmaxtableformatterid($user, $formatter, $debug) {
		$sql = wire('database')->prepare("SELECT MAX(id) FROM tableformatter WHERE user = :user AND formattertype = :formatter");
		$switching = array(':user' => $user, ':formatter' => $formatter); $withquotes = array(true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchColumn();
		}
	}

	function editformatter($user, $formatter, $data, $debug) {
		$sql = wire('database')->prepare("UPDATE tableformatter SET data = :data WHERE user = :user AND formattertype =  :formatter");
		$switching = array(':user' => $user, ':formatter' => $formatter, ':data' => $data); $withquotes = array(true, true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);

			return array('sql' => returnsqlquery($sql->queryString, $switching, $withquotes), 'affectedrows' => $sql->rowCount() ? true : false);
		}
	}

	/* =============================================================
		USER CONFIGS FUNCTIONS
	============================================================ */
	function checkconfigifexists($user, $configuration, $debug) {
		$sql = wire('database')->prepare("SELECT COUNT(*) FROM userconfigs WHERE user = :user AND configtype = :config");
		$switching = array(':user' => $user, ':config' => $configuration); $withquotes = array(true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchColumn();
		}
	}

	function getconfiguration($user, $configuration, $debug) {
		$sql = wire('database')->prepare("SELECT data FROM userconfigs WHERE user = :user AND configtype = :config LIMIT 1");
		$switching = array(':user' => $user, ':config' => $configuration); $withquotes = array(true, true);
		if ($debug) {
			return returnsqlquery($sql->queryString, $switching, $withquotes);
		} else {
			$sql->execute($switching);
			return $sql->fetchColumn();
		}
	}


























?>
