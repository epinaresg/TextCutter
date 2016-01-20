<?php

namespace TextCutter;

class TextCutter
{

    /** Output max length */
    public $max_length = 150;

    /** Enable hyperlink in the output*/
    public $hyperlinks = false;

    /** Input text */
    public $text = '';

    /** Replace spaces with HTML spaces */
    public $html_spaces = false;


    /** Stores words from the Input text */
    private $words = array();

    /** Stores words */
    private $cut_words = array();

    /** Output text */
    private $cut_text;

    /** Gets all the words from the input text */
    private function get_words() {

        $currentText = $this->text;

        $this->words = explode(' ', $currentText);

    }

    /** Check if the word is a hyperlink
     * @param $word
     * @return bool
     */
    private function is_a_hyperlink( $word) {

        $lower_words = strtolower($word);
        if(substr($lower_words, 0, 7) === 'http://')
            return true;
        else if(substr($lower_words, 0, 8) === 'http://')
            return true;
        else if(substr($lower_words, 0, 4) === 'www.')
            return true;

    }


    /** Build a HTML anchor tag
     * @param $text
     * @param $href
     * @return string
     */
    private function build_hyperlink($text, $href) {
        return '<a href="' . $href . '" target="_blank">' . $text . '</a>';
    }

    /** Cut the input text
     * @return string
     */
    public function init() {

        $this->get_words();

        $max_length = $this->max_length;

        $words = $this->words;

        $count_words = count($words);

        $count_length = 0;

        for($i = 0; $i < $count_words; $i++) {
            $current_word = $words[$i];
            $count_length += strlen($current_word) + 1;

            $this->cut_words[$i] = $current_word;

            if($this->hyperlinks && $this->is_a_hyperlink($current_word))
                $this->cut_words[$i] = $this->build_hyperlink($current_word, $current_word);

            if($count_length < $max_length){
                $next_index = $i + 1;
                $next_word = $words[$next_index];

                if(isset($next_word)){

                    $temp_length = strlen($next_word) + $count_length;
                    if($temp_length >= $max_length)
                    {
                        $last_word = substr($next_word, 0, $max_length - $count_length);
                        $this->cut_words[$next_index] = $last_word;

                        if($this->hyperlinks && $this->is_a_hyperlink($next_word))
                            $this->cut_words[$next_index] = $this->build_hyperlink($last_word, $next_word);

                        break;
                    }
                }
            }

        }

        if($this->html_spaces)
            return $this->cut_text = str_replace(' ', '&nbsp;',implode(' ',$this->cut_words));
        else
            return $this->cut_text = implode(' ',$this->cut_words);

    }

}



$text = 'Lorem  Ipsum es simplemente el texto de relleno de las imprentas y archivos de texto.
Lorem Ipsum ha sido el texto de relleno estándar de las industrias desde';

$cutter = new TextCutter();
$cutter->text = $text;
$cutter->max_length = 100;
$cutter->html_spaces = false;
var_dump($cutter->init());