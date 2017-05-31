<?php
/**
 * Copyright (c) Enalean, 2017. All rights reserved
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/
 */

namespace Tuleap\Dashboard\Widget;

class DashboardWidgetDeletor
{
    /**
     * @var DashboardWidgetDao
     */
    private $dao;

    public function __construct(DashboardWidgetDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param DashboardWidgetColumn $column
     */
    public function deleteEmptyColumn(DashboardWidgetColumn $column)
    {
        if ($this->dao->searchAllWidgetByColumnId($column->getId())->count() <= 0) {
            $this->dao->removeColumn($column->getId());
            $this->dao->reorderColumns($column->getLineId());
        }

        if ($this->dao->searchAllColumnsByLineIdOrderedByRank($column->getLineId())->count() <= 0) {
            $this->dao->removeLine($column->getLineId());
            $this->dao->reorderLines($column->getLineId());
        }
    }

    /**
     * @param DashboardWidget $widget_to_update
     * @param DashboardWidgetColumn $column
     * @return array
     */
    public function removeWidgetInWidgetsListColumn(DashboardWidget $widget_to_update, DashboardWidgetColumn $column)
    {
        $widgets = array();
        foreach ($column->getWidgets() as $widget) {
            if ($widget->getId() !== $widget_to_update->getId()) {
                $widgets[] = $widget;
            }
        }
        return $widgets;
    }
}