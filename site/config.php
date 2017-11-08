<?php namespace ProcessWire;

/**
* ProcessWire Configuration File
*
* Site-specific configuration for ProcessWire
*
* Please see the file /wire/config.php which contains all configuration options you may
* specify here. Simply copy any of the configuration options from that file and paste
* them into this file in order to modify them.
*
* SECURITY NOTICE
* In non-dedicated environments, you should lock down the permissions of this file so
* that it cannot be seen by other users on the system. For more information, please
* see the config.php section at: https://processwire.com/docs/security/file-permissions/
*
* This file is licensed under the MIT license
* https://processwire.com/about/license/mit/
*
* ProcessWire 3.x, Copyright 2016 by Ryan Cramer
* https://processwire.com
*
*/

	if(!defined("PROCESSWIRE")) die();

/*** SITE CONFIG *************************************************************************/

	/** @var Config $config */

	/**
	* Enable debug mode?
	*
	* Debug mode causes additional info to appear for use during dev and debugging.
	* This is almost always recommended for sites in development. However, you should
	* always have this disabled for live/production sites.
	*
	* @var bool
	*
	*/
	$config->debug = true;

	/**
	* Prepend template file
	*
	* PHP file in /site/templates/ that will be loaded before each page's template file.
	* Example: _init.php
	*
	* @var string
	*
	*/
	$config->prependTemplateFile = '_init.php';
	$config->maxUrlSegments = 7;

