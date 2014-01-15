<?php

class ChargesController extends BaseController {

	/**
	 * Charge Repository
	 *
	 * @var Charge
	 */
	protected $charge;

	public function __construct(Charge $charge)
	{
		$this->charge = $charge;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$charges = $this->charge->all();

		return View::make('charges.index', compact('charges'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('charges.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, Charge::$rules);

		if ($validation->passes())
		{
			$this->charge->create($input);

			return Redirect::route('charges.index');
		}

		return Redirect::route('charges.create')
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$charge = $this->charge->findOrFail($id);

		return View::make('charges.show', compact('charge'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$charge = $this->charge->find($id);

		if (is_null($charge))
		{
			return Redirect::route('charges.index');
		}

		return View::make('charges.edit', compact('charge'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = array_except(Input::all(), '_method');
		$validation = Validator::make($input, Charge::$rules);

		if ($validation->passes())
		{
			$charge = $this->charge->find($id);
			$charge->update($input);

			return Redirect::route('charges.show', $id);
		}

		return Redirect::route('charges.edit', $id)
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$this->charge->find($id)->delete();

		return Redirect::route('charges.index');
	}


	function postChargeForPos($urlShopId) {
		$request = Request::instance();

		// Filled from "identify_shop" filter
		$shopId = $request->attributes->get( \PayzenApi\Constants::SHOP_ID );
		$shopKey = $request->attributes->get( \PayzenApi\Constants::SHOP_KEY );

		// Check consistency, just for fun
		if ($reqShopId != $urlShopId) {
			return App::abort( 400, "Incoherent shop id !" );
		}

		// Json, http, who cares ? manage it all !
		$params = Input::isJson() ? ( array ) Input::json() : Input::all();

		// TODO validation des sous-tableaux via le validator
		$validation = Validator::make( $params, [
				"amount" => "required|numeric|min:0.00001",
				"currency" => "required|alpha|size:3"
				/* "available_methods" => "",
				 "available_instruments" => ""
		*/
				] );

		if (! $validation->passes()) {
			return App::abort( 400, $validation->errors() );
		}

		$available = $params ["available_methods"];
		$selected = $params ["available_instruments"];

		$api = new LegacyApi( $urlShopId, $shopKey );

		$params ["url_return"] = Url::route( "LegacyController@getReturn" ); // TODO map route
		$formData = $api->generateFormData( $params );
		$html = $api->postForm( $formData );
		$info = $api->parsePage($html);

		if($info->state==PageInfo::STATE_ERROR){
			return App::abort(500,"Error when calling payzen");
		}


		// TODO parse and get info

		// TODO si erreur KO

		// TODO new Charge

		// TODO new context
	}
}
