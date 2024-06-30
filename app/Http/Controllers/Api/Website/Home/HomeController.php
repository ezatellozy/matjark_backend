<?php

namespace App\Http\Controllers\Api\Website\Home;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\App\Offer\OfferRequest;
use App\Http\Resources\Api\Website\Category\{CategoryResource};
use App\Http\Resources\Api\Website\Home\{SiteMetaResource, CommonQuestionResource, MainCategoryResource, SimpleCategoryResource, SimpleFlashSaleResource, SimpleOfferResource, SimpleProductResource, SliderItemResource, SliderResource, SubCategoryResource};
use App\Models\{Category, CommonQuestion, FlashSale, Offer, Product, ProductDetails, Slider};
use App\Http\Resources\Api\Website\Product\SimpleProductDetailsResource;
use App\Models\Setting;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests\Api\Website\NewsLetter\NewsLetterRequest;
use App\Models\NewsLetter;


class HomeController extends Controller
{
    public function save_news_letter(NewsLetterRequest $request)
    {

        $check = NewsLetter::where('email', $request->email)->first();

        if ($check == null) {

            NewsLetter::create([
                'email' => $request->email
            ]);
        }

        $lang = app()->getLocale();

        return response()->json(['data' => null, 'status' => 'success', 'message' => $lang == 'en' ? 'you subscribed successfully' : 'تم الأشتراك بنجاح']);
    }


    public function test()
    {

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
    }



    public function homeFlashSale(Request $request)
    {

        $now = Carbon::now();

        $flash_sales = FlashSale::where('is_active', true)
            ->whereDate('start_at', '<=',  $now)
            ->whereDate('end_at', '>=',  $now)
            ->whereHas('flashSaleProducts', function ($q) {
                $q->where(\DB::raw('quantity - sold'), '>=', 0);
            })
            ->first();

        $data = $flash_sales ? SimpleFlashSaleResource::make($flash_sales) : null;

        return response()->json(['data' => $data, 'status' => 'success', 'message' => '']);
    }



