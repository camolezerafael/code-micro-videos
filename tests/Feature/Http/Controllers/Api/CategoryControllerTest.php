<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;

class CategoryControllerTest extends TestCase {
	
	use DatabaseMigrations;
	
	public function testIndex() {
		$category = factory( Category::class )->create();
		
		$response = $this->get( route( 'categories.index' ) );
		
		$response
			->assertStatus( 200 )
			->assertJson( [ $category->toArray() ] );
	}
	
	public function testShow() {
		$category = factory( Category::class )->create();
		
		$response = $this->get( route( 'categories.show', [ 'category' => $category->id ] ) );
		
		$response
			->assertStatus( 200 )
			->assertJson( $category->toArray() );
	}
	
	
	public function testInvalidationData() {
		$response = $this->json( 'POST', route( 'categories.store' ), [] );
		
		$this->assertValidationRequired( $response );
		
		$response = $this->json( 'POST', route( 'categories.store' ), [
			'name'      => str_repeat( 'a', 256 ),
			'is_active' => 'a',
		] );
		
		$this->assertValidationMax( $response );
		$this->assertValidationBoolean( $response );
		
		
		$category = factory( Category::class )->create();
		
		$response = $this->json( 'PUT', route( 'categories.update', [ 'category' => $category->id ] ), [] );
		$this->assertValidationRequired( $response );
		
		
		$response = $this->json( 'PUT', route( 'categories.update', [ 'category' => $category->id ] ), [
			'name'      => str_repeat( 'a', 256 ),
			'is_active' => 'a',
		] );
		$this->assertValidationMax( $response );
		$this->assertValidationBoolean( $response );
	}
	
	protected function assertValidationRequired( TestResponse $response ) {
		$response
			->assertStatus( 422 )
			->assertJsonValidationErrors( [ 'name' ] )
			->assertJsonMissingValidationErrors( [ 'is_active' ] )
			->assertJsonFragment(
				[
					\Lang::get( 'validation.required', [ 'attribute' => 'name' ] ),
				] );
	}
	
	protected function assertValidationMax( TestResponse $response ) {
		$response
			->assertStatus( 422 )
			->assertJsonValidationErrors( [ 'name' ] )
			->assertJsonFragment(
				[
					\Lang::get( 'validation.max.string', [ 'attribute' => 'name', 'max' => 255 ] ),
				] );
	}
	
	protected function assertValidationBoolean( TestResponse $response ) {
		$response
			->assertStatus( 422 )
			->assertJsonValidationErrors( [ 'is_active' ] )
			->assertJsonFragment(
				[
					\Lang::get( 'validation.boolean', [ 'attribute' => 'is active' ] ),
				] );
	}
	
	public function testStore() {
		$response = $this->json( 'POST', route( 'categories.store' ), [
			'name' => 'test',
		] );
		
		$id       = $response->json( 'id' );
		$category = Category::find( $id );
		
		$response
			->assertStatus( 201 )
			->assertJson( $category->toArray() );
		
		$this->assertTrue( $response->json( 'is_active' ) );
		$this->assertNull( $response->json( 'description' ) );
		
		
		$response = $this->json( 'POST', route( 'categories.store' ), [
			'name'        => 'test',
			'description' => 'description',
			'is_active'   => false,
		] );
		
		$response
			->assertJsonFragment(
				[
					'description' => 'description',
					'is_active'   => false,
				] );
		
	}
	
	
	public function testUpdate() {
		$category = factory( Category::class )->create(
			[
				'description' => 'description',
				'is_active'   => false,
			] );
		$response = $this->json(
			'PUT',
			route( 'categories.update', [ 'category' => $category->id ] ),
			[
				'name'        => 'test',
				'description' => 'description changed',
				'is_active'   => true,
			] );
		
		$id       = $response->json( 'id' );
		$category = Category::find( $id );
		
		$response
			->assertStatus( 200 )
			->assertJson( $category->toArray() )
			->assertJsonFragment(
				[
					'description' => 'description changed',
					'is_active'   => true,
				] );
		
		
		$response = $this->json(
			'PUT',
			route( 'categories.update', [ 'category' => $category->id ] ),
			[
				'name'        => 'test',
				'description' => '',
			] );
		
		$response->assertJsonFragment(
				[
					'description' => null,
				] );
		
	}
	
	
}
