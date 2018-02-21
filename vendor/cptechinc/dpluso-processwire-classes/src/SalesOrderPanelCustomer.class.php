<?php 
	class CustomerSalesOrderPanel extends SalesOrderPanel implements OrderPanelCustomerInterface {
		use OrderPanelCustomerTraits;
		
		/* =============================================================
			SalesOrderPanelInterface Functions
		============================================================ */
		public function get_ordercount($debug = false) {
			$this->count = count_customerorders($this->sessionID, $this->custID, $debug);
		}
		
		public function get_orders($debug = false) {
			$useclass = true;
			if ($this->tablesorter->orderby) {
				if ($this->tablesorter->orderby == 'orderdate') {
					$orders = get_customerordersorderdate($this->sessionID, $this->custID, Processwire\wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $useclass, $debug);
				} else {
					$orders = get_customerordersorderby($this->sessionID, $this->custID, Processwire\wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->tablesorter->orderby, $useclass, $debug);
				}
			} else {
				// DEFAULT BY ORDER DATE SINCE SALES ORDER # CAN BE ROLLED OVER
				$this->tablesorter->sortrule = 'DESC';
				// $this->tablesorter->orderby = 'orderno';
				//$orders = get_customerordersorderby($this->sessionID, $this->custID, Processwire\wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->tablesorter->orderby, $useclass, $debug);
				$orders = get_customerordersorderdate($this->sessionID, $this->custID, Processwire\wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $useclass, $debug);
			}
			return $debug ? $orders : $this->orders = $orders;
		}
		
		/* =============================================================
			OrderPanelInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		public function generate_loadurl() { 
			$url = new \Purl\Url(parent::generate_loadurl());
			$url->query->set('action', 'load-cust-orders');
			$url->query->set('custID', $this->custID);
			return $url->getUrl();
		}
		
		public function generate_searchurl() {
			$url = new \Purl\Url(parent::generate_searchurl());
			$url->path = Processwire\wire('config')->pages->ajax.'load/orders/search/cust/';
			$url->query->set('custID', $this->custID);
			if ($this->shipID) {
				$url->query->set('shipID', $this->shipID);
			}
			return $url->getUrl();
		}
		
		public function generate_loaddetailsurl(Order $order) {
			$url = new \Purl\Url(parent::generate_loaddetailsurl($order));
			$url->query->set('custID', $order->custid);
			return $url->getUrl();
		}
		
		public function generate_lastloadeddescription() {
			if (Processwire\wire('session')->{'orders-loaded-for'}) {
				if (Processwire\wire('session')->{'orders-loaded-for'} == $this->custID) {
					return 'Last Updated : ' . Processwire\wire('session')->{'orders-updated'};
				}
				return '';
			}
			return '';
		}
		
		/* =============================================================
			SalesOrderDisplayInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		public function generate_trackingrequesturl(Order $order) {
			$url = new \Purl\Url(parent::generate_trackingrequesturl($order));
			$url->query->set('custID', $this->custID);
			return $url->getUrl();
		}
		
		/* =============================================================
			OrderDisplayInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		public function generate_documentsrequesturl(Order $order, OrderDetail $orderdetail = null) {
			$url = new \Purl\Url(parent::generate_documentsrequesturl($order, $orderdetail));
			$url->query->set('custID', $this->custID);
			return $url->getUrl();
		}
	}
