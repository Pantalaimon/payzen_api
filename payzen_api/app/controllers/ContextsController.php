<?php

class ContextsController extends BaseController {

	/**
	 * Context Repository
	 *
	 * @var Context
	 */
	protected $context;

	public function __construct(Context $context)
	{
		$this->context = $context;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$contexts = $this->context->all();

		return View::make('contexts.index', compact('contexts'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('contexts.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, Context::$rules);

		if ($validation->passes())
		{
			$this->context->create($input);

			return Redirect::route('contexts.index');
		}

		return Redirect::route('contexts.create')
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
		$context = $this->context->findOrFail($id);

		return View::make('contexts.show', compact('context'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$context = $this->context->find($id);

		if (is_null($context))
		{
			return Redirect::route('contexts.index');
		}

		return View::make('contexts.edit', compact('context'));
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
		$validation = Validator::make($input, Context::$rules);

		if ($validation->passes())
		{
			$context = $this->context->find($id);
			$context->update($input);

			return Redirect::route('contexts.show', $id);
		}

		return Redirect::route('contexts.edit', $id)
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
		$this->context->find($id)->delete();

		return Redirect::route('contexts.index');
	}

}
