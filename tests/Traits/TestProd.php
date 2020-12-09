<?php


namespace Tests\Traits;


trait TestProd {
	protected function skipTestIfNotProd( $message = '' ) {
		if ( !$this->isTestingProd() ) {
			$this->markTestSkipped( $message ?? 'Testes de Produção' );
		}
	}
	
	protected function isTestingProd() {
		return env( 'TESTING_PROD' ) !== false;
	}
}