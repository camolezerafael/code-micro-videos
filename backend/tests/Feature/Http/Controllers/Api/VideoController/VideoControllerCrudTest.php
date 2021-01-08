<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use App\Http\Resources\VideoResource;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Support\Arr;
use Tests\Traits\TestResources;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class VideoControllerCrudTest extends BaseVideoControllerTestCase {
	
	use TestValidations, TestSaves, TestResources;
	
	public function testIndex() {
		$response = $this->get( route( 'videos.index' ) );
		$this->assertStatusAndStructure( $response, 200, [
			'data'  => [
				'*' => $this->serializedFields,
			],
			'links' => [],
			'meta'  => [],
		] )
			 ->assertJson( [ 'meta' => [ 'per_page' => 15 ] ] );
		
		$resource = VideoResource::collection( collect( [ $this->video ] ) );
		$this->assertResource( $response, $resource );
//		$this->assertIfFilesUrlExists( $this->video, $response );
	}
	
	public function testShow() {
		$response = $this->get( route( 'videos.show', [ 'video' => $this->video->id ] ) );
		$this->assertStatusAndStructure( $response, 200, [ 'data' => $this->serializedFields ] );
		
		$id       = $response->json( 'data.id' );
		$resource = new VideoResource( Video::find( $id ) );
		$this->assertResource( $response, $resource );
//		$this->assertIfFilesUrlExists( $this->video, $response );
	}
	
	public function testInvalidationRequired() {
		$data = [
			'title'         => '',
			'description'   => '',
			'year_launched' => '',
			'rating'        => '',
			'duration'      => '',
			'categories_id' => '',
			'genres_id'     => '',
		];
		$this->assertInvalidationInStoreAction( $data, 'required' );
		$this->assertInvalidationInUpdateAction( $data, 'required' );
	}
	
	public function testInvalidationMax() {
		$data = [ 'title' => str_repeat( 'x', 256 ) ];
		$this->assertInvalidationInStoreAction( $data, 'max.string', [ 'max' => 255 ] );
		$this->assertInvalidationInUpdateAction( $data, 'max.string', [ 'max' => 255 ] );
	}
	
	public function testInvalidationInteger() {
		$data = [ 'duration' => 'x' ];
		$this->assertInvalidationInStoreAction( $data, 'integer' );
		$this->assertInvalidationInUpdateAction( $data, 'integer' );
	}
	
	public function testInvalidationYearLauchedField() {
		$data = [ 'year_launched' => 'x' ];
		$this->assertInvalidationInStoreAction( $data, 'date_format', [ 'format' => 'Y' ] );
		$this->assertInvalidationInUpdateAction( $data, 'date_format', [ 'format' => 'Y' ] );
	}
	
	public function testInvalidationOpenedField() {
		$data = [ 'opened' => 'x' ];
		$this->assertInvalidationInStoreAction( $data, 'boolean' );
		$this->assertInvalidationInUpdateAction( $data, 'boolean' );
	}
	
	public function testInvalidationRatingField() {
		$data = [ 'rating' => '0' ];
		$this->assertInvalidationInStoreAction( $data, 'in' );
		$this->assertInvalidationInUpdateAction( $data, 'in' );
	}
	
	public function testInvalidationCategoriesIdField() {
		$data = [ 'categories_id' => 'x' ];
		$this->assertInvalidationInStoreAction( $data, 'array' );
		$this->assertInvalidationInUpdateAction( $data, 'array' );
		
		$data = [ 'categories_id' => [ 100 ] ];
		$this->assertInvalidationInStoreAction( $data, 'exists' );
		$this->assertInvalidationInUpdateAction( $data, 'exists' );
		
		$category = factory( Category::class )->create();
		$category->delete();
		$data = [ 'categories_id' => [ $category->id ] ];
		$this->assertInvalidationInStoreAction( $data, 'exists' );
		$this->assertInvalidationInUpdateAction( $data, 'exists' );
	}
	
	public function testInvalidationGenresIdField() {
		$data = [ 'genres_id' => 'x' ];
		$this->assertInvalidationInStoreAction( $data, 'array' );
		$this->assertInvalidationInUpdateAction( $data, 'array' );
		
		$data = [ 'genres_id' => [ 100 ] ];
		$this->assertInvalidationInStoreAction( $data, 'exists' );
		$this->assertInvalidationInUpdateAction( $data, 'exists' );
		
		$genre = factory( Genre::class )->create();
		$genre->delete();
		$data = [ 'genres_id' => [ $genre->id ] ];
		$this->assertInvalidationInStoreAction( $data, 'exists' );
		$this->assertInvalidationInUpdateAction( $data, 'exists' );
	}
	
	public function testSaveWithoutFiles() {
		$testData = Arr::except( $this->sendData, [ 'categories_id', 'genres_id' ] );
		$data     = [
			[
				'send_data' => $this->sendData,
				'test_data' => $testData + [ 'opened' => false ],
			],
			[
				'send_data' => $this->sendData + [ 'opened' => true ],
				'test_data' => $testData + [ 'opened' => true ],
			],
			[
				'send_data' => $this->sendData + [ 'rating' => Video::RATING_LIST[1] ],
				'test_data' => $testData + [ 'rating' => Video::RATING_LIST[1] ],
			],
		];
		
		foreach ( $data as $key => $value ) {
			$response = $this->assertStore( $value['send_data'], $value['test_data'] );
			$response->assertJsonStructure( [ 'data' => $this->serializedFields ] );
			$this->assertHasCategory( $response->json( 'data.id' ), $value['send_data']['categories_id'][0] );
			$this->assertHasGenre( $response->json( 'data.id' ), $value['send_data']['genres_id'][0] );
//			$this->assertIfFilesUrlExists( $this->video, $response );
			
			$response = $this->assertUpdate( $value['send_data'], $value['test_data'] );
			$response->assertJsonStructure( [ 'data' => $this->serializedFields ] );
			$this->assertHasCategory( $response->json( 'data.id' ), $value['send_data']['categories_id'][0] );
			$this->assertHasGenre( $response->json( 'data.id' ), $value['send_data']['genres_id'][0] );
//			$this->assertIfFilesUrlExists( $this->video, $response );
		}
	}
	
	public function testDestroy() {
		$response = $this->json( 'DELETE', route( 'videos.destroy', [ 'video' => $this->video->id ] ) );
		
		$response->assertStatus( 204 );
		$this->assertNull( Video::find( $this->video->id ) );
		$this->assertNotNull( Video::withTrashed()->find( $this->video->id ) );
	}
	
	protected function routeStore() {
		return route( 'videos.store' );
	}
	
	protected function routeUpdate() {
		return route( 'videos.update', [ 'video' => $this->video->id ] );
	}
	
	protected function model() {
		return Video::class;
	}
	
	protected function assertHasCategory( $videoId, $categoryId ) {
		$this->assertDatabaseHas( 'category_video', [
			'video_id'    => $videoId,
			'category_id' => $categoryId,
		] );
	}
	
	protected function assertHasGenre( $videoId, $genreId ) {
		$this->assertDatabaseHas( 'genre_video', [
			'video_id' => $videoId,
			'genre_id' => $genreId,
		] );
	}
}
