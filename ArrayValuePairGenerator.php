<?php

class ArrayValuePairGenerator
{
	public function getPairs($input)
	{
		$output=array();

		for ($i=0;$i<sizeof($input);$i++) {
			$k=$input[$i];
			for ($j=$i+1;$j<sizeof($input);$j++) {
				$v=$input[$j];
				$output[]=array($k=>$v);
			}
		}

		return $output;
	}
}