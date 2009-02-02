<?php
/**
 *
 * @author K. Coomans
 * @version $Id$
 * @copyright (C) 2008,2009 K. Coomans
 * @license code licensed under the GPL License: see LICENSE
 */

class XSendFileHandler extends eZBinaryFileHandler
{
    public $sendfileHeader = 'X-Sendfile';

    function __construct()
    {
        $this->eZBinaryFileHandler( 'xsendfile', "X-SendFile", eZBinaryFileHandler::HANDLE_DOWNLOAD );
    }

    function handleFileDownload( $contentObject, $contentObjectAttribute, $type, $fileInfo )
    {
        $fileName = $fileInfo['filepath'];

        $file = eZClusterFileHandler::instance( $fileName );

        if ( $fileName != "" and $file->exists() )
        {
            $file->fetch( true );
            $fileSize = $file->size();
            $mimeType =  $fileInfo['mime_type'];
            $originalFileName = $fileInfo['original_filename'];
            header( $this->sendfileHeader . ": $fileName" );
            header( "X-LIGHTTPD-send-file: " . $_ENV['DOCUMENT_ROOT'] ."/".$fileName);
            header( "Pragma: " );
            header( "Cache-Control: " );
            /* Set cache time out to 10 minutes, this should be good enough to work around an IE bug */
            header( "Expires: ". gmdate( 'D, d M Y H:i:s', time() + 600 ) . ' GMT' );
            header( "Content-Type: $mimeType" );
            header( "X-Powered-By: eZ Publish" );

            $dispositionType = self::dispositionType( $mimeType );
            header( "Content-disposition: $dispositionType; filename=\"$originalFileName\"" );

            eZExecution::cleanExit();
        }
        return eZBinaryFileHandler::RESULT_UNAVAILABLE;
    }

    /**
     * Checks if a file should be downloaded to disk or displayed inline in
     * the browser.
     *
     * This method returns "attachment" if no setting for the mime type is found.
     *
     * @param string $mimetype
     * @return string "attachment" or "inline"
     */
    protected static function dispositionType( $mimeType )
    {
        $ini = eZINI::instance( 'file.ini' );

        if ( $ini->hasVariable( 'PassThroughSettings', 'ContentDisposition' ) )
        {
            $mimeTypes = $ini->variable( 'PassThroughSettings', 'ContentDisposition' );
            if ( isset( $mimeTypes[$mimeType] ) )
            {
                return $mimeTypes[$mimeType];
            }
        }

        return 'attachment';
    }
}

?>
