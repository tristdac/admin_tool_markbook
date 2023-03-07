<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin version and other meta-data are defined here.
 *
 * @package     tool_markbook
 * @copyright   2021 Tristan daCosta <tristan.dacosta@edinburghcollege.ac.uk>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
  */

require('../../../config.php');
require_once('lib.php');
defined('MOODLE_INTERNAL') || die();
require_login();
global $PAGE;

$courseid = required_param('cid', PARAM_INT);
if (empty($courseid)) {
    $ref = $_SERVER['HTTP_REFERER'];
    if (!empty($ref)) {
        parse_str(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY), $params);
        $courseid = $params['id'];
    }
}

// $course = $DB->get_record('course', array('id'=>$courseid));
$course = get_course($courseid);
$assessments = get_assessments($courseid);
$used_assessments = json_decode(json_encode(get_used_assessments($courseid)), true);
// print_object($used_assessments);

$strname = get_string('pluginname', 'tool_markbook').' Assessment Schedule - '.$course->fullname;
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/admin/tool/markbook/index.php');
$PAGE->set_pagelayout('incourse');
$PAGE->navbar->add($strname);
$PAGE->set_title($strname);
$PAGE->set_heading($strname);

?>

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/gantt.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

<?php
// Build the page output.
echo $OUTPUT->header();



if (!empty($courseid)) {
        $courseSDate = date("Y,m,d", strtotime("-1 month", $course->startdate));
        $courseEDate = date("Y,m,d", strtotime("-1 month", $course->enddate)); 
    ?>
    <a class="btn btn-secondary" id="backtocourse" href="/course/view.php?id=<?php echo $courseid ?>"><i class="fa fa-arrow-left"></i> Back to <?php echo $course->fullname ?></a></br>

    <figure class="highcharts-figure">
        <div id="ass_container"></div>
        <p>
        <muted><small>* Assessment dates shown are for xxx purposes only, and are subject to change.</small></muted>
        </p>
        <!-- <ul>
        <li><strike>Course Name - $course[fullname]</strike></li>
        <li><strike>Course Start date - $course[startdate</strike></li>
        <li><strike>Course End date - $course[enddate]</strike></li>
        <li>Assessment name (Moodle) - if exists -? retreive and link course_modules to new markbook table</li>
        <li>Assessment link (Moodle) - ? retreive and link course_modules to new markbook table</li>
        <li>Assessment type (Moodle) - possiblity of adding icon (journal/assignment/quiz/etc) along with link
        <li><strike>Assessment name (Markbook) - $assessments[AssessmentTitle]</strike></li>
        <li><strike>Assessment type - $assessments</strike></li>
        <li><strike>Assessment date - $assessments</strike></li>
        </ul> -->
    </figure>


    <?php



    // print_object($assessments);
    // print_object($course);
    // $assnames = array();
    // foreach ($assessments as $assessment) {
    //     $assnames[] .= $assessment['AssessmentTitle'];
    // }

    // $assnames_str = implode(',', $assnames);

    $asscats = array();
    foreach ($assessments as $assessment) {
        $asscats[] .= $assessment['AssessmentType'];
    }

    // $asscats_str = implode(',', $asscats);
    

    

    ?>
    <script>
      $chart = new Highcharts.ganttChart('ass_container', {

        chart: {
          type: 'gantt',
          renderTo: 'ass_container'
        },
        title: {
          text: 'Scheduled Assessments'
        },
        accessibility: {
          point: {
            descriptionFormatter: function(point) {
              var ix = point.index,
                category = point.yCategory,
                from = new Date(point.x),
                to = new Date(point.x2);
              from.setMonth(from.getMonth() - 1);
              to.setMonth(to.getMonth() - 1);
              return ix + '. ' + category + ', ' + from.toDateString() +
                ' to ' + to.toDateString() + '.';
              from.setMonth(from.getMonth() - 1);
              to.setMonth(to.getMonth() - 1);
            }
          }
        },
        tooltip: {
          stickOnContact: true,
          xDateFormat: '%Y-%m-%d',
          useHTML: true,
          snap: 50,
          style: {
            padding: 25,
          },
          formatter: function() {
            // var startDate = new Date(this.x).toLocaleDateString("en-UK");
            var startDate = Highcharts.dateFormat('%e %B %Y', this.x);
            // var dueDate = new Date(this.x2).toLocaleDateString("en-UK");
            var dueDate = Highcharts.dateFormat('%e %B %Y', this.x2);
            return '<p><h4>' + this.point.name + '</h4>Start: ' + startDate + '<br/>End: ' + dueDate + '</p></br>' + this.series.name + '</br>' + this.point.custom;
          }
        },
        xAxis: {
          currentDateIndicator: {
            color: 'red',
            position: 'bottom',
            label: {
                format: '%e %B %Y<br/><strong>Today</strong>'
            }
          },
          type: 'datetime',
          pointIntervalUnit: 'month',
          lineWidth: 0,
          min: Date.UTC(<?php echo $courseSDate ?>),
          max: Date.UTC(<?php echo $courseEDate ?>),
          startOnTick: false,
          endOnTick: false,
          plotHeight: 20,
        },
        plotOptions: {
          series: {
            pointIntervalUnit: 'month',
            minPointLength: 20,
          }
        },
        credits: {
            enabled: false
        },
        // legend: {
        //      enabled: true
        //  },
        series: [
            <?php foreach ($asscats as $asscat) { ?>
                {
                name: <?php echo '"'.$asscat.'"' ?>,
                data: [
                    <?php foreach ($assessments as $assessment) { 
                        if ($assessment['AssessmentType'] === $asscat) {
                            foreach ($used_assessments as $used_assessment) {
                                if ($used_assessment['assid'] === $assessment['AssessmentID']) {
                                    $tooltip_footer = 'Moodle '.ucwords($used_assessment['modtype']).': '.$used_assessment['activityname'];
                                } else {
                                    $tooltip_footer = '';
                                }
                            } 
                            ?>

                            {
                            name: <?php echo '"'.$assessment['AssessmentTitle'].'"' ?>,
                            start: Date.UTC(<?php echo ($assessment['setY'].','.($assessment['setM'] - 1).','.$assessment['setD']) ?>),
                            end: Date.UTC(<?php echo ($assessment['expY'].','.($assessment['expM'] - 1).','.$assessment['expD']) ?>),
                            custom: <?php echo '"'.$tooltip_footer.'"' ?>
                            },
                        <?php } ?>
                    <?php } ?>
                ]},
            <?php } ?>
        ]
      });

    </script>
    <?php
} else {

    echo '<p class="alert alert-danger">Unknown Course</p>';
}

echo $OUTPUT->footer();

