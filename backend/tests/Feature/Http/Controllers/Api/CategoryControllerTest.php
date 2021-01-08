<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestResources;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CategoryControllerTest extends TestCase {
	
	use DatabaseMigrations, TestValidations, TestSaves, TestResources;
	
	private $category;
	private $serializedFields = [
		'id',
		'name',
		'description',
		'is_active',
		'deleted_at',
		'created_at',
		'updated_at',
	];
	
	protected function setUp(): void {
		parent::setUp();
		
		$this->category = factory( Category::class )->create();
	}
	
	public function testIndex() {
		$response = $this->get( route( 'categories.index' ) );
		$this->assertStatusAndStructure( $response, 200, [
			'data'  => [
				'*' => $this->serializedFields,
			],
			'links' => [],
			'meta'  => [],
		] )
			 ->assertJson( [ 'meta' => [ 'per_page' => 15 ] ] );
		
		$resource = CategoryResource::collection( collect( [ $this->category ] ) );
		$this->assertResource( $response, $resource );
	}
	
	public function testShow() {
		$response = $this->get( route( 'categories.show', [ 'category' => $this->category->id ] ) );
		$this->assertStatusAndStructure( $response, 200, [ 'data' => $this->serializedFields ] );
		
		$id       = $response->json( 'data.id' );
		$resource = new CategoryResource( Category::find( $id ) );
		$this->assertResource( $response, $resource );
	}
	
	public function testInvalidationData() {
		$data = [ 'name' => '' ];
		$this->assertInvalidationInStoreAction( $data, 'required' );
		$this->assertInvalidationInUpdateAction( $data, 'required' );
		
		$data = [ 'name' => str_repeat( 'a', 256 ) ];
		$this->assertInvalidationInStoreAction( $data, 'max.string', [ 'max' => 255 ] );
		$this->assertInvalidationInUpdateAction( $data, 'max.string', [ 'max' => 255 ] );
		
		$data = [ 'is_active' => 'a' ];
		$this->assertInvalidationInStoreAction( $data, 'boolean' );
		$this->assertInvalidationInUpdateAction( $data, 'boolean' );
	}
	
	public function testSave() {
		$genreId = factory( Genre::class )->create()->id;
		$data    = [
			[
				'send_data' => [ 'name' => 'test', 'genres_id' => [ $genreId ] ],
				'test_data' => [ 'name' => 'test', 'is_active' => true, 'deleted_at' => null ],
			],
			[
				'send_data' => [ 'name' => 'test', 'is_active' => false, 'genres_id' => [ $genreId ] ],
				'test_data' => [ 'name' => 'test', 'is_active' => false, 'deleted_at' => null ],
			],
		];
		
		foreach ( $data as $value ) {
			$response = $this->assertStore( $value['send_data'], $value['test_data'] );
			$response->assertJsonStructure( [ 'data' => $this->serializedFields ] );
			$this->assertHasGenre( $response->json( 'data.id' ), $value['send_data']['genres_id'][0] );
			
			$resource = new CategoryResource( Category::find( $response->json( 'data.id' ) ) );
			$this->assertResource( $response, $resource );
			
			$response = $this->assertUpdate( $value['send_data'], $value['test_data'] );
			$response->assertJsonStructure( [ 'data' => $this->serializedFields ] );
			$this->assertHasGenre( $response->json( 'data.id' ), $value['send_data']['genres_id'][0] );
			
			$resource = new CategoryResource( Category::find( $response->json( 'data.id' ) ) );
			$this->assertResource( $response, $resource );
		}
	}
	
	public function testDestroy() {
		$response = $this->json( 'DELETE', route( 'categories.destroy', [ 'category' => $this->category->id ] ) );
		
		$response->assertStatus( 204 );
		
		$this->assertNull( Category::find( $this->category->id ) );
		$this->assertNotNull( Category::withTrashed()->find( $this->category->id ) );
	}
	
	protected function assertHasGenre( $categoryId, $genreId ) {
		$this->assertDatabaseHas( 'category_genre', [
			'genre_id'    => $genreId,
			'category_id' => $categoryId,
		] );
	}
	
	protected function routeStore() {
		return route( 'categories.store' );
	}
	
	protected function routeUpdate() {
		return route( 'categories.update', [ 'category' => $this->category->id ] );
	}
	
	protected function model() {
		return Category::class;
	}
	
}