    public function index(Request $request)
    {
        $now = Carbon::now();

        $offers =  Offer::where('is_active', true)
            ->where('remain_use', '>', 0)
            ->whereDate('start_at', '<=',  $now)
            ->whereDate('end_at', '>=',  $now)
            ->whereIn('display_platform', ['website', 'both'])
            ->orderBy('ordering', 'desc')->take(5)->get();

        $main_categories  = Category::where(['is_active' => true, 'parent_id' =>  null, 'position' => 'main'])->orderBy('ordering', 'asc')->take(5)->get();
        $main_category_id  = $request->main_category_id  != null ? $request->main_category_id : ($main_categories->count() > 0 ? $main_categories[0]['id'] : null);
        $sub_categories =  Category::where(['is_active' => true, 'parent_id' =>  $main_category_id])->orderBy('ordering', 'asc')/*->take(5)*/->get();
        $second_category = $request->second_category_id != null ? $request->second_category_id : ($sub_categories != null && $sub_categories->count() > 0 && $sub_categories->first() ? $sub_categories->first()->id : null);
        $category = Category::where(['is_active' => true, 'id' => $main_category_id])->firstOrFail();
        $categories  =  lastLevel($category);

        $most_orders = Product::where('is_active', true)->whereHas('categoryProducts', function ($q) use ($categories, $category) {
            if (count($categories) > 0) {
                $q->whereIn('category_id', $categories->pluck('id')->toArray());
            } else {
                $q->where('category_id', $category->id);
            }
        })->whereHas('productDetails', function ($q) {
            $q->where('quantity', '>=', 0);
        })->join('product_details', 'products.id', '=', 'product_details.product_id')
            ->orderBy('product_details.sold', 'desc')
            ->groupBy('product_id')
            ->select('products.*')->distinct()->take(4)->get();


        $top_rated = Product::where(['is_active' => true])->whereHas('categoryProducts', function ($q) use ($categories, $category) {
            if (count($categories) > 0) {
                $q->whereIn('category_id', $categories->pluck('id')->toArray());
            } else {
                $q->where('category_id', $category->id);
            }
        })->whereHas('productDetails', function ($q) {
            $q->where('quantity', '>=', 0);
        })
            ->join('product_details', 'products.id', '=', 'product_details.product_id')
            ->orderBy('product_details.rate_avg', 'desc')
            ->groupBy('product_id')
            ->select('products.*')->distinct()->take(4)->get();


        $slider = Slider::where(['is_active' => true, 'type' => 'main', 'category_id' => $main_category_id])->whereIn('platform', ['website', 'all'])->orderBy('ordering', 'asc')->first();


        $flash_sales = FlashSale::where('is_active', true)
            ->whereDate('start_at', '<=',  $now)
            ->whereDate('end_at', '>=',  $now)
            ->whereHas('flashSaleProducts', function ($q) use ($main_category_id) {
                $q->where(\DB::raw('quantity - sold'), '>=', 0);
                // $q->whereHas('product', function ($q) use ($main_category_id) {
                //     $q->where(['is_active' => true, 'main_category_id' => $main_category_id]);
                // });
            })
            ->first();

        $new_arrivals_highlights  = Product::where(['is_active' => true])->whereHas('categoryProducts', function ($q) use ($categories, $category) {
            if (count($categories) > 0) {
                $q->whereIn('category_id', $categories->pluck('id')->toArray());
            } else {
                $q->where('category_id', $category->id);
            }
        })->whereHas('productDetails', function ($q) {
            $q->where('quantity', '>=', 0);
        })->orderBy('created_at', 'desc')->take(4)->get();


        $divided_sliders = SliderResource::collection(Slider::where(['is_active' => true, 'type' => 'divided',  'category_id' => $main_category_id])->whereIn('platform', ['website', 'all'])->orderBy('ordering', 'desc')->take(2)->get());


        $firstBanner = Slider::where(['is_active' => true, 'type' => 'banner',  'category_id' => $main_category_id])->whereIn('platform', ['website', 'all'])->orderBy('ordering', 'asc')->first();

        if ($firstBanner != null) {
            $secondBanner = Slider::where('id', '!=', $firstBanner->id)->where(['is_active' => true, 'type' => 'banner',  'category_id' => $main_category_id])->whereIn('platform', ['website', 'all'])->orderBy('ordering', 'asc')->first();
        } else {
            $secondBanner = [];
        }
        $locale = app()->getLocale();

        $meta_data = [
            'id'          => 1,
            'meta_tag' => Setting::where('key', "site_meta_tag_$locale")->first()->value,
            'meta_title' => Setting::where('key', "site_meta_title_$locale")->first()->value,
            'meta_description' => Setting::where('key', "site_meta_description_$locale")->first()->value,
            'meta_canonical_tag' => asset('storage/images/setting').'/'.Setting::where('key', "website_logo")->first()->value,
        ];
        $top_rated = [
            'type' => 'top_rated',
            'view_type' => 'products',
            'text' => trans('app.messages.top_rated'),
            'data' =>  SimpleProductResource::collection($top_rated),
        ];
        $most_orders = [
            'type' => 'most_orders',
            'view_type' => 'products',
            'text' => trans('app.messages.most_orders'),
            'data' =>  SimpleProductResource::collection($most_orders),
        ];
        $banner = [
            'type' => 'banner',
            'view_type' => 'banner',
            'text' => null,
            'data' => $firstBanner != null ?  new SliderResource($firstBanner) : null,
        ];
        $divided_slider =[
            'type' => 'divided_slider',
            'view_type' => 'divided_slider',
            'text' => null,
            'data' =>  $divided_sliders,
        ];

        $flash_sale = [
            'type' => 'flash_sale',
            'view_type' => 'flash_sale',
            'text' => trans('app.messages.flash_sale'),
            'data' =>  $flash_sales ? SimpleFlashSaleResource::make($flash_sales) : null,
        ];
        $new_arrivals_highlight = [
            'type' => 'new_arrivals_highlights',
            'view_type' => 'products',
            'text' => trans('website.messages.new_arrivals_highlights'),
            'data' =>  SimpleProductResource::collection($new_arrivals_highlights),
        ];
        // dd($main_category_id);


        $setting = Setting::all();

        $data = [
            'main_banner'               =>   SimpleOfferResource::collection($offers),
            'shop_by_category'          =>   SubCategoryResource::collection($sub_categories),
            'most_orders'               =>   $most_orders,
            'banner'                    =>   $banner,
            'top_rated'                 =>   $top_rated,
            'divided_slider'            =>   $divided_slider,
            'flash_sale'                =>   $flash_sale,
            'new_arrivals_highlights'   =>   $new_arrivals_highlight,
            'secound_banner'            =>   $secondBanner != null ?  new SliderResource($secondBanner) : null,
            'meta_data'                 =>   $meta_data,
            //[
            //  'type' => 'main_banner',
            //  'view_type' => 'main_banner',
            //  'text' => null,
            //  'data' =>  SimpleOfferResource::collection($offers),
            //],
            //[
            //  'type' => 'shop_by_category',
            //  'view_type' => 'sub_category',
            //  'text' => trans('app.messages.shop_by_category'),
            //  'data' =>  SubCategoryResource::collection($sub_categories),
            //],






            //[
            //  'type' => 'banner',
            //  'view_type' => 'banner',
            //  'text' => null,
            //  'data' => $secondBanner != null ?  new SliderResource($secondBanner) : null,
            //],

            // [
            //     'type' => 'site_meta',
            //     'view_type' => 'site_meta',
            //     'text' => null,
            //     'data' =>  new SiteMetaResource($setting),
            //     // 'data' => [
            //     //     'site_meta_tag_ar' => $setting->where('key','site_meta_tag_ar')->first()->key,
            //     //     'site_meta_tag_en' => $setting->where('key','site_meta_tag_en')->first()->key,
            //     //     'site_meta_title_ar' => $setting->where('key','site_meta_title_ar')->first()->key,
            //     //     'site_meta_title_en' => $setting->where('key','site_meta_title_en')->first()->key,
            //     //     'site_meta_description_ar' => $setting->where('key','site_meta_description_ar')->first()->key,
            //     //     'site_meta_description_en' => $setting->where('key','site_meta_description_en')->first()->key,
            //     // ]
            // ],


            // [
            //     'type' => 'sliders',
            //     'view_type'=>'sliders',
            //     'text' => null,
            //     'data' =>  SliderResource::collection(Slider::where(['is_active' => true, 'category_id' => $main_category_id])->orderBy('ordering', 'asc')->take(3)->get()),
            // ],

        ];



        return response()->json(['data' => $data, 'status' => 'success', 'message' => '']);
    }


