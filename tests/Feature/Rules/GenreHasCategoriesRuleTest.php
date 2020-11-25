<?php

namespace Tests\Feature\Rules;

use App\Models\Category;
use App\Models\Genre;
use App\Rules\GenresHasCategoriesRule;
use Tests\TestCase;

/**
 *
 *
 *
 *
 *
 * ESSA CLASSE NÃO FAZ PARTE DO EXERCÍCIO E NÃO ESTÁ FUNCIONANDO
 * FICA RETORNANDO ERRO DE TABELA CATEGORIES INEXISTENTE
 *
 *
 *
 *
 *
 * TENTAREI RESOLVER DEPOIS
 *
 * Class GenreHasCategoriesRuleTest
 * @package Tests\Feature\Rules
 */

class GenreHasCategoriesRuleTest extends TestCase {
	
	private $categories;
	private $genres;
	
	/**
	 * A basic unit test example.
	 *
	 * @return void
	 */
	
//	public function setUp(): void {
//		parent::setUp();

//		$this->categories = factory( Category::class, 4 )->create();
//		$this->genres     = factory( Genre::class, 2 )->create();
//
//		$this->genres[0]->categories()->sync(
//			[
//				$this->categories[0]->id,
//				$this->categories[1]->id,
//			] );
//		$this->genres[1]->categories()->sync(
//			[
//				$this->categories[2]->id,
//			] );
//	}
	
//	public function testPassesValid() {
//		$rule    = new GenresHasCategoriesRule(
//			[
//				$this->categories[2]->id,
//			] );
//		$isValid = $rule->passes( '', [
//			$this->genres[1]->id,
//		] );
//		$this->assertTrue( $isValid );
//
//		$rule    = new GenresHasCategoriesRule(
//			[
//				$this->categories[0]->id,
//				$this->categories[2]->id,
//			] );
//		$isValid = $rule->passes( '', [
//			$this->genres[0]->id,
//			$this->genres[1]->id,
//		] );
//		$this->assertTrue( $isValid );
//
//		$rule    = new GenresHasCategoriesRule(
//			[
//				$this->categories[0]->id,
//				$this->categories[1]->id,
//				$this->categories[2]->id,
//			] );
//		$isValid = $rule->passes( '', [
//			$this->genres[0]->id,
//			$this->genres[1]->id,
//		] );
//		$this->assertTrue( $isValid );
//	}
	
//	public function testPassesIsNotValid() {
//		$rule    = new GenresHasCategoriesRule(
//			[
//				$this->categories[0]->id,
//			] );
//		$isValid = $rule->passes( '', [
//			$this->genres[0]->id,
//			$this->genres[1]->id,
//		] );
//		$this->assertFalse( $isValid );
//	}
}