<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource {
	/**
	 * Transform the resource into an array.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return array
	 */
	public function toArray( $request ) {
		return [
			'id'            => $this->id,
			'title'         => $this->title,
			'description'   => $this->description,
			'year_launched' => $this->year_launched,
			'opened'        => $this->opened,
			'rating'        => $this->rating,
			'duration'      => $this->duration,
			'video_file'    => $this->video_file,
			'thumb_file'    => $this->thumb_file,
			'trailer_file'  => $this->trailer_file,
			'banner_file'   => $this->banner_file,
			'deleted_at'    => $this->deleted_at,
			'created_at'    => $this->created_at,
			'updated_at'    => $this->updated_at,
			'categories_id' => CategoryResource::collection( $this->categories ),
			'genres_id'     => GenreResource::collection( $this->genres ),
		];
	}
}
