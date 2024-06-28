<?php

use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\ProductMedia;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image as Image;



Route::group(
[
	'prefix' => LaravelLocalization::setLocale(),
	'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]
], function(){


		// Dashboard (Has Role)
		Route::get('dashboard/login', "Auth\LoginController@showLoginForm")->name("dashboard.login");
		Route::post('dashboard/login', "Auth\LoginController@login")->name("dashboard.post_login");


		// For All
		Route::get('activate/{confirmationCode}', 'Auth\LoginController@confirm')->name('confirmation_path');
		Route::post('setPassword', "Auth\LoginController@storePassword")->name('setPassword');
		Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('forget');
		Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('email');
		Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
		Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('resetToNew');

		Route::middleware('auth')->group(function () {
			Route::post('logout',"Auth\LoginController@logout")->name('logout');
		});
		Route::view('/',"site.index")->name('site.home');
		// Route::view('terms',"site.terms")->name('site.terms');

});


Route::get('test-shippment',function () {

	$curl = curl_init();

	curl_setopt_array($curl, [
		CURLOPT_URL => "https://api-mock.dhl.com/mydhlapi/shipments",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => "{\"plannedShippingDateAndTime\":\"2019-08-04T14:00:31GMT+01:00\",\"pickup\":{\"isRequested\":false,\"closeTime\":\"18:00\",\"location\":\"reception\",\"specialInstructions\":[{\"value\":\"please ring door bell\",\"typeCode\":\"TBD\"}],\"pickupDetails\":{\"postalAddress\":{\"postalCode\":\"14800\",\"cityName\":\"Prague\",\"countryCode\":\"CZ\",\"provinceCode\":\"CZ\",\"addressLine1\":\"V Parku 2308/10\",\"addressLine2\":\"addres2\",\"addressLine3\":\"addres3\",\"countyName\":\"Central Bohemia\",\"provinceName\":\"Central Bohemia\",\"countryName\":\"Czech Republic\"},\"contactInformation\":{\"email\":\"that@before.de\",\"phone\":\"+1123456789\",\"mobilePhone\":\"+60112345678\",\"companyName\":\"Company Name\",\"fullName\":\"John Brew\"},\"registrationNumbers\":[{\"typeCode\":\"VAT\",\"number\":\"CZ123456789\",\"issuerCountryCode\":\"CZ\"}],\"bankDetails\":[{\"name\":\"Russian Bank Name\",\"settlementLocalCurrency\":\"RUB\",\"settlementForeignCurrency\":\"USD\"}],\"typeCode\":\"business\"},\"pickupRequestorDetails\":{\"postalAddress\":{\"postalCode\":\"14800\",\"cityName\":\"Prague\",\"countryCode\":\"CZ\",\"provinceCode\":\"CZ\",\"addressLine1\":\"V Parku 2308/10\",\"addressLine2\":\"addres2\",\"addressLine3\":\"addres3\",\"countyName\":\"Central Bohemia\",\"provinceName\":\"Central Bohemia\",\"countryName\":\"Czech Republic\"},\"contactInformation\":{\"email\":\"that@before.de\",\"phone\":\"+1123456789\",\"mobilePhone\":\"+60112345678\",\"companyName\":\"Company Name\",\"fullName\":\"John Brew\"},\"registrationNumbers\":[{\"typeCode\":\"VAT\",\"number\":\"CZ123456789\",\"issuerCountryCode\":\"CZ\"}],\"bankDetails\":[{\"name\":\"Russian Bank Name\",\"settlementLocalCurrency\":\"RUB\",\"settlementForeignCurrency\":\"USD\"}],\"typeCode\":\"business\"}},\"productCode\":\"D\",\"localProductCode\":\"D\",\"getRateEstimates\":false,\"accounts\":[{\"typeCode\":\"shipper\",\"number\":\"123456789\"}],\"valueAddedServices\":[{\"serviceCode\":\"II\",\"value\":100,\"currency\":\"GBP\",\"method\":\"cash\",\"dangerousGoods\":[{\"contentId\":\"908\",\"dryIceTotalNetWeight\":12,\"customDescription\":\"1 package Lithium ion batteries in compliance with Section II of P.I. 9661\",\"unCodes\":[1234]}]}],\"outputImageProperties\":{\"printerDPI\":300,\"customerBarcodes\":[{\"content\":\"barcode content\",\"textBelowBarcode\":\"text below barcode\",\"symbologyCode\":\"93\"}],\"customerLogos\":[{\"fileFormat\":\"PNG\",\"content\":\"base64 encoded image\"}],\"encodingFormat\":\"pdf\",\"imageOptions\":[{\"typeCode\":\"label\",\"templateName\":\"ECOM26_84_001\",\"isRequested\":true,\"hideAccountNumber\":false,\"numberOfCopies\":1,\"invoiceType\":\"commercial\",\"languageCode\":\"eng\",\"languageCountryCode\":\"br\",\"languageScriptCode\":\"Latn\",\"encodingFormat\":\"png\",\"renderDHLLogo\":false,\"fitLabelsToA4\":false,\"labelFreeText\":\"string\",\"labelCustomerDataText\":\"string\",\"shipmentReceiptCustomerDataText\":\"string\"}],\"splitTransportAndWaybillDocLabels\":true,\"allDocumentsInOneImage\":true,\"splitDocumentsByPages\":true,\"splitInvoiceAndReceipt\":true,\"receiptAndLabelsInOneImage\":true},\"customerReferences\":[{\"value\":\"Customer reference\",\"typeCode\":\"CU\"}],\"identifiers\":[{\"typeCode\":\"shipmentId\",\"value\":\"1234567890\",\"dataIdentifier\":\"00\"}],\"customerDetails\":{\"shipperDetails\":{\"postalAddress\":{\"postalCode\":\"14800\",\"cityName\":\"Prague\",\"countryCode\":\"CZ\",\"provinceCode\":\"CZ\",\"addressLine1\":\"V Parku 2308/10\",\"addressLine2\":\"addres2\",\"addressLine3\":\"addres3\",\"countyName\":\"Central Bohemia\",\"provinceName\":\"Central Bohemia\",\"countryName\":\"Czech Republic\"},\"contactInformation\":{\"email\":\"that@before.de\",\"phone\":\"+1123456789\",\"mobilePhone\":\"+60112345678\",\"companyName\":\"Company Name\",\"fullName\":\"John Brew\"},\"registrationNumbers\":[{\"typeCode\":\"VAT\",\"number\":\"CZ123456789\",\"issuerCountryCode\":\"CZ\"}],\"bankDetails\":[{\"name\":\"Russian Bank Name\",\"settlementLocalCurrency\":\"RUB\",\"settlementForeignCurrency\":\"USD\"}],\"typeCode\":\"business\"},\"receiverDetails\":{\"postalAddress\":{\"postalCode\":\"14800\",\"cityName\":\"Prague\",\"countryCode\":\"CZ\",\"provinceCode\":\"CZ\",\"addressLine1\":\"V Parku 2308/10\",\"addressLine2\":\"addres2\",\"addressLine3\":\"addres3\",\"countyName\":\"Central Bohemia\",\"provinceName\":\"Central Bohemia\",\"countryName\":\"Czech Republic\"},\"contactInformation\":{\"email\":\"that@before.de\",\"phone\":\"+1123456789\",\"mobilePhone\":\"+60112345678\",\"companyName\":\"Company Name\",\"fullName\":\"John Brew\"},\"registrationNumbers\":[{\"typeCode\":\"VAT\",\"number\":\"CZ123456789\",\"issuerCountryCode\":\"CZ\"}],\"bankDetails\":[{\"name\":\"Russian Bank Name\",\"settlementLocalCurrency\":\"RUB\",\"settlementForeignCurrency\":\"USD\"}],\"typeCode\":\"business\"},\"buyerDetails\":{\"postalAddress\":{\"postalCode\":\"14800\",\"cityName\":\"Prague\",\"countryCode\":\"CZ\",\"provinceCode\":\"CZ\",\"addressLine1\":\"V Parku 2308/10\",\"addressLine2\":\"addres2\",\"addressLine3\":\"addres3\",\"countyName\":\"Central Bohemia\",\"provinceName\":\"Central Bohemia\",\"countryName\":\"Czech Republic\"},\"contactInformation\":{\"email\":\"buyer@domain.com\",\"phone\":\"+44123456789\",\"mobilePhone\":\"+42123456789\",\"companyName\":\"Customer Company Name\",\"fullName\":\"Mark Companer\"},\"registrationNumbers\":[{\"typeCode\":\"VAT\",\"number\":\"CZ123456789\",\"issuerCountryCode\":\"CZ\"}],\"bankDetails\":[{\"name\":\"Russian Bank Name\",\"settlementLocalCurrency\":\"RUB\",\"settlementForeignCurrency\":\"USD\"}],\"typeCode\":\"business\"},\"importerDetails\":{\"postalAddress\":{\"postalCode\":\"14800\",\"cityName\":\"Prague\",\"countryCode\":\"CZ\",\"provinceCode\":\"CZ\",\"addressLine1\":\"V Parku 2308/10\",\"addressLine2\":\"addres2\",\"addressLine3\":\"addres3\",\"countyName\":\"Central Bohemia\",\"provinceName\":\"Central Bohemia\",\"countryName\":\"Czech Republic\"},\"contactInformation\":{\"email\":\"that@before.de\",\"phone\":\"+1123456789\",\"mobilePhone\":\"+60112345678\",\"companyName\":\"Company Name\",\"fullName\":\"John Brew\"},\"registrationNumbers\":[{\"typeCode\":\"VAT\",\"number\":\"CZ123456789\",\"issuerCountryCode\":\"CZ\"}],\"bankDetails\":[{\"name\":\"Russian Bank Name\",\"settlementLocalCurrency\":\"RUB\",\"settlementForeignCurrency\":\"USD\"}],\"typeCode\":\"business\"},\"exporterDetails\":{\"postalAddress\":{\"postalCode\":\"14800\",\"cityName\":\"Prague\",\"countryCode\":\"CZ\",\"provinceCode\":\"CZ\",\"addressLine1\":\"V Parku 2308/10\",\"addressLine2\":\"addres2\",\"addressLine3\":\"addres3\",\"countyName\":\"Central Bohemia\",\"provinceName\":\"Central Bohemia\",\"countryName\":\"Czech Republic\"},\"contactInformation\":{\"email\":\"that@before.de\",\"phone\":\"+1123456789\",\"mobilePhone\":\"+60112345678\",\"companyName\":\"Company Name\",\"fullName\":\"John Brew\"},\"registrationNumbers\":[{\"typeCode\":\"VAT\",\"number\":\"CZ123456789\",\"issuerCountryCode\":\"CZ\"}],\"bankDetails\":[{\"name\":\"Russian Bank Name\",\"settlementLocalCurrency\":\"RUB\",\"settlementForeignCurrency\":\"USD\"}],\"typeCode\":\"business\"},\"sellerDetails\":{\"postalAddress\":{\"postalCode\":\"14800\",\"cityName\":\"Prague\",\"countryCode\":\"CZ\",\"provinceCode\":\"CZ\",\"addressLine1\":\"V Parku 2308/10\",\"addressLine2\":\"addres2\",\"addressLine3\":\"addres3\",\"countyName\":\"Central Bohemia\",\"provinceName\":\"Central Bohemia\",\"countryName\":\"Czech Republic\"},\"contactInformation\":{\"email\":\"that@before.de\",\"phone\":\"+1123456789\",\"mobilePhone\":\"+60112345678\",\"companyName\":\"Company Name\",\"fullName\":\"John Brew\"},\"registrationNumbers\":[{\"typeCode\":\"VAT\",\"number\":\"CZ123456789\",\"issuerCountryCode\":\"CZ\"}],\"bankDetails\":[{\"name\":\"Russian Bank Name\",\"settlementLocalCurrency\":\"RUB\",\"settlementForeignCurrency\":\"USD\"}],\"typeCode\":\"business\"},\"payerDetails\":{\"postalAddress\":{\"postalCode\":\"14800\",\"cityName\":\"Prague\",\"countryCode\":\"CZ\",\"provinceCode\":\"CZ\",\"addressLine1\":\"V Parku 2308/10\",\"addressLine2\":\"addres2\",\"addressLine3\":\"addres3\",\"countyName\":\"Central Bohemia\",\"provinceName\":\"Central Bohemia\",\"countryName\":\"Czech Republic\"},\"contactInformation\":{\"email\":\"that@before.de\",\"phone\":\"+1123456789\",\"mobilePhone\":\"+60112345678\",\"companyName\":\"Company Name\",\"fullName\":\"John Brew\"},\"registrationNumbers\":[{\"typeCode\":\"VAT\",\"number\":\"CZ123456789\",\"issuerCountryCode\":\"CZ\"}],\"bankDetails\":[{\"name\":\"Russian Bank Name\",\"settlementLocalCurrency\":\"RUB\",\"settlementForeignCurrency\":\"USD\"}],\"typeCode\":\"business\"},\"ultimateConsigneeDetails\":{\"postalAddress\":{\"postalCode\":\"14800\",\"cityName\":\"Prague\",\"countryCode\":\"CZ\",\"provinceCode\":\"CZ\",\"addressLine1\":\"V Parku 2308/10\",\"addressLine2\":\"addres2\",\"addressLine3\":\"addres3\",\"countyName\":\"Central Bohemia\",\"provinceName\":\"Central Bohemia\",\"countryName\":\"Czech Republic\"},\"contactInformation\":{\"email\":\"that@before.de\",\"phone\":\"+1123456789\",\"mobilePhone\":\"+60112345678\",\"companyName\":\"Company Name\",\"fullName\":\"John Brew\"},\"registrationNumbers\":[{\"typeCode\":\"VAT\",\"number\":\"CZ123456789\",\"issuerCountryCode\":\"CZ\"}],\"bankDetails\":{\"typeCode\":\"VAT\",\"number\":\"CZ123456789\",\"issuerCountryCode\":\"CZ\"},\"typeCode\":\"string\"},\"brokerDetails\":{\"postalAddress\":{\"postalCode\":\"14800\",\"cityName\":\"Prague\",\"countryCode\":\"CZ\",\"provinceCode\":\"CZ\",\"addressLine1\":\"V Parku 2308/10\",\"addressLine2\":\"addres2\",\"addressLine3\":\"addres3\",\"countyName\":\"Central Bohemia\",\"provinceName\":\"Central Bohemia\",\"countryName\":\"Czech Republic\"},\"contactInformation\":{\"email\":\"that@before.de\",\"phone\":\"+1123456789\",\"mobilePhone\":\"+60112345678\",\"companyName\":\"Company Name\",\"fullName\":\"John Brew\"},\"registrationNumbers\":[{\"typeCode\":\"VAT\",\"number\":\"CZ123456789\",\"issuerCountryCode\":\"CZ\"}],\"bankDetails\":{\"typeCode\":\"VAT\",\"number\":\"CZ123456789\",\"issuerCountryCode\":\"CZ\"},\"typeCode\":\"string\"}},\"content\":{\"packages\":[{\"typeCode\":\"2BP\",\"weight\":22.501,\"dimensions\":{\"length\":15.001,\"width\":15.001,\"height\":40.001},\"customerReferences\":[{\"value\":\"Customer reference\",\"typeCode\":\"CU\"}],\"identifiers\":[{\"typeCode\":\"shipmentId\",\"value\":\"1234567890\",\"dataIdentifier\":\"00\"}],\"description\":\"Piece content description\",\"labelBarcodes\":[{\"position\":\"left\",\"symbologyCode\":\"93\",\"content\":\"string\",\"textBelowBarcode\":\"text below left barcode\"}],\"labelText\":[{\"position\":\"left\",\"caption\":\"text caption\",\"value\":\"text value\"}],\"labelDescription\":\"bespoke label description\",\"referenceNumber\":1}],\"isCustomsDeclarable\":true,\"declaredValue\":150,\"declaredValueCurrency\":\"CZK\",\"exportDeclaration\":{\"lineItems\":[{\"number\":1,\"description\":\"line item description\",\"price\":150,\"quantity\":{\"value\":1,\"unitOfMeasurement\":\"BOX\"},\"commodityCodes\":[{\"typeCode\":\"outbound\",\"value\":851713}],\"exportReasonType\":\"permanent\",\"manufacturerCountry\":\"CZ\",\"weight\":{\"netValue\":10,\"grossValue\":10},\"isTaxesPaid\":true,\"additionalInformation\":[\"string\"],\"customerReferences\":[{\"typeCode\":\"AFE\",\"value\":\"custref123\"}],\"customsDocuments\":[{\"typeCode\":\"972\",\"value\":\"custdoc456\"}],\"preCalculatedLineItemTotalValue\":150}],\"invoice\":{\"number\":\"12345-ABC\",\"date\":\"2020-03-18\",\"signatureName\":\"Brewer\",\"signatureTitle\":\"Mr.\",\"signatureImage\":\"Base64 encoded image\",\"instructions\":[\"string\"],\"customerDataTextEntries\":[\"string\"],\"totalNetWeight\":999999999999,\"totalGrossWeight\":999999999999,\"customerReferences\":[{\"typeCode\":\"CU\",\"value\":\"custref112\"}],\"termsOfPayment\":\"100 days\",\"indicativeCustomsValues\":{\"importCustomsDutyValue\":150.57,\"importTaxesValue\":49.43,\"totalWithImportDutiesAndTaxes\":[350.57]},\"preCalculatedTotalValues\":{\"preCalculatedTotalGoodsValue\":49.43,\"preCalculatedTotalInvoiceValue\":150.57}},\"remarks\":[{\"value\":\"declaration remark\"}],\"additionalCharges\":[{\"value\":10,\"caption\":\"fee\",\"typeCode\":\"freight\"}],\"destinationPortName\":\"port details\",\"placeOfIncoterm\":\"port of departure or destination details\",\"payerVATNumber\":\"12345ED\",\"recipientReference\":\"recipient reference\",\"exporter\":{\"id\":\"123\",\"code\":\"EXPCZ\"},\"packageMarks\":\"marks\",\"declarationNotes\":[{\"value\":\"up to three declaration notes\"}],\"exportReference\":\"export reference\",\"exportReason\":\"export reason\",\"exportReasonType\":\"permanent\",\"licenses\":[{\"typeCode\":\"export\",\"value\":\"license\"}],\"shipmentType\":\"personal\",\"customsDocuments\":[{\"typeCode\":\"972\",\"value\":\"custdoc445\"}]},\"description\":\"shipment description\",\"USFilingTypeValue\":\"12345\",\"incoterm\":\"DAP\",\"unitOfMeasurement\":\"metric\"},\"documentImages\":[{\"typeCode\":\"INV\",\"imageFormat\":\"PDF\",\"content\":\"base64 encoded image\"}],\"onDemandDelivery\":{\"deliveryOption\":\"servicepoint\",\"location\":\"front door\",\"specialInstructions\":\"ringe twice\",\"gateCode\":\"1234\",\"whereToLeave\":\"concierge\",\"neighbourName\":\"Mr.Dan\",\"neighbourHouseNumber\":\"777\",\"authorizerName\":\"Newman\",\"servicePointId\":\"SPL123\",\"requestedDeliveryDate\":\"2020-04-20\"},\"requestOndemandDeliveryURL\":false,\"shipmentNotification\":[{\"typeCode\":\"email\",\"receiverId\":\"receiver@email.com\",\"languageCode\":\"eng\",\"languageCountryCode\":\"UK\",\"bespokeMessage\":\"message to be included in the notification\"}],\"prepaidCharges\":[{\"typeCode\":\"freight\",\"currency\":\"CZK\",\"value\":200,\"method\":\"cash\"}],\"getTransliteratedResponse\":false,\"estimatedDeliveryDate\":{\"isRequested\":false,\"typeCode\":\"QDDC\"},\"getAdditionalInformation\":[{\"typeCode\":\"pickupDetails\",\"isRequested\":true}],\"parentShipment\":{\"productCode\":\"s\",\"packagesCount\":1}}",
		CURLOPT_HTTPHEADER => [
			"Authorization: Basic REPLACE_BASIC_AUTH",
			"Message-Reference: SOME_STRING_VALUE",
			"Message-Reference-Date: SOME_STRING_VALUE",
			"Plugin-Name: SOME_STRING_VALUE",
			"Plugin-Version: SOME_STRING_VALUE",
			"Shipping-System-Platform-Name: SOME_STRING_VALUE",
			"Shipping-System-Platform-Version: SOME_STRING_VALUE",
			"Webstore-Platform-Name: SOME_STRING_VALUE",
			"Webstore-Platform-Version: SOME_STRING_VALUE",
			"content-type: application/json"
		],
	]);

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
		echo "cURL Error #:" . $err;
	} else {
		echo $response;
	}


});


