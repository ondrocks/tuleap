<?php
/**
 * Copyright Enalean (c) 2013. All rights reserved.
 *
 * Tuleap and Enalean names and logos are registrated trademarks owned by
 * Enalean SAS. All other trademarks or names are properties of their respective
 * owners.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

class AgileDashboard_BacklogItemPresenter implements
    AgileDashboard_Milestone_Backlog_IBacklogItem,
    AgileDashboard_Milestone_Backlog_BacklogRowPresenter
{
    /** @var Int */
    private $id;

    /** @var String */
    private $title;

    /** @var String */
    private $type;

    /** @var String */
    private $url;

    /** @var Int */
    private $initial_effort;

    /** @var String */
    private $redirect_to_self;

    /** @var String */
    private $status;

    /** @var String */
    private $color;

    /** @var Tracker_Artifact */
    private $artifact;

    /** @var Tracker_Artifact */
    private $parent;

    public function __construct(Tracker_Artifact $artifact, $redirect_to_self) {
        $this->id               = $artifact->getId();
        $this->title            = $artifact->getTitle();
        $this->url              = $artifact->getUri();
        $this->redirect_to_self = $redirect_to_self;
        $this->artifact         = $artifact;
        $this->type             = $this->artifact->getTracker()->getName();
        $this->color            = $this->artifact->getTracker()->getColor();
    }

    public function setParent(Tracker_Artifact $parent) {
        $this->parent = $parent;
    }

    /**
     * @return Tracker_Artifact
     */
    public function getParent() {
        return $this->parent;
    }

    public function setInitialEffort($value) {
        $this->initial_effort = $value;
    }

    public function getInitialEffort() {
        return $this->initial_effort;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function id() {
        return $this->id;
    }

    public function title() {
        return $this->title;
    }

    public function type() {
        return $this->type;
    }

    public function url() {
        return $this->getUrlWithRedirect($this->url);
    }

    public function points() {
        return $this->initial_effort;
    }

    public function parent_title() {
        if ($this->parent) {
            return $this->parent->getTitle();
        }
    }

    public function parent_url() {
        if ($this->parent) {
            return $this->getUrlWithRedirect($this->parent->getUri());
        }
    }

    public function parent_id() {
        if ($this->parent) {
            return $this->parent->getId();
        }
    }

    public function status() {
        return $this->status;
    }

    private function getUrlWithRedirect($url) {
        if ($this->redirect_to_self) {
            return $url.'&'.$this->redirect_to_self;
        }
        return $url;
    }

    /**
     * @return Tracker_Artifact
     */
    public function getArtifact() {
        return $this->artifact;
    }

    public function color() {
        return $this->color;
    }

    public function hasChildren() {
        return $this->artifact->hasChildren();
    }
}
