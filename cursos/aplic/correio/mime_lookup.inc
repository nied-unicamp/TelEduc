<?php
/* mime_lookup() function - returns the mime type when given a file
   extension. */

/* $Id: mime_lookup.inc,v 1.1.1.1 2008/04/08 19:30:03 gabrielmdias Exp $ */

/* This software came from http://inebria.com/ */

/* Copyright (c) 2001
      Paul Southworth.  All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
    notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
    notice, this list of conditions and the following disclaimer in the
    documentation and/or other materials provided with the distribution.
3. The name of the author may not be used to endorse or promote
    products derived from this software without specific prior
    written permission.

THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS
OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
ARE DISCLAIMED.  IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE. */


/* warning - if you have a file extension which appears more than
   once in your mime.types file, the last one found will win. */

/* where's your mime.types file? */

$mt_f = 'mime.types';

/* build an array keyed on the file ext */

if (is_readable($mt_f)) {
    $mime_types=array();
    /* open our mime.types file for reading */
    $mt_fd=fopen($mt_f,"r");
    while (!feof($mt_fd)) {
        /* pull a line off the file */
        $mt_buf=trim(fgets($mt_fd,1024));
        /* discard if the line was blank or started with a comment */
        if (strlen($mt_buf) > 0) if (substr($mt_buf,0,1) != "#") {
            /* make temp array of the mime.types line we just read */
            $mt_tmp=preg_split("/[\s]+/", $mt_buf, -1, PREG_SPLIT_NO_EMPTY);
            $mt_num=count($mt_tmp);
            /* if $mt_num = 1 then we got no file extensions for the type */
            if ($mt_num > 1) {
                for ($i=1;$i<$mt_num;$i++) {
                    /* if we find a comment mid-line, stop processing */
                    if (strstr($mt_tmp[$i],"#")) {
                        break;
                    /* otherwise stick the type in an array keyed by extension */
                    } else {
                        $mime_types[$mt_tmp[$i]]=$mt_tmp[0];
                    }
                }
            /* zero the temporary array */
            unset($mt_tmp);
            }
        }
    }
    /* close the mime.types file we were reading */
    fclose($mt_fd);
} else {
    echo "ERROR: unreadable file " . $mt_f . "\n";
}

/* eg, mime_lookup("doc") returns "application/ms-word" */

function mime_lookup($ext) {
    global $mime_types;
    return $mime_types[$ext];
}
?>
