<?php
use PayzenApi\Constants;
use PayzenApi\FormApi;
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
     * Show the form for creating a new charge.
     *
     * @return Response
     */
    public function create() {
        return View::make('charges.create');
    }

    /**
     * Wrapper around the postChargeForPos API command from the create form
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

        return $this->displayCharge($charge, true); // FIXME use Input::isJson()
                                                        // return View::make('charges.show', compact('charge'));
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
        // TODO auto-generated
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

    // /**
    // * Remove the specified resource from storage.
    // *
    // * @param int $id
    // * @return Response
    // */
    // public function destroy($id) {
    // //TODO auto-generated
    // $this->charge->find($id)->delete();

    // return Redirect::route('charges.index');
    // }

    /**
     * Creates a charge and initialize a payment context as per API proto-spec
     *
     * @param int $urlShopId
     * @throws \Exception
     * @return void Ambigous \Illuminate\View\View>
     */
    function postChargeForPos($urlShopId) {
        $request = Request::instance();

        // Filled from "identify_shop" filter
        $shopId = $request->attributes->get(\PayzenApi\Constants::SHOP_ID);
        $shopKey = $request->attributes->get(\PayzenApi\Constants::SHOP_KEY);

        // Check consistency, just for fun FIXME avoid duplicate parameters in API
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
        ;
        if (! $currency) {
            throw new \Exception("Unsupported currency : " . $currency_code); // TODO better exception
        }

        if (! $validation->passes()) {
            return App::abort(400, $validation->errors());
        }

        // Call and parse
        $api = new PayzenApi\FormApi($urlShopId, $shopKey);
        $info = $api->createPaymentContext($params, $currency);

        // Check ok
        if ($info->state == PageInfo::STATE_ERROR) {
            return App::abort(500, "Error when calling payzen");
        }

        // Prepare entities to be saved
        $charge = new Charge([
            'amount' => $params['amount'],
            'currency' => $currency->alpha3,
            'shop_id' => $shopId,
            'shop_key' => $shopKey,
            'status' => Charge::STATUS_CREATED
        ]);

        $context = new Context([
            'trans_date' => $api->trans_date,
            'trans_id' => $api->trans_id,
            'trans_time' => $api->trans_time, // TODO useless ?
            'cache_id' => $info->cache_id,
            'locale' => $info->locale,
            'status' => Context::STATUS_CREATED
        ]);
        $charge->updateFromContext($context);

        $availableMethods = [];
        foreach ($info->card_types as $method) {
            $availableMethods[] = new AvailableMethod([
                'method' => $method
            ]);
        }

        // Persist all
        $charge->save();
        $context = $charge->contexts()->save($context);
        // Log::debug("created context : " . var_export($context, true));

        // create/refresh persisted available methods
        $charge->availableMethods()->delete();
        $charge->availableMethods()->saveMany($availableMethods);

        return $this->displayCharge($charge, Input::wantsJson());
    }

    private function displayCharge(\Charge $charge, $json = true) {
        $charge->load('availableMethods', 'contexts', 'transactions');
        // TODO get information on transactions from ws and display it
        $links = [
            $this->buildLink(URL::route('charges.show', $charge->id, true), 'self', 'get')
        ];

        $wsApi = new VadsWSApi();
        $wsApi->initialize($charge->shop_key, Config::get("payzenapi.wsdl_url"));
        $transactions = [];
        $charge->transactions()
            ->getResults()
            ->each(function ($transEnt) use(&$transactions, $wsApi) {
            $transactions[] = (array)$wsApi->getInfoFromTransaction($transEnt);
        });

        // Update from last context
        $lastContext = $charge->contexts()
            ->getResults()
            ->last();
        switch ($lastContext->status) {
            case \Context::STATUS_CREATED:
                $links[] = $this->buildLink(URL::route('charges.update', $charge->id, true), 'update', 'put');
                $links[] = $this->buildLink(URL::route('redirectClient', $charge->id), 'redirect', 'get');
                $messages = $this->buildMessages('PAYMENT_INSTRUMENT_REQUIRED', "Transaction is incomplete. No payment instrument was chosen.");
                break;

            case \Context::STATUS_SUCCESS:
                break;

            case \Context::STATUS_FAILURE:
                $messages = $this->buildMessages('FAILURE', "Payment has been refused");
                break;

            case \Context::STATUS_CANCELLED:
                $messages = $this->buildMessages('CANCELLED', "Payment has been abandonned by the user");
                break;

            case \Context::STATUS_LOCKED:
                $messages = $this->buildMessages('PAYMENT_IN_PROGRESS', "User is using the payment pages. Hold your breath and stand still...");
                break;
        }

        // format and return
        $data = array_merge($charge->toArray(), compact('links', 'messages', 'transactions'));

        if ($json) {
            return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        } else {
            return View::make('charges.show', compact('charge'));
        }
    }

    private function buildLink($href, $rel, $method) {
        return compact('href', 'rel', 'method'); // OMG I love the compact function !
    }

    private function buildMessages($title, $description) {
        return [
            compact('title', 'description')
        ];
    }
}
