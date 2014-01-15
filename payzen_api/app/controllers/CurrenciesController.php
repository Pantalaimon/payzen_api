<?php

class CurrenciesController extends BaseController {

	/**
	 * Currency Repository
	 *
	 * @var Currency
	 */
	protected $currency;

	public function __construct(Currency $currency)
	{
		$this->currency = $currency;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$currencies = $this->currency->all();

		return View::make('currencies.index', compact('currencies'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('currencies.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, Currency::$rules);

		if ($validation->passes())
		{
			$this->currency->create($input);

			return Redirect::route('currencies.index');
		}

		return Redirect::route('currencies.create')
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
		$currency = $this->currency->findOrFail($id);

		return View::make('currencies.show', compact('currency'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$currency = $this->currency->find($id);

		if (is_null($currency))
		{
			return Redirect::route('currencies.index');
		}

		return View::make('currencies.edit', compact('currency'));
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
		$validation = Validator::make($input, Currency::$rules);

		if ($validation->passes())
		{
			$currency = $this->currency->find($id);
			$currency->update($input);

			return Redirect::route('currencies.show', $id);
		}

		return Redirect::route('currencies.edit', $id)
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
		$this->currency->find($id)->delete();

		return Redirect::route('currencies.index');
	}

}
