<?php
/**
 * Latin keyword finder
 *
 * Common functions
 *
 * @author    Michel Corne <mcorne@yahoo.com>
 * @copyright 2012 Michel Corne
 * @license   http://opensource.org/licenses/MIT MIT License
 */

define('CONTENT_TO_LOWER'  , 1);
define('INDEX_ROWS'        , 2);

define('DEFAULT_POS'       , 'ADJ,N,V');
define('MAX_KEYWORDS'      , 50);
define('VERSES_PER_KEYWORD', 10);
define('WORD_SEPARATOR'    , '; ');

/**
 * Makes a backup of a file
 *
 * @param string $filename the file name
 * @throws Exception
 */
function backup_file($filename)
{
    $info = pathinfo($filename);
    $backup = sprintf(__DIR__ . '/../backup/%s-%s.%s', $info['filename'], time(), $info['extension']);

    if (! @copy($filename, $backup)) {
        throw new Exception("cannot backup file $backup");
    }
}

/**
 * Echos the command title
 *
 * @param string $command_title the command title
 */
function echo_command_title($command_title)
{
    echo str_pad($command_title . '...', 30);
}

/**
 * Echos a message stating if the content has changed or not
 *
 * @param bool $has_content_changed true if the content has changed, false otherwise
 */
function echo_has_content_changed($has_content_changed)
{
    echo $has_content_changed? '(content has changed)' : '(content has not changed)';
    echo "\n";
}

/**
 * Extracts the words from a line-feed separated list of words
 *
 * @param string $content the line-feed separated list of words
 * @return array          the list of words
 */
function extract_words($content)
{
    return explode("\n", $content);
}

/**
 * Extracts the blocs of information for each word from a Whitaker WORD.OUT formatted file
 *
 * @param string $content the file content
 * @return array          the list of informations
 */
function extract_words_info($content)
{
    $content = trim($content);
    $content = fix_line_endings($content);
    $words_info = preg_split("~[\n*]{2}~", $content);

    return array_map('trim', $words_info);
}

/**
 * Fixes a file content line endings to the Unix line ending
 *
 * @param string  $content the file content
 * @return string          the file content with Unix style line endings
 */
function fix_line_endings($content)
{
    // fixes windows line ending
    $content = str_replace("\r\n", "\n", $content);
    // fixes mac line ending
    $content = str_replace("\r", "\n", $content);

    return $content;
}

/**
 * Returns the column headers from the first row of a CSV file
 *
 * @param array $rows the array of rows
 * @return array      the list of column headers
 */
function get_column_headers($rows)
{
    $first_row = current($rows);
    $column_names = array_keys($first_row);

    return array_combine($column_names, $column_names);
}

/**
 * Indexes an array of rows with one of the keys
 *
 * The key is meant to be a column file header.
 *
 * @param array  $rows          the array of rows, each row being an associative array,
 *                              one of the keys must be set to the column name
 * @param string $column_header the name of the key or column header
 * @throws Exception
 * @return array                the associative array of rows
 */
function index_rows($rows, $column_header = 'word')
{
    $indexed_rows = array();

    foreach($rows as $row) {
        if (! isset($row[$column_header])) {
            throw new Exception("missing column $column_header");
        }
        $index = $row[$column_header];

        if (isset($indexed_rows[$index])) {
            throw new Exception("index already used: $index");
        }
        $indexed_rows[$index] = $row;
    }

    return $indexed_rows;
}

/**
 * Reads a CSV file
 *
 * @param string $filename      the file name
 * @param int    $options       the or'ed options: INDEX_ROWS, CONTENT_TO_LOWER, or 0
 * @param string $column_header the name of the key or column header if the INDEX_ROWS option is present, null otherwise
 * @return                      the array of rows, each row being an associative array containing the cell values
 *                              with the column headers as keys
 */
