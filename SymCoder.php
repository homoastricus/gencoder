<?php

Class SymCoder
{
    public $min_coded_message_length = 100;

    public $symbols = [
        'А',        'Б',
        'В',        'Г',        'Д',        'Е',        'Ё',        'Ж',        'З',        'И',        'Й',        'К',        'Л',        'М',        'Н',        'О',        'П',
        'Р',        'С',        'Т',        'У',        'Ф',        'Х',        'Ц',        'Ч',        'Ш',        'Щ',        'Ы',        'Ъ',        'Ь',
        'Э',        'Ю',        'Я',        'а',        'б',        'в',        'г',        'д',        'е',        'ё',        'ж',        'з',        'и',        'й',        'к',
        'л',        'м',        'н',        'о',        'п',        'р',        'с',        'т',        'у',        'ф',        'х',        'ц',        'ш',        'ч',
        'щ',        'ы',        'ъ',        'ь',        'э',        'ю',        'я',
        '0',        '1',        '2',        '3',        '4',        '5',        '6',        '7',        '8',        '9',        ' ',        ',',        '.',        "\n",       "\n\n",
        "\r\n",     "\r", '  ',
        '!',        '?',        '-',
        'A',	'B',	'C',	'D',	'E',	'F',	'G',	'H',	'I', 'J',	'K',	'L',	'M',	'N',	'O',	'P',	'Q',	'R',	'S',	'T',	'U', 'V', 'W',	'X',	'Y',	'Z',
        'a',        'b',        'c',        'd',        'e',        'f',        'g',        'h', 'i',  'j', 'k',  'l',        'm',        'n',        'o',        'p',        'q',        'r',
        's',        't',       'u',  'v',        'w',        'x',        'y',        'z',        '/', '|',        '\\',
        '~',        '@',        '#',        '$',        '%',        '^',        '&',        '*',        '(',        ')',        '-',        '_',        '=',        '+',        '[',        ']',
        '{',        '}',        ':',        ';',        '"',        '<',        '>', '`', '\'', '№',
   ];

    private $tab_coded;

    public $coded_message;

    public $coded_symbols =
        [
        '$',
        '%',
        '^',
        '*',
        '|',
        '<',
        '>',
        '~',
        '+',
        '_',
        '-',
        '!',
        '&',
        '#',
        '@',
        '1',
        '=',
        ':',
        '.',
        '?'
        ];

    /**
     * SymCoder constructor.
     * @param $code
     */
    public function __construct($code)
    {
        $this->code = $code;
        $this->coded_symbols = $this->mixing_coded_symbols();
    }

    private function mixing_coded_symbols(){
        $mix_order = substr(base_convert(md5($this->code), 32, 10), 0, 3) % 2;
        if($mix_order == 0){
            return array_reverse($this->coded_symbols);
        } else {
            return $this->coded_symbols;
        }
    }

    /**
     * @param $code
     * @param $coded_symbols
     * @param $symbols
     * @param $message_digest
     * @return array
     */
    private function create_tab_coded($code, $coded_symbols, $symbols, $message_digest)
    {
        // создаем хеш по которому будем двигаться затем, переводя из 16-ой системы в count($coded_symbols)
        $hash = md5($code);
        // карта обхода символов
        $symbols_pattern = array();
        $hash_array = $this->mb_str_split($hash);
        foreach ($hash_array as $hash_elem) {
            if (count($symbols_pattern) > count($coded_symbols)) {
                break;
            }
            $test_elem = base_convert($hash_elem, 16, 10);
            if (!in_array($test_elem, $symbols_pattern) && $test_elem < count($coded_symbols)) {
                $symbols_pattern[] = $test_elem;
            }
        }
        $r=0;
        while(count($hash_array)<32){
            $hash_array[] = $hash_array[$r];
            $r++;
        }
        $x = 0;
        while (count($symbols_pattern) < count($coded_symbols) AND $x<32) {
            for ($t = 1; $t <= 20; $t++) {
                $test_elem = base_convert($hash_array[$x], 16, 10) - $t;
                if ($test_elem >= 0) {
                    if (!in_array($test_elem, $symbols_pattern) && $test_elem < count($coded_symbols)) {
                        $symbols_pattern[] = $test_elem;
                    }
                }
            }
            $x++;
        }
        $m = 20;
        while(count($symbols_pattern)<20) {
            $symbols_pattern[] = $m;
            $m--;
        }

        $base_coded = count($coded_symbols);
        $init_pos = substr(base_convert(md5($message_digest), 32, 10), 0, 2) % $base_coded;
        $method_tab = array();
        $noise_symbols = $this->generate_noise($this->code, $this->coded_symbols);
        foreach ($symbols as $symbol) {
            $key_ready = false;
            for ($x = $init_pos; $x > 0; $x--) {
                for ($y = count($coded_symbols) - 1; $y > 0; $y--) {
                    $x_pos = $symbols_pattern[$x];
                    $y_pos = $symbols_pattern[$y];
                    if($x_pos>=20 OR $y_pos>=20) continue;
                    $key = $coded_symbols[$x_pos] . $coded_symbols[$y_pos];
                    if (!array_key_exists($key, $method_tab)
                        && !in_array($symbol, $method_tab)
                        // проверка что символа нет в шумовых
                        && !in_array($key, $noise_symbols)
                        && strlen($key)>1
                        && substr(base_convert(md5($message_digest), 32, 10), $x_pos, 2) % 2 == 0
                    ) {

                        $key_ready = true;
                        $method_tab[$key] = $symbol;
                    }
                }
            }
            if (!$key_ready) {
                for ($x = count($coded_symbols) - 1; $x >= $init_pos; $x--) {
                    for ($y = count($coded_symbols) - 1; $y > 0; $y--) {
                        $x_pos = $symbols_pattern[$x];
                        $y_pos = $symbols_pattern[$y];
                        if($x_pos>=20 OR $y_pos>=20) continue;
                        $key = $coded_symbols[$x_pos] . $coded_symbols[$y_pos];
                        if (!array_key_exists($key, $method_tab)
                            && !in_array($symbol, $method_tab)
                            // проверка что символа нет в шумовых
                            && !in_array($key, $noise_symbols)
                            && strlen($key)>1
                            && substr(base_convert(md5($message_digest), 32, 10), $x_pos, 2) % 2 == 0
                        ) {
                            $method_tab[$key] = $symbol;
                        }
                    }
                }
            }
        }

        return $method_tab;
    }

    /**
     * @param $string
     * @param int $string_length
     * @return array
     */
    private function mb_str_split($string, $string_length = 1)
    {
        if (mb_strlen($string) > $string_length || !$string_length) {
                do {
                $c = mb_strlen($string);
                $parts[] = mb_substr($string, 0, $string_length);
                $string = mb_substr($string, $string_length);
            } while (!empty($string));
        } else {
            $parts = array($string);
        }
        return $parts;
    }

    /**
     * @param $code
     * @param $coded_symbols
     * @return array
     */
    private function generate_noise($code, $coded_symbols)
    {
        $noises = array();
        $hash_numbers = base_convert(md5("noise" . $code), 16, 10);
        $hash_numbers_2 = base_convert(md5($code . "noise"), 16, 10);
        $c = count($coded_symbols);
        foreach ($this->mb_str_split($hash_numbers) as $item) {
            if ($item >= $c) continue;
            $x = $item;
            foreach ($this->mb_str_split($hash_numbers_2) as $item_2) {
                if ($item_2 >= $c) continue;
                $y = $item_2;
                $noise_item = $coded_symbols[$x] . $coded_symbols[$y];
                if (!in_array($noise_item, $noises)) {
                    if(count($noises)>20) continue;
                    $noises[] = $noise_item;
                }
            }
        }

        return $noises;
    }

    private function message_digest($code, $message){
        return substr(md5($code . $message), 0, 8);
    }

    /**
     * @param $message
     * @return string
     */

    public function code($message)
    {
        if(mb_strlen($message)==0) {
            return "";
        }
        $message_digest = $this->message_digest($this->code, $message);
        $this->tab_coded = $this->create_tab_coded($this->code, $this->coded_symbols, $this->symbols, $message_digest);
        $total = array();
        //результирующий массив из кодированных символов
        $result_code_message_array = [];
        foreach ($this->mb_str_split($message) as $symbol) {
            foreach ($this->tab_coded as $key => $value) {
                if ($value == $symbol) {
                    $result_code_message_array[] = $key;
                }
            }
        }

        $noise_symbols = $this->generate_noise($this->code, $this->coded_symbols);
        if (count($noise_symbols) > 0) {
            // плотность разбавления полезного сообщения шумовыми символами
            $density = count($noise_symbols) / count($result_code_message_array);
            //результирующая набивка

            foreach ($result_code_message_array as $result_code_elem) {
                if (rand(0, 1) < $density) {
                    $total[] = $noise_symbols[array_rand($noise_symbols, 1)];
                }
                $total[] = $result_code_elem;
            }
            $total = implode("", $total);
            $d = 0;
            while (strlen($total)<count($result_code_message_array)*1.5 OR
                strlen($total)<$this->min_coded_message_length){
                if($d%2==0) {
                    $total = $total.$noise_symbols[array_rand($noise_symbols, 1)];
                } else {
                    $total = $noise_symbols[array_rand($noise_symbols, 1)] . $total;
                }
                $d++;
            }
        }

        return "Digest-" . $message_digest . "-" . $total;
    }

    /**
     * @param $message
     * @param $message_digest
     * @return string
     */
    public function decode($message)
    {
        if(mb_substr($message, 0, 7)!=="Digest-") {
            return "Error: Incorrect message found";
        }
        $digest = mb_substr($message, 7, 8);
        $message = mb_substr($message, 16);
        $this->mixing_coded_symbols();
        $supertab = $this->create_tab_coded($this->code, $this->coded_symbols, $this->symbols, $digest);
        $c = 0;
        $collector = "";
        $message_formatted = array();
        foreach ($this->mb_str_split($message) as $item) {
            $c++;
            $collector .= $item;
            if ($c == 2) {
                $message_formatted[] = $collector;
                $collector = "";
                $c = 0;
            }
        }
        $result = "";
        $noise_elements = $this->generate_noise($this->code, $this->coded_symbols);
        foreach ($message_formatted as $symbol) {
            if (key_exists($symbol, $supertab)) {
                if(in_array($symbol, $noise_elements)) continue;
                $result = $result . $supertab[$symbol];
            }
        }
        return $result;
    }
}