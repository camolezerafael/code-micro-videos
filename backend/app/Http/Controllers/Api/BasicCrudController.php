<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class BasicCrudController extends Controller {
	
	protected $defaultPerPage = 15;
	
	protected abstract function model();
	
	protected abstract function rulesStore();
	
	protected abstract function rulesUpdate();
	
	protected abstract function resource();
	
	protected abstract function resourceCollection();
	
	public function index( Request $request ) {
		$perPage   = (int) $request->get( 'per_page', $this->defaultPerPage );
		$hasFilter = in_array( Filterable::class, class_uses( $this->model() ) );
		
		$query = $this->queryBuilder();
		
		if ( $hasFilter ) {
			$query = $query->filter( $request->all() );
		}
		
		$data = $request->has('all') || !$this->defaultPerPage
			? $query->get()
			: $query->paginate($perPage);
			
		$resourceCollectionClass = $this->resourceCollection();
		$refClass                = new \ReflectionClass( $resourceCollectionClass );
		
		return $refClass->isSubclassOf( ResourceCollection::class )
			? new $resourceCollectionClass( $data )
			: $resourceCollectionClass::collection( $data );
	}
	
	public function show( $id ) {
		$obj      = $this->findOrFail( $id );
		$resource = $this->resource();
		
		return new $resource( $obj );
	}
	
	public function store( Request $request ) {
		$validatedData = $this->validate( $request, $this->rulesStore() );
		$obj           = $this->queryBuilder()->create( $validatedData );
		$obj->refresh();
		$resource = $this->resource();
		
		return new $resource( $obj );
	}
	
	public function update( Request $request, $id ) {
		$validatedData = $this->validate( $request, $this->rulesUpdate() );
		$obj           = $this->findOrFail( $id );
		$obj->update( $validatedData );
		$resource = $this->resource();
		
		return new $resource( $obj );
	}
	
	public function destroy( $id ) {
		$this->findOrFail( $id )->delete();
		
		return response()->noContent();
	}
	
	protected function findOrFail( $id ) {
		$model   = $this->model();
		$keyName = ( new $model )->getRouteKeyName();
		
		return $this->queryBuilder()->where( $keyName, $id )->firstOrFail();
	}
	
	protected function queryBuilder(): Builder {
		return $this->model()::query();
	}
	
}
