<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006, Demian Turner                                         |
// | All rights reserved.                                                      |
// |                                                                           |
// | Redistribution and use in source and binary forms, with or without        |
// | modification, are permitted provided that the following conditions        |
// | are met:                                                                  |
// |                                                                           |
// | o Redistributions of source code must retain the above copyright          |
// |   notice, this list of conditions and the following disclaimer.           |
// | o Redistributions in binary form must reproduce the above copyright       |
// |   notice, this list of conditions and the following disclaimer in the     |
// |   documentation and/or other materials provided with the distribution.    |
// | o The names of the authors may not be used to endorse or promote          |
// |   products derived from this software without specific prior written      |
// |   permission.                                                             |
// |                                                                           |
// | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS       |
// | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT         |
// | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR     |
// | A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT      |
// | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,     |
// | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT          |
// | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,     |
// | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY     |
// | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT       |
// | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE     |
// | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.      |
// |                                                                           |
// +---------------------------------------------------------------------------+
// | Seagull 0.6                                                               |
// +---------------------------------------------------------------------------+
// | bountyMgr.php                                                    |
// +---------------------------------------------------------------------------+
// | Author: Alouicious Bird <demian@phpkitchen.com>                                  |
// +---------------------------------------------------------------------------+
// $Id: ManagerTemplate.html,v 1.2 2005/04/17 02:15:02 demian Exp $

require_once 'DB/DataObject.php';

define('SGL_BOUNTY_STATUS_OPEN', 1);
define('SGL_BOUNTY_STATUS_SPONSORED', 2);
define('SGL_BOUNTY_STATUS_SPONSOREDANDASSIGNED', 3);
define('SGL_BOUNTY_STATUS_COMPLETE', 4);
/**
 * Type your class description here ...
 *
 * @package bounty
 * @author  Alouicious Bird <demian@phpkitchen.com>
 */
class BountyMgr extends SGL_Manager
{
    function BountyMgr()
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        parent::SGL_Manager();

        $this->pageTitle    = 'Bounty Manager';
        $this->template     = 'bountyMgrList.html';

        $this->_aActionsMapping =  array(
            'list'      => array('list'),
            'view'      => array('view'),
        );
    }

    function validate($req, &$input)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $this->validated    = true;
        $input->error       = array();
        $input->pageTitle   = $this->pageTitle;
        $input->masterTemplate = 'masterLeftCol.html';
        $input->template    = $this->template;
        $input->action      = ($req->get('action')) ? $req->get('action') : 'list';

        $input->bountyId    = $req->get('frmBountyId');
    }

    function display(&$output)
    {
        if ($this->conf['BountyMgr']['showUntranslated'] == false) {
            $c = &SGL_Config::singleton();
            $c->set('debug', array('showUntranslated' => false));
        }
    }

    function _cmd_list(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'bountyList.html';
        $output->pageTitle = 'BountyMgr :: List';


        $aBountyList = DB_DataObject::factory($this->conf['table']['bounty']);
        $result = $aBountyList->find();
        $aBounties  = array();
        if ($result > 0) {
            while ($aBountyList->fetch()) {
                $aBountyList->getLinks();
                $bountyType = $aBountyList->_status_id->name;
                $aBounties[$bountyType][] = clone($aBountyList);
            }
        }
        $output->results = $aBounties;
    }

    function _cmd_view(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'bountyView.html';
        $output->pageTitle = 'BountyMgr :: View';


        $bounty = DB_DataObject::factory($this->conf['table']['bounty']);
        $bounty->get($input->bountyId);
        $bounty->getLinks();
        $output->bounty = $bounty;
    }

    function getTotalValueOfBounties()
    {

    }
}
?>
