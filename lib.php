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
 * This page contains navigation hooks for learning plans.
 *
 * @package    tool_markbook
 * @copyright  2019 Tristan daCosta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * This function adds necessary enrol plugins UI into the course edit form.
 *
 * @param MoodleQuickForm $mform
 * @param object $data course edit form data
 * @param object $context context of existing course or parent category if course does not exist
 * @return void
 */
function tool_markbook_course_edit_form(MoodleQuickForm $mform, $data, $context) {
    echo 'ballbag';
}

/**
 * Inject the competencies elements into all moodle module settings forms.
 *
 * @param moodleform $formwrapper The moodle quickforms wrapper object.
 * @param MoodleQuickForm $mform The actual form object (required to modify the form).
 */

// function get_course_module () {
//     $cmid = null;
//     if ($cm = $formwrapper->get_coursemodule()) {
//         $cmid = $cm->id;
//     }
// }

function tool_markbook_coursemodule_standard_elements($formwrapper, $mform) {
    global $CFG, $COURSE, $DB, $PAGE;
    if (get_config('tool_markbook', 'markbookenable')) {
    if (!get_config('tool_markbook', 'markbookenable')) {
        return;
    } else if (!has_capability('moodle/course:manageactivities', $formwrapper->get_context())) {
        return;
    }
    
    $cmid = null;
    if ($cm = $formwrapper->get_coursemodule()) {
        $cmid = $cm->id;
    }
    
    // $options = array(
    //     'courseid' => $COURSE->id,
    //     'cmid' => $cmid
    // );
    
    $assessments = get_assessments($COURSE->id);
    // print_object($assessments);
    // ///// WORKING HERE ///////
    // $code = $row['AssessmentCode'];
    // if (!$DB->record_exists_select('tool_markbook', "data = '".$code."' AND fieldid = '2' AND instanceid != '".$cm->id."'")) {

    // }
    // ///////////


    $mform->addElement('header', 'markbooksection', get_string('markbook', 'tool_markbook'));
    $mform->setExpanded('markbooksection');
    $options = array('Choose...','Formative','Milestone','Summative');

    $mform->addElement('select', 'assessmenttype', get_string('assessmenttype', 'tool_markbook'), $options, ['class' => 'field_assessmenttype']);
    $type = $DB->get_record('tool_markbook', array('instanceid'=>$cmid), '*', IGNORE_MULTIPLE);
    if($type) {
      $mform->setDefault('assessmenttype',$type->asstype);
    } 
    //   else {
    //   throw new dml_exception("A record with id $id does not exist.");
    // }
    // $mform->addRule('assessmenttype', 'required', 'required', null, 'server');
    $mform->addHelpButton('assessmenttype', 'assessmenttype', 'tool_markbook');

    $assnameoptions = array();
    MoodleQuickForm::registerElementType('markbook_name',
                                         "$CFG->dirroot/admin/tool/markbook/classes/markbook_form_element.php",
                                         'tool_markbook_assessmentname_form_element');
    
    // print_r($assnameoptions);
    
    $names = $DB->get_records('tool_markbook', array('instanceid'=>$cmid));
    $assnames = array();
    foreach($names as $name) {
        $assnames[] = $name->assid;
    }
    if($assnames) {
      $mform->setDefault('assessmentid',$assnames);
    } 
    $mform->addElement('markbook_name', 'assessmentid', get_string('markbook', 'tool_markbook'), $assnameoptions, ['class' => 'field_assessmentname']);
    $mform->addHelpButton('assessmentid', 'assessmentname', 'tool_markbook');
    
    // foreach ($assessments as $assessment) {
    //     $dates[$assessment['AssessmentCode']]['setY'] = $assessment['setY'];
    //     $dates[$assessment['AssessmentCode']]['setM'] = $assessment['DateExpected'];
    // }
    // date_default_timezone_set('UTC');

    // print_object($assessments);
    $missing_assessments = get_used_assessments_for_lang($COURSE->id);
    // echo($missing_assessments);


    $mform->addElement('static', 'missing', '',
    $missing_assessments,'missing');
    $mform->addHelpButton('missing', 'missing', 'tool_markbook');
    $PAGE->requires->js_call_amd('tool_markbook/markbook', 'init', array($assessments));
   } 
}


