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

<template>
    <div class="timetracking-widget">
        <widget-reading-mode v-if="reading_mode"/>
        <widget-writing-mode v-else/>
        <widget-artifact-table/>
    </div>
</template>

<script>
import { mapState } from "vuex";
import store from "../store/index.js";
import WidgetReadingMode from "./WidgetReadingMode.vue";
import WidgetWritingMode from "./WidgetWritingMode.vue";
import WidgetArtifactTable from "./WidgetArtifactTable.vue";

export default {
    name: "Widget",
    store,
    components: {
        WidgetReadingMode,
        WidgetWritingMode,
        WidgetArtifactTable
    },
    computed: {
        ...mapState(["reading_mode"])
    }
};
</script>
