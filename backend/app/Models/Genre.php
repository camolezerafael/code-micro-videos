<?php

namespace App\Models;

use App\ModelFilters\CastMemberFilter;
use App\Models\Traits\Uuid;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Genre extends Model {
	use SoftDeletes, Uuid, Filterable;
	
	protected $casts    = [ 'is_active' => 'boolean' ];
	protected $dates    = [ 'deleted_at', 'created_at', 'updated_at' ];
	protected $fillable = [ 'name', 'is_active' ];
	protected $keyType  = 'string';
	
	public $incrementing = false;
	
	public function categories() {
		return $this->belongsToMany( Category::class )->withTrashed();
	}
	
	public function modelFilter() {
		return $this->provideFilter( CastMemberFilter::class );
	}
}
