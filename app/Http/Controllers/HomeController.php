<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view("images");
	}
	
	public function download(Request $request)
	{
		$filename = 'temp-image.jpg';
		$tempImage = tempnam(sys_get_temp_dir(), $filename);
		copy($request->imageURL, $tempImage);
		return response()->download($tempImage, $filename);
	}

	public function getJaccardIndexedRecords(Request $request)
	{
		$images = $request->images;
		$JIimages = [];
		foreach ($images as $key => $image) {
			$images[$key]["coefficient"] = $this->setSimilarityCoefficient($request->text , $image);
		}

		$sortedImages = collect($images)->sortByDesc("coefficient");
		foreach ($sortedImages as $key => $img) {array_push($JIimages , $img);}
		return $JIimages;
	}

	public function setSimilarityCoefficient( $query, $image, $separator = " " ) {
		$item1 				= explode( $separator, $query );
		$item2 				= explode( $separator, $image["tags"] );
		$arr_intersection	= array_intersect( $item2, $item2 );
		$arr_union 			= array_merge( $item1, $item2 );
		$coefficient 		= count( $arr_intersection ) / count( $arr_union );
		return $coefficient;
	}
}