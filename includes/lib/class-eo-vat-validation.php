<?php
/**
 * Modified version of https://github.com/herdani/vat-validation
 */
class EO_Vat_Validation
{
	const WSDL = "http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl";
	private $_client = null;

	private $options  = array(
		'debug' => false,
	);

	private $_valid = false;
	private $_data = array();

	public function __construct($options = array()) {

		foreach($options as $option => $value) {
			$this->options[$option] = $value;
		}

		if(!class_exists('SoapClient')) {
			throw new Exception('The Soap library has to be installed and enabled');
		}

		$this->_client = new SoapClient(self::WSDL, array('trace' => true) );
	}

	public function check($qualifiedVatNumber) {

		$qualifiedvatNumber = str_replace( ' ', '', strtoupper( $qualifiedVatNumber ) );
		$countryCode = substr( $qualifiedVatNumber, 0, 2 );
		$vatNumber = substr( $qualifiedVatNumber, 2 );

		try {
			$rs = $this->_client->checkVat( array('countryCode' => $countryCode, 'vatNumber' => $vatNumber) );
		} catch ( Exception $e ) {
			return false;
		}

		if($rs->valid) {
			$this->_valid = true;
			list($denomination,$name) = explode(" " ,$rs->name,2);
			$this->_data = array(
				'denomination' => $denomination,
				'name'         => $this->cleanUpString($name),
				'address'      => $this->cleanUpString($rs->address),
			);
			return true;
		} else {
			$this->_valid = false;
			$this->_data = array();
		  return false;
		}
	}

	public function isValid() {
		return $this->_valid;
	}

	public function getDenomination() {
		return $this->_data['denomination'];
	}

	public function getName() {
		return $this->_data['name'];
	}

	public function getAddress() {
		return $this->_data['address'];
	}

	public function isDebug() {
		return ($this->options['debug'] === true);
	}

	private function cleanUpString($string) {
		for( $i = 0; $i<100; $i++ ) {
			$newString = str_replace("  "," ",$string);
			if($newString === $string) {
				break;
			} else {
				$string = $newString;
			}
		}

		$newString = "";
		$words = explode(" ",$string);
		foreach( $words as $k => $w ) {
			$newString .= ucfirst( strtolower( $w ) ) . " ";
		}
		return $newString;
	}
}

?>
