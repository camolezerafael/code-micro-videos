<?php

namespace Tests\Traits;

use Illuminate\Foundation\Testing\TestResponse;

trait TestValidations {
	
	protected function assertInvalidationInStoreAction( array $data, string $rule, $ruleParams = [] ) {
		$response = $this->json( 'POST', $this->routeStore(), $data );
		$fields   = array_keys( $data );
		$this->assertInvalidationsFields( $response, $fields, $rule, $ruleParams );
	}
	
	protected function assertInvalidationInUpdateAction( array $data, string $rule, $ruleParams = [] ) {
		$response = $this->json( 'PUT', $this->routeUpdate(), $data );
		$fields   = array_keys( $data );
		$this->assertInvalidationsFields( $response, $fields, $rule, $ruleParams );
	}
	
	protected function assertInvalidationsFields( TestResponse $response, array $fields, string $rule, array $ruleParams = [] ) {
		$response
			->assertStatus( 422 )
			->assertJsonValidationErrors( $fields );
		
		foreach ( $fields as $field ) {
			$fieldName = str_replace( '_', ' ', $field );
			
			$response->assertJsonFragment(
				[
					\Lang::get( "validation.{$rule}", [ 'attribute' => $fieldName ] + $ruleParams ),
				] );
		}
	}
	
	protected function assertStatusAndJson( TestResponse $response, int $statusExpected, array $testJsonData ) {
		return $response->assertStatus( $statusExpected )
						->assertJson( $testJsonData );
		
	}
	
	protected function assertStatusAndStructure( TestResponse $response, int $statusExpected, array $testJsonStructure ) {
		return $response->assertStatus( $statusExpected )
						->assertJsonStructure( $testJsonStructure );
	}
	
}
