<?php

class TransactionsController extends BaseController {

	/**
	 * Transaction Repository
	 *
	 * @var Transaction
	 */
	protected $transaction;

	public function __construct(Transaction $transaction)
	{
		$this->transaction = $transaction;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$transactions = $this->transaction->all();

		return View::make('transactions.index', compact('transactions'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('transactions.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, Transaction::$rules);

		if ($validation->passes())
		{
			$this->transaction->create($input);

			return Redirect::route('transactions.index');
		}

		return Redirect::route('transactions.create')
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
		$transaction = $this->transaction->findOrFail($id);

		return View::make('transactions.show', compact('transaction'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$transaction = $this->transaction->find($id);

		if (is_null($transaction))
		{
			return Redirect::route('transactions.index');
		}

		return View::make('transactions.edit', compact('transaction'));
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
		$validation = Validator::make($input, Transaction::$rules);

		if ($validation->passes())
		{
			$transaction = $this->transaction->find($id);
			$transaction->update($input);

			return Redirect::route('transactions.show', $id);
		}

		return Redirect::route('transactions.edit', $id)
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
		$this->transaction->find($id)->delete();

		return Redirect::route('transactions.index');
	}

}
