<?php

class ArrayValuePairGenerator
{
	public function getPairs($input)
	{
		$output = [];

		foreach ($input as $element)
		{
			foreach ($input as $element2)
			{
				if ($element !== $element2)
				{
					$output[] = [$element => $element2];
				}
			}
		}

		return $output;
	}
}