    public function slider_details($id) {

        $slider = Slider::findOrFail($id);

        return SliderItemResource::make($slider)->additional(['status' => 'success', 'message' => '']);
    }


    public function flashSale(Request $request)
    {
        $categories = $request->main_category_id != null ?  thirdLavels(Category::find($request->main_category_id)) : null;

        $flash_salesProducts = ProductDetails::when($categories, function ($q) use ($categories) {
            $q->whereHas('product', function ($q) use ($categories) {
                $q->where('is_active', true);
                $q->whereHas('categoryProducts', function ($q) use ($categories) {
                    $q->whereIn('category_id', $categories->pluck('id')->toArray());
                });
            });
        })
            ->whereHas('flashSalesProduct', function ($q) use ($request) {
                $q->where(\DB::raw('quantity - sold'), '>=', 0)->whereHas('flashSale', function ($q) use ($request) {
                    $q->where('is_active', true);
                    $q->when($request->type == 'now', function ($query) {
                        $query->where('start_at', '<=',  now());
                        $query->where('end_at', '>=',  now());
                    });
                    $q->when($request->type == 'later', function ($query) {
                        $query->where('start_at', '>',   now());
                    });
                });
            })

            ->paginate(6);

        $now = Carbon::now()->format('Y-m-d H:i:s');

        $flash_sale = FlashSale::where('is_active', true)
            ->when($request->type == 'now', function ($query) use ($now) {
                $query->where('start_at', '<',  $now);
                $query->where('end_at', '>=',  $now);
            })->when($request->type == 'later', function ($query) use ($now) {
                $query->where('start_at', '>', $now);
                // $query->whereDate('end_at', '>=',  Carbon::tomorrow());
            })
            ->first();

        $additionData = null;

        if ($flash_sale) {
            $start_at = Carbon::parse($flash_sale->start_at);
            $end_at = Carbon::parse($flash_sale->end_at);
            $diff = $start_at->diffInSeconds($end_at);


            $additionData = [
                'end_at' => $flash_sale->end_at,
                'start_at' => $flash_sale->start_at,
                'ends_in' => $diff,
                'type' => $request->type,
            ];
        }
        return response()->json(['data' =>   SimpleProductDetailsResource::collection($flash_salesProducts), 'status' => 'success', 'message' => '', 'additionData' => $additionData]);
    }


