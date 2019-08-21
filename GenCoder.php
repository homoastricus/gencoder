<?php
header('Content-Type: text/html; charset=UTF-8');

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
mb_regex_encoding('UTF-8');
mb_internal_encoding("UTF-8");

Class GenCoder
{
    public $message;
    public $salt;
    public $key;
    private $pass_length = 4;
    public $max_size;
    private $salt_length = 8;
    const hash_length = 32;

    /**
     * GenCoder constructor.
     * @param $salt
     */
    public function __construct($salt = "")
    {
        if(mb_strlen($salt)<8){
            $this->salt = $this->randomSalt($this->salt_length);
        }
        $this->max_size = 64*1000;
        $this->salt = $salt;
    }

    /**
     * @param $pass
     * @param $salt
     * @return string
     */
    private function user_hashcode($pass, $salt){

        return md5($pass . $salt);
    }

    /**
     * @param $string
     * @return array
     */
    private function str_splitUTF8 ($string) {
        return preg_split('//u', $string, null, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * @param $user1_pass
     * @return string
     */
    public function sender_hashcode($user1_pass){
        return $user_code_1 = $this->user_hashcode($user1_pass, $this->salt);
    }

    /**
     * @param $user2_pass
     * @return string
     */
    public function receiver_hashcode($user2_pass){
        return $user_code_2 = $this->user_hashcode($user2_pass, $this->salt);
    }

    /**
     * @return string
     * @throws Exception
     */
    private function randomSalt($length){
        return substr(md5(random_bytes(16)), 0, $length);
    }

    /**
     * @param $message
     * @param $salt
     * @return string
     */
    private function attachKey($message, $salt){
        return md5($message . md5($salt) . md5($message . $salt));
    }

    /**
     * @param $user_code_1
     * @param $user_code_2
     * @param $attach_key
     * @return string
     */
    private function pathKeySignature($user_code_1, $user_code_2, $attach_key){
        return md5($user_code_1 . $attach_key . $user_code_2);
    }

    private function mb_chr($dec) {
        if(function_exists("mb_chr")){
            return mb_chr($dec, "UTF-8");
        } else {
            if ($dec < 128) {
                $utf = chr($dec);
            } else if ($dec < 2048) {
                $utf = chr(192 + (($dec - ($dec % 64)) / 64));
                $utf .= chr(128 + ($dec % 64));
            } else {
                $utf = chr(224 + (($dec - ($dec % 4096)) / 4096));
                $utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
                $utf .= chr(128 + ($dec % 64));
            }
            return $utf;
        }
    }

    private function mb_ord($char){
        $encoding = 'UTF-8';
        if (!function_exists('mb_ord')) {
                $c = $char;
                $index = 0;
                $len = strlen($c);
                $bytes = 0;

                if ($index >= $len)
                    return false;

                $h = ord($c{$index});

                if ($h <= 0x7F) {
                    $bytes = 1;
                    return $h;
                }
                else if ($h < 0xC2)
                    return false;
                else if ($h <= 0xDF && $index < $len - 1) {
                    $bytes = 2;
                    return ($h & 0x1F) <<  6 | (ord($c{$index + 1}) & 0x3F);
                }
                else if ($h <= 0xEF && $index < $len - 2) {
                    $bytes = 3;
                    return ($h & 0x0F) << 12 | (ord($c{$index + 1}) & 0x3F) << 6
                        | (ord($c{$index + 2}) & 0x3F);
                }
                else if ($h <= 0xF4 && $index < $len - 3) {
                    $bytes = 4;
                    return ($h & 0x0F) << 18 | (ord($c{$index + 1}) & 0x3F) << 12
                        | (ord($c{$index + 2}) & 0x3F) << 6
                        | (ord($c{$index + 3}) & 0x3F);
                }
                return false;
        } else {
            return mb_ord($char, $encoding);
        }
    }

/**
     * @param $max_size
     * @return string
     */
    private function generateKey($max_size){
         if(!function_exists("random_bytes")){
             $bytes = '';
             while (mb_strlen($bytes) < $max_size) {
                 $bytes .= chr(mt_rand(0, 255));
             }
             return $bytes;
         }
        return random_bytes($max_size);
    }

    /**
     * @param $message
     * @param $generateKey
     * @param $user1_pass
     * @param $receiver_hashcode
     * @return string
     */
    public function codeMessage($message, $generateKey, $user1_pass, $receiver_hashcode){
        $sender_hashcode = $this->sender_hashcode($user1_pass);
        $attach_key = $this->attachKey($message, $this->salt);
        $path_key_signature = $this->pathKeySignature($sender_hashcode, $receiver_hashcode, $attach_key);
        $result_cypher = $this->cypher($path_key_signature, $message, $generateKey) .  $attach_key;
        $zipped_string = gzencode($result_cypher, 9);
        return $zipped_string;
    }

    /**
     * @param $path_key_signature
     * @param $message
     * @param $generateKey
     * @return string
     */
    private function cypher($path_key_signature, $message, $generateKey){
        $cyper_message = "";
        $message = $this->str_splitUTF8($message);
        $sign_key = 0;
        $cur_key_pos = 0;
        $key_length = mb_strlen($generateKey, "UTF-8");
        for ($i = 0; $i < count($message); $i++) {
            if($sign_key>=self::hash_length) $sign_key = 0;
            $key_code_pos = hexdec($path_key_signature[$sign_key]);
            $cur_key_pos = $cur_key_pos+$key_code_pos;
            if($cur_key_pos>=$key_length){
                $cur_key_pos = $cur_key_pos - $key_length;
            }
            $key_symbol = $generateKey[$cur_key_pos];
            $cyper_message .= $this->mb_chr($this->mb_ord($message{$i}) ^ $this->mb_ord($key_symbol), "UTF-8");
            $sign_key++;
        }

        return $cyper_message;
    }

    public function decodeMessage($cyper, $generateKey, $user1_hashcode, $user2_pass){
        $cyper = gzdecode($cyper);
        $user2_hashcode = $this->receiver_hashcode($user2_pass);
        $attach_key_test = mb_substr($cyper, mb_strlen($cyper, "UTF-8")-self::hash_length, self::hash_length, "UTF-8");
        $path_key_signature_test = $this->pathKeySignature($user1_hashcode, $user2_hashcode, $attach_key_test);
        $trimmed_cyper = mb_substr($cyper, 0, mb_strlen($cyper, "UTF-8")-self::hash_length, "UTF-8");
        return $this->cypher($path_key_signature_test, $trimmed_cyper, $generateKey);
    }

    public function init(){
        $generateKey = $this->generateKey($this->max_size);
        $pass1 = $this->createPass();
        $pass2 = $this->createPass();
        $init_data = [
            'key' => $generateKey,
            'pass1' => $pass1,
            'pass2' => $pass2,
        ];
        return $init_data;
    }

    public function createPass(){
        $pass = "";
        for($t=0; $t<$this->pass_length; $t++){
            $pass.= random_int(0, 9);
        }
        return $pass;
    }
}