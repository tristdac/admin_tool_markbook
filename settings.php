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
 * Links and settings.
 *
 * This file contains links and settings used by tool_lp.
 *
 * @package    tool_markbook
 * @copyright  2019 Tristan daCosta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

// require_once($CFG->dirroot.'/tool/markbook/lib.php');

if ($hassiteconfig) {

	// // Manage competency frameworks page.
	// $temp = new admin_externalpage(
	//     'toolmarkbook',
	//     get_string('markbook', 'tool_markbook'),
	//     new moodle_url('/admin/tool/markbook/markbook.php', array('pagecontextid' => context_system::instance()->id)),
	//     array('moodle/competency:competencymanage')
	// );
	// $ADMIN->add($parentname, $temp);

	$settings = new admin_settingpage('tool_markbook', get_string('pluginname', 'tool_markbook'));
    $ADMIN->add('tools', $settings);
    $contextplugins = core_component::get_plugin_list('markbookcontext');

    // Create a settings page and add an enable setting for each markbook context type.
    // $settings = new admin_settingpage('tool_markbook', get_string('settings'));
    // if ($ADMIN->fulltree) {
    //     foreach ($contextplugins as $contextname => $contextlocation) {
    //         $item = new admin_setting_configcheckbox('markbookcontext_'.$contextname.'/markbookenabled',
    //             new lang_string('markbookenabled', 'markbookcontext_'.$contextname), '', 0);
    //         $settings->add($item);
    //     }
    // }
    // $ADMIN->add('tool_markbook', $settings);

    $settings->add(new admin_setting_configcheckbox(
        'tool_markbook/markbookenable',
        new lang_string('markbookenable', 'tool_markbook'),
        '',
        1
    ));

	$settings->add(new admin_setting_configtext('tool_markbook/dbhost', get_string('dbhost', 'tool_markbook'), get_string('dbhost_desc', 'tool_markbook'), 'localhost'));

    $settings->add(new admin_setting_configtext('tool_markbook/dbuser', get_string('dbuser', 'tool_markbook'), '', ''));

    $settings->add(new admin_setting_configpasswordunmask('tool_markbook/dbpass', get_string('dbpass', 'tool_markbook'), '', ''));

    $settings->add(new admin_setting_configtext('tool_markbook/dbname', get_string('dbname', 'tool_markbook'), '', ''));

    $settings->add(new admin_setting_configtext('tool_markbook/dbtable', get_string('dbtable', 'tool_markbook'), get_string('dbtable_desc', 'tool_markbook'), ''));

    // // Create a new external settings page for each markbook context type data definitions.
    // foreach ($contextplugins as $contextname => $contextlocation) {
    //     $contexthandler = \tool_markbook\context\context_handler::factory($contextname);
        // $ADMIN->add('markbookfolder',
        //     new admin_externalpage('markbook', get_string('markbooktitle', 'markbookcontext_'.$contextname),
        //         new moodle_url('/tool/markbook/index.php', ['contextlevel' => $contexthandler->contextlevel]),
        //             ['moodle/site:config']));

        // // Add context settings to specific context settings pages (if possible).
        // if (get_config('markbookcontext_'.$contextname, 'markbookenabled') == 1) {
        //     $contexthandler->add_settings_to_context_menu($ADMIN);
        // }
    // }

    // $settings = null;
}