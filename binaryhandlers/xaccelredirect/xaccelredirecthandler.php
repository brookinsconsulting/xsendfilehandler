<?php
/**
 *
 * @author G. Giunta
 * @version $Id$
 * @copyright (C) 2009 K. Coomans
 * @license code licensed under the GPL License: see LICENSE
 *
 * @todo add support for optional X-Accel-Limit-Rate, X-Accel-Buffering headers
 */

class XAccelRedirectHandler extends XSendFileHandler
{
    function __construct()
    {
        $this->sendfileHeader = 'X-Accel-Redirect';
        $this->eZBinaryFileHandler( 'xaccelredirect', "X-Accel-Redirect", eZBinaryFileHandler::HANDLE_DOWNLOAD );
    }
}

?>