/*
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

export function setSourceBranches(state, branches) {
    state.source_branches = branches;
}

export function setDestinationBranches(state, branches) {
    state.destination_branches = branches;
}

export function setCreateErrorMessage(state, create_error_message) {
    state.create_error_message = create_error_message;
}

export function setHasErrorWhileLoadingBranchesToTrue(state) {
    state.has_error_while_loading_branches = true;
}