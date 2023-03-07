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
 * Strings for component 'tool_lp', language 'en'
 *
 * @package    tool_markbook
 * @copyright  2019 Tristan daCosta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


$string['pluginname'] = 'ProMonitor&trade; Markbook';
$string['markbook'] = 'ProMonitor&trade; Markbook';
$string['assessmenttype'] = 'Assessment Type';
$string['assessmentname'] = 'Assessment Name';
$string['assessmenttype_help'] = '<strong>Formative assessments</strong> are in-process evaluations of student learning that are typically administered multiple times during a unit, course, or academic program. Formative assessments are usually not scored or graded, and they may take a variety of forms, from more formal quizzes and assignments to informal questioning techniques and in-class discussions with students.<br/>
<strong>Milestone assessments</strong> are blah blah blah blah.<br/>
<strong>Summative assessments</strong> are used to evaluate student learning at the conclusion of a specific instructional period—typically at the end of a unit, course, semester, program, or school year. Summative assessments are typically scored and graded tests, assignments, or projects that are used to determine whether students have learned what they were expected to learn during the defined instructional period.';
$string['assessmentname_help'] = 'Assessment Name help to come';
$string['dbtype'] = 'Database driver';
$string['dbhost'] = 'Database host';
$string['dbname'] = 'Database name';
$string['dbuser'] = 'Database user';
$string['dbpass'] = 'Database password';
$string['dbhost_desc'] = 'Type database server IP address or host name. Use a system DSN name if using ODBC.';
$string['dbtable'] = 'Remote table';
$string['dbtable_desc'] = 'Remote table name in which assessment data is stored.';
$string['markbookenable'] = 'Enable Markbook';
$string['missing'] = 'Missing Assessments';
$string['missing_help'] = '<p><i class="fa fa-star-half-o fa-2x" style="color:#8cbb01;"></i> Milestone Assessments can be used over multiple graded activities, with the sum of the grades being passed to Markbook</p><p><i class="fa fa-star fa-2x" style="color:#ffd700;"></i> Summative Assessments can only be used on ONE graded activity within Moodle</p>';
$string['noass'] = 'No selected assessments are currently being used summatively elsewhere';
$string['usedass'] = '<p>Someone (possibly you) has already assigned summative and/or Milestone assessments to this group. Summative assessments can only be linked to ONE Moodle assignment or activity and, as such, are not available to use for this activity.</p><strong>The following assessments are already in use:</strong>';