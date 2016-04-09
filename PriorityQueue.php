<?php

class PriorityQueue extends SplPriorityQueue
{
	public function compare($p1, $p2) {
		if ($p1 == $p2) {
			return 0;
		}
		else {
			return ($p1 < $p2) ? 1 : -1;
		}
	}
}