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
 * @package tool_markbook
 * @author Tristan daCosta
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2019
 */

namespace tool_markbook;

defined('MOODLE_INTERNAL') || die();

/**
 * Tool Markbook event handler.
 */
class observer {

    /**
     * Triggered via any defined delete event.
     * - Dispatches markbook type specific event, if it exists.
     * - Currently only monitors "[context]_deleted" events.
     *
     * @param \core\event\* $event
     * @return bool true on success
     */
    public static function all_events($event) {
        $localobserver = substr(strrchr($event->eventname, '\\'), 1);
        if (method_exists('tool_markbook\observer', $localobserver)) {
            return self::$localobserver($event);
        } else {
            return true;
        }
    }

    /**
     * Triggered via course_deleted event.
     * - Removes course markbook
     *
     * @param \core\event\course_deleted $event
     * @return bool true on success
     */
    public static function course_deleted(\core\event\course_deleted $event) {
        return self::delete_markbook(CONTEXT_COURSE, $event->objectid);
    }

    /**
     * Triggered via user_deleted event.
     * - Removes user markbook
     *
     * @param \core\event\user_deleted $event
     * @return bool true on success
     */
    public static function user_deleted(\core\event\user_deleted $event) {
        return self::delete_markbook(CONTEXT_USER, $event->objectid);
    }

    /**
     * Triggered via module_deleted event.
     * - Removes module markbook
     *
     * @param \core\event\course_module_deleted $event
     * @return bool true on success
     */
    public static function course_module_deleted(\core\event\course_module_deleted $event) {
        return self::delete_markbook(CONTEXT_MODULE, $event->objectid);
    }

    /**
     * Delete promonitor for appropriate contextlevel fields.
     * - Removes user markbook
     *
     * @param \core\event\user_deleted $event
     * @return bool true on success
     */
    protected static function delete_markbook($contextlevel, $instanceid) {
        global $DB;

        if (!empty($fields = $DB->get_records_select('tool_markbook', 'contextlevel = ?', [$contextlevel], '', 'id'))) {
            $fieldids = array_keys($fields);
            list($sqlin, $params) = $DB->get_in_or_equal($fieldids);
            $params[] = $instanceid;
            $DB->delete_records_select('tool_markbook', 'fieldid '.$sqlin.' AND instanceid = ?', $params);
        }
        return true;
    }
}