    public function categoryLayers()
    {
        $categories = Category::where(['is_active' => true, 'position' => 'main', 'parent_id' => null])->get();
        return (CategoryResource::collection($categories))->additional(['status' => 'success', 'message' => '']);
    }
    public function getCategories(Request $request)
    {
        $main_categories  = Category::where(['is_active' => true, 'parent_id' =>  null, 'position' => 'main'])->orderBy('ordering', 'asc')->get();
        $main_category_id  = $request->main_category_id  != null ? $request->main_category_id : (isset($main_categories) ? $main_categories[0]['id'] : null);

        $sub_categories =  Category::where(['is_active' => true, 'parent_id' =>    $main_category_id, 'position' => 'first_sub'])->orderBy('ordering', 'asc')->get();

        $second_category = $request->second_category_id != null ? $request->second_category_id : (isset($sub_categories)   && $sub_categories->count()  > 0 ? $sub_categories[0]['id'] : null);
        $recommended = Product::where(['is_active' => true, 'main_category_id' => $main_category_id])->whereHas('categoryProducts', function ($q) use ($second_category) {
            $q->whereHas('category', function ($q) use ($second_category) {
                $q->where('id', $second_category);
            });
        })->orderBy('ordering', 'asc')->take(6)->get();
        $second_category_data =  Category::where(['id' => $second_category, 'is_active' => true])->first();
        $third_category =   $second_category_data  != null  ?  thirdLavels($second_category_data) :  null;
        $data = [
            [
                'type' => 'slider',
                'text' => null,
                'data' =>  SliderResource::collection(Slider::where(['is_active' => true, 'category_id' =>   $second_category])->whereIn('platform', ['app', 'all'])->orderBy('ordering', 'asc')->take(3)->get()),
            ],
            [
                'type' => 'main_category',
                'text' => null,
                'data' =>  SimpleCategoryResource::collection($main_categories),
            ],
            [
                'type' => 'sub_category',
                'text' => null,
                'data' =>  SimpleCategoryResource::collection($sub_categories),
            ],
            [
                'type' => 'third_category',
                'text' => null,
                'data' =>  $third_category ? SubCategoryResource::collection($third_category) : [],
            ],
            [
                'type' => 'recommended',
                'text' => trans('app.messages.recommended'),
                'data' =>  SimpleProductResource::collection($recommended),
            ],

        ];
        return response()->json(['data' => $data, 'status' => 'success', 'message' => '']);
    }

    // public function getCategory(Request $request)
    // {
    //     $categories = Category::where('is_active', 1)->when($request->category_id == null, function ($query) {
    //         $query->where('position', 'main');
    //         $query->whereHas('mainCategoryProducts');
    //     })->when($request->category_id, function ($query) use ($request) {
    //         $query->where('parent_id', $request->category_id);
    //     })->orderBy('ordering', 'asc')->get();
    //     return (SimpleCategoryResource::collection($categories))->additional(['status' => 'success', 'message' => '']);
    // }

    public function getCategoryBySlug($slug)
    {
        $category = Category::whereTranslationLike('slug', "%$slug%")->firstOrFail();
        $mainCategory = $category->id ? root(Category::find($category->id)) : null;
        return (SimpleCategoryResource::make($category))->additional(['status' => 'success', 'message' => '', 'slider' => $category->id != null ? SliderResource::collection($mainCategory->sliders()->whereIn('platform', ['website', 'all'])->inRandomOrder()->take(2)->get()) : []]);
    }

    public function getCategory(Request $request)
    {
        $categories = Category::where('is_active', 1)->when($request->category_id == null, function ($query) {
            $query->where('position', 'main');
        })->when($request->category_id, function ($query) use ($request) {
            $query->where('parent_id', $request->category_id);
        })->orderBy('ordering', 'asc')->get();

        $mainCategory = $request->category_id ? root(Category::find($request->category_id)) : null;

        $meta_data = [
            'id'                 => @$mainCategory->id,
            'meta_tag'           => @$mainCategory->metas->meta_tag,
            'meta_title'         => @$mainCategory->metas->meta_title,
            'meta_description'   => @$mainCategory->metas->meta_description,
            'meta_canonical_tag' => @$mainCategory->image,
        ];

        return (SimpleCategoryResource::collection($categories))->additional(['status' => 'success', 'message' => '','meta_data' => $meta_data, 'slider' => $request->category_id != null ? SliderResource::collection($mainCategory->sliders()->whereIn('platform', ['website', 'all'])->inRandomOrder()->take(2)->get()) : []]);
    }


