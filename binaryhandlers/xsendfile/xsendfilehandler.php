<?php

class XSendFileHandler extends eZBinaryFileHandler
{
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

            header( "X-Sendfile: $fileName" );
            header( "Pragma: " );
            header( "Cache-Control: " );
            /* Set cache time out to 10 minutes, this should be good enough to work around an IE bug */
            header( "Expires: ". gmdate('D, d M Y H:i:s', time() + 600) . ' GMT' );
            header( "Content-Type: $mimeType" );
            header( "X-Powered-By: eZ Publish" );
            header( "Content-disposition: attachment; filename=\"$originalFileName\"" );

            eZExecution::cleanExit();
        }
        return eZBinaryFileHandler::RESULT_UNAVAILABLE;
    }
}

?>