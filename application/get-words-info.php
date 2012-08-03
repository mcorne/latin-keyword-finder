<?php
/**
 * Latin keyword finder
 *
 * Command to get the Whitaker WORD.OUT file (the words information) for a list of words
 *
 * @author    Michel Corne <mcorne@yahoo.com>
 * @copyright 2012 Michel Corne
 * @license   http://opensource.org/licenses/MIT MIT License
 */

require_once 'common.php';

/**
 * Main function to get the Whitaker WORD.OUT file (the words information)
 *
 * @param string $text_title the title of the text to process
 */
 function exec_get_words_info($text_title)
{
    echo "getting words info...\n";

    if (! chdir(__DIR__ . '/../whitaker')) {
        throw new Exception('cannot change directory');
    }

    $words_filename = __DIR__ . "/../data/$text_title/generated/words.txt";
    $content = read_file($words_filename);
    $words = extract_words($content);
    $words_count = count($words);

    shell_exec("words $words_filename");

    $content = read_file('WORD.OUT');
    $words_info = extract_words_info($content);
    $words_info_count = count($words_info);

    if ($words_info_count != $words_count) {
        throw new Exception("words info count ($words_info_count) different from words count ($words_count)");
    }

    $has_content_changed = move_words_info(__DIR__ . "/../data/$text_title/generated/words-info.txt");

    echo "$words_info_count words info ";
    echo $has_content_changed? '(content has changed)' : '(content has not changed)';
}

/**
 * Moves the WORD.OUT file
 *
 * For technical reasons the WORD.OUT file can only be generated with this (fixed) name in the Whitaker directory.
 *
 * @param string $words_info_filename the name of the file to move the WORD.OUT file to
 * @throws Exception
 * @return boolean
 */
function move_words_info($words_info_filename)
{
    $word_out_filename ='WORD.OUT';

    if (file_exists($words_info_filename) and read_file($words_info_filename) == read_file($word_out_filename)) {
        if (! @unlink($word_out_filename)) {
            throw new Exception("cannot remove file $word_out_filename");
        }

        $has_content_changed = false;

    } else {
        if (! @rename($word_out_filename, $words_info_filename)) {
            throw new Exception("cannot move file $word_out_filename");
        }

        $has_content_changed = true;
    }

    return $has_content_changed;
}

// runs the main function if this is the file of the command being run
exec_if_command(__FILE__);
