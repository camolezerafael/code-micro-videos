<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase {
	
	use DatabaseMigrations;
	
	public function testList() {
		factory( Category::class, 1 )->create();
		$categories = Category::all();
		$this->assertCount( 1, $categories );
		$categoryKey = array_keys( $categories->first()->getAttributes() );
		$this->assertEqualsCanonicalizing(
			[
				'id',
				'name',
				'description',
				'is_active',
				'created_at',
				'updated_at',
				'deleted_at',
			],
			$categoryKey
		);
	}
	
	public function testCreate() {
		/**
		 * @var Category $category
		 */
		$category = Category::create(
			[
				'name' => 'test1',
			] );
		
		$category->refresh();
		
		$this->assertEquals( 'test1', $category->name );
		$this->assertNull( $category->description );
		$this->assertTrue( $category->is_active );
		
		$category = Category::create(
			[
				'name'        => 'test2',
				'description' => null,
			] );
		$this->assertNull( $category->description );
		
		$category = Category::create(
			[
				'name'        => 'test3',
				'description' => 'test_description',
			] );
		$this->assertEquals( 'test_description', $category->description );
		
		$category = Category::create(
			[
				'name'      => 'test4',
				'is_active' => false,
			] );
		$this->assertFalse( $category->is_active );
		
		$category = Category::create(
			[
				'name'      => 'test5',
				'is_active' => true,
			] );
		$this->assertTrue( $category->is_active );
		
		$category = Category::create(
			[
				'name'      => 'test6',
				'is_active' => true,
			] );
		
		$id = $category->id;
		$this->assertEquals( 36, strlen( $id ) );
		
		$id = explode( '-', $category->id );
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
		 * @var Category $category
		 */
		$category = factory( Category::class )->create(
			[
				'description' => 'test_description',
				'is_active'   => false,
			] );
		
		$data = [
			'name'        => 'name_updated',
			'description' => 'description_updated',
			'is_active'   => true,
		];
		
		$category->update( $data );
		
		foreach ( $data as $key => $value ) {
			$this->assertEquals( $value, $category->{$key} );
		}
		
	}
	
	public function testDelete() {
		/**
		 * @var Category $category
		 */
		$category = factory( Category::class )->create(
			[
				'description' => 'test_description',
				'is_active'   => false,
			] );
		
		$before = Category::all()->count();
		
		$category->delete();
		
		$after = Category::all()->count();
		
		$this->assertEquals(1, $before);
		$this->assertEquals(0, $after);
		
	}
	
}
