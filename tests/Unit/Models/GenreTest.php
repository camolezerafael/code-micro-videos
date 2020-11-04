<?php

namespace Tests\Unit\Models;

use App\Models\Genre;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\TestCase;

class GenreTest extends TestCase {
	
	use DatabaseMigrations;
	
	private $genre;
	
	protected function setUp()
	: void {
		parent::setUp();
		$this->genre = new Genre();
	}
	
	public function testIfUseTraits() {
		$traits = [
			SoftDeletes::class,
			Uuid::class,
		];
		
		$genreTraits = array_keys( class_uses( Genre::class ) );
		$this->assertEquals( $traits, $genreTraits );
	}
	
	public function testFillableAttribute() {
		$fillable = [ 'name', 'is_active' ];
		$this->assertEquals( $fillable, $this->genre->getFillable() );
	}
	
	public function testDatesAttribute() {
		$dates = [ 'deleted_at', 'created_at', 'updated_at' ];
		foreach ( $dates as $date ) {
			$this->assertContains( $date, $this->genre->getDates() );
		}
		$this->assertCount( count( $dates ), $this->genre->getDates() );
	}
	
	public function testCastsAttribute() {
		$casts = [ 'is_active' => 'boolean' ];
		$this->assertEqualsCanonicalizing( $casts, $this->genre->getCasts() );
	}
	
	public function testIncrementingAttribute() {
		$this->assertFalse( $this->genre->incrementing );
	}
	
}
