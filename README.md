GenCoder is an open source php symmetric cipher library 

What is it and for what?

- **GenCoder library provides the ability to reversibly encrypt any information for your application needs**
- **A cryptographically strong encryption algorithm is used (a variation of the Vernam cipher, a random encoding sequence as the source key)**
 - **The encryption algorithm is further enhanced by randomizing the key for each unique message**
  - **The library uses a data compression mechanism to optimize the transfer and storage of encrypted information**
   - **The library is simple, contains an open algorithm and is easy to use.**
   - **Free for using**


###sandbox for testing [gencoder.ru](http://gencoder.ru/)

####How it works:
1. Connection of the GenCoder library
// GenCoder class library is connected

`require_once("GenCoder.php");`

`$salt = "My in-app encryption salt";`

`$gen_coder = new GenCoder($salt);`


2. Generation of confidential data

On initial startup, as well as upon reinitialization of the key / passwords,
generation of secret key and passwords of the sender and addressee for a given communication channel

`$gen_params = $gen_coder->init();
$key = $gen_params['key'];
$pass1 = $gen_params['pass1'];
$pass2 = $gen_params['pass2'];`

// encoded message
`$sender_hashcode = $gen_coder->sender_hashcode($pass1);
$receiver_hashcode = $gen_coder->receiver_hashcode($pass2);`

3. Message encryption
// The original message sent by the sender to the recipient is encrypted
`$coded_message = $gen_coder->codeMessage($message, $key, $pass1, $receiver_hashcode);`

4. The message is encrypted and compressed.
// The original message is securely encrypted,
// and also compressed to optimize storage and transmission of the cipher over the network to the addressee
`$coded_message = $gen_coder->codeMessage($message, $key, $pass1, $receiver_hashcode);`

5. Message decryption
// To decrypt the cipher, the recipient code is required, as well as the presence of a secret channel code.
// Thus, the information is reliably protected from tampering and tampering
// $key is taken from the storage at the destination
// $sender_hashcode - hash (public key) of the sender
// $pass2 - the password of the recipient to decrypt the channel with this sender

`$plain_message = $gen_coder->decodeMessage($coded_message, $key, $sender_hashcode, $pass2);`
//$plain_message - source, decrypted information


###Enhancing key protection by generating a key bypass function (path signature)

Usually, when using the one-time notepad method or algorithms close to it, messages in an encrypted communication channel are encrypted each time with a unique key from unique sequences of random characters. In this case, the following critical problems arise:
1. It is necessary to transmit a unique random key in advance and send it to the recipient for each cipher message.
2. For large amounts of data, it is required to transfer large keys from random characters in order to preserve the cryptographic stability of the algorithm, which in itself is laborious and inconvenient.
The randomization of the secret key allows you to get rid of these two problems at once by a simple solution:
1. The original secret key consists of randomly generated characters created one-time and stored at the addressee and sender when creating an encrypted channel. At the same time, the secret key is quite small, by default it is from 64 to 100 characters.
2. With each new encryption of the sender's message based on a complex algorithm, a method of bypassing the source key is generated, and the method depends on the hashes of the keys of the recipient and sender, the encrypted message and the source key.
That is, objectively speaking, if during encryption using the Vernam method the first character of the ciphertext is obtained by the XOR operation of the first character of the source text with the first characters of the key and so on, until the characters of the entire message and key are sorted in arithmetic order, this method compares the sequence of characters of the original message own key character sequence.