/*** INSTALLER CONFIG ********************************************************************/

	/**
	* Installer: Database Configuration
	*
	*/
	$config->dbHost = 'localhost';
	$config->dbName = 'dpluso';
	$config->dbUser = 'cptecomm';
	$config->dbPass = 'rghopeless';
	$config->dbPort = '3306';
	/**
	* Installer: User Authentication Salt
	*
	* Must be retained if you migrate your site from one server to another
	*
	*/
	$config->userAuthSalt = 'c3dea9cb09c65aea31f62a18cef02490';

	/**
	* Installer: File Permission Configuration
	*
	*/
	$config->chmodDir = '0755'; // permission for directories created by ProcessWire
	$config->chmodFile = '0644'; // permission for files created by ProcessWire

	/**
	* Installer: Time zone setting
	*
	*/
	$config->timezone = 'America/Chicago';

	/**
	* Installer: Unix timestamp of date/time installed
	*
	* This is used to detect which when certain behaviors must be backwards compatible.
	* Please leave this value as-is.
	*
	*/
	$config->installed = 1485271921;


	/**
	* Installer: HTTP Hosts Whitelist
	*
	*/
	$config->httpHosts = array('192.168.1.2', 'soft', '192.168.1.30');

	/**
	* CPTECH: Additional Configurations
	*
	*
	*
	*/
	
	$config->cgi = "DPLUSO";

	$config->cptechcustomer = 'stempf';
	$config->COMPANYNBR = '3';
	$config->companyfiles = "/var/www/html/data".$config->COMPANYNBR."/";
	$config->documentstorage = "/orderfiles/";
	$config->documentstoragedirectory = "/var/www/html/orderfiles/";
	$config->jsonfilepath = "/var/www/html/files/json/";
	$config->directory = "";
	$config->filename = $_SERVER['REQUEST_URI'];
	$config->script = str_replace($config->urls->root, '', $_SERVER['SCRIPT_NAME']);
	$config->imagedirectory = '/img/product/';
	$config->imagefiledirectory  = '/var/www/html/img/product/';
	$config->imagenotfound = 'notavailable.png';
	$config->siteassets = $config->urls->root . 'assets/files/';
	$config->sharedaccounts = 'zyxwvu';
	$config->defaultweb = 'WEBORD';
	$config->showonpage = '10';
	$config->showonpageoptions = array('5', '10', '20', '50');
	$config->yesnoarray = array('Yes' => 'Y', 'No' => 'N');
	$config->nonstockitems = array('N');
	$config->salesrepcustomer = true;

	$config->fob_array = array('Origin' => 'O', 'Delivery' => 'D');

	$config->dplusnotes = array (
		'order' => array(
			'width' => '35', 'type' => 'SORD', 'forms' => 4, 'form1' => 'Pick Ticket', 'form2' => 'Pack Ticket', 'form3' => 'Invoice', 'form4' => 'Acknowledgement'
		),
		'quote' => array(
			'width' => '35', 'type' => 'QUOT', 'forms' => 5, 'form1' => 'Quote', 'form2' => 'Pick Ticket', 'form3' => 'Pack Ticket', 'form4' => 'Invoice', 'form5' => 'Acknowledgement'
		),
		'cart' => array(
			'width' => '35', 'type' => 'CART', 'forms' => 5, 'form1' => 'Quote', 'form2' => 'Pick Ticket', 'form3' => 'Pack Ticket', 'form4' => 'Invoice', 'form5' => 'Acknowledgement'
		)
	);

	$config->phoneintl = false;
	$config->textjustify = array('r' => 'text-right', 'c' => 'text-center', 'l' => 'text-left', 'u' => '');
	$config->formattypes = array('N' => 'number', 'I' => 'integer', 'C' => 'text', 'D' => 'date');
	$config->specialordertypes = array('S' => 'Special Order', 'N' => 'Normal', 'D' => 'Dropship');
	
	$config->pages = new Paths($rootURL);
	$config->pages->index = $config->urls->root;
	$config->pages->account = $config->urls->root . 'user/account/';
	$config->pages->login = $config->urls->root . 'user/account/login/';
	$config->pages->userscreens = $config->urls->root . 'user/user-screens/';
	$config->pages->ajax = $config->urls->root . 'ajax/';
	$config->pages->ajaxjson = $config->urls->root . 'ajax/json/';
	$config->pages->ajaxload = $config->urls->root . 'ajax/load/';
	$config->pages->cart = $config->urls->root . 'cart/';
	$config->pages->customer = $config->urls->root . 'customers/';
	$config->pages->custinfo = $config->urls->root . 'customers/cust-info/';
	$config->pages->edit = $config->urls->root . 'edit/';
	$config->pages->editorder = $config->urls->root . 'edit/order/';
	$config->pages->editquote = $config->urls->root . 'edit/quote/';
	$config->pages->orderquote = $config->urls->root . 'edit/quote-to-order/';
	$config->pages->confirmorder = $config->urls->root . 'edit/order/confirm/';
	$config->pages->confirmquote = $config->urls->root . 'edit/quote/confirm/';
	$config->pages->print = $config->urls->root."print/";
	$config->pages->products = $config->urls->root . 'products/';
	$config->pages->iteminfo = $config->urls->root . 'products/item-info/';
	$config->pages->user = $config->urls->root . 'user/';
	$config->pages->usernotes = $config->urls->root . 'user/notes/';
	$config->pages->notes = $config->urls->root . 'notes/';
	$config->pages->usertasks = $config->urls->root . 'user/tasks/';
	$config->pages->tasks = $config->urls->root . 'tasks/';
	$config->pages->taskschedule = $config->urls->root . 'tasks/schedule/';
	$config->pages->userpage = $config->urls->root . 'user/';
	$config->pages->dashboard = $config->urls->root . 'user/dashboard/';
	$config->pages->userconfigs = $config->urls->root . 'user/user-config/';
	$config->pages->orders = $config->urls->root . 'user/orders/';
	$config->pages->quotes = $config->urls->root . 'user/quotes/';
	$config->pages->actions = $config->urls->root . 'activity/';
	$config->pages->documentation = $config->urls->root . "documentation/";
	$config->pages->documentstorage = $config->documentstorage;
	$config->pages->vendor = $config->urls->root . "vendors/";
	$config->pages->vendorinfo = $config->urls->root . "vendors/vend-info/";
