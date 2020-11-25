<?php

namespace App\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends BasicCrudController {
	
	private $rules;
	
	public function __construct() {
		$this->rules = [
			'name'          => 'required|max:255',
			'is_active'     => 'boolean',
			'categories_id' => 'required|array|exists:categories,id,deleted_at,NULL',
		];
	}
	
	public function index() {
		return Genre::all();
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
	
	protected function handleRelations( $genre, Request $request ) {
		$genre->categories()->sync( $request->get( 'categories_id' ) );
	}
	
	protected function model() {
		return Genre::class;
	}
	
	protected function rulesUpdate() {
		return $this->rules;
	}
	
	protected function rulesStore() {
		return $this->rules;
	}
	
	
}
