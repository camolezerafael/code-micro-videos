<?php

namespace App\Models;

use App\ModelFilters\CastMemberFilter;
use App\Models\Traits\Uuid;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CastMember extends Model {
	use SoftDeletes, Uuid, Filterable;
	
	const TYPE_DIRECTOR = 1;
	const TYPE_ACTOR    = 2;
	
	protected $dates        = [ 'deleted_at', 'created_at', 'updated_at' ];
	protected $fillable     = [ 'name', 'type' ];
	protected $keyType      = 'string';
	
	public    $incrementing = false;
	
	public function modelFilter() {
		return $this->provideFilter( CastMemberFilter::class );
	}
	
}
