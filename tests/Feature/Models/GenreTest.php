<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GenreTest extends TestCase {
	
	use DatabaseMigrations;
	
	public function testList() {
		factory( Genre::class, 1 )->create();
		$genres = Genre::all();
		$this->assertCount( 1, $genres );
		$genreKey = array_keys( $genres->first()->getAttributes() );
		$this->assertEqualsCanonicalizing(
			[
				'id',
				'name',
				'is_active',
				'created_at',
				'updated_at',
				'deleted_at',
			],
			$genreKey
		);
	}
	
	public function testCreate() {
		/**
		 * @var Genre $genre
		 */
		$genre = Genre::create(
			[
				'name' => 'test1',
			] );
		
		$genre->refresh();
		
		$this->assertEquals( 'test1', $genre->name );
		$this->assertTrue( $genre->is_active );
		
		$genre = Genre::create(
			[
				'name'      => 'test2',
				'is_active' => false,
			] );
		$this->assertFalse( $genre->is_active );
		
		$genre = Genre::create(
			[
				'name'      => 'test3',
				'is_active' => true,
			] );
		$this->assertTrue( $genre->is_active );
		
		$genre = Genre::create(
			[
				'name'      => 'test4',
				'is_active' => true,
			] );
		
		$id = $genre->id;
		$this->assertEquals( 36, strlen( $id ) );
		
		$id = explode( '-', $genre->id );
		$this->assertEquals( 5, count( $id ) );
		
		$expected = [
			8, 4, 4, 4, 12,
		];
		
		foreach ( $id as $key => $value ) {
			$this->assertEquals( $expected[ $key ], strlen( $value ) );
		}
	}
	
	public function testUpdate() {
		/**
		 * @var Genre $genre
		 */
		$genre = factory( Genre::class )->create(
			[
				'name' => 'test_name',
				'is_active'   => false,
			] );
		
		$data = [
			'name'        => 'name_updated',
			'is_active'   => true,
		];
		
		$genre->update( $data );
		
		foreach ( $data as $key => $value ) {
			$this->assertEquals( $value, $genre->{$key} );
		}
		
	}
	
	public function testDelete() {
		/**
		 * @var Genre $genre
		 */
		$genre = factory( Genre::class )->create(
			[
				'name' => 'test_name'
			] );
		
		$before = Genre::all()->count();
		
		$genre->delete();
		
		$after = Genre::all()->count();
		
		$this->assertEquals(1, $before);
		$this->assertEquals(0, $after);
		
	}
	
}