/**
* Hook function called when a module form is saved and insert/update promonitor in the database for the module
 *
 * @param stdClass $data Data from the form submission.
 * @param stdClass $course The course.
*/
function tool_markbook_coursemodule_edit_post_actions($data, $course) {
    global $DB;
if (get_config('tool_markbook', 'markbookenable')) {
    if (!empty($data->assessmenttype)) {
        $form_ass_meta_raw = get_assessments($course->id);
        $form_ass_meta = reset($form_ass_meta_raw);
        $form_ass_meta_obj =  (object) $form_ass_meta;
        
        // $assessmenttype = new stdClass();
        // $assessmenttype->instanceid = $data->coursemodule;
        // $assessmenttype->fieldid = 1;
        // $assessmenttype->data = $data->assessmenttype;
        $DB->delete_records('tool_markbook', array('instanceid' => $data->coursemodule));
        // $DB->insert_record('tool_markbook', $assessmenttype);
        $assessment = new stdClass();
        $assessment->instanceid = $data->coursemodule;
        $assessment->asstype = $data->assessmenttype;
        $assessment->timemodified = time();

        if ($data->assessmenttype === '1') {
            
            // $assessment->coursecode = $form_ass_meta_obj->StudentGroupCode;
            $assessment->coursecode = $course->shortname;
            $assessment->unitcode = $ass_meta_obj->UnitCode;
            $assessment->assid = '0';
            $assessment->data = 'formative';
            $DB->insert_record('tool_markbook', $assessment);
        }
        foreach ($data->assessmentid as $assid) {
            $ass_meta = reset(get_assessment_metadata($assid));
            $ass_meta_obj =  (object) $ass_meta;
            $assessment->instanceid = $data->coursemodule;
            $assessment->coursecode = $form_ass_meta_obj->StudentGroupCode;
            $assessment->unitcode = $ass_meta_obj->UnitCode;
            $assessment->assid = $assid;
            $assessment->data = $ass_meta_obj->AssessmentCode;
            $DB->insert_record('tool_markbook', $assessment);
        }
    }
    return $data;
}
}

function connect_to_db() {
    if (get_config('tool_markbook', 'markbookenable')) {
    // GLOBAL $PAGE, $DB, $COURSE;
    $DB_SERVER = get_config('tool_markbook', 'dbhost'); 
    $DB_USER = get_config('tool_markbook', 'dbuser'); // mysql user
    $DB_PASSWORD = get_config('tool_markbook', 'dbpass'); // mysql password
    $DB_NAME = get_config('tool_markbook', 'dbname'); // mysql database
    $DB_TABLE = get_config('tool_markbook', 'dbtable');
    $link = mysqli_connect($DB_SERVER,$DB_USER,$DB_PASSWORD,$DB_NAME);
    if (mysqli_connect_error()) {
                    echo 'Oops... we&#39;re having trouble connecting to Markbook at the moment. Please try again later. If the problem persists, please contact your system administrator. <span style="opacity:0.5;">' . mysqli_connect_errno().'</span>';
    } else {
    return $link;
    }
}
}

function get_assessments($courseid) {
    if (get_config('tool_markbook', 'markbookenable')) {
    GLOBAL $PAGE, $DB;
    $DB_TABLE = get_config('tool_markbook', 'dbtable');
    $link = connect_to_db();
    
    if (mysqli_error($link)) {
        echo 'Sorry... we&#39;re having trouble getting assessment data at the moment. Please try again later. If the problem persists, please contact your system administrator. <span style="opacity:0.5;">' . mysqli_errno($link).'</span>';
    }
    if ($link)
        $shortname = $DB->get_field('course', 'shortname', array('id' => $courseid));
        if (strpos($shortname, '/') !== false) {
            $shortname = substr($shortname, 0, strrpos($shortname, '/'));   
        //     $ass_lookup = 'SELECT * FROM '.$DB_TABLE.' WHERE UnitCode = "'.$shortname.'" ORDER BY UnitCode ASC';
        }
    // if (strpos($shortname, '-') !== false) {
    $ass_lookup = 'SELECT * FROM '.$DB_TABLE.' WHERE StudentGroupCode = "'.$shortname.'" OR UnitCode = "'.$shortname.'" ORDER BY UnitCode ASC';
        // $group = $shortname;
    // }
    date_default_timezone_set('UTC');

    $ass_raw = mysqli_query($link, $ass_lookup);
    $assessments = array();
    if (!empty($ass_raw)) {
        while($row = mysqli_fetch_array($ass_raw)){
            // ADD DUPLICATE REMOVAL HERE
            // $assessments[$row['AssessmentCode']] = $row['AssessmentName'].' ('.strstr($row['UnitName'], '/', true).')';
            // $assessments[$row['AssessmentCode']] = $row;
            $masstype = 'Quiz'; // Add DB activity type lookup here - based on $cm>id (course module id)
              
            $assessments[$row['AssessmentID']] = array(
                'AssessmentCode' => $row['AssessmentCode'],
                'AssessmentID' => $row['AssessmentID'],
                'UnitCode' => $row['UnitCode'],
                'StudentGroupCode' => $row['StudentGroupCode'],
                'AssessmentCode' => $row['AssessmentCode'],
                'AssessmentTitle' => $row['AssessmentTitle'],
                'AssessmentName' => $row['AssessmentName'],
                'AssessmentType' => $row['AssessmentType'],
                'MoodleAssessmentType' => $masstype,
                'Teacher' => $row['Teacher'],
                'DateSet' => $row['DateSet'],
                // 'DateExpected' => $row['DateExpected'],
                'setY' => date('Y', $row['DateSet']),
                'setM' => date('m', $row['DateSet']),
                'setD' => date('d', $row['DateSet']),
                'setH' => date('h', $row['DateSet']),
                'setI' => date('i', $row['DateSet']),
                'expY' => date('Y', $row['DateExpected']),
                'expM' => date('m', $row['DateExpected']),
                'expD' => date('d', $row['DateExpected']),
                'expH' => date('h', $row['DateExpected']),
                'expI' => date('i', $row['DateExpected'])
            );
        }
    }
    usort($assessments, function($a,$b){return $a['DateSet']-$b['DateSet'];});
    return $assessments;
}
}

