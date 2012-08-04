<?php
/**
 * Latin keyword finder
 *
 * Command to get the headwords of a text
 *
 * @author    Michel Corne <mcorne@yahoo.com>
 * @copyright 2012 Michel Corne
 * @license   http://opensource.org/licenses/MIT MIT License
 */

require_once 'common.php';

/**
 * Converts the keys of an array to lower case
 *
 * The keys are meant to be UTF-8 strings.
 *
 * @param array $array the array
 * @return array       the array with the converted keys
 */
function convert_keys_to_lower($array)
{
    $converted = array();

    foreach($array as $key => $value) {
        $key = mb_strtolower($key, 'utf-8');
        $converted[$key] = $value;
    }

    return $converted;
}

/**
 * Main function to get the headwords
 *
 * @param string $text_title the title of the text to process
 */
function exec_get_headwords($text_title)
{
    echo_command_title('getting headwords');

    $content = read_file(__DIR__ . "/../data/$text_title/generated/words-info.txt");
    $words_info = extract_words_info($content);
    $content = read_file(__DIR__ . "/../data/$text_title/generated/words.txt");
    $words = extract_words($content);
    $headwords = get_headwords($words_info, $words);
    $has_content_changed = write_csv(__DIR__ . "/../data/$text_title/generated/headwords.csv", $headwords);

    echo count($headwords) . ' headwords ';
    echo_has_content_changed($has_content_changed);
}

/**
 * Finds the most frequent headword in the word information section of a Whitaker WORD.OUT formatted file
 *
 * @param array  $word_info the word information section
 * @param string $word      the targeted (inflected) word
 * @throws Exception
 * @return array            the line number, the headword, and the part of speech
 */
function find_headword($word_info, $word)
{
    static $frequencies = array('A', 'B', 'C', 'D', 'E', 'F', 'I', 'M', 'N');
    static $missing_info;
    static $false_positives;

    if (! isset($missing_info)) {
        $missing_info = require __DIR__ . '/whitaker/missing-info.php';
        $missing_info = convert_keys_to_lower($missing_info);
    }

    if (! isset($false_positives)) {
        $false_positives = require __DIR__ . '/whitaker/false-positives.php';
        $false_positives = convert_keys_to_lower($false_positives);
    }

    // scans the lines for each frequency from very frequent (A) to very rare (F) or specific
    foreach($frequencies as $frequency) {
        foreach($word_info as $number => $line) {
            if (preg_match("~^(.+?) +(X|N|PRON|PACK|ADJ|NUM|ADV|V|VPAR|SUPINE|PREP|CONJ|INTERJ|TACKON|PREFIX|SUFFIX) +.+?\[...$frequency.\]~", $line, $match)) {
                // ex. abscondo, abscondere, abscondi, absconditus  V (3rd)   [XXXBO]
                list(, $headword, $pos) = $match;

                if (isset($false_positives[$word]) and $false_positives[$word] == $headword) {
                    // this is a false positive, ignores the word
                    continue;
                }

                return array($number, $headword, $pos);

            } else if (preg_match("~^ \[...$frequency.\] +$~", $line)) {
                // ex. " [XXXAO]  "
                if (! isset($missing_info[$word])) {
                    throw new Exception("found frequency but no headword for $word");
                }

                list($headword, $pos) = $missing_info[$word];
                return array($number, $headword, $pos);
            }
        }
    }

    throw new Exception("cannot find $word");
}

/**
 * Finds the meaning following a given line number in the word information section of a Whitaker WORD.OUT formatted file
 *
 * @param array  $word_info  the word information section
 * @param int    $number     the line number
 * @param string $word       the targeted word (used for error reporting only)
 * @throws Exception
 * @return                   the meaning of the headword
 */
function find_meaning($word_info, $number, $word)
{
    $word_info = array_slice($word_info, $number + 1);

    foreach($word_info as $line) {
        if (preg_match("~^(.+?);~", $line, $match)) {
            return $match[1];
        }
    }

    throw new Exception("cannot find headword meaning for $word");
}

/**
 * Returns the headwords from a Whitaker WORD.OUT formatted file
 *
 * @param array $words_info the content of the WORD.OUT formatted file (the word information sections)
 * @param array $words      the list of (targeted) words
 * @return
 */
function get_headwords($words_info, $words)
{
    static $unknow_words;

    if (! isset($unknow_words)) {
        $unknow_words = require __DIR__ . '/whitaker/unknow-words.php';
        $unknow_words = convert_keys_to_lower($unknow_words);
    }

    $headwords = array();

    foreach($words_info as $index => $word_info) {
        $word_info = explode("\n", $word_info);
        $word = $words[$index];

        if (isset($unknow_words[$word])) {
            list($headword, $pos, $meaning) = $unknow_words[$word];

        } else {
            list($number, $headword, $pos) = find_headword($word_info, $word);
            $meaning = find_meaning($word_info, $number, $word);
        }

        $headwords[] = array(
            'word'     => $word,
            'headword' => $headword,
            'pos'      => $pos,
            'meaning'  => $meaning,
        );
    }

    return $headwords;
}
