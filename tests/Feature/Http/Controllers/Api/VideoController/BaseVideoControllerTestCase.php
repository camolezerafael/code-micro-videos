<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
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
		'deleted_at',
		'created_at',
		'updated_at',
		'categories_id',
		'genres_id',
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
	
}
