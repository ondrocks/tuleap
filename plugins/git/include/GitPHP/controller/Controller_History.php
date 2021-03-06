<?php
/**
 * Copyright (c) Enalean, 2018. All Rights Reserved.
 * Copyright (C) 2010 Christopher Han <xiphux@gmail.com>
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

namespace Tuleap\Git\GitPHP;

use GitPHP\Shortlog\ShortlogPresenterBuilder;
use UserManager;

/**
 * History controller class
 *
 * @package GitPHP
 * @subpackage Controller
 */
class Controller_History extends ControllerBase // @codingStandardsIgnoreLine
{

    /**
     * __construct
     *
     * Constructor
     *
     * @access public
     * @return controller
     */
    public function __construct()
    {
        parent::__construct();
        if (!$this->project) {
            throw new MessageException(__('Project is required'), true);
        }
    }

    /**
     * GetTemplate
     *
     * Gets the template for this controller
     *
     * @access protected
     * @return string template filename
     */
    protected function GetTemplate() // @codingStandardsIgnoreLine
    {
        if (\ForgeConfig::get('git_repository_bp')) {
            return 'tuleap/history.tpl';
        }

        return 'history.tpl';
    }

    /**
     * GetName
     *
     * Gets the name of this controller's action
     *
     * @access public
     * @param boolean $local true if caller wants the localized action name
     * @return string action name
     */
    public function GetName($local = false) // @codingStandardsIgnoreLine
    {
        if ($local) {
            return __('history');
        }
        return 'history';
    }

    /**
     * ReadQuery
     *
     * Read query into parameters
     *
     * @access protected
     */
    protected function ReadQuery() // @codingStandardsIgnoreLine
    {
        if (isset($_GET['f'])) {
            $this->params['file'] = $_GET['f'];
        }
        if (isset($_GET['h'])) {
            $this->params['hash'] = $_GET['h'];
        } else {
            $this->params['hash'] = 'HEAD';
        }
    }

    /**
     * LoadData
     *
     * Loads data for this template
     *
     * @access protected
     */
    protected function LoadData() // @codingStandardsIgnoreLine
    {
        $co = $this->project->GetCommit($this->params['hash']);
        $this->tpl->assign('commit', $co);
        $this->tpl->assign('tree', $co->GetTree());

        $blobhash = $co->PathToHash($this->params['file']);
        $blob = $this->project->GetBlob($blobhash);
        $blob->SetCommit($co);
        $blob->SetPath($this->params['file']);
        $this->tpl->assign('blob', $blob);

        $commits_of_history = array_map(
            function (FileDiff $file_diff) {
                return $file_diff->GetCommit();
            },
            $blob->GetHistory()
        );
        $builder = new ShortlogPresenterBuilder(UserManager::instance());
        $this->tpl->assign('shortlog_presenter', $builder->getShortlogPresenter($commits_of_history));
    }
}
