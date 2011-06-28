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
 * Peer Review CSS styles
 *
 * @package    contrib
 * @subpackage assignment_progress
 * @copyright  2010 Michael de Raadt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

header("Content-type: text/css");
?>

// Style information for peerreview assignment type

.progressBar {
    margin-bottom: 20px;
}

.progressBox {
	float: left;
	width: 200px;
	height: 40px;
	margin: 10px 5px;
}

.progressNumber {
	font-weight: bold;
	font-size: 250%;
	padding: 0 5px;
	width: 20px;
	text-align: center;
    vertical-align: top;
}

.progressIcon {
	width: 30px;
	text-align: center;
}

.progressTitle {
	font-weight: bold;
	font-size: 120%;
}

.progressMessage {
	color: #000000;
    font-size: 80%;
}

.greyProgressBox .progressMessage {
	color: #999999;
}

.redStatusBox {
	width: 100%;
	border-collapse: collapse;
	border: 3px solid #ff3333;
	background: #ffe9e9 url("images/cross.gif") no-repeat 95% 20%;
	color: #ff3333;
}

.blueProgressBox {
	width: 100%;
	border-collapse: collapse;
	border: 3px solid #3333cc;
	background: #e9e9ff url("images/alert.gif") no-repeat 95% 20%;
	color: #3333cc;
}

.greyProgressBox {
	width: 100%;
	border-collapse: collapse;
	border: 3px solid #999999;
	background: #e9e9e9;
	color: #999999;
}

.greenProgressBox {
	width: 100%;
	border-collapse: collapse;
	border: 3px solid #339933;
	background: #e9ffe9 url("images/tick.gif") no-repeat 95% 20%;
	color: #339933;
}

.errorStatus {
	color:#ff6600;
	background:#ffff00;
	padding:2px;
}

.goodStatus {
	color:#009900;
	background:#ccffcc;
	padding:2px;
}

.evenCriteriaRow {
    background:#f6f6f6;
}

.criteriaCheckboxColumn {
    vertical-align:top;
    width:20px;
}

.criteriaTextColumn {
    vertical-align:top;
}

.criteriaDisplayRow {
    border-top:1px dotted #e9e9e9;
}

.criteriaDisplayColumn {
    padding:0 0 10px 5px;
    text-align: left;
    vertical-align:top;
}

.reviewCommentRow {
    text-align:left;
    vertical-align:bottom;
    padding:5px;
}

.reviewDetailsRow {
    font-size:x-small;
}

.reviewDetailsRow img {
    vertical-align:middle;
    border:none;
}

.reviewDateColumn {
    text-align:right;
}

.commentTextBox {
    font-family: monospace;
    padding:3px;
    margin:0;
    border:none;
    width:99%;
    white-space: pre-wrap;
}

.reviewStatus {
    padding:5px 0;
}

#mod-assignment-type-peerreview-analysis .generalbox, #mod-assignment-type-peerreview-massMark .generalbox  {
	text-align: left;
	margin-left: auto;
	margin-right: auto;
}