function get_assessment_metadata($assid) {
    if (get_config('tool_markbook', 'markbookenable')) {
    global $DB;
    $DB_TABLE = get_config('tool_markbook', 'dbtable');
    
    $link = connect_to_db();
    
    if (mysqli_error($link)) {
        echo 'Sorry... we&#39;re having trouble getting assessment data at the moment. Please try again later. If the problem persists, please contact your system administrator. <span style="opacity:0.5;">' . mysqli_errno($link).'</span>';
    }
    if ($link) 
    $meta_lookup = 'SELECT * FROM '.$DB_TABLE.' WHERE AssessmentID = "'.$assid.'"';
    $meta_raw = mysqli_query($link, $meta_lookup);
    // echo($group_lookup);
    $meta = array();
    while($row = mysqli_fetch_array($meta_raw)){
        // ADD DUPLICATE REMOVAL HERE
        // $assessments[$row['AssessmentCode']] = $row['AssessmentName'].' ('.strstr($row['UnitName'], '/', true).')';
        // $assessments[$row['AssessmentCode']] = $row;
        
          
        $meta[$row['AssessmentID']] = array(
            'AssessmentCode' => $row['AssessmentCode'],
            'AssessmentID' => $row['AssessmentID'],
            'UnitCode' => $row['UnitCode'],
            'StudentGroupCode' => $row['StudentGroupCode'],
            'AssessmentCode' => $row['AssessmentCode'],
            'AssessmentTitle' => $row['AssessmentTitle'],
            'AssessmentName' => $row['AssessmentName'],
            'AssessmentType' => $row['AssessmentType'],
            'Teacher' => $row['Teacher'],
            'DateSet' => $row['DateSet'],
            // 'DateExpected' => $row['DateExpected'],
            'setY' => date('Y', $row['DateSet']),
            'setM' => date('m', $row['DateSet']),
            'setD' => date('d', $row['DateSet']),
            'setH' => date('h', $row['DateSet']),
            'setI' => date('i', $row['DateSet']),
            'expY' => date('Y', $row['DateExpected']),
            'expM' => date('m', $row['DateExpected']),
            'expD' => date('d', $row['DateExpected']),
            'expH' => date('h', $row['DateExpected']),
            'expI' => date('i', $row['DateExpected'])
        );
    }

    return $meta;
}
}

function get_used_summative_assessments($coursecode) {
    if (get_config('tool_markbook', 'markbookenable')) {
    global $DB;
    // $cmid = $PAGE->cm->id;


    // $cm_assessments = get_assessments();
    // $newList = array();
    // foreach($cm_assessments as $cm_ass) {
    //     $newList[$cm_ass['AssessmentID']] = $cm_ass['AssessmentID'];
    // }
    // foreach($newList as $itemID => $itemName) {
    //     echo "Item ID: $itemID - Item Name: $itemName<br>";
    // }
    // echo'course module assessments (newlist): ';
    // print_object($newList);
    $used_assessments = $DB->get_fieldset_select('tool_markbook', 'assid', 'coursecode = ? AND asstype = 3', array($coursecode), IGNORE_MISSING);
    // echo'used assessments: ';
    // print_object($used_assessments);
    // $DB->get_field('tool_markbook', "data = '".$assnameoption['AssessmentCode']."' AND fieldid = '2' AND instanceid != '".$cmid."'")) {
    return $used_assessments;
}
}