    public function offerProducts(OfferRequest $request,  $offer_id)
    {
        $additionData  = [];
        $offer = Offer::findOrFail($offer_id);

        if ($offer->type == 'buy_x_get_y') {

            $offerProducts = $this->dataOfBuyXGetY($offer, $request->type);
        } elseif ($offer->type == 'fix_amount'  || $offer->type == 'percentage') {
            if ($offer->discountOfOffer->apply_on  == 'special_products') {
                $offerProducts =  ProductDetails::when($offer, function ($q) use ($offer) {
                    $q->whereIn('id', $offer->discountOfOffer->apply_ids);
                    // $q->groupBy('color_id');
                    $q->groupBy('product_id');

                    $q->whereHas('product', function ($q) {
                        $q->where('is_active', true);
                        // $q->whereHas('productDetails', function($q){
                        //                             $q->groupBy('color_id');

                        // });
                    });
                })->paginate(20);
            } elseif ($offer->discountOfOffer->apply_on == 'special_categories') {
                $offerProducts =  ProductDetails::whereHas('product', function ($q) use ($offer) {
                    $q->where('is_active', true);
                    $q->whereHas('categoryProducts', function ($q) use ($offer) {
                        $q->whereIn('category_id', $offer->discountOfOffer->apply_ids);
                    });
                })->paginate(20);
            } else {
            }
        }
        $start_at = Carbon::parse($offer->start_at);
        $end_at = Carbon::parse($offer->end_at);
        $diff = $start_at->diff($end_at)->format('%h:%I:%s');
        $additionData += [
            'end_at' => $offer->end_at,
            'end_at_for_web' =>  $offer->end_at->format('Y-m-d H:i:s'),
            'start_at' => $offer->start_at,
            'ends_in' => $diff,
        ];
        return (SimpleProductDetailsResource::collection($offerProducts))->additional(['status' => 'success', 'message' => '', 'additionData' => $additionData]);
    }

    private function dataOfBuyXGetY($offer, $type)
    {
        $offerProducts  = [];
        if ($type == 'buy_x') {
            if ($offer->buyToGetOffer->buy_apply_on == 'special_products') {
                $offerProducts =  ProductDetails::when($offer, function ($q) use ($offer) {
                    $q->whereIn('id', $offer->buyToGetOffer->buy_apply_ids);
                    $q->whereHas('product', function ($q) {
                        $q->where('is_active', true);
                    });
                })->paginate(20);
            } elseif ($offer->buyToGetOffer->buy_apply_on == 'special_categories') {
                $offerProducts =  ProductDetails::whereHas('product', function ($q) use ($offer) {
                    $q->where('is_active', true);
                    $q->whereHas('categoryProducts', function ($q) use ($offer) {
                        $q->whereIn('category_id', $offer->buyToGetOffer->buy_apply_ids);
                    });
                })->paginate(20);
            } else {
            }
        } elseif ($type == 'get_y') {
            if ($offer->buyToGetOffer->get_apply_on == 'special_products') {
                $offerProducts =  ProductDetails::when($offer, function ($q) use ($offer) {
                    $q->whereIn('id', $offer->buyToGetOffer->get_apply_ids);
                    $q->whereHas('product', function ($q) {
                        $q->where('is_active', true);
                    });
                })->paginate(20);
            } elseif ($offer->buyToGetOffer->get_apply_on == 'special_categories') {
                $offerProducts =  ProductDetails::whereHas('product', function ($q) use ($offer) {
                    $q->where('is_active', true);
                    $q->whereHas('categoryProducts', function ($q) use ($offer) {
                        $q->whereIn('category_id', $offer->buyToGetOffer->get_apply_ids);
                    });
                })->paginate(20);
            }
        }
        return  $offerProducts;
    }

    public function getCommonQuestions()
    {
        $commonQuestions = CommonQuestion::latest()->paginate();
        return CommonQuestionResource::collection($commonQuestions)->additional(['status' => 'success', 'message' => null]);
    }
}
