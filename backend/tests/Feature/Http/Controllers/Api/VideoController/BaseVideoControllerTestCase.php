<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;

abstract class BaseVideoControllerTestCase extends TestCase {
	
	use DatabaseMigrations;
	
	protected $video;
	protected $sendData;
	protected $serializedFields = [
		'id',
		'title',
		'description',
		'year_launched',
		'opened',
		'rating',
		'duration',
		'video_file',
		'thumb_file',
		'trailer_file',
		'banner_file',
		'video_file_url',
		'thumb_file_url',
		'trailer_file_url',
		'banner_file_url',
		'deleted_at',
		'created_at',
		'updated_at',
		'categories' => [
			'*' => [
				'id',
				'name',
				'description',
				'is_active',
				'deleted_at',
				'created_at',
				'updated_at',
			],
		],
		'genres'     => [
			'*' => [
				'id',
				'name',
				'is_active',
				'deleted_at',
				'created_at',
				'updated_at',
			],
		],
	];
	
	protected function setUp(): void {
		parent::setUp();
		
		$this->video = factory( Video::class )->create(
			[
				'opened' => false,
			] );
		$category    = factory( Category::class )->create();
		$genre       = factory( Genre::class )->create();
		$genre->categories()->sync( $category->id );
		
		$this->sendData = [
			'title'         => 'test',
			'description'   => 'test description',
			'year_launched' => 2000,
			'rating'        => Video::RATING_LIST[0],
			'duration'      => 90,
			'categories_id' => [ $category->id ],
			'genres_id'     => [ $genre->id ],
		];
	}
	
	protected function assertIfFilesUrlExists( Video $video, TestResponse $response ) {
		$fileFields = Video::$fileFields;
		$data       = $response->json( 'data' );
		$data       = array_key_exists( 0, $data ) ? $data[0] : $data;
		foreach ( $fileFields as $field ) {
			$file = $video->{$field};
			$this->assertEquals( \Storage::url( $video->relativeFilePath( $file ) ), $data[ $field . '_url' ] );
		}
	}
	
}
