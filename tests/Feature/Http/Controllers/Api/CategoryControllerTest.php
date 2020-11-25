<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CategoryControllerTest extends TestCase {
	
	use DatabaseMigrations, TestValidations, TestSaves;
	
	private $category;
	
	protected function setUp(): void {
		parent::setUp();
		
		$this->category = factory( Category::class )->create();
	}
	
	public function testIndex() {
		$response = $this->get( route( 'categories.index' ) );
		$this->assertStatusAndJson( $response, 200, [ $this->category->toArray() ] );
	}
	
	public function testShow() {
		$response = $this->get( route( 'categories.show', [ 'category' => $this->category->id ] ) );
		$this->assertStatusAndJson( $response, 200, $this->category->toArray() );
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
				'send_data' => [
					'name'      => 'test',
					'genres_id' => [ $genreId ],
				],
				'test_data' => [
					'name'       => 'test',
					'is_active'  => true,
					'deleted_at' => null,
				],
			],
			[
				'send_data' => [
					'name'      => 'test',
					'is_active' => false,
					'genres_id' => [ $genreId ],
				],
				'test_data' => [
					'name'       => 'test',
					'is_active'  => false,
					'deleted_at' => null,
				],
			],
		];
		
		foreach ( $data as $value ) {
			$response = $this->assertStore( $value['send_data'], $value['test_data'] );
			$response->assertJsonStructure( [ 'created_at', 'updated_at' ] );
			$this->assertHasGenre( $response->json( 'id' ), $value['send_data']['genres_id'][0] );
			
			$response = $this->assertUpdate( $value['send_data'], $value['test_data'] );
			$response->assertJsonStructure( [ 'created_at', 'updated_at' ] );
			$this->assertHasGenre( $response->json( 'id' ), $value['send_data']['genres_id'][0] );
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
