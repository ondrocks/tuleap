<?php
/**
 * Copyright (c) Enalean, 2018. All Rights Reserved.
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

namespace Tuleap\Git\PerGroup;

use Project;
use Tuleap\Project\Admin\PerGroup\PermissionPerGroupPanePresenter;

class GitPanePresenter
{
    /**
     * @var PermissionPerGroupPanePresenter
     */
    public $service_presenter;
    /**
     * @var RepositoriesSectionPresenter
     */
    public $repositories_presenter;
    public $url;

    public function __construct(
        PermissionPerGroupPanePresenter $service_presenter,
        RepositoriesSectionPresenter $repositories_presenter,
        Project $project
    ) {
        $this->service_presenter      = $service_presenter;
        $this->repositories_presenter = $repositories_presenter;
        $this->url                    = $this->getGlobalAdminLink($project);
    }

    private function getGlobalAdminLink(Project $project)
    {
        return GIT_BASE_URL . "?" . http_build_query(
            [
                "group_id" => $project->getID(),
                "action"   => "admin-git-admins"
            ]
        );
    }
}