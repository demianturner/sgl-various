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

/**
 * Type your class description here ...
 *
 * @package bounty
 * @author  Alouicious Bird <demian@phpkitchen.com>
 */
class AdminBountyMgr extends SGL_Manager
{
    function AdminBountyMgr()
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        parent::SGL_Manager();

        $this->pageTitle    = 'Admin Bounty Manager';
        $this->template     = 'adminBountyList.html';

        $this->_aActionsMapping =  array(
            'add'       => array('add'),
            'insert'    => array('insert', 'redirectToDefault'),
            'edit'      => array('edit'),
            'update'    => array('update', 'redirectToDefault'),
            'list'      => array('list'),
            'delete'    => array('delete', 'redirectToDefault'),
        );
    }

    function validate($req, &$input)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $this->validated    = true;
        $input->error       = array();
        $input->pageTitle   = $this->pageTitle;
        $input->masterTemplate = 'masterNoCols.html';
        $input->template    = $this->template;
        $input->action      = ($req->get('action')) ? $req->get('action') : 'list';
        $input->aDelete     = $req->get('frmDelete');
        $input->submitted   = $req->get('submitted');
        $input->bounty = (object)$req->get('bounty');
        $input->bountyId = $req->get('frmBountyID');

        //  if errors have occured
        if (isset($aErrors) && count($aErrors)) {
            SGL::raiseMsg('Please fill in the indicated fields');
            $input->error = $aErrors;
            $this->validated = false;
        }
    }

    function display(&$output)
    {
        if ($this->conf['BountyMgr']['showUntranslated'] == false) {
            $c = &SGL_Config::singleton();
            $c->set('debug', array('showUntranslated' => false));
        }

        $output->aStatuses = $this->getBountyStatuses();
    }

    function getBountyStatuses()
    {
        $query = "
            SELECT bounty_status_id, name
            FROM bounty_status";
        $aRes = $this->dbh->getAssoc($query);
        return $aRes;
    }


    function _cmd_add(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'bountyEdit.html';
        $output->pageTitle = 'BountyMgr :: Add';
        $output->action    = 'insert';
        $output->wysiwyg   = true;    }

    function _cmd_insert(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $bounty = DB_DataObject::factory($this->conf['table']['bounty']);
        $bounty->setFrom($input->bounty);
        $bounty->date_created = $bounty->last_updated = SGL_Date::getTime();
        $bounty->bounty_id = $this->dbh->nextId($this->conf['table']['bounty']);
        $success = $bounty->insert();

        if ($success !== false) {
            SGL::raiseMsg('bounty insert successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('bounty insert NOT successfull',
                SGL_ERROR_NOAFFECTEDROWS);
        }
    }

    function _cmd_edit(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'bountyEdit.html';
        $output->pageTitle = 'BountyMgr :: Edit';
        $output->action    = 'update';
        $output->wysiwyg   = true;

        $bounty = DB_DataObject::factory($this->conf['table']['bounty']);
        $bounty->get($input->bountyId);
        $output->bounty = $bounty;    }

    function _cmd_update(&$input, &$output)
    {

        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $bounty = DB_DataObject::factory($this->conf['table']['bounty']);
        $bounty->get($input->bountyId);
        $bounty->setFrom($input->bounty);
        $bounty->last_updated = SGL_Date::getTime();
        $success = $bounty->update();

        if ($success !== false) {
            SGL::raiseMsg('bounty update successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('bounty update NOT successfull',
                SGL_ERROR_NOAFFECTEDROWS);
        }    }

    function _cmd_list(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $output->pageTitle = 'BountyMgr :: List';


        $aBountyList = DB_DataObject::factory($this->conf['table']['bounty']);
        $result = $aBountyList->find();
        $aBounties  = array();
        if ($result > 0) {
            while ($aBountyList->fetch()) {
                $aBountyList->getLinks();
                $aBounties[] = clone($aBountyList);
            }
        }
        $output->results = $aBounties;
    }

    function _cmd_delete(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        if (is_array($input->aDelete)) {
            foreach ($input->aDelete as $index => $bountyId) {
                $bounty = DB_DataObject::factory($this->conf['table']['bounty']);
                $bounty->get($bountyId);
                $bounty->delete();
                unset($bounty);
            }
            SGL::raiseMsg('bounty delete successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('bounty delete NOT successfull ' .
                __CLASS__ . '::' . __FUNCTION__, SGL_ERROR_INVALIDARGS);
        }
    }


}
?>