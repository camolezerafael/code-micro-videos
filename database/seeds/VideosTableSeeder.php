<?php

use App\Models\Genre;
use App\Models\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

class VideosTableSeeder extends Seeder {
	
	protected $allGenres;
	protected $relations = [
		'genres_id'     => [],
		'categories_id' => [],
	];
	
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$dir = \Storage::getDriver()->getAdapter()->getPathPrefix();
		\File::deleteDirectory( $dir, true );
		$self            = $this;
		$this->allGenres = Genre::All();
		Model::reguard();
		factory( Video::class, 100 )
			->make()
			->each( function( Video $video ) use ( $self ) {
				$self->fetchRelations();
				Video::create(
					array_merge(
						$video->toArray(),
						[
							'video_file'   => $self->getVideoFile(),
							'thumb_file'   => $self->getImageFile(),
							'trailer_file' => $self->getVideoFile(),
							'banner_file'  => $self->getImageFile(),
						],
						$self->relations
					)
				);
			} );
		Model::unguard();
	}
	
	public function fetchRelations() {
		$subGenres    = $this->allGenres->random( 5 )->load( 'categories' );
		$genresId     = $subGenres->pluck( 'id' )->toArray();
		$categoriesId = [];
		foreach ( $subGenres as $genre ) {
			array_push( $categoriesId, ...$genre->categories()->pluck( 'id' )->toArray() );
		}
		$categoriesId                     = array_unique( $categoriesId );
		$this->relations['categories_id'] = $categoriesId;
		$this->relations['genres_id']     = $genresId;
	}
	
	public function getImageFile() {
		return new UploadedFile(
			storage_path( 'faker/thumbs/Laravel Framework.png' ),
			'Laravel Framework.png'
		);
	}
	
	public function getVideoFile() {
		return new UploadedFile(
			storage_path( 'faker/videos/video-teste.mp4' ),
			'video-teste.mp4'
		);
	}
}
