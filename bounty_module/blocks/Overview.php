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
// | Seagull 0.6.5                                                             |
// +---------------------------------------------------------------------------+
// | Breadcrumbs.php                                                           |
// +---------------------------------------------------------------------------+
// | Author: Andrey Podshivalov <planetaz@gmail.com>                           |
// +---------------------------------------------------------------------------+

require_once 'DB/DataObject.php';

/**
 * Bounty overview block.
 *
 * @package seagull
 * @subpackage bounty
 */
class Bounty_Block_Overview
{
    function init(&$output, $block_id, &$aParams)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        return $this->getBlockContent($output, $aParams);
    }

    function getBlockContent(&$output, &$aParams)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $c = &SGL_Config::singleton();
        $aBountyList = DB_DataObject::factory($c->get(array('table' => 'bounty')));
        $result = $aBountyList->find();
        $aBounties  = array();
        if ($result > 0) {
            while ($aBountyList->fetch()) {
                $aBountyList->getLinks();
                $bountyType = $aBountyList->_status_id->name;
                $aBounties[$bountyType][] = clone($aBountyList);
            }
        }
        $out = '';
        $out .= "<p>Make money with Seagull and claim your bounty.</p>";
        foreach ($aBounties as $type => $aMyBounties) {
            $title = SGL_Inflector::humanise($type);
            $title = "<strong>$title</strong>";
            $out .= $title;
            $out .= "<ul class='noindent'>";
            foreach ($aMyBounties as $oBounty) {
                $link = SGL_Output::makeUrl('view', 'bounty', 'bounty') . 'frmBountyId/'.$oBounty->bounty_id;
                $out .= "<li><a href='$link'>$oBounty->name</a> [$$oBounty->value]</li>";
            }
            $out .= "</ul>";
        }
        $link = SGL_Output::makeUrl('list', 'bounty', 'bounty');
        $out .= "<p><a href='$link'>See all bounties</a></p>";
        return $out;
    }
}
?>