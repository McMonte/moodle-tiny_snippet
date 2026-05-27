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
 *
 * @package   tiny_snippet
 * @copyright 2023 Justin Hunt {@link http://www.poodll.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tiny_snippet;

defined('MOODLE_INTERNAL') || die;

class constants {

    const M_COMPONENT = 'tiny_snippet';

    /**
     * Hardcoded mapping of group number → display name for the snippet selector.
     * The group number is the first segment of a snippet's snippetversion_X value
     * (e.g. "2.5.0" → group 2). Edit this array to add or rename groups.
     */
    const GROUP_NAMES = [
        1 => 'Headings',
        2 => 'Layout',
        3 => 'Formatting',
    ];

    /** Label used when a snippet's group number is not present in GROUP_NAMES. */
    const UNGROUPED_NAME = 'Other';

}