function read_csv($filename, $options = 0, $column_header = 'word')
{
    $content = read_file($filename);

    if ($options & CONTENT_TO_LOWER) {
        $content = mb_strtolower($content, 'utf-8');
    }

    $content = fix_line_endings($content);
    $lines = explode("\n", $content); // TODO: handle line feeds within a cell

    $first_line = array_shift($lines);
    $column_headers = read_line($first_line);
    $columns_count = count($column_headers);

    $rows = array();
    foreach($lines as $line) {
        $cells = read_line($line);
        $cells = array_pad($cells, $columns_count, null);
        $rows[] = array_combine($column_headers, $cells);
    }

    if ($options & INDEX_ROWS) {
        $rows = index_rows($rows, $column_header);
    }

    return $rows;
}

/**
 * Reads a file
 *
 * @param string $filename the file name
 * @throws Exception
 * @return string          the file content
 */
function read_file($filename)
{
    if (! $content = @file_get_contents($filename)) {
        throw new Exception("cannot read $filename");
    }

    return $content;
}

/**
 * Reads a TAB separated line content
 *
 * @param string $line the line
 * @return array an array of cell values
 */
function read_line($line)
{
    // splits the line by tabs
    $cells = explode("\t", $line);

    foreach($cells as &$cell) {
        // trims the enclosing quotes
        $cell = trim($cell, '" ');
        // unescapes quotes
        $cell = str_replace('""', '"', $cell);
    }

    return $cells;
}

/**
 * Sorts an array of rows
 *
 * @param array  $rows          the array of rows, each row being an associative array,
 *                              one of the keys must be set to the column name
 * @param string $column_header the name of the key or column header to sort by
 * @param int    $option        the or'ed options: SORT_ASC, SORT_DESC, or 0
 * @return array                the sorted array of rows
 */
function sort_rows($rows, $column_header, $option = SORT_ASC)
{
    $column = array();

    foreach($rows as $row) {
        $column[] = $row[$column_header];
    }

    array_multisort($column, $option, $rows);

    return $rows;
}

/**
 * Writes a CSV file
 *
 * @param string $filename the file name
 * @param array  $rows     the array of rows, each row being an associative array
 * @throws Exception
 * @return boolean         true if the file content has changed (and the file was actually written),
 *                         false otherwise
 */
function write_csv($filename, $rows)
{
    if (empty($rows)) {
        throw new Exception("no rows to write for $filename");
    }

    $column_headers = get_column_headers($rows);
    array_unshift($rows, $column_headers);
    $row_defaults = array_fill_keys($column_headers, null);

    $lines = array();
    foreach(array_keys($rows) as $key) {
        $cells = array_merge($row_defaults, $rows[$key]);
        $lines[] = write_line($cells);
    }

    return write_file($filename, $lines);
}

/**
 * Writes a file
 *
 * The previous file content is backed up in the "backup" directory if the file content has changed.
 *
 * @param string $filename the file name
 * @param string $content  the file content
 * @throws Exception
 * @return boolean         true if the file content has changed (and the file was actually written),
 *                         false otherwise
 */
function write_file($filename, $content)
{
    if (is_array($content)) {
        $content = implode("\n", $content);
    }

    $is_file = file_exists($filename);

    if ($is_file and read_file($filename) == $content) {
        $has_content_changed = false;

    } else {
        $has_content_changed = true;

        if ($is_file) {
            backup_file($filename);
        }

        if (! @file_put_contents($filename, $content)) {
            throw new Exception("cannot write $filename");
        }
    }

    return $has_content_changed;
}

/**
 * Writes a TAB separated line content
 *
 * @param array   $cells the row cells
 * @return string        the line
 */
function write_line($cells)
{
    foreach($cells as &$cell) {
        if (! is_numeric($cell)) {
            // escapes quotes
            $cell = str_replace('"', '""', $cell);
           // encloses cells with quotes
            $cell = '"' . $cell . '"';
        }
    }

    return implode("\t", $cells);
}