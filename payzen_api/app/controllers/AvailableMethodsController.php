<?php

class AvailableMethodsController extends BaseController {

	/**
	 * AvailableMethod Repository
	 *
	 * @var AvailableMethod
	 */
	protected $availableMethod;

	public function __construct(AvailableMethod $availableMethod)
	{
		$this->availableMethod = $availableMethod;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$availableMethods = $this->availableMethod->all();

		return View::make('availableMethods.index', compact('availableMethods'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('availableMethods.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, AvailableMethod::$rules);

		if ($validation->passes())
		{
			$this->availableMethod->create($input);

			return Redirect::route('availableMethods.index');
		}

		return Redirect::route('availableMethods.create')
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
		$availableMethod = $this->availableMethod->findOrFail($id);

		return View::make('availableMethods.show', compact('availableMethod'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$availableMethod = $this->availableMethod->find($id);

		if (is_null($availableMethod))
		{
			return Redirect::route('availableMethods.index');
		}

		return View::make('availableMethods.edit', compact('availableMethod'));
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
		$validation = Validator::make($input, AvailableMethod::$rules);

		if ($validation->passes())
		{
			$availableMethod = $this->availableMethod->find($id);
			$availableMethod->update($input);

			return Redirect::route('availableMethods.show', $id);
		}

		return Redirect::route('availableMethods.edit', $id)
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
		$this->availableMethod->find($id)->delete();

		return Redirect::route('availableMethods.index');
	}

}
