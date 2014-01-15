<?php

class UsedMethodsController extends BaseController {

	/**
	 * UsedMethod Repository
	 *
	 * @var UsedMethod
	 */
	protected $usedMethod;

	public function __construct(UsedMethod $usedMethod)
	{
		$this->usedMethod = $usedMethod;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$usedMethods = $this->usedMethod->all();

		return View::make('usedMethods.index', compact('usedMethods'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('usedMethods.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, UsedMethod::$rules);

		if ($validation->passes())
		{
			$this->usedMethod->create($input);

			return Redirect::route('usedMethods.index');
		}

		return Redirect::route('usedMethods.create')
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
		$usedMethod = $this->usedMethod->findOrFail($id);

		return View::make('usedMethods.show', compact('usedMethod'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$usedMethod = $this->usedMethod->find($id);

		if (is_null($usedMethod))
		{
			return Redirect::route('usedMethods.index');
		}

		return View::make('usedMethods.edit', compact('usedMethod'));
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
		$validation = Validator::make($input, UsedMethod::$rules);

		if ($validation->passes())
		{
			$usedMethod = $this->usedMethod->find($id);
			$usedMethod->update($input);

			return Redirect::route('usedMethods.show', $id);
		}

		return Redirect::route('usedMethods.edit', $id)
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
		$this->usedMethod->find($id)->delete();

		return Redirect::route('usedMethods.index');
	}

}
