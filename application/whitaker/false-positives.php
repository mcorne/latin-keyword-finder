<?php
/**
 * Latin keyword finder
 *
 * List of false positives extracted from a Whitaker WORD.OUT formatted file based on their frequencies
 * Ex. "eam" from "eo" has a frequency of "A" like "eam" from "ea" but comes first
 * although it is less frequent
 *
 * @author    Michel Corne <mcorne@yahoo.com>
 * @copyright 2012 Michel Corne
 * @license   http://opensource.org/licenses/MIT MIT License
 */

return array(
    'eam'  => 'eo, ire, ivi(ii), itus',       // is, ea, id  PRON   [XXXAX]  he/she/it/they (by GENDER/NUMBER)
    'eas'  => 'eo, ire, ivi(ii), itus',       // is, ea, id  PRON   [XXXAX]  he/she/it/they (by GENDER/NUMBER)
    'eo'   => 'eo, ire, ivi(ii), itus',       // is, ea, id  PRON   [XXXAX]  he/she/it/they (by GENDER/NUMBER)
    'iam'  => 'eo, ire, ivi(ii), itus',       // jam         ADV    [XXXAO]  now, already, by/even now
    'is'   => 'eo, ire, ivi(ii), itus',       // is, ea, id  PRON   [XXXAX]  he/she/it/they (by GENDER/NUMBER)
    'ita'  => 'eo, ire, ivi(ii), itus',       // ita         ADV    [XXXAX]  thus, so; therefore;
    'mane' => 'maneo, manere, mansi, mansus', // mane        ADV    [XXXAX]  in the morning; early in the morning;
);
