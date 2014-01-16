<?php
use PayzenApi\Constants;
use PayzenApi\LegacyApi;
use PayzenApi\PageInfo;

class ChargesController extends BaseController {

    /**
     * Charge Repository
     *
     * @var Charge
     */
    protected $charge;

    public function __construct(Charge $charge) {
        $this->charge = $charge;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $charges = $this->charge->all();

        return View::make('charges.index', compact('charges'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        return View::make('charges.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {
        $input = Input::all();
        $validation = Validator::make($input, Charge::$rules);

        if ($validation->passes()) {
            // $this->charge->create($input);
            $shop_id = Input::get("shop_id");
            $shop_key = Input::get("shop_key");

            $json = json_encode(Input::only([
                'amount',
                'currency'
            ]), JSON_PRETTY_PRINT);
            $url = URL::route('postChargeForPos', [
                "urlShopId" => $shop_id
            ]);

            // Call API
            $curl = new Curl();
            $curl->http_login($shop_id . '_' . $shop_key, "");
            $curl->http_header('Content-Type', 'application/json');
            $curl->http_header('Accept', 'application/json');
            $curl->ssl(Config::get("payzenapi.ssl_verifypeer"));
            $response = $curl->simple_post($url, $json);

            // Display result
            if (! $response) {
                $response = "\nFAILED :\n" . $curl->debug(true) . $response;
            }
            return View::make('api_debug')->with('url', $url)
                ->with('json', $json)
                ->with('response', var_export($response, true));
        }

        return Redirect::route('charges.create')->withInput()
            ->withErrors($validation)
            ->with('message', 'There were validation errors.');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id) {
        $charge = $this->charge->findOrFail($id);

        return View::make('charges.show', compact('charge'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id) {
        $charge = $this->charge->find($id);

        if (is_null($charge)) {
            return Redirect::route('charges.index');
        }

        return View::make('charges.edit', compact('charge'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return Response
     */
    public function update($id) {
        $input = array_except(Input::all(), '_method');
        $validation = Validator::make($input, Charge::$rules);

        if ($validation->passes()) {
            $charge = $this->charge->find($id);
            $charge->update($input);

            return Redirect::route('charges.show', $id);
        }

        return Redirect::route('charges.edit', $id)->withInput()
            ->withErrors($validation)
            ->with('message', 'There were validation errors.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id) {
        $this->charge->find($id)->delete();

        return Redirect::route('charges.index');
    }

    function postChargeForPos($urlShopId) {
        // return "input:" . Input::all() . " json" . Input::json();
        $request = Request::instance();

        // Filled from "identify_shop" filter
        $shopId = $request->attributes->get(\PayzenApi\Constants::SHOP_ID);
        $shopKey = $request->attributes->get(\PayzenApi\Constants::SHOP_KEY);

        // Check consistency, just for fun
        if ($shopId != $urlShopId) {
            return App::abort(400, "Incoherent shop id !");
        }

        // Json, http, who cares ? manage it all !
        $params = Input::isJson() ? Input::json()->all() : Input::all();

        // TODO validation des sous-tableaux via le validator
        $validation = Validator::make($params, [
            "amount" => "required|numeric|min:0.00001",
            "currency" => "required|alphanum|size:3"
				/* "available_methods" => "",
				 "available_instruments" => ""
		*/
				]);

        // look for currency by alpha or num code
        $currency_code = strtolower(array_get($params, "currency", ""));
        $currency = \Currency::where('alpha3', '=', $currency_code)->orWhere('numeric', '=', $currency_code)->first();
        if (! $currency) {
            throw new \Exception("Unsupported currency : " . $currency_code); // TODO better exception
        }

        if (! $validation->passes()) {
            return App::abort(400, $validation->errors());
        }

        $available = array_get($params, "available_methods", []);
        $selected = array_get($params, "available_instruments", []);

        $params["url_return"] = URL::action("RedirectController@getReturn");
        // Call and parse
        $api = new LegacyApi();
        $api->init($urlShopId, $shopKey);
        $api->postForm($api->generateFormData($params, $currency), $html, $headers, $cookies);
        $info = $api->parsePage($html, $cookies);

        // Check ok
        if ($info->state == PageInfo::STATE_ERROR) {
            return App::abort(500, "Error when calling payzen");
        }

        // save Charge
        $charge = new Charge();
        $charge->amount = $params['amount'];
        $charge->currency = $currency->alpha3;
        $charge->shop_id = $shopId;
        $charge->shop_key = $shopKey;
        $charge->status = Charge::STATUS_CREATED;
        $charge->save();

        // attach context
        $context = new Context();
        $context->trans_date = $api->trans_date;
        $context->trans_id = $api->trans_id;
        $context->trans_time = $api->trans_timestamp;
        $context->cache_id = $info->cache_id;
        $context->locale = $info->locale;
        $context->status = Context::STATUS_CREATED;
        $context = $charge->contexts()->save($context);

        // create/refresh persisted available methods
        $availableMethods = [];
        foreach ($info->card_types as $method) {
            $availableMethod = new AvailableMethod();
            $availableMethod->setAttribute('method', $method);
            $availableMethods[] = $availableMethod;
        }
        $charge->availableMethods()->delete();
        $charge->availableMethods()->saveMany($availableMethods);

        return $this->displayCharge($charge, Input::wantsJson());
    }

    private function displayCharge(\Charge $charge, $json = true) {
        return $charge->toJson(JSON_PRETTY_PRINT);//TODO print relations
    }
}
