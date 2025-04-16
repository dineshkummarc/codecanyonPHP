<?php

class Pagination {
	public 	$per_page,
			$current_page,
			$total_pages,
			$limit,
			$current_page_link,
			$link;

	public function __construct($per_page, $where, $additional_join = null, $from = 'quotes') {
		global $database;
		global $account_user_id;

		/* Initiate the $per_page variable */
		$this->per_page = $per_page;

		/* Get the total servers count */
		$stmt = $database->prepare("SELECT COUNT(*) FROM `{$from}` {$additional_join} {$where}");
		$stmt->execute();
		$stmt->bind_result($total);
		$stmt->fetch();
		$stmt->close();

		/* Determine the number of total pages */
		$this->total_pages = ceil($total/$this->per_page);

		/* Determine the current page and check for errors */
		$this->current_page = (isset($_GET['current_page'])) ? (int)$_GET['current_page'] : 1;

		/* Check if the current page number is less than 1 or higher than the $total_pages */
		$this->current_page = ($this->current_page < 1 || $this->current_page > $this->total_pages) ? 1 : $this->current_page;

		/* Generate the limit query */
		$this->limit = "LIMIT " . ($this->current_page - 1) * $this->per_page . "," . $this->per_page;
		
	}

	public function set_current_page_link($current_page_link) {

		/* Initiate the $current_page_link variable */
		$this->current_page_link = $current_page_link;

		/* Generate the $link without any affix */
		$this->link = $this->current_page_link . '/' . $this->current_page;

	}

	public function display($affix = null, $aside = 5) {

		/* Create the next and the previous variables */
		$previous = $this->current_page - 1;
		$next = $this->current_page + 1;

		/* Start generating the links */
		$pagination = '<ul class="pagination no-margin">';

		/* Previous button */
		$pagination .= ($this->current_page != 1) ? '<li><a href="' . $this->current_page_link . '/' . $previous . $affix . '">&laquo;</a></li>' : '<li class="disabled"><a href="' . $this->current_page_link . '/' . $this->current_page . '">&laquo;</a></li>';
		
		/* Previous X buttons */
		for($i = $this->current_page - $aside; $i < $this->current_page; $i++) {
			if($i > 0) {
				$pagination .= '<li><a href="' . $this->current_page_link . '/' . $i . $affix . '">' . $i . '</a></li>';
			}
		}

		/* Current Page */
		$pagination .= '<li class="active"><a href="' . $this->current_page_link . '/' . $this->current_page . $affix . '">' . $this->current_page . '</a></li>';

		/* Next X buttons */
		for($i = $this->current_page + 1; $i <= $this->total_pages; $i++) {
			$pagination .= '<li><a href="' . $this->current_page_link . '/' . $i . $affix . '">' . $i . '</a></li>';
			
			if($i >= $this->current_page + $aside) break;
		}

		/* Next button */
		$pagination .= ($this->current_page != $this->total_pages) ? '<li><a href="' . $this->current_page_link . '/' . $next . $affix . '">&raquo;</a></li>' : '<li class="disabled"><a href="' . $this->current_page_link . '/' . $this->current_page . '">&raquo;</a></li>';

			
		$pagination .= '</ul>';

		echo $pagination;
	}

}
?>