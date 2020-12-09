<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Http\UploadedFile;
use Tests\Feature\Http\Controllers\Api\VideoController\BaseVideoControllerTestCase;
use Tests\Traits\TestUploads;
use Tests\Traits\TestValidations;

class VideoControllerUploadTest extends BaseVideoControllerTestCase {
	
	use TestValidations, TestUploads;
	
	public function testInvalidationVideoFile() {
		$this->assertInvalidationFile( 'video_file', 'mp4', Video::VIDEO_SIZE, 'mimetypes', [ 'values' => 'video/mp4' ] );
		$this->assertInvalidationFile( 'trailer_file', 'mp4', Video::TRAILER_SIZE, 'mimetypes', [ 'values' => 'video/mp4' ] );
		$this->assertInvalidationFile( 'thumb_file', 'jpg', Video::THUMB_SIZE, 'mimetypes', [ 'values' => 'image/jpeg, image/png' ] );
		$this->assertInvalidationFile( 'banner_file', 'png', Video::BANNER_SIZE, 'mimetypes', [ 'values' => 'image/jpeg, image/png' ] );
	}
	
	public function testStoreWithFiles() {
		UploadedFile::fake()->image( 'image.jpg' );
		\Storage::fake();
		$files = $this->getFiles();
		
		$category = factory( Category::class )->create();
		$genre    = factory( Genre::class )->create();
		$genre->categories()->sync( $category->id );
		
		$response = $this->json( 'POST', $this->routeStore(), $this->sendData + [
										   'categories_id' => [ $category->id ],
										   'genres_id'     => [ $genre->id ],
									   ] + $files );
		
		$response->assertStatus( 201 );
		$id = $response->json( 'id' );
		
		foreach ( $files as $file ) {
			\Storage::assertExists( "{$id}/{$file->hashName()}" );
		}
	}
	
	protected function getFiles() {
		return [
			'video_file'   => UploadedFile::fake()->create( 'video_file.mp4' ),
			'trailer_file' => UploadedFile::fake()->create( 'trailer_file.mp4' ),
			'thumb_file'   => UploadedFile::fake()->create( 'thumb_file.jpg' ),
			'banner_file'  => UploadedFile::fake()->create( 'banner_file.png' ),
		];
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
	
}
