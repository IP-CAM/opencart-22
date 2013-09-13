<?php 
namespace in\verse\ipayy\crypto {
class CryptoUtils {

	const APPLICATION_KEY_PARAM = "a";
	const ENCRYPTED_STRING_PARAM = "gh";
	const MERCHANT_KEY_PARAM = "m";
	const REQUEST_TOKEN_PARAM = "r";
	const PACKAGE_ID_PARAM = "p";
	const CALLBACK_FUNCTION_PARAM = "cf";
	const CUSTOMER_TOKEN_ID_PARAM = "ti";
	const COUNTRY_KEY_PARAM = "co";
	const ITEM_NAME_PARAM = "in";
	const ITEM_CODE_PARAM = "ic";
	const ITEM_PRICE_PARAM = "ip";
	const TXN_ID_PARAM = "tx";
	const CURRENCY_PARAM = "c";
	const MSISDN_PARAM = "mn";
	const OPERATOR_NAME_PARAM = "op";
	const REDIRECT_URL_PARAM = "ru";

	private static $ALLOWED_KEYS = array (
		self::APPLICATION_KEY_PARAM,
		self::ENCRYPTED_STRING_PARAM,
		self::MERCHANT_KEY_PARAM,
		self::REQUEST_TOKEN_PARAM,
		self::PACKAGE_ID_PARAM,
		self::CALLBACK_FUNCTION_PARAM,
		self::CUSTOMER_TOKEN_ID_PARAM,
		self::TXN_ID_PARAM,
		self::COUNTRY_KEY_PARAM,
		self::ITEM_NAME_PARAM,
		self::ITEM_CODE_PARAM,
		self::ITEM_PRICE_PARAM,
		self::CURRENCY_PARAM,
		self::MSISDN_PARAM,
		self::OPERATOR_NAME_PARAM,
		self::REDIRECT_URL_PARAM
	);
	const MAX_ENCRYPTION_LIMIT = 116;
	const DECRYPTION_BLOCK_SIZE = 128;
	const KEY_FILE = "-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCEEUJXC16atHs+nMN/gE/oSJyX
kebmd63Rjat+ia0txMUtdynHXpRQ0me1DY7LOSh6AT9NyhqGNb4x04j+C83M5DyT
TPj7ATZi58Et2EoZugE6UCx/cEmPE3IMFEJ0IVu/DtYn3haXo1EpiW5rSzAvJc0Z
ulnaybcE0jE1f8wwkwIDAQAB
-----END PUBLIC KEY-----";

	/**
	 * Accepts a array of parameters which should be encrypted using an encryption algorithm. The array keys are checked
	 * against pre-defined values. If any key is not amongst the pre-defined values, then a {@link CryptoException} is
	 * thrown. The data length is also validated before the encryption process starts. In case the data exceeds the max
	 * encryption limit, {@link CryptoException} is thrown.
	 *
	 * @param params
	 *            The parameter array which need to be encrypted
	 * @return The encrypted String. The value needs to be appended in the url with the key as defined in
	 *         {@link CryptoUtils#ENCRYPTED_STRING_PARAM}
	 * @throws CryptoException
	 */
	public static function encrypt(array $params) {
		if (!isset($params) || count($params) == 0) {
			throw new CryptoException("No Parameters specified!");
		}
		$escapedArray = array();
		foreach ($params as $k => $v) {
			if (in_array($k, self::$ALLOWED_KEYS)) {
				array_push($escapedArray, $k . "=" . self::getEscapedValue($v));
			} else {
				throw new CryptoException($k . " is not allowed to be encrypted");
			}
		}
		if (count($escapedArray) != 0) {
			$finalParamToEncrypt = implode("&", $escapedArray);
			$splitted = str_split($finalParamToEncrypt, self::MAX_ENCRYPTION_LIMIT);
			$encrypted = "";
			foreach ($splitted as $block) {
				if (openssl_public_encrypt($block, $crypted, self::KEY_FILE)) {
					$encrypted = $encrypted . $crypted;
				} else {
					throw new CryptoException("Encryption failed");
				}
			}
			return strtr(base64_encode($encrypted), '+/', '-_');
		}
		else {
			throw new CryptoException("Unable to generated escaped array.");
		}
		return $finalParamToEncrypt;
	}
	
	/**
	 * This method decrypts the encrypted string using a pre-defined decryption algorithm and returns a map of
	 * parameters which were encrypted by the iPayy Server.
	 *
	 * @param encryptedString
	 *            The encrypted String
	 * @return The decrypted array of parameters.
	 * @throws CryptoException
	 */
	public static function decrypt($encrypted) {
		if (gettype($encrypted) === "string") {
			$splitted = str_split(base64_decode(strtr($encrypted, '-_', '+/')), self::DECRYPTION_BLOCK_SIZE);
			$originalString = "";
			foreach ($splitted as $block) {
				if (openssl_public_decrypt($block, $decryptedBlock, self::KEY_FILE)) {
					$originalString = $originalString . $decryptedBlock;
				} else {
					throw new CryptoException("Decryption failed");
				}
			}
			$params = array();
			parse_str($originalString, $params);
			foreach ($params as $k => $v) {
				$params[$k] = self::getOriginalValue($v);
			}
			return $params;
		} else {
			throw new CryptoException("Only strings are supposed to be decrypted.");
		}
	}

	private static function getEscapedValue($value) {
		if ($value == null || $value == "") {
			return "#";
		} else if (strpos($value, "#") === 0) {
			return "#" . $value;
		} else
		{
			return urlencode($value);
		}
	}

	private static function getOriginalValue($escapedValue) {
		if ($escapedValue == null) {
			throw new CryptoException("value cannot be null");
		} else if ($escapedValue == "#") {
			return null;
		} else if (strpos($escapedValue, "#") === 0) {
			return substr($escapedValue, 1);
		} else {
			return $escapedValue;
		}
	}
}
class CryptoException extends \Exception {}
}
?>