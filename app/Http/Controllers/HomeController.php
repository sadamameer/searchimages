<?php

namespace App\Http\Controllers;
use PHPHtmlParser\Dom;
use __;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view("images");
    }

    public function search($keyword)
    {
        $url = "https://www.google.com/search?q=" . urlencode($keyword) . "&tbm=isch";

		$ua = \Campo\UserAgent::random([
		    'os_type' => ['Windows', 'OS X'],
		    'device_type' => 'Desktop'
		]);

		$options  = [
			'http' => [
				'method'     =>"GET",
				'user_agent' =>  $ua,
			],
			'ssl' => [
				"verify_peer"      => FALSE,
				"verify_peer_name" => FALSE,
			],
		];

		$context  = stream_context_create($options);

		$response = file_get_contents($url, FALSE, $context);


		$htmldom = new Dom;
		$htmldom->loadStr($response, []);

        $results = [];

		foreach ($htmldom->find('.rg_di > .rg_meta') as $n => $dataset) {

			$jsondata = $dataset->text;
            $data = json_decode($jsondata);
            
            return $data;

		    $results[$n]['keyword'] = $keyword;
		    $results[$n]['slug'] = __::slug($keyword);

		    $results[$n]['title'] = ucwords(__::slug($data->pt, ['delimiter' => ' ']));
		    // $results[$n]['alt'] = __::slug($data->s, ['delimiter' => ' ']);
		    
		    $results[$n]['url'] = $data->ou;
		    $results[$n]['filetype'] = $data->ity;
		    $results[$n]['width'] = $data->ow;
		    $results[$n]['height'] = $data->oh;
		    $results[$n]['source'] = $data->ru;
		}

		return $results;
    }
}