function get_used_assessments($courseid) {
    if (get_config('tool_markbook', 'markbookenable')) {
    global $DB, $PAGE;
    // require_once($CFG->libdir . '/accesslib.php');
    $assessments = get_assessments($courseid);
    $coursecodes = (object) $assessments;
    $coursecodes = reset($coursecodes);
    // print_object($coursecodes);
    $coursecode = $coursecodes['StudentGroupCode'];
    // print_object($coursecode);
    
    // $html .= $coursecode;
    $sql = "SELECT tm.*, cm.module, cm.instance, m.name AS modType,
              CASE
                WHEN m.name = 'assign'  THEN (SELECT name FROM {assign} WHERE id = cm.instance)
                WHEN m.name = 'assignment'  THEN (SELECT name FROM {assignment} WHERE id = cm.instance)
                WHEN m.name = 'choice'  THEN (SELECT name FROM {choice} WHERE id = cm.instance)
                WHEN m.name = 'data'  THEN (SELECT name FROM {data} WHERE id = cm.instance)
                WHEN m.name = 'feedback'  THEN (SELECT name FROM {feedback} WHERE id = cm.instance)
                WHEN m.name = 'forum' THEN (SELECT name FROM {forum} WHERE id = cm.instance)
                WHEN m.name = 'glossary' THEN (SELECT name FROM {glossary} WHERE id = cm.instance)
                WHEN m.name = 'h5pactivity' THEN (SELECT name FROM {h5pactivity} WHERE id = cm.instance)
                WHEN m.name = 'lesson'  THEN (SELECT name FROM {lesson} WHERE id = cm.instance)
                WHEN m.name = 'lti'  THEN (SELECT name FROM {lti}  WHERE id = cm.instance)
                WHEN m.name = 'quiz'  THEN (SELECT name FROM {quiz} WHERE id = cm.instance)
                WHEN m.name = 'scorm'  THEN (SELECT name FROM {scorm} WHERE id = cm.instance)
                WHEN m.name = 'survey'  THEN (SELECT name FROM {survey} WHERE id = cm.instance)
                WHEN m.name = 'wiki' THEN (SELECT name FROM {wiki}  WHERE id = cm.instance)
                WHEN m.name = 'workshop' THEN (SELECT name FROM {workshop}  WHERE id = cm.instance)
               ELSE 'Other activity'
            END AS Activityname
              FROM {tool_markbook} tm
              JOIN {course_modules} cm ON cm.id = tm.instanceid
              JOIN {modules} m ON m.id = cm.module
             WHERE tm.data != 'formative' AND tm.coursecode=?";
    $used_assessments = $DB->get_records_sql($sql, array($coursecode));
    // print_object($used_assessments);
    return $used_assessments;
}
}

function get_used_assessments_for_lang($courseid) {
    if (get_config('tool_markbook', 'markbookenable')) {
    GLOBAL $PAGE, $DB, $CFG;
    $used_assessments = get_used_assessments($courseid);
    $html = get_string('usedass', 'tool_markbook');
    $html .= '<ul id="asslist" class="list-group">';

    // echo ($PAGE->cm->id);
    $ass_count = 0;
    foreach ($used_assessments as $ua) {
        if ($ua->instanceid !== $PAGE->cm->id) {
            $record = $DB->get_record('course_modules', array('id' => $ua->instanceid));
            $activity_module = $DB->get_record('modules',array('id' =>$record->module));
            $mod = $DB->get_record($activity_module->name,array('id' =>$record->instance));
            // $title = $DB->get_record();
            if ($ua->asstype === '2') {
                $html .= '<li id="'.$ua->assid.'" class="list-group-item milestone-li" asscode="'.$ua->data.'">';
                $html .= '<i id="milestone-icon" class="fa fa-star-half-o fa-2x" style="color:#8cbb01;"></i> ';
            } elseif ($ua->asstype === '3' ) {
                $html .= '<li id="'.$ua->assid.'" class="list-group-item summative-li" asscode="'.$ua->data.'">';
                $html .= '<i id="summative-icon" class="fa fa-star fa-2x" style="color:#ffd700;"></i> ';
            }
            $html .= '<a href="'.$CFG->wwwroot.'/course/modedit.php?update='.$ua->instanceid.'#id_markbooksection" target="_blank"><span role="listitem" class="used tag badge">'.$ua->data.'</span> - '.$mod->name.' ('.$activity_module->name.')</a></li>';
            $ass_count++;
        }
    }
    $html .= '</ul>';
    if ($ass_count < 1) {
        $html = get_string('noass', 'tool_markbook');
    }
    return $html;
}
}

/**
 * Adds an markbook assessment link to the top of the page.
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course to object for the tool
 * @param context $context The context of the course
 * @return void|null return null if we don't want to display the node.
 */
function tool_markbook_extend_navigation_course($navigation, $course, $context) {
    if (get_config('tool_markbook', 'markbookenable')) {
    global $PAGE, $OUTPUT;
    if ($PAGE->pagelayout == 'course') {
        $assessments = get_assessments($course->id);
        $PAGE->requires->js_call_amd('tool_markbook/schedule', 'init', array($course, $assessments));
        
    }
}
}


