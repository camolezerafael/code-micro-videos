<?php

namespace App\Models;

use App\ModelFilters\CategoryFilter;
use App\Models\Traits\Uuid;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model {
	use SoftDeletes, Uuid, Filterable;
	
	protected $casts    = [ 'is_active' => 'boolean' ];
	protected $dates    = [ 'deleted_at', 'created_at', 'updated_at' ];
	protected $fillable = [ 'name', 'description', 'is_active' ];
	protected $keyType  = 'string';
	
	public $incrementing = false;
	
	public function genres() {
		return $this->belongsToMany( Genre::class )->withTrashed();
	}
	
	public function modelFilter() {
		return $this->provideFilter( CategoryFilter::class );
	}
}
