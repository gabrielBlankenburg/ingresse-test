<?php 

namespace App\Helpers;
use GuzzleHttp\Client;

class CpfValidation
{
	private static $url = 'http://geradorapp.com/api/v1/';
	private static $token = '933eb2131f28c928a5e8d732290d8b4b';

	private static function connect($url)
	{
		$client = new Client([
			'base_uri' => self::$url,
			'timeout' => 20	
		]);

		$response = $client->request('GET', $url);

		if ($response->getStatusCode() == 200) {
			return $response->getBody();
		} else {
			return false;
		}
	}

	public static function validate($cpf)
	{
		$resp = self::connect('cpf/validate/'.$cpf.'?token='.self::$token);
		if ($resp) {
			$response = json_decode($resp, true);
			if ($response['status'] == 1) {
				return true;
			}
		}

		return false;
	}

	public static function generate()
	{
		$resp = self::connect('cpf/generate?token='.self::$token);
		if ($resp) {
			$response = json_decode($resp, true);
			if ($response['status'] == 1) {
				return $response['data']['number'];
			}
		}

		return false;
	}
}