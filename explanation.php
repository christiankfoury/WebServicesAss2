<?php

// The plain text to encrypt
$plaintext = "message to be encrypted";
// The length of the A non-NULL Initialization Vector.
// The openssl_cipher_iv_length() function returns the length of the IV on success and FALSE on error.
// It returns an E_WARNING level error when the cipher alorithm is unknown
// The parameter that it accepts is the cipher algorithm, which in this case is is AES-128-CBC.
// A cipher method is a way of hiding characters with an encryption algorithm, by replacing or substituting characters with a different character according to the alorithm.
$ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
// The openssl_random_pseudo_bytes generates a string of pseudo-random bytes according to the length parameter, which in this case is the ivlength, created from before.
$iv = openssl_random_pseudo_bytes($ivlen);
// The openssl_encrypt function encrypts the plaintext using the method, key, and returns a raw or base64 encoded string.
// The parameter that it accepts is the plaintext, which in this case is the message to be encrypted.
// The second parameter is the cipher method, which in this case is AES-128-CBC.
// The third parameter is the encryption key, which in this case is the passphrase.
// The fourth parameter is the options, which in this case is OPENSSL_RAW_DATA.
// OPENSSL_RAW_DATA is a constant that is used to specify that the data should be returned as a raw string, not base64 (which is by default).
// The fifth parameter is the Initialization Vector, which in this case is the variable created previously.
$ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
// hash_hmac() function returns a HMAC (hash-based message authentication code) of the given data using the given hash algorithm.
// According to PHP doc, it returns "lowercase hexits unless binary is set to true in which case the raw binary representation of the message digest is returned."
// The first parameter is the hash algorithm, which in this case is sha256.
// The second parameter is the data, which in this case is the ciphertext.
// The third parameter is the key, which is the shared secret key used for generating the HMAC variant
// The fourth parameter is an options parameter, which if set true, returns raw binary data.
$hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
// Base64 encode the make binary data survive transport through transport layers that are not 8-bit clean
// If this step is skipped, the data set will involve ? characters
$ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);

//decrypt later....
$c = base64_decode($ciphertext);
// Same thing as line 10.
$ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
// Extract the IV from the first 16 bytes of the ciphertext
$iv = substr($c, 0, $ivlen);
// Extract the HMAC from the 16 bytes after the IV
$hmac = substr($c, $ivlen, $sha2len = 32);
// Extract the ciphertext from the 16 bytes after the IV and the HMAC
$ciphertext_raw = substr($c, $ivlen + $sha2len);
// Decrypt the ciphertext using the same key and the same method
$original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
// Hashing the original plaintext. Method explained above.
$calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
// Comparing the two hashes together. If both are equal, then the text was not manipulated by a man in the middle
if (hash_equals($hmac, $calcmac)) // timing attack safe comparison
{
    echo $original_plaintext . "\n";
}