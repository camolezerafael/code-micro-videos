<?php

namespace Tests\Feature\Models\Video;

use App\Models\Video;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Http\UploadedFile;
use Tests\Exceptions\TestException;

class VideoUploadTest extends BaseVideoTestCase {
	
	public function testCreateIfRollbackFiles() {
		\Storage::fake();
		\Event::listen( TransactionCommitted::class, function() {
			throw new TestException();
		} );
		$hasError = false;
		
		try {
			Video::create(
				$this->data + [
					'video_file'   => UploadedFile::fake()->create( 'video.mp4' ),
					'trailer_file' => UploadedFile::fake()->create( 'trailer.mp4' ),
					'thumb_file'   => UploadedFile::fake()->image( 'thumb.jpg' ),
					'banner_file'  => UploadedFile::fake()->create( 'banner.png' ),
				]
			);
		} catch ( TestException $e ) {
			$this->assertCount( 0, \Storage::allFiles() );
			$hasError = true;
		}
		
		$this->assertTrue( $hasError );
	}
	
	public function testCreateWithFiles() {
		\Storage::fake();
		$video = Video::create(
			$this->data + [
				'video_file'   => UploadedFile::fake()->create( 'video.mp4' ),
				'trailer_file' => UploadedFile::fake()->create( 'trailer.mp4' ),
				'thumb_file'   => UploadedFile::fake()->image( 'thumb.jpg' ),
				'banner_file'  => UploadedFile::fake()->create( 'banner.png' ),
			] );
		\Storage::assertExists( "{$video->id}/{$video->thumb_file}" );
		\Storage::assertExists( "{$video->id}/{$video->video_file}" );
	}
	
	public function testUpdateWithFiles() {
		\Storage::fake();
		$video       = factory( Video::class )->create();
		$videoFile   = UploadedFile::fake()->create( 'video.mp4' );
		$trailerFile = UploadedFile::fake()->create( 'trailer.mp4' );
		$thumbFile   = UploadedFile::fake()->image( 'thumb.jpg' );
		$bannerFile  = UploadedFile::fake()->image( 'banner.jpg' );
		
		$video->update(
			$this->data + [
				'video_file'   => $videoFile,
				'trailer_file' => $trailerFile,
				'thumb_file'   => $thumbFile,
				'banner_file'  => $bannerFile,
			] );
		\Storage::assertExists( "{$video->id}/{$video->video_file}" );
		\Storage::assertExists( "{$video->id}/{$video->trailer_file}" );
		\Storage::assertExists( "{$video->id}/{$video->thumb_file}" );
		\Storage::assertExists( "{$video->id}/{$video->banner_file}" );
		
		$newVideoFile = UploadedFile::fake()->image( 'video.mp4' );
		$video->update( $this->data + [ 'video_file' => $newVideoFile ] );
		
		\Storage::assertExists( "{$video->id}/{$thumbFile->hashName()}" );
		\Storage::assertExists( "{$video->id}/{$newVideoFile->hashName()}" );
		\Storage::assertMissing( "{$video->id}/{$videoFile->hashName()}" );
	}
	
	public function testUpdateIfRollbackFiles() {
		\Storage::fake();
		$video = factory( Video::class )->create();
		\Event::listen( TransactionCommitted::class, function() {
			throw new TestException();
		} );
		
		$hasError = false;
		
		try {
			$video->update(
				$this->data + [
					'video_file'   => UploadedFile::fake()->create( 'video.mp4' ),
					'trailer_file' => UploadedFile::fake()->create( 'trailer.mp4' ),
					'thumb_file'   => UploadedFile::fake()->image( 'thumb.jpg' ),
					'banner_file'  => UploadedFile::fake()->create( 'banner.png' ),
				] );
			
		} catch ( TestException $e ) {
			$this->assertCount( 0, \Storage::allFiles() );
			$hasError = true;
		}
		$this->assertTrue( $hasError );
	}
}