Route::get('update-products',function () {

	// $order_products = OrderProduct::get();

	// foreach($order_products as $order_product) {

	// 	$order_product->update([
	// 		'product_id' => @$order_product->productDetail->product_id
	// 	]);
	// }

	// $category_transilation = CategoryTranslation::get();

	// foreach($category_transilation as $item) {

	// 	if(! $item->category) {
	// 		$item->delete();
	// 	}
	// }

	// $categories = Category::get();

	// foreach($categories as $category) {

	// 	$parent = $category->parent;

	// 	$slug_ar = str_replace(" ","_",$category->translate('ar')->name);
	// 	$slug_en = str_replace(" ","_",$category->translate('en')->name);

	// 	$category->update([
	// 		'ar' => [ 'slug' => $slug_ar ]
	// 	]);

	// 	$category->update([
	// 		'en' => ['slug' => $slug_en],
	// 	]);

	// 	if($parent){

	// 		$slug_ar = str_replace(" ","_",$category->translate('ar')->name) . '-' . $parent->translate('ar')->slug;
	// 		$slug_en = str_replace(" ","_",$category->translate('en')->name) . '-' . $parent->translate('en')->slug;

	// 		$category->update([
	// 			'ar' => ['slug' => $slug_ar]
	// 		]);

	// 		$category->update([
	// 			'en' => ['slug' => $slug_en],
	// 		]);
	// 	}

	//  }

	// dd('ok');

	// $ProductDetails = ProductDetails::where('product_id',67)->orderBy('created_at','asc')->get();

	// $colorsArr = $ProductDetails->pluck('color_id')->toArray();
	// $colorsArr = array_unique($colorsArr);

	// foreach($ProductDetails as $row) {

	// 	foreach($colorsArr as $color_id) {

	// 		$product_media = ProductMedia::where('product_details_id', '=', $row->id)->where('color_id',$row->color_id)->first();

	// 	}



	// 	$first_product_color_details = ProductDetails::where('product_id', '=', $row->product_id)->where('color_id',$product_details_row->color_id)->first();


	// 	if(file_exists($oldPath)) {
	// 		foreach($colorsArr as $color_id) {

	// 		}
	// 	}


	// }

    // $link1 = ('https://outfitnew.products.aait-d.com/storage/app/public/images/products/187/1174/664260799411f_1715626105.webp');

    // $link2 = (asset('storage/images/products/187/1174/664260799411f_1715626105.webp'));

    // $link3 = ('images/products/187/1174/664260799411f_1715626105.webp');

    // dd(file_exists($link1),file_exists($link2),\Storage::disk('public')->exists($link3),$link1,$link2);

	// $routesNamesList = array();

	// $routeCollection = Route::getRoutes();

	// foreach ($routeCollection as $index => $value) {

	// 	if($value->getActionName() != null && startsWith($value->getActionName(),'App\Http\Controllers\Api\Dashboard')) {

	// 		$routeName = $value->getName();

	// 		// if($routeName && ! startsWith($routeName, "ignition")) { }

	// 		if(\Str::endsWith($routeName, '.index')) {

	// 			$routesNamesList[$index]['route_name'] = $routeName;

	// 			$subject = $routeName;
	// 			$search = '.index' ;
	// 			$trimmed = str_replace($search, '', $subject) ;

	// 			$routesNamesList[$index]['front_name'] = $trimmed.'/show-all';

	// 		} elseif(\Str::endsWith($routeName, '.store')) {

	// 			$routesNamesList[$index]['route_name'] = $routeName;

	// 			$subject = $routeName;
	// 			$search = '.store' ;
	// 			$trimmed = str_replace($search, '', $subject) ;

	// 			$routesNamesList[$index]['front_name'] = $trimmed.'/add';

	// 		} elseif(\Str::endsWith($routeName, '.show')) {

	// 			$routesNamesList[$index]['route_name'] = $routeName;

	// 			$subject = $routeName;
	// 			$search = '.show' ;
	// 			$trimmed = str_replace($search, '', $subject) ;

	// 			$routesNamesList[$index]['front_name'] = $trimmed.'/show';

	// 		} elseif(\Str::endsWith($routeName, '.update')) {

	// 			$routesNamesList[$index]['route_name'] = $routeName;

	// 			$subject = $routeName;
	// 			$search = '.update' ;
	// 			$trimmed = str_replace($search, '', $subject) ;

	// 			$routesNamesList[$index]['front_name'] = $trimmed.'/edit';

	// 		}
	// 	}
	// }

	// dd($routesNamesList);



	// dd('ok',$routesNamesList);

	// $routeCollection = Route::getRoutes();

    // echo "<table style='width:100%'>";
    // echo "<tr>";
    // echo "<td width='10%'><h4>HTTP Method</h4></td>";
    // echo "<td width='10%'><h4>Route</h4></td>";
    // echo "<td width='10%'><h4>Name</h4></td>";
    // echo "<td width='70%'><h4>Corresponding Action</h4></td>";
    // echo "</tr>";
    // foreach ($routeCollection as $value) {



	// 	if(startsWith($value->getActionName(),'App\Http\Controllers\Api\Dashboard')) {

	// 		echo "<tr>";
	// 		echo "<td>" . $value->methods()[0] . "</td>";
	// 		echo "<td>" . $value->uri() . "</td>";
	// 		echo "<td>" . $value->getName() . "</td>";
	// 		echo "<td>" . $value->getActionName() . "</td>";
	// 		echo "</tr>";

	// 	}


    // }


    // echo "</table>";

	// dd(permissions_names_v2());

    // $products_media = ProductMedia::where('option',null)->get();

    // foreach($products_media as $product_media) {

    //      $first_product_color_media = ProductMedia::where('product_id', $product_media->product_id)->where('color_id',$product_media->color_id)->where('option',null)->orderBy('created_at','asc')->first();

    //     // dd($first_product_color_media);

    //     if($first_product_color_media) {

    //         $url     = 'products/'.$product_media->product_id.'/'.$first_product_color_media->product_details_id;
    //         $oldPath = 'images/' . $url . "/" . $first_product_color_media->media;

    //         $new_url = 'products/'.$product_media->product_id.'/'.$product_media->product_details_id;
    //         $newPath = ('storage/images/'.$new_url. '/' . $product_media->media);

    //         info($oldPath);
    //         info($newPath);

    //         if(\Storage::disk('public')->exists($oldPath)) {

    //             info('make 2 dir ok - path is '.asset('storage/app/public/images/' .$new_url));

    //             try {

    //                 // File::makeDirectory(asset('storage/app/public/images/' .$new_url), 0777, true);
    //                 File::makeDirectory(storage_path('app/public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $new_url . DIRECTORY_SEPARATOR), 0777, true);

    //                 // Source image path
    //                 $sourceImagePath = ('storage/'.$oldPath);

    //                 // Destination image path
    //                 $destinationImagePath = ($newPath);

    //                 // Load the source image using Intervention Image
    //                 $image = Image::make($sourceImagePath);

    //                 // Save the manipulated image to the destination path
    //                 $image->save($destinationImagePath);

    //             } catch(Exception $e) {
    //                 info($e->getMessage());
    //                 info($e);
    //             }
    //         }

    //     }

    // }



    // $categories = Category::get();

	// foreach($categories as $category) {

	// 	$parent = $category->parent;

	// 	$slug_ar = str_replace(" ","_",$category->translate('ar')->name);
	// 	$slug_en = str_replace(" ","_",$category->translate('en')->name);

	// 	$category->update([
	// 		'ar' => [ 'slug' => $slug_ar ]
	// 	]);

	// 	$category->update([
	// 		'en' => ['slug' => $slug_en],
	// 	]);

	// 	if($parent){

	// 		$slug_ar = str_replace(" ","_",$category->translate('ar')->name) . '-' . $parent->translate('ar')->slug;
	// 		$slug_en = str_replace(" ","_",$category->translate('en')->name) . '-' . $parent->translate('en')->slug;

	// 		$category->update([
	// 			'ar' => ['slug' => $slug_ar]
	// 		]);

	// 		$category->update([
	// 			'en' => ['slug' => $slug_en],
	// 		]);
	// 	}

	//  }

	// dd('ok');


	// $products_media = ProductMedia::where('option',null)->get();

    // foreach($products_media as $product_media) {

    //      $first_product_color_media = ProductMedia::where('product_id', $product_media->product_id)->where('color_id',$product_media->color_id)->where('option',null)->orderBy('created_at','asc')->first();

    //     // dd($first_product_color_media);

    //     if($first_product_color_media) {

    //         $url     = 'products/'.$product_media->product_id.'/'.$first_product_color_media->product_details_id;
    //         $oldPath = 'images/' . $url . "/" . $first_product_color_media->media;

    //         $new_url = 'products/'.$product_media->product_id.'/'.$product_media->product_details_id;
    //         $newPath = ('storage/images/'.$new_url. '/' . $product_media->media);

    //         info($oldPath);
    //         info($newPath);

    //         if(\Storage::disk('public')->exists($oldPath)) {

    //             info('make 2 dir ok - path is '.asset('storage/app/public/images/' .$new_url));

    //             try {

    //                 // File::makeDirectory(asset('storage/app/public/images/' .$new_url), 0777, true);
    //                 File::makeDirectory(storage_path('app/public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $new_url . DIRECTORY_SEPARATOR), 0777, true);

    //                 // Source image path
    //                 $sourceImagePath = ('storage/'.$oldPath);

    //                 // Destination image path
    //                 $destinationImagePath = ($newPath);

    //                 // Load the source image using Intervention Image
    //                 $image = Image::make($sourceImagePath);

    //                 // Save the manipulated image to the destination path
    //                 $image->save($destinationImagePath);

    //             } catch(Exception $e) {
    //                 info($e->getMessage());
    //                 info($e);
    //             }
    //         }

    //     }

    // }


	$productDetails = ProductDetails::groupBy('color_id')->orderBy('created_at','asc')->get();

	foreach($productDetails as $first_details) {

		$others = ProductDetails::where('id','!=',$first_details->id)->where('product_id',$first_details->product_id)->where('color_id',$first_details->color_id)->orderBy('created_at','asc')->get();

		foreach($others as $row) { 

			$check_medias = ProductMedia::where('product_id',$row->product_id)->where('color_id',$row->color_id)->where('product_details_id',$row->id)->get();

			if($check_medias != null && $check_medias->count() > 0) {
				foreach($check_medias as $media_row) {

					$url     = 'products/'.$media_row->product_id.'/'.$media_row->product_details_id;
					$oldPath = 'images/' . $url . "/" . $media_row->media;

					info($oldPath .' oldPath');

					if(! \Storage::disk('public')->exists($oldPath)) {

						info($oldPath .' file not exists');

						$current_url     = 'products/'.$media_row->product_id.'/'.$first_details->id;
						$currentPath = 'images/' . $current_url . "/" . $media_row->media;

						info($currentPath .' is current path');
						info($oldPath .' is new path');

						if(\Storage::disk('public')->exists($currentPath)) {
							
							try {

								// File::makeDirectory(asset('storage/app/public/images/' .$new_url), 0777, true);
			
								$path = 'app/public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $url . DIRECTORY_SEPARATOR;

								if(! File::exists(storage_path($path))) {
									File::makeDirectory(storage_path($path), 0777, true);
								}

								// Source image path
								$sourceImagePath = ('storage/'.$currentPath);
			
								// Destination image path
								$destinationImagePath = ('storage/'.$oldPath);

								// Copy the file
								if (\File::copy($sourceImagePath, $destinationImagePath)) {
									info(['message' => 'File copied successfully']);
								} else {
									info(['message' => 'Failed to copy file']);
								}

								// sleep(1);

			
							} catch(Exception $e) {
								info($e->getMessage());
								info($e);
							}
						}
					} else {
						info($oldPath .' file is exists');
					}
				}
			}

		}
	}

	
	dd('ok');



});
