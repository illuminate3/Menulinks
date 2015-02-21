<?php
namespace TypiCMS\Modules\Menulinks\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Controller;
use Input;
use Lang;
use Redirect;
use Response;
use TypiCMS\Http\Controllers\AdminNestedController;
use TypiCMS\Modules\Menulinks\Http\Requests\FormRequest;
use TypiCMS\Modules\Menulinks\Repositories\MenulinkInterface;
use View;

class AdminController extends Controller
{
    protected $repository;

    public function __construct(MenulinkInterface $menulink)
    {
        $this->repository = $menulink;
    }

    /**
     * Redirect to menu edit form
     * 
     * @param  $menu
     * @return Redirect
     */
    public function index($menu = null)
    {
        return Redirect::route('admin.menus.edit', $menu->id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  $menu
     * @return void
     */
    public function create($menu = null)
    {
        $model = $this->repository->getModel();
        $selectPages = $this->repository->getPagesForSelect();
        $selectModules = $this->repository->getModulesForSelect();

        return view('menulinks::admin.create')
            ->with(compact('model', 'menu', 'selectPages', 'selectModules'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $menu
     * @param  $model
     * @return void
     */
    public function edit($menu = null, $model)
    {
        $selectPages = $this->repository->getPagesForSelect();
        $selectModules = $this->repository->getModulesForSelect();

        return view('menulinks::admin.edit')
            ->with(compact('model', 'menu', 'selectPages', 'selectModules'));
    }

    /**
     * Show resource.
     *
     * @param  $menu
     * @param  $model
     * @return Redirect
     */
    public function show($menu = null, $model)
    {
        return Redirect::route('admin.menus.menulinks.edit', [$menu->id, $model->id]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $menu
     * @param  FormRequest $request
     * @return Redirect
     */
    public function store($menu = null, FormRequest $request)
    {
        $model = $this->repository->create($request->all());
        return $this->redirect($request, $model);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  $menu
     * @param  $model
     * @param  FormRequest $request
     * @return Redirect
     */
    public function update($menu = null, $model, FormRequest $request)
    {
        $this->repository->update($request->all());
        return $this->redirect($request, $model);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $parent
     * @param  $model
     * @return Redirect
     */
    public function destroy($parent = null, $model)
    {
        if ($this->repository->delete($model)) {
            return back();
        }
    }

    /**
     * Sort list.
     *
     * @return Response
     */
    public function sort()
    {
        $this->repository->sort(Input::all());
        return Response::json([
            'error'   => false,
            'message' => trans('global.Items sorted')
        ], 200);
    }

    /**
     * Redirect after a form is saved
     * 
     * @param  $request
     * @param  $model
     * @return \Illuminate\Routing\Redirector
     */
    protected function redirect($request, $model)
    {
        $redirectUrl = $request->get('exit') ? $model->indexUrl() : $model->editUrl() ;
        return redirect($redirectUrl);
    }
}