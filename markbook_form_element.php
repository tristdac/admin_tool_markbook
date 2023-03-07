<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 *
 * @package   tool_markbook
 * @copyright 2019 Tristan daCosta
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once('./lib.php');


class tool_markbook_assessmentname_form_element extends MoodleQuickForm_autocomplete {

    /**
     * Constructor
     *
     * @param string $elementName Element name
     * @param mixed $elementLabel Label(s) for an element
     * @param array $options Options to control the element's display
     * @param mixed $attributes Either a typical HTML attribute string or an associative array.
     */
    public function __construct($elementname=null, $elementlabel=null, $options=array(), $attributes=null) {

        GLOBAL $DB, $PAGE, $COURSE;

        if ($assessments = get_assessments($COURSE->id)) {
            $cmid = $PAGE->cm->id;
            foreach ($assessments as $option) {
                $sql = 'SELECT assid
                        FROM {tool_markbook}
                        WHERE assid = "'.$option['AssessmentID'].'" AND coursecode = "'
                        .$option['StudentGroupCode'].'" AND unitcode ="'.$option['UnitCode']
                        .'" AND asstype = "3" AND instanceid <> "'.$cmid.'"';

                if (!$DB->record_exists_sql($sql)) {
                    $assnameoptions[$option['AssessmentID']] = $option['AssessmentTitle'].' - '.$option['AssessmentCode'].' ('.$option['UnitCode'].')';
                }
            }
        } else {
            $assnameoptions = '';
        }

        $elementname = 'assessmentid';
        $elementlabel = 'Assessment Name';

        $attributes['multiple'] = 'multiple';
        parent::__construct($elementname, $elementlabel, $assnameoptions, $attributes);
    }
}