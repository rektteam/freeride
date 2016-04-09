<?php

class JCDecauxApiClient
{
	public function getAllStations()
	{
		$serviceUrl = 'https://api.jcdecaux.com/vls/v1/stations?contract=Luxembourg&apiKey=9fb75f8fc19827e9086cbd5707e7124f895e36b8';

		$response = $this->call($serviceUrl);

		$stationsTemp = json_decode($response, true);

		$stations = [];

		foreach ($stationsTemp as $station)
		{
			$stations[$station['number']] = $station;
		}

		return $stations;
	}

	protected function call($url)
	{
		$ch = curl_init();
		$timeout = 60;
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
		$data = curl_exec($ch);
		curl_close($ch);

		return $data;
	}
}