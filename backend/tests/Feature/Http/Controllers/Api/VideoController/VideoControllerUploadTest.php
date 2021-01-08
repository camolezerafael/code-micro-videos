<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Traits\UploadFiles;
use App\Models\Video;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\UploadedFile;
use Tests\Feature\Http\Controllers\Api\VideoController\BaseVideoControllerTestCase;
use Tests\Traits\TestUploads;
use Tests\Traits\TestValidations;

class VideoControllerUploadTest extends BaseVideoControllerTestCase {
	
	use TestValidations, TestUploads;
	
	public function testInvalidationFilesFields() {
		$this->assertInvalidationFile( 'video_file', 'mp4', Video::VIDEO_SIZE, 'mimetypes', [ 'values' => 'video/mp4' ] );
		$this->assertInvalidationFile( 'trailer_file', 'mp4', Video::TRAILER_SIZE, 'mimetypes', [ 'values' => 'video/mp4' ] );
		$this->assertInvalidationFile( 'thumb_file', 'jpg', Video::THUMB_SIZE, 'mimetypes', [ 'values' => 'image/jpeg, image/png' ] );
		$this->assertInvalidationFile( 'banner_file', 'png', Video::BANNER_SIZE, 'mimetypes', [ 'values' => 'image/jpeg, image/png' ] );
	}
	
	public function testStoreWithFiles() {
		\Storage::fake();
		$files = $this->getFiles();
		
		$response = $this->json( 'POST', $this->routeStore(), $this->sendData + $files );
		
		$response->assertStatus( 201 );
		$this->assertFilesOnPersist( $response, $files );
	}
	
	public function testUpdateWithFiles() {
		\Storage::fake();
		$files = $this->getFiles();
		
		$response = $this->json( 'PUT', $this->routeUpdate(), $this->sendData + $files );
		
		$response->assertStatus( 200 );
		$this->assertFilesOnPersist( $response, $files );
		
		$newFiles = [
			'thumb_file' => UploadedFile::fake()->create( 'thumb_file.jpg' ),
			'video_file' => UploadedFile::fake()->create( 'video_file.mp4' ),
		];
		
		$response = $this->json( 'PUT', $this->routeUpdate(), $this->sendData + $newFiles );
		$response->assertStatus( 200 );
		$this->assertFilesOnPersist( $response, \Arr::except( $files, [ 'thumb_file', 'video_file' ] ) + $newFiles );
		
		$id    = $response->json( 'data.id' );
		$video = Video::find( $id );
		\Storage::assertMissing( $video->relativeFilePath( $files['thumb_file']->hashName() ) );
		\Storage::assertMissing( $video->relativeFilePath( $files['video_file']->hashName() ) );
	}
	
	protected function assertFilesOnPersist( TestResponse $response, $files ) {
		$id    = $response->json( 'data.id' );
		$video = Video::find( $id );
		$this->assertFilesExistsInStorage( $video, $files );
	}
	
	protected function assertFilesExistsInStorage( $model, array $files ) {
		/** @var UploadFiles $model */
		foreach ( $files as $file ) {
			\Storage::assertExists( $model->relativeFilePath( $file->hashName() ) );
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
