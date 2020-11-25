<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends BasicCrudController {
	
	private $rules;
	
	public function __construct() {
		$this->rules = [
			'name'        => 'required|max:255',
			'description' => 'nullable',
			'is_active'   => 'boolean',
		];
	}
	
	public function index() {
		return Category::all();
	}
	
	public function store( Request $request ) {
		$validatedData = $this->validate( $request, $this->rulesStore() );
		$self          = $this;
		$obj           = \DB::transaction( function() use ( $request, $validatedData, $self ) {
			$obj = $this->model()::create( $validatedData );
			$self->handleRelations( $obj, $request );
			
			return $obj;
		} );
		$obj->refresh();
		
		return $obj;
	}
	
	public function update( Request $request, $id ) {
		$obj           = $this->findOrFail( $id );
		$validatedData = $this->validate( $request, $this->rulesUpdate() );
		$self          = $this;
		$obj           = \DB::transaction( function() use ( $request, $validatedData, $self, $obj ) {
			$obj->update( $validatedData );
			$self->handleRelations( $obj, $request );
			
			return $obj;
		} );
		
		return $obj;
	}
	
	public function show( $id ) {
		return $this->findOrFail( $id );
	}
	
	public function destroy( $id ) {
		$this->findOrFail( $id )->delete();
		
		return response()->noContent(); // 204 - No Content
	}
	
	protected function handleRelations( $category, Request $request ) {
		$category->genres()->sync( $request->get( 'genres_id' ) );
	}
	
	protected function model() {
		return Category::class;
	}
	
	protected function rulesStore() {
		return $this->rules;
	}
	
	protected function rulesUpdate() {
		return $this->rules;
	}
}
