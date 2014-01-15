<?php

class AvalaibleMethodsController extends BaseController {

	/**
	 * AvalaibleMethod Repository
	 *
	 * @var AvalaibleMethod
	 */
	protected $avalaibleMethod;

	public function __construct(AvalaibleMethod $avalaibleMethod)
	{
		$this->avalaibleMethod = $avalaibleMethod;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$avalaibleMethods = $this->avalaibleMethod->all();

		return View::make('avalaibleMethods.index', compact('avalaibleMethods'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('avalaibleMethods.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, AvalaibleMethod::$rules);

		if ($validation->passes())
		{
			$this->avalaibleMethod->create($input);

			return Redirect::route('avalaibleMethods.index');
		}

		return Redirect::route('avalaibleMethods.create')
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
		$avalaibleMethod = $this->avalaibleMethod->findOrFail($id);

		return View::make('avalaibleMethods.show', compact('avalaibleMethod'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$avalaibleMethod = $this->avalaibleMethod->find($id);

		if (is_null($avalaibleMethod))
		{
			return Redirect::route('avalaibleMethods.index');
		}

		return View::make('avalaibleMethods.edit', compact('avalaibleMethod'));
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
		$validation = Validator::make($input, AvalaibleMethod::$rules);

		if ($validation->passes())
		{
			$avalaibleMethod = $this->avalaibleMethod->find($id);
			$avalaibleMethod->update($input);

			return Redirect::route('avalaibleMethods.show', $id);
		}

		return Redirect::route('avalaibleMethods.edit', $id)
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
		$this->avalaibleMethod->find($id)->delete();

		return Redirect::route('avalaibleMethods.index');
	}

}
