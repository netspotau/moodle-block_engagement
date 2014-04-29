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
 * Analytics Block
 *
 * @package    blocks_engagement
 * @copyright  2012 NetSpot Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_engagement extends block_base {

    /**
     * Set the initial properties for the block
     */
    public function init() {
        global $CFG;
        $this->blockname = get_class($this);
        $this->title = get_string('pluginname', $this->blockname);
    }

    /**
     * All multiple instances of this block
     * @return bool Returns true
     */
    public function instance_allow_multiple() {
        return false;
    }

    /**
     * Set the applicable formats for this block to all
     * @return array
     */
    public function applicable_formats() {
        return array('course' => true);
    }

    /**
     * Allow the user to configure a block instance
     * @return bool Returns true
     */
    public function instance_allow_config() {
        return true;
    }

    public function  instance_can_be_hidden() {
        return true;
    }

    public function instance_can_be_docked() {
        return (parent::instance_can_be_docked() && (empty($this->config->enabledock) || $this->config->enabledock=='yes'));
    }

    /**
     * Gets the content for this block by grabbing it from $this->page
     */
    public function get_content() {
        global $CFG, $DB, $COURSE;

        if ($this->content !== null) {
            return $this->content;
        }

        if (!has_capability('report/engagement:view', $this->page->context)) {
            $this->content = '';
            return $this->content;
        }

        require_once($CFG->dirroot . '/report/engagement/lib.php');

        $risks = report_engagement_get_course_summary($COURSE->id);
        $users = $DB->get_records_list('user', 'id', array_keys($risks), '', 'id, firstname, lastname');

        // Grab the items to display.
        $this->content = new stdClass();
        $renderer = $this->page->get_renderer('block_engagement');
        $this->content->text = $renderer->user_risk_list($risks, $users);
        return $this->content;
    }
}
